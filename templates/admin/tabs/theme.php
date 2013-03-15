

<!-- Cropper dialog -->
<div id="wx-jcrop-dialog" class="wx-jquery-dialog wx-hide" xmlns="http://www.w3.org/1999/html">
	<img src="" id="wx-jcrop-dialog-img" />
	<div id="wx-jcrop-dialog-loading" style="display: none;"><img src="<?php echo WEEVER_PLUGIN_URL; ?>static/images/loading.gif" /> Saving...</div>
</div>


<div class="wx-add-ui">
    <form action="" enctype="multipart/form-data" method="post" id="themeAdminForm">
        <?php wp_nonce_field( 'weever_settings', 'weever_settings_nonce' ); ?>

        <div id="tabs">
        	<ul id="listTabsSortable" style="<?php if ( ! apply_filters( 'weever_list_show_wordpress_content', true ) ) echo 'display:none;'; ?>">
        		<li><a href="#tabs-1"><?php _e( 'Logo Area &amp; App Colours', 'weever' ); ?></a></li>
                <li><a href="#tabs-2"><?php _e( 'Launch Screens', 'weever' ); ?></a></li>
                <li><a href="#tabs-3"><?php _e( 'Home Screen Icon', 'weever' ); ?></a></li>
                <li><a href="#tabs-4"><?php _e( 'Branding &amp; Language', 'weever' ); ?></a></li>
                <?php if ( apply_filters( 'weever_list_show_wordpress_content', true ) ): ?>
                    <li><a href="#tabs-5"><?php _e( 'Advanced (CSS)', 'weever' ); ?></a></li>
                <?php endif; ?>
        	</ul>


<!-- Start: Add a Left Margin of 20px -->
<div class="wxui-stdContainer">

