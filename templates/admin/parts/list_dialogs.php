
<?php
/**
 * Function to create a dropdown of items the user can edit for the given component ids
 * If none found, shows nothing
 * 
 * @param WeeverApp weeverapp object
 * @param array $component_ids
 * @param string $type 'item' or 'page' - default is 'item'
 */ 
function weever_show_edit_dropdown($weeverapp, $component_ids, $type = 'item') {

	$items = array();
	// Get items to edit (if any)
	
	if ( is_object($weeverapp) ) {
		if ( 'item' == $type ) {
			foreach ( $weeverapp->get_tabs() as $tab ) {
				$subtabs = $tab->get_subtabs();
			
				foreach ( $subtabs as $subtab ) {
					if ( in_array( $subtab->get_component_id(), $component_ids ) ) {
						// Include in the list
						$items[] = array( 'item' => $subtab, 'item_id' => $subtab->id, 'description' => $tab->name . ' &rsaquo; ' . $subtab->name );
					}
				}
			}
		} elseif ( 'page' == $type ) {
			// Get list of pages, and posts broken down by category / type
			$result = array();
			$result['regular_posts'] = new WP_Query(array(
							'posts_per_page' => -1,
							'post_type' => 'post',
							'category_name' => 'uncategorized',
							'post_status' => 'publish'
						)
					);
			
			$result['map_posts'] = new WP_Query(array(
							'posts_per_page' => -1,
							'post_type' => 'post',
							'category_name' => 'map',
							'post_status' => 'publish'
						)
					);
			
			$result['pages'] = new WP_Query(array(
							'posts_per_page' => -1,
							'post_type' => 'page',
							'post_status' => 'publish'
						)
					);
			
			foreach ( $result as $key => $query ) {
				if ( ! in_array( $key, $component_ids ) ) continue;
				
				while ( $query->have_posts() ) {
					$post = $query->next_post();
					
					$items[] = array( 'item' => $post, 'item_id' => $post->ID, 'description' => ucwords(array_shift(explode('_', $key))) . ' &rsaquo; ' . $post->post_title );
				}
			}
		}
	}
		
    // TODO: Re-show but in a different way?
	/*if ( count($items) ) {
		echo '<div class="wx-service-description wx-edit-choices"><p>or edit an existing item:</p>';
		echo '<select class="edit-item-choice">';
		foreach ( $items as $item ) {
			echo '<option value="edit' . $type . '-' . $item['item_id'] . '">' . esc_html($item['description']) . '</option>';
		}
		echo '</select>';
		echo '<button type="button" class="wxui-btn medium radius3 white wx-edit-choice"><span class="ui-button-text">Edit</span></button> &nbsp; <a href="#" class="wx-delete-choice">Delete</a>';
		echo '</div>';
	}*/
}

?>

<div id="wx-change-icon-dialog" class="wx-jquery-dialog wx-hide">
    <h2>Change Icon</h2>
    <?php include('list_icon_picker.php'); ?>
</div>

<div id="wx-change-map-options-dialog" class="wx-jquery-dialog wx-hide">
	<div>
    	<input type="checkbox" value="1" id="map_options_cluster" /> <label for="map_options_cluster">Cluster markers?</label>
	</div>
	<div>
        <input type="checkbox" value="1" id="map_options_autoGPS" /> <label for="map_options_autoGPS">Automatically ask for GPS position?</label>
	</div>
	<div>
		Starting latitude: <input type="text" id="map_options_start_latitude" />
	</div>
    <div>
        Starting longitude: <input type="text" id="map_options_start_longitude" />
    </div>
    <div>
        Starting zoom level (0-18): <input type="text" id="map_options_start_zoom" />
    </div>
	<div>
        <input type="checkbox" value="1" id="map_options_starting_zoom_enabled" /> <label for="map_options_starting_zoom_enabled">Starting zoom level enabled? (Auto zoom/center otherwise)</label>
	</div>
    <div>
        Max zoom level: <input type="text" id="map_options_maxZoom" />
    </div>
    <div>
        Min zoom level: <input type="text" id="map_options_minZoom" />
    </div>
	<div>
		Custom marker image URL: <input type="text" id="map_options_marker" />
	</div>
    <div>
        Limit distance of results (km): <input type="text" id="map_options_distance" />
    </div>
    <div>
        GPS radius: <input type="text" id="map_options_gpsRadius" />
    </div>
    <div>
        GPS radius color: <input type="text" id="map_options_gpsRadius_colour" />
    </div>
    <input type="hidden" id="map_options_display" />
</div>


<div id="wx-change-layout-dialog" class="wx-jquery-dialog wx-hide">
    <input type="radio" name="tab_layout" value="list" id="tab_layout_list" /> <label for="tab_layout_list">List Layout</label><br />
    <input type="radio" name="tab_layout" value="carousel" id="tab_layout_carousel" /> <label for="tab_layout_carousel">Carousel Layout</label><br />
    <input type="radio" name="tab_layout" value="" id="tab_layout_default" /> <label for="tab_layout_default">Default Layout</label>
</div>

