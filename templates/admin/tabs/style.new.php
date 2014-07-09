<!-- Cropper dialog -->
<div id="wx-jcrop-dialog" class="reveal-modal" data-reveal>

    <h2>Crop Image</h2>
    <p class="lead">Click and drag to crop the selected image or select "Done" now to use the full image without resizing.</p>

    <div class="row">

        <div class="medium-8 columns" style="padding-left: 0;">
            <img src="" id="wx-jcrop-dialog-img" />
            <br>
        </div>

        <div class="medium-4 columns">
            <p><b>Final image sizes:</b></p>

            <div class="row">
                <div class="small-6 columns">Logo</div>
                <div class="small-6 columns">320 x 88px</div>
            </div>

            <br>

            <div class="row">
                <div class="small-6 columns">Install icon</div>
                <div class="small-6 columns">144 x 144px</div>
            </div>

            <br>
            <br>

            <div class="row">
                <div class="small-12 columns"><b>Launch Screens</b></div>
            </div>

            <br>

            <div class="row">
                <div class="small-6 columns">Phone</div>
                <div class="small-6 columns">640 x 920px</div>
            </div>

            <br>

            <div class="row">
                <div class="small-6 columns">Tablet portrait</div>
                <div class="small-6 columns">1536 x 2008px</div>
            </div>

            <br>

            <div class="row">
                <div class="small-6 columns">Tablet landscape</div>
                <div class="small-6 columns">1496 x 2048px</div>
            </div>

            <br>
            <br>

            <p>Please upload all images in PNG or JPG format.</p>

        </div>

    </div>

    <button class="button secondary radius" id="finish-crop">Done</button>
    <div id="wx-jcrop-dialog-loading" style="display: none;"><img src="<?php echo WEEVER_PLUGIN_URL; ?>static/img/loading.gif" /> Saving...</div>

</div>

<!-- style subtabs -->
<ul class="tabs wx-subtabs" data-tab>
    <li class="tab-title active"><a href="#logo_design"><?php echo __( 'Logo Design', 'weever' ); ?></a></li>
    <li class="tab-title"><a href="#launch_screen"><?php echo __( 'Launch Screens', 'weever' ); ?></a></li>
    <li class="tab-title"><a href="#install_icon"><?php echo __( 'Installation', 'weever' ); ?></a></li>
    <li class="tab-title"><a href="#custom_branding"><?php echo __( 'Custom Branding', 'weever' ); ?></a></li>
    <li class="tab-title"><a href="#advanced"><?php echo __( 'Advanced', 'weever' ); ?></a></li>
</ul>
<div class="tabs-content">
    <div class="content active" id="logo_design">
        <p>test - a</p>
        <!-- logodesign.tpl.html -->
    </div>
    <div class="content" id="launch_screen">
        <p>test - b</p>
        <!-- launchscreen.tpl.html -->
    </div>
    <div class="content" id="install_icon">
        <p>test - c</p>
        <!-- installicon.tpl.html -->
    </div>
    <div class="content" id="custom_branding">
        <p>test - d</p>
        <!-- custombranding.tpl.html -->
    </div>
    <div class="content" id="advanced">
        <p>test - e</p>
        <!-- advanced.tpl.html -->
    </div>
</div>