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

    /**
     * Crops the last image uploaded
     */
    public static function ajaxCropImage() {
        // Use the session variable so they can't just crop any arbitrary image!
        if ( is_user_logged_in() and current_user_can('manage_options') and isset( $_SESSION['last_upload'] ) ) {
            $targ_w = intval($_POST['image_width']);
            $targ_h = intval($_POST['image_height']);
            //$jpeg_quality = 90;
            $png_compression = 6;

            $src = $_SESSION['last_upload']['filename'];
            //$image_string = file_get_contents($src);
            $size_arr = getimagesize($src);

            if ( IMAGETYPE_GIF == $size_arr[2] )
                $img_r = imagecreatefromgif($src);
            elseif ( IMAGETYPE_JPEG == $size_arr[2] )
                $img_r = imagecreatefromjpeg($src);
            elseif ( IMAGETYPE_PNG == $size_arr[2] )
                $img_r = imagecreatefrompng($src);
            else
                $img_r = false;

            //$img_r = imagecreatefromstring($image_string);

            if ( false === $img_r )
                status_header(500);
            else {
                if ( isset( $_POST['selection'] ) and isset( $_POST['selection']['x1'] ) and is_numeric( $_POST['selection']['x1'] ) and is_numeric( $_POST['selection']['y1'] ) and is_numeric( $_POST['selection']['height'] ) and is_numeric( $_POST['selection']['width'] ) ) {
                    // Crop the image first

                    $scale = $size_arr[0] / 652;
                    if ($scale < 1)
                        $scale = 1.0;
                    $image_width = intval( $scale * intval( $_POST['selection']['width'] ) );
                    $image_height = intval( $scale * intval( $_POST['selection']['height'] ) );
                    $x1 = intval( $scale * intval( $_POST['selection']['x1'] ) );
                    $y1 = intval( $scale * intval( $_POST['selection']['y1'] ) );

                    $dst_r = ImageCreateTrueColor( $image_width, $image_height );

                    imagecopyresampled($dst_r,$img_r,0,0,$x1,$y1,
                        $image_width,$image_height,$image_width,$image_height);
                } else {
                    $dst_r = $img_r;
                    $image_width = $size_arr[0];
                    $image_height = $size_arr[1];
                }

                $dest_basename = 'crop-' . hash('md5', basename($src)) . time() . '.png';
                $dest_image = str_replace(basename($src), $dest_basename, $src);

                // Resize the image down so it's not larger than the maximum size (targ_w/targ_h)
                $ratio = $image_width / $image_height;

                if ( $targ_w / $targ_h > $ratio ) {
                    $targ_w = floor( $targ_h * $ratio );
                } else {
                    $targ_h = floor( $targ_w / $ratio );
                }

                // Resample
                $final_img = imagecreatetruecolor( $targ_w, $targ_h );
                imagecopyresampled($final_img, $dst_r, 0, 0, 0, 0, $targ_w, $targ_h, $image_width, $image_height);
                imagepng($final_img, $dest_image, $png_compression);

                // Send over the new image name
                echo str_replace(basename($src), '', $_SESSION['last_upload']['url']) . $dest_basename;
            }
        } else {
            status_header(500);
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
