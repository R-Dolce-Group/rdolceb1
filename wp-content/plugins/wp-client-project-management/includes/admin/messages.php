<?php
if ( ! defined( 'ABSPATH' ) ) { exit; } $wpc_file_sharing = $wpc_client->cc_get_settings( 'file_sharing' ); ?>
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
 switch( $_GET['message'] ) { case '1': _e( 'Message sent successfully.', WPC_CLIENT_TEXT_DOMAIN ); break; case '20': _e( 'Sorry, you have used all of your storage quota.', WPC_CLIENT_TEXT_DOMAIN ); break; case '21': _e( 'There was an error uploading the file, please try again!', WPC_CLIENT_TEXT_DOMAIN ); break; } ?>
            </p>
        </div>
        <div class="wpc_clear"></div>
    <?php } ?>
    <?php echo $this->gen_project_tabs_menu(); ?>
    <div class="wpc_clear"></div>
    <div class="wpc_pm_main_block">
        <div class="col-wrap">
            <form method="POST" action="" id="wpc_pm_messages" enctype="multipart/form-data">
                <div class="message_form">
                    <textarea name="message" class="message" rows="5"></textarea>
                    <div class="wpc_clear"></div>
                    <script type="text/javascript">
                        jQuery(document).ready(function() {
                            jQuery('.wpc_pm_add_file_block').hide();
                            jQuery('.wpc_pm_add_file_link').click(function() {
                                jQuery('.wpc_pm_add_file_block').toggle('slow');
                            });
                        });
                    </script>
                    <a href="javascript: void(0);" class="wpc_pm_add_file_link"><?php _e( "Add Attachment(s)", WPC_CLIENT_TEXT_DOMAIN ); ?></a>
                    <div class="wpc_pm_add_file_block">
                        <?php if ( isset( $wpc_pm_settings['flash_uploader'] ) && 'html5' == $wpc_pm_settings['flash_uploader'] ) { ?>
                            <div id="uploader_warning" style="display: none;"></div>
                            <br clear="all" />
                            <div class="wpc_st_button_addfile">
                                <input class="talk_file_upload" id="talk_file_upload" name="Filedata" type="file" multiple="true">
                            </div>
                            <div id="queue"></div>
                        <?php } elseif( isset( $wpc_pm_settings['flash_uploader'] ) && 'plupload' == $wpc_pm_settings['flash_uploader'] ) { ?>
                            <div class="wpc_pluploder_talk_queue">
                                <p><?php _e( "Your browser doesn't have Flash, Silverlight or HTML5 support.", WPC_CLIENT_TEXT_DOMAIN ) ?></p>
                            </div>
                        <?php } else { ?>
                            <br />
                            <script type="text/javascript">
                                jQuery( document ).ready(function() {
                                    jQuery('#add_file').click(function() {
                                        jQuery(this).before('<br /><input type="file" name="file[]" />');
                                    });
                                });
                            </script>

                            <input type="file" name="file[]" />
                            <a href="javascript:void(0);" id="add_file"></a>
                        <?php } ?>
                    </div>
                    <div class="wpc_clear"></div>
                    <input type="submit" class="button-primary message_send" value="<?php _e( "Send", WPC_CLIENT_TEXT_DOMAIN ); ?>" />
                </div>
                <div class="messages_list">
                    <?php foreach( $messages as $message ) { ?>
                        <div class="msg_block left">
                            <div class="message_image">
                                <div class="object_img">
                                    <?php echo $wpc_client->cc_user_avatar( $message['send_from'] ); ?>
                                </div>
                                <br class="wpc_clear" />
                                <?php
 $userdata = get_userdata( $message['send_from'] ); if( $userdata !== false ) { echo(!empty($userdata->data->display_name) ? $userdata->data->display_name : $userdata->data->user_login); } else { _e( "Deleted user", WPC_CLIENT_TEXT_DOMAIN ); } ?>
                            </div>
                            <div class="msg_inner">
                                <b><?php echo $this->date_convert( $message['creation_date'], 'Y-m-d H:i:s', $this->get_date_format() . ' ' . $this->get_time_format() ); ?></b> ::
                                <?php echo $this->linkifyText( $this->multiString( $message['message'] ) ); $file_list = $this->get_files_for_message( $message['id'] ); if( $file_list !== false ) { ?>
                                    <ul class="files_list">
                                        <?php foreach( $file_list as $file_value ) { $file_type = $this->get_file_ext_by_filename( $file_value['file_path'] ); ?>
                                            <li>
                                                <a href="<?php echo get_admin_url() . 'admin.php?wpc_action=download&module=pm&id=' . $file_value['id'] . '&nonce=' . wp_create_nonce( get_current_user_id() . AUTH_KEY . $file_value['id'] ); ?>"><?php echo $file_value['filename']; ?></a>
                                                <a href="<?php echo get_admin_url() . 'admin.php?wpc_action=download&module=pm&id=' . $file_value['id'] . '&nonce=' . wp_create_nonce( get_current_user_id() . AUTH_KEY . $file_value['id'] ); ?>" class="pm_download_icon" title="<?php _e( 'Download', WPC_CLIENT_TEXT_DOMAIN ); ?>"></a>
                                                <?php if( ( isset( $wpc_file_sharing['view_type'] ) && 'google_doc' == $wpc_file_sharing['view_type'] && in_array( $file_type, array_keys( $wpc_client->files_for_google_doc_view ) ) ) || in_array( $file_type, $wpc_client->files_for_regular_view ) ) { ?>
                                                        <a target="_blank" href="<?php echo get_admin_url() . 'admin.php?wpc_action=view&module=pm&id=' . $file_value['id'] . '&nonce=' . wp_create_nonce( get_current_user_id() . AUTH_KEY . $file_value['id'] ); ?>" class="pm_view_icon" title="<?php _e( 'View', WPC_CLIENT_TEXT_DOMAIN ); ?>"></a>
                                                <?php } ?>
                                            </li>
                                        <?php } ?>
                                    </ul>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="wpc_clear"></div>
                    <?php } ?>
                </div>
            </form>
        </div>
    </div>
</div>