<?php
add_action('admin_print_scripts', 'fix_jquery_version', 1);

// add_action('admin_init', 'weever_override_wordpress_styles');
add_action('admin_menu', 'weever_admin_add_page');

weever_admin_warnings();

/**
 * This method is used to override the default WordPress stylings.
 * For more information, see the WP_Styles documentation in the WP Codex
 * http://codex.wordpress.org/Class_Reference/WP_Styles
 */
function weever_override_wordpress_styles() {
	global $wp_styles;
	$wp_styles->registered = array();
}

function fix_jquery_version() {

	$page = ( isset( $_GET['page'] ) ? basename( $_GET['page'] ) : '' );

	if ( substr( $page, 0, strlen('weever-') ) == 'weever-' ) {
		echo '<script type=\'text/javascript\' src=\'//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js\'></script>';
		// jQuery-migrate is only used for choosing to display the preview window. If possible, we should try to remove it.
		echo '<script type=\'text/javascript\' src=\'//code.jquery.com/jquery-migrate-1.2.1.min.js\'></script>';
	    echo '<script type=\'text/javascript\' src=\'' . plugins_url( 'static/js/vendor/jquery-ui.custom.min.js', __FILE__ ) . '\'></script>';
	    echo '<script type=\'text/javascript\' src=\'' . plugins_url( 'static/js/vendor/underscore.min.js', __FILE__ ) . '\'></script>';
	    echo '<script type=\'text/javascript\' src=\'' . plugins_url( 'static/js/vendor/backbone.min.js', __FILE__ ) . '\'></script>';

        global $wp_scripts;
	    $wp_scripts->remove( 'jquery' );
	}

}

/**
 * Add the administration pages for Weever Apps. Uses the admin_print_scripts action to ensure
 * the javascript/css is only loaded on our configuration screens.
 */
function weever_admin_add_page() {
    if ( function_exists('add_menu_page') )
    {
        // Ensure there are no directory references
        $page = ( isset( $_GET['page'] ) ? basename( $_GET['page'] ) : '' );

        // If this is a weever page, add it as the admin menu item (so it is always highlighted properly between admin page tabs)
        if ( substr( $page, 0, strlen('weever-') ) == 'weever-' )
        {
    		$mypage = add_menu_page(__('Weever App', 'weever'), __('Weever App', 'weever'), 'manage_options', $page, 'weever_admin_page', '');
        }
        else
        {
            if ( get_option( 'weever_api_key' ) )
    		    $mypage = add_menu_page(__('Weever App', 'weever'), __('Weever App', 'weever'), 'manage_options', 'weever-list', 'weever_admin_page', '');
            else
    		    $mypage = add_menu_page(__('Weever App', 'weever'), __('Weever App', 'weever'), 'manage_options', 'weever-account', 'weever_admin_page', '');
        }

        add_action( "admin_print_scripts-$mypage", 'weever_page_scripts_init' );
		add_action( "admin_print_styles-$mypage", 'weever_page_styles_init' );
		add_action( 'admin_footer', 'weever_load_backbone_templates' );
	}
}

function weever_load_backbone_templates() {
    // Backbone Templates
    $backbone_template_files = glob( dirname( __FILE__ ) . '/static/js/spec/fixtures/*.html' );
    
    foreach ( $backbone_template_files as $backbone_template_file )
        require_once( $backbone_template_file );
}

function weever_remove_wp_magic_quotes() {
	$_GET    = stripslashes_deep( $_GET );
	$_POST   = stripslashes_deep( $_POST );
	$_COOKIE = stripslashes_deep( $_COOKIE );
	$_REQUEST = stripslashes_deep( $_REQUEST );
}

/**
 * Page controller, loads the wrapper layout and the indivdual page content
 */
