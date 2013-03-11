<?php

class WeeverApp {

    const TIER_BASIC = 0;
    const TIER_PRO = 1;

    const CONTACT_ICON_ID = 34;

    private $_data = array();
    private $_changed = array();
    private $_device_options = array(
            // Granular options
            'DetectIphoneOrIpod' => 0,
            'DetectAndroid' => 0,
            'DetectBlackBerryTouch' => 0,
            'DetectWebOSTablet' => 0,
            'DetectIpad' => 0,
            'DetectBlackBerryTablet' => 0,
            'DetectAndroidTablet' => 0,
            'DetectGoogleTV' => 0,
            'DetectAppleTVTwo' => 0,
            'DetectTouchPad' => 0,
            // Options if granular is disabled
            'DetectTierWeeverSmartphones' => 1,
            'DetectTierWeeverTablets' => 0,
        );

        // Options if granular is disabled
        private $_device_options_standard = array( 'DetectTierWeeverSmartphones',
            								   'DetectTierWeeverTablets' );

    /*
     * Constructor
     *
     * @return WeeverApp
     */
    public function __construct( $load_from_server = true, $site_key = false ) {
        try {
            // Register the app with the helper class
            WeeverHelper::set_weeverapp($this);

            // Initial settings
            $this->_data['site_id'] = false;
            $this->_data['theme'] = new WeeverAppThemeStyles();
			$this->_data['launch'] = new WeeverAppThemeLaunch();
            $this->_data['app_enabled'] = get_option( 'weever_app_enabled', 0 );
            $this->_data['site_key'] = ( $site_key === false ? get_option( 'weever_api_key', '' ) : $site_key );
            $this->_data['staging_mode'] = get_option( 'weever_staging_mode', 0 );
            $this->_data['primary_domain'] = get_option( 'weever_primary_domain', '' );
            $this->_data['titlebar_title'] = '';
            $this->_data['title'] = '';
            $this->_data['google_analytics'] = '';
			$this->_data['piwik'] = '';
            $this->_data['ecosystem'] = '';
            $this->_data['domain'] = get_option( 'weever_domain', '' );
            $this->_data['tier'] = get_option( 'weever_tier', 0 ); // Payment tier 0-basic 1-pro
            $this->_data['loadspinner'] = '';
            $this->_data['expiry'] = false;
            $this->_data['local'] = '';
            $this->_data['locales'] = array();
            $this->_data['raw_tabsync_data'] = false;

            $this->_data['tabs'] = array();

            if ( class_exists('WeeverAppLogin') )
                $this->_data['login'] = new WeeverAppLogin();
            else
                $this->_data['login'] = false;

            // QR code urls
            $this->_data['qr_code_private'] = $this->_data['qr_code_public'] = '';
            $this->generate_qr_code();
            // Device detection settings
            $this->_data['granular'] = get_option( 'weever_granular_devices', 0 );
            foreach ( $this->_device_options as $key => $value ) {
                 $this->_data[$key] = get_option( 'weever_device_option_'.$key, $value );
            }

            if ( $load_from_server ) {
                $this->reload_from_server();
            }

            // Finished, mark as loaded
            $this->_data['loaded'] = true;
        } catch ( Exception $e ) {
            $this->_data['loaded'] = false;
            $this->_data['error_message'] = $e->getMessage();
        }
    }

    public function & __get($var) {
        switch ( $var ) {
            default:
                if ( array_key_exists( $var, $this->_data ) )
                    return $this->_data[$var];
                else
                    throw new Exception( "Invalid parameter name $var" );
        }
    }

    public function __set($var, $val) {

        switch ( $var ) {
        	case 'local':
        		// Only allow locales that are supported 
        		if ( isset( $this->_data['locales']->$val ) ) {
        			$this->_changed[$var] = $var;
        			$this->_data['local'] = $val;
        		}
        		break;
        		
            case 'staging_mode':
                if ( $val )
                    $this->_data['staging_mode'] = 1;
                else
                    $this->_data['staging_mode'] = 0;
                $this->_changed[$var] = $var;
                break;

            case 'site_key':
                // TODO: Add any pre-validation here
                $this->_data['site_key'] = $val;
                $this->_changed[$var] = $var;
                break;

            case 'primary_domain':
                throw new Exception( "Cannot set $var directly" );
                break;

            default:
                if ( array_key_exists( $var, $this->_data ) ) {
                    $this->_data[$var] = $val;
                    $this->_changed[$var] = $var;
                } else {
                    throw new Exception( "Invalid parameter name $var" );
                }
        }

    }
    
