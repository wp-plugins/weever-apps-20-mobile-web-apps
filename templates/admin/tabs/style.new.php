<!-- Cropper dialog -->
<div id="wx-jcrop-dialog" class="reveal-modal">

    <h2>Crop Image</h2>
    <p class="lead">Click and drag to crop the selected image or select "Done" now to use the full image without resizing.</p>

    <div class="row">

        <div class="large-8 columns" style="padding-left: 0;">
            <img src="" id="wx-jcrop-dialog-img" />
            <br>
        </div>

        <div class="large-4 columns">
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

    <button class="button secondary" id="finish-crop">Done</button>
    <div id="wx-jcrop-dialog-loading" style="display: none;"><img src="<?php echo WEEVER_PLUGIN_URL; ?>static/img/loading.gif" /> Saving...</div>

</div>


<div class="section-container auto section-subnav" data-section>
	<!-- Logo Area & Design -->
	<section id="logo_design">
		<p class="title" data-section-title>
			<a href="#"><?php echo __( 'logo design', 'weever' ); ?></a>
		</p> 
		<div class="content" data-section-content>
			<!-- logodesign.tpl.html -->
		</div>
	</section>
	<!-- Launch Screen -->
	<section id="launch_screen">
		<p class="title" data-section-title>
			<a href="#"><?php echo __( 'launch screens', 'weever' ); ?></a>
		</p>
		<div class="content" data-section-content>
			<!-- launchscreen.tpl.html -->
		</div>
	</section>

	<!-- Install Icon -->
	<section id="install_icon">
		<p class="title" data-section-title><a href="#"><?php echo __( 'installation', 'weever' ); ?></a></p>
		<div class="content" data-section-content>
			<!-- installicon.tpl.html -->
		</div>
	</section>

	<!-- Custom Branding -->
	<section id="custom_branding">
		<p class="title" data-section-title><a href="#"><?php echo __( 'custom branding', 'weever' ); ?></a></p>
		<div class="content" data-section-content>
			<!-- custombranding.tpl.html -->
		</div>
	</section>

	<!-- Advanced -->
	<section id="advanced">
		<p class="title" data-section-title><a href="#"><?php echo __( 'advanced', 'weever' ); ?></a></p>
		<div class="content" data-section-content>
			<!-- advanced.tpl.html -->
		</div>
	</section>
</div>