<?php

/*
*	Weever Apps Administrator Component for Joomla
*	(c) 2010-2011 Weever Apps Inc. <http://www.weeverapps.com/>
*
*	Author: 	Robert Gerald Porter (rob.porter@weever.ca)
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

session_start();

class WeeverController {

    public static function ajaxHandleUpload() {
        if ( is_user_logged_in() and current_user_can('manage_options') ) {
            // list of valid extensions, ex. array("jpeg", "xml", "bmp")
            $allowedExtensions = array('jpeg', 'jpg', 'bmp', 'xpm', 'gif', 'png');

            $uploads = wp_upload_dir();

            $uploader = new qqFileUploader($allowedExtensions);
            $result = $uploader->handleUpload( $uploads['path'] . '/' );
            $_SESSION['last_upload'] = $result;
            unset($result['filename']);
            // to pass data through iframe you will need to encode all html tags
            echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
        }
        die();
    }

    public function ajaxSaveTheme() {
        weever_remove_wp_magic_quotes();

        try {
            if ( $_POST['main_titlebar_color'] )
                update_option( 'weever_main_titlebar_color', $_POST['main_titlebar_color'] );
            if ( $_POST['main_titlebar_text_color'] )
                update_option( 'weever_main_titlebar_text_color', $_POST['main_titlebar_text_color'] );
            if ( $_POST['subtab_color'] )
                update_option( 'weever_subtab_color', $_POST['subtab_color'] );
            if ( $_POST['subtab_text_color'] )
                update_option( 'weever_subtab_text_color', $_POST['subtab_text_color'] );

            status_header(204);

        } catch (Exception $e) {
            status_header(500);
            echo $e->getMessage(); 
        }

        die();
    }

    public function ajaxToggleAppStatus() {
        weever_remove_wp_magic_quotes();
        
        if ( ! empty($_POST) and check_ajax_referer( 'weever-list-js', 'nonce' ) ) {
            $weeverapp = new WeeverApp();

            if ( $weeverapp->loaded ) {
                try {
                    if ( isset( $_POST['app_enabled'] ) ) {
                        // Use the given value
                        $weeverapp->app_enabled = ( $_POST['app_enabled'] ? 1 : 0 );                        
                    } else {
                        // Toggle
                        $weeverapp->app_enabled = ( $weeverapp->app_enabled ? 0 : 1 );
                    }
                    $weeverapp->save();
                } catch ( Exception $e ) {
                    status_header(500);
                    echo $e->getMessage();
                }
            }
        } else {
            status_header(401);
            echo __( 'Authentication error' );
        }

        die();
    }

	public function ajaxToggleTabletStatus() {
		weever_remove_wp_magic_quotes();
		
		if ( ! empty($_POST) and check_ajax_referer( 'weever-list-js', 'nonce' ) ) {
            $weeverapp = new WeeverApp();

            if ( $weeverapp->loaded ) {
                try {
                    if ( isset( $_POST['tablets_enabled'] ) ) {
                        // Use the given value
                        $weeverapp->DetectTierWeeverTablets = ( $_POST['tablets_enabled'] ? 1 : 0 );                        
                    }
                    $weeverapp->save();
                } catch ( Exception $e ) {
                    status_header(500);
                    echo $e->getMessage();
                }
            }
        } else {
            status_header(401);
            echo __( 'Authentication error' );
        }

        die();
	}

	// public function ajaxSaveNewTab() {
	// 	weever_remove_wp_magic_quotes();
		
	// 	if ( ! empty($_POST) and check_ajax_referer( 'weever-list-js', 'nonce' ) ) {
 //            $weeverapp = new WeeverApp();

 //            if ( $weeverapp->loaded ) {
 //                $tab = $weeverapp->get_tab( $_POST['type'] );

 //                if ( $tab !== false ) {
 //                    try {
 //                    	// If it's a page tab, look for an image (if any) to add as an icon
 //                    	if ( 'page' == $_POST['component'] ) {
 //                    		$page_id = str_replace( 'index.php?page_id=', '', $_POST['cms_feed'] );
                    		
 //                    		if ( is_numeric( $page_id ) ) {
 //                    			$page = get_page( $page_id );
                    			
 //                    			if ( ! empty( $page ) ) {
	// 		                    	if ( has_post_thumbnail( $page_id ) ) {
	// 		                    		$image = wp_get_attachment_image_src( get_post_thumbnail_id( $page_id ) );
			                    	
	// 		                    		if ( is_array( $image ) and isset( $image[0] ) )
	// 		                    			$image = $image[0];
	// 		                    	}
			                    	
	// 		                    	if ( empty( $image ) ) {
	// 		                    		$html = WeeverSimpleHTMLDomHelper::str_get_html( $page->post_content );
			                    	
	// 		                    		foreach ( @$html->find('img') as $vv )
	// 		                    		{
	// 		                    			if ( $vv->src )
	// 		                    			{
	// 		                    				$image = WeeverHelper::make_absolute($vv->src, get_site_url());
	// 		                    				break;
	// 		                    			}
	// 		                    		}
	// 		                    	}
			                    	
	// 		                    	if ( empty( $image ) )
	// 		                    		$image = "";
	                    	
	// 								$_POST['var'] = $image;
 //                    			}
 //                    		}
 //                    	}
                    	
 //                        // Create a new subtab with the given params
 //                        $tab->create_subtab( $_POST );
 //                    } catch ( Exception $e ) {
 //                        status_header(500);
 //                        echo $e->getMessage();
 //                    }
 //                } else {
 //                    status_header(500);
 //                    echo __( 'Invalid tab id' );
 //                }
 //            }
 //        } else {
 //            status_header(401);
 //            echo __( 'Authentication error' );
 //        }

 //        die();
	// }
}