    public function __toString() {
    	switch ( $this->tier ) {
    		case 1:
    			return 'Basic';
    		case 2:
    		case 3:
    			return 'Pro';
    		case '2.1':
    			return 'Pro Trial';
    		case 4:
    			return 'Premium/Reseller';
    		default:
    			return 'Unknown Tier';
    	}
    }

    /**
     * Attempt to reload the data from the Weever server using the API
     * TODO: Only reload the required vars for the given tab or function
     */
    private function reload_from_server() {
        if ( ! empty( $this->_data['site_key'] ) ) {
            $start = microtime(true);
            
			// TABS
        	$result = WeeverHelper::send_to_weever_server( 'tabs/get_tabs', array( 'site_key' => $this->_data['site_key'] ) );
            
            if ( ! is_array( $result->tabs ) )
                throw new Exception( __( 'No tabs found') );

            foreach ( $result->tabs as $tab ) {
				if ( 'share' == $tab->layout )
					continue;
				
                if ( empty( $tab->parent_id ) or $tab->parent_id == $tab->id ) {
                    // Main level tab
                    $this->_data['tabs'][] = new weever_app_tab( array( 
                            'id' => $tab->id,
                            'component' => $tab->content,
                            'name' => ( ! empty( $tab->tabTitle ) ? $tab->tabTitle : $tab->title ),
                            'published' => $tab->published,
                            'ordering' => $tab->ordering,
                            'icon_id' => $tab->icon_id,
                            'var' => ( property_exists($tab, 'var')  ? ( ! is_null( json_decode($tab->var) ) ? json_decode($tab->var) : $tab->var ) : false ),
                            'layout' => $tab->layout,
                            'config' => $tab->config,
                         )
                    );
                    
                    // Also add a subtab (there's no longer separate rows for main tabs, just a subtab with parent_id NULL)
                    // unless it's something like the 'Share App' tab which can't be edited
                    if ( '' != $tab->content ) {
                        $this->_data['tabs'][] = new weever_app_subtab( array( 
                                'id' => $tab->id, 
                                'name' => ( ! empty($tab->title) ? $tab->title : $tab->tabTitle ), 
                                'component' => $tab->content,
                                'ordering' => -1000, // make this first tab in subtab list (only one ordering param for both tabs and subtabs) 
                                'published' => $tab->published,
                                'cms_feed' => WeeverHelper::get_cms_feed( $tab ),
                                'component_behaviour' => WeeverHelper::get_cms_Feed( $tab ), //TODO: Remove, just use config_cache 
                                'var' => ( property_exists($tab, 'var')  ? ( ! is_null( json_decode($tab->var) ) ? json_decode($tab->var) : $tab->var ) : false ), 
                                'type' => $tab->content,
                                'layout' => $tab->layout,
                                'icon_id' => $tab->icon_id,
                                'parent_id' => $tab->id, // NOTE: parent ID is the same as this tab's ID currently (confusing)
                                'config' => $tab->config,
                            )
                        );
                    }
                } else {
                    // Sub-level tab
                    $this->_data['tabs'][] = new weever_app_subtab( array(
                            'id' => $tab->id,
                            'name' => ( ! empty($tab->title) ? $tab->title : $tab->tabTitle ),
                            'component' => $tab->content,
                            'ordering' => $tab->ordering, 
                            'published' => $tab->published,
                            'cms_feed' => WeeverHelper::get_cms_feed( $tab ),
                            'component_behaviour' => WeeverHelper::get_cms_feed( $tab ),
                            'var' => ( property_exists($tab, 'var')  ? ( ! is_null( json_decode($tab->var) ) ? json_decode($tab->var) : $tab->var ) : false ),
                            'type' => $tab->content,
                            'layout' => $tab->layout,
                            'icon_id' => $tab->icon_id,
                            'parent_id' => $tab->parent_id, // NOTE: parent ID is the same as this tab's ID
                            'config' => $tab->config,
                        )
                    );
                    //$tab->id, $tab->title, $tab->content, $tab->ordering, $tab->published, $tab->cms_feed, $tab->component_behaviour, ( ! is_null($tab->config) ) ? json_decode($tab->config) : $tab->config), $tab->component );
                }
            }

            if ( ! function_exists( 'tab_order' ) ) {
                function tab_order($a, $b) {
                    if ( $a->ordering == $b->ordering )
                        // Take the tab's ID into account
                        return $a->id - $b->id;
                    else
                        return ceil($a->ordering - $b->ordering);
                }
            }

            // Order the tabs
            usort( $this->_data['tabs'], 'tab_order' );

            // Put the subtabs in
            foreach ( $this->_data['tabs'] as $tab ) {
                if ( ! $tab->is_top_level_tab() ) {
                    // Get the parent tab by the subtab id
                    $parent_tab = $this->get_tab_by_id( $tab->parent_id );
                    
                    if ( false !== $parent_tab )
                        $parent_tab->add_subtab( $tab );
                }
            }

            $config = WeeverHelper::send_to_weever_server('config/get_config', array( 'site_key' => $this->_data['site_key'] ) );
            $design = WeeverHelper::send_to_weever_server('design/get_design', array( 'site_key' => $this->_data['site_key'] ) );
            $account = WeeverHelper::send_to_weever_server('account/get_account', array( 'site_key' => $this->_data['site_key'] ) );

            // Save raw data for loading into front-end javascript 
            $this->_data['raw_tabsync_data'] = json_encode( array( 'tabs' => $result->tabs, 'config' => $config->config, 'design' => $design->design, 'account' => $account->account ) );
            $this->_data['site_id'] = $account->account->app_id;
            // Install app name
            $this->_data['title'] =  $design->design->install->name;
            
            // Text at the top of the app (if no image used) 
            $this->_data['titlebar_title'] = $design->design->titlebar->text;
            
            // TODO: Perhaps check this against the local variable and sync it up?
            //$this->_data['app_enabled'] = $state->results->config->app_enabled;
            
            // weever directory
            $this->_data['ecosystem'] = $config->config->syndication->ecosystem;
            
            $this->_data['primary_domain'] = trim( str_ireplace( 'http://', '', str_ireplace( 'https://', '', $account->account->site ) ), '/' );
            
            // TODO: Modify to use new string-based tiers later
            $this->_data['tier'] = $account->account->tier_raw;
            
            $this->_data['loadspinner'] = $design->design->loadspinner->text;
            
            $this->_data['google_analytics'] = '';
            if ( is_array($config->config->analytics) ) {
                foreach ( $config->config->analytics as $analytic ) {
                    if ( 'google-analytics' == $analytic->service ) {
                        $this->_data['google_analytics'] = $analytic->code;
                    } elseif ( 'piwik' == $analytic->service ) {
                        $this->_data['piwik'] = $analytic->code;
                    }
                }
            }
            
            $this->_data['expiry'] = $account->account->expiry;

            // TODO: Grab domain from server?  Seems to be in $design->design->doamin (but is an array?)
            if ( isset($design->design->domain, $design->design->domain[0], $design->design->domain[0]->domain) )
                $this->_data['domain'] = $design->design->domain[0]->domain;

            // TODO: Confirm what the defaults are (false? blank?) before trying to save defaults
            if ( $design->design->launchscreen === false or ! $design->design->launchscreen->phone ) {
                // Push defaults to server
                
                // TODO: Uncomment when save_theme finished
                //$this->save_theme();
            } else {
                // TODO: Load granular options for devices (tablet, phone, etc)
                
                
                
                // Load additional theme settings
                $this->theme->titlebarSource = $design->design->titlebar->type;
                $this->theme->titlebarHtml = $design->design->titlebar->html;
                
                // TODO: Still exist?
                //$this->theme->template = $state->results->config->template;
                $this->launch->install_prompt = $design->design->install->prompt;
                $this->theme->tablet_load_live = $design->design->launchscreen->tablet;
                $this->theme->tablet_landscape_load_live = $design->design->launchscreen->tablet_landscape;
                $this->theme->phone_load_live = $design->design->launchscreen->phone;
                $this->theme->icon_live = $design->design->install->icon;
                $this->theme->titlebar_logo_live = $design->design->titlebar->image;
                
                $this->theme->css = $design->design->css->styles;
            }

            // Get the data
            $this->_data['local'] = @$config->config->localization;
            $this->_data['locales'] = @$config->config->available_locales;
        }
    }

