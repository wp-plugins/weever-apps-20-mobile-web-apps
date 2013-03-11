<?php
try {
    // Get the listing of icons and cache the result
    $icons = get_transient( 'weever_icon_listing' );

    if ( false === $icons ) {
        $icons = WeeverHelper::send_to_weever_server( 'icons/get_icons' );
        set_transient( 'weever_icon_listing', $icons, ( 60 * 60 * 24 ) );
    }
} catch ( Exception $e ) {
    $icons = array();
}
?>

<div class="wx-icon-picker-container">
    <img src="" class="wx-icon-picker-preview" />
    
    <select class="wx-icon-picker">
        <?php foreach ( $icons->icons as $icon ): ?>
        <option value="<?php echo $icon->id; ?>"><?php echo esc_html($icon->name); ?></option>
        <?php endforeach; ?>
    </select>    
</div>
