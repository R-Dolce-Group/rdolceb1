<?php
if ( ! defined( 'ABSPATH' ) ) { exit; } ?>

<script type="text/javascript">
    jQuery(document).ready(function() {
        var user_block_width = 100;
        jQuery('.wpc_user').each(function() {
            if( jQuery(this).width() > user_block_width ) {
                user_block_width = jQuery(this).width();
            }
        });

        jQuery('.wpc_user').width( user_block_width );

        jQuery('.wpc_user').live( 'click', function() {
            var checkbox = jQuery(this).find('.wpc_pm_user_checkbox');
            checkbox.prop( "checked", !checkbox.is(":checked") );
            jQuery(this).toggleClass('checked');
        });
    });
</script>
<div class='wrap'>
    <?php echo $wpc_client->get_plugin_logo_block(); ?>
    <div class="wpc_clear"></div>
    <div class="icon32" id="icon-themes"><br /></div>

    <?php $this->get_breadcrumbs() ?>

    <div class="wpc_clear"></div>
    <?php if( isset( $_GET['message'] ) ) { ?>
        <div id="message" class="updated">
            <p>
            <?php
 switch( $_GET['message'] ) { case '1': printf( __( '%s added successfully.', WPC_CLIENT_TEXT_DOMAIN ), $wpc_client->custom_titles['business_sector']['s'] ); break; case '2': printf( __( '%s updated successfully.', WPC_CLIENT_TEXT_DOMAIN ), $wpc_client->custom_titles['business_sector']['s'] ); break; case '3': printf( __( '%s deleted successfully.', WPC_CLIENT_TEXT_DOMAIN ), $wpc_client->custom_titles['business_sector']['s'] ); break; case '4': printf( __( 'Wrong %s ID.', WPC_CLIENT_TEXT_DOMAIN ), $wpc_client->custom_titles['business_sector']['s'] ); break; } ?>
            </p>
        </div>
        <div class="wpc_clear"></div>
    <?php } ?>
    <?php if( isset( $_GET['error'] ) ) { ?>
        <div id="message" class="error">
            <p>
            <?php
 switch( $_GET['error'] ) { case '1': printf( __( 'Wrong %s ID.', WPC_CLIENT_TEXT_DOMAIN ), $wpc_client->custom_titles['business_sector']['s'] ); break; case '2': _e( 'Business Sector Name is empty.', WPC_CLIENT_TEXT_DOMAIN ); break; case '3': _e( 'Business Sector Name already exists.', WPC_CLIENT_TEXT_DOMAIN ); break; } ?>
            </p>
        </div>
        <div class="wpc_clear"></div>
    <?php } ?>
    <?php echo $this->gen_tabs_menu(); ?>
    <div class="wpc_clear"></div>
    <div class="wpc_pm_main_block teams_page">
        <div class="wpc_right_column">
            <div class="col-wrap">
                <form class="sector_data" method="POST">
                    <p>
                        <?php _e( "Here you can identify different sectors in your organization, and then you'll be able to associate projects with certain sectors. Reports can be generated to show you which business sectors are generating the most load on your Team", WPC_CLIENT_TEXT_DOMAIN ); ?>
                    </p>
                    <p>
                        <label>
                            <?php printf( __( '%s Name', WPC_CLIENT_TEXT_DOMAIN ), $wpc_client->custom_titles['business_sector']['s'] ); ?>:<br />
                            <input type="text" name="title" id="sector_name" value="<?php echo( isset( $title ) ? esc_attr( $title ) : '' ); ?>" />
                        </label>
                    </p>
                    <p>
                        <label>
                            <?php printf( __( 'Business Sector Description', WPC_CLIENT_TEXT_DOMAIN ), $wpc_client->custom_titles['business_sector']['s'] ); ?>:<br />
                            <textarea name="description" id="description" rows="5" cols="30"><?php echo( isset( $description ) ? $description : '' ); ?></textarea>
                        </label>
                    </p>
                    <p align="right">
                        <input type="submit" name="add_sector" class="button-primary" value="<?php _e( 'Submit', WPC_CLIENT_TEXT_DOMAIN ); ?>" />
                    </p>
                </form>
            </div>
        </div>
        <div class="wpc_left_column">
            <div class="col-wrap">
                <form action="" method="POST">
                    <?php
 $ListTable->display(); ?>
                </form>
            </div>
        </div>
    </div>
</div>