    private function is_valid() {
        return isset( $this->_data['site_key'] ) && ! empty( $this->_data['site_key'] ) && ! empty( $this->_data['primary_domain'] );
    }

	public function is_free() {
		return ( $this->_data['tier'] == 1 or $this->_data['tier'] == 2.1 );
	}

    public function & get_device_option_names() {
        $option_names = array_keys( $this->_device_options );
        return $option_names;
    }

    public function & get_granular_device_option_names() {
        $option_names = $this->get_device_option_names();
        foreach ( $this->get_standard_device_option_names() as $device ) {
            foreach ( $option_names as $key => $option ) {
                if ( $device == $option )
                    unset( $option_names[$key] );
            }
        }

        return $option_names;
    }

    public function & get_standard_device_option_names() {
        return $this->_device_options_standard;
    }

    /**
     * Retrieve tabs, optionally only the top level ones
     * 
     * @param bool $only_top_level
     * @return array
     */
    public function & get_tabs($only_top_level = true) {
        if ( ! $only_top_level ) {
            // Return everything
            return $this->_data['tabs'];
        } else {
            $retval = array();

            foreach ( $this->_data['tabs'] as $tab ) {
                if ( $tab->is_top_level_tab() ) {
                    $retval[] = $tab;
                }
            }

            return $retval;
        }
    }