<?php if ( ! apply_filters( 'weever_list_show_wordpress_content', true ) ): ?>
    <div id="wx-add-new-content-dialog" class="wx-jquery-dialog wx-hide" xmlns="http://www.w3.org/1999/html">
        <?php weever_show_edit_dropdown($weeverapp, array('regular_posts', 'map_posts', 'pages'), $type = 'page'); ?>

        <?php // wp_editor( '', 'wx-add-content-editor', array( 'media_buttons' => false, 'textarea_rows' => 5, 'teeny' => true ) ); ?>

        <div id="wx-add-content-title-container">
            Article Title: <input name="wx-add-content-title" id="wx-add-content-title" />
        </div>
        <div id="wx-add-content-editor-container">
            <textarea id="wx-add-content-editor"></textarea>
        </div>

        <div id="wx-add-content-action-buttons">
            <div id="wx-file-uploader">
                <noscript>
                    <p>Please enable JavaScript to use file uploader.</p>
                    <!-- or put a simple form for upload here -->
                    <img src="" id="wx-file-load" />
                </noscript>
            </div>
            <?php if ( ! apply_filters( 'weever_list_show_wordpress_content', true ) and function_exists('wserver_sms_account_info') ): ?>
                <?php $sms_account_info = wserver_sms_account_info( get_option( 'weever_api_key', '' ) ); ?>
                <div id="wx-add-sms-form" style="margin-left: 20px;" class="wxuia-button YellowButton <?php if ( empty( $sms_account_info ) or ! $sms_account_info->user->active or ! $sms_account_info->user->sms_allowed ) echo 'wx-upgrade-prompt-sms'; ?>" style="position: relative; overflow: hidden; direction: ltr; ">Add SMS Form</div>
                <div id="wx-add-mobile-coupon" class="wxuia-button YellowButton" style="margin-left: 20px; position: relative; overflow: hidden; direction: ltr; ">Add Mobile Coupon</div>
            <?php endif; ?>
        </div>
        <div id="wx-mobile-coupon-generator" style="clear:left;">
            <div id="wx-mobile-coupon-preview">
                <div class="wx-coupon-container">
                    <div style="padding: 20px; border: dashed 2px #333; background: #fff;">
                        <div class="wx_title_preview" style="font-size: 2em; line-height: 1; font-weight: bold; background: #990000; padding: .5em; box-sizing: border-box; -webkit-box-sizing: border-box; color: #fff;">
                            <span id="wx_coupon_title_preview">Coupon Title</span>
                        </div>
                        <p style="margin: 1em;">
                            <span id="wx_coupon_content_preview">EXAMPLE: Shop anytime in the next 30 days and receive up to 25% off</span>
                        </p>
                        <div id="wx_coupon_barcode_preview" style="text-align:center;"></div>
                        <div style="">
                            <p id="wx_coupon_conditions_preview" style="display: block; box-sizing: border-box; -webkit-box-sizing: border-box; font-size: .75em; margin: 1.5em 0 0; border-style: solid; border-width: 1px 0 0 0; border-color: #d5d5d5; padding-top: 1.5em;">EXAMPLE: Limit One Coupon Per Customer</p>
                        </div>
                    </div>
                    <br />
                </div>
           </div>

           <div id="wxuia-addcouponinputs">

           <p><label class="wx-jqui-label">Give Your Coupon a Short Title:</label>
           <input type="text" class="wx_coupon_field wx-dialog-input" id="wx_coupon_title" placeholder="Coupon Title" />
           </p>

           <p>
                <label class="wx-jqui-label">Describe the Coupon Offer:</label>
                <textarea class="wx_coupon_field wx-dialog-input" id="wx_coupon_content" placeholder="EXAMPLE: Shop anytime in the next 30 days and receive up to 25% off"></textarea>
           </p>

           <p><label class="wx-jqui-label">Optional: Add a Scannable Bar Code</label>
           <input  class="wx-dialog-input" type="text" id="wx_coupon_barcode" placeholder="2013-30Day-Sale" /><br/>
           <span style="color: #666;">The Barcode graphic will be added to the coupon in your app when you save this form.</span>
           </p>

          <p><label class="wx-jqui-label">Enter any Terms and Conditions:</label>
          <input class="wx_coupon_field wx-dialog-input" type="text" id="wx_coupon_conditions" placeholder="EXAMPLE: Limit One Coupon Per Customer" /><br/>
          </p>

          </div>

           <h3>Coupon Color Options:</h3>
            <table class="admintable colortable" id="colortable-logo">
                <tr>
                    <th><?php echo __( 'Coupon Title Background Colour', 'weever' ); ?></th>
                    <th><?php echo __( 'Coupon Title Text Colour', 'weever' ); ?></th>
                </tr>
                <tr>
                    <td>
                        <input type="text" class="wx_coupon_title_color hexacolorinput" rel="background" id="main_titlebar_color" name="main_titlebar_color" value="#990000" />
                        <div id="main_titlebar_colorpicker"></div>
                    </td>
                    <td>
                        <input type="text" class="wx_coupon_title_color hexacolorinput" rel="color" id="main_titlebar_text_color" name="main_titlebar_text_color" value="#ffffff" />
                        <div id="main_titlebar_text_colorpicker"></div>
                    </td>
                </tr>
            </table>

            <div id="wx-inject-mobile-coupon" class="wxuia-button YellowButton" style="position: relative; overflow: hidden; direction: ltr; ">Create Mobile Coupon</div>
        </div>

        <input type="hidden" name="wx-add-content-type" value="" />
        <div class="wxui-geolocation">
            <label class="screen-reader-text" for="geolocation-address">Geolocation</label>
            <div style="color: #333; font-size:14px; font-weight: bold; margin-bottom: 10px; line-height: 1.5;">Enter your address</div>
            <input type="text" id="geolocation-address" name="geolocation-address" class="newtag form-input-tip" size="25" autocomplete="off" value="" />
            <input id="geolocation-load" type="button" class="button geolocationadd" value="Submit" tabindex="3" />
            <input type="hidden" id="geolocation-latitude" name="geolocation-latitude" />
            <input type="hidden" id="geolocation-longitude" name="geolocation-longitude" />
            <div id="geolocation-map" style="border:solid 1px #c6c6c6;width:500px;height:200px;margin-top:5px;"></div>
        </div>
    </div>
<?php endif; ?>

<div style="clear:both;" id="wx-hidden-dialogs">&nbsp;</div>

<div id="wx-add-blog-type-dialog" class="wx-jquery-dialog wx-hide"></div>
<div id="wx-add-page-type-dialog" class="wx-jquery-dialog wx-hide"></div>
<div id="wx-add-panel-type-dialog" class="wx-jquery-dialog wx-hide"></div>
<div id="wx-add-map-type-dialog" class="wx-jquery-dialog wx-hide"></div>
<div id="wx-add-video-type-dialog" class="wx-jquery-dialog wx-hide"></div>
<div id="wx-add-photo-type-dialog" class="wx-jquery-dialog wx-hide"></div>
<div id="wx-add-calendar-type-dialog" class="wx-jquery-dialog wx-hide"></div>
<div id="wx-add-social-type-dialog" class="wx-jquery-dialog wx-hide"></div>
<div id="wx-add-form-type-dialog" class="wx-jquery-dialog wx-hide"></div>
<div id="wx-add-contact-type-dialog" class="wx-jquery-dialog wx-hide"></div>
<div id="wx-add-directory-type-dialog" class="wx-jquery-dialog wx-hide"></div>
<div id="wx-add-aboutapp-type-dialog" class="wx-jquery-dialog wx-hide"></div>
<div id="wx-add-audio-type-dialog" class="wx-jquery-dialog wx-hide"></div>
<div id="wx-add-product-type-dialog" class="wx-jquery-dialog wx-hide"></div>
<div id="wx-add-proximity-type-dialog" class="wx-jquery-dialog wx-hide"></div>

