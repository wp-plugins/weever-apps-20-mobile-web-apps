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

            if ( true ) {
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
        if ( array_key_exists( $var, $this->_data ) )
            return $this->_data[$var];
        else
            throw new Exception( "Invalid parameter name $var" );
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

    public function is_free() {
        return ( $this->tier == 1 or $this->tier == 2.1 );
    }

    private function is_valid() {
        return isset( $this->_data['site_key'] ) && ! empty( $this->_data['site_key'] ) && ! empty( $this->_data['primary_domain'] );
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
     * Generate a QR code and cache it locally
     */
    public function generate_qr_code() {
        if ( $this->is_valid() ) {
            if ( $this->staging_mode ) {
                $type = 'stage';
                $queryExtrax = '&staging=1';
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
            
            $qr_site_url = "//qr.weeverapps.com/?site=" . $this->primary_domain;
            $qr_app_url = "//qr.weeverapps.com/?site=" . urlencode( str_replace('http://', '', $server) . 'app/' . $this->primary_domain . $queryExtra );

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
            $options->syndication = new StdClass();
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
                // $this->save_theme();

                // Make sure this site key has been upgraded
                //$this->upgrade_site_key();
            }
        }
        
        // Reset the changed array
        $this->_changed = array();

        // Reload the data afterwards to ensure everything was saved
        // $this->reload_from_server();

        // Cache the primary_domain, domain and tier value for the redirection url creation
        update_option( 'weever_primary_domain', $this->_data['primary_domain'] );
        update_option( 'weever_tier', $this->_data['tier'] );
        update_option( 'weever_domain', $this->_data['domain'] );
    }

    /**
     * Attempt to reload the data from the Weever server using the API
     * TODO: Only reload the required vars for the given tab or function
     */
    private function reload_from_server() {
        if ( ! empty( $this->_data['site_key'] ) ) {
            // $start = microtime(true);
            
            $config = WeeverHelper::send_to_weever_server('config/get_config', array( 'site_key' => $this->_data['site_key'] ) );
            $account = WeeverHelper::send_to_weever_server('account/get_account', array( 'site_key' => $this->_data['site_key'] ) );
            $domains = WeeverHelper::send_to_weever_server('design/get_domain', array( 'site_key' => $this->_data['site_key'] ) );
            
            $this->_data['app_enabled'] = $config->config->online;

            // Save raw data for loading into front-end javascript 
            // $this->_data['raw_tabsync_data'] = json_encode( array( 'tabs' => $result->tabs, 'config' => $config->config, 'account' => $account->account ) );
            $this->_data['site_id'] = $account->account->app_id;
            
            // weever directory
            // $this->_data['ecosystem'] = $config->config->syndication->ecosystem;
            
            $this->_data['primary_domain'] = trim( str_ireplace( 'http://', '', str_ireplace( 'https://', '', $account->account->site ) ), '/' );
            update_option( 'weever_primary_domain', $this->_data['primary_domain'] );

            if ( $domains and $domains->domain ) {
                $d = end( $domains->domain );
                $this->_data['domain'] = $d->domain;
                update_option( 'weever_domain', $this->_data['domain'] );
            }

            // TODO: Modify to use new string-based tiers later
            $this->_data['tier'] = $account->account->tier_raw;
            
            $this->_data['google_analytics'] = '';
            // if ( is_array($config->config->analytics) ) {
            //     foreach ( $config->config->analytics as $analytic ) {
            //         if ( 'google-analytics' == $analytic->service ) {
            //             $this->_data['google_analytics'] = $analytic->code;
            //         } elseif ( 'piwik' == $analytic->service ) {
            //             $this->_data['piwik'] = $analytic->code;
            //         }
            //     }
            // }
            
            $this->_data['expiry'] = $account->account->expiry;

            // Get the data
            // $this->_data['local'] = @$config->config->localization;
            // $this->_data['locales'] = @$config->config->available_locales;
        }
    }
}