function weever_admin_page() {
	
	if ( ( function_exists('current_user_can') && current_user_can('manage_options') ) ) {

		// Load the weeverapp object, which fetches the admin page content
		try {
	        $weeverapp = new WeeverApp();
	
	        if ( ! $weeverapp->loaded ) {
		        add_settings_error('weever_settings', 'weever_settings', $weeverapp->error_message . " " . sprintf( __( '<a target="_new" href="%s">Contact Weever Apps support</a>', 'weever' ), 'http://support.weeverapps.com' ) );
	        }
		} catch (Exception $e) {
		    add_settings_error('weever_settings', 'weever_settings', __( 'Error loading necessary data' ) . " " . sprintf( __( '<a target="_new" href="%s">Contact Weever Apps support</a>', 'weever' ), 'http://support.weeverapps.com' ) );
		}
	
		if ($_SERVER['REQUEST_METHOD'] === 'POST') {

			try {
				update_option( 'weever_api_key', $_POST['site_key'] );
				$weeverapp->site_key = trim( $_POST['site_key'] );
    
                if ( isset( $_POST['stagingmode'] ) ) {
                    // Toggle staging mode
                    $weeverapp->staging_mode = ! $weeverapp->staging_mode;
                }
    
                $weeverapp->save();
                add_settings_error('weever_api_key', 'weever_settings', __( 'Weever Apps account settings saved. <a href="admin.php?page=weever-list">Click here to start building your app!</a>', 'weever' ), 'updated');
            } catch (Exception $e) {
                add_settings_error('weever_api_key', 'weever_settings', $e->getMessage() . " " . sprintf( __( '<a target="_new" href="%s">Contact Weever Apps support</a>', 'weever' ), 'http://support.weeverapps.com' ) );
            }

		}

		// Check if the domain is different than the current site domain
		if ( $weeverapp->loaded && $weeverapp->site_key ) {
			if ( ! stripos( site_url(), $weeverapp->primary_domain ) )
				add_settings_error('weever_settings', 'weever_settings', sprintf( __( 'Your Weever App subscription key url %s does not match the current Wordpress site url %s - please verify your Wordpress settings, that you have the correct subscription key, or contact support.' ), $weeverapp->primary_domain, site_url() ) . " " . sprintf( __( '<a target="_new" href="%s">Contact Weever Apps support</a>', 'weever' ), 'http://support.weeverapps.com' ) );
		}
		
	} else {
		die( __( 'Access denied', 'weever' ) );
	}
	
	// Include the main content to fire things off
	require( dirname( __FILE__) . '/templates/admin/layout.php' );
}

function weever_admin_warnings() {
	global $wp_version;
	
	if ( ! get_option( 'weever_api_key' ) && ! isset( $_POST['submit']) && ( function_exists( 'is_multisite' ) || ! version_compare( $wp_version, '3.0', '<' ) ) ) {
		function weever_warning() {
			echo "
			<div id='weever-warning' class='updated'><p><strong>".__('Weever Apps is almost ready.', 'weever')."</strong> ".sprintf(__('You must <a href="%1$s">enter your Weever Apps Subscription Key</a> for it to work.  Don\'t have one?  <a target="_blank" href="http://weeverapps.com/product/cms">Get one here</a>.', 'weever'), "plugins.php?page=weever-account")."</p></div>
			";
		}
		add_action( 'admin_notices', 'weever_warning' );
		return;
	}
}

function weever_admin_init() {
    global $wp_version;

    // all admin functions are disabled in old versions
    if ( ! function_exists( 'is_multisite' ) && version_compare( $wp_version, '3.0', '<' ) ) {

        function weever_version_warning() {
            echo "
            <div id='weever-warning' class='updated'><p><strong>".sprintf( __( 'Weever Apps %s requires WordPress 3.0 or higher.', 'weever' ), WeeverConst::VERSION ) ."</strong> ".sprintf( __( 'Please <a href="%s">upgrade WordPress</a> to a current version.', 'weever' ), 'http://codex.wordpress.org/Upgrading_WordPress' ). "</p></div>
            ";
        }
        add_action( 'admin_notices', 'weever_version_warning' );

        return;
    }
}

add_action( 'admin_init', 'weever_admin_init' );

function wserver_default_editor() {
	$r = 'tinymce'; // html or tinymce
	return $r;
}
add_filter( 'wp_default_editor', 'wserver_default_editor' );

/**
 * Load styles needed for Weever Apps
 */
function weever_page_styles_init() {
    // global $wp_styles;

    wp_register_style( 'weever-icon-font-1.css', WEEVER_PLUGIN_URL . 'static/css/weever-icon-font-1.css' );
	wp_register_style( 'imgareaselect.css', WEEVER_PLUGIN_URL . 'static/css/imgareaselect-default.css' );

    wp_enqueue_style( 'weever-icon-font-1.css' );
	wp_enqueue_style( 'imgareaselect.css' );

	wp_register_style( 'app.css', WEEVER_PLUGIN_URL . 'static/css_wordpress/app.css' );
	wp_enqueue_style('app.css');
}

/**
 * Loads scripts needed for Weever Apps
 */
