<?php

$html = WeeverSimpleHTMLDomHelper::str_get_html( $jsonHtml->html );

if ( ! get_option('weever_do_not_modify_links', false ) ) {
    
    // Make all external links target="_BLANK"
    make_links_target_blank( $html->nodes[0] );
    $jsonHtml->html = $html->save();

    $jsonHtml->html = str_replace( "<iframe title=\"YouTube video player\" width=\"480\" height=\"390\"",
        "<iframe title=\"YouTube video player\" width=\"160\" height=\"130\"", $jsonHtml->html );

    // Add full=1 to the end of all links
    // With query param
    $jsonHtml->html = preg_replace( '`(href)="http([^?"]*)\?([^?"#]*)(#)?([^?"#]*)"`i', '$1="http$2?$3&full=1$4$5"', $jsonHtml->html );
    $jsonHtml->html = preg_replace( '`(href)=\'http([^?\']*)\?([^?\']*)(#)?([^?\'#]*)\'`i', '$1=\'http$2?$3&full=1$4$5\'', $jsonHtml->html );
    // Without query param
    $jsonHtml->html = preg_replace( '`(href)="http([^?"#]*)(#)?([^?"#]*)"`i', '$1="http$2?full=1$3$4"', $jsonHtml->html );
    $jsonHtml->html = preg_replace( '`(href)=\'http([^?\']*)(#)?([^?\'#]*)\'`i', '$1=\'http$2?full=1$3$4\'', $jsonHtml->html );

    // Make sure any internal app links aren't affected by the full=1
    $jsonHtml->html = str_replace('?full=1#!/', '#!/', $jsonHtml->html);
    $jsonHtml->html = str_replace('full=1#!/', '#!/', $jsonHtml->html);
}

// Add the geo data if any
if ( function_exists( 'get_post_meta' ) ) {
    if ( get_post_meta( get_the_ID(), 'geo_public', true ) != '' ) {
        // Make sure geo is enabled on the post and pass the lat/lon
        $geo_on = get_post_meta( get_the_ID(), 'geo_enabled', true );
        if ( '' == $geo_on )
            $geo_on = true;

        if ( $geo_on ) {
            $geo_latitude = get_post_meta( get_the_ID(), 'geo_latitude', true );
            $geo_longitude = get_post_meta( get_the_ID(), 'geo_longitude', true );
            $geo_address = get_post_meta( get_the_ID(), 'weever_map_address', true ) ? get_post_meta( get_the_ID(), 'weever_map_address', true ) : get_post_meta( get_the_ID(), 'geo_address', true );

            $jsonHtml->geo[0]['latitude'] = $geo_latitude;
            $jsonHtml->geo[0]['longitude'] = $geo_longitude;
            $jsonHtml->geo[0]['altitude'] = '';
            $jsonHtml->geo[0]['address'] = $geo_address;
            $jsonHtml->geo[0]['label'] = '';
            $jsonHtml->geo[0]['marker'] = get_post_meta( get_the_ID(), 'weever_map_marker', true );
            $jsonHtml->geo[0]['kml'] = get_post_meta( get_the_ID(), 'weever_kml', true );
        }
    }

    if ( ! isset( $jsonHtml->geo[0] ) and get_post_meta( get_the_ID(), '_wp_geo_latitude', true ) != '' and get_post_meta( get_the_ID(), '_wp_geo_longitude', true ) != '' ) {
        // WP Geo
        $jsonHtml->geo[0]['latitude'] = get_post_meta( get_the_ID(), '_wp_geo_latitude', true );
        $jsonHtml->geo[0]['longitude'] = get_post_meta( get_the_ID(), '_wp_geo_longitude', true );
        $jsonHtml->geo[0]['altitude'] = '';
        $jsonHtml->geo[0]['address'] = get_post_meta( get_the_ID(), 'weever_map_address', true );
        $jsonHtml->geo[0]['label'] = '';
        $jsonHtml->geo[0]['marker'] = get_post_meta( get_the_ID(), 'weever_map_marker', true );
        $jsonHtml->geo[0]['kml'] = get_post_meta( get_the_ID(), 'weever_kml', true );
    }

    if ( ! isset( $jsonHtml->geo[0] ) and get_post_meta( get_the_ID(), 'weever_kml', true ) != '' ) {
        // Just KML
        $jsonHtml->geo[0]['latitude'] = '';
        $jsonHtml->geo[0]['longitude'] = '';
        $jsonHtml->geo[0]['altitude'] = '';
        $jsonHtml->geo[0]['address'] = get_post_meta( get_the_ID(), 'weever_map_address', true );
        $jsonHtml->geo[0]['label'] = '';
        $jsonHtml->geo[0]['marker'] = get_post_meta( get_the_ID(), 'weever_map_marker', true );
        $jsonHtml->geo[0]['kml'] = get_post_meta( get_the_ID(), 'weever_kml', true );
    }
}


/* This method makes all external links TARGET="_BLANK"
 * Unless $in_there is set to true, in which case it ignores them.
 * Currently, $in_ther is set when within the class 'wx-enable-imglinks'
 */
function make_links_target_blank($node, $deep=0, $in_there=false) {
    if (count($node->attr)>0) {
        foreach($node->attr as $k=>$v) {
            if ( $k === 'class' and $v === 'wx-enable-imglinks' ) {
                $in_there = true;
            }
            else if ( $in_there == false and ($k === 'href' || $k === 'src') ) {

                // If the link is external (eg, if it starts with http), we 
                // add a TARGET="_BLANK". Internal links do not start with http.
                if ( strpos( $v, 'http' ) === 0 ) {
                    $node->attr[' target'] = "_BLANK";
                }
            }
        }
    }

    foreach($node->nodes as $c)
        make_links_target_blank($c, $deep+1, $in_there);
}