<div id="wx-add-wordpress_contact-dialog" class="wx-jquery-dialog wx-hide">

	
	<?php weever_show_edit_dropdown( $weeverapp, array('wordpress_contact') ); ?>
	

	<div class='wx-add-contact'>
   <h3 class="wxui-helptip-h3"><img src="<?php echo WEEVER_PLUGIN_URL; ?>static/images/icons/icons_library/phone_48.png" class="wxui-helptip-icon">Add Click to Call, Email and Location Info</h3>
    
    	<div class='wx-add-title'>
    		<label for='wx-contact-dialog-title'><?php echo __( 'Name', 'weever' ); ?></label>
    		<input type='text' value='' id='wx-contact-dialog-title' class='wx-title wx-input wx-contact-input required' name='contactname' />
    	</div>

    	<div class='wx-add-title'>
    		<label for='wx-contact-dialog-phone'><?php echo __( 'Phone Number', 'weever' ); ?></label>
    		<input type='text' value='' id='wx-contact-dialog-phone' class='wx-title wx-input wx-contact-input' name='phone' />
    	</div>

    	<div class='wx-add-title'>
    		<label for='wx-contact-dialog-email'><?php echo __( 'E-mail Address', 'weever' ); ?></label>
    		<input type='text' value='' id='wx-contact-dialog-email' class='wx-title wx-input wx-contact-input' name='email' />
    	</div>

    	<div class='wx-add-title'>
    		<label for='wx-contact-dialog-address'><?php echo __( 'Address', 'weever' ); ?></label>
    		<input type='text' value='' id='wx-contact-dialog-address' class='wx-title wx-input wx-contact-input' name='address' />
    	</div>

    	<div class='wx-add-title'>
    		<label for='wx-contact-dialog-town'><?php echo __( 'Town', 'weever' ); ?></label>
    		<input type='text' value='' id='wx-contact-dialog-town' class='wx-title wx-input wx-contact-input' name='town' />
    	</div>

    	<div class='wx-add-title'>
    		<label for='wx-contact-dialog-state'><?php echo __( 'State/Province', 'weever' ); ?></label>
    		<input type='text' value='' id='wx-contact-dialog-state' class='wx-title wx-input wx-contact-input' name='state' />
    	</div>

    	<div class='wx-add-title'>
    		<label for='wx-contact-dialog-country'><?php echo __( 'Country', 'weever' ); ?></label>
    		<input type='text' value='' id='wx-contact-dialog-country' class='wx-title wx-input wx-contact-input' name='country' />
    	</div>

    	<div class='wx-add-title'>
    		<label for='wx-contact-dialog-misc'><?php echo __( 'Miscellaneous Text', 'weever' ); ?></label>
    		<textarea id='wx-contact-dialog-misc' class='wx-title wx-input wx-contact-input' name='misc'></textarea>
    	</div>
    	<?php if ( apply_filters( 'weever_show_contact_url', true ) ): ?>
    	<div class='wx-add-title'>
    		<label for='wx-contact-dialog-image'><?php echo __( 'Contact Photo URL', 'weever' ); ?></label>
    		<input type='text' value='' id='wx-contact-dialog-image' placeholder='http://' class='wx-title wx-input wx-contact-input' name='image' />
    	</div>
		<?php else: ?>
    	<input type='hidden' value='' id='wx-contact-dialog-image' placeholder='http://' class='wx-title wx-input wx-contact-input' name='image' />
		<?php endif; ?>
        <div style="clear:both; overflow:hidden;"></div>
	</div>

</div>



<div id="wx-add-title-tab-dialog" class="wx-jquery-dialog wx-hide">

	<div id="wx-add-confirm-feed">
		<h2>Is this the feed you were trying to add?  If not, go back and verify the information entered.</h2>
		
		<div id="wx-add-confirm-feed-loading"><img src="<?php echo WEEVER_PLUGIN_URL; ?>static/images/ajax-loader.gif" /> Loading...</div>
		
		<table id="wx-add-confirm-feed-data">
			<tbody>
			</tbody>
		</table>
	</div>

	<h2>Give this content a title</h2>
	
	<p id="wx-add-title-use"></p>

	<input type="text" id="wx-add-title-tab-item" class="wx-input" />	

    <h2>Select an Icon</h2>
    
    <?php include('list_icon_picker.php'); ?>
    
</div>

<div id="wx-add-wordpress-blog-dialog" class="wx-jquery-dialog wx-hide wx-choose-content">
    <div id='wx-add-blog-menu-item'>
        <select id='wx-add-wordpress-blog-select' class='wx-cms-feed-select'>
            <option><?php echo __('Chose an option...', 'weever'); ?></option>

            <option value="index.php?feed=r3s">All published posts</option>
            <?php
            $args = array(
                    'public'   => true,
                    '_builtin' => false,
                    'publicly_queryable' => true,
                    );

            $output = 'objects'; // names or objects, note names is the default
            $operator = 'and'; // 'and' or 'or'
            $post_types = get_post_types( $args, $output, $operator );
            ?>
            <?php foreach ($post_types as $post_type ): ?>
            <option value="index.php?feed=r3s&post_type=<?php echo $post_type->name; ?>"><?php echo sprintf( __( 'All %s', 'weever'), $post_type->labels->name ); ?></option>
            <?php endforeach; ?>
        </select>
    </div>
</div>


<?php