function weever_page_scripts_init() {

    if ( file_exists( dirname( __FILE__ ) . '/static/js/combined.js' ) ) {
    	
    	// Combined/compressed file exists. Use that!
    	wp_register_script( 'combined.js', plugins_url( 'static/js/combined.js', __FILE__ ), array(), WeeverConst::VERSION, true );
        wp_enqueue_script( 'combined.js' );

    } else {

    	wp_register_script( 'jquery-iframe-transport', plugins_url( 'static/js/vendor/jquery.iframe-transport.js', __FILE__ ), array(), WeeverConst::VERSION );
    	wp_enqueue_script( 'jquery-iframe-transport' );

		wp_register_script( 'weever.js', plugins_url( 'static/js/weever.js', __FILE__ ), array(), WeeverConst::VERSION );
		wp_enqueue_script( 'weever.js' );
		wp_localize_script( 'weever.js', 'WPText', WeeverHelper::get_js_strings() );

		wp_register_script('modernizr', plugins_url( 'static/js/vendor/modernizr.js', __FILE__ ));
		wp_enqueue_script( 'modernizr');

		// wp_register_script('jscolor', plugins_url( 'static/js/jscolor/jscolor.js', __FILE__ ));
		// wp_enqueue_script( 'jscolor');

		wp_register_script( 'fileuploader.js', plugins_url( 'static/js/fileuploader.js', __FILE__ ), array(), WeeverConst::VERSION );
		
		// Foundation scripts
		wp_register_script('foundation',                plugins_url('static/js/foundation/foundation.js', __FILE__));
		wp_enqueue_script( 'foundation');
		wp_register_script('foundation.abide',          plugins_url('static/js/foundation/foundation.abide.js', __FILE__), array('foundation'));
		wp_enqueue_script( 'foundation.abide');
        wp_register_script('foundation.accordion',      plugins_url('static/js/foundation/foundation.accordion.js', __FILE__), array('foundation'));
        wp_enqueue_script( 'foundation.accordion');
		wp_register_script('foundation.alert',          plugins_url('static/js/foundation/foundation.alert.js', __FILE__), array('foundation'));
		wp_enqueue_script( 'foundation.alert');
		wp_register_script('foundation.clearing',       plugins_url('static/js/foundation/foundation.clearing.js', __FILE__), array('foundation'));
		wp_enqueue_script( 'foundation.clearing');
		wp_register_script('foundation.dropdown',       plugins_url('static/js/foundation/foundation.dropdown.js', __FILE__), array('foundation'));
		wp_enqueue_script( 'foundation.dropdown');
        wp_register_script('foundation.equalizer',     plugins_url('static/js/foundation/foundation.equalizer.js', __FILE__), array('foundation'));
        wp_enqueue_script( 'foundation.equalizer');
		wp_register_script('foundation.interchange',    plugins_url('static/js/foundation/foundation.interchange.js', __FILE__), array('foundation'));
		wp_enqueue_script( 'foundation.interchange');
		wp_register_script('foundation.joyride',        plugins_url('static/js/foundation/foundation.joyride.js', __FILE__), array('foundation'));
		wp_enqueue_script( 'foundation.joyride');
		wp_register_script('foundation.magellan',       plugins_url('static/js/foundation/foundation.magellan.js', __FILE__), array('foundation'));
		wp_enqueue_script( 'foundation.magellan');
        wp_register_script('foundation.offcanvas',      plugins_url('static/js/foundation/foundation.offcanvas.js', __FILE__), array('foundation'));
        wp_enqueue_script( 'foundation.offcanvas');
		wp_register_script('foundation.orbit',          plugins_url('static/js/foundation/foundation.orbit.js', __FILE__), array('foundation'));
		wp_enqueue_script( 'foundation.orbit');
		wp_register_script('foundation.reveal',         plugins_url('static/js/foundation/foundation.reveal.js', __FILE__), array('foundation'));
		wp_enqueue_script( 'foundation.reveal');
        wp_register_script('foundation.slider',         plugins_url('static/js/foundation/foundation.slider.js', __FILE__), array('foundation'));
        wp_enqueue_script( 'foundation.slider');
        wp_register_script('foundation.tab',            plugins_url('static/js/foundation/foundation.tab.js', __FILE__), array('foundation'));
        wp_enqueue_script( 'foundation.tab');
		wp_register_script('foundation.tooltip',        plugins_url('static/js/foundation/foundation.tooltip.js', __FILE__), array('foundation'));
		wp_enqueue_script( 'foundation.tooltip');
		wp_register_script('foundation.topbar',         plugins_url('static/js/foundation/foundation.topbar.js', __FILE__), array('foundation'));
		wp_enqueue_script( 'foundation.topbar');

	    wp_register_script('jquery.cookie',             plugins_url('static/js/vendor/jquery.cookie.js', __FILE__), array('foundation'), WeeverConst::VERSION, true);
	    wp_enqueue_script( 'jquery.cookie');
	    wp_register_script('vendor.fastclick',          plugins_url('static/js/vendor/fastclick.js', __FILE__), array('foundation'), WeeverConst::VERSION, true);
	    wp_enqueue_script( 'vendor.fastclick');
	    wp_register_script('vendor.placeholder',        plugins_url('static/js/vendor/placeholder.js', __FILE__), array('foundation'), WeeverConst::VERSION, true);
	    wp_enqueue_script( 'vendor.placeholder');

	    wp_register_script( 'weever.account.js',        plugins_url( 'static/js/account.js', __FILE__ ), array(), WeeverConst::VERSION );
	    wp_enqueue_script( 'weever.account.js' );
	    
	    if ( ! apply_filters( 'weever_list_show_wordpress_content', true ) )
		    wp_enqueue_script( 'fileuploader.js' );
		
		wp_enqueue_script( 'media-upload' );

	    wp_register_script( 'weever.list.js', plugins_url( 'static/js/list.js', __FILE__ ), array(), WeeverConst::VERSION, true );
	    wp_enqueue_script( 'weever.list.js' );

	    wp_register_script( 'jq.list.js', plugins_url( 'static/js/jq.list.js', __FILE__ ), array( 'weever.js' ), WeeverConst::VERSION );
		wp_enqueue_script( 'jq.list.js' );

    	// TODO: Add dependencies between files? (They should be enqueued in this order regardless)
    	$pre_loaded_models = array( 'formbuilder.control.js', 'formbuilder.control.input.js', 'tab.js', 'subtab.js' );
    	foreach ($pre_loaded_models as $i => $value) {
    		$file_name = 'models.' . basename( $value );
    		wp_register_script( $file_name, plugins_url( 'static/js/models/' . basename( $value ), __FILE__ ), array(), WeeverConst::VERSION, true );
	        wp_enqueue_script( $file_name );
    	}

	    foreach( glob( dirname(__FILE__) . '/static/js/models/*.js' ) as $model_js_file ) {
	    	$file_name = 'models.' . basename( $model_js_file );
	        wp_register_script( $file_name, plugins_url( 'static/js/models/' . basename( $model_js_file ), __FILE__ ), array(), WeeverConst::VERSION, true );
	        wp_enqueue_script( $file_name );
	    }

	    foreach( glob( dirname(__FILE__) . '/static/js/collections/*.js' ) as $model_js_file ) {
	    	$file_name = 'collections.' . basename( $model_js_file );
	        wp_register_script( $file_name, plugins_url( 'static/js/collections/' . basename( $model_js_file ), __FILE__ ), array(), WeeverConst::VERSION, true );
	        wp_enqueue_script( $file_name );
	    }

	    $pre_loaded_views = array( 'formbuilder.control.js', 'tab.js', 'subtab.edit.js', 'subtab.formbuilder.edit.js', 'style.js' );
    	foreach ($pre_loaded_views as $i => $value) {
    		$file_name = 'views.' . basename( $value );
    		wp_register_script( $file_name, plugins_url( 'static/js/views/' . basename( $value ), __FILE__ ), array(), WeeverConst::VERSION, true );
	        wp_enqueue_script( $file_name );
    	}

	    foreach( glob( dirname(__FILE__) . '/static/js/views/*.js' ) as $model_js_file ) {
            $model_js_file = basename( $model_js_file );
            if ( $model_js_file == 'subtab.coupon.edit.js' or $model_js_file == 'subtab.map.edit.js' )  // These models aren't used in WordPress.
                continue;
	    	$file_name = 'views.' . $model_js_file;
	        wp_register_script( $file_name, plugins_url( 'static/js/views/' . $model_js_file, __FILE__ ), array(), WeeverConst::VERSION, true );
	        wp_enqueue_script( $file_name );
	    }

	}

    do_action('admin_head-weever-list');
    
	wp_register_script( 'jquery.imgareaselect.js', plugins_url( 'static/js/jquery.imgareaselect.min.js', __FILE__ ), array(  ), WeeverConst::VERSION );
    wp_enqueue_script( 'jquery.imgareaselect.js' );
}

