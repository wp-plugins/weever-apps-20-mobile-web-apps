<?php
/*
*	Weever Apps Administrator Component for Joomla
*	(c) 2010-2011 Weever Apps Inc. <http://www.weeverapps.com/>
*
*	Author: 	Robert Gerald Porter (rob.porter@weever.ca)
*				Modified by Brian Hogg (brian@bhconsulting.ca)
*	Version: 	0.9.2
*   License: 	GPL v3.0
*
*   This extension is free software: you can redistribute it and/or modify
*   it under the terms of the GNU General Public License as published by
*   the Free Software Foundation, either version 3 of the License, or
*   (at your option) any later version.
*
*   This extension is distributed in the hope that it will be useful,
*   but WITHOUT ANY WARRANTY; without even the implied warranty of
*   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*   GNU General Public License for more details <http://www.gnu.org/licenses/>.
*
*/
?>
<script type="text/javascript">
    wx.navIconDir 			= "<?php echo WEEVER_PLUGIN_URL; ?>static/images/icons/nav/";
    wx.baseExtensionUrl 	= "<?php echo admin_url( 'admin.php?page=weever-list' ); ?>";
</script>

<?php

$child_html = "";
$tabsUnpublished = 0;
$tab_output = ''; ?>

<div id="listTabs">
	<?php
		
        $tab_output = '';
		$tab_found = false;
		$tabs_count = 0;
		
		foreach ( $weeverapp->get_tabs() as $row ) {
		
			$is_tab_active = false;
            $component_rows_count = 0;
		
			// Tab is active if at least one row in the tab is published
			foreach ( $row->get_subtabs() as $subrow ) {
				if ( $subrow->published ) {
                    $is_tab_active = true;
					$tabs_count++;
					break;
				}
				$component_rows_count++;
			}
			
			if ( $is_tab_active || $component_rows_count )
				$tab_found = true;
		
			if ( ! $is_tab_active && $component_rows_count )
				$tab_output .= '<li id="' . $row->id . 'TabID" class="wx-nav-tabs" rel="unpublished"
								style="float:right;" style="float:center;"><a
								href="#' . $row->id . 'Tab" class="wx-tab-sortable"><div
								class="'.$row->icon.' wx-grayed-out wx-nav-icon" rel="'.$weeverapp->site_key.'"
								style="height:32px;width:auto;min-width:32px;text-align:center" title="'.$row->name.'"><img
								class="wx-nav-icon-img" src="' . $row->icon_image. '" /></div><div class="wx-nav-label wx-grayed-out"
								title="ID #'.$row->id.'">'.$row->name.'</div></a></li>';
				
			elseif ( $is_tab_active )
				$tab_output .= '<li id="' . $row->id . 'TabID" class="wx-nav-tabs" ><a href="#'. $row->id . 'Tab"
								class="wx-tab-sortable"><div class="'.$row->icon.' wx-nav-icon"
								style="height:32px;width:auto;min-width:32px;text-align:center"
								rel="'.$weeverapp->site_key.'" tab_id="' . $row->id . '" icon_id="' . $row->icon_id . '" title="'.$row->name.'"><img class="wx-nav-icon-img"
								src="' . $row->icon_image. '" /></div><div class="wx-nav-label"
								title="ID #'.$row->id.'">'.$row->name.'</div></a></li>';
		}
		
		?>

		<?php //if ( $tab_found ): ?>
		<ul id="listTabsSortable" class="list-items-sortable list-main-tabs">
            <li id="addFeatureID" class="wx-nav-tabs wx-nosort"><a href="#addTab" class="wx-tab-sortable" style="padding-top: 14px;" title="Drag New Features to this Location"><div class="wx-nav-icon" style="height:32px;width:auto;min-width:32px;text-align:center" title="Add"><span id="addtabspan" style="display: block; margin: 0 auto; font-weight: bold; color: #666; position: relative; line-height: 1.5; white-space: normal;">Drag New Features Here</span><span style="display:none; margin: 0 auto; text-align: left; font-weight: bold; color: #666; position: relative; line-height: 1.5; white-space: normal;" id="edittabspan">&lsaquo; back to "Build"</span></div></a></li>

			<?php echo $tab_output; ?>
			
            <li style="width:80px; height:60px; border-style: dashed; margin-left: 5px; border-width: 2px 2px 0; border-color: #666; color: #666; padding: 5px 0 0 5px; box-sizing: border-box; -webkit-box-sizing: border-box; display: none;" id="dropTab">Drop Here</li>
				<?php if ( $tabs_count > 1 ) { ?>
				<span id="wx-note-tabs-id" class="wx-note-tabs-class wx-nosort">&larr; <?php _e( 'Drag and drop tabs to re-order!' );?></span>
				<?php } ?>
		</ul>
		<?php //endif; ?>
    
        <!-- TABS -->
        <div id="addTab">
    
            <!-- START: INTERFACE FOR THE WEEVER APP BUILDER UI V2 -->
            <div id="wx-add-OneView">
    
                <!-- START: Add to App Button Sets -->
                <div id="wx-add-content-to-app-buttons">
    
                    <?php do_action('weever_list_message_area', $tabs_count ); ?>
    
                    <!-- 0.  Empty State Message for Newbies -->
    
                    <div class="wx_box wxblk" id="wxnavtip-empty">
                        <h2 style="display: block; margin: 0px;"><?php _e( '<strong>Get started</strong> by selecting app features below!' );?></h2>
                    </div>
    
    
                    <ul id="wxui-addbuttons-content" class="wxui-btnwrapper list-items-sortable list-add-content-items">
    
                        <?php if ( apply_filters( 'weever_list_show_wordpress_content', true ) ): ?>
                            <li class="wxui-btn white large radius3 wx-floatleft wx-add-source-icon wx-add-single" ref="add-wordpress-blog" id="add-wordpress-blog">
                                <img src="http://weeverapps.com/wp-content/plugins/weever-apps-for-wordpress/static/images/icons/nav/wordpress-blog.png">
                                <span><?php _e( 'Posts' );?></span>
                            </li>
                            <li class="wxui-btn white large radius3 wx-floatleft wx-add-source-icon wx-add-single" ref="add-wordpress-category" id="add-wordpress-category">
                                <img src="http://weeverapps.com/wp-content/plugins/weever-apps-for-wordpress/static/images/icons/nav/wordpress-category.png">
                                <span><?php _e( 'Category' );?></span>
                            </li>
                            <li class="wxui-btn white large radius3 wx-floatleft wx-add-source-icon wx-add-single" ref="add-wordpress-page" id="add-wordpress-page">
                                <img src="http://weeverapps.com/wp-content/plugins/weever-apps-for-wordpress/static/images/icons/nav/wordpress-page.png">
                                <span><?php _e( 'Page' );?></span>
                            </li>
                            <li class="wxui-btn white large radius3 wx-floatleft wx-add-source-icon wx-add-single" ref="add-wordpress-tag" id="add-wordpress-tag">
                                <img src="http://weeverapps.com/wp-content/plugins/weever-apps-for-wordpress/static/images/icons/nav/wordpress-tag.png">
                                <span><?php _e( 'Tag' );?></span>
                            </li>
                            <li class="wxui-btn white large radius3 wx-floatleft wx-add-source-icon wx-add-single" ref="add-wordpress-searchterm" id="add-wordpress-searchterm">
                                <img src="http://weeverapps.com/wp-content/plugins/weever-apps-for-wordpress/static/images/icons/nav/wordpress-searchterm.png">
                                <span><?php _e( 'Search Term' );?></span>
                            </li>
                            <li class="wxui-btn white large radius3 wx-floatleft wx-add-source-icon wx-add-single" ref="add-wordpress-map" id="add-wordpress-map">
                                <img src="http://weeverapps.com/wp-content/plugins/weever-apps-for-wordpress/static/images/icons/nav/addlocation.png">
                                <span><?php _e( 'Map Feed' );?></span>
                            </li>
                            <li class="wxui-btn white large radius3 wx-floatleft wx-add-source-icon wx-add-single" ref="add-wordpress-proximity" id="add-wordpress-proximity">
                                <img src="http://weeverapps.com/wp-content/plugins/weever-apps-for-wordpress/static/images/icons/nav/addlocation.png">
                                <span><?php _e( 'Nearest To Me' );?></span>
                            </li>
                            <li class="wxui-btn white large radius3 wx-floatleft wx-add-source-icon wx-add-single" ref="add-wordpress-directory" id="add-wordpress-directory">
                                <img src="http://weeverapps.com/wp-content/plugins/weever-apps-for-wordpress/static/images/icons/nav/wordpress-category.png">
                                <span><?php _e( 'Directory' );?></span>
                            </li>
                        <?php endif; ?>

                        <?php if ( ! apply_filters( 'weever_list_show_wordpress_content', true ) ): ?>
                            <li rel="page" class="wxui-btn white large radius3 wx-floatleft wx-add-new-content-button wx-add-new-accordionbutton wx-add-source-icon">
                                <img src="http://weeverapps.com/wp-content/plugins/weever-apps-for-wordpress/static/images/icons/nav/page.png"><span><?php _e( 'Add Page' ); ?></span>
                            </li>
                            <li rel="page" class="wxui-btn white large radius3 wx-floatleft wx-add-new-content-button wx-add-new-coupon wx-add-new-accordionbutton wx-add-source-icon">
                                <img src="http://weeverapps.com/wp-content/plugins/weever-apps-for-wordpress/static/images/icons/nav/coupon.png"><span><?php _e( 'Add Coupon' ); ?></span>
                            </li>
                        <?php endif; ?>

                        <li class="wxui-btn white large radius3 wx-floatleft wx-add-source-icon" ref="add-twitter" icon_id="21" id="add-twitter">
                            <img src="http://weeverapps.com/wp-content/plugins/weever-apps-for-wordpress/static/images/icons/nav/twitter.png">
                            <span><?php _e( 'Add Twitter' );?></span>
                        </li>                        
                        <li class="wxui-btn white large radius3 wx-floatleft wx-add-source-icon wx-add-single" icon_id="16" ref="add-facebook-social" id="add-facebook-social">
                            <img src="http://weeverapps.com/wp-content/plugins/weever-apps-for-wordpress/static/images/icons/nav/facebook.png">
                            <span><?php _e( 'Facebook Wall' );?></span>
                        </li>    
                        <li class="wxui-btn white large radius3 wx-floatleft wx-add-source-icon" ref="add-youtube" icon_id="18" id="add-youtube">
                            <img src="http://weeverapps.com/wp-content/plugins/weever-apps-for-wordpress/static/images/icons/nav/youtube.png">
                            <span><?php _e( 'YouTube' );?></span>
                        </li> 	                         <li class="wxui-btn white large radius3 wx-floatleft wx-add-source-icon wx-add-single" ref="add-vimeo" id="add-vimeo">
                            <img src="http://weeverapps.com/wp-content/plugins/weever-apps-for-wordpress/static/images/icons/nav/vimeo.png">
                            <span><?php _e( 'Vimeo' ); ?></span>
                        </li>
                        <?php if ( ! apply_filters( 'weever_list_show_wordpress_content', true ) ): ?>
                            <li rel="map" class="wxui-btn white large radius3 wx-floatleft wx-add-new-content-button wx-add-new-accordionbutton wx-add-source-icon">
                                <img src="http://weeverapps.com/wp-content/plugins/weever-apps-for-wordpress/static/images/icons/nav/addlocation.png"><span><?php _e( 'Add Map Location' ); ?></span>
                            </li>
                        <?php endif; ?>
                        <li class="wxui-btn white large radius3 wx-floatleft wx-add-source-icon" ref="add-flickr" icon_id="19" id="add-flickr">
                            <img src="http://weeverapps.com/wp-content/plugins/weever-apps-for-wordpress/static/images/icons/nav/flickr.png">
                            <span><?php _e( 'Flickr' ); ?></span>
                        </li>
                        <li class="wxui-btn white large radius3 wx-floatleft wx-add-source-icon wx-add-single" icon_id="17" ref="add-picasa" id="add-picasa">
                            <img src="http://weeverapps.com/wp-content/plugins/weever-apps-for-wordpress/static/images/icons/nav/picasa.png">
                            <span><?php _e( 'Picasa' ); ?></span>
                        </li>
                        <li class="wxui-btn white large radius3 wx-floatleft wx-add-source-icon wx-add-single" icon_id="25" ref="add-foursquare" id="add-foursquare">
                            <img src="http://weeverapps.com/wp-content/plugins/weever-apps-for-wordpress/static/images/icons/nav/foursquare.png">
                            <span><?php _e( 'Foursquare' );?></span>
                        </li>
                        <li class="wxui-btn white large radius3 wx-floatleft wx-add-source-icon wx-add-single" icon_id="16" ref="add-facebook-photos" id="add-facebook">
                            <img src="http://weeverapps.com/wp-content/plugins/weever-apps-for-wordpress/static/images/icons/nav/facebook.png">
                            <span><?php _e( 'Facebook Photos' );?></span>
                        </li>
                        <li class="wxui-btn white large radius3 wx-floatleft wx-add-source-icon wx-add-single" icon_id="7" ref="add-google_calendar" id="add-google_calendar">
                            <img src="http://weeverapps.com/wp-content/plugins/weever-apps-for-wordpress/static/images/icons/nav/google_calendar.png"><span>Google Calendar</span>
                        </li>
                        <li class="wxui-btn white large radius3 wx-floatleft wx-add-source-icon wx-add-single" icon_id="16" ref="add-facebook-events" id="add-facebook">
                            <img src="http://weeverapps.com/wp-content/plugins/weever-apps-for-wordpress/static/images/icons/nav/facebook.png">
                            <span><?php _e( 'Facebook Events' );?></span>
                        </li>

                        <li class="wxui-btn white large radius3 wx-floatleft wx-add-source-icon wx-add-single" ref="add-wordpress_contact" id="add-wordpress_contact">
                            <img src="http://weeverapps.com/wp-content/plugins/weever-apps-for-wordpress/static/images/icons/nav/wordpress_contact.png">
                            <span><?php _e( 'Click-to-Call and Email' );?></span>
                        </li>
                        <li class="wxui-btn white large radius3 wx-floatleft wx-add-source-icon wx-add-single <?php if ( $weeverapp->tier == 1 ) echo 'wx-upgrade-prompt'; ?>" ref="add-wufoo" id="add-wufoo">
                            <img src="http://weeverapps.com/wp-content/plugins/weever-apps-for-wordpress/static/images/icons/nav/form_icon.png">
                            <span><?php _e( 'Wufoo Forms' );?></span>
                        </li>
                        <?php if ( apply_filters( 'weever_list_r3s_dialog', true ) ): ?>
                            <li class="wxui-btn white large radius3 wx-floatleft wx-add-source-icon wx-add-single" ref="add-r3s" id="add-r3s">
                                <img src="http://weeverapps.com/wp-content/plugins/weever-apps-for-wordpress/static/images/icons/nav/r3s.png">
                                <span><?php _e( 'R3S Code' );?></span>
                            </li>
                        <?php endif; ?>
                        
                        <li class="wxui-btn white large radius3 wx-floatleft wx-add-source-icon wx-add-single" icon_id="6" ref="add-blogger" id="add-blogger">
                            <img src="http://weeverapps.com/wp-content/plugins/weever-apps-for-wordpress/static/images/icons/nav/blogger.png">
                            <span><?php _e( 'Blogger' );?></span>
                        </li>
                        <li class="wxui-btn white large radius3 wx-floatleft wx-add-source-icon wx-unavailable wx-add-single" ref="add-google_plus" id="add-google_plus">
                            <img src="http://weeverapps.com/wp-content/plugins/weever-apps-for-wordpress/static/images/icons/nav/google_plus.png">
                            <span><?php _e( 'Google +' );?></span>
                        </li>   
                        <li class="wxui-btn white large radius3 wx-floatleft wx-add-source-icon wx-unavailable wx-add-single" ref="add-tumblr" id="add-tumblr">
                            <img src="http://weeverapps.com/wp-content/plugins/weever-apps-for-wordpress/static/images/icons/nav/tumblr.png">
                            <span><?php _e( 'Tumblr' );?></span>
                        </li>                        <li class="wxui-btn white large radius3 wx-floatleft wx-add-source-icon wx-unavailable" ref="add-soundcloud" id="add-soundcloud">
                            <img src="http://weeverapps.com/wp-content/plugins/weever-apps-for-wordpress/static/images/icons/nav/soundcloud.png">
                            <span><?php _e( 'SoundCloud' );?></span>
                        </li>
                        <li class="wxui-btn white large radius3 wx-floatleft wx-add-source-icon wx-unavailable" ref="add-bandcamp" id="add-bandcamp">
                            <img src="http://weeverapps.com/wp-content/plugins/weever-apps-for-wordpress/static/images/icons/nav/bandcamp.png">
                            <span><?php _e( 'BandCamp' );?></span>
                        </li>

                    </ul>
                    <!-- END: Add to App Buttons -->
    
                </div>
            </div>
            <!-- wx-add-OneView -->
        </div>
        <!-- END: INTERFACE FOR THE WEEVER APP BUILDER UI V2 -->
    
        <div id="wx-overlay-drag">
            <div id="wx-overlay-unpublished">
                <?php echo __( 'This tab has no published items' ); ?>
            </div>
            <img id="wx-overlay-drag-img" src="<?php echo WEEVER_PLUGIN_URL; ?>static/images/icons/drag.png" />
        </div>
    
        <div id="wx-modal-loading">
            <div id="wx-modal-msgcontainer">
            <img src="<?php echo WEEVER_PLUGIN_URL; ?>static/images/loading.gif" style="float: right; margin-left: 16px;" />
                <div id="wx-modal-loading-text"></div>
                <div id="wx-modal-secondary-text"></div>
                <div id="wx-modal-error-text"></div>
            </div>
        </div>
    
        <input type="hidden" id="nonce" name="nonce" value="<?php echo wp_create_nonce( 'weever-list-js' ); ?>" />
    
        <?php
        foreach ( $weeverapp->get_tabs() as $row ) {
    
            //$componentRowsName = $row->component . 'Rows';
            $componentRows = $row->get_subtabs();
    
            if ( count( $componentRows ) ) {
                $published = ''; //JHTML::_('grid.published', $row, $iii);
                $checked = ''; //JHTML::_('grid.id', $iii, $row->id);
    
                if ( $row->published == 0 )
                    $tabsUnpublished++;
    
            } else {
                $published = __('WEEVER_NOT_APPLICABLE', 'weever');
                $checked = null;
                $tabsUnpublished++;
            }
    
            ?>
    
            <?php if ( count( $componentRows ) ): ?>
            <div id="<?php echo $row->id . 'Tab'; ?>" class="wx-tabs-stdcontainer wxui-stdContainer">
                
	            <fieldset class="wx-adminContainer">
		                <h2 class="wxuia-fieldTitle" style="border: 1px solid #e5e5e5;" id="wxnavtip-options"><?php _e( 'On this tab...' );?>
		                    <span class="wx-inputContainer">
		                        <a href="#" style="float: right;" class="wxuia-button wx-nav-icon-edit" rel="<?php echo $row->id; ?>">Change Icon</a>
		                    </span>
		                    <span class="wx-inputContainer">
	                        <a href="#" style="float: right;" class="wxuia-button wx-nav-label-edit" rel="<?php echo $row->id; ?>">Change Label</a>
	                    </span>
						<?php if ( 'map' == $row->tabLayout ): ?>
							<span class="wx-inputContainer">
								<a href="#" style="float: right;" class="wxuia-button wx-nav-map-options-edit" rel="<?php echo $row->id; ?>">Change Options</a>
							</span>
						<?php else: ?>
		                    <span class="wx-inputContainer">
	    	                    <a href="#" style="float: right;" class="wxuia-button wx-nav-layout-edit" layout="<?php echo $row->tabLayout; ?>" rel="<?php echo $row->id; ?>">Change Layout</a>
	        	            </span>
        				<?php endif; ?>
                	</h2>
    			</fieldset>
                <!-- Start: Add a Left and Right Margin of 20px -->
                <div class="wxui-stdContainer wxuia-edit-tabsContainer">
                    <input type="hidden" name="boxchecked<?php echo $row->component; ?>" id="boxchecked<?php echo $row->component; ?>" value="0" />
                    <div class='adminlist'>
                        <ul id="listItemsSortable<?php echo $row->id; ?>" class="list-items-sortable list-items list-sub-items">
                            <?php
                            $sub = 0;
                            $component = $row->component;
                            ?>
    
                            <?php foreach ( $componentRows as $sub_row ): ?>

                                <li id="<?php echo $sub_row->id; ?>SubtabID" class="wx-ui-row" rel="<?php echo $sub_row->id; ?>">
                                    <span class="wx-subtab-link-text" title="<?php echo $sub_row->name; ?>">
                                        <?php if ( 'blog' != $sub_row->component ): ?>
                                            <?php echo $sub_row->name; ?>
                                        <?php else: ?>
                                            <a href="#" id="edititem-<?php echo $sub_row->id; ?>" class="wx-add-source-icon wx-add-single wx-edit-link" rel="add-<?php echo $row->get_component_id(); ?>" ref="add-<?php echo $row->get_component_id(); ?>" edit_id="<?php echo $sub_row->id; ?>" tab_data="<?php echo htmlentities($sub_row); ?>"><?php echo esc_html($sub_row->name); ?></a>
                                        <?php endif; ?>
                                    </span>

                                    <span class="wx-subtab-type-text"><?php echo $sub_row->get_display_name(); ?></span>

                                    <span class="wx-subtab-movehandle">&#8597; Drag</span>


                                    <?php if ( strstr($sub_row->cms_feed, 'feed=r3s') and ! strstr($sub_row->cms_feed, '//') ): ?>
                                        <ul class="wx-edit-content-item-feed" type="<?php echo $sub_row->type; ?>" cmsfeed="<?php echo (strpos($weeverapp->primary_domain, 'http://') === false ? 'http://' . $weeverapp->primary_domain : $weeverapp->primary_domain) . '/' . $sub_row->cms_feed; ?>"></ul>
                                    <?php endif; ?>

                                    <span class="wx-edit-subtab-rowactions">
                                        <?php if ( ! in_array( $sub_row->component, array('htmlPage', 'blog', 'panel', 'page', 'aboutapp', 'map') ) ): ?>
                                            <span class="edit_item"><a id="edititem-<?php echo $sub_row->id; ?>" href="#" class="wx-add-source-icon wx-add-single wx-edit-link" rel="add-<?php echo $sub_row->get_component_id(); ?>" ref="add-<?php echo $sub_row->get_component_id(); ?>" edit_id="<?php echo $sub_row->id; ?>" tab_data="<?php echo htmlentities($sub_row); ?>">Edit</a></span>
                                            <span class="edit_icon"><a id="subtabicon-<?php echo $sub_row->id; ?>" class="wx-edit-subtab-icon" href="#" tab_id="<?php echo $sub_row->id; ?>" icon_id="<?php echo $sub_row->icon_id; ?>">Change Icon</a></span>
                                        <?php elseif ( in_array( $sub_row->component, array('htmlPage') ) ): ?>
                                            <?php $id_pos = ( FALSE !== strpos($sub_row->config->url, 'index.php?p=') ? ( strpos($sub_row->config->url, 'index.php?p=') + strlen('index.php?p=') ) : ( strpos($sub_row->config->url, 'index.php?page_id=') + strlen('index.php?page_id=') ) ); ?>
                                            <?php $edit_page_id = intval( substr( $sub_row->config->url, $id_pos ) ); ?>
                                            <a id="editpage-<?php echo $edit_page_id; ?>" class="wx-edit-content-item" type="<?php echo $sub_row->type; ?>" cmsfeed="<?php echo $sub_row->config->url; ?>" rel="<?php echo $edit_page_id; ?>" href="<?php echo admin_url("post.php?post=$edit_page_id&action=edit"); ?>">Edit</a>
                                        <?php endif; ?>

                                        <span class="delete"><a id="deleteitem-<?php echo $sub_row->id; ?>" href="#" title="ID #<?php echo $sub_row->id; ?>" class="wx-subtab-delete" rel="<?php echo $sub_row->type; ?>" alt="<?php echo __( 'Delete', 'weever' ); ?> &quot;<?php echo htmlentities($sub_row->name); ?>&quot;"><?php echo __( 'Delete', 'weever' ); ?></a></span>
                                    </span>
                                </li>

                            <?php endforeach; ?>
    
                        </ul>
    
                        <?php if ( ! count( $componentRows ) ): ?><div><?php _e( 'There are no items in this tab.', 'weever' ); ?></div><?php endif; ?>
                    </div>
                    <!-- End: Add a Left and Right Margin of 20px -->
                </div>
            </div>
            <!-- End: Add Fieldset for Style -->
            </fieldset>
                <?php endif; ?>
    
            <?php
        }
        ?>
    
    
        <!-- END TABS -->
    
</div>		

<!-- START: iFrame Preview -->

<?php weever_phone_preview($tab_found); ?>

<!-- END: iFrame Preview -->

<input type="hidden" name="option" value="<?php //echo $option; ?>" />
<input type="hidden" name="site_key" id="wx-site-key" value="<?php echo $weeverapp->site_key; ?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="view" value="list" />
<input type="hidden" name="filter_order" value="<?php //echo $weeverapp->lists['order']; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php //echo $weeverapp->lists['order_Dir']; ?>" />

<?php
	// Include the dialogs for the new add content UI
	//include( apply_filters( 'weever_list_dialogs', dirname( __FILE__ ) . '/../parts/list_dialogs.php' ) );
	include( dirname( __FILE__ ) . '/../parts/list_dialogs.php' );
?>

<div class="clear"></div>