    /*
     * Get a tab
     *
     * @param int $id
     * @param bool $subtab - whether to return a subtab or the main level tab (both can have same ID)
     * @return weever_app_tab
     */
    public function & get_tab_by_id($id, $subtab = false) {
    	$retval = false;

        foreach ( $this->_data['tabs'] as $tab ) {
            if ( $tab->id == $id and $tab->is_top_level_tab() != $subtab ) {
                $retval = $tab;
                break;
            }
        }

        return $retval;
    }

    public function & get_subtab_by_id($id) {
        $retval = $this->get_tab_by_id($id, true);
        return $retval;
    }

    /**
     * Calls the validation function with the given function, to 
     * @param $data
     */
    public function & validate_tab( $data ) {
        return $this->create_tab($data);
    }

    /**
     * Function to edit tabs
     * @param $data
     */
    public function edit_tab($data) {
        // Same function for now
        return $this->create_tab($data);
    }

    /**
     * Function to verify tab
     * 
     * @param $data
     */
    public function verify_tab($data) {
        // Same function with confirm_feed / api_check set to 1
        return $this->create_tab($data);
    }
    
    /**
     * Creates a new tab with the given data
     * To create a subtab of an existing tab, use weever_app_tab::create_subtab instead
     * 
     * @param array $data
     * @throws Exception
     */
    public function create_tab($data) {
        // Build the contact details if applicable
        if ( isset( $data['component'] ) and $data['component'] == 'contact' ) {
            $contact = new stdClass;
            $contact->telephone = $data['phone'];
            $contact->email_to = $data['email'];
            $contact->address = $data['address'];
            $contact->town = $data['town'];
            $contact->state = $data['state'];
            $contact->country = $data['country'];
            $contact->googlemaps = ( $data['googlemaps'] ? 1 : 0 );
            $contact->emailform = ( $data['emailform'] ? 1 : 0 );

            if ( isset( $data['misc'] ) and trim( $data['misc'] ) )
                $contact->misc = $data['misc'];

            if ( isset( $data['image'] ) and trim( $data['image'] ) ) {
                $contact->image = $data['image'];
                $contact->showimage = 1;
            }

            // Contacts is now an array
            // TODO: Add support for grouping contacts together on one tab
            $contacts = array();
            $contacts[] = $contact;
            $data['config_cache'] = json_encode( array( 'contacts' => $contacts ) );
            $data['content'] = 'contact';
            $data['layout'] = 'panel';
            $data['icon_id'] = self::CONTACT_ICON_ID;
        }

        // Translate old component / component_behaviour values to the new config / config_cache
        $data = $this->convert_component_to_content_config($data);
        
        $postdata = array(
            'title' => isset( $data['name'] ) ? $data['name'] : null,
            'content' => isset( $data['content'] ) ? $data['content'] : null,
            'config' => isset( $data['config'] ) ? $data['config'] : null,
            'published' => isset( $data['published'] ) ? $data['published'] : null,
            'id' => isset( $data['id'] ) ? $data['id'] : null,
            'var' => isset( $data['var'] ) ? $data['var'] : null,
            'parent_id' => isset( $data['parent_id'] ) ? $data['parent_id'] : null,
            'layout' => isset( $data['layout'] ) ? $data['layout'] : 'list',
            'icon_id' => isset( $data['icon_id'] ) ? $data['icon_id'] : 1,
            'config_cache' => isset( $data['config_cache'] ) ? $data['config_cache'] : null,
        );

        if ( isset( $data['tabLayout'] ) )
            $postdata['tabLayout'] = $data['tabLayout'];

        if ( isset( $data['parent_id'] ) and is_numeric( $data['parent_id'] ) )
            $postdata['parent_id'] = intval( $data['parent_id'] );
        
        // Do we want to verify the api?
        if ( isset( $data['api_check'] ) and is_numeric( $data['api_check'] ) )
            $postdata['api_check'] = intval($data['api_check']);

        if ( isset( $data['confirm_feed'] ) and is_numeric( $data['confirm_feed'] ) )
            $postdata['confirm_feed'] = intval($data['confirm_feed']);

        // Do we want to edit an existing item?
        if ( isset( $data['edit_id'] ) and is_numeric( $data['edit_id'] ) )
            $postdata['tab_id'] = intval($data['edit_id']);

        if ( isset( $postdata['confirm_feed'] ) and $postdata['confirm_feed'] ) {
            $result = WeeverHelper::send_to_weever_server('validator/validate_feed', $postdata);
            
            // Verify it's a valid json
            if ( empty($result) )
                echo json_encode( array( 'api_check'=>false, 'invalid_json'=>$result ) );
            else
                echo json_encode( $result );

            return;
        } else {
            $result = WeeverHelper::send_to_weever_server('tabs/add_tab', $postdata);            
        }

        if ( false === $result ) {
            throw new Exception( sprintf( __( 'Error adding new tab', 'weever' ), $result ) );
        }
    }