function weever_localize_script() {
	$local_vars = array(
		'templateDirectoryUri' => get_template_directory_uri(),
		'pluginUrl' => WEEVER_PLUGIN_URL
	);

	return $local_vars;
}

/**
 * Add custom fields for post editing, for KML and custom marker url
 */

function weever_add_custom_box() {
	if ( apply_filters( 'weever_show_map_meta_box', true ) ) {
		add_meta_box('weever_sectionid', __( 'Weever Apps - Map Data', 'myplugin_textdomain' ), 'weever_inner_custom_box', 'post', 'advanced' );
	}
}

add_action('admin_menu', 'weever_add_custom_box');

function weever_inner_custom_box() {
	echo '<input type="hidden" id="weever_nonce" name="weever_nonce" value="' .
			wp_create_nonce( plugin_basename(__FILE__) ) . '" />';
	echo '
	<p>
		<label for="weever-kml">Display Address:</label>
		<input type="text" id="weever-map-address" name="weever-map-address" value="">
	</p>
	
	<p>
		<label for="weever-kml">KML File URL:</label>
		<input type="text" id="weever-kml" name="weever-kml" value="">
	</p>
	<p>
		<label for="weever-kml">Custom Map Marker URL:</label>
		<input type="text" id="weever-map-marker" name="weever-map-marker" value="">
	</p>
	<p>
		NOTE: Custom map markers must be PNG image sprites that are 128 pixels by 74 pixels. The image on the left is the normal state, the one on the right is the selected state; each is 64x74 pixels placed beside each other in the same transparent PNG image file.
	</p>
	';
}