<!-- Logo and Launch Screens       -->
<!-- ***************************** -->

        	<div id="tabs-1">

            	<div>
                	<fieldset class='adminForm'>
                    <h2 class="wxuia-fieldTitle"><?php echo __( 'Logo Area Settings', 'weever' ); ?>
                    	<span class="wx-inputContainer">
                    	<input id="wx-button-submit" class="wxuia-button" style="float:right" name="submit" type="submit" value="<?php _e( 'Save Changes', 'weever' ); ?>" />
                        </span>
                    </h2>
					<input type="hidden" name="template" value="sencha" />
                	<table class="admintable">
                    	<tr>
                        	<td class="key hasTip" title="<?php echo __( 'Choose to display a text, image or custom HTML logo in the app', 'weever' ); ?>"><?php echo __( 'Use <strong>image or text</strong> for the App Logo?', 'weever' ); ?></td>
                        	<td>
                            	<select name="titlebarSource" class="wx-220-select required">
                                	<option value="text" <?php echo ($weeverapp->theme->titlebarSource == 'text' ? "selected='selected'":""); ?>><?php echo __( 'Use a Text Title', 'weever' ); ?> ("<?php echo strip_tags($weeverapp->titlebar_title); ?>")</option>
                                	<option value="image" <?php echo ($weeverapp->theme->titlebarSource == 'image' ? "selected='selected'":""); ?>><?php echo __( 'Use An Image (Upload Below)', 'weever' ); ?></option>
                                	<option value="html" <?php echo ($weeverapp->theme->titlebarSource == 'html' ? "selected='selected'":""); ?>><?php echo __( 'Use Custom CSS and HTML', 'weever' ); ?></option>
                            	</select>
                        	</td>
                    	</tr>
                    	<tr>
                        	<td class="key hasTip" title="<?php echo __( 'Places your choice of text in the logo area.', 'weever' ); ?>"><?php echo __( 'If using text, please specify the logo text:' ); ?></td>
                        	<td><input type="text" name="titlebar_title" maxlength="35" style="width:220px;" value="<?php echo htmlentities($weeverapp->titlebar_title, ENT_QUOTES, "UTF-8"); ?>" class="required" /></td>
                    	</tr>
                         <tr>
                        <td class="key hasTip">
                        <?php echo __( 'If using an image, please upload an image file<span style="display: block; clear:both; color: #bbb;">Optimal size is 600 x 88px</span>', 'weever' ); ?>
                        </td>
                        <td>
                        	<div id="wx-file-uploader-titlebar_logo_live" rel="titlebar_logo_live" ref="wx-titlebar_logo_live" img_width="600" img_height="88" class="wx-theme-file-uploader">
							    <noscript>
							        <p>Please enable JavaScript to use file uploader.</p>
							        <!-- or put a simple form for upload here -->
							        <img src="" id="wx-file-load" />
							    </noscript>
							</div>
						<input type="hidden" style="float:left;" class="wx-theme-input" name="titlebar_logo_live" size="22" />
						</td>
                        </tr>
                    	 <tr>
                        <td colspan=2" class="key hasTip" style="">
                        <?php /* <a style="float:left;" href='<?php echo $weeverapp->theme->titlebar_logo_live; ?>?nocache=<?php echo microtime(); ?>' class='popup' rel='{handler: "iframe", size:  { x: 600, y: 64}}'> */ ?>
                        <img id="wx-titlebar_logo_live" class="wx-theme-image" style="max-width:300px; margin-top:1em;" src="<?php echo $weeverapp->theme->titlebar_logo_live; ?>?nocache=<?php echo microtime(); ?>" />
                        <?php /* </a> */ ?>
						</td>
                        </tr>
                       
				
			

                	</table>
                	</fieldset>                	
                </div>

            
    	<!-- Start Left Column Container for Color Pickers -->
    	<div>
        	
				<div>
                	<fieldset class="DupadminForm">
                    <h2 class="wxuia-fieldTitle"><?php echo __( 'Logo Area Colours', 'weever' ); ?>
                    	<span class="wx-inputContainer">
                    	<input id="wx-button-submit" class="wxuia-button" style="float:right" name="submit" type="submit" value="<?php _e( 'Save Changes', 'weever' ); ?>" />
                        </span>
                    </h2>
                	<table class="admintable colortable" id="colortable-logo">
						<tr>
							<th><?php echo __( 'Logo Area Background Colour', 'weever' ); ?></th>
							<th><?php echo __( 'Logo Area Text Colour', 'weever' ); ?></th>
						</tr>
						<tr>
							<td>
								<input type="text" class="hexacolorinput" id="main_titlebar_color" name="main_titlebar_color" value="<?php echo get_option( 'weever_main_titlebar_color', '#ffffff'); ?>" />
								<div id="main_titlebar_colorpicker"></div>
							</td>
							<td>
								<input type="text" class="hexacolorinput" id="main_titlebar_text_color" name="main_titlebar_text_color" value="<?php echo get_option( 'weever_main_titlebar_text_color', '#000000'); ?>" />
								<div id="main_titlebar_text_colorpicker"></div>
							</td>
                          
						</tr>                	
                	</table>
                	
                	</fieldset>
                </div>
                
                
                <div>
                	<fieldset class="DupadminForm">
                    <h2 class="wxuia-fieldTitle"><?php echo __( 'Sub Tab Bar ( Just Below the Logo Area )', 'weever' ); ?>
                        <span class="wx-inputContainer">
                    	<input id="wx-button-submit" class="wxuia-button" style="float:right" name="submit" type="submit" value="<?php _e( 'Save Changes', 'weever' ); ?>" />
                        </span>
                    </h2>

                	<table class="admintable colortable">
						<tr>
							<th><?php echo __( 'Sub Tab Background Colour', 'weever' ); ?></th>
							<th><?php echo __( 'Sub Tab Text Colour', 'weever' ); ?></th>
						</tr>
						<tr>
							<td>
								<input type="text" class="hexacolorinput" id="subtab_color" name="subtab_color" value="<?php echo get_option( 'weever_subtab_color', '#bfbfbf'); ?>" />
								<div id="subtab_colorpicker"></div>
							</td>
							<td>
								<input type="text" class="hexacolorinput" id="subtab_text_color" name="subtab_text_color" value="<?php echo get_option( 'weever_subtab_text_color', '#ffffff'); ?>" />
								<div id="subtab_text_colorpicker"></div>
							</td>
                           
						</tr>                	
                	</table>
                	
                	</fieldset>
                    
                    
                    
                    
                    
                </div>
                
                
                


                

                
                
                
        	</div>
            
            
            
            
                

        	</div>
            

