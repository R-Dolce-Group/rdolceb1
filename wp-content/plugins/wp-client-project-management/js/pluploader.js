jQuery( function() {

    //file upload
    jQuery(".wpc_pluploder_queue").pluploadQueue({
        runtimes : 'html5,flash,silverlight,html4',
        url : wpc_pm_pluploader.admin_url + 'admin-ajax.php?action=wpc_pm_plupload_upload_files',
        chunk_size : '1mb',
        max_retries: 3,
        rename : true,
        dragdrop: true,
        init : {
            FilesAdded: function(uploader, files) {
                for( key in files ) {
                    jQuery( '#' + files[ key ].id ).append('<textarea name="note[]" id="note_' + files[ key ].id + '" class="note_field" rows="3" cols="50"></textarea>');
                }
            },
            BeforeUpload: function(uploader, file) {
                uploader.settings.url = wpc_pm_pluploader.admin_url + 'admin-ajax.php?action=wpc_pm_plupload_upload_files&object_id=' + wpc_pm_pluploader.project_id + '&description=' + jQuery('#note_' + file.id).val();
            },
            UploadComplete: function(up, files) {
                self.location.href="admin.php?page=wpc_project_management&action=details&project_id=" + wpc_pm_pluploader.project_id + "&sub_tab=files";
                return false;
            }
        },
        // Flash settings
        flash_swf_url : wpc_pm_pluploader.plugin_url + 'js/plupload/Moxie.swf',

        // Silverlight settings
        silverlight_xap_url : wpc_pm_pluploader.plugin_url + 'js/plupload/Moxie.xap'
    });

    //file upload
    jQuery(".wpc_request_pluploder_queue").pluploadQueue({
        runtimes : 'html5,flash,silverlight,html4',
        url : wpc_pm_pluploader.admin_url + 'admin-ajax.php?action=wpc_pm_plupload_upload_files',
        chunk_size : '1mb',
        max_retries: 3,
        rename : true,
        dragdrop: true,
        multiple_queues : true,
        init : {
            FilesAdded: function(uploader, files) {
                uploader.start();
            },
            FileUploaded: function(uploader, file, data) {
                var response = jQuery.parseJSON(data.response);
                if( data.status == 200 ) {
                    if( typeof response.id != 'undefined' && !isNaN( response.id ) ) {
                        jQuery( '#' + file.id ).parents('.wpc_request_pluploder_queue').append('<input type="hidden" name="file_id[]" id="' + file.id + '" class="file_ids" value="' + response.id + '" />');
                    }
                    return true;
                } else {
                    return false;
                }
            },
            Error: function(up, args) {
                // Called when error occurs
                console.log('[Error] ', args);
            }
        },
        // Flash settings
        flash_swf_url : wpc_pm_pluploader.plugin_url + 'js/plupload/Moxie.swf',

        // Silverlight settings
        silverlight_xap_url : wpc_pm_pluploader.plugin_url + 'js/plupload/Moxie.xap'
    });

    //file upload
    jQuery(".wpc_pluploder_task_files_queue").pluploadQueue({
        runtimes : 'html5,flash,silverlight,html4',
        url : wpc_pm_pluploader.admin_url + 'admin-ajax.php?action=wpc_pm_plupload_upload_files',
        chunk_size : '1mb',
        max_retries: 3,
        rename : true,
        dragdrop: true,
        multiple_queues : true,
        init : {
            FilesAdded: function(uploader, files) {
                for( key in files ) {
                    jQuery( '#' + files[ key ].id ).append('<textarea name="note[]" id="note_' + files[ key ].id + '" class="note_field" rows="3" cols="50"></textarea>');
                }
            },
            BeforeUpload: function(uploader, file) {
                uploader.settings.url = wpc_pm_pluploader.admin_url + 'admin-ajax.php?action=wpc_pm_plupload_upload_files&type=task&object_id=' + wpc_pm_selected_task + '&description=' + jQuery('#note_' + file.id).val();
            },
            UploadComplete: function(up, files) {
                up.splice();
                jQuery('span.plupload_upload_status').html('');
                jQuery('#task_files_tab a').trigger('click');
                return false;
            }
        },
        // Flash settings
        flash_swf_url : wpc_pm_pluploader.plugin_url + 'js/plupload/Moxie.swf',

        // Silverlight settings
        silverlight_xap_url : wpc_pm_pluploader.plugin_url + 'js/plupload/Moxie.xap'
    });

    //file upload
    jQuery(".wpc_pluploder_task_messages_queue").pluploadQueue({
        runtimes : 'html5,flash,silverlight,html4',
        url : wpc_pm_pluploader.admin_url + 'admin-ajax.php?action=wpc_pm_plupload_upload_files',
        chunk_size : '1mb',
        max_retries: 3,
        rename : true,
        dragdrop: true,
        multiple_queues : true,
        init : {
            FilesAdded: function(uploader, files) {
                uploader.start();
            },
            FileUploaded: function(uploader, file, data) {
                var response = jQuery.parseJSON(data.response);
                if( data.status == 200 ) {
                    if( typeof response.id != 'undefined' && !isNaN( response.id ) ) {
                        jQuery( '#' + file.id ).parents('.wpc_pluploder_task_messages_queue').append('<input type="hidden" name="file_id[]" id="' + file.id + '" class="file_ids" value="' + response.id + '" />');
                    }
                    return true;
                } else {
                    return false;
                }
            },
            Error: function(up, args) {
                // Called when error occurs
                console.log('[Error] ', args);
            }
        },
        // Flash settings
        flash_swf_url : wpc_pm_pluploader.plugin_url + 'js/plupload/Moxie.swf',

        // Silverlight settings
        silverlight_xap_url : wpc_pm_pluploader.plugin_url + 'js/plupload/Moxie.xap'
    });

    //file upload
    jQuery(".wpc_pluploder_talk_queue").pluploadQueue({
        runtimes : 'html5,flash,silverlight,html4',
        url : wpc_pm_pluploader.admin_url + 'admin-ajax.php?action=wpc_pm_plupload_upload_files',
        chunk_size : '1mb',
        max_retries: 3,
        rename : true,
        dragdrop: true,
        multiple_queues : true,
        init : {
            FilesAdded: function(uploader, files) {
                uploader.start();
            },
            FileUploaded: function(uploader, file, data) {
                var response = jQuery.parseJSON(data.response);
                if( data.status == 200 ) {
                    if( typeof response.id != 'undefined' && !isNaN( response.id ) ) {
                        jQuery( '#' + file.id ).parents('.wpc_pluploder_talk_queue').append('<input type="hidden" name="file_id[]" id="' + file.id + '" class="file_ids" value="' + response.id + '" />');
                    }
                    return true;
                } else {
                    return false;
                }
            },
            Error: function(up, args) {
                // Called when error occurs
                console.log('[Error] ', args);
            }
        },
        // Flash settings
        flash_swf_url : wpc_pm_pluploader.plugin_url + 'js/plupload/Moxie.swf',

        // Silverlight settings
        silverlight_xap_url : wpc_pm_pluploader.plugin_url + 'js/plupload/Moxie.xap'
    });


});