    /**
     * Takes array of posted data with component/component_behaviour etc, and converts to content/config as
     * needed
     * TODO: Remove this and change UI to use content / config / cache_config instead
     * 
     * @return array
     */
    public function convert_component_to_content_config($data) {
        switch ( strtolower($data['component']) ) {
            case 'blog':
                $data['config'] = json_encode(array('url' => site_url($data['cms_feed'])));
                $data['content'] = 'html';
                break;
            case 'page':
                $image = WeeverHelper::get_image_url(str_replace('index.php?page_id=', '', $data['cms_feed']));
                $data['config'] = json_encode(array('url' => site_url($data['cms_feed']), 'type' => 'htmlContent', 'name' => $data['name'], 'image' => $image));
                $data['content'] = 'htmlPage';
                $data['layout'] = 'panel';
                break;
            case 'map':
                $data['config'] = json_encode(array('url' => site_url($data['cms_feed']), 'gps' => '1'));
                $data['tabLayout'] = 'map';
                $data['content'] = 'html';
                break;
            case 'proximity':
                $data['config'] = json_encode(array('url' => site_url($data['cms_feed']), 'gps' => '1', 'geotag' => '1'));
                $data['content'] = 'html';
                break;
            case 'directory':
                $data['config'] = json_encode(array('url' => site_url($data['cms_feed'])));
                $data['tabLayout'] = 'list';
                $data['content'] = 'html';
                break;
            case 'twitteruser':
                $data['config'] = json_encode(array('screen_name' => $data['component_behaviour'], 'include_entities'=>1));
                $data['content'] = 'twitterUser';
                break;
            case 'twitter':
                $data['config'] = json_encode(array('q' => $data['component_behaviour'], 'include_entities' => 1));
                $data['content'] = 'twitter';
                break;
            case 'twittersearch':
                $data['config'] = json_encode(array('q' => $data['component_behaviour'], 'include_entities' => 1));
                $data['content'] = 'twitterSearch';
                break;
            case 'blogger':
                $data['config'] = json_encode(array('blog_url' => $data['component_behaviour']));
                $data['content'] = 'blogger';
                break;
            case 'facebook':
                $data['config'] = json_encode(array('user_id' => $data['component_behaviour']));
                $data['content'] = 'facebookStatuses';
                break;
            case 'facebook.events':
                $data['config'] = json_encode(array('user_id' => $data['component_behaviour']));
                $data['content'] = 'facebookEvents';
                break;
            case 'facebook.photos':
                $data['config'] = json_encode(array('user_id' => $data['component_behaviour']));
                $data['content'] = 'facebookAlbums';
                break;
            case 'youtube':
                $data['config'] = json_encode(array('url' => $data['component_behaviour']));
                $data['content'] = 'youtube';
                break;
            case 'youtube.playlists':
                $data['config'] = json_encode(array('url' => $data['component_behaviour']));
                $data['content'] = 'youtubePlaylist';
                break;
            case 'vimeo':
                $data['config'] = json_encode(array('url' => $data['component_behaviour']));
                $data['content'] = 'vimeo';
                break;
            case 'flickr':
                // url should be user_id?
                $data['config'] = json_encode(array('user_id' => $data['component_behaviour']));
                $data['content'] = 'flickrPhotostream';
                $data['layout'] = 'carousel';
                break;
            case 'flickr.photosets':
                $data['config'] = json_encode(array('user_id' => $data['component_behaviour']));
                $data['content'] = 'flickrPhotosets';
                $data['layout'] = 'list';
                break;
            case 'google.picasa':
                $data['config'] = json_encode(array('user_id' => $data['component_behaviour']));
                $data['content'] = 'picasaAlbums';
                break;
            case 'google.calendar':
                $data['config'] = json_encode(array('calendar_id' => $data['component_behaviour']));
                $data['content'] = 'googleCalendar';
                break;
            case 'foursquare':
                $data['config'] = json_encode(array('venue_id' => $data['component_behaviour']));
                $data['content'] = 'foursquarePhotos';
                $data['layout'] = 'carousel';
                break;
            case 'wufoo':
                // url and apikey
                $data['config'] = json_encode(array('url' => $data['component_behaviour'], 'apikey' => $data['var']));
                $data['content'] = 'wufoo';
                break;
            default:
                // Probably a page or something, just fall through...
        }

        return $data;
    }
    