<!-- Launch Screens                -->
<!-- ****************************** -->

	            <div id="tabs-2">

    <div>
            		<fieldset class="DupadminForm">
                    <h2 class="wxuia-fieldTitle"><?php echo __( 'Launch Screens', 'weever' ); ?>
                        <span class="wx-inputContainer">
                    	<input id="wx-button-submit" class="wxuia-button" style="float:right" name="submit" type="submit" value="<?php _e( 'Save Changes', 'weever' ); ?>" />
                        </span>
                    </h2>
                    <div class="wxuia-20p">                      
                    <p>A launch screen appears momentarily when you app is first loading. This is a great opportunity to display your company logo!</p>

                        
                        
                              		<div class="wx-theme-screen">

                    		<div>
                        		<div class="wx-theme-caption"><?php echo __( 'Phone Launch Screen<span class="wx-caption">Size &ndash; 640 x 920px</span>', 'weever' ); ?></div>
									<div id="wx-file-uploader-phone_load_live" rel="phone_load_live" ref="wx-phone_load_live" img_width="640" img_height="920" class="wx-theme-file-uploader">
									    <noscript>
									        <p>Please enable JavaScript to use file uploader.</p>
									        <!-- or put a simple form for upload here -->
									        <img src="" id="wx-file-load" />
									    </noscript>
									</div>
                        		
                                        <input type="hidden" class="wx-theme-input" name="phone_load_live" size="13" />
                                        
                        		<?php /* <a href='<?php echo $weeverapp->theme->phone_load_live; ?>?nocache=<?php echo microtime(); ?>' class='popup' rel='{handler: "iframe", size:  { x: 640}}'> */ ?>
                        		<img id="wx-phone_load_live" class="wx-theme-image" src="<?php echo $weeverapp->theme->phone_load_live; ?>?nocache=<?php echo microtime(); ?>" />
                        		<?php /*</a> */ ?>
                    		</div>

                		</div>
                        
                        
                		<div class="wx-theme-screen">

                    		<div>
                        		<div class="wx-theme-caption"><?php echo __( 'Tablet Portrait<span class="wx-caption">Size &ndash; 1536 x 2008px</span>', 'weever' ); ?></div>
									<div id="wx-file-uploader-tablet_load_live" rel="tablet_load_live" ref="wx-tablet_load_live" img_width="1536" img_height="2008" class="wx-theme-file-uploader">
									    <noscript>
									        <p>Please enable JavaScript to use file uploader.</p>
									        <!-- or put a simple form for upload here -->
									        <img src="" id="wx-file-load" />
									    </noscript>
									</div>
                        		
                                        <input type="hidden" class="wx-theme-input" name="tablet_load_live" size="13" />
                        		<?php /* <a href='<?php echo $weeverapp->theme->tablet_load_live; ?>?nocache=<?php echo microtime(); ?>' class='popup' rel='{handler: "iframe", size:  { x: 920}}'> */ ?>
                        		<img id="wx-tablet_load_live" class="wx-theme-image" src="<?php echo $weeverapp->theme->tablet_load_live; ?>?nocache=<?php echo microtime(); ?>" />
                        		<?php /*?></a> */ ?>
                    		</div>

                		</div>


                		<div class="wx-theme-screen">

                    		<div>
                        		<div class="wx-theme-caption"><?php echo __( 'Tablet Landscape<span class="wx-caption">Size &ndash; 1496 x 2048px</span>', 'weever' ); ?></div>
                        		
									<div id="wx-file-uploader-tablet_landscape_load_live" rel="tablet_landscape_load_live" ref="wx-tablet_landscape_load_live" img_width="1496" img_height="2048" class="wx-theme-file-uploader">
									    <noscript>
									        <p>Please enable JavaScript to use file uploader.</p>
									        <!-- or put a simple form for upload here -->
									        <img src="" id="wx-file-load" />
									    </noscript>
									</div>
                        		                        		
                                        <input type="hidden" class="wx-theme-input" name="tablet_landscape_load_live" size="13" />
                                        
                        		<?php /*<a href='<?php echo $weeverapp->theme->tablet_landscape_load_live; ?>?nocache=<?php echo microtime(); ?>' class='popup' rel='{handler: "iframe", size:  { x: 920}}'> */ ?>
                        		<img id="wx-tablet_landscape_load_live" class="wx-theme-image" src="<?php echo $weeverapp->theme->tablet_landscape_load_live; ?>?nocache=<?php echo microtime(); ?>" />
                        		<?php /*</a>*/ ?>
                    		</div>

                		</div>

                    </div>

                		
                		

            		</fieldset>
        		</div>
                </div>


            
            
