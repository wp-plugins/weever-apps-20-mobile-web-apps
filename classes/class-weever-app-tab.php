<?php

class weever_app_tab {

    const MOVE_UP = 'up';
    const MOVE_DOWN = 'down';

    protected $_data = array();
    protected $_changed = array();
    private $_subtabs = array();

    public function __construct($args) {
        // TODO: Any validation outside what is being done already in the WeeverApp class
        $this->_data = $args;
        if ( ! isset($this->_data['tabLayout']) )
            $this->_data['tabLayout'] = null;
        
		$weeverapp = new WeeverApp( false );
				
		$this->_data['icon'] = $this->_data['icon_id'];
        // Get the icon from cache if available, otherwise reload from server
        $icon_image_data = get_transient( 'weever_icon_image_' . $this->_data['icon_id'] );
        if ( false === $icon_image_data ) {
            $icon_image_data = file_get_contents( WeeverConst::LIVE_SERVER . '/api/' . WeeverConst::API_VERSION . '/icons/get_icon_base64?site_key=' . $weeverapp->site_key . '&icon_id=' . $this->_data['icon_id'] );
            // Cache for a day
            set_transient( 'weever_icon_image_' . $this->_data['icon_id'], $icon_image_data, ( 60 * 60 * 24 ) );
        }
        $this->_data['icon_image'] = 'data:image/png;base64,' . $icon_image_data;
    }

    public function & __get($var) {
        switch ( $var ) {
            default:
                if ( array_key_exists( $var, $this->_data ) )
                    return $this->_data[$var];
                else
                    throw new Exception( __( 'Invalid parameter name', 'weever' ) );
        }
    }

    public function __set($var, $val) {
        switch ( $var ) {
            case 'id':
                throw new Exception( __( 'Cannot edit the tab id after loading', 'weever' ) );
                break;
            default:
                if ( array_key_exists( $var, $this->_data ) ) {
                    $this->_data[$var] = $val;
                    $this->_changed[$var] = $var;
                } else {
                    throw new Exception( __( 'Invalid parameter name', 'weever' ) );
                }
        }
    }

    /**
     * Return json encoded version of this tab (for editing existing items on front-end)
     */
    public function __toString() {
   		$retval = $this->_data;
   		unset($retval['var']);
   		if ( is_object($this->_data['var']) )
   			$var = get_object_vars($this->_data['var']);
   		else 	
   			$var = array();
   		
   		$retval = array_merge($retval, $var);

   		switch ( $this->component ) {
   			case 'contact':
   				$retval['phone'] = $retval['telephone'];
   				$retval['email'] = $retval['email_to'];
   				break;
   			case 'wufoo':
   				$retval['var_value'] = $retval['apikey'];
   				break;
   		} 

   		return str_replace("\n", "", json_encode($retval));
    }

    /**
     * Get the display name for this tab
     */
    public function get_display_name() {
        switch ( strtolower($this->component) ) {
            case 'twitteruser':
                return 'Twitter User Feed';
            case 'twitter':
                return 'Twitter Feed';
            case 'twittersearch':
                return 'Twitter Search Feed';
            case 'blogger':
                return 'Blogger';
            case 'facebook':
                return 'Facebook';
            case 'facebookevents':
                return 'Facebook Events';
            case 'facebookalbums':
                return 'Facebook Albums';
            case 'youtube':
                return 'Youtube';
            case 'youtubeplaylist':
                return 'Youtube Playlist';
            case 'vimeo':
                return 'Vimeo';
            case 'flickrphotostream':
                return 'Flickr';
            case 'flickrphotosets':
                return 'Flickr Photoset';
            case 'picasaalbums':
                return 'Picasa';
            case 'googlecalendar':
                return 'Google Calendar';
            case 'foursquare':
                return 'Foursquare';
            case 'wufoo':
                return 'wufoo';
        }
    }
    
