<div class="wrap">

    <form method="post">

        <div class="wxui-stdContainer">

        <!-- CSS Overrides                 -->
        <!-- ***************************** -->

                            <fieldset class="DupadminForm">
                <h2 class="wxuia-fieldTitle"><?php echo __( 'CSS Theme Overrides' ); ?>
                <span class="wx-inputContainer">
                <input id="wx-button-submit" class="wxuia-button" style="float:right" name="submit" type="submit" value="Save Changes">
                </span>
                </h2>

                                <?php $weever_server = $weeverapp->staging_mode ? WeeverConst::LIVE_STAGE : WeeverConst::LIVE_SERVER; ?>
                                <?php $private_url = $weever_server . 'app/' . $weeverapp->primary_domain; ?>

                                <table class="admintable" style="width:100%;">
                                    <tr><td><p><?php echo sprintf( __( 'Modify your app by entering valid CSS classes in the box below.<br/>
                                    <br/>To identify CSS Styles use a webkit browser (such as Chrome or Safari), right-click on the app and inspect the HTML elements.', 'weever' ), $private_url, $private_url ); ?>
                                    </p>
        </td></tr>
                                    <tr>
                                    <td class="key"><?php echo __( 'Enter CSS Styles:', 'weever' ); ?></td>
                                    </tr>
                                    <tr>
                                        <td>
                                        <textarea name="css" rows="30" style="width: 100%;"><?php echo $weeverapp->theme->css; ?></textarea>
                                        </td>
                                    </tr>
                                </table>
                            </fieldset>



        <!-- End: Add a Left and Right Margin of 20px -->

        </div>

    </form>

<!-- START: iFrame Preview -->

<?php echo weever_phone_preview(true); ?>

<!-- END: iFrame Preview -->



</div>

