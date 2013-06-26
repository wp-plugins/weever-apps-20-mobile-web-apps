<?php

    class WeeverHelper {

        private static $_weeverapp = false;

        public static function get_rss_url_from_site_url($site_url) {
            $retval = false;
            $result = wp_remote_get($site_url);
            if ( ! is_wp_error( $result ) and is_array( $result ) and isset( $result['body'] ) ) {
                // search for the <link rel="alternate" .../> RSS tag
                $matches = null;
                $dom = new DOMDocument();
                $dom->loadHTML($result['body']);
                $link_tags = $dom->getElementsByTagName('link');
                foreach ( $link_tags as $link_tag ) {
                    if ( $link_tag->hasAttributes() ) {
                        $attributes = $link_tag->attributes;
                        if ( ! is_null($attributes) ) {
                            if ( 'alternate' == $attributes->getNamedItem('rel')->nodeValue and 'application/rss+xml' == $attributes->getNamedItem('type')->nodeValue ) {
                                $rss_url = $attributes->getNamedItem('href')->nodeValue;
                                break;
                            }
                        }
                    }
                }
                if ( isset($rss_url) ) {
                    $rss_url_parts = parse_url( $rss_url );
                    if ( strlen( $rss_url ) and is_array( $rss_url_parts ) and ! isset( $rss_url_parts['host'] ) ) {
                        // may be a relative url, add the domain of the site_url onto it
                        $url_parts = parse_url( $site_url );
                        $rss_url = trailingslashit( $url_parts['scheme'] . '://' . $url_parts['host'] ) . ltrim( $rss_url, '/' );
                    }
                    if ( FALSE !== filter_var( $rss_url, FILTER_VALIDATE_URL ) )
                        $retval = $rss_url;
                }
            }
            return $retval;
        }

        public static function is_rss_url_valid($url) {
            $retval = false;
            $request_url = self::get_rss_r3s_feed_url($url);
            $result = wp_remote_get( $request_url );
            if ( ! is_wp_error( $result ) and isset( $result['body'] ) ) {
                $r3s_feed = json_decode( $result['body'] );
                if ( ! is_null( $r3s_feed ) and isset( $r3s_feed->count ) and $r3s_feed->count > 0 ) {
                    $retval = true;
                }
            }
            return $retval;
        }

        public static function get_rss_r3s_feed_url($rss_url) {
            return WeeverConst::LIVE_SERVER . 'api/v2/_rss2r3s/byUrl?url=' . urlencode($rss_url);
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
		        'WEEVER_JS_NO_IMAGE_URL' => WEEVER_PLUGIN_URL . 'static/images/icons/no-image.png',
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
            return "index.php?page_id=$page->ID";
        }

        /**
         * Get the url to the user page/feed without any permalink
         *
         * @param WPUser $user
         */
        public static function get_user_link_relative( $user ) {
            $link = "index.php?author_name=$user->user_nicename";
            return $link;
        }

        public static function get_user_feed_link_relative( $user, $feed = 'r3s' ) {
        }

        /**
         * Set the weeverapp variable in preparation for server
		 *
         * @param unknown_type $weeverapp
         */
        public static function set_weeverapp(&$weeverapp) {
            self::$_weeverapp = $weeverapp;
        }

        public static function & get_weeverapp() {
            return self::$_weeverapp;
        }

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
        
        /**
         * Helper function to convert naming from the old v1 API to one standard "cms_feed" value
         * @TODO: Handle other cases, things like wufoo have two...
         * 
         * @param stdClass $tab
         */
        public static function get_cms_feed($tab) {
            $possible_values = array('screen_name', 'q', 'user_id', 'blog_url', 'calendar_id', 'venue_id');
            
            $config_cache = $tab->config;
            
            foreach ( $possible_values as $val ) {
                if ( isset($config_cache->$val) )
                    return $config_cache->$val;
                elseif ( isset($config_cache->proxy) and isset($config_cache->proxy->$val) )
                    return $config_cache->proxy->$val;
                elseif ( isset($config_cache->extraParams) and isset($config_cache->extraParams->$val) )
                    return $config_cache->extraParams->$val;
            }
            
            // If nothing found, look for 'url' as a last resort
            if ( isset($config_cache->url) )
                return $config_cache->url;
            elseif ( isset($config_cache->proxy->url) )
                return $config_cache->proxy->url;
            elseif ( isset($config_cache->extraParams->url) )
                return $config_cache->extraParams->url;
            
            return false;
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
                        throw new Exception( __( 'Error communicating with the Weever Apps server', 'weever' ) );
                    elseif ( isset($retval->error) and true == $retval->error )
                        throw new Exception( __( 'Error communicating with the Weever Apps server - ' . $retval->message, 'weever' ) );                        
            	} else {
            		$error = ( is_wp_error( $result ) ? ': ' . $result->get_error_message() : '' );
            		
            	    throw new Exception( __( 'Error communicating with the Weever Apps server' . $error, 'weever' ) );
            	}
            } else {
                var_dump(debug_backtrace(FALSE));
                throw new Exception( __( 'Invalid postdata sent to function, invalid endpoint, or weeverapp not set', 'weever' ) );
            }

        	return $retval;
        }
    }