<?php
if ( ! defined( 'ABSPATH' ) ) { exit; } global $wpc_client, $wpdb; $business_sectors = $wpdb->get_results( "SELECT id, title FROM {$wpdb->prefix}wpc_pm_sectors", ARRAY_A ); $wpc_currency = $wpc_client->cc_get_settings( 'currency' ); $wpc_request_type = $wpc_client->cc_get_settings( 'pm_request_types' ); ?>
<script type="text/javascript">
    jQuery(document).ready(function() {
        var type_id = 0;

        jQuery( ".wpc_pm_visibility" ).buttonset();

        jQuery( '.add_request_type').shutter_box({
            view_type       : 'lightbox',
            width           : '500px',
            type            : 'inline',
            href            : '#request_type_popup',
            title           : '<?php _e( 'New Work Request Type', WPC_CLIENT_TEXT_DOMAIN ) ?>'
        });

        jQuery( '.edit_request_type').each( function() {
            type_id = jQuery(this).data('id');
            if( type_id.length == 0 ) {
                type_id = 0;
                alert('<?php _e( 'Wrong ID', WPC_CLIENT_TEXT_DOMAIN ) ?>');
                return;
            }

            jQuery(this).shutter_box({
                view_type       : 'lightbox',
                width           : '500px',
                type            : 'ajax',
                dataType        : 'json',
                href            : '<?php echo get_admin_url() ?>admin-ajax.php',
                ajax_data       : "action=wpc_pm_settings&act=get&id=" + type_id,
                setAjaxResponse : function( data ) {
                    jQuery( '.sb_lightbox_content_title' ).html( data.title );
                    jQuery( '.sb_lightbox_content_body' ).html( data.content );
                }
            });

            type_id = 0;
        });

        jQuery('.request_type_table').on('click', '.delete_request_type', function() {
            type_id = jQuery(this).data('id');
            if( type_id.length == 0 ) {
                type_id = 0;
                alert('<?php _e( 'Wrong ID', WPC_CLIENT_TEXT_DOMAIN ) ?>');
                return;
            }

            jQuery.ajax({
                type     : 'POST',
                dataType : 'json',
                url      : '<?php echo get_admin_url(); ?>admin-ajax.php',
                data     : {
                    action          : 'wpc_pm_settings',
                    act             : 'delete',
                    id              : type_id
                },
                success  : function( data ) {
                    if( data.status ) {
                        jQuery('.request_type_table tbody tr[data-id=' + type_id + ']').remove();
                    } else {
                        alert( data.message );
                    }
                    type_id = 0;
                }
             });
        });

        jQuery('body').on('click', '.update_popup_data', function() {
            var type_id = jQuery(this).parents('#request_type_popup').find('.type_id_input').val();
            var title = jQuery(this).parents('#request_type_popup').find('.title_input').val();
            var price = jQuery(this).parents('#request_type_popup').find('.price_input').val();
            var currency = jQuery(this).parents('#request_type_popup').find('.currency_select').val();
            var business_sector = ( jQuery(this).parents('#request_type_popup').find('.business_sector_select').length > 0 && jQuery(this).parents('#request_type_popup').find('.business_sector_select').val().length > 0 ) ? jQuery(this).parents('#request_type_popup').find('.business_sector_select').val() : 0;

            if( isNaN( price ) && price > 0 ) {
                alert('<?php _e( 'Wrong value in price field', WPC_CLIENT_TEXT_DOMAIN ) ?>');
                return;
            }

            var ajax_data = {};
            var action = 'add';

            if( typeof(type_id) !== 'undefined' && type_id != 0 ) {
                action = 'edit';
                ajax_data.id = type_id;
            }

            ajax_data.action = 'wpc_pm_settings';
            ajax_data.act = action;
            ajax_data.title = title;
            ajax_data.price = price;
            ajax_data.currency = currency;
            ajax_data.business_sector = business_sector;

            jQuery.ajax({
                type     : 'POST',
                dataType : 'json',
                url      : '<?php echo get_admin_url(); ?>admin-ajax.php',
                data     : ajax_data,
                success  : function( data ) {
                    if( data.status ) {
                        if( action == 'add' ) {
                            jQuery('.request_type_table tbody').append('<tr data-id="' + data.message.key + '">' +
                                '<td>' +
                                    '<strong><span class="request_type_title">' + data.message.title + '</span></strong>' +
                                    '<div class="row-actions">' +
                                        '<span class="edit"><a class="edit_request_type" href="javascript: void(0);" data-id="' + data.message.key + '">Edit</a> | </span>' +
                                        '<span class="delete"><a class="delete_request_type" href="javascript: void(0);" data-id="' + data.message.key + '">Delete Permanently</a></span>' +
                                    '</div>' +
                                '</td>' +
                                '<td>' +
                                    '<span class="business_sector">' + data.message.business_sector_name + '</span>' +
                                '</td>' +
                                '<td>' +
                                    '<span class="price">' + data.message.price_field + '</span>' +
                                '</td>' +
                            '</tr>');
                        } else {
                            jQuery('.request_type_table tbody tr[data-id=' + data.message.key + ']').html('<td>' +
                                '<strong><span class="request_type_title">' + data.message.title + '</span></strong>' +
                                '<div class="row-actions">' +
                                    '<span class="edit"><a class="edit_request_type" href="javascript: void(0);" data-id="' + data.message.key + '">Edit</a> | </span>' +
                                    '<span class="delete"><a class="delete_request_type" href="javascript: void(0);" data-id="' + data.message.key + '">Delete Permanently</a></span>' +
                                '</div>' +
                            '</td>' +
                            '<td>' +
                                '<span class="business_sector">' + data.message.business_sector_name + '</span>' +
                            '</td>' +
                            '<td>' +
                                '<span class="price">' + data.message.price_field + '</span>' +
                            '</td>');
                        }
                        jQuery('.title_input').val('');
                        jQuery('.business_sector_select').val('');
                        jQuery('.price_input').val('');
                        jQuery('.currency_select').val('');
                        jQuery('#currency_select option.default_currency').prop('selected', 'selected');
                        if( type_id != 0 ) {
                            jQuery( '.edit_request_type').shutter_box('close');
                            type_id = 0;
                        } else {
                            jQuery( '.add_request_type').shutter_box('close');
                        }
                    } else {
                        alert( data.message );
                    }
                }
             });
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
 switch( $_GET['message'] ) { case '1': _e( 'Settings updated successfully.', WPC_CLIENT_TEXT_DOMAIN ); break; } ?>
            </p>
        </div>
        <div class="wpc_clear"></div>
    <?php } ?>
    <?php echo $this->gen_tabs_menu(); ?>
    <div class="wpc_clear"></div>
    <div class="wpc_pm_main_block teams_page">
        <form action="" method="post" name="wpc_settings" id="wpc_settings" >

                <h2><span><?php _e( 'General Settings', WPC_CLIENT_TEXT_DOMAIN ) ?></span></h2>
                <div class="inside">
                    <span class="description"><?php _e( "Use this section to setup general Project Management settings", WPC_CLIENT_TEXT_DOMAIN ) ?></span>
                    <hr />
                    <table border="0" cellpadding="0" cellspacing="0" class="wpc_pm_settings_table">
                        <tr>
                            <td>
                                <?php _e( 'Use uploader:', WPC_CLIENT_TEXT_DOMAIN ) ?>
                            </td>
                            <td>
                                <select name="settings[pm_general][flash_uploader]" style="width: auto;">
                                    <option value="regular" <?php selected( isset( $global_settings['pm_general']['flash_uploader'] ) ? $global_settings['pm_general']['flash_uploader'] : 'plupload', 'regular' ); ?>><?php _e( 'Regular', WPC_CLIENT_TEXT_DOMAIN ); ?></option>
                                    <option value="html5" <?php selected( isset( $global_settings['pm_general']['flash_uploader'] ) ? $global_settings['pm_general']['flash_uploader'] : 'plupload', 'html5' ); ?>>HTML5 (with progress bar, multiple files uploading)</option>
                                    <option value="plupload" <?php selected( isset( $global_settings['pm_general']['flash_uploader'] ) ? $global_settings['pm_general']['flash_uploader'] : 'plupload', 'plupload' ); ?>>uberLOADER (with progress bar, multiple files uploading, chunking upload for big files)</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <?php _e( 'Auto hide tasks after', WPC_CLIENT_TEXT_DOMAIN ); ?>
                            </td>
                            <td>
                                <input type="text" style="width: 100px;" name="settings[pm_general][auto_hide_days]" value="<?php echo( isset( $global_settings['pm_general']['auto_hide_days'] ) ? $global_settings['pm_general']['auto_hide_days'] : 0 ); ?>" />
                                <span class="description"><?php _e( 'days they were completed/closed.', WPC_CLIENT_TEXT_DOMAIN ); ?></span>
                            </td>
                        </tr>
                    </table>
                </div>
                <br /><br />
                <h2><span><?php _e( 'Email Notification Settings', WPC_CLIENT_TEXT_DOMAIN ) ?></span></h2>
                <div class="inside">
                    <span class="description"><?php _e( "Use this section to select notification settings", WPC_CLIENT_TEXT_DOMAIN ) ?></span>
                    <hr />
                    <table border="0" cellpadding="0" cellspacing="0" class="wpc_pm_settings_table">
                        <tr>
                            <td width="200" style="font-weight: bold; font-size: 12px;">
                                <?php _e( 'Show Settings to other users', WPC_CLIENT_TEXT_DOMAIN ) ?>
                            </td>
                            <td colspan="2" style="font-weight: bold; font-size: 12px;">
                                <?php _e( 'Send Notifications', WPC_CLIENT_TEXT_DOMAIN ) ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="wpc_pm_visibility">
                                    <input type="radio" id="user_assigned_task_hide" name="settings[pm_email_notifications][user_assigned_task_visibility]" value="0" <?php checked( $global_settings['pm_email_notifications']['user_assigned_task_visibility'], 0 ); ?>><label for="user_assigned_task_hide"><?php _e( 'Hide', WPC_CLIENT_TEXT_DOMAIN ) ?></label>
                                    <input type="radio" id="user_assigned_task_show" name="settings[pm_email_notifications][user_assigned_task_visibility]" value="1" <?php checked( $global_settings['pm_email_notifications']['user_assigned_task_visibility'], 1 ); ?>><label for="user_assigned_task_show"><?php _e( 'Show', WPC_CLIENT_TEXT_DOMAIN ) ?></label>
                                </div>
                            </td>
                            <td width="30">
                                <input type="checkbox" name="settings[pm_email_notifications][user_assigned_task]" class="wpc_pm_send_email" value="1" <?php checked( isset( $global_settings['pm_email_notifications']['user_assigned_task'] ) ? $global_settings['pm_email_notifications']['user_assigned_task'] : 0, 1 ); ?> />
                            </td>
                            <td>
                                <?php _e( 'Task is assigned to Teammate/Freelancer', WPC_CLIENT_TEXT_DOMAIN ) ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="wpc_pm_visibility">
                                    <input type="radio" id="user_assigned_to_project_hide" name="settings[pm_email_notifications][user_assigned_to_project_visibility]" value="0" <?php checked( $global_settings['pm_email_notifications']['user_assigned_to_project_visibility'], 0 ); ?>><label for="user_assigned_to_project_hide"><?php _e( 'Hide', WPC_CLIENT_TEXT_DOMAIN ) ?></label>
                                    <input type="radio" id="user_assigned_to_project_show" name="settings[pm_email_notifications][user_assigned_to_project_visibility]" value="1" <?php checked( $global_settings['pm_email_notifications']['user_assigned_to_project_visibility'], 1 ); ?>><label for="user_assigned_to_project_show"><?php _e( 'Show', WPC_CLIENT_TEXT_DOMAIN ) ?></label>
                                </div>
                            </td>
                            <td width="30">
                                <input type="checkbox" name="settings[pm_email_notifications][user_assigned_to_project]" class="wpc_pm_send_email" value="1" <?php checked( isset( $global_settings['pm_email_notifications']['user_assigned_to_project'] ) ? $global_settings['pm_email_notifications']['user_assigned_to_project'] : 0, 1 ); ?> />
                            </td>
                            <td>
                                <?php printf( __( '%s is assigned to %s/%s', WPC_CLIENT_TEXT_DOMAIN ), $wpc_client->custom_titles['project']['s'], $wpc_client->custom_titles['teammate']['s'], $wpc_client->custom_titles['freelancer']['s'] ); ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="wpc_pm_visibility">
                                    <input type="radio" id="message_posted_to_my_task_hide" name="settings[pm_email_notifications][message_posted_to_my_task_visibility]" value="0" <?php checked( $global_settings['pm_email_notifications']['message_posted_to_my_task_visibility'], 0 ); ?>><label for="message_posted_to_my_task_hide"><?php _e( 'Hide', WPC_CLIENT_TEXT_DOMAIN ) ?></label>
                                    <input type="radio" id="message_posted_to_my_task_show" name="settings[pm_email_notifications][message_posted_to_my_task_visibility]" value="1" <?php checked( $global_settings['pm_email_notifications']['message_posted_to_my_task_visibility'], 1 ); ?>><label for="message_posted_to_my_task_show"><?php _e( 'Show', WPC_CLIENT_TEXT_DOMAIN ) ?></label>
                                </div>
                            </td>
                            <td width="30">
                                <input type="checkbox" name="settings[pm_email_notifications][message_posted_to_my_task]" class="wpc_pm_send_email" value="1" <?php checked( isset( $global_settings['pm_email_notifications']['message_posted_to_my_task'] ) ? $global_settings['pm_email_notifications']['message_posted_to_my_task'] : 0, 1 ); ?> />
                            </td>
                            <td>
                                <?php _e( 'Message added to assigned Task(s)', WPC_CLIENT_TEXT_DOMAIN ) ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="wpc_pm_visibility">
                                    <input type="radio" id="file_uploaded_to_my_task_hide" name="settings[pm_email_notifications][file_uploaded_to_my_task_visibility]" value="0" <?php checked( $global_settings['pm_email_notifications']['file_uploaded_to_my_task_visibility'], 0 ); ?>><label for="file_uploaded_to_my_task_hide"><?php _e( 'Hide', WPC_CLIENT_TEXT_DOMAIN ) ?></label>
                                    <input type="radio" id="file_uploaded_to_my_task_show" name="settings[pm_email_notifications][file_uploaded_to_my_task_visibility]" value="1" <?php checked( $global_settings['pm_email_notifications']['file_uploaded_to_my_task_visibility'], 1 ); ?>><label for="file_uploaded_to_my_task_show"><?php _e( 'Show', WPC_CLIENT_TEXT_DOMAIN ) ?></label>
                                </div>
                            </td>
                            <td width="30">
                                <input type="checkbox" name="settings[pm_email_notifications][file_uploaded_to_my_task]" class="wpc_pm_send_email" value="1" <?php checked( isset( $global_settings['pm_email_notifications']['file_uploaded_to_my_task'] ) ? $global_settings['pm_email_notifications']['file_uploaded_to_my_task'] : 0, 1 ); ?> />
                            </td>
                            <td>
                                <?php _e( 'File is added to assigned Task(s)', WPC_CLIENT_TEXT_DOMAIN ) ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="wpc_pm_visibility">
                                    <input type="radio" id="team_talk_posted_hide" name="settings[pm_email_notifications][team_talk_posted_visibility]" value="0" <?php checked( $global_settings['pm_email_notifications']['team_talk_posted_visibility'], 0 ); ?>><label for="team_talk_posted_hide"><?php _e( 'Hide', WPC_CLIENT_TEXT_DOMAIN ) ?></label>
                                    <input type="radio" id="team_talk_posted_show" name="settings[pm_email_notifications][team_talk_posted_visibility]" value="1" <?php checked( $global_settings['pm_email_notifications']['team_talk_posted_visibility'], 1 ); ?>><label for="team_talk_posted_show"><?php _e( 'Show', WPC_CLIENT_TEXT_DOMAIN ) ?></label>
                                </div>
                            </td>
                            <td width="30">
                                <input type="checkbox" name="settings[pm_email_notifications][team_talk_posted]" class="wpc_pm_send_email" value="1" <?php checked( isset( $global_settings['pm_email_notifications']['team_talk_posted'] ) ? $global_settings['pm_email_notifications']['team_talk_posted'] : 0, 1 ); ?> />
                            </td>
                            <td>
                                <?php _e( 'Message posted in Team Talk (not sent to Freelancers)', WPC_CLIENT_TEXT_DOMAIN ) ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="wpc_pm_visibility">
                                    <input type="radio" id="client_talk_posted_hide" name="settings[pm_email_notifications][client_talk_posted_visibility]" value="0" <?php checked( isset($global_settings['pm_email_notifications']['client_talk_posted_visibility']) ? $global_settings['pm_email_notifications']['client_talk_posted_visibility'] : 0, 0 ); ?>><label for="client_talk_posted_hide"><?php _e( 'Hide', WPC_CLIENT_TEXT_DOMAIN ) ?></label>
                                    <input type="radio" id="client_talk_posted_show" name="settings[pm_email_notifications][client_talk_posted_visibility]" value="1" <?php checked( isset($global_settings['pm_email_notifications']['client_talk_posted_visibility']) ? $global_settings['pm_email_notifications']['client_talk_posted_visibility'] : 0, 1 ); ?>><label for="client_talk_posted_show"><?php _e( 'Show', WPC_CLIENT_TEXT_DOMAIN ) ?></label>
                                </div>
                            </td>
                            <td width="30">
                                <input type="checkbox" name="settings[pm_email_notifications][client_posted_message]" class="wpc_pm_send_email" value="1" <?php checked( isset( $global_settings['pm_email_notifications']['client_posted_message'] ) ? $global_settings['pm_email_notifications']['client_posted_message'] : 0, 1 ); ?> />
                            </td>
                            <td>
                                <?php _e( 'Message posted in Client Talk (not sent to Freelancers)', WPC_CLIENT_TEXT_DOMAIN ) ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="wpc_pm_visibility">
                                    <input type="radio" id="file_uploaded_to_my_project_hide" name="settings[pm_email_notifications][file_uploaded_to_my_project_visibility]" value="0" <?php checked( $global_settings['pm_email_notifications']['file_uploaded_to_my_project_visibility'], 0 ); ?>><label for="file_uploaded_to_my_project_hide"><?php _e( 'Hide', WPC_CLIENT_TEXT_DOMAIN ) ?></label>
                                    <input type="radio" id="file_uploaded_to_my_project_show" name="settings[pm_email_notifications][file_uploaded_to_my_project_visibility]" value="1" <?php checked( $global_settings['pm_email_notifications']['file_uploaded_to_my_project_visibility'], 1 ); ?>><label for="file_uploaded_to_my_project_show"><?php _e( 'Show', WPC_CLIENT_TEXT_DOMAIN ) ?></label>
                                </div>
                            </td>
                            <td width="30">
                                <input type="checkbox" name="settings[pm_email_notifications][file_uploaded_to_my_project]" class="wpc_pm_send_email" value="1" <?php checked( isset( $global_settings['pm_email_notifications']['file_uploaded_to_my_project'] ) ? $global_settings['pm_email_notifications']['file_uploaded_to_my_project'] : 0, 1 ); ?> />
                            </td>
                            <td>
                                <?php _e( 'File is added to assigned Project(s)', WPC_CLIENT_TEXT_DOMAIN ) ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="wpc_pm_visibility">
                                    <input type="radio" id="priority_modified_on_my_task_hide" name="settings[pm_email_notifications][priority_modified_on_my_task_visibility]" value="0" <?php checked( $global_settings['pm_email_notifications']['priority_modified_on_my_task_visibility'], 0 ); ?>><label for="priority_modified_on_my_task_hide"><?php _e( 'Hide', WPC_CLIENT_TEXT_DOMAIN ) ?></label>
                                    <input type="radio" id="priority_modified_on_my_task_show" name="settings[pm_email_notifications][priority_modified_on_my_task_visibility]" value="1" <?php checked( $global_settings['pm_email_notifications']['priority_modified_on_my_task_visibility'], 1 ); ?>><label for="priority_modified_on_my_task_show"><?php _e( 'Show', WPC_CLIENT_TEXT_DOMAIN ) ?></label>
                                </div>
                            </td>
                            <td width="30">
                                <input type="checkbox" name="settings[pm_email_notifications][priority_modified_on_my_task]" class="wpc_pm_send_email" value="1" <?php checked( isset( $global_settings['pm_email_notifications']['priority_modified_on_my_task'] ) ? $global_settings['pm_email_notifications']['priority_modified_on_my_task'] : 0, 1 ); ?> />
                            </td>
                            <td>
                                <?php _e( 'Task priority is modified on assigned Task(s)', WPC_CLIENT_TEXT_DOMAIN ) ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="wpc_pm_visibility">
                                    <input type="radio" id="due_date_modified_on_my_task_hide" name="settings[pm_email_notifications][due_date_modified_on_my_task_visibility]" value="0" <?php checked( $global_settings['pm_email_notifications']['due_date_modified_on_my_task_visibility'], 0 ); ?>><label for="due_date_modified_on_my_task_hide"><?php _e( 'Hide', WPC_CLIENT_TEXT_DOMAIN ) ?></label>
                                    <input type="radio" id="due_date_modified_on_my_task_show" name="settings[pm_email_notifications][due_date_modified_on_my_task_visibility]" value="1" <?php checked( $global_settings['pm_email_notifications']['due_date_modified_on_my_task_visibility'], 1 ); ?>><label for="due_date_modified_on_my_task_show"><?php _e( 'Show', WPC_CLIENT_TEXT_DOMAIN ) ?></label>
                                </div>
                            </td>
                            <td width="30">
                                <input type="checkbox" name="settings[pm_email_notifications][due_date_modified_on_my_task]" class="wpc_pm_send_email" value="1" <?php checked( isset( $global_settings['pm_email_notifications']['due_date_modified_on_my_task'] ) ? $global_settings['pm_email_notifications']['due_date_modified_on_my_task'] : 0, 1 ); ?> />
                            </td>
                            <td>
                                <?php _e( 'Task due date is modified on assigned Task(s)', WPC_CLIENT_TEXT_DOMAIN ) ?>
                            </td>
                        </tr>
                    </table>
                </div>


                <br /><br />
                <h2><span><?php _e( 'Work Request Type Settings', WPC_CLIENT_TEXT_DOMAIN ) ?></span></h2>
                <div class="inside">
                    <span class="description"><?php _e( "Use this section to setup Work Request Types for Project Management extension", WPC_CLIENT_TEXT_DOMAIN ) ?></span>
                    <hr />
                    <br />
                    <a class="add-button add_request_type" href="javascript:void(0);"><?php _e( 'Add Type', WPC_CLIENT_TEXT_DOMAIN ) ?></a>
                    <br />
                    <br />
                    <table class="wp-list-table widefat fixed request_type_table" cellspacing="0">
                        <thead>
                            <tr>
                                <th scope="col" id="title" class="manage-column column-title" style=""><?php _e( 'Title', WPC_CLIENT_TEXT_DOMAIN ); ?></th>
                                <th scope="col" id="code" class="manage-column column-business-sector" style=""><?php echo $wpc_client->custom_titles['business_sector']['s']; ?></th>
                                <th scope="col" id="symbol" class="manage-column column-price" style=""><?php _e( 'Price', WPC_CLIENT_TEXT_DOMAIN ); ?></th>
                            </tr>
                        </thead>

                        <tbody id="the-list">
                            <?php foreach( $wpc_request_type as $key=>$val ) { ?>
                                <tr data-id="<?php echo $key; ?>">
                                    <td>
                                        <strong><span class="request_type_title"><?php echo $val['title']; ?></span></strong>
                                        <div class="row-actions">
                                            <span class="edit"><a class="edit_request_type" href="javascript: void(0);" data-id="<?php echo $key; ?>">Edit</a> | </span>
                                            <span class="delete"><a class="delete_request_type" href="javascript: void(0);" data-id="<?php echo $key; ?>">Delete Permanently</a></span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="business_sector"><?php echo $this->get_business_sector_name( $val['business_sector'] ); ?></span>
                                    </td>
                                    <td>
                                        <span class="price"><?php $wpc_client->cc_set_currency( $val['price'], $val['currency'] ); ?></span>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>

            <br />
            <p>
                <input type='submit' name='update' id="update" class='button-primary' value='<?php _e( 'Update Settings', WPC_CLIENT_TEXT_DOMAIN ) ?>' />
            </p>

            <div style="display: none;float:left;width:100%;">
                 <div id="request_type_popup" style="float:left;width:100%;">
                    <label>
                        <?php _e( 'Title', WPC_CLIENT_TEXT_DOMAIN ) ?><br />
                        <input type="text" name="title" id="title_input" class="title_input" />
                    </label>
                    <br /><br />
                    <?php if( count( $business_sectors ) ) { ?>
                        <label>
                            <?php echo $wpc_client->custom_titles['business_sector']['s']; ?><br />
                            <select name="business_sector" id="business_sector_select" class="business_sector_select">
                                <option value="0"></option>
                                <?php foreach( $business_sectors as $val ) { ?>
                                    <option value="<?php echo $val['id']; ?>"><?php echo $val['title']; ?></option>
                                <?php } ?>
                            </select>
                        </label>
                        <br /><br />
                    <?php } ?>
                    <label>
                        <?php _e( 'Price', WPC_CLIENT_TEXT_DOMAIN ) ?><br />
                        <input type="text" name="price" id="price_input" class="price_input" />
                    </label>
                    <select name="currency" id="currency_select" class="currency_select">
                        <?php foreach( $wpc_currency as $key=>$val ) { ?>
                            <option value="<?php echo $key; ?>" <?php selected( $val['default'], 1 ); ?> <?php echo( $val['default'] == 1 ? 'class="default_currency"' : '' ); ?>><?php echo $val['title']; ?></option>
                        <?php } ?>
                    </select>
                    <br /><br />
                    <p align="center">
                        <input type="button" class="update_popup_data button-primary" value="<?php _e( 'Save', WPC_CLIENT_TEXT_DOMAIN ) ?>" />
                    </p>
                 </div>
             </div>
        </form>
    </div>
</div>