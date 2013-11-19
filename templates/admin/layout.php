<!-- start: container -->
<div id="appbuilder">
<div id="interface" class="platform">

<!-- primary navigation -->
<nav class="top-bar" style="">
    <ul class="title-area">
        <!-- Title Area -->
        <li class="name"><h1>appBuilder for Wordpress</h1></li>
        <!-- Remove the class "menu-icon" to get rid of menu icon. Take out "menu" to just have icon alone -->
        <li class="toggle-topbar menu-icon"><a href=""><span>Menu</span></a></li>
    </ul>

    <section class="top-bar-section">
        <ul class="right">
            <!-- remove dividers for cms platforms -->
            <li class=""><a href="#" data-reveal-id="wx-account">subscription key</a></li>
            <!--
            <li class="divider"></li>
            <!-- -->
            <li class=""><a target="_blank" href="http://weeverapps.com/login">visitor statistics</a></li>
            <!--
            <li class="divider"></li>
            <!-- -->
            <li class=""><a target="_blank" href="http://weeverapps.com/login/">my account</a></li>
            <!--
            <li class="divider"></li>
            <!-- -->
        </ul>
    </section>
</nav>

<!-- upgrade prompts -->
<div id="account-expiration-warning" class="row" style="display: none;">
    <div data-alert class="alert-box secondary">
        Your free trial app expires <span id="expiry-days">in ?? days</span>
        <a target="_blank" href="http://weeverapps.com/pricing">View plans and pricing</a>.
        <a href="#" class="close">&times;</a>
    </div>
</div>

<div id="account-expired" class="row" style="display: none;">
    <div data-alert class="alert-box alert">
        Your app subscription is expired.
        <a target="_blank" href="http://weeverapps.com/pricing">View plans and pricing</a>.
        <a href="#" class="close">&times;</a>
    </div>
</div>

<!-- wp errors and messages -->
<?php $errors = get_settings_errors(); ?>
<?php if (is_array($errors)): ?>
    <?php foreach($errors as $error): ?>
        <!-- <div id="message" class="<?php echo $error['type']; ?>"><p><strong><?php echo __($error['message'], 'weever'); ?></strong></p></div> -->
        <div class="row">
                <div data-alert class="alert-box alert">
                    <?php echo __($error['message'], 'weever'); ?>
                    <!-- <a href="#" data-reveal-id="myModal">View plans and pricing</a>. -->
                    <a href="#" class="close">&times;</a>
                </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<!-- start user interface -->