function weever_post_admin_head() {
	global $post;
	$post_id = $post->ID;
	$post_type = $post->post_type;
	$zoom = (int) get_option('geolocation_default_zoom');
	?>
	<script type="text/javascript">
	jQuery(function() {
		jQuery(document).ready(function() {
			jQuery('#weever-map-address').val('<?php echo esc_js(get_post_meta($post_id, 'weever_map_address', true)); ?>');
			jQuery('#weever-kml').val('<?php echo esc_js(get_post_meta($post_id, 'weever_kml', true)); ?>');
			jQuery('#weever-map-marker').val('<?php echo esc_js(get_post_meta($post_id, 'weever_map_marker', true)); ?>');
		});
	});
	</script>
	<?php 
}

add_action('admin_head-post-new.php', 'weever_post_admin_head');
add_action('admin_head-post.php', 'weever_post_admin_head');

function weever_save_postdata($post_id) {
	// Check authorization, permissions, autosave, etc
	if ( ! isset( $_POST['weever_nonce'] ) or ! wp_verify_nonce( $_POST['weever_nonce'], plugin_basename( __FILE__ ) ) )
		return $post_id;

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
		return $post_id;

	if( 'page' == $_POST['post_type'] ) {
		if( ! current_user_can( 'edit_page', $post_id ) )
			return $post_id;
	} else {
		if ( ! current_user_can( 'edit_post', $post_id ) )
			return $post_id;
	}

	if ( apply_filters( 'weever_show_map_meta_box', true ) ) {

		// NOTE - Commented out until weever_geolocation is ready for prime time.

		// $address = $_POST['weever-map-address'];
		// update_post_meta($post_id, 'weever_map_address', $address);
		// update_post_meta($post_id, 'geo_public', true);
		// update_post_meta($post_id, 'weever_kml', $_POST['weever-kml']);
		// update_post_meta($post_id, 'weever_map_marker', $_POST['weever-map-marker']);

		// // Get latitude & longitude
		// $address = urlencode( $address );
		// $response = curl_file_get_contents( "http://maps.google.com/maps/api/geocode/json?sensor=false&address=".$address );
		// $json = json_decode($response, true);

		// if( $json['status']='OK' ) {
		// 	$lat = $json['results'][0]['geometry']['location']['lat'];
		// 	$lng = $json['results'][0]['geometry']['location']['lng'];

		// 	update_post_meta($post_id, 'geo_latitude', $lat);
		// 	update_post_meta($post_id, 'geo_longitude', $lng);
		// }
	}
	
	return $post_id;
}

function curl_file_get_contents($url) {
	$c = curl_init();
    curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($c, CURLOPT_URL, $URL);
    $contents = curl_exec($c);
    curl_close($c);

    if ($contents) return $contents;
    else return false;
}

add_action('save_post', 'weever_save_postdata');

// function weever_section_text() {
// 	echo '<p></p>';
// }

// function weever_api_key_string() {
//     $weever_api_key = get_option( 'weever_api_key' );
//     echo "<input id='weever_api_key' name='weever_api_key' size='40' type='text' value='$weever_api_key' />";
// }
