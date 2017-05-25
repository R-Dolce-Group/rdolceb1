<?php
if ( ! defined( 'ABSPATH' ) ) { exit; } global $wpc_client; ?>
<script type="text/javascript">
    var wpc_activity_type = '';
    var date_filters_html = '';
    var params = new Array();
    var action = '';
    var filter_by = '';
    var filter_id = '';
    var projects_count = 0;
    jQuery(document).ready(function($) {

        jQuery(document).on('click', '.wpc_pm_project .wpc_pm_more_users', function(e) {
            jQuery( this ).toggleClass('active');
            jQuery( this ).parents('.wpc_pm_project_items').toggleClass('full_data');
            e.stopPropagation();
        });

        jQuery('.blocks_sortable').sortable({
            items: ".wpc_pm_project_items:not(.add_new_project)",
            start: function(e, ui) {
                if ( jQuery('#order_by').val() != '' ) {
                    alert('<?php _e( 'Project list already sorted', WPC_CLIENT_TEXT_DOMAIN ); ?>');
                    jQuery( ui.sender ).sortable('cancel');
                }
            },
            update: function( event, ui ) {
                jQuery.ajax({
                    type     : 'POST',
                    dataType : 'json',
                    url      : '<?php echo get_admin_url(); ?>admin-ajax.php',
                    data     : 'action=wpc_pm_projects&act=set_order&' + jQuery('.blocks_sortable').sortable( "serialize", { key: "project[]" } )
                });
            }
        });

        jQuery('.wpc_pm_project_items').init_project_content();

        jQuery( document ).on( 'click', '.year, .day, .week, .month, .quarter', function() {
            if( !jQuery(this).hasClass('active') ) {
                params.period = jQuery( this ).attr('class');
            } else {
                params.period = '';
            }
            params.offset = 0;
            filter_ajax_action( jQuery.extend( true, {}, params, { 'start' : 1} ) );

            var previous_title = "";
            var current_title = "";
            var next_title = "";

            if( jQuery(this).attr('class') == 'day' ) {
                previous_title = '<?php _e( 'Previous Day', WPC_CLIENT_TEXT_DOMAIN ) ?>';
                current_title = '<?php _e( 'Today', WPC_CLIENT_TEXT_DOMAIN ) ?>';
                next_title = '<?php _e( 'Next Day', WPC_CLIENT_TEXT_DOMAIN ) ?>';
            } else if( jQuery(this).attr('class') == 'week' ) {
                previous_title = '<?php _e( 'Previous Week', WPC_CLIENT_TEXT_DOMAIN ) ?>';
                current_title = '<?php _e( 'Current Week', WPC_CLIENT_TEXT_DOMAIN ) ?>';
                next_title = '<?php _e( 'Next Week', WPC_CLIENT_TEXT_DOMAIN ) ?>';
            } else if( jQuery(this).attr('class') == 'month' ) {
                previous_title = '<?php _e( 'Previous Month', WPC_CLIENT_TEXT_DOMAIN ) ?>';
                current_title = '<?php _e( 'Current Month', WPC_CLIENT_TEXT_DOMAIN ) ?>';
                next_title = '<?php _e( 'Next Month', WPC_CLIENT_TEXT_DOMAIN ) ?>';
            } else if( jQuery(this).attr('class') == 'quarter' ) {
                previous_title = '<?php _e( 'Previous Quarter', WPC_CLIENT_TEXT_DOMAIN ) ?>';
                current_title = '<?php _e( 'Current Quarter', WPC_CLIENT_TEXT_DOMAIN ) ?>';
                next_title = '<?php _e( 'Next Quarter', WPC_CLIENT_TEXT_DOMAIN ) ?>';
            } else if( jQuery(this).attr('class') == 'year' ) {
                previous_title = '<?php _e( 'Previous Year', WPC_CLIENT_TEXT_DOMAIN ) ?>';
                current_title = '<?php _e( 'Current Year', WPC_CLIENT_TEXT_DOMAIN ) ?>';
                next_title = '<?php _e( 'Next Year', WPC_CLIENT_TEXT_DOMAIN ) ?>';
            }


            jQuery('.icons').find('a').not(this).removeClass('active');
            jQuery(this).toggleClass('active');
            jQuery('.dates_block').hide();
            jQuery('.date_description').show();

            jQuery('.prev').attr( 'title', previous_title );
            jQuery('.today').attr( 'title', current_title );
            jQuery('.next').attr( 'title', next_title );

        });

        jQuery('.icons a.dates').click(function() {
            if( !jQuery(this).hasClass('active') ) {
                $temp_icons = jQuery('.icons a.active');
                jQuery(this).siblings('.dates_block').show();
                jQuery(this).siblings('.date_description').hide();
                jQuery(this).siblings('.today, .prev, .next').hide();

                $temp_icons.removeClass('active');
                jQuery(this).addClass('active');
            }
        });

        jQuery( ".project-from" ).datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat:'yy-mm-dd',
            onClose: function( selectedDate ) {
                if( '' != selectedDate ) {
                    jQuery(this).siblings( ".project-to" ).datepicker( "option", "minDate", selectedDate );
                }
            }
        });


        jQuery( ".project-to" ).datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat:'yy-mm-dd',
            onClose: function( selectedDate ) {
                if( '' != selectedDate ) {
                    jQuery(this).siblings( ".project-from" ).datepicker( "option", "maxDate", selectedDate );
                }
            }
        });

        jQuery( document ).on( 'click', '.show_dates', function() {
            var from = jQuery( this ).siblings('.project-from').val();
            var to = jQuery( this ).siblings('.project-to').val();

            var $obj = jQuery(this);
            params.period = 'custom';
            params.from = from;
            params.to = to;
            action = "select_custom_date_filter";

            filter_ajax_action( jQuery.extend( true, {}, params, { 'start' : 1} ) );
        });


        jQuery(document).on( 'click', '.today', function() {
            params.period = "day";
            params.offset = 0;
            filter_ajax_action( jQuery.extend( true, {}, params, { 'start' : 1} ) );
        });

        jQuery(document).on( 'click', '.prev', function() {
            if( params.period != '' ) {
                params.offset = ( typeof params.offset != 'undefined' ? params.offset : 0 )*1 - 1;
                filter_ajax_action( jQuery.extend( true, {}, params, { 'start' : 1} ) );
            }
        });


        jQuery(document).on( 'click', '.next', function() {
            if( params.period != '' ) {
                params.offset = ( typeof params.offset != 'undefined' ? params.offset : 0 )*1 + 1;
                filter_ajax_action( jQuery.extend( true, {}, params, { 'start' : 1} ) );
            }
        });

        jQuery('#filter_object').hide();
        jQuery('#filter_by').change(function() {
            var value = jQuery(this).val();
            jQuery('#filter_object').hide();
            if( value.length > 0 ) {
                jQuery(this).after('<img alt="Loading..." src="<?php echo $wpc_client->plugin_url ?>images/ajax_loading.gif" id="project_filter_ajax_loading" />');
                jQuery.ajax({
                    type: "POST",
                    url: "<?php echo get_admin_url(); ?>admin-ajax.php",
                    data: "action=wpc_pm_get_project_objects&archived_flag=<?php echo $archived_flag; ?>&type=" + value,
                    dataType: "json",
                    success: function(data){
                        jQuery('#project_filter_ajax_loading').remove();
                        jQuery('#filter_object').show();
                        if( typeof data.status != 'undefined' && data.status ) {
                            jQuery('#filter_object').html('<option value=""></option>');
                            data.message.forEach(function( item ) {
                                jQuery('#filter_object').append('<option value="' +item.id + '">' +item.title + '</option>' );
                            });
                        } else {
                            alert( data.message );
                        }
                    }
                });
            } else {
                params.filter_by = '';
                params.filter_id = '';
                filter_ajax_action( jQuery.extend( true, {}, params, { 'start' : 1} )  );
            }
        });

        jQuery('#filter_object').change(function() {
            jQuery(this).children('option[value=""]').remove();
            params.filter_by = jQuery('#filter_by').val();
            params.filter_id = jQuery('#filter_object').val();
            filter_ajax_action( jQuery.extend( true, {}, params, { 'start' : 1} )  );
        });

        jQuery('#order_by').change(function() {
            var value = jQuery(this).val();
            if( value != '' ) {
                var array = value.split('-');
                params.order_by = array[0];
                params.order_value = array[1];
            } else {
                params.order_by = '';
                params.order_value = '';
            }
            filter_ajax_action( jQuery.extend( true, {}, params, { 'start' : 1} ) );
        });

        jQuery('.view_type a').click(function() {
            if( !jQuery(this).hasClass('active') ) {
                jQuery('.view_type a').removeClass('active');
                jQuery(this).addClass('active');
                if( jQuery(this).hasClass('view_type_tile') ) {
                    var view_type = 'tile';
                } else {
                    var view_type = 'list';
                }
                filter_ajax_action( jQuery.extend( true, {}, params, { 'start' : 1, 'view_type' : view_type } ) );
            }
        });

        date_filters_html = jQuery('#graphic-icons').html();
        filter_ajax_action( { 'start' : 1 } );
    });

    function filter_ajax_action( params ) {
        //if( !( typeof params.start != 'undefined' && params.start == '1' ) ) {
            jQuery('.blocks_sortable').infinitescroll('destroy');
            jQuery('.blocks_sortable').data('infinitescroll', '');
            jQuery('.blocks_sortable').html('<div class="wpc_pm_project_items"></div>' +
                '<a id="wpc_next_project_page" href="javascript:void(0);"></a>');
        //}
        jQuery('.blocks_sortable').infinitescroll({
            navSelector      : "#wpc_next_project_page",
            finishedMsg      : '',
            state            : {
                currPage: 0
            },
            nextSelector     : "a#wpc_next_project_page",
            itemSelector     : "div.wpc_pm_project_items",
            debug            : false,
            prefill          : true,
            dataType         : 'json',
            appendCallback   : true,
            template         : function( data ) {
                return data.html;
            },
            binder           : jQuery('.blocks_sortable'),
            path: function(currPage) {
                projects_count = jQuery('.wpc_pm_project_items:not(.add_new_project)').length;
                var params_string = '';
                for( var key in params ) {
                    if( params[ key ] != '' ) {
                        params_string += '&' + key + '=' + params[ key ];
                    }
                }
                return "admin-ajax.php?action=wpc_pm_pagination&type=projects&archived_flag=<?php echo $archived_flag; ?>&count=" +
                    ( projects_count + 1 ) + '&current_page=' + currPage + params_string + '&filter_by=' + ( typeof params.filter_by != 'undefined' ? params.filter_by : '' ) +
                    '&filter_id=' + ( typeof params.filter_id != 'undefined' ? params.filter_id : '' );
            }
        }, function(newElements, data, url){
            jQuery('.wpc_pm_project_items.ajax_loaded').init_project_content();
            jQuery('.wpc_pm_project_items.ajax_loaded').removeClass('ajax_loaded');
        });

    }


    jQuery.fn.wpc_pm_show_load = function() {
        jQuery(this).addClass('wpc_pm_show_load_bg');
        jQuery(this).html("<img src='<?php echo $wpc_client->plugin_url; ?>images/ajax_big_loading.gif' width='64' height='64' />");
        return true;
    };

    jQuery.fn.wpc_pm_hide_load = function() {
        jQuery(this).html('');
        jQuery(this).removeClass('wpc_pm_show_load_bg');
        return true;
    };

    jQuery.fn.init_project_content = function() {
        jQuery(this).find('.object_img').each(function() {
            jQuery(this).css({'height': ( jQuery(this).width() ) + 'px'});
            jQuery( this ).qtip({
                content: {
                    text: function(event, api) {
                        if( api.elements.target.hasClass('user') ) {
                            return api.elements.target.data('name');
                        } else {
                            jQuery.ajax({
                                type: "POST",
                                url: "<?php get_admin_url(); ?>admin-ajax.php",
                                data: "action=wpc_pm_get_team_users&team_id=" + api.elements.target.attr('alt')
                            })
                            .then( function( content ) {
                                // Set the tooltip content upon successful retrieval
                                api.set('content.text', content);
                            }, function(xhr, status, error) {
                                // Upon failure... set the tooltip content to error
                                api.set('content.text', status + ': ' + error);
                            });
                            return 'Loading...'; // Set some initial text
                        }
                    }
                },
                position: {
                    viewport: jQuery(window)
                },
                style: 'qtip-wiki'
            });
        });

        jQuery(this).find('.progress_bar').progressbar({
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
    };
</script>
<div class="wrap">
    <?php echo $wpc_client->get_plugin_logo_block(); ?>

    <?php
 if ( '' == $wpc_client->cc_get_slug( 'project_details_page_id' ) ) { $wpc_client->get_install_page_notice(); } ?>

    <div class="wpc_clear"></div>
    <div class="icon32" id="icon-themes"><br /></div>

    <?php $this->get_breadcrumbs() ?>

    <div class="wpc_clear"></div>
    <?php if( isset( $_GET['message'] ) ) { ?>
        <div id="message" class="updated">
            <p>
            <?php
 switch( $_GET['message'] ) { case '1': _e( 'Project added successfully.', WPC_CLIENT_TEXT_DOMAIN ); break; case '2': _e( 'Project updated successfully.', WPC_CLIENT_TEXT_DOMAIN ); break; case '3': _e( 'Project deleted successfully.', WPC_CLIENT_TEXT_DOMAIN ); break; case '4': _e( 'Wrong project ID.', WPC_CLIENT_TEXT_DOMAIN ); break; case '5': _e( 'Wrong file ID.', WPC_CLIENT_TEXT_DOMAIN ); break; case '6': _e( 'Project archived successfully.', WPC_CLIENT_TEXT_DOMAIN ); break; case '7': _e( 'Project restored successfully.', WPC_CLIENT_TEXT_DOMAIN ); break; } ?>
            </p>
        </div>
        <div class="wpc_clear"></div>
    <?php } ?>


    <div id="wpc_container">

        <?php echo $this->gen_tabs_menu() ?>

        <span class="wpc_clear"></span>

        <div class="wpc_tab_container_block wpc_pm_main_block" style="position: relative;">
            <div class="filters-block">
                <select name="filter_by" id="filter_by">
                    <option value=""><?php _e( 'All', WPC_CLIENT_TEXT_DOMAIN ); ?></option>
                    <?php if( current_user_can('wpc_pm_level_3') ) { ?>
                        <optgroup label="<?php _e( 'User', WPC_CLIENT_TEXT_DOMAIN ); ?>">
                            <option value="client"><?php echo $wpc_client->custom_titles['client']['s']; ?></option>
                            <option value="manager"><?php printf( __( '%s or Admin', WPC_CLIENT_TEXT_DOMAIN ), $wpc_client->custom_titles['project_manager']['s'] ); ?></option>
                            <option value="teammate"><?php echo $wpc_client->custom_titles['teammate']['s']; ?></option>
                            <option value="freelancer"><?php echo $wpc_client->custom_titles['freelancer']['s']; ?></option>
                        </optgroup>
                    <?php } ?>
                    <?php if( current_user_can('wpc_pm_level_3') ) { ?>
                        <option value="team"><?php echo $wpc_client->custom_titles['team']['s']; ?></option>
                    <?php } ?>
                    <option value="percentage"><?php _e( 'Percentage of Completion', WPC_CLIENT_TEXT_DOMAIN ); ?></option>
                    <?php if( current_user_can('wpc_pm_level_3') ) { ?>
                        <option value="business_sector"><?php echo $wpc_client->custom_titles['business_sector']['s']; ?></option>
                    <?php } ?>
                </select>
                <select name="filter_object" id="filter_object">
                    <option value=""></option>
                </select>

                <div class="icons" id="graphic-icons">
                    <a href="javascript:void(0);" class="day" title="<?php _e( 'Day', WPC_CLIENT_TEXT_DOMAIN ) ?>"></a>
                    <a href="javascript:void(0);" class="week" title="<?php _e( 'Week', WPC_CLIENT_TEXT_DOMAIN ) ?>"></a>
                    <a href="javascript:void(0);" class="month" title="<?php _e( 'Month', WPC_CLIENT_TEXT_DOMAIN ) ?>"></a>
                    <a href="javascript:void(0);" class="quarter" title="<?php _e( 'Quarter', WPC_CLIENT_TEXT_DOMAIN ) ?>"></a>
                    <a href="javascript:void(0);" class="year" title="<?php _e( 'Year', WPC_CLIENT_TEXT_DOMAIN ) ?>"></a>
                    <a href="javascript:void(0);" class="dates" title="<?php _e( 'Custom Date', WPC_CLIENT_TEXT_DOMAIN ) ?>"></a>
                    <div class="dates_block">
                        <label for="from"><?php _e( 'From', WPC_CLIENT_TEXT_DOMAIN ) ?></label>
                        <input type="text" style="width:100px;" class="project-from" name="from" value="" />
                        <label for="to"><?php _e( 'to', WPC_CLIENT_TEXT_DOMAIN ) ?></label>
                        <input type="text" style="width:100px;" class="project-to" name="to" value="" />
                        <input type="button" value="<?php _e( 'Show', WPC_CLIENT_TEXT_DOMAIN ) ?>" class="button show_dates">
                    </div>
                    <input type="hidden" class="buttons-offset" value="0" />
                    <a href="javascript:void(0);" class="today" title="<?php _e( 'Today', WPC_CLIENT_TEXT_DOMAIN ) ?>"></a>
                    <a href="javascript:void(0);" class="prev" title="<?php _e( 'Previous Day', WPC_CLIENT_TEXT_DOMAIN ) ?>"></a>
                    <a href="javascript:void(0);" class="next" title="<?php _e( 'Next Day', WPC_CLIENT_TEXT_DOMAIN ) ?>"></a>
                    <span class="date_description"><?php _e('All time', WPC_CLIENT_TEXT_DOMAIN); ?></span>
                </div>

                <select name="order_by" id="order_by">
                    <option value=""><?php _e( 'Default', WPC_CLIENT_TEXT_DOMAIN ); ?></option>
                    <option value="creation_date-asc"><?php _e( 'Creation Date in ascending', WPC_CLIENT_TEXT_DOMAIN ); ?></option>
                    <option value="creation_date-desc"><?php _e( 'Creation Date in descending', WPC_CLIENT_TEXT_DOMAIN ); ?></option>
                    <option value="title-asc"><?php _e( 'Title in ascending', WPC_CLIENT_TEXT_DOMAIN ); ?></option>
                    <option value="title-desc"><?php _e( 'Title in descending', WPC_CLIENT_TEXT_DOMAIN ); ?></option>
                    <option value="due_date-asc"><?php _e( 'Projected Completion Date in ascending', WPC_CLIENT_TEXT_DOMAIN ); ?></option>
                    <option value="due_date-desc"><?php _e( 'Projected Completion Date in descending', WPC_CLIENT_TEXT_DOMAIN ); ?></option>
                    <option value="percent-asc"><?php _e( 'Percent of Completion in ascending', WPC_CLIENT_TEXT_DOMAIN ); ?></option>
                    <option value="percent-desc"><?php _e( 'Percent of Completion in descending', WPC_CLIENT_TEXT_DOMAIN ); ?></option>
                </select>
                <?php
 $view_type = get_user_meta( get_current_user_id(), 'wpc_pm_project_view_type_admin', true ); if( empty( $view_type ) ) { $view_type = 'tile'; } ?>
                <div class="view_type">
                    <a href="javascript: void(0);" class="view_type_list <?php echo $view_type == 'list' ? 'active' : ''; ?>"></a>
                    <a href="javascript: void(0);" class="view_type_tile <?php echo $view_type == 'tile' ? 'active' : ''; ?>"></a>
                </div>
            </div>
            <div class="col-wrap blocks_sortable">
                <div class="wpc_pm_project_items"></div>
                <a id="wpc_next_project_page" href="javascript:void(0);"></a>
            </div>

            <div class="wpc_pm_activities_block">
                <div class="activity_show_border"><div class="activity_show_button"><?php _e( 'Activities', WPC_CLIENT_TEXT_DOMAIN ) ?></div></div>
                <h2 class="title">
                    <?php _e( 'Activities', WPC_CLIENT_TEXT_DOMAIN ) ?>
                    <span style="color: #888">&rarr;</span>
                    <div class="dropdown-block" data-value="">
                        <span class="selected">All</span>
                        <ul class="dropdown-menu">
                            <li><a href="#" data-value="project"><?php _e( 'Projects', WPC_CLIENT_TEXT_DOMAIN ); ?></a></li>
                            <li><a href="#" data-value="milestone"><?php _e( 'Milestones', WPC_CLIENT_TEXT_DOMAIN ); ?></a></li>
                            <li><a href="#" data-value="task"><?php _e( 'Tasks', WPC_CLIENT_TEXT_DOMAIN ); ?></a></li>
                            <li><a href="#" data-value="user"><?php _e( 'Users', WPC_CLIENT_TEXT_DOMAIN ); ?></a></li>
                            <li><a href="#" data-value="team"><?php _e( 'Teams', WPC_CLIENT_TEXT_DOMAIN ); ?></a></li>
                            <li><a href="#" data-value="file"><?php _e( 'Files', WPC_CLIENT_TEXT_DOMAIN ); ?></a></li>
                        </ul>
                    </div>
                </h2>
                <?php
 $this->show_activities(); ?>
                <a id="wpc_next_page" href="javascript:void(0);"></a>
            </div>
        </div>
    </div>
</div>