<div class="row">
    <div class="large-7 columns wx-column-inline">

        <div class="section-container auto" data-section>
            <!-- tabs -->
            <section class="active">
                <p class="title" data-section-title><a href="#panel1">1. Build <span>&rarr;</span></a></p>
                <div class="content" data-section-content>
                    <?php require( dirname(__FILE__) . '/tabs/list.new.php' ); ?>
                </div>
            </section>
            <section>
                <p class="title" id="edit-title" data-section-title><a href="#panel2">2. Edit <span>&rarr;</span></a></p>
                <div class="content" data-section-content>
                    <?php require( dirname(__FILE__) . '/tabs/edit.new.php' ); ?>
                </div>
            </section>
            <section>
                <p class="title" data-section-title><a href="#panel3">3. Style <span>&rarr;</span></a></p>
                <div class="content" data-section-content>
                    <?php require( dirname(__FILE__) . '/tabs/style.new.php' ); ?>
                </div>
            </section>
            <section>
                <p class="title" data-section-title><a href="#panel4">4. Launch</a></p>
                <div class="content" data-section-content>
                    <?php require( dirname(__FILE__) . '/tabs/launch.new.php' ); ?>
                </div>
            </section>
        </div>
    </div>

    <!-- Phone preview -->
    <div id="wx-preview-container" class="large-5 columns wx-column-inline">

        <!-- start: plugin only - mobile redirect status -->
        <!-- -->
        <div class="row">
            <div class="small-8 columns" style="padding-left: 0; padding-right: 0;">
                <h6 class="subheader wx-switch-labelright">
                    <img class="wx-load-spinner" src="<?php echo WEEVER_PLUGIN_URL; ?>static/img/loading.gif" alt="Loading" id="status-loading" />
                    &nbsp;app is <span id="appStatus"><b><?php if ($weeverapp->app_enabled) { echo 'on'; } else { echo 'off'; } ?>line</b></span> for mobile visitors.
                </h6>
            </div>
            <div class="small-4 columns" style="padding-right: 0;">
                <div class="switch">
                    <input id="off" name="switch-x" type="radio" <?php if (!$weeverapp->app_enabled) { echo 'checked'; } ?>>
                    <label for="off" onclick="">offline</label>
                    <input id="on" name="switch-x" type="radio"  <?php if ($weeverapp->app_enabled) { echo 'checked'; } ?>>
                    <label for="on" onclick="">online</label>
                    <span></span>
                </div>
            </div>
        </div>

        <!-- start: app preview -->
        <div class="row">
            <div class="large-12 columns">
                <div id="wx-phone-bg">
                    <!-- start: development iframe -->
                    <!--
                    <iframe src="http://www.wga.hu/art/l/leonardo/04/2scapigl.jpg" height="568" width="320" frameborder="0" scrolling="no" name="iframe-preview" seamless></iframe>
                    <!-- endof: development iframe -->
                    <!-- start: production iframe -->
                    <!-- -->
                    <?php if ( ! isset( $tab_found ) or $tab_found ): ?>
                        <div id="preview-app-dialog-webkit" style="">
                            <iframe id="preview-app-dialog-frame" rel="<?php echo esc_url( WeeverConst::LIVE_SERVER . 'app/' . $weeverapp->primary_domain ); ?>?simphone=1&cache_manifest=false" height="568" width="320" frameborder="0" scrolling="no" name="iframe-preview" seamless></iframe>
                            <div id="iframe-loading" style="display: none;margin: 0 auto;width: 300px;height: 568px;border: 1px #222 solid;box-sizing: content-box;padding: 0 10px;">
                                <div style="padding-top: 3.75em; text-align: center">
                                    <p>
                                        <img src="<?php echo WEEVER_PLUGIN_URL; ?>static/img/loading.gif" /> applying changes
                                    </p>
                                </div>
                            </div>
                            <div id="preview-app-dialog-no-webkit" style="display: none;margin: 0 auto;width: 300px;height: 568px;border: 1px #222 solid;box-sizing: content-box;padding: 0 10px;">
                                <div style="padding-top: 3.75em; text-align: center">
                                    <p>Scan this QR Code with a touch-based smart phone to preview your app!</p>
                                    <p><img src="http://qr.weeverapps.com/?site=<?php echo $weeverapp->primary_domain; ?>"  class="wx-qr-imgprev" /></p>
                                    <p>To view a preview of your app while you build, open this page with the <a target="_blank" href="http://google.com/chrome/">Google Chrome</a> or <a href="http://www.apple.com/safari/">Safari</a> web browser.</p>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div id="preview-app-dialog-no-tabs-welcome">
                            <p>Welcome to appBuilder.  A preview of your app will appear here as soon as you add content.</p>
                        </div>
                    <?php endif; ?>
                    <!-- -->
                    <!-- endof: production iframe -->
                </div>
            </div>
        </div>

        <br>

        <div class="row">
            <div class="small-10 small-centered large-12 large-uncentered columns">
                <button class="large button secondary expand radius" id="refresh_preview"><span class="appbuilder-icon icon-refresh"></span> Refresh Preview</button>
            </div>
        </div>

        <br>

    </div>
</div>
<!-- end - user interface -->

<!-- spacer -->


<!-- footer -->

