<?php
 if ( ! defined( 'ABSPATH' ) ) { exit; } global $wpc_client; $wpc_file_sharing = $wpc_client->cc_get_settings( 'file_sharing' ); ?>

<script type="text/javascript">
    jQuery(document).ready(function() {
        var id = jQuery('.open_project_popup').data('id');

        jQuery('.open_project_popup').shutter_box({
            view_type       : 'lightbox',
            width           : '500px',
            type            : 'ajax',
            dataType        : 'json',
            href            : '<?php echo get_admin_url() ?>admin-ajax.php',
            ajax_data       : "action=wpc_get_work_request_data&id=" + id,
            setAjaxResponse : function( data ) {
                jQuery( '.sb_lightbox_content_title' ).html( data.title );
                jQuery( '.sb_lightbox_content_body' ).html( data.content );

                custom_datepicker_init();

                jQuery('#project_due_date').datepicker( "option", "minDate", 0 );
                jQuery('#project_due_date').trigger( 'change' );
            }
        });

        jQuery('body').on('click', '.wpc_create_project_button', function() {
            jQuery('.open_project_popup').shutter_box('showLoader');
            jQuery.ajax({
                type: "POST",
                url: "<?php echo get_admin_url(); ?>admin-ajax.php",
                data: {
                    action          : 'wpc_create_project_by_work_request_data',
                    id              : id,
                    title           : jQuery('#project_title').val(),
                    description     : jQuery('#project_description').val(),
                    file_ids        : jQuery('.file_ids').val(),
                    due_date        : jQuery('input[name="project_due_date"]').val(),
                    business_sector : jQuery('#project_business_sector').val()
                },
                dataType: "json",
                success: function(data) {
                    if( data.status ) {
                        window.location = '<?php echo get_admin_url(); ?>/admin.php?page=wpc_project_management&action=details&project_id=' + data.message;
                    } else {
                        alert( data.message );
                    }
                },
                error: function() {
                    jQuery('.open_project_popup').shutter_box('close');
                }
            });
        });

        jQuery('body').on('click', '.wpc_cancel_button', function() {
            jQuery('.open_project_popup').shutter_box('close');
        });

    });
</script>

<div class="wrap">
    <?php echo $wpc_client->get_plugin_logo_block(); ?>
    <div class="wpc_clear"></div>
    <div class="icon32" id="icon-themes"><br /></div>

    <?php $this->get_breadcrumbs() ?>

    <div class="wpc_clear"></div>
    <?php if( isset( $_GET['message'] ) ) { ?>
        <div id="message" class="updated">
            <p>

            </p>
        </div>
        <div class="wpc_clear"></div>
    <?php } ?>
    <?php echo $this->gen_tabs_menu(); ?>
    <div class="wpc_clear"></div>
    <div class="wpc_pm_main_block">
        <h1><?php _e( 'Work Request Info:', WPC_CLIENT_TEXT_DOMAIN ) ?></h1>

        <p><b><?php _e( 'Title', WPC_CLIENT_TEXT_DOMAIN ) ?>:</b> <?php echo $details['title']; ?></p>
        <p><b><?php _e( 'From Client', WPC_CLIENT_TEXT_DOMAIN ) ?>:</b> <?php
 if ( isset( $details['client_id'] ) ) { if ( false != $user = get_userdata( $details['client_id'] ) ) { echo $user->get( 'user_login' ); } } ?></p>

        <p><b><?php _e( 'Description', WPC_CLIENT_TEXT_DOMAIN ) ?>:</b><br />
        <?php echo $details['description']; ?></p>

        <br />

        <?php if( count( $request_files ) ) { ?>
            <p><b><?php _e( 'Attachment(s)', WPC_CLIENT_TEXT_DOMAIN ) ?>:</b>
                <br />
                <?php
 $file_ids = array(); foreach( $request_files as $file_value ) { $file_ids[] = $file_value['id']; $download_url = get_admin_url(). '/admin.php?wpc_action=download&module=pm&id=' . $file_value['id'] . '&nonce=' . wp_create_nonce( get_current_user_id() . AUTH_KEY . $file_value['id'] ); $view_url = get_admin_url(). '/admin.php?wpc_action=view&module=pm&id=' . $file_value['id'] . '&nonce=' . wp_create_nonce( get_current_user_id() . AUTH_KEY . $file_value['id'] ); $file_type = $this->get_file_ext_by_filename( $file_value['file_path'] ); ?>
                        <a target="_blank" href="<?php echo $download_url; ?>"><?php echo $file_value['filename']; ?></a>
                        <a href="<?php echo get_admin_url() . 'admin.php?wpc_action=download&module=pm&id=' . $file_value['id'] . '&nonce=' . wp_create_nonce( get_current_user_id() . AUTH_KEY . $file_value['id'] ); ?>" class="pm_download_icon" title="<?php _e( 'Download', WPC_CLIENT_TEXT_DOMAIN ); ?>"></a>
                        <?php if( ( isset( $wpc_file_sharing['view_type'] ) && 'google_doc' == $wpc_file_sharing['view_type'] && in_array( $file_type, array_keys( $wpc_client->files_for_google_doc_view ) ) ) || in_array( $file_type, $wpc_client->files_for_regular_view ) ) { ?>
                                <a target="_blank" href="<?php echo get_admin_url() . 'admin.php?wpc_action=view&module=pm&id=' . $file_value['id'] . '&nonce=' . wp_create_nonce( get_current_user_id() . AUTH_KEY . $file_value['id'] ); ?>" class="pm_view_icon" title="<?php _e( 'View', WPC_CLIENT_TEXT_DOMAIN ); ?>"></a>
                        <?php } ?>
                        <br />
                <?php } ?>
                <input type="hidden" class="file_ids" value="<?php echo implode( ',', $file_ids ); ?>" />
            </p>
        <?php } ?>
        <p align="center">
            <input type="button" class="button-primary accept_request_button open_project_popup" data-id="<?php echo $request_id; ?>" value="<?php _e( 'Accept', WPC_CLIENT_TEXT_DOMAIN ); ?>">
        </p>
    </div>
</div>