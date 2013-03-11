<script type="text/javascript">
    jQuery(document).ready(function() {
        // Preview code
        setTimeout(function(){
            if (jQuery.browser.webkit) {
                jQuery('#preview-app-dialog-no-webkit').hide();
                jQuery('#preview-app-dialog-frame').attr('src', jQuery('#preview-app-dialog-frame').attr('rel'));
                jQuery('#preview-app-dialog-webkit').show();
                // jQuery('#preview-app-dialog-webkit').dialog('option', 'width', 320);
                //jQuery('#preview-app-dialog-webkit').dialog('open');
            } else if (jQuery.browser.webkit == undefined || jQuery.browser.webkit == false) {
                jQuery('#preview-app-dialog-no-webkit').show();
                //jQuery('#preview-app-dialog-no-webkit').dialog('open');
            }
        }, 300);
    });
</script>

<div id="container380">

    <div id="preview-bg" class="">
        <div id="preview-app">
            <?php if ( ! isset( $tab_found ) or $tab_found ): ?>
            <div id="preview-app-dialog-webkit" style="">
                <iframe scrolling="no" class="" frameborder="0" id="preview-app-dialog-frame" width="320" height="480" rel="<?php echo esc_url( WeeverConst::LIVE_SERVER . 'app/' . $weeverapp->primary_domain ); ?>?simphone=1&cache_manifest=false"></iframe>
            </div>
            <div id="preview-app-dialog-no-webkit" style="display:none;">
                <strong>Scan This Code</strong> using a QR code reader on a touch-based smart phone to preview your app!
                <p><img src="<?php echo $weeverapp->qr_code_private; ?>"  class="wx-qr-imgprev" /></p>
                <p>Want to preview your app right from the browser?  Use a WebKit browser such as <a href="http://support.google.com/chrome/bin/answer.py?hl=en&answer=95346">Google Chrome</a> or <a href="http://www.apple.com/safari/">Safari</a></p>
            </div>
            <?php else: ?>
            <div id="preview-app-dialog-no-tabs-welcome">
                <strong>Welcome to appBuilder<span style="position: relative; top: -.5em; font-size: .5em; font-weight: normal;">&trade;</span></strong> <span style="display: block; font-size: 14px; clear: both; line-height: 1.5em; margin: 1em;">Your app will preview here as soon as you add a first feature!</span>
                <span style="display: block; font-size: 14px; clear: both; line-height: 1.5em; margin: 1em;"><strong>First time building an app?</strong><br/><br/>Try adding a page to your app via the buttons to the left.<br/><br/>When you're done adding features, go to <strong>Design</strong> and set your color styles!</span>
            </div>
            <?php endif; ?>
        </div>
    </div>
<?php /*
    <div id="preview-choices">
        <ol>
            <li><a id="preview-iphone" class="active">iPhone</a></li>
            <li><a id="preview-android" class="">Android</a></li>
            <li><a id="preview-blackberry" class="">BlackBerry</a></li>
            <li class="coming-soon"><a id="preview-windows" class="">Windows Phone</a></li>
            <li class="coming-soon"><a id="preview-tablets">Tablets</a></li>
        </ol>
    </div>
*/ ?>
</div>
