<?php
if ( ! defined( 'ABSPATH' ) ) { exit; } global $wpc_client; ?>
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
 switch( $_GET['message'] ) { case '1': _e( 'User added successfully.', WPC_CLIENT_TEXT_DOMAIN ); break; case '2': _e( 'Wrong user ID.', WPC_CLIENT_TEXT_DOMAIN ); break; case '3': _e( 'User updated successfully.', WPC_CLIENT_TEXT_DOMAIN ); break; case '4': _e( 'User deleted successfully.', WPC_CLIENT_TEXT_DOMAIN ); break; case '5': _e( 'Users deleted successfully.', WPC_CLIENT_TEXT_DOMAIN ); break; } ?>
            </p>
        </div>
        <div class="wpc_clear"></div>
    <?php } ?>
    <?php echo $this->gen_tabs_menu(); ?>
    <div class="wpc_clear"></div>
    <div class="wpc_pm_main_block">
        <p>
            <a href="<?php echo admin_url('admin.php?page=wpc_project_management&tab=users&action=add'); ?>" class="add-new-h2"><?php _e( 'Add User', WPC_CLIENT_TEXT_DOMAIN ); ?></a>
        </p>
        <form action="" method="POST">
            <?php
 $ListTable->display(); ?>
        </form>
    </div>
</div>