<?php
if ( ! defined( 'ABSPATH' ) ) { exit; } if( !current_user_can('wpc_pm_level_3') ) { ?>
    <style>
        .edit_description_link, .edit_title_link, .edit_task_list_link, .ui-datepicker-trigger {
            display: none !important;
        }
    </style>
<?php } ?>
<script type="text/javascript">
    jQuery(document).ready(function() {
        var wpc_pm_selected_milestone = 0;
        var wpc_pm_active_tasks = new Array();
        var previous_html = '';

        var window_height = jQuery( window ).height() - jQuery('.milestone_table').offset().top;
        var menu_height = jQuery('#adminmenu').height() - jQuery('.milestone_table').offset().top;
        jQuery('.milestone_table') .css({
            'height': ( window_height > menu_height ? window_height : menu_height ) +'px',
            'max-height': ( window_height > menu_height ? window_height : menu_height ) +'px'
        });

        jQuery('.wpc_pm_milestone').hide();

        jQuery('.wpc_pm_milestone .die_date').datepicker("option", 'onClose', function() {
            var d = jQuery(this).datepicker('getDate');
            d.setHours(0, -d.getTimezoneOffset(), 0, 0);
            var due_date = d.getTime() / 1000;
            var due_date_text = jQuery(this).val();
            jQuery.ajax({
                type     : 'POST',
                dataType : 'json',
                url      : '<?php echo get_admin_url(); ?>admin-ajax.php',
                data     : 'action=wpc_pm_milestone&act=save_due_date&milestone_id=' + wpc_pm_selected_milestone + '&due_date=' + due_date,
                success  : function( data ){
                    if( data.status ) {
                        jQuery('.milestone_id[value=' + wpc_pm_selected_milestone + ']').siblings('.milestone_die_date').html( due_date_text );
                    } else {
                        alert( data.message );
                    }
                }
             });
        });

        var full_width = jQuery('.wpc_pm_main_block').width();
        jQuery('.wpc_left_column').css('width', parseInt( full_width * 0.55 ) + 'px');
        jQuery('.wpc_right_column').css('width', parseInt( full_width * 0.44 ) + 'px');

        jQuery( window ).resize(function(){
            var full_width = jQuery('.wpc_pm_main_block').width();
            jQuery('.wpc_left_column').css('width', parseInt( full_width * 0.55 ) + 'px');
            jQuery('.wpc_right_column').css('width', parseInt( full_width * 0.44 ) + 'px');
        });

        jQuery( '.add-new-milestone').shutter_box({
            view_type       : 'lightbox',
            width           : '500px',
            type            : 'inline',
            href            : '#add_milestone_box',
            title           : '<?php _e( 'New Milestone', WPC_CLIENT_TEXT_DOMAIN ) ?>'
        });

        jQuery('.milestone_add_button').click(function() {
            var error = '';
            var title = jQuery('#add_milestone_box .milestone_title').val();
            if( title == '' ) {
                error = '<?php _e( 'Milestone title is empty. Please add it.', WPC_CLIENT_TEXT_DOMAIN ); ?>';
            }

            var description = jQuery('#add_milestone_box .milestone_description').val();
            var die_date = jQuery('#add_milestone_box .milestone_die_date').next('input[type="hidden"]').val();

            jQuery( '.add-new-milestone').shutter_box('close');
            if( !error ) {
                jQuery.ajax({
                    type     : 'POST',
                    dataType : 'json',
                    url      : '<?php echo get_admin_url(); ?>admin-ajax.php',
                    data     : 'action=wpc_pm_milestone&act=add&project_id=<?php echo( ( isset( $_GET['project_id'] ) && is_numeric( $_GET['project_id'] ) ) ? $_GET['project_id'] : 0 ); ?>&title=' + title + '&description=' + description + '&die_date=' + die_date,
                    success  : function( data ){
                        if( data.status ) {
                            wpc_pm_selected_milestone = data.message.id;
                            jQuery('.wpc_pm_milestone .title').html( linkifyText( title ) + '<a href="javascript:void(0);" class="edit_title_link"></a>' );
                            jQuery('.wpc_pm_milestone .description').html( linkifyText( multiString( description ) ) + '<a href="javascript:void(0);" class="edit_description_link"></a>' );
                            jQuery('.wpc_pm_milestone .die_date').next('input[type="hidden"]').val( die_date );
                            jQuery('.wpc_pm_milestone .die_date').next('input[type="hidden"]').trigger('change');
                            jQuery('.wpc_pm_milestone .task_list').html('');
                            if( !jQuery('.edit_task_list_link').length ) {
                                jQuery('.wpc_pm_milestone .task_list').parent().append('<a href="javascript:void(0);" class="edit_task_list_link"><?php _e( 'Edit task list', WPC_CLIENT_TEXT_DOMAIN ); ?></a>');
                            }
                            jQuery('.wpc_pm_milestone .task_list').html('<?php _e( 'No Tasks Yet', WPC_CLIENT_TEXT_DOMAIN ); ?>');

                            jQuery('.wpc_pm_milestone').show();
                            jQuery('.milestone_row').removeClass('active');
                            jQuery('.milestone_table').append('<div class="milestone_row wpc_edit_milestone active">' +
                                '<input type="hidden" name="milestone_id[]" class="milestone_id" value="' + data.message.id + '">' +
                                '<div class="milestone_title">' + title + '</div>' +
                                '<div class="milestone_die_date">' + jQuery('.wpc_pm_milestone .die_date').val() + '</div>' +
                                '<div class="milestone_progress_bar" id="milestone_progress_bar' + data.message.id + '"><div class="progress-label"><?php _e( 'No Tasks Yet', WPC_CLIENT_TEXT_DOMAIN ); ?></div></div>' +
                                '<a href="javascript:void(0);" class="detele_milestone"></a>' +
                            '</div>');

                            jQuery('#milestone_progress_bar' + data.message.id).progressbar({
                                value: 0
                            });

                            jQuery('.submit_task_list').remove();
                            jQuery('.cancel_task_list').remove();

                            jQuery('#add_milestone_box .milestone_title').val('');
                            jQuery('#add_milestone_box .milestone_description').val('');
                            jQuery('#add_milestone_box .milestone_die_date').val('');
                        } else {
                            alert( data.message );
                        }
                    }
                 });
            } else {
                alert( error );
            }
        });

        jQuery( '.detele_milestone' ).live('click', function() {
            if( confirm('<?php _e( 'Are you sure?', WPC_CLIENT_TEXT_DOMAIN ); ?>') ) {
                var obj = jQuery(this);
                var milestone_id = jQuery(this).siblings('.milestone_id').val();
                jQuery.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: '<?php echo get_admin_url(); ?>admin-ajax.php',
                    data: 'action=wpc_pm_milestone&act=remove&milestone_id=' + milestone_id,
                    success: function (data) {
                        if (data.status) {
                            jQuery('.wpc_pm_milestone').hide();
                            obj.parent('.milestone_row').remove();
                        } else {
                            alert(data.message);
                        }
                    }
                });
            }
        });

        jQuery('.wpc_edit_milestone').live('click', function() {
            jQuery('.milestone_row').removeClass('active');
            if( jQuery(this).hasClass('milestone_row') ) {
                jQuery( this ).addClass('active');
            } else {
                jQuery( this ).parents('.milestone_row').addClass('active');
            }
            var milestone_id = jQuery( this ).find('.milestone_id').val();

            if( !isNaN( milestone_id ) ) {
                jQuery.ajax({
                    type     : 'POST',
                    dataType : 'json',
                    url      : '<?php echo get_admin_url(); ?>admin-ajax.php',
                    data     : 'action=wpc_pm_milestone&act=get_details&milestone_id=' + milestone_id,
                    success  : function( data ){
                        if( data.status ) {
                            var milestone_details = data.message;

                            jQuery('.wpc_pm_milestone .title').html( linkifyText( milestone_details.title ) + '<a href="javascript:void(0);" class="edit_title_link"></a>' );
                            jQuery('.wpc_pm_milestone .description').html( linkifyText( multiString( milestone_details.description ) ) + '<a href="javascript:void(0);" class="edit_description_link"></a>' );
                            jQuery('.wpc_pm_milestone .die_date').next('input[type="hidden"]').val( milestone_details.die_date );
                            jQuery('.wpc_pm_milestone .die_date').next('input[type="hidden"]').trigger('change');
                            jQuery('.wpc_pm_milestone .task_list').html('<?php _e( 'No Tasks Yet', WPC_CLIENT_TEXT_DOMAIN ); ?>');
                            for( var i = 0; i < milestone_details.task_list.length; i++ ) {
                                jQuery('.wpc_pm_milestone .task_list').append('<div class="task_list_row">' +
                                        '<div class="id_in_project">' + milestone_details.task_list[ i ].id_in_project + '</div>' +
                                        '<div class="task_title">' + linkifyText( milestone_details.task_list[ i ].title ) + '</div>' +
                                        '<div class="task_status">' + milestone_details.task_list[ i ].status + '</div>' +
                                    '</div>'
                                );
                            }
                            if( !jQuery('.edit_task_list_link').length ) {
                                jQuery('.wpc_pm_milestone .task_list').parent().append('<a href="javascript:void(0);" class="edit_task_list_link"><?php _e( 'Edit task list', WPC_CLIENT_TEXT_DOMAIN ); ?></a>');
                            }

                            jQuery('.wpc_pm_milestone').show();
                            wpc_pm_selected_milestone = milestone_id;

                            jQuery('.submit_task_list').remove();
                            jQuery('.cancel_task_list').remove();
                        } else {
                            alert( data.message );
                        }
                    }
                });
            }
        });

        jQuery('.edit_title_link').live('click', function() {
            if( !isNaN( wpc_pm_selected_milestone ) && wpc_pm_selected_milestone > 0 ) {
                var obj = jQuery(this);
                jQuery.ajax({
                    type     : 'POST',
                    dataType : 'json',
                    url      : '<?php echo get_admin_url(); ?>admin-ajax.php',
                    data     : 'action=wpc_pm_milestone&act=get_details&milestone_id=' + wpc_pm_selected_milestone,
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
            if( !isNaN( wpc_pm_selected_milestone ) && wpc_pm_selected_milestone > 0 ) {
                var title = jQuery(this).val();
                var obj = jQuery(this);
                jQuery.ajax({
                    type     : 'POST',
                    dataType : 'json',
                    url      : '<?php echo get_admin_url(); ?>admin-ajax.php',
                    data     : 'action=wpc_pm_milestone&act=save_title&milestone_id=' + wpc_pm_selected_milestone + '&title=' + title,
                    success  : function( data ){
                        if( data.status ) {
                            obj.parent().html( linkifyText( title ) + '<a href="javascript:void(0);" class="edit_title_link"></a>' );
                            jQuery('.milestone_row .milestone_id[value="'+ wpc_pm_selected_milestone + '"]').siblings('.milestone_title').html( title );
                        } else {
                            alert( data.message );
                        }
                    }
                });
            }
        });

        jQuery('#title_textarea').live('keypress', function(e) {
            if( e.which == 13 && !isNaN( wpc_pm_selected_milestone ) && wpc_pm_selected_milestone > 0 ) {
                var title = jQuery(this).val();
                var obj = jQuery(this);
                jQuery.ajax({
                    type     : 'POST',
                    dataType : 'json',
                    url      : '<?php echo get_admin_url(); ?>admin-ajax.php',
                    data     : 'action=wpc_pm_milestone&act=save_title&milestone_id=' + wpc_pm_selected_milestone + '&title=' + title,
                    success  : function( data ){
                        if( data.status ) {
                            obj.parent().html( linkifyText( title ) + '<a href="javascript:void(0);" class="edit_title_link"></a>' );
                            jQuery('.milestone_row .milestone_id[value="'+ wpc_pm_selected_milestone + '"]').siblings('.milestone_title').html( title );
                        } else {
                            alert( data.message );
                        }
                    }
                });
                return false;
            }
        });

        jQuery('.edit_description_link').live('click', function() {
            if( !isNaN( wpc_pm_selected_milestone ) && wpc_pm_selected_milestone > 0 ) {
                var obj = jQuery(this);
                jQuery.ajax({
                    type     : 'POST',
                    dataType : 'json',
                    url      : '<?php echo get_admin_url(); ?>admin-ajax.php',
                    data     : 'action=wpc_pm_milestone&act=get_details&milestone_id=' + wpc_pm_selected_milestone,
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
            if( !isNaN( wpc_pm_selected_milestone ) && wpc_pm_selected_milestone > 0 ) {
                var description = jQuery(this).val();
                var obj = jQuery(this);
                jQuery.ajax({
                    type     : 'POST',
                    dataType : 'json',
                    url      : '<?php echo get_admin_url(); ?>admin-ajax.php',
                    data     : 'action=wpc_pm_milestone&act=save_description&milestone_id=' + wpc_pm_selected_milestone + '&description=' + description,
                    success  : function( data ){
                        if( data.status ) {
                            obj.parent().html( linkifyText( multiString( description ) ) + '<a href="javascript:void(0);" class="edit_description_link"></a>' );
                        } else {
                            alert( data.message );
                        }
                    }
                });
            }
        });

        jQuery('.edit_task_list_link').live('click', function() {
            if( !isNaN( wpc_pm_selected_milestone ) && wpc_pm_selected_milestone > 0 ) {
                var obj = jQuery(this);
                jQuery.ajax({
                    type     : 'POST',
                    dataType : 'json',
                    url      : '<?php echo get_admin_url(); ?>admin-ajax.php',
                    data     : 'action=wpc_pm_milestone&act=get_task_list&milestone_id=' + wpc_pm_selected_milestone,
                    success  : function( data ){
                        if( data.status ) {
                            previous_html = obj.siblings('.task_list').html();
                            obj.siblings('.task_list').html('');
                            for( var i = 0; i < data.message.tasks.length; i++ ) {
                                obj.siblings('.task_list').append('<div class="task_list_row">' +
                                        '<div class="id_in_project"><input type="checkbox" value="' + data.message.tasks[ i ].id + '" ' + ( data.message.tasks[ i ].milestone_id > 0 ? 'checked="checked"' : '' ) + ' /> ' + data.message.tasks[ i ].id_in_project + '</div>' +
                                        '<div class="task_title">' + linkifyText( data.message.tasks[ i ].title ) + '</div>' +
                                        '<div class="task_status">' + data.message.tasks[ i ].status + '</div>' +
                                    '</div>');
                            }

                            obj.parent().append('<input type="button" name="cancel" class="cancel_task_list button" value="<?php _e( 'Cancel', WPC_CLIENT_TEXT_DOMAIN ); ?>" />' +
                                '<input type="button" name="submit" value="<?php _e( 'Save Selected Tasks', WPC_CLIENT_TEXT_DOMAIN ); ?>" class="submit_task_list button-primary" />');
                            jQuery('.edit_task_list_link').remove();

                            wpc_pm_active_tasks = new Array();
                            for( i = 0; i < data.message.tasks.length; i++ ) {
                                if( data.message.tasks[ i ].milestone_id != 0 ) {
                                    wpc_pm_active_tasks.push( data.message.tasks[ i ].id );
                                }
                            }

                        } else {
                            alert( data.message );
                        }
                    }
                });
            }
        });

        jQuery('.submit_task_list').live('click', function() {
            if( !isNaN( wpc_pm_selected_milestone ) && wpc_pm_selected_milestone > 0 ) {
                wpc_pm_active_tasks = new Array();
                jQuery(this).siblings('.task_list').find('input:checked').each(function() {
                    wpc_pm_active_tasks.push( jQuery(this).val() );
                });

                var obj = jQuery(this);
                jQuery.ajax({
                    type     : 'POST',
                    dataType : 'json',
                    url      : '<?php echo get_admin_url(); ?>admin-ajax.php',
                    data     : 'action=wpc_pm_milestone&act=save_task_list&milestone_id=' + wpc_pm_selected_milestone + '&task_list_ids=' + wpc_pm_active_tasks.join(','),
                    success  : function( data ){
                        if( data.status ) {
                            obj.siblings('.task_list').find('.task_list_row .id_in_project input:not(:checked)').each(function() {
                                jQuery(this).parents('.task_list_row').remove();
                            });
                            obj.siblings('.task_list').find('.task_list_row .id_in_project input:checked').remove();
                            obj.parent().append('<a href="javascript:void(0);" class="edit_task_list_link"><?php _e( 'Edit task list', WPC_CLIENT_TEXT_DOMAIN ); ?></a>');
                            jQuery('.submit_task_list').remove();
                            jQuery('.cancel_task_list').remove();
                            if( !isNaN( data.percent ) && data.percent  >= 0 ) {
                                jQuery('#milestone_progress_bar' + wpc_pm_selected_milestone).progressbar( "value", data.percent  );
                                if( wpc_pm_active_tasks.length ) {
                                    jQuery('#milestone_progress_bar' + wpc_pm_selected_milestone + ' .progress-label').html('<?php echo __( 'Completed', WPC_CLIENT_TEXT_DOMAIN ); ?>: ' + data.percent  + '%');
                                    jQuery('#milestone_progress_bar' + wpc_pm_selected_milestone + ' .bar_counter').remove();
                                    jQuery('#milestone_progress_bar' + wpc_pm_selected_milestone + ' .progress-label').parent().append('<span class="bar_counter">' + data.completed_count + ' <?php _e( 'of', WPC_CLIENT_TEXT_DOMAIN); ?> ' + data.total_count + '</span>');
                                    if( data.percent  < 50 ) {
                                        jQuery('#milestone_progress_bar' + wpc_pm_selected_milestone + ' .ui-widget-header').css('background-image', "url('<?php echo $this->extension_url; ?>images/red_progress.png')");
                                    } else if( data.percent  < 75 ) {
                                        jQuery('#milestone_progress_bar' + wpc_pm_selected_milestone + ' .ui-widget-header').css('background-image', "url('<?php echo $this->extension_url; ?>images/orange_progress.png')");
                                    } else if( data.percent  < 100 ) {
                                        jQuery('#milestone_progress_bar' + wpc_pm_selected_milestone + ' .ui-widget-header').css('background-image', "url('<?php echo $this->extension_url; ?>images/yellow_progress.png')");
                                    } else {
                                        jQuery('#milestone_progress_bar' + wpc_pm_selected_milestone + ' .ui-widget-header').css('background-image', "url('<?php echo $this->extension_url; ?>images/green_progress.png')");
                                    }
                                } else {
                                    jQuery('#milestone_progress_bar' + wpc_pm_selected_milestone + ' .progress-label').html('<?php _e( 'No Tasks Yet', WPC_CLIENT_TEXT_DOMAIN ); ?>');
                                    jQuery('.wpc_pm_milestone .task_list').html('<?php _e( 'No Tasks Yet', WPC_CLIENT_TEXT_DOMAIN ); ?>');
                                    jQuery('#milestone_progress_bar' + wpc_pm_selected_milestone + ' .bar_counter').remove();
                                }
                            }
                        } else {
                            alert( data.message );
                        }
                    }
                });
            }
        });

        jQuery('.cancel_task_list').live('click', function() {
            if( !isNaN( wpc_pm_selected_milestone ) && wpc_pm_selected_milestone > 0 ) {
                jQuery(this).siblings('.task_list').html( previous_html );
                jQuery(this).parent().append('<a href="javascript:void(0);" class="edit_task_list_link"><?php _e( 'Edit task list', WPC_CLIENT_TEXT_DOMAIN ); ?></a>');
                jQuery('.submit_task_list').remove();
                jQuery('.cancel_task_list').remove();
            }
        });


            jQuery('.milestone_progress_bar').progressbar({
                create : function() {
                    var val = jQuery(this).data('percent');
                    var completed = jQuery(this).data('completed');
                    var tasks = jQuery(this).data('tasks');

                    if( val * 1 >= 0 ) {
                        jQuery(this).children('.progress-label').html('<?php _e( "Completed", WPC_CLIENT_TEXT_DOMAIN ); ?> ' + val + '%');
                        jQuery(this).children('.progress-label').parent().append('<span class="bar_counter">' + completed + ' <?php _e( 'of', WPC_CLIENT_TEXT_DOMAIN); ?> ' + tasks + '</span>');
                        jQuery(this).progressbar( "option", { value: val } );
                    } else {
                        jQuery(this).children('.progress-label').html('<?php _e( "No Tasks Yet - Create one", WPC_CLIENT_TEXT_DOMAIN ); ?>');
                        jQuery(this).children('.bar_counter').remove();
                        jQuery(this).progressbar( "option", { value: 0 } );
                    }

                    if( val < 50 ) {
                        jQuery(this).children('.ui-widget-header').css('background-image', "url('<?php echo $this->extension_url; ?>/images/red_progress.png')");
                    } else if( val < 75 ) {
                        jQuery(this).children('.ui-widget-header').css('background-image', "url('<?php echo $this->extension_url; ?>/images/orange_progress.png')");
                    } else if( val < 100 ) {
                        jQuery(this).children('.ui-widget-header').css('background-image', "url('<?php echo $this->extension_url; ?>/images/yellow_progress.png')");
                    } else {
                        jQuery(this).children('.ui-widget-header').css('background-image', "url('<?php echo $this->extension_url; ?>/images/green_progress.png')");
                    }

                }
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
                <?php if( current_user_can('wpc_pm_level_3') ) { ?>
                    <a class="add-new-milestone button-primary" href="#add_milestone_box"><?php _e( 'Add Milestone', WPC_CLIENT_TEXT_DOMAIN )?></a>
                <?php } ?>
                <div class="milestone_table">
                    <?php foreach( $milestones_list as $val ) { if( $val['tasks_count'] > 0 ) { $percent = round( $val['completed_tasks_count'] / $val['tasks_count'] * 100 ); } else { $percent = -1; } ?>
                        <div class="milestone_row wpc_edit_milestone">
                            <input type="hidden" name="milestone_id[]" class="milestone_id" value="<?php echo $val['id']; ?>" />
                            <div class="milestone_title"><?php echo $val['title']; ?></div>
                            <div class="milestone_die_date"><?php echo $wpc_client->cc_date_format( $val['die_date'], 'date' ); ?></div>
                            <div class="milestone_progress_bar" data-percent="<?php echo $percent; ?>" data-completed="<?php echo $val['completed_tasks_count']; ?>" data-tasks="<?php echo $val['tasks_count']; ?>" id="milestone_progress_bar<?php echo $val['id']; ?>" ><div class="progress-label"></div></div>
                            <a href="javascript:void(0);" class="detele_milestone"></a>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
        <div class="wpc_right_column">
            <div class="col-wrap">
                <div class="wpc_pm_milestone">
                    <h3 class="title"></h3>
                    <p class="description"></p>
                    <p class="die_date_outer">
                        <?php _e( "Due date:", WPC_CLIENT_TEXT_DOMAIN ); ?><br />
                        <input type="text" <?php if( !current_user_can('wpc_pm_level_3') ) { ?>disabled="disabled"<?php } ?> name="fake_die_date" class="die_date custom_datepicker_field change_month change_year" value="" />
                        <input type="hidden" name="die_date" value="" />

                    </p>
                    <div class="task_list_outer">
                        <a href="javascript:void(0);"  class="edit_task_list_link"><?php _e( 'Edit task list', WPC_CLIENT_TEXT_DOMAIN ); ?></a>
                        <div class="task_list_header">
                            <div class="id_in_project"><?php _e( "ID", WPC_CLIENT_TEXT_DOMAIN ); ?></div>
                            <div class="task_title"><?php _e( "Title", WPC_CLIENT_TEXT_DOMAIN ); ?></div>
                            <div class="task_status"><?php _e( "Status", WPC_CLIENT_TEXT_DOMAIN ); ?></div>
                        </div>
                        <div class="task_list">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div style="display: none;float:left;width:100%;">
    <div id="add_milestone_box" style="float:left;width:98%;">
        <p>
            <label>
                <?php _e( "Title", WPC_CLIENT_TEXT_DOMAIN ); ?>
                <br />
                <input type="text" name="milestone_title" class="milestone_title" style="float:left;width:100%;" value="" />
            </label>
        </p>
        <p>
            <label>
                <?php _e( "Description", WPC_CLIENT_TEXT_DOMAIN ); ?>
                <br />
                <textarea name="milestone_description" class="milestone_description" rows="5" style="float:left;width:100%;resize:vertical;"></textarea>
            </label>
        </p>
        <p>
            <label>
                <?php _e( "Due date", WPC_CLIENT_TEXT_DOMAIN ); ?>
                <br />
                <input type="text" name="fake_milestone_die_date" class="milestone_die_date custom_datepicker_field" value="" />
                <input type="hidden" name="milestone_die_date" value="" />
            </label>
        </p>
        <p align="center">
            <input type="button" class="button-primary milestone_add_button" value="<?php _e( "Add milestone", WPC_CLIENT_TEXT_DOMAIN ); ?>" />
        </p>
    </div>
</div>