<div class="wx-footer row" id="wx-footer-top">
    <!-- markup is different between full width and container-wrapped platforms -->
    <div class="large-12 columns">
        <div class="large-4 columns">
            <p class="wx-footer-icon"><span class="appbuilder-icon icon-earth"></span></p>
            <h5>appBuilder&trade;</h5>
            <p>appBuilder is made with care by <b><a target="_blank" href="http://weeverapps.com">Weever Apps</a></b>, a company in Hamilton, Canada.  appBuilder is used in over 60 countries and 16 languages.</p>
            <p>Weever Apps clients include both small businesses and enterprise brands, like Allergan, Habitat for Humanity, and Microsoft.</p>
        </div>
        <div class="large-8 columns">
            <div class="row">
                <div class="large-4 columns">
                    <p class="wx-footer-icon"><span class="appbuilder-icon icon-rocket"></span></p>
                    <h5 class="subheader">Custom Features</h5>
                    <p><b><a target="_blank" href="http://weeverapps.com/enterprise">weeverapps.com/enterprise</a></b></p>
                    <p>Our professional services department can help you take your project from idea to launch.</p>
                </div>
                <div class="large-4 columns">
                    <p class="wx-footer-icon"><span class="appbuilder-icon icon-support"></span></p>
                    <h5 class="subheader">Support</h5>
                    <p><b><a target="_blank" href="http://support.weeverapps.com">support.weeverapps.com</a></b></p>
                    <p>Report a problem or review our support knowledgebase for tips, tricks and tutorials.</p>
                </div>
                <div class="large-4 columns">
                    <p class="wx-footer-icon"><span class="appbuilder-icon icon-envelope"></span></p>
                    <h5 class="subheader">Get Updates</h5>
                    <p><b><a target="_blank" href="http://weeverapps.com/newsletter/">subscribe now</a></b></p>
                    <p>Get notified of new app features via our monthly newsletter and twitter stream.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="wx-footer row" id="wx-footer-bottom">
    <!-- markup is different between full width and container-wrapped platforms -->
    <div class="large-12 columns">
        <div class="large-4 push-8 small-12 columns">
            <div class="wx-social-links">
                <a target="_blank" href="http://www.twitter.com/weeverapps"  class="wx-footer-bottom-icon"><span class="appbuilder-icon icon-twitter-bird-alt1"></span></a>
                &nbsp;
                <a target="_blank" href="http://www.facebook.com/weeverapps" class="wx-footer-bottom-icon"><span class="appbuilder-icon icon-facebook-alt1"></span></a>
                &nbsp;
                <a target="_blank" href="http://weeverapps.com/contact" class="wx-footer-bottom-icon"><span class="appbuilder-icon icon-chat-bubble-alt1"></span></a>
            </div>
        </div>
        <div class="large-8 pull-4 small-12 columns">
            <div class="wx-inline-links">
                <a href="http://weeverapps.com/">Weever Apps</a>
                <a href="http://weeverapps.com/blog/">Blog</a>
                <a href="http://weeverapps.com/newsletter/">Newsletter</a>
                <a href="http://weeverapps.com/contact/">Contact</a>
            </div>
            <p class="copyright">&copy; 2011 - 2013 Weever Apps Inc. All rights reserved.</p>
        </div>
    </div>
</div>

<!-- subscription key -->
<div class="reveal-modal" id="wx-account">
    <?php require( dirname(__FILE__) . '/tabs/account.php' ); ?>
    <a class="close-reveal-modal">&times;</a>
</div>


<ol class="joyride-list" data-joyride>
    <li data-id="edit-title" data-text="Got it, thanks!">
        <p><b>Congratulations,</b></p>
        <p>You have added a first new feature to your app. Nice job!</p>
        <p>Use the <b>&ldquo;Edit&rdquo;</b> area to change the icon and label for this feature or to remove it from your app at any time.</p>
    </li>
</ol>

<!-- endof: container -->
</div>
</div>

<script type="text/javascript">
    var wx = wx || {};
    wx.pluginUrl = "<?php echo str_replace('templates/admin/', '', plugin_dir_url( __FILE__ )); /* This is in the templates/admin folder, so remove that from the path. */ ?>";
    wx.navIconDir = "<?php echo WEEVER_PLUGIN_URL; ?>static/img/";
    wx.baseExtensionUrl = "<?php echo admin_url( 'admin.php?page=weever-list' ); ?>";
    wx.siteKey = "<?php echo $weeverapp->site_key; ?>";
    wx.apiUrl = "<?php echo WeeverHelper::get_root_weever_api_url(); ?>";
    wx.poll = true;

    jQuery( document ).ready( function() {

        jQuery(document).foundation();
        doPoll();

        <?php if ( isset( $_GET['page'] ) and basename( $_GET['page'] ) == 'weever-account' ) { ?>
        jQuery('#wx-account').foundation('reveal', 'open');
        <?php } ?>
    } );

    var buildNum = '';
    function doPoll() {
        if ( wx.poll ) {
            wx.getText('_metadata/get_build_version', function(data) {
                if (data != buildNum) {
                    buildNum = data;
                    console.log( 'New build: ' + buildNum );
                    wx.refreshAppPreview();
                    wx.poll = false;
                }
                setTimeout(doPoll, 1000);
            });
        } else {
            // Check back later.
            setTimeout(doPoll, 1000);
        }
    }
</script>

<input type="hidden" id="nonce" name="nonce" value="<?php echo wp_create_nonce( 'weever-list-js' ); ?>" />
<input type="hidden" name="site_key" id="wx-site-key" value="<?php echo $weeverapp->site_key; ?>" />
