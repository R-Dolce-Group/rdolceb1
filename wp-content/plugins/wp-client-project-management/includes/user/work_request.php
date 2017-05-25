<?php
if ( ! defined( 'ABSPATH' ) ) { exit; } if( isset( $_GET['message'] ) ) { ?>
    <div id="message" class="wpc_notice <?php if( $_GET['message'] == '1' ) {?>wpc_apply<?php } else { ?>wpc_error<?php } ?>">
        <?php switch( $_GET['message'] ) { case '1': _e( 'Work request successfully sent.', WPC_CLIENT_TEXT_DOMAIN ); break; case '2': _e( 'Work request title is required.', WPC_CLIENT_TEXT_DOMAIN ); break; case '20': _e( 'Sorry, you have used all of your storage quota.', WPC_CLIENT_TEXT_DOMAIN ); break; case '21': _e( 'There was an error uploading the file, please try again!', WPC_CLIENT_TEXT_DOMAIN ); break; } ?>
    </div>
<?php } ?>
<form method="POST" action="" id="wpc_pm_work_request" enctype="multipart/form-data" class="wpc_form">
    <div class="wpc_form_line">
        <div class="wpc_form_label">
            <label data-title="<?php _e( 'Title', WPC_CLIENT_TEXT_DOMAIN ) ?>" for="wpc_pm_title">
                <?php _e( 'Title', WPC_CLIENT_TEXT_DOMAIN ) ?>
                <?php _e( ' <span style="color:red;" title="This field is marked as required by the administrator.">*</span>', WPC_CLIENT_TEXT_DOMAIN ) ?>
            </label>
        </div>
        <div class="wpc_form_field">
            <input type="text" name="title" class="title" id="wpc_pm_title" data-required_field="1" value="<?php echo ( isset( $_POST['title'] ) ) ? $_POST['title'] : '' ?>" />
            <div class="wpc_field_validation">
                <span class="wpc_field_required"><?php _e( 'Ticket Title is required', WPC_CLIENT_TEXT_DOMAIN ) ?></span>
            </div>
        </div>
    </div>
    <?php if( count( $request_types ) ) { ?>
        <div class="wpc_form_line">
            <div class="wpc_form_label">
                <label data-title="<?php _e( 'Type', WPC_CLIENT_TEXT_DOMAIN ) ?>" for="wpc_pm_request_type">
                    <?php _e( 'Type', WPC_CLIENT_TEXT_DOMAIN ) ?>
                </label>
            </div>
            <div class="wpc_form_field">
                <select name="request_type" class="request_type" id="wpc_pm_request_type">
                    <option value=""><?php _e( 'None', WPC_CLIENT_TEXT_DOMAIN ); ?></option>
                    <?php foreach( $request_types as $key=>$val ) { ?>
                        <option value="<?php echo $key; ?>">
                            <?php echo $val['title']; if( $val['price'] != '' && $val['currency'] != '' ) { ?>
                                (<?php echo $wpc_client->cc_set_currency( $val['price'], $val['currency'], false ); ?>)
                            <?php } ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
        </div>
    <?php } ?>

    <div class="wpc_form_line">
        <div class="wpc_form_label">
            <label data-title="<?php _e( 'Attachments', WPC_CLIENT_TEXT_DOMAIN ) ?>" for="wpc_pm_attachment">
                <?php _e( 'Attachments', WPC_CLIENT_TEXT_DOMAIN ) ?>
            </label>
        </div>
        <div class="wpc_form_field">
            <?php if ( isset( $wpc_pm_settings['flash_uploader'] ) && 'html5' == $wpc_pm_settings['flash_uploader'] ) { ?>
                <div id="uploader_warning" style="display: none;"></div>
                <div class="wpc_pm_button_addfile wpc_button" style="float: left; width: 100%;">
                    <input class="work_request_file_upload" id="work_request_file_upload" name="Filedata" type="file" multiple="true">
                </div>
                <div id="queue" style="float: left; width: 100%;"></div>
            <?php } elseif( isset( $wpc_pm_settings['flash_uploader'] ) && 'plupload' == $wpc_pm_settings['flash_uploader'] ) { ?>
                <div class="wpc_request_pluploder_queue">
                    <p><?php _e( "Your browser doesn't have Flash, Silverlight or HTML5 support.", WPC_CLIENT_TEXT_DOMAIN ) ?></p>
                </div>
            <?php } else { ?>
                <div class="wpc_clear"></div>
                <input type="hidden" name="wpc_pm_action" value="upload" />
                <script type="text/javascript">
                    jQuery( document ).ready(function() {
                        jQuery('body').on('click', '#add_file', function() {
                            jQuery(this).remove();
                            jQuery('.uploader_block:last').after('<div class="uploader_block">' +
                                '<a href="javascript:void(0);" id="add_file" title="<?php _e( 'Add File', WPC_CLIENT_TEXT_DOMAIN ) ?>"></a>' +
                                '<input type="file" name="file[]" />' +
                                '</div>');
                        });
                    });
                </script>
                <div class="uploader_block">
                    <a href="javascript:void(0);" id="add_file" title="<?php _e( 'Add File', WPC_CLIENT_TEXT_DOMAIN ) ?>"></a>
                    <input type="file" name="file[]" />
                </div>
            <?php } ?>
        </div>
    </div>

    <div class="wpc_form_line">
        <div class="wpc_form_label">
            <label data-title="<?php _e( 'Description', WPC_CLIENT_TEXT_DOMAIN ) ?>" for="wpc_pm_message">
                <?php _e( 'Description', WPC_CLIENT_TEXT_DOMAIN ) ?>
            </label>
        </div>
        <div class="wpc_form_field">
            <textarea name="message" class="message" rows="5" id="wpc_pm_message"><?php echo ( isset( $_POST['message'] ) ) ? $_POST['message'] : '' ?></textarea>
        </div>
    </div>

    <div class="wpc_form_line">
        <div class="wpc_form_label">
            &nbsp;
        </div>
        <div class="wpc_form_field">
            <input type="submit" class="button-primary request_send wpc_submit" value="<?php _e( "Send", WPC_CLIENT_TEXT_DOMAIN ); ?>" style="float:left;" />
            <div class="wpc_submit_info" style="float: left;margin-left:10px; line-height: 44px;"></div>
        </div>
    </div>
</form>

<script type="text/javascript" language="javascript">

    jQuery( document ).ready( function($) {
        var form = $( "#wpc_pm_work_request" );

        if( form.find('*[data-required_field="1"]').length > 0 ) {
            form.find('input[type="submit"]').prop('disabled', true).attr('disabled',true);
            infoSubmit();
        }

        //input fields
        form.find('input').focusout( function() {
            //check field on required value
            var field = $(this).parents('.wpc_form_field');
            if( $(this).data('required_field') ) {
                //another fields
                if ( $(this).val() == '' ) {
                    //if field empty
                    showValidationMessage( field, 'required' );
                } else {
                    //if field not empty
                    //check field content
                    hideValidationMessage( field );
                }

                triggerSubmit();
            }
        });

        form.on('keyup', '.wpc_form_field.wpc_validate_error input', function() {
            //check field on required value
            var field = $(this).parents('.wpc_form_field');

            if( $(this).data('required_field') && $(this).val() == '' ) {
                //if field required and empty
                showValidationMessage( field, 'required' );
            } else {
                //if field not required or required and not empty
                //check field content
                hideValidationMessage( field );
            }

            triggerSubmit();
        });

        function showValidationMessage( field, type ) {
            field.find( '.wpc_field_validation' ).children().hide();
            field.find( '.wpc_field_' + type ).show();
            field.addClass( 'wpc_validate_error' );
        }

        function hideValidationMessage( field ) {
            field.find( '.wpc_field_validation' ).children().hide();
            field.removeClass( 'wpc_validate_error' );
        }


        function triggerSubmit() {
            if( form.find('*[data-required_field="1"]').length > 0 ) {
                var validated = 0;

                form.find('*[data-required_field="1"]').each(function () {
                    if (jQuery(this).val() != '') {
                        //if field not empty
                        //check field content
                        validated++;
                    }
                });
                if( form.find('*[data-required_field="1"]').length == validated ) {
                    form.find('input[type="submit"]').prop('disabled',false).attr('disabled',false);
                } else {
                    form.find('input[type="submit"]').prop('disabled',true).attr('disabled',true);
                }
            } else {
                form.find('input[type="submit"]').prop('disabled',false).attr('disabled',false);
            }

            infoSubmit();
        }

        function infoSubmit() {
            var html = '';
            if( form.find('*[data-required_field="1"]').length > 0 ) {
                form.find('*[data-required_field="1"]').each(function () {
                    var label = form.find('label[for="' + jQuery(this).attr('id') + '"]').data('title');
                    if (jQuery(this).val() == '') {
                        html = '<?php _e( 'You need to fill', WPC_CLIENT_TEXT_DOMAIN ) ?> "<a href="#' + jQuery(this).attr('id') + '">' + label + '</a>"';
                        return false;
                    }
                });
            }

            if( form.find('.wpc_submit_info').html() != html ) {
                form.find('.wpc_submit_info').html(html);
            }
        }
    });

</script>