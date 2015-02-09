<?php

    class WeeverHelper {

        private static $_weeverapp = false;
        private static $_api_version = WeeverConst::API_VERSION;

        public static function tag_select_dropdown_options() {
            ?>
            <option><?php echo __('Choose a tag...'); ?></option>

            <?php
            $categories = array();
            foreach ( get_taxonomies( array( 'public' => true ), 'objects' ) as $taxonomy ) {
                if ( ! $taxonomy->query_var || $taxonomy->query_var != 'tag' ) continue;
                foreach ( get_terms( $taxonomy->name ) as $term ) {
                    $categories[] = array('link' => site_url( WeeverHelper::get_term_feed_link_relative( $term ) ),
                        'name' => $term->name
                    );
                }
            }

            if ( ! function_exists( 'weever_category_sort' ) ) {
                function weever_category_sort($a, $b) {
                    return strcmp($a['name'], $b['name']);
                }
            }

            usort($categories, 'weever_category_sort');
            ?>
            <?php foreach ($categories as $category): ?>
                <option value="<?php echo $category['link']; ?>"><?php echo $category['name']; ?></option>
            <?php endforeach; ?>
        <?php
        }

        public function category_select_dropdown_options() {
            ?>
            <option><?php echo __('Choose a category...'); ?></option>

            <?php
            $categories = array();
            foreach ( get_taxonomies( array( 'public' => true ), 'objects' ) as $taxonomy ) {
                if ( ! $taxonomy->query_var || $taxonomy->query_var == 'post_format' || $taxonomy->query_var == 'tag' ) continue;
                foreach ( get_terms( $taxonomy->name ) as $term ) {
                    $categories[] = array('link' => site_url( WeeverHelper::get_term_feed_link_relative( $term ) ),
                        'name' => $term->name
                    );
                }
            }

            if ( ! function_exists( 'weever_category_sort' ) ) {
                function weever_category_sort($a, $b) {
                    return strcmp($a['name'], $b['name']);
                }
            }

            usort($categories, 'weever_category_sort');
            ?>
            <?php foreach ($categories as $category): ?>
                <option value="<?php echo $category['link']; ?>"><?php echo $category['name']; ?></option>
            <?php endforeach; ?>
        <?php
        }


        /**
         * Function to take a url and ensure it is an absolute url
         * http://www.geekality.net/2011/05/12/php-dealing-with-absolute-and-relative-urls/
		 *
         * @param string $url
         * @param string $base
         */
        public static function make_absolute($url, $base) {
            // Return base if no url
            if( ! $url) return $base;

            // Return if already absolute URL
            if(parse_url($url, PHP_URL_SCHEME) != '') return $url;

            // Urls only containing query or anchor
            if($url[0] == '#' || $url[0] == '?') return $base.$url;

            // Parse base URL and convert to local variables: $scheme, $host, $path
            extract(parse_url($base));

            // If no path, use /
            if( ! isset($path)) $path = '/';

            // Remove non-directory element from path
            $path = preg_replace('#/[^/]*$#', '', $path);

            // Destroy path if relative url points to root
            if($url[0] == '/') $path = '';

            // Dirty absolute URL
            $abs = "$host$path/$url";

            // Replace '//' or '/./' or '/foo/../' with '/'
            $re = array('#(/\.?/)#', '#/(?!\.\.)[^/]+/\.\./#');
            for($n = 1; $n > 0; $abs = preg_replace($re, '/', $abs, -1, $n)) {}

            // Absolute URL is ready!
            return $scheme.'://'.$abs;
        }

        /**
         * Return the strings for internationalization in the javascript files
         *
         * @return array
         */
        public static function get_js_strings() {
		    return array(
                'LIVE_SERVER' => WeeverConst::LIVE_SERVER,
    			'WEEVER_JS_ENTER_NEW_APP_ICON_NAME' => __( 'Enter a New App Icon Name:', 'weever' ),
    			'WEEVER_JS_APP_UPDATED' => __( 'App Updated', 'weever' ),
    			'WEEVER_JS_PLEASE_WAIT' => __( 'Please wait, communicating with server', 'weever' ),
    			'WEEVER_JS_SAVING_CHANGES' => __( 'Please Wait...', 'weever' ),
    			'WEEVER_JS_SERVER_ERROR' => __( 'Server Error Occurred', 'weever' ),
    			'WEEVER_JS_ENTER_NEW_APP_ITEM' => __( '' ),
    			'WEEVER_JS_ARE_YOU_SURE_YOU_WANT_TO' => __( 'Are you sure you want to ', 'weever' ),
    			'WEEVER_JS_QUESTION_MARK' => __( '?', 'weever' ),
    			'WEEVER_JS_CHANGING_NAV_ICONS' => __( 'Changing Navigation Icons:', 'weever' ),
    			'WEEVER_JS_CHANGING_NAV_ICONS_INSTRUCTIONS_A' => __( 'Weever Apps Icons are made with &quot;Base64&quot;-encoded CSS.', 'weever' ),
                'WEEVER_JS_CHANGING_NAV_ICONS_INSTRUCTIONS_B' => __( 'To create a new icon, please upload your icon-image to a %1Base64 Encoder%2 and paste in the results below. We strongly recommend using a black monochrome, transparent 64 x 64 pixel PNG image.', 'weever' ),
		        'WEEVER_JS_RESET_TO_DEFAULT' => __( 'Reset to Default', 'weever' ),
		        'WEEVER_JS_NO_IMAGE_URL' => WEEVER_PLUGIN_URL . 'static/img//no-image.png',
    			'WEEVER_JS_CHANGING_NAV_PASTE_CODE' => __( 'Click and paste your code here', 'weever' ),
		        'WEEVER_JS_STATIC_PATH' => WEEVER_PLUGIN_URL . 'static/',
		        'WEEVER_JS_ADMIN_LIST_URL' => admin_url( 'admin.php?page=weever-list' ),
		        'WEEVER_JS_SELECT_AN_ELEMENT' => __( 'Select at least one item from the list first', 'weever' ),
		        'WEEVER_JS_CONFIRM_LIST_ACTION' => __( 'Are you sure you want to %s the selected items?', 'weever' ),
		    	'WEEVER_JS_PANEL_TRANSITION_ANIMATIONS' => __( 'Panel Transition Animations', 'weever' ),
		    	'WEEVER_JS_PANEL_TRANSITION_TOOLTIP' => __( 'When enabled, each items will transition one to the next, like a slideshow', 'weever' ),
		    	'WEEVER_JS_PANEL_TRANSITION_TOGGLE' => __( 'Transitions', 'weever' ),
		    	'WEEVER_CONFIG_DISABLED' => __( 'Disabled', 'weever' ),
		    	'WEEVER_CONFIG_ENABLED' => __( 'Enabled', 'weever' ),
		    	'WEEVER_JS_PANEL_TRANSITION_DURATION_TOOLTIP' => __( 'Controls the duration of the animated transition', 'weever' ),
		    	'WEEVER_JS_PANEL_TRANSITION_DURATION' => __( 'Animation Duration', 'weever' ),
		    	'WEEVER_JS_PANEL_TRANSITION_DURATION_SHORT' => __( 'Shorter', 'weever' ),
		    	'WEEVER_JS_PANEL_TRANSITION_DURATION_DEFAULT' => __( 'Default', 'weever' ),
		    	'WEEVER_JS_PANEL_TRANSITION_DURATION_LONG' => __( 'Longer', 'weever' ),
		    	'WEEVER_JS_PANEL_TIMEOUT_TOOLTIP' => __( 'Sets how long until a transition occurs', 'weever' ),
		    	'WEEVER_JS_PANEL_TIMEOUT' => __( 'Time Until Transition', 'weever' ),
		    	'WEEVER_JS_PANEL_TIMEOUT_SHORT' => __( 'Shorter', 'weever' ),
		    	'WEEVER_JS_PANEL_TIMEOUT_DEFAULT' => __( 'Default', 'weever' ),
		    	'WEEVER_JS_PANEL_TIMEOUT_LONG' => __( 'Longer', 'weever' ),
		    	'WEEVER_JS_PANEL_HEADERS_TOOLTIP' => __( 'When disabled, article/content items\' titles, authors, and other meta-data is not displayed.', 'weever' ),
		    	'WEEVER_JS_PANEL_HEADERS' => __( 'Content Headers', 'weever' ),
		    	'WEEVER_JS_MAP_SETTINGS' => __( 'Map Settings', 'weever' ),
		    	'WEEVER_JS_MAP_START_LATITUDE_TOOLTIP' => __( 'Latitude that which the map first starts at.', 'weever' ),
		    	'WEEVER_JS_MAP_START_LATITUDE' => __( 'Starting Latitude', 'weever' ),
		    	'WEEVER_JS_MAP_START_LONGITUDE_TOOLTIP' => __( 'Longitude that which the map first starts at.', 'weever' ),
		    	'WEEVER_JS_MAP_START_LONGITUDE' => __( 'Starting Longitude', 'weever' ),
		    	'WEEVER_JS_MAP_START_ZOOM_TOOLTIP' => __( 'Zoom level that which the map first starts at. (1-15)', 'weever' ),
		    	'WEEVER_JS_MAP_START_ZOOM' => __( 'Zoom Level (1-15)', 'weever' ),
		    	'WEEVER_JS_MAP_DEFAULT_MARKER_TOOLTIP' => __( 'Default marker sprite, used when a marker sprite is unspecified. Must be a 128x74 PNG file, containging two images 64x74 side by side.', 'weever' ),
		    	'WEEVER_JS_MAP_DEFAULT_MARKER' => __( 'Default Marker Image URL', 'weever' ),
		    );
        }

        /**
         * Get the url of the feed for the given term
         *
         * @param term $term
         * @param string $feed
         * @return string or bool false on error
         */
        public static function get_term_feed_link_relative( $term, $feed = 'r3s' ) {
		    $term_id = $term->term_id;
		    $taxonomy = $term->taxonomy;
		    $link = false;
			if ( 'category' == $taxonomy ) {
    			$link = "index.php?feed=$feed&amp;cat=$term_id";
    		}
    		elseif ( 'post_tag' == $taxonomy ) {
    			$link = "index.php?feed=$feed&amp;tag=$term->slug";
    		} else {
    			$t = get_taxonomy( $taxonomy );
    			if ( $t->query_var )
    				$link = "index.php?feed=$feed&amp;$t->query_var=$term->slug";
    		}

    		return $link;
        }

        public static function get_page_link_relative( $page ) {
            return "index.php?wx_page_id=$page->ID";
        }

        /**
         * Set the weeverapp variable in preparation for server
		 *
         * @param unknown_type $weeverapp
         */
        public static function set_weeverapp(&$weeverapp) {
            self::$_weeverapp = $weeverapp;
        }

        // public static function & get_weeverapp() {
        //     return self::$_weeverapp;
        // }

        /**
         * Get the URL for the main image, either as the featured image or the first image
         * in the content
         * 
         * @param $post_id
         */
        public static function get_image_url( $post_id ) {
            $image = '';
            
            /* Use the featured image, if any */
            if ( has_post_thumbnail( $post_id ) ) {
                $image = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ) );

                if ( is_array( $image ) and isset( $image[0] ) )
                    $image = $image[0];
            }

            if ( empty( $image ) ) {
                $post = get_post( $post_id );
                
                if ( $post ) {
                    $html = WeeverSimpleHTMLDomHelper::str_get_html( $post->post_content );
    
                    foreach ( @$html->find('img') as $vv )
                    {
                        if ( $vv->src )
                        {
                            $image = WeeverHelper::make_absolute($vv->src, get_site_url());
                            break;
                        }
                    }
                }
            }
            
            return $image;
        }
        
        public static function get_root_weever_api_url() {
	        $retval = self::get_root_weever_live_url();

            // Append the api version and endpoint
            $retval .= 'api/' . self::$_api_version . '/';

            return $retval;
        }

	    public static function get_root_weever_live_url() {
		    $retval = ( self::$_weeverapp->staging_mode ? WeeverConst::LIVE_STAGE : WeeverConst::LIVE_SERVER );

		    return $retval;
	    }

        /**
         * Retrieve and send data to the Weever Apps server
         *
         * @param string $endpoint (ie. 'tabs/get_tabs')
         * @param array $postdata (not required for most calls, site_key added automatically)
         * @param string $api_version (default 'v2')
         * @return stdClass - json decoded return value
         */
        public static function send_to_weever_server($endpoint, $postdata = array(), $api_version = 'v2') {
            global $wp_version;
            
            $retval = false;

            // Check the passed params first
            if ( is_array( $postdata ) and ! is_array($endpoint) and FALSE !== strpos($endpoint, '/') and self::$_weeverapp !== false ) {
            	$server = ( self::$_weeverapp->staging_mode ? WeeverConst::LIVE_STAGE : WeeverConst::LIVE_SERVER );
                
                // Append the api version and endpoint
                $server .= 'api/' . $api_version . '/' . $endpoint;

                // If no protocol is provided, supply one.
                if ( substr($server, 0, '2') === '//' ) {
                    $isSecure = false;
                    if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
                        $isSecure = true;
                    }
                    elseif (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' || !empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] == 'on') {
                        $isSecure = true;
                    }
                    $protocol = $isSecure ? 'https:' : 'http:';
                    $server = $protocol . $server;
                }

            	if ( ! isset( $postdata['site_key'] ) )
            	    $postdata['site_key'] = self::$_weeverapp->site_key;

                $postdata['version'] = WeeverConst::VERSION;
                $postdata['generator'] = WeeverConst::NAME;
                $postdata['cms'] = WeeverConst::CMS;
                $postdata['cms_version'] = $wp_version;

                if ( isset($postdata['build_version']) )
                    unset($postdata['build_version']);

            	$result = wp_remote_post( $server, array( 
                            'method' => 'POST',
                            'timeout' => 45,
                            'redirection' => 5,
                            'httpversion' => '1.0',
                            'blocking' => true,
                            'headers' => array(),
                            'body' => $postdata,
                            'cookies' => array() ) );

                if ( is_array( $result ) and isset( $result['body'] ) ) {
            	    $retval = json_decode($result['body']);
                    if ( false === $retval )
                        // Not valid json
                        throw new Exception( __( 'Sorry, there was a communication error.', 'weever' ) );
                    elseif ( isset($retval->error) and true == $retval->error )
                        throw new Exception( __( 'Sorry, there was a communication error.', 'weever' ) );
            	} else {
            		$error = ( is_wp_error( $result ) ? ': ' . $result->get_error_message() : '' );
            		
            	    throw new Exception( __( 'Sorry, there was a communication error.', 'weever' ) );
            	}
            } else {
                var_dump(debug_backtrace(FALSE));
                throw new Exception( __( 'Invalid postdata sent to function, invalid endpoint, or weeverapp not set', 'weever' ) );
            }

        	return $retval;
        }
    }