    /**
     * Used when editing to get the right dialog ID
     */
    public function get_component_id() {
    	
    	if ( 'contact' == $this->component ) {
    		$component = 'wordpress_contact';
    	} elseif ('twitteruser' == strtolower($this->component) ) {
    		$component = 'twitter-user';
    	} elseif ('twitter' == strtolower($this->component) ) {
    		$component = 'twitter-search';
    	} elseif ('facebookstatuses' == strtolower($this->component) ) {
    		$component = 'facebook-social';
        } elseif ('facebookevents' == strtolower($this->component) ) {
            $component = 'facebook-events';
        } elseif ('facebookalbums' == strtolower($this->component) ) {
            $component = 'facebook-photos';
        } elseif ('flickrphotostream' == strtolower($this->component) ) {
            $component = 'flickr';
        } elseif ('flickrphotosets' == strtolower($this->component) ) {
            $component = 'flickr-photosets';
        } elseif ('picasaalbums' == strtolower($this->component) ) {
            $component = 'picasa';
        } elseif ('foursquarephotos' == strtolower($this->component) ) {
            $component = 'foursquare';
    	} elseif ('google.calendar' == strtolower($this->component) or 'googleCalendar' == $this->component ) {
    		$component = 'google_calendar';
    	} elseif ('youtube' == strtolower($this->component) ) {
    		$component = 'youtube-channel';
    	} elseif ('youtubeplaylist' == strtolower($this->component) ) {
    		$component = 'youtube-playlist';
    	} else {
    		$component = str_replace('.', '-', $this->component);
    	}
    			 
    	return $component;
    }

    /**
     * 
     * @param bool $parent_tab_id
     */
    public function move_tab($parent_tab_id = false) {
        $result = WeeverHelper::send_to_weever_server('tabs/set_parent_id', array(
                'tab_id' => $this->id,
                'parent_id' => intval($parent_tab_id)
            )
        );
        
        if ( false === $result )
            throw new Exception('Error moving tab');
    }
    
    /**
     * Add an already created subtab to this tab's elements
     *
     * @param WeeverAppSubtab $subtab
     */
    public function add_subtab(&$subtab) {
        $this->_subtabs[] = $subtab;
    }

    public function edit_subtab($data) {
    	// Just call create_subtab for now, same difference
    	return $this->create_subtab($data);
    }
    
    /**
     * Creates a new subtab from the given parameters and pushes to the Weever Apps server
     *
     * @param array tab data
     */
    public function create_subtab($data) {
        $weeverapp = new WeeverApp(false);
        // Ensure parent ID is set to this parent ID
        $data['parent_id'] = $this->id;
        return $weeverapp->create_tab($data);
    }

    public function & get_subtabs() {
        return $this->_subtabs;
    }

    public function & get_subtab($id) {
        foreach ( $this->_subtabs as $subtab ) {
            if ( $subtab->id == $id )
                return $subtab;
        }

		return false;
    }

    /**
     * Function to show if this is a top level (toolbar) tab or not
     *
     * @return bool
     */
    public function is_top_level_tab() {
        return true;
    }

    public function delete() {
        $postdata = array(
            'tab_id' => $this->id
        );
        
        $result = WeeverHelper::send_to_weever_server('tabs/delete', $postdata);

        if ( false === $result )
            throw new Exception( __( 'Error deleting tab', 'weever' ) );
    }

    public function save() {
        // Name change
        if ( isset( $this->_changed['name'] ) ) {
            $postdata = array('tab_id' => $this->id);

            if ( $this->is_top_level_tab() ) {
                $postdata['tabTitle'] = $this->name;
                WeeverHelper::send_to_weever_server( 'tabs/set_tabTitle', $postdata );                
            } else {
    			$postdata['title'] = $this->name;
                WeeverHelper::send_to_weever_server( 'tabs/set_title', $postdata );
            }
        }

        if ( isset( $this->_changed['icon_id'] ) ) {
	        $postdata = array('tab_id' => $this->id);
            $postdata['icon_id'] = $this->icon_id;
            WeeverHelper::send_to_weever_server( 'tabs/set_icon_id', $postdata );
        }

        if ( isset( $this->_changed['tabLayout'] ) ) {
            $postdata = array('tab_id' => $this->id);
            $postdata['tabLayout'] = $this->tabLayout;
            WeeverHelper::send_to_weever_server( 'tabs/add_tab', $postdata );
        }

        if ( isset( $this->_changed['published'] ) ) {
			$postdata = array(
                'tab_id' => $this->id,
                'published' => $this->published,
            );
            
            WeeverHelper::send_to_weever_server( 'tabs/set_published', $postdata );
        }
        
        $this->_changed = array();
    }
}