    /**
     * Generate a QR code and cache it locally
     */
	public function generate_qr_code() {
	    if ( $this->is_valid() ) {
    		if ( $this->staging_mode ) {
    			$type = 'stage';
    			$queryExtra = '&staging=1';
    		} else {
    			$type = 'live';
    			$queryExtra = '';
    		}

    		// Add cache manifest = false for private preview
    		if ( $queryExtra )
    			$queryExtra .= '&cache_manifest=false';
    		else
    			$queryExtra = '?cache_manifest=false';
    		
    		$server = ($this->staging_mode ? WeeverConst::LIVE_STAGE : WeeverConst::LIVE_SERVER);
    		
    		$qr_site_url = "http://qr.weever.ca/?site=" . $this->primary_domain;
    		$qr_app_url = "http://qr.weever.ca/?site=" . urlencode( str_replace('http://', '', $server) . 'app/' . $this->primary_domain . $queryExtra );

    		// Set the urls to the direct link by default
		    $this->_data['qr_code_private'] = $qr_app_url;
		    $this->_data['qr_code_public'] = $qr_site_url;

    		// See if the uploads folder is accessible, and if so cache the image
    		if ( ! WP_DEBUG ) {
	    		$uploads = wp_upload_dir();
	    		if ( isset( $uploads['basedir'] ) and isset( $uploads['baseurl'] ) ) {
	    		    // Try to make the weeverapps sub-folder
	    		    if ( wp_mkdir_p( trailingslashit( $uploads['basedir'] ) . 'weeverapps' ) ) {
	            		if ( get_option( 'weever_qr_site_'.$type.'_code_time', 0 ) > time() || @copy( $qr_site_url, trailingslashit( $uploads['basedir'] ) . 'weeverapps/qr_site_'.$type.'.png' ) ) {
	                        $this->_data['qr_code_public'] = trailingslashit( $uploads['baseurl'] ) . 'weeverapps/qr_site_'.$type.'.png';
	
	                        if ( get_option( 'weever_qr_site_'.$type.'_code_time', 0 ) <= time() )
	                            // Cache for 1 hour
	                            update_option( 'weever_qr_site_'.$type.'_code_time', time() + 60*60 );
	            		}
	
	            		if ( get_option( 'weever_qr_app_'.$type.'_code_time', 0 ) > time() || @copy( $qr_app_url, trailingslashit( $uploads['basedir'] ) . 'weeverapps/qr_app_'.$type.'.png' ) ) {
	                        $this->_data['qr_code_private'] = trailingslashit( $uploads['baseurl'] ) . 'weeverapps/qr_app_'.$type.'.png';
	
	                        if ( get_option( 'weever_qr_app_'.$type.'_code_time', 0 ) <= time() )
	                            // Cache for 1 hour
	                            update_option( 'weever_qr_app_'.$type.'_code_time', time() + 60*60 );
	            		}
	            	}
	    		}
    		}
	    } else {
	        $this->_data['qr_code_private'] = $this->_data['qr_code_public'] = '';
	    }
	}

