<?php
if ( ! defined( 'ABSPATH' ) ) { exit; } ?>
<script type="text/javascript">
    jQuery(document).ready(function() {
        jQuery('.add-new-file').click(function() {
            jQuery('.uploader_block').slideToggle('slow');
        });

        jQuery('#wpc_pm_author').live('change', function() {
            jQuery(this).parents('form').submit();
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
 switch( $_GET['message'] ) { case '1': _e( 'File added successfully.', WPC_CLIENT_TEXT_DOMAIN ); break; case '2': _e( 'File updated successfully.', WPC_CLIENT_TEXT_DOMAIN ); break; case '3': _e( 'File deleted successfully.', WPC_CLIENT_TEXT_DOMAIN ); break; case '4': _e( 'Wrong File ID.', WPC_CLIENT_TEXT_DOMAIN ); break; case '20': _e( 'Sorry, you have used all of your storage quota.', WPC_CLIENT_TEXT_DOMAIN ); break; case '21': _e( 'There was an error uploading the file, please try again!', WPC_CLIENT_TEXT_DOMAIN ); break; } ?>
            </p>
        </div>
        <div class="wpc_clear"></div>
    <?php } ?>
    <?php echo $this->gen_project_tabs_menu(); ?>
    <div class="wpc_clear"></div>
    <div class="wpc_pm_main_block">
        <a class="add-new-file add-new-h2" href="javascript:void(0);"><?php _e( 'Add File', WPC_CLIENT_TEXT_DOMAIN ); ?></a>
        <form class="uploader_block" action="" enctype="multipart/form-data" method="POST">
            <?php if ( isset( $wpc_pm_settings['flash_uploader'] ) && 'html5' == $wpc_pm_settings['flash_uploader'] ) { ?>
                <div id="uploader_warning" style="display: none;"></div>
                <br clear="all" />
                <div class="wpc_st_button_addfile">
                    <input class="file_upload" name="Filedata" type="file" multiple="true">
                </div>
                <div id="queue"></div>
            <?php } elseif( isset( $wpc_pm_settings['flash_uploader'] ) && 'plupload' == $wpc_pm_settings['flash_uploader'] ) { ?>
                <div class="wpc_pluploder_queue">
                    <p><?php _e( "Your browser doesn't have Flash, Silverlight or HTML5 support.", WPC_CLIENT_TEXT_DOMAIN ) ?></p>
                </div>
            <?php } else { ?>
                <div class="wpc_clear"></div>
                <input type="hidden" name="wpc_pm_action" value="upload" />
                <script type="text/javascript">
                    jQuery( document ).ready(function() {
                        jQuery('.files_block').on('click', '#add_file', function() {
                            jQuery(this).remove();
                            jQuery('.files_block:last').after('<div class="files_block">' +
                                '<input type="file" name="file[]" />' +
                                '<a href="javascript:void(0);" id="add_file"></a><br />' +
                                '<textarea name="note[]" class="note_field" rows="5" cols="50"></textarea>' +
                            '</div>');
                        });
                    });
                </script>
                <div class="files_block">
                    <input type="file" name="file[]" />
                    <a href="javascript:void(0);" id="add_file"></a><br />
                    <textarea name="note[]" class="note_field" rows="5" cols="50"></textarea>
                </div>
            <?php } ?>
            <div class="wpc_clear"></div>
            <?php if ( isset( $wpc_pm_settings['flash_uploader'] ) && 'html5' == $wpc_pm_settings['flash_uploader'] ) { ?>
            <input type="button" class="button-primary" onclick="jQuery('.file_upload').uploadifive('upload');" value="<?php _e( "Upload", WPC_CLIENT_TEXT_DOMAIN ); ?>" />
            <?php } elseif( !( isset( $wpc_pm_settings['flash_uploader'] ) && 'plupload' == $wpc_pm_settings['flash_uploader'] ) ) { ?>
            <input type="submit" class="button-primary" value="<?php _e( "Upload", WPC_CLIENT_TEXT_DOMAIN ); ?>" />
            <?php } ?>
        </form>
        <div class="wpc_clear"></div>
        <form class="filter_form">
            <div style="width: 300px; float: left;">
                <label>
                    <?php _e( "Filter by Author", WPC_CLIENT_TEXT_DOMAIN ); ?>
                    <select id="wpc_pm_author" name="wpc_pm_author">
                        <option value=""><?php _e( 'All', WPC_CLIENT_TEXT_DOMAIN ); ?></option>
                        <?php foreach( $authors as $id=>$name ) { ?>
                            <option value="<?php echo $id; ?>" <?php selected($id, isset( $_GET['wpc_pm_author'] ) ? $_GET['wpc_pm_author'] : ''); ?>><?php echo $name; ?></option>
                        <?php } ?>
                    </select>
                </label>
            </div>
            <input type="hidden" name="page" value="wpc_project_management" />
            <input type="hidden" name="action" value="details" />
            <input type="hidden" name="project_id" value="<?php echo( isset( $_GET['project_id'] ) ? $_GET['project_id'] : '' ); ?>" />
            <input type="hidden" name="sub_tab" value="files" />

            <div style="width: 300px; float: right;">
                <input type="search" id="wpc_pm_search-input" name="s" value="<?php echo( isset( $_GET['s'] ) ? $_GET['s'] : '' ); ?>">
                <input type="submit" name="" id="wpc_pm_search-submit" class="button" value="Search Files">
            </div>
        </form>
        <form action="" method="POST">
            <?php
 $ListTable->display(); ?>
        </form>
    </div>
</div>