function weever_tag_select_dropdown($id, $class = 'wx-cms-feed-select') {
    ?>
    <select id='<?php echo $id; ?>' class='<?php echo $class; ?>'>
        <option><?php echo __('Choose a tag...'); ?></option>

        <?php
        $categories = array();
        foreach ( get_taxonomies( array( 'public' => true ), 'objects' ) as $taxonomy ) {
            if ( ! $taxonomy->query_var || $taxonomy->query_var != 'tag' ) continue;
            foreach ( get_terms( $taxonomy->name ) as $term ) {
                $categories[] = array('link' => WeeverHelper::get_term_feed_link_relative( $term ),
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

    </select>
    <?php
}

function weever_category_select_dropdown($id, $class = 'wx-cms-feed-select') {
    ?>
        <select id='<?php echo $id; ?>' class='<?php echo $class; ?>'>
            <option><?php echo __('Choose a category...'); ?></option>

            <?php
                $categories = array();
                foreach ( get_taxonomies( array( 'public' => true ), 'objects' ) as $taxonomy ) {
                    if ( ! $taxonomy->query_var || $taxonomy->query_var == 'post_format' || $taxonomy->query_var == 'tag' ) continue;
                    foreach ( get_terms( $taxonomy->name ) as $term ) {
                        $categories[] = array('link' => WeeverHelper::get_term_feed_link_relative( $term ),
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

		</select>
    <?php
}

?>

<div id="wx-add-wordpress-category-dialog" class="wx-jquery-dialog wx-hide wx-choose-content">
		
	<div id='wx-add-blog-jcategory-item'>
        <?php echo weever_category_select_dropdown('wx-add-wordpress-blog-category_name-select'); ?>
	</div>

</div>

<div id="wx-add-wordpress-map-dialog" class="wx-jquery-dialog wx-hide wx-choose-content">

    <div id='wx-add-blog-map-item'>
        <?php echo weever_category_select_dropdown('wx-add-wordpress-map-select'); ?>
    </div>

</div>

<div id="wx-add-wordpress-proximity-dialog" class="wx-jquery-dialog wx-hide wx-choose-content">

    <div id='wx-add-blog-proximity-item'>
        <?php echo weever_category_select_dropdown('wx-add-wordpress-proximity-select'); ?>
    </div>

</div>

<div id="wx-add-wordpress-directory-dialog" class="wx-jquery-dialog wx-hide wx-choose-content">

    <div id='wx-add-blog-directory-item'>
        <?php echo weever_category_select_dropdown('wx-add-wordpress-directory-select'); ?>
    </div>

</div>

<div id="wx-add-wordpress-page-dialog" class="wx-jquery-dialog wx-hide wx-choose-content">

	<div id='wx-add-panel-content-joomla'>
		<select id="wx-add-wordpress-page-menu-item-select">
			<option value=""><?php echo __( 'Choose a Page...', 'weever' ); ?></option>
			<?php foreach ( get_pages() as $page ): ?>
			<option value="<?php echo WeeverHelper::get_page_link_relative( $page ); ?>"><?php echo $page->post_title; ?></option>
			<?php endforeach; ?>
		</select>		
	</div>

</div>



<div id="wx-add-wordpress-searchterm-dialog" class="wx-jquery-dialog wx-hide wx-choose-content">

	<h2>Enter a search term and content matching the search term will be added to the app</h2>

	<input type="text" id="wx-add-wordpress-searchterm" class="wx-input" />	

</div>


<div id="wx-add-wordpress-tag-dialog" class="wx-jquery-dialog wx-hide wx-choose-content">
		
	<div id='wx-add-blog-tag-item'>
        <?php weever_tag_select_dropdown('wx-add-wordpress-tag-select'); ?>
	</div>

</div>

<div id="wx-add-twitter-description" class="wx-hide">
	
	<?php weever_show_edit_dropdown( $weeverapp, array('twitter-user', 'twitter-hashtag', 'twitter-search') ); ?>
	
	<div class='wx-service-description'>
		<h3 class="wxui-helptip-h3"><img src="<?php echo WEEVER_PLUGIN_URL; ?>static/images/icons/icons_library/help_48.png" class="wxui-helptip-icon">Help Tips:</h3><p>Planning an event?  Adding a twitter #hashtag stream for a specific event encourages people to tweet and share photos.</p>
	</div>
</div>

<div id="wx-add-twitter-user-dialog" class="wx-jquery-dialog wx-hide wx-service-twitter wx-choose-content">

	<div>

		<label for='wx-twitter-user-value' id='wx-twitter-user' class='wx-social-label  wx-jqui-label'><?php echo __( 'Enter a Twitter Handle, e.g. <b>@mytwittername</b>', 'weever' ); ?></label>	
		<input type='text' value='' class='wx-dialog-input wx-social-input' id='wx-twitter-user-value' name='component_behaviour' placeholder='@mytwittername' />
		
		<div class='wx-service-description'>
			<h3 class="wxui-helptip-h3"><img src="<?php echo WEEVER_PLUGIN_URL; ?>static/images/icons/icons_library/help_48.png" class="wxui-helptip-icon">Help Tips:</h3><p>Planning an event?  Adding a twitter #hashtag stream for a specific event encourages people to tweet and share photos.</p>
		</div>
		
	</div>

</div>

<div id="wx-add-twitter-hashtag-dialog" class="wx-jquery-dialog wx-hide wx-service-twitter wx-choose-content">

	<div>
		<label for='wx-twitter-hashtag-value' id='wx-twitter-hashtag' class='wx-social-label  wx-jqui-label'><?php echo __( 'Enter a Twitter #Hashtag, e.g. <b>#MyHashtag</b>', 'weever' ); ?></label>
		<input type='text' value='' class='wx-dialog-input wx-social-input' id='wx-twitter-hashtag-value' name='component_behaviour' placeholder='#MyHashtag' />		
		
		<div class='wx-service-description'>
			<h3 class="wxui-helptip-h3"><img src="<?php echo WEEVER_PLUGIN_URL; ?>static/images/icons/icons_library/help_48.png" class="wxui-helptip-icon">Help Tips:</h3><p>Planning an event?  Adding a twitter #hashtag stream for a specific event encourages people to tweet and share photos.</p>
		</div>
		
	</div>

</div>

<div id="wx-add-twitter-search-dialog" class="wx-jquery-dialog wx-hide wx-service-twitter wx-choose-content">

	<div>
		<label for='wx-twitter-search-value' id='wx-twitter-query' class='wx-social-label  wx-jqui-label'><?php echo __( 'Enter a Search Keyword for Twitter', 'weever' ); ?></label>		
		<input type='text' value='' class='wx-dialog-input wx-social-input' id='wx-twitter-search-value' name='component_behaviour' placeholder='Enter a Search Term' />
		
		<div class='wx-service-description'>
			<h3 class="wxui-helptip-h3"><img src="<?php echo WEEVER_PLUGIN_URL; ?>static/images/icons/icons_library/help_48.png" class="wxui-helptip-icon">Help Tips:</h3><p>Planning an event?  Adding a twitter #hashtag stream for a specific event encourages people to tweet and share photos.</p>
		</div>
				
	</div>

</div>

<div id="wx-add-facebook-dialog" class="wx-jquery-dialog wx-hide wx-service-facebook wx-choose-content">

</div>

<div id="wx-add-facebook-social-dialog" class="wx-jquery-dialog wx-hide wx-service-facebook wx-choose-content">

	<div>
    	<label for='wx-facebook-user-value' id='wx-facebook-url' class='wx-social-label  wx-jqui-label'><?php echo __( 'Enter a Facebook Page URL, e.g. <b>http://facebook.com/MyPage</b>', 'weever' ); ?></label>
		<input type='text' value='' class='wx-dialog-input wx-social-input' id='wx-facebook-user-value' name='component_behaviour' value='http://facebook.com/' />
		
		<?php weever_show_edit_dropdown( $weeverapp, array('facebook-social', 'facebook-events', 'facebook-photos', ) ); ?>
		
		<div class='wx-service-description'>
			<h3 class="wxui-helptip-h3"><img src="<?php echo WEEVER_PLUGIN_URL; ?>static/images/icons/icons_library/help_48.png" class="wxui-helptip-icon">Help Tips:</h3><p>Due to privacy settings, only <strong>Facebook Page</strong> content can be shared in a mobile app.  Make sure you're adding a public-facing page and not a <em>personal</em> profile.</p>
		</div>
	</div>

</div>

<div id="wx-add-facebook-events-dialog" class="wx-jquery-dialog wx-hide wx-service-facebook wx-choose-content">

	<div>
		
        <label for='wx-facebook-calendar-value' id='wx-facebook-url' class='wx-social-label  wx-jqui-label'><?php echo __( 'Enter a Facebook Page URL, e.g. <b>http://facebook.com/MyPage</b>', 'weever' ); ?></label>
		<input type='text' value='' class='wx-dialog-input wx-social-input' id='wx-facebook-calendar-value' name='component_behaviour' value='http://facebook.com/' />
		
		<?php weever_show_edit_dropdown( $weeverapp, array('facebook-social', 'facebook-events', 'facebook-photos', ) ); ?>
		
		<div class='wx-service-description'>
			<h3 class="wxui-helptip-h3"><img src="<?php echo WEEVER_PLUGIN_URL; ?>static/images/icons/icons_library/help_48.png" class="wxui-helptip-icon">Help Tips:</h3><p>Due to privacy settings, only <strong>Facebook Page</strong> content can be shared in a mobile app.  Make sure you're adding a public-facing page and not a <em>personal</em> profile.</p>
		</div>
		
	</div>

</div>

<div id="wx-add-facebook-photos-dialog" class="wx-jquery-dialog wx-hide wx-service-facebook wx-choose-content">

	<div>
		
        <label for='wx-facebook-stream-value' id='wx-facebook-url' class='wx-social-label  wx-jqui-label'><?php echo __( 'Enter a Facebook Page URL, e.g. <b>http://facebook.com/MyPage</b>', 'weever' ); ?></label>
		<input type='text' value='' class='wx-dialog-input wx-social-input' id='wx-facebook-photo-value' name='component_behaviour' value='http://facebook.com/' />

		<?php weever_show_edit_dropdown( $weeverapp, array('facebook-social', 'facebook-events', 'facebook-photos', ) ); ?>
		
		<div class='wx-service-description'>
			<h3 class="wxui-helptip-h3"><img src="<?php echo WEEVER_PLUGIN_URL; ?>static/images/icons/icons_library/help_48.png" class="wxui-helptip-icon">Help Tips:</h3><p>Due to privacy settings, only <strong>Facebook Page</strong> content can be shared in a mobile app.  Make sure you're adding a public-facing page and not a <em>personal</em> profile.</p>
		</div>
	</div>

</div>

<div id="wx-add-youtube-description" class="wx-hide">
	<?php weever_show_edit_dropdown( $weeverapp, array( 'youtube-playlist', 'youtube-channel' ) ); ?>

	<div class='wx-service-description'>
		<h3 class="wxui-helptip-h3"><img src="<?php echo WEEVER_PLUGIN_URL; ?>static/images/icons/icons_library/help_48.png" class="wxui-helptip-icon">Help Tips:</h3><p>A YouTube <strong>Channel</strong> displays all videos associated with one <a href="http://youtube.com" target="_blank">YouTube</a> user account.  If you would like to show a collection of videos from around YouTube, add a YouTube <i>Playlist</i>.
	</div>
</div>

<div id="wx-add-youtube-channel-dialog" class="wx-jquery-dialog wx-hide wx-service-youtube wx-choose-content">

	<div>
		
		<label for='wx-youtube-channel-url' id='wx-youtube-url' class='wx-video-label wx-jqui-label'><?php echo __( 'Enter a YouTube Channel ID, e.g. <b>MyChannelName</b><br /><em>OR</em> the URL to your channel, e.g. <b>http://youtube.com/MyChannelName</b>', 'weever' ); ?></label>
		<input type='text' value='' name='component_behaviour' id='wx-youtube-channel-url' class='wx-dialog-input wx-video-input' placeholder='MyChannelName' />
		
		<div class='wx-service-description'>
			<h3 class="wxui-helptip-h3"><img src="<?php echo WEEVER_PLUGIN_URL; ?>static/images/icons/icons_library/help_48.png" class="wxui-helptip-icon">Help Tips:</h3><p>A YouTube <strong>Channel</strong> displays all videos associated with one <a href="http://youtube.com" target="_blank">YouTube</a> user account.  If you'd like to show a collection of videos from around YouTube, add a YouTube <i>Playlist</i>.
		</div>
	</div>

</div>



<div id="wx-add-youtube-playlist-dialog" class="wx-jquery-dialog wx-hide wx-service-youtube wx-choose-content">

	<div>
		
		<label for='wx-youtube-playlist-url' id='wx-youtube-playlist-url-label' class='wx-video-label wx-jqui-label'><?php echo __( 'Enter a YouTube Playlist URL, <br>e.g. <b>http://www.youtube.com/playlist?list=abcd1234</b>', 'weever' ); ?></label>
		<input type='text' value='' placeholder='http://youtube.com/playlist?list=abcd1234' name='component_behaviour' id='wx-youtube-playlist-url' class='wx-dialog-input wx-video-input' />
				
		<div class='wx-service-description'>
			<h3 class="wxui-helptip-h3"><img src="<?php echo WEEVER_PLUGIN_URL; ?>static/images/icons/icons_library/help_48.png" class="wxui-helptip-icon">Help Tips:</h3><p>A YouTube <strong>Channel</strong> displays all videos associated with one <a href="http://youtube.com" target="_blank">YouTube</a> user account.  If you'd like to show a collection of videos from around YouTube, add a YouTube <i>Playlist</i>.
		</div>
		
	</div>

</div>


<div id="wx-add-vimeo-dialog" class="wx-jquery-dialog wx-hide wx-service-vimeo wx-choose-content">

	<div>
		
		<label for='wx-vimeo-channel-url' id='wx-vimeo-url' class='wx-video-label wx-jqui-label'><?php echo __( 'Enter a Vimeo User or Channel URL, e.g. <b>https://vimeo.com/user1234</b>', 'weever' ); ?></label>
		<input type='text' value='' placeholder='https://vimeo.com/user1234' name='component_behaviour' id='wx-vimeo-channel-url' class='wx-dialog-input wx-video-input' />

		<?php weever_show_edit_dropdown( $weeverapp, array('vimeo') ); ?>
		
		<div class='wx-service-description'>				
			<h3 class="wxui-helptip-h3"><img src="<?php echo WEEVER_PLUGIN_URL; ?>static/images/icons/icons_library/help_48.png" class="wxui-helptip-icon">Help Tips:</h3><p>Adding a Vimeo User shares all videos associated with one <a href="http://vimeo.com" target="_blank">Vimeo</a> user account.  To share a collections of videos add <i>channels</i>.  One Vimeo user can have multiple channels.
		</div>				
	</div>

</div>


<div id="wx-add-wufoo-dialog" class="wx-jquery-dialog wx-hide wx-service-wufoo wx-choose-content">

	<div>
		
        <label for='wx-wufoo-form-url' class='wx-jqui-label'><?php echo __( 'Enter a Wufoo Account URL', 'weever' ); ?></label>
		<input type='text' placeholder='https://USERNAME.wufoo.com' id='wx-wufoo-form-url' class='wx-dialog-input wx-form-input' name='url' />
		
        <label for='wx-wufoo-form-api-key' class='wx-jqui-label'><?php echo __( 'Enter a Matching Wufoo API Key', 'weever' ); ?></label>
		<input type='text' placeholder='XXXX-XXXX-XXXX-XXXX' id='wx-wufoo-form-api-key' class='wx-dialog-input wx-form-input' name='api_key' />
				
		<?php weever_show_edit_dropdown( $weeverapp, array('wufoo') ); ?>
				
		<div class='wx-service-description'>
			<h3 class="wxui-helptip-h3"><img src="<?php echo WEEVER_PLUGIN_URL; ?>static/images/icons/icons_library/help_48.png" class="wxui-helptip-icon">Help Tips:</h3><p>Wufoo's free online form creator can power your app's contact forms, surveys, and event registrations. <b>To find your Wufoo API Key:</b></p>
		
			<ol><li>Login to <a href="http://wufoo.com/" target="_blank">Wufoo.com</a> and navigate to a specific form.</li><li>Choose the "Code" link, then click the "API Information" button.</li></ol>
			
			<h3 class="wxui-helptip-h3"><img src="<?php echo WEEVER_PLUGIN_URL; ?>static/images/icons/icons_library/network_48.png" class="wxui-helptip-icon">Connect a Form to Your Favourite Web Service!</h3>

			<p>Wufoo Forms connects to many free and paid services on the web including MailChimp email newsletters, Campaign Monitor, PayPal Donations and Payments, SalesForce CRM, Freshbooks Accounting &amp; Billing, Highrise Contact Management and Twitter.
			
			<p>For more information check out: <a href="http://wufoo.com/integrations" target="_blank">http://wufoo.com/integrations</a></p>
		</div>
	</div>

</div>


<div id="wx-add-foursquare-dialog" class="wx-jquery-dialog wx-hide wx-service-foursquare wx-choose-content">

	<div>
    	
        <label for='wx-foursquare-photo-url' id='wx-foursquare-url' class='wx-photo-label wx-jqui-label'><?php echo __( 'Enter a Foursquare Venue Page URL, e.g. <b>https://foursquare.com/v/your-venue/abcd123456</b>', 'weever' ); ?></label>	
    	<input type='text' value='' placeholder='http://foursquare.com/v/your-venue/abcd123456' id='wx-foursquare-photo-url' class='wx-dialog-input wx-photo-input' name='url' />			
	
		<?php weever_show_edit_dropdown( $weeverapp, array('foursquare') ); ?>
	
		<div class='wx-service-description'>
			<h3 class="wxui-helptip-h3"><img src="<?php echo WEEVER_PLUGIN_URL; ?>static/images/icons/icons_library/help_48.png" class="wxui-helptip-icon">Help Tips:</h3>
			<p>Sharing a Live Stream of <strong><a href="http://foursquare.com" target="_blank">Foursquare</a> Venue Photos</strong> for a particular location is a great way to engage event attendees or share info about a specific place!</p>
			<p>To find the URL (web address) for a Foursquare Venue:</p>
			<ol><li>Search the web for "Foursquare" and the name of the location (venue)</li>
			<li>Open the Foursquare Venue Page</li>
			<li>Copy the URL of the page from your browser address bar</li></ol>
		</div>	
    </div>

</div>


<div id="wx-add-picasa-dialog" class="wx-jquery-dialog wx-hide wx-service-picasa wx-choose-content">

	<div>
		
        <label for='wx-picasa-photo-url' id='wx-google-picasa-email' class='wx-photo-label wx-jqui-label'><?php echo __('Enter a Picasa User Profile URL or Email Address', 'weever'); ?></label>
		<input type='text' value='' placeholder='your.email@gmail.com' id='wx-picasa-photo-url' class='wx-dialog-input wx-photo-input' name='url' />

		<?php weever_show_edit_dropdown( $weeverapp, array('picasa') ); ?>
	
		<div class='wx-service-description'>
			<h3 class="wxui-helptip-h3"><img src="<?php echo WEEVER_PLUGIN_URL; ?>static/images/icons/icons_library/help_48.png" class="wxui-helptip-icon">Help Tips:</h3>
			<p>Showcase all your <a href="http://picasa.google.com" target="_blank">Picasa</a> photo albums!</p>
			<h3 class="wxui-helptip-h3"><img src="<?php echo WEEVER_PLUGIN_URL; ?>static/images/icons/icons_library/present_48.png" class="wxui-helptip-icon">Coming Soon</h3>
			<p>Support for Flickr Group Pools (community galleries) and adding specific photo sets.</p>
		</div>
	
    </div>

</div>


<div id="wx-add-google_calendar-dialog" class="wx-jquery-dialog wx-hide wx-service-google_calendar wx-choose-content">

	<div>
    
		<label for='wx-google-calendar-email' id='wx-google-calendar-email-label' class='wx-jqui-label'><?php echo __('Enter a Google Calendar Address ID, e.g. <b>abc123@group.calendar.google.com</b> <br/><em>OR</em> Your Gmail ID (for a primary calendar), e.g. <b>my.email@gmail.com</b>', 'weever'); ?></label>
        <input type='text' placeholder='abs123@group.calendar.google.com' id='wx-google-calendar-email' class='wx-calendar-input wx-dialog-input' name='email' />

		<?php weever_show_edit_dropdown( $weeverapp, array('google_calendar') ); ?>
        
        <div class='wx-service-description'>
			<h3 class="wxui-helptip-h3"><img src="<?php echo WEEVER_PLUGIN_URL; ?>static/images/icons/icons_library/help_48.png" class="wxui-helptip-icon">Help Tips:</h3>
			<p>To add the <strong>Primary Calendar</strong> associated with a Google account, simply enter the email address associated with that account (example@mail.com)</p>
			<h3 class="wxui-helptip-h3"><img src="<?php echo WEEVER_PLUGIN_URL; ?>static/images/icons/icons_library/gear_48.png" class="wxui-helptip-icon">How to Find the ID of a Group / Secondary Calendar</h3>
			<ol><li>Login to <a href="www.google.com/calendar" target="_blank">www.google.com/calendar</a> if you are not logged in already</li><li>Highlight a specific calendar name and choose 'calendar settings'</li><li>Copy and paste the calendar ID from this page.  It will be in a format like "abc123@group.calendar.google.com"</li></ol>
			<h3 class="wxui-helptip-h3"><img src="<?php echo WEEVER_PLUGIN_URL; ?>static/images/icons/icons_library/gear_48.png" class="wxui-helptip-icon">Privacy Settings</h3>
			<p>Only <em>publicly available</em> Google Calendars will display.  Choose 'share this calendar' to confirm your calendars are public and allow for a one day delay on any changes.</p>
		</div>
    </div>

</div>

<div id="wx-add-blogger-dialog" class="wx-jquery-dialog wx-hide wx-service-blogger wx-choose-content">

	<div>
		<label for='wx-add-blog-blogger-url-input' id='wx-add-blog-blogger-url-input-label' class='wx-blog-label wx-jqui-label'><?php echo __('Enter a Blogger or Blogspot Site URL, e.g. <b>http://yourblogname.blogspot.com/</b>', 'weever'); ?></label>
        <input type='text' value='' id='wx-add-blog-blogger-url-input' class='wx-dialog-input wx-blog-input' name='unnamed' placeholder='<?php echo __('http://yourblogname.blogspot.com/', 'weever'); ?>' />
	</div>

</div>


<div id="wx-add-tumblr-dialog" class="wx-jquery-dialog wx-hide wx-service-tumblr wx-choose-content">

	<div>
    
		<label for='wx-add-blog-tumblr-url-input' id='wx-add-blog-tumblr-url-input-label' class='wx-blog-label wx-jqui-label'><?php echo __('WEEVER_ADD_TUMBLR_URL_LABEL'); ?></label>
		<input type='text' value='' id='wx-add-blog-tumblr-url-input' class='wx-dialog-input wx-blog-input' name='unnamed' placeholder='<?php echo __("WEEVER_TUMBLR_URL_PLACEHOLDER"); ?>' />
				
	</div>

</div>


<div id="wx-add-google_plus-dialog" class="wx-jquery-dialog wx-hide wx-service-google_plus wx-choose-content">

	<div>
    
		<label for='wx-add-google_plus-url-input' id='wx-add-google_plus-url-input-label' class='wx-social-label wx-jqui-label'><?php echo __('WEEVER_ADD_GOOGLE_PLUS_URL_LABEL'); ?></label>
		<input type='text' value='' id='wx-add-google_plus-url-input' class='wx-dialog-input wx-social-input' name='unnamed' placeholder='<?php echo __("WEEVER_GOOGLE_PLUS_URL_PLACEHOLDER"); ?>' />
				
	</div>

</div>


<div id="wx-add-identica-dialog" class="wx-jquery-dialog wx-hide wx-service-identica wx-choose-content">

	<div>
		
        <label for='wx-identica-social-value' id='wx-identica-query' class='wx-social-label wx-jqui-label'><?php echo __('Enter a Search Keyword for Identi.ca', 'weever'); ?></label>
		<input type='text' value='' class='wx-dialog-input wx-social-input' id='wx-identica-social-value' name='component_behaviour' placeholder='Enter a Search Query' />

	</div>

</div>


<div id="wx-add-flickr-description" class="wx-hide">

	<?php weever_show_edit_dropdown( $weeverapp, array('flickr') ); ?>

	<div class='wx-service-description'>
		<h3 class="wxui-helptip-h3"><img src="<?php echo WEEVER_PLUGIN_URL; ?>static/images/icons/icons_library/help_48.png" class="wxui-helptip-icon">Help Tips:</h3>
		<p>Add a Flickr Photostream (stream all photos) for one <a href="http://flickr.com" target="_blank">Flickr</a> user account, or list all your Flickr Photo Sets (galleries).</p>
		
		<h3 class="wxui-helptip-h3"><img src="<?php echo WEEVER_PLUGIN_URL; ?>static/images/icons/icons_library/gear_48.png" class="wxui-helptip-icon">Flickr Compatibility</h3>
		
		<p>Only <em>publicly available</em> photos on Flickr will display. Photos uploaded prior to April 2011 may not display as gallery thumbnails – simply rotate and resave these photos to fix.</p>
		
		<h3 class="wxui-helptip-h3"><img src="<?php echo WEEVER_PLUGIN_URL; ?>static/images/icons/icons_library/present_48.png" class="wxui-helptip-icon">Coming Soon</h3>
		
		<p>Support for Flickr Group Pools (community galeries) and adding just one photo set at a time.</p>
	</div>
</div>


<div id="wx-add-flickr-photostream-dialog" class="wx-jquery-dialog wx-hide wx-service-flickr wx-choose-content">

	<div>
		
		<label for='wx-flickr-photostream-photo-url' id='wx-flickr-photostream-url' class='wx-photo-label wx-jqui-label'><?php echo __('Enter a Flickr Profile URL, e.g. <b>http://www.flickr.com/photos/99999999@N00/</b>', 'weever'); ?></label>		<input type='text' value='' id='wx-flickr-photostream-photo-url' class='wx-dialog-input wx-photo-input' name='url' placeholder='http://flickr.com/photos/123456@N01' />
		<div class='wx-service-description'>
			<h3 class="wxui-helptip-h3"><img src="<?php echo WEEVER_PLUGIN_URL; ?>static/images/icons/icons_library/help_48.png" class="wxui-helptip-icon">Help Tips:</h3>
			<p>Add a Flickr Photostream (stream all photos) for one <a href="http://flickr.com" target="_blank">Flickr</a> user account, or list all your Flickr Photo Sets (galleries).</p>
			
			<h3 class="wxui-helptip-h3"><img src="<?php echo WEEVER_PLUGIN_URL; ?>static/images/icons/icons_library/gear_48.png" class="wxui-helptip-icon">Flickr Compatibility</h3>
			
			<p>Only <em>publicly available</em> photos on Flickr will display. Photos uploaded prior to April 2011 may not display as gallery thumbnails – simply rotate and resave these photos to fix.</p>
			
			<h3 class="wxui-helptip-h3"><img src="<?php echo WEEVER_PLUGIN_URL; ?>static/images/icons/icons_library/present_48.png" class="wxui-helptip-icon">Coming Soon</h3>
			
			<p>Support for Flickr Group Pools (community galeries) and adding just one photo set at a time.</p>
		</div>
		
	</div>

</div>


<div id="wx-add-flickr-photosets-dialog" class="wx-jquery-dialog wx-hide wx-service-flickr wx-choose-content">

	<div>
		
		<label for='wx-flickr-photosets-photo-url' id='wx-flickr-photosets-url' class='wx-photo-label wx-jqui-label'><?php echo __('Enter a Flickr Profile URL, e.g. <b>http://www.flickr.com/photos/99999999@N00/</b>', 'weever'); ?></label>
        <input type='text' value='' placeholder='http://flickr.com/photos/123456@N01' id='wx-flickr-photosets-photo-url' class='wx-dialog-input wx-photo-input' name='url' />
		<div class='wx-service-description'>
			<h3 class="wxui-helptip-h3"><img src="<?php echo WEEVER_PLUGIN_URL; ?>static/images/icons/icons_library/help_48.png" class="wxui-helptip-icon">Help Tips:</h3>
			<p>Add a Flickr Photostream (stream all photos) for one <a href="http://flickr.com" target="_blank">Flickr</a> user account, or list all your Flickr Photo Sets (galleries).</p>
			
			<h3 class="wxui-helptip-h3"><img src="<?php echo WEEVER_PLUGIN_URL; ?>static/images/icons/icons_library/gear_48.png" class="wxui-helptip-icon">Flickr Compatibility</h3>
			
			<p>Only <em>publicly available</em> photos on Flickr will display. Photos uploaded prior to April 2011 may not display as gallery thumbnails – simply rotate and resave these photos to fix.</p>
			
			<h3 class="wxui-helptip-h3"><img src="<?php echo WEEVER_PLUGIN_URL; ?>static/images/icons/icons_library/present_48.png" class="wxui-helptip-icon">Coming Soon</h3>
			
			<p>Support for Flickr Group Pools (community galeries) and adding just one photo set at a time.</p>
		</div>
	        
	</div>

</div>


<div id="wx-add-soundcloud-set-dialog" class="wx-jquery-dialog wx-hide wx-service-soundcloud wx-choose-content">

	<div>
		
		<label for='wx-soundcloud-set-audio-url' id='wx-soundcloud-set-url' class='wx-audio-label wx-jqui-label'><?php echo __('WEEVER_SOUNDCLOUD_SET_URL'); ?></label>
		<input type='text' value='' id='wx-soundcloud-set-audio-url' class='wx-dialog-input wx-audio-input' name='url' />
				
	</div>

</div>

<div id="wx-add-soundcloud-user-dialog" class="wx-jquery-dialog wx-hide wx-service-soundcloud wx-choose-content">

	<div>
		
		<label for='wx-soundcloud-user-audio-url' id='wx-soundcloud-user-url' class='wx-audio-label wx-jqui-label'><?php echo __('WEEVER_SOUNDCLOUD_USER_URL'); ?></label>
		<input type='text' value='' id='wx-soundcloud-user-audio-url' class='wx-dialog-input wx-audio-input' name='url' />
				
	</div>

</div>


<div id="wx-add-bandcamp-band-dialog" class="wx-jquery-dialog wx-hide wx-service-bandcamp wx-choose-content">

	<div>
		
		<label for='wx-bandcamp-band-audio-url' id='wx-bandcamp-band-url' class='wx-audio-label wx-jqui-label'><?php echo __('WEEVER_BANDCAMP_BAND_URL'); ?></label>
		<input type='text' value='' id='wx-bandcamp-band-audio-url' class='wx-dialog-input wx-audio-input' name='url' />
				
	</div>

</div>


<div id="wx-add-bandcamp-album-dialog" class="wx-jquery-dialog wx-hide wx-service-bandcamp wx-choose-content">

	<div>
		
		<label for='wx-bandcamp-album-audio-url' id='wx-bandcamp-album-url' class='wx-audio-label wx-jqui-label'><?php echo __('WEEVER_BANDCAMP_ALBUM_URL'); ?></label>
		<input type='text' value='' id='wx-bandcamp-album-audio-url' class='wx-dialog-input wx-audio-input' name='url' />
				
	</div>

</div>


<div id="wx-add-r3s-dialog" class="wx-jquery-dialog wx-hide wx-choose-content">

	<p>Add an R3S Object URL</p>
	
	<div id="wx-add-page-r3s-url">
    
		<label for='wx-add-page-r3s-url-input' id='wx-add-page-r3s-url-input-label' class='wx-page-label wx-jqui-label'><?php echo __('<a href="http://support.weeverapps.com/entries/20786806-what-is-the-add-from-r3s-feed-url-option-for" target="_blank">What is an R3S Object?</a>', 'weever'); ?></label>
        <input type='text' value='' id='wx-add-page-r3s-url-input' class='wx-dialog-input wx-page-input' name='unnamed' placeholder='<?php echo __('http://', 'weever'); ?>' />
		
	</div>

</div>

<div id="wx-upgrade-notice-sms" class="wx-jquery-dialog wx-hide">

	<div>
        <?php if ( ! apply_filters( 'weever_list_show_wordpress_content', true ) and function_exists('wserver_sms_account_info') ): ?>

            <h2>This feature requires an SMS messaging subscription</h2>
            <?php $domain = get_blog_details( get_current_blog_id() )->domain; ?>
            <?php switch_to_blog( 1 ); ?>
            <?php $url = home_url(); ?>
            <?php restore_current_blog(); ?>
            <p>You'll need to <a href="<?php echo $url; ?>/sms/?site_url=<?php echo $domain; ?>" target="_blank">upgrade your subscription</a> to add this feature.</p>
        <?php endif; ?>
	</div>

</div>

<div id="wx-upgrade-notice" class="wx-jquery-dialog wx-hide">

	<div>
	
	<h2>This is a Pro feature</h2>
    <?php if ( ! apply_filters( 'weever_list_show_wordpress_content', true ) and function_exists('wserver_sms_account_info') ): ?>
        <?php $domain = get_blog_details( get_current_blog_id() )->domain; ?>
        <?php switch_to_blog( 1 ); ?>
        <?php $url = home_url(); ?>
        <?php restore_current_blog(); ?>
    <?php else: ?>
        <?php $domain = $weeverapp->primary_domain; ?>
    <?php endif; ?>
	<p>You'll need to <a href="<?php echo $url; ?>/new-app/?subscription_type=pro&site_url=<?php echo $domain; ?>" target="_blank">upgrade your subscription</a> to add this feature.</p>
		
	</div>

</div>

<div id="wx-upgrade-notice-premium" class="wx-jquery-dialog wx-hide">

	<div>
	
	<h2>This is a Premium or Reseller feature</h2>
    <?php if ( ! apply_filters( 'weever_list_show_wordpress_content', true ) and function_exists('wserver_sms_account_info') ): ?>
        <?php $domain = get_blog_details( get_current_blog_id() )->domain; ?>
        <?php switch_to_blog( 1 ); ?>
        <?php $url = home_url(); ?>
        <?php restore_current_blog(); ?>
    <?php else: ?>
        <?php $domain = $weeverapp->primary_domain; ?>
    <?php endif; ?>
	<p>You'll need to <a href="<?php echo $url; ?>/new-app/?subscription_type=premium&site_url=<?php echo $domain; ?>" target="_blank">upgrade your subscription</a> to add this feature.</p>
		
	</div>

</div>


<div id="wx-add-suggestion-dialog" class="wx-jquery-dialog wx-hide">

	<div>
	
	<h2>Something you don't see here?</h2>
	
	<p>Let us know on the support forums, we'd love to hear from you!<p>
	
	<p><a href="http://support.weeverapps.com/forums/20413397-feature-requests" target="_blank">Click to visit the Feature Request Forum</a>.</p>
		
	</div>

</div>