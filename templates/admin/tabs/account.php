<div class="wx-add-ui">
    <form action="" method="post">


		<?php wp_nonce_field( 'weever_settings', 'weever_settings_nonce' ); ?>

        <div id="tabs">
        	<ul id="listTabsSortable">
        		<li><a href="#tabs-1"><?php _e( 'Account Information', 'weever' ); ?></a></li>
        		<li><a href="#tabs-2"><?php _e( 'Sandbox Mode (Advanced)', 'weever' ); ?></a></li>
        	</ul>

        	<div id="tabs-1">
            	<div>
                	<fieldset class='adminForm'>
                        <h2 class="wxuia-fieldTitle"><?php echo __( 'Weever Apps Subscription Info', 'weever' ); ?>
                            <span class="wx-inputContainer">
                                <input id="wx-button-submit" class="wxuia-button" style="float:right" name="submit" type="submit" value="Save Changes">
                            </span>
                        </h2>

                    	<table class="admintable">
                        	<tr><td class="key"><?php _e( 'Subscription Key', 'weever' ); ?></td>
                        	<td valign="top"><input <?php if ( ! $weeverapp->site_key ) echo 'class="error"'; ?> type="text" name="site_key" maxlength="42" style="width:250px;" value="<?php echo $weeverapp->site_key; ?>" /><br />
                        	<a target="_blank" href="http://weeverapps.com/pricing"><?php echo __( 'Need a Weever Apps subscription key?' ); ?></a>
                        	</td>
                        	</tr>
                        	
                        	<?php if ( $weeverapp->site_key and $weeverapp->expiry and strtotime( $weeverapp->expiry ) !== false ): ?>
                        	<tr><td class="key" valign="top"><?php echo apply_filters( 'weever_subscription_expires_title', __( 'Subscription Expires', 'weever' ) ); ?></td>
                        	<td valign="top">
                        		<?php if ( strtotime( $weeverapp->expiry ) > time() ): ?>
	                        	<?php $expiry = date( 'F d, Y', strtotime( $weeverapp->expiry ) ); ?>
	                        	<?php else: ?>
	                        	<?php $expiry = '<strong>' . __( 'Expired', 'weever' ) .  '</strong> (<a target="_blank" href="http://weeverapps.com/pricing">' . __( 'Renew', 'weever' ) . '</a>)'; ?>
	                        	<?php endif; ?>
	                        	<?php $expiry .= '<p>Note that if your subscription expires and you are on a Free plan, you can continue to use your app but with only the Free features.<br /><a target="_blank" href="http://weeverapps.com/pricing">See details of each plan</a>.</p>'; ?>
	                        	<?php echo apply_filters( 'weever_subscription_expires_message', $expiry ); ?>
                        	</td>
                        	</tr>
                        	
                        	<?php endif; ?>
                        	
                        	<?php if ( $weeverapp->site_key ): ?>
                        	<tr><td class="key" valign="top"><?php _e( 'Subscription Domain', 'weever' ); ?></td>
                        	<td>
	                        	<?php if ( $weeverapp->primary_domain ): ?>
                        	    <?php echo sprintf( __( 'This key is linked to the domain <b>%s</b>', 'weever' ), $weeverapp->primary_domain ); ?>
                        	    <?php endif; ?>
                        	    <br />
                        	</td>
                        	</tr>
                        	<?php endif; ?>
                    	</table>
                	</fieldset>
            	</div>

        	</div>
        	<div id="tabs-2">
                <fieldset class='adminForm'>
                    <h2 class="wxuia-fieldTitle"><?php echo __( 'Sandbox Mode', 'weever' ); ?>
                        <span class="wx-inputContainer">
                                <input id="wx-button-submit" class="wxuia-button" style="float:right" name="submit" type="submit" value="Save Changes">
                            </span>
                    </h2>

                    <table class="admintable">
                    <tr>
                        <td>
                            <p><strong>Important: Do not build your app in sandbox mode!</strong></p>

                            <?php if ($weeverapp->staging_mode): ?>
                                <input class="wxui-btn purple small radius3" name="stagingmode" type="submit" value="<?php _e( 'Turn sandbox mode OFF', 'weever' ); ?>" />
                            <?php else: ?>
                                <input class="wxui-btn purple small radius3" name="stagingmode" type="submit" value="<?php _e( 'Turn sandbox mode ON', 'weever' ); ?>" />
                            <?php endif; ?>

                            <p><?php _e( 'Sandbox mode creates a <strong>separate copy</strong> of your Weever App in a test environment. Changes made in sandbox mode will not affect your public or live app.', 'weever'  ); ?></p>

                            <p><?php _e( 'Use sandbox mode when developing a new site on a test server, or to preview changes in a safely while your Weever App is already online.', 'weever'  ); ?></p>

                            <p><?php _e( 'Note: When in sandbox mode, your private QR Code and URL will update, below.', 'weever'  ); ?></p>
                        </td>
                    </tr>
                    </table>
                </fieldset>
        	</div>
        </div>

        <ol id="wx-account-content">
  			<li data-id="wx-app-features-navigation" data-options="tipLocation:top" data-text="Thanks!"><p>Once you've entered your subscription key from <a target="_blank" href="http://weeverapps.com/signup">weeverapps.com</a> click here to add features to your app!</p></li>
  		</ol>
        <!-- End feature tour -->
        
        <script type="text/javascript">
  		jQuery(window).load(function() {
    		jQuery(this).joyride({
        		//'timer': 8000,
        		//'startTimerOnClick': false,
    			'tipContent': '#wx-account-content', // The ID of the <ol> used for content
    			'tipAnimation': 'fade'
      			/* Options will go here */
    		});
  		});
  		</script>
        
    </form>
</div>