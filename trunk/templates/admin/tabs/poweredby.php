<div style="margin:-20px 0 -5px 0;" class="wx-ab-submenucontainer">

    <div style="min-width: 880px;">

	<a style="padding-left:20px; width:180px;" href="/wp-admin/admin.php?page=weever-list" class="wx-ab-submenulink wx-ab-abovethedock">
    <img src="/wp-content/plugins/weever-server/images/icons-library/plus_48.png" class="ab-submenuicon" />
    Features and Content</a>
    
    <a href="/wp-admin/admin.php?page=weever-theme" style="width:180px;" class="wx-ab-submenulink wx-ab-abovethedock">
    <img src="/wp-content/plugins/weever-server/images/icons-library/wand_48.png" class="ab-submenuicon" />Design & Branding</a>
    
    <a href="/wp-admin/admin.php?page=weever-poweredby" style="" class="wx-ab-submenulink wx-ab-abovethedock wx-ab-submenulink-rowcurrent">
    <img src="/wp-content/plugins/weever-server/images/icons-library/network_48.png" class="ab-submenuicon" />Powered by...</a>
    
    </div>
    
</div>


<!-- Remove Weever Apps Branding   -->
<!-- ***************************** -->

<form action='' method='post' name='adminForm' id='adminForm'>


<!-- Start: Add a Left Margin of 20px -->
<div class="wxui-stdContainer">


	<div id="wx-submitcontainer">
	        <input id="wx-button-submit" class="wxui-btn orange small radius3" name="submit" type="submit" value="<?php _e( 'Save Changes', 'weever' ); ?>" />
	</div>


	<?php wp_nonce_field( 'weever_settings', 'weever_settings_nonce' ); ?>

<div id="tabs-1">
        	
				<div>
                	<fieldset class="adminForm">
                		<legend><?php echo __( 'Remove Weever Apps Branding', 'weever' ); ?></legend>
                	
                	<p style="max-width: 61.8%;">Modify the 'Powered by Weever Apps' message that visitors see when the app launches.</p>
                    <p style="max-width: 61.8%;">Note: This feature is only available for <a target="_blank" style="text-decoration:underline;" href="http://weeverapps.com/pricing">Premium & Reseller level apps.</a></p>

				

                	<table class="admintable">

                	

					<tr>
					<td style="vertical-align:top;" class="key hasTip" title="<?php echo __( 'Add custom text or HTML to appear as the app loads.  If the field is left blank, the load spinner will not appear.', 'weever' ); ?>"><?php echo __( 'Loading Spinner Text', 'weever' ); ?></td>
					<td><textarea type="textbox" name="loadspinner" id="wx-load-spinner" placeholder="<?php echo __( 'Powered By...', 'weever' ); ?>"><?php echo htmlspecialchars($weeverapp->loadspinner); ?></textarea> </td>	
					</tr>	

                	</table>

                	
            	</fieldset>
                	
                	
                </div>
                
                
             </div>
             
             
                     
<!-- Domain Mapping   -->
<!-- ***************************** -->        
        
        <div id="tabs-2">
        	
				<div>
                	<fieldset class="adminForm">
                		<legend><?php echo __( 'Use a Custom Web Address', 'weever' ); ?></legend>
                	
                	<p style="max-width: 61.8%;">Set a custom web address (domain name) for your app like mynewapp.com (or) app.mywebsite.com</p>

                	<table class="admintable">

                	<tr>
                	<td class="key hasTip" title="<?php echo __( "Use this to set up app.yourname.com or yourappname.com", 'weever' ); ?>"><?php echo __( 'Domain Mapping', 'weever' ); ?></td>
                	<td><input type="textbox" name="domain"  value="<?php echo $weeverapp->domain; ?>" id="wx-domain-map-input" placeholder="app.yourdomain.com" /> </td>
                	</tr>

					
                	</table>
                    <p>Important: To use a custom web address (also known as a domain name) you must own the domain name in question and point it towards your app.  Please see our <a style="text-decoration: underline" target=-"_blank" href="http://weeverapps.zendesk.com/entries/20394158-how-do-i-use-my-own-domain-name-web-site-address-url-with-weever-apps">technical instructions on using custom domains</a> and allow up to 24 hours for the process to complete.</p>
                	
            	</fieldset>
                	
                	
                </div>
                
                
             </div>


<!-- Language Options              -->
<!-- ***************************** -->

    	<div id="tabs-3">
        	<div>
        		
            	<fieldset class='adminForm'>
                	<legend><?php echo __('Language Options'); ?></legend>

                	<table class="admintable">
                    	<tr>
                        	<td class="key hasTip" title="<?php echo __( "Localize your app language and units", 'weever' ); ?>"><?php echo __( 'App Localization &amp; Language', 'weever' ); ?></td>
                        	<td>
                        		<select name="local">
                        			<?php foreach ($weeverapp->locales as $local => $description): ?>
                        			<option value="<?php echo $local; ?>" <?php if ( $local == $weeverapp->local or ( ! $weeverapp->local and $local == 'en-CA' ) ) echo 'SELECTED'; ?>><?php echo esc_html($description); ?></option>
                        			<?php endforeach; ?>
                        		</select>
                        	</td>
                    	</tr>
                	</table>
                                    	<p style="max-width: 61.8%;">Choose the language used in the app interface (back buttons and other elements).<br/>
                                        Localization also affects whether maps show distance in miles or kilometers.</p>

                </fieldset>

               
        	</div>
		</div>
      </form>
      
          <!-- End: Add a Left Margin of 20px -->
    </div>