	/**
	 *
	 *
	 * @param array $order array of type, in order they should appear
	 */
	public function order_tabs( $order ) {
		$postdata = array(
		    'order' => implode(',', $order),
		);

        try {
            $result = WeeverHelper::send_to_weever_server( 'tabs/sort_tabs', $postdata );            
        } catch ( Exception $e ) {
            throw new Exception( 'Error updating tabs' . $e->getMessage() );
        }
	}
	
	/**
	 * Manually force a build of the app
	 */
	public function build() {
		wp_remote_get(WeeverConst::LIVE_SERVER . 'build2/' . $this->primary_domain );
	}

    /**
     * Save the currently stored configuration to the server
     *
     * @throws exception on error
     */
    public function save($build = true) {
        update_option( 'weever_app_enabled', $this->_data['app_enabled'] );
        update_option( 'weever_api_key', $this->_data['site_key'] );
        update_option( 'weever_staging_mode', $this->_data['staging_mode'] );

        if ( $this->_data['site_key'] ) {
	        // Mobile settings
	        foreach ( $this->get_device_option_names() as $option ) {
	            update_option( 'weever_device_option_'.$option, $this->_data[$option] );
	        }
	        update_option( 'weever_granular_devices', $this->_data['granular'] );
	
	        // Build the devices listing
	        $devices = array();
	        if ( $this->granular ) {
	            foreach ( $this->get_granular_device_option_names() as $option ) {
	                if ( $this->$option )
	                    $devices[] = $option;
	            }
	        } else {
	            foreach ( $this->get_standard_device_option_names() as $option ) {
	                if ( $this->$option )
	                    $devices[] = $option;
	            }
	        }
	        $devices = implode( ",", $devices );

            $options = new StdClass();

            $options->analytics = array();

            $options->analytics[0] = new StdClass();
            $options->analytics[0]->service = 'google-analytics';
            $options->analytics[0]->code = $this->google_analytics;

            $options->device = $devices;
            $options->syndication->ecosystem = $this->ecosystem;
            $options->online = $this->app_enabled;
            $options->localization = $this->local;

            $postdata = array(
				'config' => json_encode( $options ),
			);
	
		    if ( false === $build )
		    	$postdata['build'] = false;
		    
			$result = WeeverHelper::send_to_weever_server('config/set_config', $postdata);
	
			if ( false === $result )
			    throw new Exception( __( 'Error saving config', 'weever' ) );
	
			if ( array_key_exists( 'site_key', $this->_changed ) ) {
                // Save the theme settings to ensure there are defaults
                $this->save_theme();

				// Make sure this site key has been upgraded
				//$this->upgrade_site_key();
			}
        }
        
		// Reset the changed array
		$this->_changed = array();

        // Reload the data afterwards to ensure everything was saved
        $this->reload_from_server();

        // Cache the primary_domain, domain and tier value for the redirection url creation
        update_option( 'weever_primary_domain', $this->_data['primary_domain'] );
        update_option( 'weever_tier', $this->_data['tier'] );
        update_option( 'weever_domain', $this->_data['domain'] );
    }

