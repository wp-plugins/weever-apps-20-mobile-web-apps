<!-- style subtabs -->
<ul class="tabs wx-subtabs" data-tab>
    <li class="tab-title active"><a href="#launch_share"><?php echo __( 'Share App Online', 'weever' ); ?></a></li>
    <li class="tab-title"><a href="#launch_publishing"><?php echo __( 'Submit To App Stores', 'weever' ); ?></a></li>
    <li class="tab-title"><a href="#launch_marketing"><?php echo __( 'Marketing Toolbox', 'weever' ); ?></a></li>
    <li class="tab-title"><a href="#launch_advanced"><?php echo __( 'Advanced', 'weever' ); ?></a></li>
</ul>
<div class="tabs-content">
    <div class="content active" id="launch_share">
        <!-- launch-share.tpl.html -->
    </div>
    <div class="content" id="launch_publishing">
        <!-- App Stores -->
        <p class="wx-ui-title">Publish to app stores <span class="label secondary"><?php echo __( 'add-on service', 'weever' ); ?></span></p>
        <p>When your app is ready we can submit it on your behalf to the leading app marketplaces. We offer discount pricing when submitting to multiple stores.</p>
        <ul class="small-block-grid-3 medium-block-grid-5">
            <li>
                <a target="_blank" class="wx-launch-publish" id="publish-app-itunes" href="http://weeverapps.com/addon-services"></a>
                <p>iTunes</p>
            </li>
            <li>
                <a target="_blank" class="wx-launch-publish" id="publish-app-google-play" href="http://weeverapps.com/addon-services"></a>
                <p>Play</p>
            </li>
            <li>
                <a target="_blank" class="wx-launch-publish" id="publish-app-windows-store" href="http://weeverapps.com/addon-services"></a>
                <p>Windows</p>
            </li>
            <li>
                <a target="_blank" class="wx-launch-publish" id="publish-app-blackberry-world" href="http://weeverapps.com/addon-services"></a>
                <p>BB 10+</p>
            </li>
        </ul>
        <a target="_blank" class="button success radius" href="http://weeverapps.com/addon-services">View plans and pricing</a>
        <br>
        <br>
        <!-- Push notifications -->
        <p class="wx-ui-title">Push Notifications <span class="label secondary">add-on service</span> <span class="label secondary">Hybrid Only</span></p>
        <p>Send &lsquo;push notifications&rsquo; to customers who have your hybrid app installed.</p>
        <p><a target="_blank" class="button success radius" href="http://weeverapps.com/addon-services">View plans and pricing</a></p>
        <br>
        <!-- NFC stickers -->
        <p class="wx-ui-title">NFC Stickers <span class="label secondary">add-on service</span</p>
        <p>Imagine tapping a poster with your phone and seeing an app launch instantly.  That technology is here today with NFC Stickers!</p>
        <p>NFC Stickers (aka 'tags') come with a sticker backing and are pre-programmed to go to your Mobile App URL.  NFC Stickers are compatible with Android, Windows 8 and Blackberry 10 devices.</p>
        <p><a target="_blank" class="button success radius" href="http://weeverapps.com/addon-services">View plans and pricing</a></p>
    </div>
    <div class="content" id="launch_marketing">
        <!-- Marketing Toolbox -->
        <p class="wx-ui-title">App QR Code</p>
        <p>Your app includes a set of QR Codes for print marketing use.  When a mobile user scans your QR code with a barcode reader like or Google Goggles (<a target="_blank" href="https://play.google.com/store/apps/details?id=com.google.android.apps.unveil">Android</a> | <a target="_blank" href="https://itunes.apple.com/us/app/google-mobile-app/id284815942">iTunes</a>)  or the <a target="_blank" href="http://www.i-nigma.mobi">i-nigma QR Reader</a>, your app launches instantly to their touch phone or tablet browser.</p>
        <a class="button success radius" href="#" data-reveal-id="qr-modal">View your QR Code</a>
        <div id="qr-modal" class="reveal-modal" data-reveal>
            <div class="row">
                <div class="medium-6 columns">
                    <h2>Your app QR Code</h2>
                    <p>Launch address for this QR Code:</p>
                    <p><?php echo $weeverapp->primary_domain ?></p>
                    <p><b>How do I scan this QR Code?</b></p>
                    <p>Install a barcode reader on your phone if one is not already pre-installed.  Try Google Goggles (<a target="_blank" href="https://play.google.com/store/apps/details?id=com.google.android.apps.unveil">Android</a>, <a target="_blank" href="https://itunes.apple.com/us/app/google-mobile-app/id284815942">iTunes</a>)  or the <a target="_blank" href="http://www.i-nigma.mobi">i-nigma QR Reader</a>.</p>
                </div>
                <div class="medium-6 columns">
                    <img src="//qr.weeverapps.com/?site=<?php echo $weeverapp->primary_domain; ?>&style=smooth&error=3&margin=1&size=20" />
                    <br>
                    <br>
                </div>
            </div>
            <div>
                <a class="button success radius" href="<?php plugins_url( 'print-qr.html', __FILE__ ); ?>?domain=<?php echo $weeverapp->primary_domain; ?>&style=smooth&error=3&margin=1&size=20" target="_BLANK">Print</a>
                <button onclick="jQuery('#qr-modal').foundation('reveal', 'close');">Done</button>
                <a class="close-reveal-modal">&#215;</a>
            </div>
        </div>
        <br>
        <br>
        <br>
        <p class="wx-ui-title">SMS Messages <span class="label secondary">add-on service</span></p>
        <p>Statistics indicate that SMS messages are ready by over 90% of recipients.  Weever Apps SMS Messaging service allows you to send targeted offers to your mobile audience.</p>
        <p><a target="_blank" class="button success radius" href="http://weeverapps.com/addon-services">View plans and pricing</a></p>
        <br>
        <br>
        <p class="wx-ui-title">NFC Stickers <span class="label secondary">add-on service</span></p>
        <p>Imagine tapping a poster with your phone and seeing an app launch instantly.  That technology is here today with NFC Stickers!</p>
        <p>NFC Stickers (aka 'tags') come with a sticker backing and are pre-programmed to go to your Mobile App URL.  Weever NFC Stickers are compatible with Android, Windows 8 and Blackberry 10 devices.</p>
        <p><a target="_blank" class="button success radius" href="http://weeverapps.com/addon-services">View plans and pricing</a></p>
        <br>
        <!-- Push notifications -->
        <p>Send &lsquo;push notifications&rsquo; to customers who have downloaded your hybrid app from the App Store&trade; Google Play&trade; Windows Store&trade; or Blackberry World&trade;.</p>
        <p><a target="_blank" class="button success radius" href="http://weeverapps.com/addon-services">View plans and pricing</a></p>
    </div>
    <div class="content" id="launch_advanced">
        <!-- Advanced -->
        <p class="wx-ui-title"><?php echo __( 'Mobile Redirect', 'weever' ); ?></p>
        <p><?php echo __( 'When your app is <b>online</b>, compatible mobile device visitors will see your app launch instantly when accessing your web site address.', 'weever' ); ?></p>
        <label class="" for="off" onclick=""><input id="off" name="switch-tablet" type="radio" <?php if (!$weeverapp->DetectTierWeeverTablets) { echo 'checked'; } ?>> When app is online, instant-launch for phones only</label>
        <label class="" for="on"  onclick=""><input id="on" name="switch-tablet" type="radio"  <?php if  ($weeverapp->DetectTierWeeverTablets) { echo 'checked'; } ?>> When app is online, instant-launch for phones <b>and tablets</b></label>
        <br>
        <br>
        <p class="wx-ui-title"><?php echo __( 'Compatible Devices', 'weever' ); ?></p>
        <p><?php echo __( 'Weever Apps support all popular touch phones and tablets including Apple, Android, Windows and Blackberry 10 touch devices.', 'weever' ); ?></p>
        <p><?php echo __( 'Please <a target="_blank" href="http://weeverapps.com/">check our web site</a> for a complete up-to-date list of supported devices and operating systems.', 'weever' ); ?></p>
        <br>
        <!-- Redirect Tool -->
        <p class="wx-ui-title"><?php echo __( 'Redirect Snippet', 'weever' ); ?></p>
        <p><?php echo __( 'For advanced users only. Modify the redirect snippet below and launch your app from <b>separate</b> third-party web sites.', 'weever' ); ?></p>

        <textarea style="height: 200px">&lt;!--======================================---&gt;
            &lt;!--IMPORTANT - MODIFY this REDIRECT CODE ---&gt;
            &lt;!--1. Change the URL to point to your app --&gt;
            &lt;!--2. Choose phones, tablets or both -------&gt;
            &lt;!--3. Paste somewhere in &lt;body&gt; tag --------&gt;
            &lt;!--======================================---&gt;

            &lt;script src="http://detect.weeverapp.com/mdetect.js"&gt;&lt;/script&gt;
            &lt;script&gt;

            /* STEP 1.  Change the URL to point to your app */
            /* Update the app URL below: */

            var appUrl = "http://myappname.weeverapps.com";

            /* STEP 2. Choose phones, tablets or both /*
            /* Uncomment which devices you want to show your app to: phones only, tablets only, or both (default).  Two of the three device options below should always start with a double-forward slash (//) */

            //var deviceForward = isTierWeeverSmartphone;
            //var deviceForward = isTierWeeverTablet;
            var deviceForward = isTierWeeverAny;

            var forwardWeever = function() {

            if(deviceForward) {

            window.location.href = appUrl;

            }

            }();

            &lt;/script&gt;

            &lt;!--==============--&gt;
            &lt;!--END EMBED CODE--&gt;
            &lt;!--==============--&gt;
        </textarea>
        <div class="panel">
            <p><b>Where do I put this code?</b></p>
            <p>Generally this snippet can be placed just before the closing <b>/body</b> tag in a template or theme file, or pasted into an input area where you would normally add analytics tracking code.</p>
        </div>
    </div>
</div>