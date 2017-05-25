jQuery(document).ready(function() {
    //file upload
    jQuery( '.file_upload' ).uploadifive({
        'auto'              : false,
        'sizeLimit'         : html5uploader.file_size_limit,
        'itemTemplate'      : '<div class="uploadifive-queue-item"><a class="close" href="#">X</a><div><span class="filename"></span><span class="fileinfo"></span></div><div class="progress"><div class="progress-bar"></div></div><textarea name="note[]" class="note_field" rows="3" cols="50"></textarea></div>',
        'formData'          : {},
        'queueID'           : 'queue',
        'uploadScript'      : html5uploader.admin_url + 'admin-ajax.php?action=wpc_pm_upload_files',
        'onUploadComplete' : function(file, data) {
            if( isNaN( data ) ) {
                jQuery( file.queueItem[0] ).find('.close').trigger('click');
                jQuery('#uploader_warning').html( data );
                jQuery('#uploader_warning').show();
                setTimeout(function() {
                    jQuery('#uploader_warning').hide('slow');
                    jQuery('#uploader_warning').html('');
                }, 5000);
            } else {
                jQuery( file.queueItem[0] ).append('<input type="hidden" name="file_id[]" class="file_ids" value="' + data + '" />');
            }
            return false;
        },
        'onQueueComplete' : function(file, data) {
            jQuery('.uploader_block').submit();
            return false;
        }
    });
    //file upload
    jQuery( '.work_request_file_upload' ).uploadifive({
        'auto'              : true,
        'sizeLimit'         : html5uploader.file_size_limit,
        'formData'          : {},
        'queueID'           : 'queue',
        'uploadScript'      : html5uploader.admin_url + 'admin-ajax.php?action=wpc_pm_upload_files',
        'onUploadComplete' : function(file, data) {
            if( isNaN( data ) ) {
                jQuery( file.queueItem[0] ).find('.close').trigger('click');
                jQuery('#uploader_warning').html( data );
                jQuery('#uploader_warning').show();
                setTimeout(function() {
                    jQuery('#uploader_warning').hide('slow');
                    jQuery('#uploader_warning').html('');
                }, 5000);
            } else {
                jQuery( file.queueItem[0] ).append('<input type="hidden" name="file_id[]" class="file_ids" value="' + data + '" />');
            }
            return false;
        },
        'onQueueComplete' : function(file, data) {
            jQuery('.uploader_block').submit();
            return false;
        }
    });

    jQuery( '#uploadifive-work_request_file_upload').addClass('wpc_button');

    //file upload
    jQuery( '.talk_file_upload' ).uploadifive({
        'auto'              : true,
        'formData'          : {},
        'queueID'           : 'queue',
        'uploadScript'      : html5uploader.admin_url + 'admin-ajax.php?action=wpc_pm_upload_files',
        'onUploadComplete' : function(file, data) {
            if( isNaN( data ) ) {
                jQuery( file.queueItem[0] ).find('.close').trigger('click');
                jQuery('#uploader_warning').html( data );
                jQuery('#uploader_warning').show();
                setTimeout(function() {
                    jQuery('#uploader_warning').hide('slow');
                    jQuery('#uploader_warning').html('');
                }, 5000);
            } else {
                jQuery( file.queueItem[0] ).append('<input type="hidden" name="file_id[]" class="file_ids" value="' + data + '" />');
            }
            return false;
        },
        'onQueueComplete' : function(file, data) {
            jQuery('.uploader_block').submit();
            return false;
        }
    });

    jQuery( '#uploadifive-talk_file_upload').addClass('wpc_button');

    //file upload
    jQuery( '.file_upload_ajax' ).uploadifive({
        'auto'              : false,
        'sizeLimit'         : html5uploader.file_size_limit,
        'itemTemplate'      : '<div class="uploadifive-queue-item"><a class="close" href="#">X</a><div><span class="filename"></span><span class="fileinfo"></span></div><div class="progress"><div class="progress-bar"></div></div><textarea name="note[]" class="note_field" rows="3" cols="50"></textarea></div>',
        'formData'          : {},
        'queueID'           : 'queue',
        'uploadScript'      : html5uploader.admin_url + 'admin-ajax.php?action=wpc_pm_upload_files',
        'onUploadComplete' : function(file, data) {
            if( isNaN( data ) ) {
                jQuery( file.queueItem[0] ).find('.close').trigger('click');
                jQuery('#uploader_warning').html( data );
                jQuery('#uploader_warning').show();
                setTimeout(function() {
                    jQuery('#uploader_warning').hide('slow');
                    jQuery('#uploader_warning').html('');
                }, 5000);
            } else {
                jQuery( file.queueItem[0] ).append('<input type="hidden" name="file_id[]" class="file_ids" value="' + data + '" />');
            }
            return false;
        },
        'onQueueComplete' : function(file, data) {
            var data = new Array();
            jQuery('.uploadifive-queue-item').each(function( index ) {
                data.push( { description: jQuery(this).find('.note_field').val(), id: jQuery(this).find('.file_ids').val() } );

            });

            if( data.length > 0 ) {
                jQuery.ajax({
                    type     : 'POST',
                    dataType : 'json',
                    url      : html5uploader.admin_url + '/admin-ajax.php',
                    data     : {'action': 'wpc_pm_task', 'act': 'assign_files', 'task_id': wpc_pm_selected_task, 'data': data },
                    success  : function( data ){
                        if( data.status ) {
                            jQuery('#div_task_messages_tab .task_messages_list').prepend( data.message );
                            jQuery('#queue').html('');
                            jQuery('#uploader_warning').html('');
                            jQuery('#uploader_warning').hide();
                            jQuery('#task_files_tab a').trigger('click');
                        } else {
                            jQuery( '.task_row .task_status .dropdown-toggle' ).html( previous_value );
                            alert( data.message );
                        }
                        jQuery('.task_files_submit').removeAttr('disabled');
                    }
                });
            } else {
                jQuery('.task_files_submit').removeAttr('disabled');
            }

            return false;
        }
    });

    //file upload
    /*jQuery( '.file_upload' ).uploadifive({
        'auto'              : true,
        'sizeLimit'         : html5uploader.file_size_limit,
        'formData'          : {},
        'queueID'           : 'queue',
        'uploadScript'      : html5uploader.admin_url + 'admin-ajax.php?action=wpc_pm_upload_files',
        'onUploadComplete' : function(file, data) {
            if( isNaN( data ) ) {
                jQuery( file.queueItem[0] ).find('.close').trigger('click');
                jQuery('#uploader_warning').html( data );
                jQuery('#uploader_warning').show();
                setTimeout(function() {
                    jQuery('#uploader_warning').hide('slow');
                    jQuery('#uploader_warning').html('');
                }, 5000);
            } else {
                jQuery( file.queueItem[0] ).append('<input type="hidden" name="file_id[]" class="file_ids" value="' + data + '" />' +
                '<textarea name="note[]" class="note_field" rows="5" cols="50"></textarea>');
            }
            return false;
        }
    }); */
    //file upload
    jQuery( '.file_upload_messages' ).uploadifive({
        'auto'              : true,
        'sizeLimit'         : html5uploader.file_size_limit,
        'formData'          : {},
        'queueID'           : 'queue_messages',
        'uploadScript'      : html5uploader.admin_url + 'admin-ajax.php?action=wpc_pm_upload_files',
        'onUploadComplete' : function(file, data) {
            if( isNaN( data ) ) {
                jQuery( file.queueItem[0] ).find('.close').trigger('click');
                jQuery('#uploader_warning_messages').html( data );
                jQuery('#uploader_warning_messages').show();
                setTimeout(function() {
                    jQuery('#uploader_warning_messages').hide('slow');
                    jQuery('#uploader_warning_messages').html('');
                }, 5000);
            } else {
                jQuery( file.queueItem[0] ).append('<input type="hidden" name="file_id[]" class="file_ids" value="' + data + '" />');
            }
            return false;
        }
    });

});