<!-- Home Screen Install Icon      -->
<!-- ***************************** -->
            
            
            <div id="tabs-3">
        	
				<div>
                	<fieldset class='adminForm'>
                    <h2 class="wxuia-fieldTitle"><?php echo __( 'Home Screen Icon', 'weever' ); ?>
                        <span class="wx-inputContainer">
                        	<input id="wx-button-submit" class="wxuia-button" style="float:right" name="submit" type="submit" value="<?php _e( 'Save Changes', 'weever' ); ?>" />
                        </span>
                    </h2>
					<input type="hidden" name="template" value="sencha" />
                	<table class="admintable">
                    
                    <tr>
                    
                    <td class="key hasTip">
                    	<?php /*?><a href='<?php echo $weeverapp->theme->icon_live; ?>?nocache=<?php echo microtime(); ?>' class='popup' rel='{handler: "iframe", size:  { x: 144, y: 144}}'>*/ ?>
                    	<img id="wx-icon_live" class="wx-theme-image radius25" style="margin-top:0;" src="<?php echo $weeverapp->theme->icon_live; ?>?nocache=<?php echo microtime(); ?>" />
                        <?php /*?></a> */ ?>
                    </td>
                    
                    <td class="key hasTip" style="vertical-align:top;">
                    <div class="wx-theme-caption"><?php echo __( 'App Installation Icon<br/><!--(144 x 144 pixel PNG)-->', 'weever' ); ?></div>
                    
									<div id="wx-file-uploader-tablet_landscape_load_live" rel="icon_live" ref="wx-icon_live" img_width="144" img_height="144" class="wx-theme-file-uploader">
									    <noscript>
									        <p>Please enable JavaScript to use file uploader.</p>
									        <!-- or put a simple form for upload here -->
									        <img src="" id="wx-file-load" />
									    </noscript>
									</div>
                        		
                    <input type="hidden" class="wx-theme-input" name="icon_live" size="13" />
                    </td>
                   
                    
                    </tr>

                    <tr>
                    <td style="vertical-align:top;" class="key hasTip"
                    title="<?php echo __( "This name is used for visitors who install your app to their homescreen, and appears below the app's icon.", 'weever' ); ?>">
						<input type="text" name="title" placeholder="Edit Name" maxlength="13" style="width:144px; text-align:center; color:#000; font-weight:bold; font-family: sans-serif; padding: 10px;" value="<?php echo htmlentities($weeverapp->title, ENT_QUOTES, "UTF-8"); ?>" class="required" /><br/>
					</td>
                    <td></td>
                    </tr>

                    
                    
                   
                	</table>
                    
                   
                
                	
                	</fieldset>                	
                </div>

            	<div>
            		<fieldset class="DupadminForm">
                    <h2 class="wxuia-fieldTitle"><?php echo __( 'Prompt Users to Install Your App', 'weever' ); ?>  
                        <span class="wx-inputContainer">
                    	<input id="wx-button-submit" class="wxuia-button" style="float:right" name="submit" type="submit" value="<?php _e( 'Save Changes', 'weever' ); ?>" />
                        </span>
                    </h2>             		
                        <table class="admintable">
                		
                		<tr>
                     		 <td class="key hasTip">
                    	     <select name="install_prompt" style="min-width: 72px;">
									<option value="0"><?php echo __( 'NO', 'weever' ); ?></option>
									<option value="1" <?php echo ($weeverapp->launch->install_prompt ? "selected='selected'":""); ?>><?php echo __( 'YES', 'weever' ); ?></option>
								</select>
						     <?php echo __( 'Prompt to Install?', 'weever' ); ?>
                           <p>Prompt new visitors to install the app to their phone or tablet home screen.<br><br><strong>Note:</strong> &nbsp; This feature is only available when:<br><br>
