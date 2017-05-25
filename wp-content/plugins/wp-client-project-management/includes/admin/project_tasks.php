<?php
if ( ! defined( 'ABSPATH' ) ) { exit; } ?>

<script type="text/javascript">
    var ajax_data = new Array();
    var preFillFollowersObject = new Array();
    var wpc_pm_selected_task = 0;
    var wpc_pm_subtask_flag = 0;
    var preFillFollowersIndex = 0;
    var exclude_task = new Array();
    <?php if( isset( $settings['auto_hide_days'] ) && is_numeric( $settings['auto_hide_days'] ) && $settings['auto_hide_days'] > 0 ) { ?>
        var hide_completed_flag = 1;
    <?php } else { ?>
        var hide_completed_flag = 0;
    <?php } ?>

    var assigned_user_filter = new Array();
    var priority_filter = new Array();
    var status_filter = new Array();
    var tags_filter = new Array();
    var timer_id = 0;
    var order_by = '';
    var order_dir = 'asc';

    jQuery( document ).ready(function() {

        var window_height = jQuery( window ).height() - jQuery('.task_table').offset().top;
        var menu_height = jQuery('#adminmenu').height() - jQuery('.task_table').offset().top;

        jQuery('.task_form .task_tabs_content').hide();

        var full_width = jQuery('.wpc_pm_main_block').width();
        jQuery('.wpc_left_column').css('width', parseInt( full_width * 0.55 ) + 'px');
        jQuery('.wpc_right_column').css('width', parseInt( full_width * 0.44 ) + 'px');

        jQuery('.wpc_pm_task, .bulk_actions_bar').hide();

        jQuery('.add-new-task').qtip();

        jQuery( window ).resize(function(){
            var full_width = jQuery('.wpc_pm_main_block').width();
            jQuery('.wpc_left_column').css('width', parseInt( full_width * 0.55 ) + 'px');
            jQuery('.wpc_right_column').css('width', parseInt( full_width * 0.44 ) + 'px');
        });

        var change_flag = 0;
        jQuery('.task_form .tags').tokenInput('<?php echo get_admin_url(); ?>admin-ajax.php?action=wpc_pm_get_tags', {
            preventDuplicates: true,
            method: 'POST',
            hintText: '<?php _e( 'Please enter tags', WPC_CLIENT_TEXT_DOMAIN ); ?>',
            onResult: function (results) {
                var obj = jQuery(this);
                jQuery.each(results, function (index, value) {
                    results[ index ].value = value.name;
                    if( value.id == 0 ) {
                        results[ index ].id = '-' + ( obj.siblings('.token-input-list').find('.token-input-token').length * 1 + 1 );
                        results[ index ].name = "<?php _e( 'Add new tag:', WPC_CLIENT_TEXT_DOMAIN ); ?> " + value.name;
                    }
                });

                return results;
            },
            onAdd: function (item) {
                if( change_flag ) {
                    return '';
                }
                if( item.id > 0 ) {
                    var param = '&type=exists&object=' + item.id;
                } else {
                    var param = '&type=new&object=' + item.value;
                }
                var obj = jQuery(this);
                var item_id = item.id;

                jQuery.ajax({
                    type     : 'POST',
                    dataType : 'json',
                    url      : '<?php echo get_admin_url(); ?>admin-ajax.php',
                    data     : 'action=wpc_pm_get_tags&act=add&task_id=' + wpc_pm_selected_task + param,
                    success  : function( data ){
                        if( data.status ) {
                            if( item.id <= 0 ) {
                                change_flag = 1;
                                obj.tokenInput("clear");
                                for( key in data.message ) {
                                    obj.tokenInput( "add", data.message[ key ] );
                                }
                                change_flag = 0;
                                item_id = data.message[ data.message.length - 1 ]['id'];
                            }
                            var die_date_tag = jQuery('.task_id[value=' + wpc_pm_selected_task + ']').siblings('.task_tag').children('.die_date_tag').clone();
                            jQuery('.task_id[value=' + wpc_pm_selected_task + ']').siblings('.task_tag').html( data.left_side_tags );
                            jQuery('.task_id[value=' + wpc_pm_selected_task + ']').siblings('.task_tag').append( die_date_tag );

                            if( jQuery('select.tags_filter option[value="' + item_id + '"]').length == 0 ) {
                                $opt = jQuery("<option />", {
                                    value: item_id,
                                    text: item.value
                                });
                                jQuery('select.tags_filter').append( $opt ).multipleSelect("refresh");
                            }

                        } else {
                            alert( data.message );
                        }
                    }
                });
            },
            onDelete: function (item) {
                var param = '';
                if( change_flag ) {
                    return '';
                }
                //console.log(item);
                if( item.id > 0 ) {
                    param = '&object=' + item.id;
                }
                jQuery.ajax({
                    type     : 'POST',
                    dataType : 'json',
                    url      : '<?php echo get_admin_url(); ?>admin-ajax.php',
                    data     : 'action=wpc_pm_get_tags&act=delete&task_id=' + wpc_pm_selected_task + param,
                    success  : function( data ){
                        if( data.status ) {
                            var die_date_tag = jQuery('.task_id[value=' + wpc_pm_selected_task + ']').siblings('.task_tag').children('.die_date_tag').clone();
                            jQuery('.task_id[value=' + wpc_pm_selected_task + ']').siblings('.task_tag').html( data.left_side_tags );
                            jQuery('.task_id[value=' + wpc_pm_selected_task + ']').siblings('.task_tag').append( die_date_tag );
                        } else {
                            alert( data.message );
                        }
                    }
                });
            },
            tokenFormatter: function(item) {
                return "<li><p>" + item.value + "</p></li>";
            }
        });

        jQuery('#div_task_description_tab').show();
        jQuery('.task_details_menu li a').click(function() {
            jQuery('.task_details_menu li.active').removeClass('active');
            jQuery(this).parent().addClass('active');
            var id = jQuery(this).parent().attr('id');
            jQuery('.task_tabs_content').hide();
            jQuery('#div_' + id).show();

            var full_width = jQuery('.wpc_pm_main_block').width();
            jQuery('.wpc_left_column').css('width', parseInt( full_width * 0.55 ) + 'px');
            jQuery('.wpc_right_column').css('width', parseInt( full_width * 0.44 ) + 'px');
        });

        jQuery( '.dropdown-toggle' ).dropdown();
        jQuery( '.dropdown-toggle' ).qtip();
        jQuery( '.assign_dropdown .dropdown-menu a' ).click(function() {
            var user_id = jQuery( this ).data('id');
            var color = jQuery( this ).data('color');
            var short_name = jQuery( this ).data('short-name');
            var full_name = jQuery( this ).data('full-name');
            var previous_value = jQuery( '.wpc_pm_task .assign_dropdown .dropdown-toggle' ).html();
            var name = jQuery( this ).data('name');
            name = name.substr( 0, 10 ) + ( name.length > 10 ? '...' : '' );
            jQuery(this).parents( '.assign_dropdown' ).find( '.dropdown-toggle' ).html('<span class="icon color-' + color + '">' + short_name + '</span>' + name + '<?php if( current_user_can('wpc_pm_level_3') ) { ?><span class="caret"></span><?php } ?>');
            jQuery('.assign_dropdown .close-but').show();
            jQuery(this).parents( '.assign_dropdown' ).find( '.dropdown-toggle' ).attr( 'title', full_name );

            var current_tasks = new Array();
            if( jQuery('.bulk_id:checked').length > 1 ) {
                jQuery('.bulk_id:checked').each(function() {
                    current_tasks.push( jQuery(this).val() );
                });
            } else {
                current_tasks.push( wpc_pm_selected_task );
            }

            if( current_tasks.length ) {
                jQuery.ajax({
                    type     : 'POST',
                    dataType : 'json',
                    url      : '<?php echo get_admin_url(); ?>admin-ajax.php',
                    data     : 'action=wpc_pm_task&act=assign_user&task_id=' + current_tasks.join(',') + '&user_id=' + user_id,
                    success  : function( data ){
                        if( data.status ) {
                            for (var key in current_tasks) {
                              jQuery('.task_row .task_id[value="'+ current_tasks[ key ] + '"]').siblings('.task_assign').html('<a href="javascript:void(0);" class="user color-' + color + '">' + short_name + '</a>');
                            }
                        } else {
                            jQuery( '.wpc_pm_task .assign_dropdown .dropdown-toggle' ).html( previous_value );
                            alert( data.message );
                        }
                    }
                 });
            }

        });

        jQuery( '.milestone_dropdown .dropdown-menu a' ).click(function() {
            var milestone_id = jQuery( this ).data('id');
            var title = jQuery( this ).data('name');
            var old_html = jQuery(this).parents( '.milestone_dropdown' ).find( '.dropdown-toggle' ).html();
            jQuery(this).parents( '.milestone_dropdown' ).find( '.dropdown-toggle' ).html( title + '<?php if( current_user_can('wpc_pm_level_3') ) { ?><span class="caret"></span><?php } ?>');
            jQuery('.milestone_dropdown .close-but').show();
            jQuery(this).parents( '.milestone_dropdown' ).find( '.dropdown-toggle' ).attr( 'title', title );

            var current_tasks = new Array();
            if( jQuery('.bulk_id:checked').length > 1 ) {
                jQuery('.bulk_id:checked').each(function() {
                    current_tasks.push( jQuery(this).val() );
                });
            } else {
                current_tasks.push( wpc_pm_selected_task );
            }

            if( current_tasks.length ) {
                jQuery.ajax({
                    type     : 'POST',
                    dataType : 'json',
                    url      : '<?php echo get_admin_url(); ?>admin-ajax.php',
                    data     : 'action=wpc_pm_task&act=assign_milestone&task_id=' + current_tasks.join(',') + '&milestone_id=' + milestone_id,
                    success  : function( data ){
                        if( data.status ) {

                        } else {
                            jQuery( '.wpc_pm_task .milestone_dropdown .dropdown-toggle' ).html( old_html );
                            alert( data.message );
                        }
                    }
                 });
            }

        });


        jQuery( '.add-new-task').shutter_box({
            view_type       : 'lightbox',
            width           : '430px',
            type            : 'inline',
            href            : '#add_task_box',
            title           : '<?php _e( 'Add new task', WPC_CLIENT_TEXT_DOMAIN ) ?>',
            onClose         : function() {
                jQuery('.task_add_button').removeAttr('disabled');
            }
        });

        jQuery( '.add_subtask').each( function(){
            jQuery(this).shutter_box({
                view_type       : 'lightbox',
                width           : '430px',
                type            : 'inline',
                href            : '#add_task_box',
                title           : '<?php _e( "Add new Subtask to", WPC_CLIENT_TEXT_DOMAIN ) ?>',
                inlineBeforeLoad : function() {
                    wpc_pm_subtask_flag = 1;
                    jQuery( '.sb_lightbox_content_title').append( ' "' + jQuery('.task_form .title').html() + '"' );
                },
                onClose         : function() {
                    jQuery('.task_add_button').removeAttr('disabled');
                    wpc_pm_subtask_flag = 0;
                }
            });
        });

        var $dp = jQuery("<input type='text' class='wpc_pm_picker' />").css('width', 0).css('opacity', 0).css('position', 'absolute').datepicker({
            showAnim : 'slideDown',
            changeMonth: true,
            changeYear: true,
            minDate: 0,
            onSelect : function() {
                var d = jQuery(this).datepicker('getDate');
                d.setHours(0, -d.getTimezoneOffset(), 0, 0);
                var die_date = d.getTime() / 1000;
                jQuery(this).next().val( die_date );

                var obj = jQuery(this);
                var previous_data = obj.siblings('.die_date_text').val();
                var die_date_text = jQuery(this).val();

                var current_tasks = new Array();
                if( jQuery('.bulk_id:checked').length > 1 ) {
                    jQuery('.bulk_id:checked').each(function() {
                        current_tasks.push( jQuery(this).val() );
                    });
                } else {
                    current_tasks.push( wpc_pm_selected_task );
                }

                jQuery.ajax({
                    type     : 'POST',
                    dataType : 'json',
                    url      : '<?php echo get_admin_url(); ?>admin-ajax.php',
                    data     : 'action=wpc_pm_task&act=change_die_date&task_id=' + current_tasks.join(',') + '&die_date=' + die_date,
                    success  : function( data ){
                        if( data.status ) {
                            if( current_tasks.length == 1 ) {
                                jQuery('.die_date_link  .die_date_text').html( die_date_text );
                                jQuery( '.die_date_outer .close-but' ).removeAttr('style');
                            }
                            var type_date_class = '';
                            var date = new Date();
                            date.setTime( ( die_date * 1 + d.getTimezoneOffset()*60 ) * 1000 );
                            var current_date = new Date();
                            if( date.getTime() < current_date.getTime() - 3600*24*1000 ) {
                                type_date_class = ' overdue';
                            }

                            for( key in current_tasks ) {
                                jQuery('.task_id[value=' + current_tasks[ key ] + ']').siblings('.task_tag').children('.die_date_tag').remove();
                                jQuery('.parent_id[value=' + current_tasks[ key ] + ']').siblings('.task_tag').children('.die_date_tag').remove();
                                if( !( jQuery('.task_id[value=' + current_tasks[ key ] + ']').parent().hasClass('completed_task') ||
                                    jQuery('.task_id[value=' + current_tasks[ key ] + ']').parent().hasClass('closed_task') ) ){
                                    jQuery('.task_id[value=' + current_tasks[ key ] + ']').siblings('.task_tag').append('<a href="javascript:void(0);" class="tag die_date_tag' + type_date_class + '" title="' + die_date_text + '">' + die_date_text + '</a>');
                                }
                                jQuery('.parent_id[value=' + current_tasks[ key ] + ']').each(function() {
                                    if( !( jQuery(this).parent().hasClass('completed_task') ||
                                        jQuery(this).parent().hasClass('closed_task') ) ){
                                        jQuery(this).siblings('.task_tag').append('<a href="javascript:void(0);" class="tag die_date_tag' + type_date_class + '" title="' + die_date_text + '">' + die_date_text + '</a>');
                                    }
                                });

                            }
                        } else {
                            obj.siblings('.die_date_text').val( previous_data );
                            alert( data.message );
                        }
                    }
                });
            }
        }).appendTo('.die_date_link');
        $dp.after("<input type=\"hidden\" value=\"\" />");
        $dp.datepicker( "option", wpc_custom_fields.regional );

        jQuery('.die_date_link').click(function(e) {
            if( jQuery( this ).hasClass('die_date_access') ) {
                jQuery(this).children('.wpc_pm_picker').datepicker("show").datepicker("widget").show();
            }
            e.preventDefault();
        });

        jQuery('.add_task_due_date').datepicker( "option", "minDate", 0 );

        jQuery( '.task_priority_select .dropdown-menu a' ).click(function() {
            var priority = jQuery( this ).attr('rel');
            var previous_value = jQuery(this).parents('.task_priority_select').find( '.dropdown-toggle' ).html();
            jQuery(this).parents('.task_priority_select').find( '.dropdown-toggle' ).html( jQuery( this ).html() + ' <?php if( current_user_can('wpc_pm_level_3') ) { ?><span class="caret"></span><?php } ?>' );

            var current_tasks = new Array();
            if( jQuery('.bulk_id:checked').length > 1 ) {
                jQuery('.bulk_id:checked').each(function() {
                    current_tasks.push( jQuery(this).val() );
                });
            } else {
                current_tasks.push( wpc_pm_selected_task );
            }

            if( current_tasks.length ) {
                jQuery.ajax({
                    type     : 'POST',
                    dataType : 'json',
                    url      : '<?php echo get_admin_url(); ?>admin-ajax.php',
                    data     : 'action=wpc_pm_task&act=change_priority&task_id=' + current_tasks.join(',') + '&priority=' + priority,
                    success  : function( data ){
                        if( data.status ) {
                            for( var key in current_tasks ) {
                                jQuery('.task_row .task_id[value="'+ current_tasks[ key ] + '"]').siblings('.task_priority').removeClass('high normal low urgent');
                                jQuery('.task_row .task_id[value="'+ current_tasks[ key ] + '"]').siblings('.task_priority').addClass( priority );
                                jQuery('.task_row .task_id[value="'+ current_tasks[ key ] + '"]').siblings('.task_priority').attr( 'title', priority.charAt(0).toUpperCase() + priority.substr(1, priority.length-1) );
                                jQuery('.task_row .parent_id[value="'+ current_tasks[ key ] + '"]').siblings('.task_priority').removeClass('high normal low urgent');
                                jQuery('.task_row .parent_id[value="'+ current_tasks[ key ] + '"]').siblings('.task_priority').addClass( priority );
                                jQuery('.task_row .parent_id[value="'+ current_tasks[ key ] + '"]').siblings('.task_priority').attr( 'title', priority.charAt(0).toUpperCase() + priority.substr(1, priority.length-1) );
                            }
                        } else {
                            jQuery( '.task_row .task_priority_select .dropdown-toggle' ).html( previous_value );
                            alert( data.message );
                        }
                    }
                 });
            }

        });

        jQuery( '.task_status .dropdown-menu a' ).click(function() {
            var status = jQuery( this ).attr('rel');
            var previous_value = jQuery( this ).parents( '.task_status' ).find( '.dropdown-toggle' ).html();
            jQuery( this ).parents( '.task_status' ).find( '.dropdown-toggle' ).html( jQuery( this ).html() + ' <span class="caret"></span>' );

            var current_tasks = new Array();
            if( jQuery('.bulk_id:checked').length > 1 ) {
                jQuery('.bulk_id:checked').each(function() {
                    current_tasks.push( jQuery(this).val() );
                });
            } else {
                current_tasks.push( wpc_pm_selected_task );
            }

            if( current_tasks.length ) {
                jQuery.ajax({
                    type     : 'POST',
                    dataType : 'json',
                    url      : '<?php echo get_admin_url(); ?>admin-ajax.php',
                    data     : 'action=wpc_pm_task&act=change_status&task_id=' + current_tasks.join(',') + '&status=' + status,
                    success  : function( data ){
                        if( data.status ) {
                            for( var key in current_tasks ) {
                                jQuery('.task_row .task_id[value="'+ current_tasks[ key ] + '"]').parent().removeClass('new_task open_task pending_task testing_task completed_task closed_task');
                                jQuery('.task_row .task_id[value="'+ current_tasks[ key ] + '"]').parent().addClass( status + '_task' );
                                jQuery('.task_row .parent_id[value="'+ current_tasks[ key ] + '"]').parent().removeClass('new_task open_task pending_task testing_task completed_task closed_task');
                                jQuery('.task_row .parent_id[value="'+ current_tasks[ key ] + '"]').parent().addClass( status + '_task' );
                                if( 'completed' == status || 'closed' == status ){
                                    jQuery('.task_row .task_id[value="'+ current_tasks[ key ] + '"]').parent().find('.die_date_tag').remove();
                                }
                                jQuery('.task_row .task_id[value="'+ current_tasks[ key ] + '"]').parent().find('.status_tag').html( status.charAt(0).toUpperCase() + status.substr(1, status.length-1) );
                                jQuery('.task_row .task_id[value="'+ current_tasks[ key ] + '"]').parent().find('.status_tag').prop( 'title', status.charAt(0).toUpperCase() + status.substr(1, status.length-1) );
                                jQuery('.task_row .parent_id[value="'+ current_tasks[ key ] + '"]').parent().find('.status_tag').html( status.charAt(0).toUpperCase() + status.substr(1, status.length-1) );
                                jQuery('.task_row .parent_id[value="'+ current_tasks[ key ] + '"]').parent().find('.status_tag').prop( 'title', status.charAt(0).toUpperCase() + status.substr(1, status.length-1) );
                            }
                        } else {
                            jQuery( '.task_row .task_status .dropdown-toggle' ).html( previous_value );
                            alert( data.message );
                        }
                    }
                 });
            }

        });

        jQuery( '.detele_task' ).click(function() {
            var current_tasks = new Array();
            if( jQuery('.bulk_id:checked').length > 1 ) {
                jQuery('.bulk_id:checked').each(function() {
                    current_tasks.push( jQuery(this).val() );
                });
            } else {
                current_tasks.push( wpc_pm_selected_task );
            }
            if( confirm('<?php _e( "Are you sure?", WPC_CLIENT_TEXT_DOMAIN ) ?>') && current_tasks.length ) {
                jQuery.ajax({
                    type     : 'POST',
                    dataType : 'json',
                    url      : '<?php echo get_admin_url(); ?>admin-ajax.php',
                    data     : 'action=wpc_pm_task&act=remove_task&task_id=' + current_tasks.join(','),
                    success  : function( data ){
                        if( data.status ) {
                            jQuery('.wpc_pm_task').hide();
                            jQuery('.bulk_actions_bar').hide();
                            for( var key in current_tasks ) {
                                jQuery('.task_id[value="' + current_tasks[ key ] + '"]').parent('.task_row').remove();
                            }
                        } else {
                            alert( data.message );
                        }
                    }
                });
            }

        });

        jQuery('.task_add_button').click(function() {
            var error = '';
            var original_title = jQuery('#add_task_box .task_title').val();
            if( original_title == '' ) {
                error = '<?php _e( 'Task title is empty. Please add it.', WPC_CLIENT_TEXT_DOMAIN ); ?>';
            } else {
                title = encodeURIComponent( jQuery.base64Encode( original_title ) );
            }

            var description = jQuery('#add_task_box .task_description').val();
            if( description != '' ) {
                description = encodeURIComponent( jQuery.base64Encode( description ) );
            }

            var assigned_user = jQuery('.add_task_assigned_user').val();
            var due_date = jQuery('.add_task_due_date').next('input[type="hidden"]').val();
            var priority = jQuery('.add_task_priority').val();

            var obj = jQuery(this);
            if( !error ) {
                jQuery(this).attr("disabled", "disabled");
                jQuery.ajax({
                    type     : 'POST',
                    dataType : 'json',
                    url      : '<?php echo get_admin_url(); ?>admin-ajax.php',
                    data     : 'action=wpc_pm_task&act=add&project_id=<?php echo( ( isset( $_GET['project_id'] ) && is_numeric( $_GET['project_id'] ) ) ? $_GET['project_id'] : 0 ); ?>&task_id=' + ( wpc_pm_subtask_flag ? wpc_pm_selected_task : 0 ) + '&title=' + title + '&description=' + description + '&assigned_user=' + assigned_user + '&due_date=' + due_date + '&priority=' + priority,
                    success  : function( data ){
                        if( data.status ) {
                            if( !obj.hasClass('add_and_create') ) {
                                jQuery('.wpc_pm_task .task_form .title').html( original_title + '<?php if( current_user_can('wpc_pm_level_3') ) { ?><a href="javascript:void(0);" class="edit_title_link"></a><?php } ?>' );
                                jQuery('#div_task_description_tab').html( description + '<?php if( current_user_can('wpc_pm_level_3') ) { ?><a href="javascript:void(0);" class="edit_description_link"></a><?php } ?>' );
                            }
                            var tags = '';

                            if( data.message.details.die_date_text != '' ) {
                                tags += '<a href="javascript:void(0);" class="tag die_date_tag " title="' + data.message.details.die_date_text + '">' + data.message.details.die_date_text + '</a>';
                            }

                            if( data.message.details.status.length ) {
                                tags += '<a href="javascript:void(0);" class="tag status_tag " title="' + data.message.details.status + '">' + data.message.details.status + '</a>';
                            }
                            if( wpc_pm_subtask_flag ) {
                                var temp_task = jQuery('.task_row.active');
                                if( !obj.hasClass('add_and_create') ) {
                                    jQuery('.task_row').removeClass('active');
                                }
                                temp_task.after('<div class="task_row wpc_edit_task ' + ( obj.hasClass('add_and_create') ? '' : 'active'  ) + ' subtask_row task_new">' +
                                    '<input type="hidden" name="task_id[]" class="task_id" value="' + data.message.id + '">' +
                                    '<input type="hidden" name="parent_id[]" class="parent_id" value="' + wpc_pm_selected_task + '">' +
                                    '<a class="task_priority normal" href="javascript:void(0);" title="Normal"></a>' +
                                    '<label class="bulk_checkbox"><input type="checkbox" name="bulk_id[]" class="bulk_id" id="id_' + data.message.id + '" value="' + data.message.id + '" /></label>' +
                                    '<div class="task_id">' + data.message.id_in_project + '</div>' +
                                    '<div class="task_assign"></div>' +
                                    '<div class="task_title">' + original_title + '</div>' +
                                    '<div class="task_tag">' + tags + '</div>' +
                                '</div>');
                                jQuery('.task_id[value=' + wpc_pm_selected_task + ']').siblings('.task_assign').html('');
                                jQuery('.task_id[value=' + data.message.id + ']').parents('.task_row').trigger('click');
                            } else {
                                if( !obj.hasClass('add_and_create') ) {
                                    jQuery('.task_row').removeClass('active');
                                }
                                jQuery('.task_table').prepend('<div class="task_row wpc_edit_task ' + ( obj.hasClass('add_and_create') ? '' : 'active'  ) + ' task_new">' +
                                    '<input type="hidden" name="task_id[]" class="task_id" value="' + data.message.id + '">' +
                                    '<a class="task_priority normal" href="javascript:void(0);" title="Normal"></a>' +
                                    '<label class="bulk_checkbox"><input type="checkbox" name="bulk_id[]" class="bulk_id" id="id_' + data.message.id + '" value="' + data.message.id + '" /></label>' +
                                    '<div class="task_id">' + data.message.id_in_project + '</div>' +
                                    '<div class="task_assign"></div>' +
                                    '<div class="task_title">' + original_title + '</div>' +
                                    '<div class="task_tag">' + tags + '</div>' +
                                '</div>');
                                jQuery('.task_row:first').trigger('click');
                                exclude_task.push( data.message.id );
                            }
                            jQuery('.wpc_pm_task .assign_dropdown').show();
                            if( wpc_pm_subtask_flag ) {
                                jQuery('.add_subtask').parent().hide();
                            } else {
                                jQuery('.add_subtask').parent().show();
                            }
                            if( !obj.hasClass('add_and_create') ) {
                                wpc_pm_selected_task = data.message.id;
                            }
                            jQuery('#add_task_box .task_title').val('');
                            jQuery('#add_task_box .task_description').val('');
                            jQuery('#add_task_box .add_task_due_date').next('input[type="hidden"]').val('');
                            jQuery('#add_task_box .add_task_due_date').next('input[type="hidden"]').trigger('change');
                            jQuery('#add_task_box .add_task_assigned_user option').removeAttr('selected');
                            jQuery('#add_task_box .add_task_priority option').removeAttr('selected');
                            jQuery("#add_task_box .add_task_priority option[value='normal']").attr('selected', 'selected');
                            obj.removeAttr("disabled");
                            if( !obj.hasClass('add_and_create') ) {
                                if( wpc_pm_subtask_flag ) {
                                    jQuery('.add_subtask').shutter_box('close');
                                } else {
                                    jQuery('.add-new-task').shutter_box('close');
                                }
                            }
                        } else {
                            alert( data.message );
                        }
                    }
                 });
            } else {
                alert( error );
            }
        });

        jQuery('.wpc_edit_task').live('click', function() {

            jQuery('.bulk_id').attr('checked', false);
            jQuery('.wpc_pm_task').hide();
            jQuery('.bulk_actions_bar').hide();

            jQuery('.task_row').removeClass('active');
            if( jQuery(this).hasClass('task_row') ) {
                jQuery( this ).addClass('active');
            } else {
                jQuery( this ).parents('.task_row').addClass('active');
            }
            var task_id = jQuery( this ).find('.task_id').val();
            var obj = jQuery( this );

            if( !isNaN( task_id ) ) {
                jQuery.ajax({
                    type     : 'POST',
                    dataType : 'json',
                    url      : '<?php echo get_admin_url(); ?>admin-ajax.php',
                    data     : 'action=wpc_pm_task&act=get_details&task_id=' + task_id,
                    success  : function( data ){
                        if( data.status ) {
                            var task_details = data.message;

                            if( task_details.assigned_user > 0 && task_details.task_type != 'parent_task' ) {
                                jQuery( '.wpc_pm_task .assign_dropdown .dropdown-toggle' ).html('<span class="icon color-' + task_details.color + '">' + task_details.short_name + '</span>' + task_details.name + '<?php if( current_user_can('wpc_pm_level_3') ) { ?><span class="caret"></span><?php } ?>');
                                jQuery( '.wpc_pm_task .assign_dropdown .dropdown-toggle' ).attr( 'title', task_details.full_name );
                                obj.children('.task_assign').html('<a href="javascript:void(0);" class="user color-' + task_details.color + '">' + task_details.short_name + '</a>');
                                jQuery( '.assign_dropdown .close-but' ).removeAttr('style');
                            } else {
                                jQuery( '.wpc_pm_task .assign_dropdown .dropdown-toggle' ).html('<?php _e( 'None', WPC_CLIENT_TEXT_DOMAIN ); ?> <?php if( current_user_can('wpc_pm_level_3') ) { ?><span class="caret"></span><?php } ?>');
                                jQuery( '.wpc_pm_task .assign_dropdown .dropdown-toggle' ).attr( 'title', 'User does not selected' );
                                obj.children('.task_assign').html('');
                                jQuery( '.assign_dropdown .close-but' ).hide();
                            }

                            if( task_details.milestone_title.length ) {
                                jQuery( '.wpc_pm_task .milestone_dropdown .dropdown-toggle' ).html( task_details.milestone_title + '<?php if( current_user_can('wpc_pm_level_3') ) { ?><span class="caret"></span><?php } ?>');
                                jQuery( '.wpc_pm_task .milestone_dropdown .dropdown-toggle' ).attr( 'title', task_details.milestone_title );
                            } else {
                                jQuery( '.wpc_pm_task .milestone_dropdown .dropdown-toggle' ).html('<?php _e( 'Milestone', WPC_CLIENT_TEXT_DOMAIN ); ?> <?php if( current_user_can('wpc_pm_level_3') ) { ?><span class="caret"></span><?php } ?>');
                                jQuery( '.wpc_pm_task .milestone_dropdown .dropdown-toggle' ).attr( 'title', 'Milestone does not selected' );
                            }

                            if( task_details.die_date != '' ) {
                                var d = new Date();
                                d.setTime( ( task_details.die_date * 1 + d.getTimezoneOffset()*60 ) * 1000 );
                                jQuery( '.wpc_pm_task .die_date_link .hasDatepicker' ).datepicker( "setDate", d );
                                jQuery( '.wpc_pm_task .die_date_link .hasDatepicker' ).next().val( task_details.die_date );
                                jQuery( '.wpc_pm_task .die_date_link .die_date_text' ).html( task_details.die_date_text );
                                jQuery( '.die_date_outer .close-but' ).removeAttr('style');
                            } else {
                                jQuery( '.wpc_pm_task .die_date_link .die_date_text' ).html('<?php _e( 'Due date', WPC_CLIENT_TEXT_DOMAIN ); ?>');
                                jQuery( '.wpc_pm_task .die_date_link .hasDatepicker' ).datepicker("setDate", new Date() );
                                jQuery( '.die_date_outer .close-but' ).hide();
                            }

                            jQuery( '.wpc_pm_task .task_priority_select .dropdown-toggle' ).html( task_details.priority.charAt(0).toUpperCase() + task_details.priority.substr(1, task_details.priority.length-1) + ' <?php if( current_user_can('wpc_pm_level_3') ) { ?><span class="caret"></span><?php } ?>' );

                            jQuery( '.wpc_pm_task .task_status .dropdown-toggle' ).html( task_details.status.charAt(0).toUpperCase() + task_details.status.substr(1, task_details.status.length-1) + ' <span class="caret"></span>' );

                            jQuery('.wpc_pm_task .task_form .title').html( task_details.title + '<?php if( current_user_can('wpc_pm_level_3') ) { ?><a href="javascript:void(0);" class="edit_title_link"></a><?php } ?>' );
                            jQuery('#div_task_description_tab').html( task_details.description + '<?php if( current_user_can('wpc_pm_level_3') ) { ?><a href="javascript:void(0);" class="edit_description_link"></a><?php } ?>' );

                            switch( task_details.task_type ) {
                                case 'parent_task':
                                    jQuery('.wpc_pm_task .assign_dropdown').hide();
                                    jQuery('.add_subtask').parent().show();
                                    break;
                                case 'children_task':
                                    jQuery('.wpc_pm_task .assign_dropdown').show();
                                    jQuery('.add_subtask').parent().hide();
                                    break;
                                default:
                                    jQuery('.wpc_pm_task .assign_dropdown').show();
                                    jQuery('.add_subtask').parent().show();
                                    break;
                            }

                            obj.children('.task_priority').removeClass('high normal low urgent');
                            obj.children('.task_priority').addClass( task_details.priority );
                            obj.children('.task_priority').attr( 'title', task_details.priority.charAt(0).toUpperCase() + task_details.priority.substr(1, task_details.priority.length-1) );

                            change_flag = 1;
                            jQuery('.task_form .tags').tokenInput("clear");

                            for( key in task_details.tags ) {
                                jQuery('.task_form .tags').tokenInput( "add", task_details.tags[ key ] );
                            }
                            change_flag = 0;

                            jQuery('#task_description_tab a').trigger('click');
                            jQuery('.wpc_pm_task').show();
                            wpc_pm_selected_task = task_id;

                            jQuery('.wpc_pm_messages_counter').html( task_details.messages_count );
                            jQuery('.wpc_pm_files_counter').html( task_details.files_count );

                            if( jQuery(window).scrollTop() > jQuery(".wpc_pm_main_menu").offset().top ) {
                                jQuery("html, body").animate({
                                    scrollTop: jQuery(".wpc_pm_main_menu").offset().top
                                }, 1000);
                            }
                        } else {
                            alert( data.message );
                        }
                    }
                });
            }
        });

        jQuery('.edit_title_link').live('click', function() {
            if( !isNaN( wpc_pm_selected_task ) && wpc_pm_selected_task > 0 ) {
                var obj = jQuery(this);
                jQuery.ajax({
                    type     : 'POST',
                    dataType : 'json',
                    url      : '<?php echo get_admin_url(); ?>admin-ajax.php',
                    data     : 'action=wpc_pm_task&act=get_details&type=original&task_id=' + wpc_pm_selected_task,
                    success  : function( data ){
                        if( data.status ) {
                            obj.parent().html('<textarea rows="3" id="title_textarea" name="title_textarea">' + data.message.title + '</textarea>');
                            jQuery('#title_textarea').focus();
                        } else {
                            alert( data.message );
                        }
                    }
                });
            }
        });

        jQuery('#title_textarea').live('blur', function() {
            if( !isNaN( wpc_pm_selected_task ) && wpc_pm_selected_task > 0 ) {
                var title = encodeURIComponent( jQuery.base64Encode( jQuery(this).val() ) );
                var original_title = jQuery(this).val();
                var obj = jQuery(this);
                jQuery.ajax({
                    type     : 'POST',
                    dataType : 'json',
                    url      : '<?php echo get_admin_url(); ?>admin-ajax.php',
                    data     : 'action=wpc_pm_task&act=save_title&task_id=' + wpc_pm_selected_task + '&title=' + title,
                    success  : function( data ){
                        if( data.status ) {
                            obj.parent().html( linkifyText( original_title ) + '<?php if( current_user_can('wpc_pm_level_3') ) { ?><a href="javascript:void(0);" class="edit_title_link"></a><?php } ?>' );
                            jQuery('.task_row .task_id[value="'+ wpc_pm_selected_task + '"]').siblings('.task_title').html( original_title );
                        } else {
                            alert( data.message );
                        }
                    }
                });
            }
        });

        jQuery('#title_textarea').live('keypress', function(e) {
            if( e.which == 13 && !isNaN( wpc_pm_selected_task ) && wpc_pm_selected_task > 0 ) {
                var title = encodeURIComponent( jQuery.base64Encode( jQuery(this).val() ) );
                var original_title = jQuery(this).val();
                var obj = jQuery(this);
                jQuery.ajax({
                    type     : 'POST',
                    dataType : 'json',
                    url      : '<?php echo get_admin_url(); ?>admin-ajax.php',
                    data     : 'action=wpc_pm_task&act=save_title&task_id=' + wpc_pm_selected_task + '&title=' + title,
                    success  : function( data ){
                        if( data.status ) {
                            obj.parent().html( original_title + '<?php if( current_user_can('wpc_pm_level_3') ) { ?><a href="javascript:void(0);" class="edit_title_link"></a><?php } ?>' );
                            jQuery('.task_row .task_id[value="'+ wpc_pm_selected_task + '"]').siblings('.task_title').html( original_title );
                        } else {
                            alert( data.message );
                        }
                    }
                });
                return false;
            }
        })

        jQuery('.edit_description_link').live('click', function() {
            if( !isNaN( wpc_pm_selected_task ) && wpc_pm_selected_task > 0 ) {
                var obj = jQuery(this);
                jQuery.ajax({
                    type     : 'POST',
                    dataType : 'json',
                    url      : '<?php echo get_admin_url(); ?>admin-ajax.php',
                    data     : 'action=wpc_pm_task&act=get_details&type=original&task_id=' + wpc_pm_selected_task,
                    success  : function( data ){
                        if( data.status ) {
                            obj.parent().html('<textarea rows="10" id="description_textarea" name="description_textarea">' + data.message.description + '</textarea>' +
                            '<a href="javascript:void(0);" class="submit_description_link"></a>');
                            jQuery('#description_textarea').focus();
                        } else {
                            alert( data.message );
                        }
                    }
                });
            }
        });

        jQuery('#description_textarea').live('blur', function() {
            if( !isNaN( wpc_pm_selected_task ) && wpc_pm_selected_task > 0 ) {
                var description = encodeURIComponent( jQuery.base64Encode( jQuery(this).val() ) );
                var original_description = jQuery(this).val();
                var obj = jQuery(this);
                jQuery.ajax({
                    type     : 'POST',
                    dataType : 'json',
                    url      : '<?php echo get_admin_url(); ?>admin-ajax.php',
                    data     : 'action=wpc_pm_task&act=save_description&task_id=' + wpc_pm_selected_task + '&description=' + description,
                    success  : function( data ){
                        if( data.status ) {
                            obj.parent().html( linkifyText( multiString( original_description ) ) + '<?php if( current_user_can('wpc_pm_level_3') ) { ?><a href="javascript:void(0);" class="edit_description_link"></a><?php } ?>' );
                        } else {
                            alert( data.message );
                        }
                    }
                });
            }
        });

        jQuery('#task_activity_tab a').click(function() {
            jQuery('#div_task_activity_tab').html('<img alt="" src="<?php echo $wpc_client->plugin_url ?>/images/ajax_big_loading.gif" border="0" class="ajax_image" />');
            jQuery.ajax({
                type     : 'POST',
                dataType : 'json',
                url      : '<?php echo get_admin_url(); ?>admin-ajax.php',
                data     : 'action=wpc_pm_get_task_activity&task_id=' + wpc_pm_selected_task,
                success  : function( data ){
                    if( data.status ) {
                        last_id = data.last_id;
                        jQuery('#div_task_activity_tab').html( data.message );
                        jQuery('#div_task_activity_tab .wpc_pm_activities').unbind('infinitescroll');
                        jQuery('#div_task_activity_tab .wpc_pm_activities').infinitescroll({
                            navSelector      : "#wpc_next_page",
                            finishedMsg      : '',
                            nextSelector     : "a#wpc_next_page",
                            itemSelector     : "li",
                            debug            : true,
                            prefill          : true,
                            dataType         : 'json',
                            appendCallback   : true,
                            template         : function( data ) {
                                last_id = data.last_id;
                                return data.html;
                            },
                            binder           : jQuery('#div_task_activity_tab .wpc_pm_activities'),
                            path: function(index) {
                                return "admin-ajax.php?action=wpc_pm_pagination&type=activity&object=task&id=" + wpc_pm_selected_task + "&last_id=" + last_id;
                            }
                        }, function(newElements, data, url){
                        });
                    } else {
                        alert( data.message );
                    }
                }
            });
        });

        jQuery('#task_messages_tab a').click(function() {
            jQuery('#div_task_messages_tab .task_messages_list').html('<img alt="" src="<?php echo $wpc_client->plugin_url ?>/images/ajax_big_loading.gif" border="0" class="ajax_image" />');
            jQuery.ajax({
                type     : 'POST',
                dataType : 'json',
                url      : '<?php echo get_admin_url(); ?>admin-ajax.php',
                data     : 'action=wpc_pm_task_messages&act=get&task_id=' + wpc_pm_selected_task,
                success  : function( data ){
                    if( data.status ) {
                        jQuery('#div_task_messages_tab .task_messages_list').html( data.message );
                    } else {
                        alert( data.message );
                    }
                }
            });
        });

        jQuery('#task_files_tab a').click(function() {
            jQuery('#wpc_pm_file_list').html('<img alt="" src="<?php echo $wpc_client->plugin_url ?>/images/ajax_big_loading.gif" border="0" class="ajax_image" />');
            jQuery.ajax({
                type     : 'POST',
                dataType : 'json',
                url      : '<?php echo get_admin_url(); ?>admin-ajax.php',
                data     : 'action=wpc_pm_task&act=get_files&task_id=' + wpc_pm_selected_task,
                success  : function( data ){
                    if( data.status ) {
                        jQuery('#wpc_pm_file_list').html('');
                        if( data.message.length > 0 ) {
                            for( var i = 0; i < data.message.length; i++ ) {
                                jQuery('#wpc_pm_file_list').append('<div class="task_msg_block left">' +
                                    '<div class="message_image">' +
                                        data.message[ i ].avatar + '<br class="wpc_clear" />' +
                                    data.message[ i ].username +
                                    '</div>' +
                                    '<div class="msg_inner">' +
                                        '<b>' + data.message[ i ].creation_date + '</b> ::' +
                                        '<a href="admin.php?wpc_action=download&module=pm&id=' + data.message[i].id + '&nonce=' + data.message[ i ].download_nonce + '">' + data.message[i].filename + '</a>' +
                                        '<a href="admin.php?wpc_action=download&module=pm&id=' + data.message[i].id + '&nonce=' + data.message[ i ].download_nonce + '" class="pm_download_icon" title="<?php _e( 'Download', WPC_CLIENT_TEXT_DOMAIN ); ?>"></a>' +
                                        ( data.message[ i ].view_link == '1' ? '<a target="_blank" target="_blank" href="admin.php?wpc_action=view&module=pm&id=' + data.message[i].id + '&nonce=' + data.message[ i ].download_nonce + '" class="pm_view_icon" title="<?php _e( 'View', WPC_CLIENT_TEXT_DOMAIN ); ?>"></a>' : '' ) +
                                        ( data.message[i].access ? '<a href="javascript:void(0);" class="remove_task_files" data-id="' + data.message[i].id + '"></a>' : '' ) + '<br />' +
                                        data.message[ i ].note +
                                    '</div>' +
                                '</div>');
                            }
                        }
                        jQuery('.wpc_pm_files_counter').html( data.count );
                    } else {
                        alert( data.message );
                    }
                }
            });
        });

        jQuery('.task_message_send').click(function() {
            var message = jQuery('.task_message').val();
            message = encodeURIComponent( jQuery.base64Encode( message ) );

            var file_ids = new Array();
            jQuery('.message_form .file_ids').each(function() {
                file_ids.push( jQuery(this).val() );
            });
            jQuery.ajax({
                type     : 'POST',
                dataType : 'json',
                url      : '<?php echo get_admin_url(); ?>admin-ajax.php',
                data     : 'action=wpc_pm_task_messages&act=send&task_id=' + wpc_pm_selected_task + '&message=' + message + '&file_ids=' + file_ids.join(','),
                success  : function( data ){
                    if( data.status ) {
                        jQuery('#div_task_messages_tab .task_messages_list').prepend( data.message );
                        jQuery('.task_message').val('');
                        jQuery('#div_task_messages_tab #queue_messages').html('');
                        jQuery('.file_ids').remove();
                        jQuery('.plupload_filelist').html('');
                        if( jQuery(".wpc_pluploder_task_messages_queue").length ) {
                            jQuery(".wpc_pluploder_task_messages_queue").pluploadQueue().splice();
                        }
                        jQuery('.wpc_pm_messages_counter').html( data.count );
                    } else {
                        alert( data.message );
                    }
                }
            });
        });

        jQuery('.task_files_submit').on('click', function() {
            jQuery(this).attr('disabled', 'disabled');
            jQuery('.file_upload_ajax').uploadifive('upload');
        });

        jQuery('.wpc_pm_task .assign_dropdown .close-but').live('click', function() {
            jQuery.ajax({
                type     : 'POST',
                dataType : 'json',
                url      : '<?php echo get_admin_url(); ?>admin-ajax.php',
                data     : 'action=wpc_pm_task&act=remove_assigned_user&task_id=' + wpc_pm_selected_task,
                success  : function( data ){
                    if( data.status ) {
                        jQuery('.task_id[value=' + wpc_pm_selected_task + ']').siblings('.task_assign').html('');
                        jQuery( '.wpc_pm_task .assign_dropdown .dropdown-toggle' ).html('<?php _e( 'None', WPC_CLIENT_TEXT_DOMAIN ); ?> <?php if( current_user_can('wpc_pm_level_3') ) { ?><span class="caret"></span><?php } ?>');
                        jQuery( '.wpc_pm_task .assign_dropdown .dropdown-toggle' ).attr( 'title', 'User does not selected' );
                    } else {
                        alert( data.message );
                    }
                }
            });
        });

        jQuery('.wpc_pm_task .die_date_outer .close-but').live('click', function() {
            jQuery.ajax({
                type     : 'POST',
                dataType : 'json',
                url      : '<?php echo get_admin_url(); ?>admin-ajax.php',
                data     : 'action=wpc_pm_task&act=remove_die_date&task_id=' + wpc_pm_selected_task,
                success  : function( data ){
                    if( data.status ) {
                        jQuery( '.wpc_pm_task .die_date_link .die_date_text' ).html('<?php _e( 'Due date', WPC_CLIENT_TEXT_DOMAIN ); ?>');
                        jQuery( '.wpc_pm_task .die_date_outer .close-but' ).hide();
                        jQuery('.task_id[value=' + wpc_pm_selected_task + ']').siblings('.task_tag').children('.die_date_tag').remove();
                        jQuery('.parent_id[value=' + wpc_pm_selected_task + ']').siblings('.task_tag').children('.die_date_tag').remove();
                    } else {
                        alert( data.message );
                    }
                }
            });
        });

        jQuery('.wpc-scroll').infinitescroll({
            navSelector      : "#wpc_next_page",
            finishedMsg      : '',
            nextSelector     : "a#wpc_next_page",
            itemSelector     : ".task_row",
            debug            : false,
            prefill          : true,
            dataType         : 'json',
            appendCallback   : true,
            template         : function( data ) {
                last_id = data.last_id;
                return data.html;
            },
            binder           : jQuery('.task_table'),
            path: function(index) {
                var tasks_count = jQuery('.task_row:not(.subtask_row)').length;

                var from = jQuery('#due_date_from_filter').val();
                if( from == '<?php _e( 'From Date', WPC_CLIENT_TEXT_DOMAIN ); ?>' ) {
                    from = '';
                }
                var to = jQuery('#due_date_to_filter').val();
                if( to == '<?php _e( 'To Date', WPC_CLIENT_TEXT_DOMAIN ); ?>' ) {
                    to = '';
                }

                var auto_hide_days = jQuery('#auto_hide_days:checked').length;

                return "admin-ajax.php?action=wpc_pm_pagination&type=task&project_id=<?php echo( ( isset( $_GET['project_id'] ) && is_numeric( $_GET['project_id'] ) ) ? $_GET['project_id'] : 0 ); ?>&count=" + tasks_count + "&exclude=" + exclude_task.join(',') + '&assigned_users=' + assigned_user_filter.join(',') + '&priority=' + priority_filter.join(',') + '&status=' + status_filter.join(',') + '&tags=' + tags_filter.join(',') + '&due_date_from=' + from + '&due_date_to=' + to + '&order_by=' + order_by + '&order_dir=' + order_dir + '&auto_hide_days=' + auto_hide_days + '&auto_hide_flag=' + hide_completed_flag;
            }
        }, function(newElements, data, url){
        });

        jQuery('.bulk_id').live( 'click', function(e) {
            var task_id = jQuery(this).val();
            jQuery('.task_row').children('.parent_id[value=' + task_id + ']').siblings('.bulk_checkbox').children('input').attr('checked', jQuery(this).is(':checked') );

            if( !jQuery(this).is(':checked') && jQuery(this).parent().siblings('.parent_id').val() > 0 ) {
                var parent_id = jQuery(this).parent().siblings('.parent_id').val();
                jQuery('#id_' + parent_id).attr('checked', false);
            }

            if( jQuery('.bulk_id:checked').length > 1 ) {
                jQuery('.wpc_pm_task').hide();
                jQuery('.bulk_actions_bar').show();
            } else {
                jQuery('.bulk_actions_bar').hide();
            }
            e.stopPropagation();
        });

        jQuery('.assigned_user_filter').multipleSelect({
            placeholder: "<?php _e("By user", WPC_CLIENT_TEXT_DOMAIN); ?>",
            onClick: function( obj ) {
                assigned_user_filter = obj.instance.getSelects();
                clearTimeout(timer_id);
                timer_id = setTimeout(filterTasks, 5000);
            },
            onCheckAll: function() {
                assigned_user_filter = new Array();
                clearTimeout(timer_id);
                timer_id = setTimeout(filterTasks, 5000);
            },
            onClose: function() {
                if( 0 != timer_id ) {
                    clearTimeout( timer_id );
                    filterTasks();
                }
            }
        });

        jQuery('.priority_filter').multipleSelect({
            placeholder: "<?php _e("By priority", WPC_CLIENT_TEXT_DOMAIN); ?>",
            onClick: function( obj ) {
                priority_filter = obj.instance.getSelects();
                clearTimeout(timer_id);
                timer_id = setTimeout(filterTasks, 5000);
            },
            onCheckAll: function() {
                priority_filter = new Array();
                clearTimeout(timer_id);
                timer_id = setTimeout(filterTasks, 5000);
            },
            onClose: function() {
                if( 0 != timer_id ) {
                    clearTimeout( timer_id );
                    filterTasks();
                }
            }
        });

        jQuery('.status_filter').multipleSelect({
            placeholder: "<?php _e("By status", WPC_CLIENT_TEXT_DOMAIN); ?>",
            onClick: function( obj ) {
                status_filter = obj.instance.getSelects();
                clearTimeout( timer_id );
                timer_id = setTimeout(filterTasks, 5000);
            },
            onCheckAll: function() {
                status_filter = new Array();
                clearTimeout(timer_id);
                timer_id = setTimeout(filterTasks, 5000);
            },
            onClose: function() {
                if( 0 != timer_id ) {
                    clearTimeout( timer_id );
                    filterTasks();
                }
            }
        });

        jQuery('.tags_filter').multipleSelect({
            placeholder: "<?php _e("By tags", WPC_CLIENT_TEXT_DOMAIN); ?>",
            onClick: function( obj ) {
                tags_filter = obj.instance.getSelects();
                clearTimeout( timer_id );
                timer_id = setTimeout(filterTasks, 5000);
            },
            onCheckAll: function() {
                tags_filter = new Array();
                clearTimeout(timer_id);
                timer_id = setTimeout(filterTasks, 5000);
            },
            onClose: function() {
                if( 0 != timer_id ) {
                    clearTimeout( timer_id );
                    filterTasks();
                }
            }
        });

        jQuery('.task_order_by_select').multipleSelect({
            placeholder: "<?php _e("Order By", WPC_CLIENT_TEXT_DOMAIN); ?>",
            single: true,
            onClick: function( obj ) {
                jQuery( 'body' ).trigger( 'click' );
                order_by = obj.value;
                if( order_dir ) {
                    clearTimeout( timer_id );
                    filterTasks();
                }
            }
        });

        jQuery('.task_order_dir_select').multipleSelect({
            placeholder: "&uarr;&darr;",
            single: true,
            onClick: function( obj ) {
                jQuery( 'body' ).trigger( 'click' );
                order_dir = obj.value;
                if( order_by ) {
                    clearTimeout( timer_id );
                    filterTasks();
                }
            }
        });

        jQuery( "#fake_due_date_from_filter" ).datepicker({
            changeMonth: true,
            numberOfMonths: 3,
            onSelect : function() {
                var d = jQuery(this).datepicker('getDate');
                d.setHours(0, -d.getTimezoneOffset(), 0, 0);
                jQuery(this).next().val( d.getTime() / 1000 );
                jQuery(this).trigger('change');
            },
            onClose: function( selectedDate ) {
                jQuery( "#fake_due_date_to_filter" ).datepicker( "option", "minDate", selectedDate );
            }
        });

        jQuery( "#fake_due_date_to_filter" ).datepicker({
            changeMonth: true,
            numberOfMonths: 3,
            onSelect : function() {
                var d = jQuery(this).datepicker('getDate');
                d.setHours(0, -d.getTimezoneOffset(), 0, 0);
                jQuery(this).next().val( d.getTime() / 1000 );
                jQuery(this).trigger('change');
            },
            onClose: function( selectedDate ) {
                jQuery( "#fake_due_date_from_filter" ).datepicker( "option", "maxDate", selectedDate );
            }
        });

        jQuery( "#fake_due_date_from_filter, #fake_due_date_to_filter" ).datepicker( "option", wpc_custom_fields.regional );

        jQuery( document ).on( 'change', ".due_date_filter", function() {
            if( jQuery(this).val() == '' ) {
                jQuery(this).next().val('');
            }
            filterTasks();
        } );
        jQuery( "#auto_hide_days" ).click( function() {
            hide_completed_flag = jQuery(this).is(':checked') ? 1 : 0;
            filterTasks();
        });

        jQuery('.ms-drop:first').css( 'min-width', '200px' );

        jQuery('.toggle_tasks').click(function() {
            var type = jQuery(this).data('type');
            if( type == 'hide' ) {
                status_filter = [ 'new', 'open', 'pending', 'testing' ];
                jQuery(this).text('<?php _e( 'Show All Completed/Closed tasks', WPC_CLIENT_TEXT_DOMAIN ); ?>');
                jQuery(this).data('type', 'show');
                jQuery('#auto_hide_days_box').hide();
                hide_completed_flag = 1;
            } else {
                status_filter = new Array();
                jQuery(this).text('<?php _e( 'Hide All Completed/Closed tasks', WPC_CLIENT_TEXT_DOMAIN ); ?>');
                jQuery(this).data('type', 'hide');
                jQuery('#auto_hide_days_box').show();
                var auto_hide_days = jQuery('#auto_hide_days:checked').length;
                if( auto_hide_days > 0 ) {
                    hide_completed_flag = 1;
                } else {
                    hide_completed_flag = 0;
                }
            }
            filterTasks();
        });

        jQuery('#wpc_pm_file_list').on('click', '.remove_task_files', function() {
            var id = jQuery(this).data('id');
            var task_id = jQuery('.task_row.active .task_id').val();
            var obj = jQuery(this);
            if( !isNaN( id ) && !isNaN( task_id ) ) {
                jQuery.ajax({
                    type     : 'POST',
                    dataType : 'json',
                    url      : '<?php echo get_admin_url(); ?>admin-ajax.php',
                    data     : 'action=wpc_pm_task&act=remove_file&task_id=' + task_id + '&file_id=' + id,
                    success  : function( data ){
                        if( data.status ) {
                            obj.parents('.task_msg_block').remove();
                        } else {
                            alert( data.message );
                        }
                    }
                });
            }
        });

        jQuery('.quick_due_date').click(function() {
            var date = jQuery(this).data('date');
            jQuery('.add_task_due_date').next('input[type="hidden"]').val( date );
            jQuery('.add_task_due_date').next('input[type="hidden"]').trigger('change');
        });

        <?php if( isset( $_GET['open_add_block'] ) && '1' == $_GET['open_add_block'] ) { ?>
            jQuery('.add-new-task').trigger('click');
        <?php } ?>

        <?php if( isset( $_GET['task_id'] ) && is_numeric( $_GET['task_id'] ) ) { ?>
            var $task_id_block = jQuery('.task_id[value="<?php echo $_GET['task_id']; ?>"]');
            if( $task_id_block.length > 0 ) {
                $task_id_block.parent('.wpc_edit_task').trigger('click');
            }
        <?php } ?>

        jQuery('.wpc_pm_add_file_block').hide();
        jQuery('.wpc_pm_add_file_link').click(function() {
            jQuery('.wpc_pm_add_file_block').toggle('slow');
        });

    });

    function filterTasks() {
        timer_id = 0;
        var from = jQuery('#due_date_from_filter').val();
        if( from == '<?php _e( 'From Date', WPC_CLIENT_TEXT_DOMAIN ); ?>' ) {
            from = '';
        }
        var to = jQuery('#due_date_to_filter').val();
        if( to == '<?php _e( 'To Date', WPC_CLIENT_TEXT_DOMAIN ); ?>' ) {
            to = '';
        }

        var auto_hide_days = jQuery('#auto_hide_days:checked').length;

        jQuery('.task_row').remove();
        jQuery('.task_table .ajax_image').remove();
        jQuery('.task_table').prepend('<img alt="" src="<?php echo $wpc_client->plugin_url ?>/images/ajax_big_loading.gif" border="0" class="ajax_image" />');
        jQuery.ajax({
            type     : 'POST',
            dataType : 'json',
            url      : '<?php echo get_admin_url(); ?>admin-ajax.php',
            data     : 'action=wpc_pm_pagination&type=task&project_id=<?php echo( ( isset( $_GET['project_id'] ) && is_numeric( $_GET['project_id'] ) ) ? $_GET['project_id'] : 0 ); ?>&count=0&assigned_users=' + assigned_user_filter.join(',') + '&priority=' + priority_filter.join(',') + '&status=' + status_filter.join(',') + '&tags=' + tags_filter.join(',') + '&due_date_from=' + from + '&due_date_to=' + to + '&order_by=' + order_by + '&order_dir=' + order_dir + '&auto_hide_days=' + auto_hide_days + '&auto_hide_flag=' + hide_completed_flag,
            success  : function( data ){
                if( data.status ) {
                    jQuery('.task_table .ajax_image').remove();
                    jQuery('.task_table').prepend( data.html );
                    wpc_pm_selected_task = 0;
                    jQuery('.wpc_pm_task').hide();
                    jQuery('.wpc-scroll').infinitescroll('refresh');
                    jQuery('.wpc-scroll').infinitescroll('bind');
                } else {
                    alert( data.message );
                }
            }
        });
    }

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
 switch( $_GET['message'] ) { case '1': _e( 'Project added successfully.', WPC_CLIENT_TEXT_DOMAIN ); break; case '2': _e( 'Project updated successfully.', WPC_CLIENT_TEXT_DOMAIN ); break; case '3': _e( 'Project deleted successfully.', WPC_CLIENT_TEXT_DOMAIN ); break; } ?>
            </p>
        </div>
        <div class="wpc_clear"></div>
    <?php } ?>
    <?php echo $this->gen_project_tabs_menu(); ?>
    <div class="wpc_clear"></div>
    <div class="wpc_pm_main_block">
        <div class="wpc_left_column">
            <div class="col-wrap">
                <div class="wpc_pm_task_filters">
                     <?php if( current_user_can('wpc_pm_level_3') ) { ?>
                        <a class="add-new-task button-primary" href="#add_task_box">Add Task</a>
                    <?php } ?>

                    <?php if( current_user_can('wpc_pm_level_3') ) { ?>
                        <select class="assigned_user_filter task_filters" name="assigned_user_filter" multiple="multiple" autocomplete="off">
                            <?php foreach( $project_users as $val ) { $full_name = $this->get_full_name( $val['ID'] ); $full_name = !empty( $full_name ) ? $full_name : $val['user_login']; ?>
                                <option value="<?php echo $val['ID']; ?>"><?php echo $full_name; ?></option>
                            <?php } ?>
                        </select>
                    <?php } ?>
                    <select class="priority_filter task_filters" name="priority_filter" multiple="multiple" autocomplete="off">
                        <option value="low"><?php _e( 'Low', WPC_CLIENT_TEXT_DOMAIN ); ?></option>
                        <option value="normal"><?php _e( 'Normal', WPC_CLIENT_TEXT_DOMAIN ); ?></option>
                        <option value="high"><?php _e( 'High', WPC_CLIENT_TEXT_DOMAIN ); ?></option>
                        <option value="urgent"><?php _e( 'Urgent', WPC_CLIENT_TEXT_DOMAIN ); ?></option>
                    </select>
                    <select class="status_filter task_filters" name="status_filter" multiple="multiple" autocomplete="off">
                        <option value="new"><?php _e( 'New', WPC_CLIENT_TEXT_DOMAIN ); ?></option>
                        <option value="open"><?php _e( 'Open', WPC_CLIENT_TEXT_DOMAIN ); ?></option>
                        <option value="pending"><?php _e( 'Pending', WPC_CLIENT_TEXT_DOMAIN ); ?></option>
                        <option value="testing"><?php _e( 'Testing', WPC_CLIENT_TEXT_DOMAIN ); ?></option>
                        <option value="completed"><?php _e( 'Completed', WPC_CLIENT_TEXT_DOMAIN ); ?></option>
                        <option value="closed"><?php _e( 'Closed', WPC_CLIENT_TEXT_DOMAIN ); ?></option>
                    </select>
                    <select class="tags_filter task_filters" name="tags_filter" multiple="multiple" autocomplete="off">
                        <?php foreach( $tags as $val ) { ?>
                            <option value="<?php echo $val['id']; ?>"><?php echo $val['title']; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="wpc_pm_task_filters">
                    <select class="task_order_by_select order_select" name="task_order_by_select" multiple="multiple" autocomplete="off">
                        <option value="title"><?php _e( 'Title', WPC_CLIENT_TEXT_DOMAIN ); ?></option>
                        <option value="assigned_user"><?php _e( 'Assigned user', WPC_CLIENT_TEXT_DOMAIN ); ?></option>
                        <option value="die_date"><?php _e( 'Due date', WPC_CLIENT_TEXT_DOMAIN ); ?></option>
                        <option value="priority"><?php _e( 'Priority', WPC_CLIENT_TEXT_DOMAIN ); ?></option>
                        <option value="status"><?php _e( 'Status', WPC_CLIENT_TEXT_DOMAIN ); ?></option>
                    </select>
                    <select class="task_order_dir_select order_select" name="task_order_sir_select" multiple="multiple" autocomplete="off">
                        <option value="asc" selected="selected"><?php _e( 'ASC', WPC_CLIENT_TEXT_DOMAIN ); ?></option>
                        <option value="desc"><?php _e( 'DESC', WPC_CLIENT_TEXT_DOMAIN ); ?></option>
                    </select>

                    <label class="due_date_to_filter_block">
                        <input type="text" id="fake_due_date_to_filter" name="fake_due_date_to_filter" class="due_date_filter" placeholder="<?php _e( 'To Date', WPC_CLIENT_TEXT_DOMAIN ); ?>" />
                        <input type="hidden" id="due_date_to_filter" name="due_date_to_filter" value="" />
                    </label>
                    <label class="due_date_from_filter_block">
                        <input type="text" id="fake_due_date_from_filter" name="fake_due_date_from_filter" class="due_date_filter" placeholder="<?php _e( 'From Date', WPC_CLIENT_TEXT_DOMAIN ); ?>" />
                        <input type="hidden" id="due_date_from_filter" name="due_date_from_filter" value="" />
                    </label>
                </div>
                <div class="task_table wpc-scroll">
                    <?php foreach( $tasks_list as $val ) { ?>
                        <div class="task_row wpc_edit_task <?php echo $val['status'] . '_task'; ?>">
                            <input type="hidden" name="task_id[]" class="task_id" value="<?php echo $val['id']; ?>" />
                            <a class="task_priority <?php echo $val['priority']; ?>" href="javascript:void(0);" title="<?php echo ucfirst( $val['priority'] ); ?>"></a>
                            <label class="bulk_checkbox"><input type="checkbox" name="bulk_id[]" class="bulk_id" id="id_<?php echo $val['id']; ?>" value="<?php echo $val['id']; ?>" /></label>
                            <div class="task_id"><?php echo $val['id_in_project']; ?></div>
                            <div class="task_assign">
                                <?php
 if( !count( $val['subtasks'] ) && $val['assigned_user'] ) { $assigned_user_data = get_userdata( $val['assigned_user'] ); $first_name = get_user_meta( $val['assigned_user'], 'first_name', true ); $short_name = strtoupper( substr( $first_name ? $first_name : ( $assigned_user_data ? $assigned_user_data->user_login : '' ), 0, 2 ) ); $user_color = get_user_meta( $val['assigned_user'], 'wp_pm_user_color', true ); if( empty( $user_color ) ) { $user_color = rand( 0, 8 ); update_user_meta( $val['assigned_user'], 'wp_pm_user_color', $user_color ); } ?>
                                        <a href="javascript:void(0);" class="user color-<?php echo $user_color; ?>"><?php echo $short_name; ?></a>
                                        <?php
 } ?>
                            </div>
                            <div class="task_title"><?php echo $val['title']; ?></div>
                            <div class="task_tag">
                                <?php $tags = $this->tags_prepare( $wpdb->get_results( $wpdb->prepare( "SELECT id, title FROM {$wpdb->prefix}wpc_pm_tags WHERE task_id = '%d'", $val['id'] ), ARRAY_A ) ); for( $i = 0; $i < 2; $i++ ) { if( isset( $tags[ $i ] ) ) {?>
                                        <a href="javascript:void(0);" class="tag jtip_tag" title="<?php echo $tags[ $i ]['title']; ?>"><?php echo $tags[ $i ]['short_title']; ?></a>
                                <?php } } if( count( $tags ) > 2 ) { $titles_array = array(); foreach( array_slice( $tags, 2 ) as $value ) { $titles_array[] = $value['title']; } ?>
                                    <a href="javascript:void(0);" class="tag jtip_tag" title="<?php echo implode(',', $titles_array); ?>"> ... </a>
                                <?php } if( $val['die_date'] != '' && !in_array( $val['status'], array( 'completed', 'closed' ) ) ) { ?>
                                    <a href="javascript:void(0);" class="tag die_date_tag <?php echo( ( strtotime( date( 'Y-m-d 23:59:59', $val['die_date'] ) ) < time() ) ? 'overdue' : '' ); ?>" title="<?php echo $wpc_client->cc_date_format( $val['die_date'], 'date' ); ?>"><?php echo $wpc_client->cc_date_format( $val['die_date'], 'date' ); ?></a>
                                <?php } ?>
                                <a href="javascript:void(0);" class="tag status_tag" title="<?php echo $val['status']; ?>"><?php echo ucfirst( $val['status'] ); ?></a>
                            </div>
                        </div>
                        <?php if( count( $val['subtasks'] ) ) { foreach( $val['subtasks'] as $subtask ) { ?>
                                <div class="task_row wpc_edit_task subtask_row <?php echo $subtask['status'] . '_task'; ?>">
                                    <input type="hidden" name="task_id[]" class="task_id" value="<?php echo $subtask['id']; ?>" />
                                    <input type="hidden" name="parent_id[]" class="parent_id" value="<?php echo $val['id']; ?>" />
                                    <a class="task_priority <?php echo $subtask['priority']; ?>" href="javascript:void(0);" title="<?php echo ucfirst( $subtask['priority'] ); ?>"></a>
                                    <label class="bulk_checkbox"><input type="checkbox" name="bulk_id[]" class="bulk_id" id="id_<?php echo $subtask['id']; ?>" value="<?php echo $subtask['id']; ?>" /></label>
                                    <div class="task_id"><?php echo $subtask['id_in_project']; ?></div>
                                    <div class="task_assign">
                                        <?php
 if( $subtask['assigned_user'] ) { $assigned_user_data = get_userdata( $subtask['assigned_user'] ); $first_name = get_user_meta( $subtask['assigned_user'], 'first_name', true ); $short_name = strtoupper( substr( $first_name ? $first_name : ( $assigned_user_data ? $assigned_user_data->user_login : '' ), 0, 2 ) ); $user_color = get_user_meta( $subtask['assigned_user'], 'wp_pm_user_color', true ); if( empty( $user_color ) ) { $user_color = rand( 0, 8 ); update_user_meta( $subtask['assigned_user'], 'wp_pm_user_color', $user_color ); } ?>
                                                <a href="javascript:void(0);" class="user color-<?php echo $user_color; ?>"><?php echo $short_name; ?></a>
                                                <?php
 } ?>
                                    </div>
                                    <div class="task_title"><?php echo $subtask['title']; ?></div>
                                    <div class="task_tag">
                                        <?php $tags = $this->tags_prepare( $wpdb->get_results( $wpdb->prepare( "SELECT id, title FROM {$wpdb->prefix}wpc_pm_tags WHERE task_id = '%d'", $subtask['id'] ), ARRAY_A ) ); for( $i = 0; $i < 2; $i++ ) { if( isset( $tags[ $i ] ) ) {?>
                                                <a href="javascript:void(0);" class="tag jtip_tag" title="<?php echo $tags[ $i ]['title']; ?>"><?php echo $tags[ $i ]['title']; ?></a>
                                        <?php } } if( count( $tags ) > 2 ) { $titles_array = array(); foreach( array_slice( $tags, 2 ) as $value ) { $titles_array[] = $value['title']; } ?>
                                            <a href="javascript:void(0);" class="tag jtip_tag" title="<?php echo implode(',', $titles_array); ?>"> ... </a>
                                        <?php } if( $subtask['die_date'] != '' && !in_array( $subtask['status'], array( 'completed', 'closed' ) ) ) { ?>
                                            <a href="javascript:void(0);" class="tag die_date_tag <?php echo( ( strtotime( date( 'Y-m-d 23:59:59', $subtask['die_date'] ) ) < time() ) ? 'overdue' : '' ); ?>" title="<?php echo $wpc_client->cc_date_format( $subtask['die_date'], 'date' ); ?>"><?php echo $wpc_client->cc_date_format( $subtask['die_date'], 'date' ); ?></a>
                                        <?php } ?>
                                        <a href="javascript:void(0);" class="tag status_tag" title="<?php echo $subtask['status']; ?>"><?php echo ucfirst( $subtask['status'] ); ?></a>

                                    </div>
                                </div>
                            <?php } } } ?>
                        <a id="wpc_next_page" href="javascript:void(0);"></a>
                </div>
                <a href="javascript: void(0);" class="toggle_tasks" data-type="hide"><?php _e( 'Hide All Completed/Closed tasks', WPC_CLIENT_TEXT_DOMAIN ); ?></a>
                <?php if( $auto_hide_days > 0 ) { ?>
                    &nbsp;&nbsp;&nbsp;&nbsp;
                    <label id="auto_hide_days_box">
                        <input type="checkbox" name="auto_hide_days" id="auto_hide_days" checked="checked" value="1" />
                        <?php printf( __( 'only last %s days', WPC_CLIENT_TEXT_DOMAIN ), $auto_hide_days ); ?>
                    </label>
                <?php } ?>
            </div>
        </div>
        <div class="wpc_right_column wpc_rail_task_block">
            <div class="col-wrap">
                <div class="wpc_pm_task">
                    <div class="wpc-toolbar clearfix task_top_bar">
                        <ul class="nav nav-pills pull-left">
                            <li class="dropdown assign_dropdown">
                                <a data-toggle="dropdown" class="dropdown-toggle">
                                    None
                                    <?php if( current_user_can('wpc_pm_level_3') ) { ?>
                                        <span class="caret"></span>
                                    <?php } ?>
                                </a>
                                <?php if( current_user_can('wpc_pm_level_3') ) { ?>
                                    <ul class="dropdown-menu">
                                        <?php foreach( $project_users as $val ) { $short_name = get_user_meta( $val['ID'], 'first_name', true ); $short_name = $short_name ? $short_name : $val['user_login']; $short_name = substr( $short_name, 0, 2 ); $full_name = $this->get_full_name( $val['ID'] ); $full_name = !empty( $full_name ) ? $full_name : $val['user_login']; $name = get_user_meta( $val['ID'], 'first_name', true ); $name = substr( $name ? $name : $val['user_login'], 0, 10 ) . ( strlen( $name ? $name : $val['user_login'] ) > 10 ? '...' : '' ); ?>
                                            <li>
                                                <a href="javascript:void(0);" data-color="<?php echo $val['user_color']; ?>" data-id="<?php echo $val['ID']; ?>" data-short-name="<?php echo strtoupper( $short_name ); ?>" data-full-name="<?php echo $full_name; ?>" data-name="<?php echo $name; ?>"><?php echo $full_name; ?></a>
                                            </li>
                                        <?php } ?>
                                    </ul>
                                    <span class="close-but"></span>
                                <?php } ?>
                            </li>
                            <li class="die_date_outer">
                                <a href="javascript:void(0);" class="die_date_link <?php if( current_user_can('wpc_pm_level_3') ) { ?>die_date_access<?php } ?>"><span class="die_date_text"><?php _e( 'Due date', WPC_CLIENT_TEXT_DOMAIN ); ?></span></a>
                                <?php if( current_user_can('wpc_pm_level_3') ) { ?>
                                    <span class="close-but"></span>
                                <?php } ?>
                            </li>
                            <?php if( current_user_can('wpc_pm_level_3') ) { ?>
                                <li class="dropdown milestone_dropdown">
                                    <a data-toggle="dropdown" class="dropdown-toggle">
                                        <?php _e( "Milestone", WPC_CLIENT_TEXT_DOMAIN ); ?> <span class="caret"></span>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <?php foreach( $milestones as $val ) { ?>
                                            <li>
                                                <a href="javascript:void(0);" data-id="<?php echo $val['id']; ?>" data-name="<?php echo addslashes( $val['title'] ); ?>"><?php echo $val['title']; ?></a>
                                            </li>
                                        <?php } ?>
                                    </ul>
                                </li>
                            <?php } ?>
                        </ul>
                        <ul class="nav nav-pills pull-right">
                            <li class="dropdown status task_priority_select">
                                <a data-toggle="dropdown" class="dropdown-toggle">
                                    Normal
                                    <?php if( current_user_can('wpc_pm_level_3') ) { ?>
                                        <span class="caret"></span>
                                    <?php } ?>
                                </a>
                                <?php if( current_user_can('wpc_pm_level_3') ) { ?>
                                    <ul class="dropdown-menu pull-right">
                                        <li><a href="#" rel="low"><?php _e( 'Low', WPC_CLIENT_TEXT_DOMAIN ); ?></a></li>
                                        <li><a href="#" rel="normal"><?php _e( 'Normal', WPC_CLIENT_TEXT_DOMAIN ); ?></a></li>
                                        <li><a href="#" rel="high"><?php _e( 'High', WPC_CLIENT_TEXT_DOMAIN ); ?></a></li>
                                        <li><a href="#" rel="urgent"><?php _e( 'Urgent', WPC_CLIENT_TEXT_DOMAIN ); ?></a></li>
                                    </ul>
                                <?php } ?>
                            </li>
                            <li class="dropdown status task_status">
                                <a data-toggle="dropdown" class="dropdown-toggle">
                                    New <span class="caret"></span>
                                </a>
                                <ul class="dropdown-menu pull-right">
                                    <li><a href="#" rel="new"><?php _e( 'New', WPC_CLIENT_TEXT_DOMAIN ); ?></a></li>
                                    <li><a href="#" rel="open"><?php _e( 'Open', WPC_CLIENT_TEXT_DOMAIN ); ?></a></li>
                                    <li><a href="#" rel="pending"><?php _e( 'Pending', WPC_CLIENT_TEXT_DOMAIN ); ?></a></li>
                                    <li><a href="#" rel="testing"><?php _e( 'Testing', WPC_CLIENT_TEXT_DOMAIN ); ?></a></li>
                                    <li><a href="#" rel="completed"><?php _e( 'Completed', WPC_CLIENT_TEXT_DOMAIN ); ?></a></li>
                                    <li><a href="#" rel="closed"><?php _e( 'Closed', WPC_CLIENT_TEXT_DOMAIN ); ?></a></li>
                                </ul>
                            </li>
                            <?php if( current_user_can('wpc_pm_level_3') ) { ?>
                                <li class="dropdown additional_actions">
                                    <a data-toggle="dropdown" class="dropdown-toggle">
                                        <span class="caret"></span>
                                    </a>
                                    <ul class="dropdown-menu pull-right">
                                        <li><a href="javascript:void(0);" class="detele_task"><?php _e( 'Delete Task', WPC_CLIENT_TEXT_DOMAIN ); ?></a></li>
                                        <li><a href="javascript:void(0);" class="add_subtask"><?php _e( 'Add Subtask', WPC_CLIENT_TEXT_DOMAIN ); ?></a></li>
                                    </ul>
                                </li>
                            <?php } ?>
                        </ul>
                    </div>

                    <form class="task_form">
                        <h3 class="title"><?php if( current_user_can('wpc_pm_level_3') ) { ?><a href="javascript:void(0);" class="edit_title_link"></a><?php } ?></h3>
                        <label class="tags_label">
                            <?php _e( 'Tags', WPC_CLIENT_TEXT_DOMAIN ); ?>:<br />
                            <input type="text" name="tags" class="tags" />
                        </label>

                        <ul class="menu task_details_menu">
                            <li id="task_description_tab" class="active"><a href="javascript:void(0);" ><?php _e( 'Description', WPC_CLIENT_TEXT_DOMAIN ); ?></a></li>
                            <li id="task_activity_tab"><a href="javascript:void(0);" ><?php _e( 'Activity', WPC_CLIENT_TEXT_DOMAIN ); ?></a></li>
                            <li id="task_messages_tab"><a href="javascript:void(0);" ><?php _e( 'Messages', WPC_CLIENT_TEXT_DOMAIN ); ?> (<span class="wpc_pm_messages_counter">0</span>)</a></li>
                            <li id="task_files_tab"><a href="javascript:void(0);" ><?php _e( 'Files', WPC_CLIENT_TEXT_DOMAIN ); ?> (<span class="wpc_pm_files_counter">0</span>)</a></li>
                        </ul>
                        <div id="div_task_description_tab" class="task_tabs_content"><?php if( current_user_can('wpc_pm_level_3') ) { ?><a href="javascript:void(0);" class="edit_description_link"></a><?php } ?></div>
                        <div id="div_task_activity_tab" class="task_tabs_content">
                        </div>
                        <div id="div_task_messages_tab" class="task_tabs_content">
                            <div class="message_form">
                                <textarea name="task_message" class="task_message" rows="5"></textarea>
                                <div id="uploader_warning_messages" style="display: none;"></div>
                                <br clear="all" />
                                <a href="javascript: void(0);" class="wpc_pm_add_file_link"><?php _e( "Add Attachment(s)", WPC_CLIENT_TEXT_DOMAIN ); ?></a>
                                <div class="wpc_pm_add_file_block">
                                    <?php if( isset( $settings['flash_uploader'] ) && 'html5' == $settings['flash_uploader'] ) { ?>
                                        <div class="wpc_st_button_addfile">
                                            <input class="file_upload_messages" name="Filedata" type="file" multiple="true">
                                        </div>
                                        <div id="queue_messages"></div>
                                    <?php } else { ?>
                                        <div class="wpc_pluploder_task_messages_queue">
                                            <p><?php _e( "Your browser doesn't have Flash, Silverlight or HTML5 support.", WPC_CLIENT_TEXT_DOMAIN ) ?></p>
                                        </div>
                                    <?php } ?>
                                </div>
                                <input type="button" class="button-primary task_message_send" value="<?php _e( "Send", WPC_CLIENT_TEXT_DOMAIN ); ?>" />
                            </div>
                            <div class="task_messages_list">

                            </div>
                        </div>
                        <div id="div_task_files_tab" class="task_tabs_content">
                            <div id="uploader_warning" style="display: none;"></div>
                            <br clear="all" />
                            <?php if( isset( $settings['flash_uploader'] ) && 'html5' == $settings['flash_uploader'] ) { ?>
                                <div class="wpc_st_button_addfile">
                                    <input class="file_upload_ajax" name="Filedata" type="file" multiple="true">
                                </div>
                                <div id="queue"></div>
                                <div class="wpc_clear"></div>
                                <input type="button" class="button-primary task_files_submit" value="<?php _e( "Attach File(s) to Task", WPC_CLIENT_TEXT_DOMAIN ); ?>" />
                            <?php } else { ?>
                                <div class="wpc_pluploder_task_files_queue">
                                    <p><?php _e( "Your browser doesn't have Flash, Silverlight or HTML5 support.", WPC_CLIENT_TEXT_DOMAIN ) ?></p>
                                </div>
                            <?php } ?>
                            <div id="wpc_pm_file_list">
                            </div>
                        </div>
                    </form>
                    <!--<div class="task_folowers">
                        <table width="100%">
                            <tr>
                                <td valign="middle" width="15">
                                    <a href="javascript:void(0);" class="my_follow"></a>
                                </td>
                                <td valign="middle" style="border-right: 1px solid #ddd;" width="60">
                                    <a href="javascript:void(0);" class="follow_link">
                                        <?php _e( "Followers", WPC_CLIENT_TEXT_DOMAIN ); ?>
                                    </a>
                                </td>
                                <td>
                                    <input type="text" name="followers" class="followers" />
                                </td>
                        </table>
                    </div>-->
                </div>

                <div class="wpc-toolbar clearfix task_top_bar bulk_actions_bar">
                    <ul class="nav nav-pills pull-left">
                        <?php if( current_user_can('wpc_pm_level_3') ) { ?>
                            <li class="dropdown assign_dropdown">
                                <a data-toggle="dropdown" class="dropdown-toggle">
                                    None <span class="caret"></span>
                                </a>
                                <ul class="dropdown-menu">
                                    <?php foreach( $project_users as $val ) { $short_name = get_user_meta( $val['ID'], 'first_name', true ); $short_name = $short_name ? $short_name : $val['user_login']; $short_name = substr( $short_name, 0, 2 ); $full_name = $this->get_full_name( $val['ID'] ); $full_name = !empty( $full_name ) ? $full_name : $val['user_login']; $name = get_user_meta( $val['ID'], 'first_name', true ); $name = substr( $name ? $name : $val['user_login'], 0, 10 ) . ( strlen( $name ? $name : $val['user_login'] ) > 10 ? '...' : '' ); ?>
                                        <li>
                                            <a href="javascript:void(0);" data-color="<?php echo $val['user_color']; ?>" data-id="<?php echo $val['ID']; ?>" data-short-name="<?php echo strtoupper( $short_name ); ?>" data-full-name="<?php echo $full_name; ?>" data-name="<?php echo $name; ?>"><?php echo $full_name; ?></a>
                                        </li>
                                    <?php } ?>
                                </ul>
                            </li>
                        <?php } ?>
                        <?php if( current_user_can('wpc_pm_level_3') ) { ?>
                            <li>
                                <a href="javascript:void(0);" class="die_date_link die_date_access"><span class="die_date_text"><?php _e( 'Due date', WPC_CLIENT_TEXT_DOMAIN ); ?></span></a>
                            </li>
                        <?php } ?>
                        <?php if( current_user_can('wpc_pm_level_3') ) { ?>
                            <li class="dropdown milestone_dropdown">
                                <a data-toggle="dropdown" class="dropdown-toggle">
                                    <?php _e( "Milestone", WPC_CLIENT_TEXT_DOMAIN ); ?> <span class="caret"></span>
                                </a>
                                <ul class="dropdown-menu">
                                    <?php foreach( $milestones as $val ) { ?>
                                        <li>
                                            <a href="javascript:void(0);" data-id="<?php echo $val['id']; ?>" data-name="<?php echo addslashes( $val['title'] ); ?>"><?php echo $val['title']; ?></a>
                                        </li>
                                    <?php } ?>
                                </ul>
                            </li>
                        <?php } ?>
                    </ul>
                    <ul class="nav nav-pills pull-right">
                        <?php if( current_user_can('wpc_pm_level_3') ) { ?>
                            <li class="dropdown status task_priority_select">
                                <a data-toggle="dropdown" class="dropdown-toggle">
                                    None <span class="caret"></span>
                                </a>
                                <ul class="dropdown-menu pull-right">
                                    <li><a href="#" rel="low"><?php _e( 'Low', WPC_CLIENT_TEXT_DOMAIN ); ?></a></li>
                                    <li><a href="#" rel="normal"><?php _e( 'Normal', WPC_CLIENT_TEXT_DOMAIN ); ?></a></li>
                                    <li><a href="#" rel="high"><?php _e( 'High', WPC_CLIENT_TEXT_DOMAIN ); ?></a></li>
                                    <li><a href="#" rel="urgent"><?php _e( 'Urgent', WPC_CLIENT_TEXT_DOMAIN ); ?></a></li>
                                </ul>
                            </li>
                        <?php } ?>
                        <li class="dropdown status task_status">
                            <a data-toggle="dropdown" class="dropdown-toggle">
                                None <span class="caret"></span>
                            </a>
                            <ul class="dropdown-menu pull-right">
                                <li><a href="#" rel="new"><?php _e( 'New', WPC_CLIENT_TEXT_DOMAIN ); ?></a></li>
                                <li><a href="#" rel="open"><?php _e( 'Open', WPC_CLIENT_TEXT_DOMAIN ); ?></a></li>
                                <li><a href="#" rel="pending"><?php _e( 'Pending', WPC_CLIENT_TEXT_DOMAIN ); ?></a></li>
                                <li><a href="#" rel="testing"><?php _e( 'Testing', WPC_CLIENT_TEXT_DOMAIN ); ?></a></li>
                                <li><a href="#" rel="completed"><?php _e( 'Completed', WPC_CLIENT_TEXT_DOMAIN ); ?></a></li>
                                <li><a href="#" rel="closed"><?php _e( 'Closed', WPC_CLIENT_TEXT_DOMAIN ); ?></a></li>
                            </ul>
                        </li>
                        <?php if( current_user_can('wpc_pm_level_3') ) { ?>
                            <li class="dropdown additional_actions">
                                <a data-toggle="dropdown" class="dropdown-toggle">
                                    <span class="caret"></span>
                                </a>
                                <ul class="dropdown-menu pull-right">
                                    <li><a href="javascript:void(0);" class="detele_task"><?php _e( 'Delete Task', WPC_CLIENT_TEXT_DOMAIN ); ?></a></li>
                                </ul>
                            </li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<div style="display: none;">
    <div id="add_task_box">
        <h3 class="title"></h3>
        <p>
            <label>
                <?php _e( "Title", WPC_CLIENT_TEXT_DOMAIN ); ?>
                <br />
                <input type="text" name="task_title" class="task_title" value="" />
            </label>
        </p>
        <p>
            <label>
                <?php _e( "Description", WPC_CLIENT_TEXT_DOMAIN ); ?>
                <br />
                <textarea name="task_description" class="task_description" rows="5"></textarea>
            </label>
        </p>
        <p>
            <label>
                <?php _e( "Assigned user", WPC_CLIENT_TEXT_DOMAIN ); ?>
                <br />
                <select class="add_task_assigned_user" name="add_task_assigned_user">
                    <option value=""></option>
                    <?php foreach( $project_users as $val ) { $full_name = $this->get_full_name( $val['ID'] ); $full_name = !empty( $full_name ) ? $full_name : $val['user_login']; ?>
                        <option value="<?php echo $val['ID']; ?>"><?php echo $full_name; ?></option>
                    <?php } ?>
                </select>
            </label>
        </p>
        <p>
            <label>
                <?php _e( "Due date", WPC_CLIENT_TEXT_DOMAIN ); ?>
                <a href="javascript: void(0);" class="quick_due_date" data-date="<?php echo strtotime( date('Y-m-d 00:00:00') ); ?>"><?php _e( "Today", WPC_CLIENT_TEXT_DOMAIN ); ?></a> -
                <a href="javascript: void(0);" class="quick_due_date" data-date="<?php echo strtotime( date( 'Y-m-d 00:00:00', strtotime('+1 day') ) ); ?>"><?php _e( "Tomorrow", WPC_CLIENT_TEXT_DOMAIN ); ?></a> -
                <a href="javascript: void(0);" class="quick_due_date" data-date="<?php echo strtotime( date( 'Y-m-d 00:00:00', strtotime('+5 day') ) ); ?>"><?php _e( "5 Days", WPC_CLIENT_TEXT_DOMAIN ); ?></a> -
                <a href="javascript: void(0);" class="quick_due_date" data-date="<?php echo strtotime( date( 'Y-m-d 00:00:00', strtotime('+1 month') ) ); ?>"><?php _e( "1 Month", WPC_CLIENT_TEXT_DOMAIN ); ?></a>
                <br />
                <input type="text" class="add_task_due_date custom_datepicker_field" name="fake_add_task_due_date" />
                <input type="hidden" name="add_task_due_date" />
            </label>
        </p>
        <p>
            <label>
                <?php _e( "Priority", WPC_CLIENT_TEXT_DOMAIN ); ?>
                <br />
                <select class="add_task_priority" name="add_task_priority">
                    <option value="low"><?php _e( 'Low', WPC_CLIENT_TEXT_DOMAIN ); ?></option>
                    <option value="normal" selected="selected"><?php _e( 'Normal', WPC_CLIENT_TEXT_DOMAIN ); ?></option>
                    <option value="high"><?php _e( 'High', WPC_CLIENT_TEXT_DOMAIN ); ?></option>
                    <option value="urgent"><?php _e( 'Urgent', WPC_CLIENT_TEXT_DOMAIN ); ?></option>
                </select>
            </label>
        </p>
        <p align="center">
            <input type="button" class="button-primary task_add_button" value="<?php _e( "Add task", WPC_CLIENT_TEXT_DOMAIN ); ?>" />
            <input type="button" class="button-primary task_add_button add_and_create" value="<?php _e( "Add task &amp; Create another", WPC_CLIENT_TEXT_DOMAIN ); ?>" />
        </p>
    </div>
</div>