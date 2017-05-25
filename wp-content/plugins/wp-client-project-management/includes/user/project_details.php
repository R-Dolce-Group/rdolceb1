<?php
if ( ! defined( 'ABSPATH' ) ) { exit; } if( isset( $_GET['message'] ) ) { ?>
    <div id="message" class="updated">
        <p>
        <?php
 switch( $_GET['message'] ) { case '20': _e( 'Sorry, you have used all of your storage quota.', WPC_CLIENT_TEXT_DOMAIN ); break; case '21': _e( 'There was an error uploading the file, please try again!', WPC_CLIENT_TEXT_DOMAIN ); break; } ?>
        </p>
    </div>
    <div class="wpc_clear"></div>
<?php } ?>
<h1 class="wpc_project_title"><?php echo $project_details['title']; ?></h1>
<p class="wpc_project_description"><?php echo $project_details['description']; ?></p>

<?php $custom_fields = $wpc_client->cc_get_settings( 'pm_custom_fields' ); if( is_array( $custom_fields ) ) { foreach( $custom_fields as $key=>$val ) { if( isset( $val['client_available'] ) && '1' == $val['client_available'] ) { ?>
            <p><b><?php echo $val['title']; ?></b>:<br /> <?php echo $this->show_custom_field_values( $project_details['id'], $key ); ?></p>
        <?php } } } ?>

<?php if( current_user_can('wpc_client') || ( current_user_can('wpc_client_staff') && ( current_user_can('wpc_pm_write_project_messages') || current_user_can('wpc_pm_read_project_messages') ) ) ) { ?>
    <div class="messages_list">
        <?php foreach( $messages as $message ) { ?>
            <div class="wpc_pm_message_line">
                <div class="wpc_pm_message_avatar">
                    <?php echo $wpc_client->cc_user_avatar( $message['send_from'] ); ?>
                </div>
                <div class="wpc_pm_message_line_content">
                    <div class="wpc_pm_author_date">
                        <div class="wpc_pm_message_author">
                            <?php $userdata = get_userdata( $message['send_from'] ); if( !$userdata ) { _e( "Removed User", WPC_CLIENT_TEXT_DOMAIN ); } else { echo( !empty( $userdata->data->display_name ) ? $userdata->data->display_name : $userdata->data->user_login ); } ?>
                        </div>
                        <div class="wpc_pm_message_date"><?php echo $this->date_convert( $message['creation_date'], 'Y-m-d H:i:s', $this->get_date_format() . ' ' . $this->get_time_format() ); ?></div>
                    </div>
                    <div class="wpc_pm_message_content">
                        <?php echo $this->linkifyText( $this->multiString( stripslashes( $message['message'] ) ) ); $file_list = $this->get_files_for_message( $message['id'] ); if( $file_list !== false ) { ?>
                            <ul class="files_list">
                                <?php foreach( $file_list as $file_value ) { ?>
                                    <li>
                                        <?php
 if ( $wpc_client->permalinks ) { $download_url = get_home_url() . '/wpc_downloader/pm/?wpc_action=download&id=' . $file_value['id'] . '&nonce=' . wp_create_nonce( get_current_user_id() . AUTH_KEY . $file_value['id'] ); $view_url = get_home_url() . '/wpc_downloader/pm/?wpc_action=view&id=' . $file_value['id'] . '&nonce=' . wp_create_nonce( get_current_user_id() . AUTH_KEY . $file_value['id'] ); } else { $download_url = add_query_arg( array( 'wpc_page' => 'downloader', 'wpc_page_value' => 'pm', 'wpc_action' => 'download', 'id' => $file_value['id'], 'nonce' => wp_create_nonce( get_current_user_id() . AUTH_KEY . $file_value['id'] ) ), get_home_url() ); $view_url = add_query_arg( array( 'wpc_page' => 'downloader', 'wpc_page_value' => 'pm', 'wpc_action' => 'view', 'id' => $file_value['id'], 'nonce' => wp_create_nonce( get_current_user_id() . AUTH_KEY . $file_value['id'] ) ), get_home_url() ); } $file_type = $this->get_file_ext_by_filename( $file_value['file_path'] ); ?>
                                        <a href="<?php echo $download_url; ?>"><?php echo $file_value['filename']; ?></a>
                                        <a href="<?php echo $download_url; ?>" class="pm_download_icon" title="<?php _e( 'Download', WPC_CLIENT_TEXT_DOMAIN ); ?>"></a>
                                        <?php if( ( isset( $wpc_pm_settings['view_type'] ) && 'google_doc' == $wpc_pm_settings['view_type'] && in_array( $file_type, array_keys( $wpc_client->files_for_google_doc_view ) ) ) || in_array( $file_type, $wpc_client->files_for_regular_view ) ) { ?>
                                            <a target="_blank" href="<?php echo $view_url; ?>" class="pm_view_icon" title="<?php _e( 'View', WPC_CLIENT_TEXT_DOMAIN ); ?>"></a>
                                        <?php } ?>
                                    </li>
                                <?php } ?>
                            </ul>
                        <?php } ?>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
<?php } ?>

<form method="POST" action="" id="wpc_pm_messages" enctype="multipart/form-data" class="wpc_form">
    <?php if( current_user_can('wpc_client') || ( current_user_can('wpc_client_staff') && current_user_can('wpc_pm_write_project_messages') ) ) { ?>
        <div class="wpc_pm_new_message">
            <div class="wpc_pm_message_avatar">
                <?php echo $wpc_client->cc_user_avatar( get_current_user_id() ) ?>
            </div>
            <div class="wpc_pm_new_message_field">
                <textarea name="message" class="message" rows="5" placeholder="<?php _e( "New Message", WPC_CLIENT_TEXT_DOMAIN ) ?>"></textarea>
                <div class="wpc_clear"></div>
                <b><?php _e( "Attachment Files", WPC_CLIENT_TEXT_DOMAIN ) ?></b>
                <div class="wpc_clear"></div>
                <?php if ( isset( $wpc_pm_settings['flash_uploader'] ) && 'html5' == $wpc_pm_settings['flash_uploader'] ) { ?>
                    <div id="uploader_warning" style="display: none;"></div>
                    <div class="wpc_st_button_addfile" style="float: left; width: 100%;">
                        <input class="talk_file_upload" id="talk_file_upload" name="Filedata" type="file" multiple="true">
                    </div>
                    <div id="queue" style="float: left; width: 100%;"></div>
                <?php } elseif( isset( $wpc_pm_settings['flash_uploader'] ) && 'plupload' == $wpc_pm_settings['flash_uploader'] ) { ?>
                    <div class="wpc_pluploder_talk_queue">
                        <p><?php _e( "Your browser doesn't have Flash, Silverlight or HTML5 support.", WPC_CLIENT_TEXT_DOMAIN ) ?></p>
                    </div>
                <?php } else { ?>
                    <script type="text/javascript">
                        jQuery( document ).ready(function() {
                            jQuery('#add_file').click(function() {
                                jQuery(this).before('<input type="file" name="file[]" />');
                            });
                        });
                    </script>
                    <a href="javascript:void(0);" id="add_file"></a>
                    <input type="file" name="file[]" />
                <?php } ?>
            </div>
        </div>
        <div class="wpc_form_line">
            <div class="wpc_form_label">
                &nbsp;
            </div>
            <div class="wpc_form_field">
                <input type="submit" class="button-primary message_send wpc_submit" value="<?php _e( "Send", WPC_CLIENT_TEXT_DOMAIN ); ?>" />
            </div>
        </div>
    <?php } ?>
</form>