    /**
     * Function to save the theme/design settings to server 
     * @param bool $build
     * @throws Exception
     */
    public function save_theme($build = true) {
        // Theme settings
        update_option( 'weever_tablet_load_live', $this->theme->tablet_load_live );
        update_option( 'weever_tablet_landscape_load_live', $this->theme->tablet_landscape_load_live );
        update_option( 'weever_phone_load_live', $this->theme->phone_load_live );
        update_option( 'weever_icon_live', $this->theme->icon_live );
        update_option( 'weever_titlebar_logo_live', $this->theme->titlebar_logo_live );

        $design = new StdClass();
        $design->titlebar = new StdClass();

        // Graphics
        $design->launchscreen->phone = $this->theme->phone_load_live;
        $design->launchscreen->tablet = $this->theme->tablet_load_live;
        $design->launchscreen->tablet_landscape = $this->theme->tablet_landscape_load_live;
        
        $design->titlebar->html	= $this->theme->titlebarHtml;
        $design->titlebar->text = $this->titlebar_title;
        $design->titlebar->type = $this->theme->titlebarSource;
        $design->titlebar->image = $this->theme->titlebar_logo_live;
        
        $design->css = new StdClass();
        $design->css->styles = $this->theme->css;
        $design->css->url = ''; // TODO: Add ability for specifying a css url

        $design->animations = new StdClass();
        $design->animation->launch = new StdClass();
        // TODO: Interface to override
        $design->animation->launch->type = '';
        $design->animation->launch->duration = 0;
        $design->animation->launch->timeout = 0;

        $design->install = new StdClass();
        $design->install->prompt = $this->launch->install_prompt;
        $design->install->name = $this->title;
        $design->install->icon = $this->theme->icon_live;
        
        $design->loadspinner = new stdClass();
        $design->loadspinner->text = $this->loadspinner;

        $design->domain = array();

        if( $this->domain )
            $design->domain[0] = $this->domain;

        $postdata = array(
			'design' => json_encode( $design ),
		);

		if ( false === $build )
			$postdata['build'] = false;
		
        $result = WeeverHelper::send_to_weever_server('design/set_design', $postdata);

        if ( false === $result )
            throw new Exception( __( 'Error saving theme' ) );
	}

    /**
     * Publish or Unpublish the given local tab ids
     *
     * @param int[] $ids
     * @param int publish flag
     */
    public function save_publish_status($ids, $publish) {
		$postdata = array(
				'published' => $publish,
				'app' =>'ajax',
				'm' => 'publish_tab',
				'site_key' => $this->site_key,
				'local_tab_id' => $ids,
				'version' => WeeverConst::VERSION,
				'generator' => WeeverConst::NAME
				);

		return WeeverHelper::send_to_weever_server($postdata);
    }
}