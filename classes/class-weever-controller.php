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

	public static function ajaxSortPosts() {
		global $wpdb;
		
		if ( ! empty($_POST) and is_array($_POST['order']) and count($_POST['order']) and check_ajax_referer( 'weever-list-js', 'nonce' ) and is_user_logged_in() and current_user_can('manage_options') ) {
			// Loop through the posts, save the order in the post meta
			foreach ($_POST['order'] as $key => $post_id) {
				update_post_meta($post_id, 'weever-post-order', $key);
			}
		}
		die();
	}

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

                    $scale = $size_arr[0] / 500;
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

    public static function ajaxSaveTabName() {
		weever_remove_wp_magic_quotes();
		
		if ( ! empty($_POST) and check_ajax_referer( 'weever-list-js', 'nonce' ) ) {
            $weeverapp = new WeeverApp();

            if ( $weeverapp->loaded ) {
                $tab = $weeverapp->get_tab_by_id( $_POST['id'] );

                if ( $tab !== false ) {
                    try {
                        $tab->name = $_POST['name'];
                        $tab->save();
                    } catch ( Exception $e ) {
                        status_header(500);
                        echo $e->getMessage();
                    }
                } else {
                    status_header(500);
                    echo __( 'Invalid tab id' );
                }
            }
        } else {
            status_header(401);
        }

        die();
    }

    
	public static function ajaxUpdateTabSettings() {
		weever_remove_wp_magic_quotes();
		
		if ( ! empty($_POST) and check_ajax_referer( 'weever-list-js', 'nonce' ) ) {
            $weeverapp = new WeeverApp();

            if ( $weeverapp->loaded ) {
                $tab = $weeverapp->get_tab( $_POST['id'] );
				$type = $_POST['type'];
				
				switch ( $_POST['type'] ) {
					case "map":
						$submitted_vars = explode( ',', $_POST['var'] );
						
						$tab->var->start->latitude = $submitted_vars[0];
						$tab->var->start->longitude = $submitted_vars[1];
						$tab->var->start->zoom = $submitted_vars[2];
						$tab->var->marker = $submitted_vars[3];
						
						// TODO: Figure out how to detect changes to the object itself if possible
						$tab->var = $tab->var;
						
						$tab->save();
						break;
						
					case "panel": 
					case "aboutapp":
						$submitted_vars = explode( ',' , $_POST['var'] );
						
						$tab->var->animation->type = $submitted_vars[0];
						$tab->var->animation->duration = $submitted_vars[1];
						$tab->var->animation->timeout = $submitted_vars[2];
						$tab->var->content_header = $submitted_vars[3];
						
						// TODO: Figure out how to detect changes to the object itself if possible
						$tab->var = $tab->var;
						
						$tab->save();
						break;
				}
            }
		} else {
            status_header(401);
        }

        die();
	}
    
    
	public static function ajaxSubtabDelete() {
		weever_remove_wp_magic_quotes();
		
		if ( ! empty($_POST) and check_ajax_referer( 'weever-list-js', 'nonce' ) ) {
            $weeverapp = new WeeverApp();

            if ( $weeverapp->loaded ) {
                $tab = $weeverapp->get_tab_by_id( intval( $_POST['id'] ), true );
                if ( $tab !== false ) {
                    try {
                        // Delete this tab
                        $tab->delete();
                    } catch ( Exception $e ) {
                        status_header(500);
                        echo $e->getMessage();
                    }
                } else {
                    status_header(500);
                    echo __( 'Invalid tab id' );
                }
            } else {
                status_header(500);
                echo __( 'Unable to communicate with Weever Apps server' );
            }
		}

		die();
	}

	public function ajaxPublishSelected() {
		weever_remove_wp_magic_quotes();
		
		if ( ! empty($_POST) and check_ajax_referer( 'weever-list-js', 'nonce' ) ) {
            $weeverapp = new WeeverApp();

            if ( $weeverapp->loaded ) {
                try {
                    $weeverapp->publish_tabs( explode( ",", $_POST['ids'] ) );
                } catch ( Exception $e ) {
                    status_header(500);
                    echo $e->getMessage();
                }
            } else {
                status_header(500);
                echo __( 'Unable to communicate with Weever Apps server' );
            }
        } else {
            status_header(401);
        }

        die();
    }

	public function ajaxUnpublishSelected() {
		weever_remove_wp_magic_quotes();
		
		if ( ! empty($_POST) and check_ajax_referer( 'weever-list-js', 'nonce' ) ) {
            $weeverapp = new WeeverApp();

            if ( $weeverapp->loaded ) {
                try {
                    $weeverapp->unpublish_tabs( explode( ",", $_POST['ids'] ) );
                } catch ( Exception $e ) {
                    status_header(500);
                    echo $e->getMessage();
                }
            } else {
                status_header(500);
                echo __( 'Unable to communicate with Weever Apps server' );
            }
        } else {
            status_header(401);
        }

        die();
    }

	public function ajaxDeleteSelected() {
		weever_remove_wp_magic_quotes();
		
		if ( ! empty($_POST) and check_ajax_referer( 'weever-list-js', 'nonce' ) ) {
            $weeverapp = new WeeverApp();

            if ( $weeverapp->loaded ) {
                try {
                    $weeverapp->delete_tabs( explode( ",", $_POST['ids'] ) );
                } catch ( Exception $e ) {
                    status_header(500);
                    echo $e->getMessage();
                }
            } else {
                status_header(500);
                echo __( 'Unable to communicate with Weever Apps server' );
            }
        } else {
            status_header(401);
        }

        die();
	}

	public function ajaxTabPublish() {
		weever_remove_wp_magic_quotes();
		
		if ( ! empty($_POST) and check_ajax_referer( 'weever-list-js', 'nonce' ) ) {
            $weeverapp = new WeeverApp();

            if ( $weeverapp->loaded ) {
                $tab = $weeverapp->get_tab( intval( $_POST['id'] ) );
                if ( $tab !== false ) {
                    try {
                        // Toggle the publish flag based on the status given
                        $tab->published = $_POST['status'] ? 0 : 1;
                        $tab->save();
                    } catch ( Exception $e ) {
                        status_header(500);
                        echo $e->getMessage();
                    }
                } else {
                    status_header(500);
                    echo __( 'Invalid tab id' );
                }
            } else {
                status_header(500);
                echo __( 'Unable to communicate with Weever Apps server' );
            }
		} else {
            status_header(401);
        }

        die();
    }

    /**
     * Get the icon source URL or base64
     */
    public function ajaxGetIconSrc() {
		if ( ! empty($_POST) and check_ajax_referer( 'weever-list-js', 'nonce' ) ) {
            $weeverapp = new WeeverApp(false);
            // Get the icon from cache if available, otherwise reload from server
            $icon_image_data = get_transient( 'weever_icon_image_' . intval( $_POST['icon_id'] ) );
            if ( false === $icon_image_data ) {
                $icon_image_data = file_get_contents( WeeverConst::LIVE_SERVER . '/api/' . WeeverConst::API_VERSION . '/icons/get_icon_base64?site_key=' . $weeverapp->site_key . '&icon_id=' . $_POST['icon_id'] );
                // Cache for a day
                set_transient( 'weever_icon_image_' . $_POST['icon_id'], $icon_image_data, ( 60 * 60 * 24 ) );
            }
            echo json_encode( array( 'icon_src' => 'data:image/png;base64,' . $icon_image_data ) );
        } else {
            status_header(500);
        }

        die();
    }


    public function ajaxSaveTabLayout() {
        weever_remove_wp_magic_quotes();

        if ( ! empty($_POST) and check_ajax_referer( 'weever-list-js', 'nonce' ) ) {
            $weeverapp = new WeeverApp();

            if ( $weeverapp->loaded ) {
                $tab = $weeverapp->get_tab_by_id( $_POST['tab_id'] );

                if ( $tab !== false ) {
                    try {
                        $tab->tabLayout = $_POST['layout'];
                        $tab->save();
                    } catch ( Exception $e ) {
                        status_header(500);
                        echo $e->getMessage();
                    }
                } else {
                    echo __( 'Invalid tab id' );
                    status_header(500);
                }
            } else {
                status_header(500);
                echo __( 'Unable to communicate with Weever Apps server' );
            }
        } else {
            status_header(401);
        }

        die();
    }


    public function ajaxSaveTabIcon() {
		weever_remove_wp_magic_quotes();
		
		if ( ! empty($_POST) and check_ajax_referer( 'weever-list-js', 'nonce' ) ) {
            $weeverapp = new WeeverApp();

            if ( $weeverapp->loaded ) {
                $tab = $weeverapp->get_tab_by_id( $_POST['tab_id'] );

                if ( $tab === false )
                    $tab = $weeverapp->get_subtab_by_id( $_POST['tab_id'] );

                if ( $tab !== false ) {
                    try {
                        $tab->icon_id = $_POST['icon_id'];
                        $tab->save();
                    } catch ( Exception $e ) {
                        status_header(500);
                        echo $e->getMessage();
                    }
                } else {
                    echo __( 'Invalid tab id' );
                    status_header(500);
                }
            } else {
                status_header(500);
                echo __( 'Unable to communicate with Weever Apps server' );
            }
        } else {
            status_header(401);
        }

        die();
    }

    public function ajaxMoveTab() {
        weever_remove_wp_magic_quotes();

        if ( ! empty($_POST) and check_ajax_referer( 'weever-list-js', 'nonce' ) ) {
            $weeverapp = new WeeverApp();

            if ( $weeverapp->loaded ) {
                try {
                    $tab = $weeverapp->get_tab_by_id( intval( $_POST['tab_id'] ), true );
                    
                    if ( false !== $tab ) {
                        $tab->move_tab( intval( $_POST['parent_id'] ) );
                    } else {
                        status_header(500);
                    }
                } catch ( Exception $e ) {
                    status_header(500);
                    echo __( 'Unable to move, please try again' );
                }
            }
        } else {
            status_header(401);
        }
        
        die();
    }
    
	public function ajaxSaveTabOrder() {
		weever_remove_wp_magic_quotes();
		
		if ( ! empty($_POST) and check_ajax_referer( 'weever-list-js', 'nonce' ) ) {
            $weeverapp = new WeeverApp();

            if ( $weeverapp->loaded ) {
                $order = array();
                $raw_order = explode( ",", $_POST['order'] );
                foreach ( $raw_order as $k => $o ) {
                    // Check that it's not the 'add tab' and other stuff
                    $tab_id = str_ireplace( 'tabid', '', $o );
                    
                    if ( is_numeric( $tab_id ) )
                        $order[] = str_ireplace( 'tabid', '', $o );
                }
                $weeverapp->order_tabs( $order );
            } else {
                status_header(500);
                echo __( 'Unable to communicate with Weever Apps server' );
            }
        } else {
            status_header(401);
        }

        die();
	}


	public function ajaxSaveSubtabOrder() {
        weever_remove_wp_magic_quotes();

        if ( ! empty($_POST) and check_ajax_referer( 'weever-list-js', 'nonce' ) ) {
            $weeverapp = new WeeverApp();

            if ( $weeverapp->loaded ) {
                $order = array();
                $raw_order = explode( ",", $_POST['order'] );
                foreach ( $raw_order as $k => $o ) {
                    // Check that it's not the 'add tab' and other stuff
                    $tab_id = str_ireplace( 'subtabid', '', $o );

                    if ( is_numeric( $tab_id ) )
                        $order[] = $tab_id;
                }
                $weeverapp->order_tabs( $order );
            } else {
                status_header(500);
                echo __( 'Unable to communicate with Weever Apps server' );
            }
        } else {
            status_header(401);
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

    public function ajaxValidate() {
        weever_remove_wp_magic_quotes();

        if ( ! empty($_POST) and check_ajax_referer( 'weever-list-js', 'nonce' ) ) {
            $weeverapp = new WeeverApp();
            
            if ( $weeverapp->loaded ) {
                $weeverapp->verify_tab( $_POST );
            }
        }
        
        die();
    }
    
	public function ajaxSaveNewTab() {
		weever_remove_wp_magic_quotes();

		if ( ! empty($_POST) and check_ajax_referer( 'weever-list-js', 'nonce' ) ) {
            
            if ( isset( $_POST['confirm_feed'] ) and $_POST['confirm_feed'] ) {
                self::ajaxValidate();
            } else {
                $weeverapp = new WeeverApp();
    
                if ( $weeverapp->loaded ) {
                    if ( isset( $_POST['parent_id'] ) and is_numeric( $_POST['parent_id'] ) ) {
                        $tab = $weeverapp->get_tab_by_id( $_POST['parent_id'] );
    
                        if ( $tab !== false ) {
                            try {
                                // Create a new subtab with the given params
                                // BJH - Use the app create subtab
                                // @TODO: Review function signature, create additional functions if needed
                                $weeverapp->create_tab( $_POST );
//                                if ( isset( $_POST['edit_id'] ) and is_numeric( $_POST['edit_id'] ) )
//                                    $tab->edit_subtab( $_POST );
//                                else
//                                    $tab->create_subtab( $_POST );
                            } catch ( Exception $e ) {
                                status_header(500);
                                echo $e->getMessage();
                            }
                        } else {
                            status_header(500);
                            echo __( 'Invalid tab id' );
                        }
                    } else {
                        // New tab
                        $weeverapp->create_tab( $_POST );
                    }
                }
            }
        } else {
            status_header(401);
            echo __( 'Authentication error' );
        }
        
        die();
	}
}