&nbsp; &nbsp; • The phone or tablet supports easy installation (and)<br>&nbsp; &nbsp; • The app is not already installed  or open in the browser</p>
                    </td>
                    
                       </tr>

                		</table>

            		</fieldset>
        		</div>
            </div>

            <div id="tabs-4">
                <fieldset class="adminForm">
                    <h2 class="wxuia-fieldTitle">
                        <?php echo __( 'Remove Weever Apps Branding', 'weever' ); ?>
                        <span class="wx-inputContainer">
                        	<input id="wx-button-submit" class="wxuia-button" style="float:right" name="submit" type="submit" value="<?php _e( 'Save Changes', 'weever' ); ?>" />
                        </span>
                    </h2>
                    <div class="wxuia-20p">
                        <p>Modify the 'Powered by Weever Apps' message that visitors see when the app launches.</p>
                        <?php if ( $weeverapp->tier != 4 ): ?>
                            <p>Note: This feature is only available for Premium & Reseller level apps.</p>
                            <p><a class="wx-add-new-content-button wxuia-button" href="http://weeverapps.com/new-app/?site_url=<?php echo urlencode($weeverapp->primary_domain); ?>&subscription_type=premium">Upgrade Your App</a></p>
                        <?php else: ?>
                            <table class="admintable">
                            <tr>
                                <td style="vertical-align:top;" class="key hasTip" title="<?php echo __( 'Add custom text or HTML to appear as the app loads.  If the field is left blank, the load spinner will not appear.', 'weever' ); ?>"><?php echo __( 'Loading Spinner Text', 'weever' ); ?></td>
                                <td><textarea type="textbox" name="loadspinner" id="wx-load-spinner" placeholder="<?php echo __( 'Powered By...', 'weever' ); ?>"><?php echo htmlspecialchars($weeverapp->loadspinner); ?></textarea> </td>
                            </tr>
                            </table>
                        <?php endif; ?>
                    </div>
                </fieldset>

                <fieldset class="adminForm">
                    <h2 class="wxuia-fieldTitle"><?php echo __( 'Use a Custom Web Address', 'weever' ); ?>
                        <span class="wx-inputContainer">
                        	<input id="wx-button-submit" class="wxuia-button" style="float:right" name="submit" type="submit" value="<?php _e( 'Save Changes', 'weever' ); ?>" />
                        </span>
                    </h2>
                    <div class="wxuia-20p">
                        <p>Set a custom web address (domain name) for your app like mynewapp.com (or) app.mywebsite.com</p>

                        <?php if ( $weeverapp->tier == 1 or $weeverapp->tier == '2.1' ): ?>
                            <p>Note: This feature is only available for Pro, Premium & Reseller level apps.</p>
                            <p><a class="wx-add-new-content-button wxuia-button" href="http://weeverapps.com/new-app/?site_url=<?php echo urlencode($weeverapp->primary_domain); ?>&subscription_type=pro">Upgrade Your App</a></p>
                        <?php else: ?>
                            <table class="admintable">
                            <tr>
                                <td class="key hasTip" title="<?php echo __( "Use this to set up app.yourname.com or yourappname.com", 'weever' ); ?>"><?php echo __( 'Domain Mapping', 'weever' ); ?></td>
                                <td><input type="textbox" name="domain"  value="<?php echo $weeverapp->domain; ?>" id="wx-domain-map-input" placeholder="app.yourdomain.com" /> </td>
                            </tr>
                            </table>
                            <p>Important: To use a custom web address (also known as a domain name) you must own the domain name in question and point it towards your app.  Please see our <a style="text-decoration: underline" target=-"_blank" href="http://weeverapps.zendesk.com/entries/20394158-how-do-i-use-my-own-domain-name-web-site-address-url-with-weever-apps">technical instructions on using custom domains</a> and allow up to 24 hours for the process to complete.</p>
                        <?php endif; ?>
                    </div>
                </fieldset>

                <fieldset class='adminForm'>
                    <h2 class="wxuia-fieldTitle">
                        <?php echo __( 'Language Options', 'weever' ); ?>
                        <span class="wx-inputContainer">
                        	<input id="wx-button-submit" class="wxuia-button" style="float:right" name="submit" type="submit" value="<?php _e( 'Save Changes', 'weever' ); ?>" />
                        </span>
                    </h2>

                    <table class="admintable">
                        <tr>
                            <td class="key hasTip" title="<?php echo __( "Localize your app language and units", 'weever' ); ?>"><?php echo __( 'App Localization &amp; Language', 'weever' ); ?></td>
                            <td>
                                <select name="local">
                                    <?php foreach ($weeverapp->locales as $local => $description): ?>
                                    <option value="<?php echo esc_html( $local ); ?>" <?php if ( $local == $weeverapp->local or ( ! $weeverapp->local and $local == 'en-CA' ) ) echo 'SELECTED'; ?>><?php echo esc_html($description); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                        </tr>
                    </table>
                </fieldset>
            </div>

            <?php if ( apply_filters( 'weever_list_show_wordpress_content', true ) ): ?>
                <div id="tabs-5">
                    <fieldset class="adminForm">
                        <h2 class="wxuia-fieldTitle">
                            <?php echo __( 'Advanced (CSS)', 'weever' ); ?>
                            <span class="wx-inputContainer">
                                <input id="wx-button-submit" class="wxuia-button" style="float:right" name="submit" type="submit" value="<?php _e( 'Save Changes', 'weever' ); ?>" />
                            </span>
                        </h2>
                        <div class="wxuia-20p">
                            <div>
                                Use the Web Inspector functionality in Google Chrome or Safari to determine the elements to apply the below css to.  You can also create a <strong>weever.css</strong> file in your current theme for ease of editing and to use in source code control.
                            </div>
                            <textarea style="width: 100%; height: 300px;" name="css"><?php echo esc_textarea( $weeverapp->theme->css ); ?></textarea>
                        </div>
                    </fieldset>
                    <fieldset class="adminForm">
                        <h2 class="wxuia-fieldTitle">
                            <?php echo __( 'Advanced Options', 'weever' ); ?>
                            <span class="wx-inputContainer">
                                <input id="wx-button-submit" class="wxuia-button" style="float:right" name="submit" type="submit" value="<?php _e( 'Save Changes', 'weever' ); ?>" />
                            </span>
                        </h2>
                        <div class="wxuia-20p">
                            <div>
                                <input type="checkbox" id="do_not_modify_links" name="do_not_modify_links"<?php echo ( get_option('weever_do_not_modify_links', false ) ? ' checked="checked"' : '' ); ?>' /> <label for="do_not_modify_links">Do not modify href links (normally adds full=1 to external links, etc)</label>
                            </div>
                            <div>
                                <input type="checkbox" id="remove_image_links" name="remove_image_links"<?php echo ( get_option('weever_remove_image_links', true ) ? ' checked="checked"' : '' ); ?>' /> <label for="remove_image_links">Remove links around images added by Wordpress</label>
                            </div>
                        </div>
                    </fieldset>
                </div>
            <?php endif; ?>
        <div id="tabs-999">
    </div>




        </div>
        
            <!-- START: iFrame Preview -->
        
				<div id="container380" style="">

				
	                <div id="preview-bg">
						<div id="preview-app">
					
							<div id="preview-app-dialog-webkit" style="display:none;">
								<iframe scrolling="no" frameborder="0" id="preview-app-dialog-frame" width="320" height="480" rel="<?php echo esc_url( WeeverConst::LIVE_SERVER . 'app/' . $weeverapp->primary_domain ); ?>?simphone=1&cache_manifest=false"></iframe>
							</div>
							<div id="preview-app-dialog-no-webkit" style="display:none;">
								<strong>Scan This Code</strong> using a QR code reader on a touch-based smart phone to preview your app!
								<p><img src="<?php echo $weeverapp->qr_code_private; ?>"  class="wx-qr-imgprev" /></p>

								<p>Want to preview your app right from the browser?  Use a WebKit browser such as <a href="http://support.google.com/chrome/bin/answer.py?hl=en&answer=95346">Google Chrome</a> or <a href="http://www.apple.com/safari/">Safari</a></p>
							</div>
						</div>
					</div>

				</div>
            
	        <!-- END: iFrame Preview -->

        
        

    </form>
    
    <!-- End: Add a Left Margin of 20px -->
    </div>
    
    
   
</div>
