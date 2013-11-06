<?php

    class WeeverAppThemeStyles {

    	public 		$aLink;
    	public 		$spanLogo;
    	public 		$contentButton;
    	public 		$border;
    	public 		$fontType;
    	public 		$blogIcon;
    	public 		$pagesIcon;
    	public 		$contactIcon;
    	public 		$socialIcon;
    	public 		$videoIcon;
    	public 		$photoIcon;
    	public 		$mapIcon;
    	public 		$titlebarHtml;
    	public 		$titlebarSource		= "image";
    	public 		$template			= "sencha";
    	public 		$useCssOverride;
    	public 		$useCustomIcons;
    	public		$css;

    	// Theme images
    	public      $tablet_load_live;
    	public      $tablet_landscape_load_live;
    	public      $phone_load_live;
    	public      $icon_live;
    	public      $titlebar_logo_live;
    	
    	private		$image_dimensions = array('tablet_load_live' => array('width' => 1536, 'height' => 2008),
    										  'tablet_landscape_load_live' => array('width' => 1496, 'height' => 2048),
    										  'phone_load_live' => array('width' => 640, 'height' => 920),
    									      'icon_live' => array('width' => 144, 'height' => 144),
    										  'titlebar_logo_live' => array('width' => 600, 'height' => 64));
    	
    	public function __construct() {
            // Default theme images
            // TODO: Remove the get_option and load from server instead
    	    $this->tablet_load_live = get_option( 'weever_tablet_load_live', WEEVER_PLUGIN_URL . 'static/img/launchscreens/tablet_load_default.png' );
    	    $this->tablet_landscape_load_live = get_option( 'weever_tablet_landscape_load_live', WEEVER_PLUGIN_URL . 'static/img/launchscreens/tablet_landscape_load_default.png' );
    	    $this->phone_load_live = get_option( 'weever_phone_load_live', WEEVER_PLUGIN_URL . 'static/img/launchscreens/phone_load_default.png' );
    	    $this->icon_live = get_option( 'weever_icon_live', WEEVER_PLUGIN_URL . 'static/img/launchscreens/icon_default.png' );
    	    $this->titlebar_logo_live = get_option( 'weever_titlebar_logo_live', WEEVER_PLUGIN_URL . 'static/img/launchscreens/titlebar_logo_default.png' );
    	}

    	public function load_from_json($json_obj) {
			if ( is_array( $json_obj ) || is_object( $json_obj ) ) {
	    	    foreach ( $json_obj as $key => $val ) {
					if ( property_exists( $this, $key ) )
	    	        	$this->$key = $json_obj->$key;
	    	        	
	    	        if ( property_exists( $this, $key . '_live' ) and ! empty( $json_obj->$key ) )
	    	        	$this->{$key.'_live'} = $json_obj->$key;
	    	    }
			}
    	}
    }