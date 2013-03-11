<?php

class weever_app_subtab extends weever_app_tab {

    //$id, $name, $type, $ordering, $published, $cms_feed = false, $component_behaviour = '', $var = '', $component = ''
    public function __construct($args) {
        // TODO: Add validation and manipulation of params, if needed
        $this->_data = $args;
    }

    /**
     * Function to show if this is a top level (toolbar) tab or not
     *
     * @return bool
     */
    public function is_top_level_tab() {
        return false;
    }
}