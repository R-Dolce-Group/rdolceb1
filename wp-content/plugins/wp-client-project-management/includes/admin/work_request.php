<?php
if ( ! defined( 'ABSPATH' ) ) { exit; } global $wpdb, $wpc_client; ?>

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

    <?php $this->get_breadcrumbs(); ?>

    <div class="wpc_clear"></div>
    <?php if( isset( $_GET['message'] ) ) { ?>
        <div id="message" class="updated" style="display: block;">
            <p>
            <?php
 switch( $_GET['message'] ) { case '1': _e( 'Work request added successfully.', WPC_CLIENT_TEXT_DOMAIN ); break; case '2': _e( 'Work request updated successfully.', WPC_CLIENT_TEXT_DOMAIN ); break; case '3': _e( 'Work request deleted successfully.', WPC_CLIENT_TEXT_DOMAIN ); break; case '3': _e( 'Wrong Work request ID.', WPC_CLIENT_TEXT_DOMAIN ); break; } ?>
            </p>
        </div>
        <div class="wpc_clear"></div>
    <?php } ?>
    <?php echo $this->gen_tabs_menu(); ?>
    <div class="wpc_clear"></div>
    <div class="wpc_pm_main_block">
        <form action="" method="POST">
            <?php
 $ListTable->display(); ?>
        </form>
    </div>
</div>