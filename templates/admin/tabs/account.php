<div class="row">
    <div class="large-12 columns">
        <h2><?php echo __( 'Weever Apps Subscription Info', 'weever' ); ?></h2>
        <!-- This might be a good place for a logo -->
    </div>
</div>

<form action="" method="post">
    <div class="row">
        <div class="large-3 columns">
            Subscription Key
        </div>
        <div class="large-9 columns">
            <p><input <?php if ( ! $weeverapp->site_key ) echo 'class="error"'; ?> type="text" name="site_key" maxlength="42" style="width:250px;" value="<?php echo $weeverapp->site_key; ?>" /></p>
            <p><a target="_blank" href="http://weeverapps.com/pricing"><?php echo __( 'Need a Weever Apps subscription key?' ); ?></a></p>
        </div>
    </div>

    <?php if ( $weeverapp->site_key and $weeverapp->expiry and strtotime( $weeverapp->expiry ) !== false ): ?>
        <div class="row">
            <div class="large-3 columns">
                <?php echo apply_filters( 'weever_subscription_expires_title', __( 'Subscription Expires', 'weever' ) ); ?>
            </div>
            <div class="large-9 columns">
                <?php if ( strtotime( $weeverapp->expiry ) > time() ): ?>
                <?php $expiry = date( 'F d, Y', strtotime( $weeverapp->expiry ) ); ?>
                <?php else: ?>
                <?php $expiry = '<strong>' . __( 'Expired', 'weever' ) .  '</strong> (<a target="_blank" href="http://weeverapps.com/pricing">' . __( 'Renew', 'weever' ) . '</a>)'; ?>
                <?php endif; ?>
                <?php $expiry .= '<p>Note that if your subscription expires and you are on a Free plan, you can continue to use your app but with only the Free features.<br /><a target="_blank" href="http://weeverapps.com/pricing">See details of each plan</a>.</p>'; ?>
                <?php echo apply_filters( 'weever_subscription_expires_message', $expiry ); ?>
            </div>
        </div>
    <?php endif; ?>

    <?php if ( $weeverapp->site_key ): ?>
        <div class="row">
            <div class="large-3 columns">
                <?php _e( 'Subscription Domain', 'weever' ); ?>
            </div>
            <div class="large-9 columns">
                <?php if ( $weeverapp->primary_domain ): ?>
                <?php echo sprintf( __( 'This key is linked to the domain <b>%s</b>', 'weever' ), $weeverapp->primary_domain ); ?>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="large-12 columns">
            <span class="wx-inputContainer" id="save-button" style="width: 120px; float: left;">
                <input id="wx-button-submit" class="wxuia-button" name="submit" type="submit" value="Save Changes">
            </span>
        </div>
    </div>

</form>

<ol class="joyride-list" data-joyride>
    <li data-id="save-button" data-text="Thanks!">
        <p>Once you've entered your subscription key from <a target="_blank" href="http://weeverapps.com/signup">weeverapps.com</a> click here to add features to your app!</p>
    </li>
</ol>

<script type="text/javascript">
    // jQuery(document).foundation('joyride', 'start');
</script>
