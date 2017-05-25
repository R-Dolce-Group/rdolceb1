<?php
if ( ! defined( 'ABSPATH' ) ) { exit; } ?>
<div class='wrap'>
    <?php echo $wpc_client->get_plugin_logo_block(); ?>
    <div class="wpc_clear"></div>
    <div class="icon32" id="icon-themes"><br /></div>
    <h2 class="title" style="float:left;"><?php _e( 'Project Management', WPC_CLIENT_TEXT_DOMAIN ); ?></h2>
    <div class="wpc_clear"></div>
    <?php if( isset( $_GET['message'] ) ) { ?>
        <div id="message" class="updated">
            <p>
            <?php
 switch( $_GET['message'] ) { case '1': _e( 'Project added successfully.', WPC_CLIENT_TEXT_DOMAIN ); break; case '2': _e( 'Project updated successfully.', WPC_CLIENT_TEXT_DOMAIN ); break; case '3': _e( 'Project deleted successfully.', WPC_CLIENT_TEXT_DOMAIN ); break; } ?>
            </p>
        </div>
        <div class="wpc_clear"></div>
    <?php } ?>
    <?php echo $this->gen_project_tabs_menu(); ?>
    <div class="wpc_clear"></div>
    <div class="wpc_pm_main_block">
        <div class="wpc_left_column">
            <div class="col-wrap">

            </div>
        </div>
        <div class="wpc_right_column">
            <div class="col-wrap"></div>
        </div>
    </div>
</div>