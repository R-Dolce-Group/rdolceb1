<?php
 if ( ! defined( 'ABSPATH' ) ) { exit; } global $wpc_client; $show_progress = 1; if( isset( $attrs['show_progress'] ) && 'yes' != strtolower( $attrs['show_progress'] ) ) { $show_progress = 0; } $wpc_client->add_custom_datepicker_scripts(); wp_enqueue_script( 'jquery-base64', $wpc_client->plugin_url . 'js/jquery.b_64.min.js', array( 'jquery' ) ); wp_enqueue_script( 'jquery-ui-datepicker', false, array('jquery'), false, true ); wp_enqueue_script( 'jquery-ui-progressbar' ); wp_register_style( 'wpc-pm-ui-styles', $wpc_client->plugin_url . 'css/jqueryui/jquery-ui-1.10.3.css' ); wp_enqueue_style( 'wpc-pm-ui-styles' ); wp_register_style( 'wpc-pm-projects-styles', $this->extension_url . 'css/wpc_pm_project_list.css' ); wp_enqueue_style( 'wpc-pm-projects-styles' ); ?>

<script type="text/javascript">
    var wpc_activity_type = '';
    var date_filters_html = '';
    var params = new Array();
    var action = '';
    var filter_by = '';
    var filter_id = '';
    var projects_count = 0;
    var filter = {};
    jQuery(document).ready(function($) {
        jQuery( '.projects_list_table thead th.wpc_sortable' ).on( 'click', function() {
            var obj = jQuery(this);
            var clsName = obj.find( '.wpc_cust_project_sort' ).attr('class').match(/\w*wpc_cust_sort_\w*/)[0].replace('wpc_cust_sort_','');

            params.order_by = '';
            params.order_value = '';
            if( obj.hasClass( 'wpc_sort_asc' ) ) {
                params.order_by = clsName;
                params.order_value = 'desc';
            } else if( obj.hasClass( 'wpc_sort_desc' ) ) {
                params.order_by = clsName;
                params.order_value = 'asc';
            } else if( !obj.hasClass( 'wpc_sort_desc' ) && !obj.hasClass( 'wpc_sort_asc' ) ) {
                params.order_by = clsName;
                params.order_value = 'asc';
            }

            filter_ajax_action( params );

            if( obj.hasClass( 'wpc_sort_asc' ) ) {
                obj.parents( '.projects_list_table' ).find('th.wpc_sortable').removeClass('wpc_sort_asc').removeClass('wpc_sort_desc');
                obj.parents( '.projects_list_table' ).find( '.wpc_cust_sort_' + clsName ).parents('th.wpc_sortable').addClass( 'wpc_sort_desc' );
            } else if( obj.hasClass( 'wpc_sort_desc' ) ) {
                obj.parents( '.projects_list_table' ).find('th.wpc_sortable').removeClass('wpc_sort_asc').removeClass('wpc_sort_desc');
                obj.parents( '.projects_list_table' ).find( '.wpc_cust_sort_' + clsName ).parents('th.wpc_sortable').addClass( 'wpc_sort_asc' );
            } else if( !obj.hasClass( 'wpc_sort_desc' ) && !obj.hasClass( 'wpc_sort_asc' ) ) {
                obj.parents( '.projects_list_table' ).find('th.wpc_sortable').removeClass('wpc_sort_asc').removeClass('wpc_sort_desc');
                obj.addClass( 'wpc_sort_asc' );
            }
        });


        /*filters js*/
        jQuery( '.wpc_pm_project_list .wpc_show_filters' ).click( function(e) {
            jQuery( '.wpc_show_filters' ).not( this ).parents( '.wpc_filters_select_wrapper' ).removeClass( 'filtered' );
            jQuery(this).parents( '.wpc_filters_select_wrapper' ).toggleClass( 'filtered' );

            e.stopPropagation();

            jQuery( 'body' ).bind( 'click', function( event ) {
                jQuery( '.wpc_show_filters' ).parents( '.wpc_filters_select_wrapper' ).removeClass( 'filtered' );
                jQuery( 'body' ).unbind( event );
            });
        });

        jQuery( '.wpc_filters_contect' ).click( function(e) {
            e.stopPropagation();
        });




        jQuery( '.wpc_pm_project_list .wpc_filter_by' ).change( function() {
            var obj = jQuery(this);

            if( obj.val() == 'none' ) {
                obj.parents( '.wpc_pm_project_list' ).find( '.wpc_files_filter' ).hide();
                return false;
            }

            var filters = '';
            if( typeof filter !== 'undefined' && typeof filter !== 'undefined' && Object.keys(filter).length > 0 ) {
                filters = jQuery.base64Encode( JSON.stringify( filter ) );
            }

            obj.parents('.wpc_filters_contect').find( '.wpc_ajax_content' ).addClass( 'wpc_is_loading' );
            jQuery.ajax({
                type: 'POST',
                url: "<?php echo get_admin_url(); ?>admin-ajax.php",
                data: 'action=wpc_pm_projects_shortcode_get_filter&filter_by=' + obj.val() + '&filters=' + filters,
                dataType: "json",
                success: function( data ){
                    if( !data.status ) {
                        alert( data.message );
                    } else {
                        obj.parents( '.wpc_pm_project_list' ).find( '.wpc_msg_filter_selectors' ).html( data.filter_html ).show();

                        if( obj.val() == 'date' ) {
                            if( typeof( custom_datepicker_init ) !== 'undefined' ) {
                                custom_datepicker_init();
                            }

                            obj.parents('.wpc_filters_contect').find( '.from_date_field' ).datepicker( "option", {
                                minDate: new Date( data.mindate*1000 ),
                                maxDate: new Date( data.maxdate*1000 ),
                                onClose: function( selectedDate ) {
                                    obj.parents('.wpc_filters_contect').find('.to_date_field').datepicker( "option", "minDate", selectedDate );
                                }
                            });

                            obj.parents('.wpc_filters_contect').find('.to_date_field').datepicker( "option", {
                                minDate: new Date( data.mindate*1000 ),
                                maxDate: new Date( data.maxdate*1000 ),
                                onClose: function( selectedDate ) {
                                    obj.parents('.wpc_filters_contect').find('.from_date_field').datepicker( "option", "maxDate", selectedDate );
                                }
                            });
                        }

                        obj.parents('.wpc_filters_contect').find( '.wpc_ajax_content' ).removeClass( 'wpc_is_loading' );
                    }
                }
            });
        });

        if( jQuery( '.wpc_pm_project_list .wpc_filter_by' ).length > 0 ) {
            jQuery( '.wpc_pm_project_list .wpc_filter_by' ).trigger( 'change' );
        }



        jQuery( '.wpc_pm_project_list .wpc_filters_wrapper' ).change( function() {
            var filters = '';
            if( typeof filter !== 'undefined' && typeof filter !== 'undefined' && Object.keys(filter).length > 0 ) {
                params.filters = jQuery.base64Encode( JSON.stringify( filter ) );
            } else {
                params.filters = '';
            }

            filter_ajax_action(params);
        });


        //add filter
        jQuery( '.wpc_pm_project_list .wpc_add_filter' ).click( function() {

            var obj = jQuery(this);

            var filter_before = filter;

            var filter_by = obj.parents( '.wpc_projects_filter_block' ).find('.wpc_filter_by').val();
            var filter_id = obj.parents( '.wpc_projects_filter_block' ).find('.wpc_filter').val();

            if( filter_by == 'date' ) {
                if (obj.parents( '.wpc_projects_filter_block' ).find('.from_date_field').next().val() == '' && obj.parents( '.wpc_projects_filter_block' ).find('.to_date_field').next().val() == '') {
                    return false;
                }

                if (obj.parents( '.wpc_projects_filter_block' ).find('.to_date_field').next().val() == '') {
                    var current_time = new Date().getTime();
                    obj.parents( '.wpc_projects_filter_block' ).find('.to_date_field').next().val(Math.floor(current_time / 1000));
                }

                if( typeof filter_before === 'undefined' ) {
                    filter = {};
                    filter[filter_by] = [];
                }

                if( !filter.hasOwnProperty( filter_by ) ) {
                    filter[filter_by] = [];
                }

                filter[filter_by] = {
                    'from': obj.parents( '.wpc_projects_filter_block' ).find('.from_date_field').next().val(),
                    'to': obj.parents( '.wpc_projects_filter_block' ).find('.to_date_field').next().val()
                };


                jQuery.ajax({
                    type: 'POST',
                    url: "<?php echo get_admin_url(); ?>admin-ajax.php",
                    data: 'action=wpc_pm_get_filter_data&filter_by=' + filter_by + '&from=' + filter[filter_by]['from'] + '&to=' + filter[filter_by]['to'],
                    dataType: "json",
                    success: function( data ){
                        if( !data.status ) {
                            alert( data.message );
                        } else {
                            obj.parents( '.wpc_pm_project_list' ).find( '.wpc_filters_wrapper' ).append(
                                '<div class="wpc_filter_wrapper" data-filter_by="' + filter_by + '">' +
                                data.title + ': ' + data.from + ' - ' + data.to +
                                '<div class="wpc_remove_filter">&times;</div>' +
                                '</div>'
                            ).trigger( 'change' );
                        }
                    }
                });
            } else {
                var in_array = false;

                if (typeof filter_before !== 'undefined' && filter_before.hasOwnProperty(filter_by)) {
                    jQuery.map(filter_before[filter_by], function (elementOfArray, indexInArray) {
                        if (elementOfArray == filter_id) {
                            in_array = true;
                        }
                    });
                }

                if (in_array) {
                    return false;
                }

                if (typeof filter_before === 'undefined') {
                    filter = {};
                    //filter[filter_by] = [];
                }

                /*if (!filter.hasOwnProperty(filter_by)) {
                    filter[filter_by] = [];
                }*/

                filter[filter_by] = filter_id;

                add_filter(obj.parents('.wpc_pm_project_list'), filter_by, filter_id);
            }
            obj.parents( '.wpc_projects_filter_block' ).find('.wpc_filters_select_wrapper').removeClass( 'filtered' );

        });


        //remove filters
        jQuery( '.wpc_pm_project_list' ).on( 'click', '.wpc_remove_filter', function() {
            var obj = jQuery(this);

            var filter_before = filter;

            var filter_by = obj.parents( '.wpc_filter_wrapper' ).data('filter_by');
            var filter_id = obj.parents( '.wpc_filter_wrapper' ).data('filter_id');

            if( filter_by == 'date' ) {
                delete filter.date;

                obj.parents( '.wpc_filter_wrapper' ).find('.wpc_filter_by option[value="date"]').attr('disabled', false);
                obj.parents( '.wpc_filter_wrapper' ).find('.wpc_filter_by').trigger( 'change' );
            } else {
                //var filter_id = obj.parents( '.wpc_filter_wrapper' ).data('filter_id');
                filter[filter_by] = null;
/*                var index = filter_before[filter_by].indexOf( filter_id.toString() );
                if( index > -1 ) {
                    filter[filter_by].splice( index, 1 );
                }*/
            }

            obj.parents( '.wpc_filters_wrapper' ).trigger( 'change' );
            obj.parents( '.wpc_filter_wrapper' ).remove();
        });


        //pagination
        jQuery( '.wpc_pm_project_list' ).on( 'click', 'a.wpc_pagination_links', function() {
            var obj = jQuery(this);
            var page_number = '';
            var count_pages = <?php echo $count_pages ?>;

            if( obj.hasClass( 'wpc_active_page' ) ) {
                return false;
            }

            var filters = '';
            if( typeof filter !== 'undefined' && typeof filter !== 'undefined' && Object.keys(filter).length > 0 ) {
                params.filters = jQuery.base64Encode( JSON.stringify( filter ) );
            }

            var order_by = '';
            var order = '';
            obj.parents( '.wpc_pm_project_list' ).find( '.wpc_client_files th.wpc_sortable' ).each( function(){
                if( jQuery(this).hasClass( 'wpc_sort_desc' ) ) {

                    var clsName = jQuery(this).find( '.wpc_cust_file_sort' ).attr('class').match(/\w*wpc_cust_sort_\w*/)[0].replace('wpc_cust_sort_','');
                    order_by = clsName;
                    order = 'desc';

                } else if( jQuery(this).hasClass( 'wpc_sort_asc' ) ) {

                    var clsName = jQuery(this).find( '.wpc_cust_file_sort' ).attr('class').match(/\w*wpc_cust_sort_\w*/)[0].replace('wpc_cust_sort_','');
                    order_by = clsName;
                    order = 'asc';

                }
            });


            if( obj.hasClass( 'wpc_last' ) ) {
                page_number = count_pages;
            } else if( obj.hasClass( 'wpc_first' ) ) {
                page_number = 1;
            } else if( obj.hasClass( 'wpc_next' ) ) {
                page_number = obj.siblings('.wpc_active_page').html()*1 + 1;
            } else if( obj.hasClass( 'wpc_previous' ) ) {
                page_number = obj.siblings('.wpc_active_page').html()*1 - 1;
            } else if( obj.hasClass( 'wpc_next_pages' ) )  {
                page_number = ( Math.floor( ( obj.siblings('.wpc_active_page').html()*1 - 1 ) / 3 ) + 1 )*3 + 1;
            } else if( obj.hasClass( 'wpc_previous_pages' ) ) {
                page_number = ( Math.floor( ( obj.siblings('.wpc_active_page').html()*1 - 1 ) / 3 ) )*3;
            } else {
                page_number = obj.html()*1;
            }

            params_string = '';
            for( var key in params ) {
                if( params[ key ] != '' ) {
                    params_string += '&' + key + '=' + params[ key ];
                }
            }

            projects_count = '<?php echo $this->projects_per_page ?>';

            jQuery('.blocks_sortable').wpc_pm_show_load();
            jQuery.ajax({
                type: 'POST',
                url: '<?php echo get_admin_url(); ?>admin-ajax.php',
                data: 'action=wpc_pm_pagination&type=projects&user_area=1&archived_flag=<?php echo $archive_flag == 1 ? 2 : 0; ?>&current_page=' + page_number +
                    params_string + '&view_type=table&show_progress=<?php echo $show_progress; ?>',
                dataType: "json",
                success: function( data ){
                    if( !data.status ) {
                        alert( data.message );
                    } else {
                        jQuery('.blocks_sortable').wpc_pm_hide_load();
                        obj.parents( '.wpc_pm_project_list' ).find( 'tbody' ).html( data.html );
                        jQuery('.wpc_pm_project_items.ajax_loaded').removeClass('ajax_loaded');

                        jQuery('.wpc_pm_project_items').init_project_content();

                        if( obj.hasClass( 'wpc_last' ) ) {

                            obj.parents( '.wpc_pm_project_list' ).find('.wpc_last').hide();
                            obj.parents( '.wpc_pm_project_list' ).find( 'a.wpc_pagination_links.wpc_next_pages' ).hide();
                            obj.parents( '.wpc_pm_project_list' ).find( 'a.wpc_pagination_links.wpc_next' ).hide();

                            obj.parents( '.wpc_pm_project_list' ).find( 'a.wpc_pagination_links' ).removeClass( 'wpc_active_page' );
                            obj.parents( '.wpc_pm_project_list' ).find( 'a.wpc_pagination_links.wpc_next_pages' ).prev().addClass( 'wpc_active_page' ).show();

                            if( page_number*1 > 3 ) {
                                obj.parents( '.wpc_pm_project_list' ).find( 'a.wpc_pagination_links.wpc_previous_pages' ).show();
                            }
                            obj.parents( '.wpc_pm_project_list' ).find( 'a.wpc_pagination_links.wpc_previous' ).show();
                            obj.parents( '.wpc_pm_project_list' ).find( 'a.wpc_pagination_links.wpc_first' ).show();

                            obj.parents( '.wpc_pm_project_list' ).find( 'a.wpc_pagination_links' ).each( function() {
                                if( jQuery(this).html()*1 > ( Math.floor( ( page_number*1 - 1 ) / 3 ) )*3 && jQuery(this).html()*1 < ( Math.floor( ( page_number*1 - 1 ) / 3 ) + 1 )*3 + 1 ) {
                                    jQuery(this).show();
                                } else if( jQuery.isNumeric( jQuery(this).html() ) ) {
                                    jQuery(this).hide();
                                }
                            });

                        } else if( obj.hasClass( 'wpc_first' ) ) {

                            obj.parents( '.wpc_pm_project_list' ).find('.wpc_first').hide();
                            obj.parents( '.wpc_pm_project_list' ).find( 'a.wpc_pagination_links.wpc_previous_pages' ).hide();
                            obj.parents( '.wpc_pm_project_list' ).find( 'a.wpc_pagination_links.wpc_previous' ).hide();

                            obj.parents( '.wpc_pm_project_list' ).find( 'a.wpc_pagination_links' ).removeClass( 'wpc_active_page' );
                            obj.parents( '.wpc_pm_project_list' ).find( 'a.wpc_pagination_links.wpc_previous_pages' ).next().addClass( 'wpc_active_page' ).show();

                            if( page_number*1 < ( ( Math.ceil( count_pages/3 ) - 1)*3 + 1 ) ) {
                                obj.parents( '.wpc_pm_project_list' ).find( 'a.wpc_pagination_links.wpc_next_pages' ).show();
                            }
                            obj.parents( '.wpc_pm_project_list' ).find( 'a.wpc_pagination_links.wpc_next' ).show();
                            obj.parents( '.wpc_pm_project_list' ).find( 'a.wpc_pagination_links.wpc_last' ).show();

                            obj.parents( '.wpc_pm_project_list' ).find( 'a.wpc_pagination_links' ).each( function() {
                                if( jQuery(this).html()*1 > ( Math.floor( ( page_number*1 - 1 ) / 3 ) )*3 && jQuery(this).html()*1 < ( Math.floor( ( page_number*1 - 1 ) / 3 ) + 1 )*3 + 1 ) {
                                    jQuery(this).show();
                                } else if( jQuery.isNumeric( jQuery(this).html() ) ) {
                                    jQuery(this).hide();
                                }
                            });

                        } else if( obj.hasClass( 'wpc_next' ) ) {

                            var before_active = obj.parents( '.wpc_pm_project_list' ).find( 'a.wpc_pagination_links.wpc_active_page' );
                            obj.parents( '.wpc_pm_project_list' ).find( 'a.wpc_pagination_links' ).removeClass( 'wpc_active_page' );
                            before_active.next().addClass( 'wpc_active_page' ).show();

                            if( page_number*1 > 1 ) {
                                obj.parents( '.wpc_pm_project_list' ).find( 'a.wpc_pagination_links.wpc_previous' ).show();
                                obj.parents( '.wpc_pm_project_list' ).find( 'a.wpc_pagination_links.wpc_first' ).show();
                            }

                            if( page_number*1 > 3 ) {
                                obj.parents( '.wpc_pm_project_list' ).find( 'a.wpc_pagination_links.wpc_previous_pages' ).show();
                            }

                            if( page_number*1 == count_pages*1 ) {
                                obj.parents( '.wpc_pm_project_list' ).find( '.wpc_next' ).hide();
                                obj.parents( '.wpc_pm_project_list' ).find( 'a.wpc_pagination_links.wpc_next_pages' ).hide();
                                obj.parents( '.wpc_pm_project_list' ).find( 'a.wpc_pagination_links.wpc_last' ).hide();
                            }

                            obj.parents( '.wpc_pm_project_list' ).find( 'a.wpc_pagination_links' ).each( function() {
                                if( jQuery(this).html()*1 > ( Math.floor( ( page_number*1 - 1 ) / 3 ) )*3 && jQuery(this).html()*1 < ( Math.floor( ( page_number*1 - 1 ) / 3 ) + 1 )*3 + 1 ) {
                                    jQuery(this).show();
                                } else if( jQuery.isNumeric( jQuery(this).html() ) ) {
                                    jQuery(this).hide();
                                }
                            });

                            if( ( Math.floor( ( page_number*1 - 1 ) / 3 ) + 1 )*3 + 1 > count_pages ) {
                                obj.parents( '.wpc_pm_project_list' ).find( 'a.wpc_pagination_links.wpc_next_pages' ).hide();
                            }

                        } else if( obj.hasClass( 'wpc_previous' ) ) {

                            var before_active = obj.parents( '.wpc_pm_project_list' ).find( 'a.wpc_pagination_links.wpc_active_page' );
                            obj.parents( '.wpc_pm_project_list' ).find( 'a.wpc_pagination_links' ).removeClass( 'wpc_active_page' );
                            before_active.prev().addClass( 'wpc_active_page' ).show();

                            if( page_number*1 < count_pages*1 ) {
                                obj.parents( '.wpc_pm_project_list' ).find( 'a.wpc_pagination_links.wpc_next' ).show();
                                obj.parents( '.wpc_pm_project_list' ).find( 'a.wpc_pagination_links.wpc_last' ).show();
                            }

                            if( page_number*1 < ( ( Math.ceil( count_pages/3 ) - 1)*3 + 1 ) ) {
                                obj.parents( '.wpc_pm_project_list' ).find( 'a.wpc_pagination_links.wpc_next_pages' ).show();
                            }

                            if( page_number*1 == 1 ) {
                                obj.parents( '.wpc_pm_project_list' ).find( '.wpc_previous' ).hide();
                                obj.parents( '.wpc_pm_project_list' ).find( 'a.wpc_pagination_links.wpc_previous_pages' ).hide();
                                obj.parents( '.wpc_pm_project_list' ).find( 'a.wpc_pagination_links.wpc_first' ).hide();
                            }

                            obj.parents( '.wpc_pm_project_list' ).find( 'a.wpc_pagination_links' ).each( function() {
                                if( jQuery(this).html()*1 > ( Math.floor( ( page_number*1 - 1 ) / 3 ) )*3 && jQuery(this).html()*1 < ( Math.floor( ( page_number*1 - 1 ) / 3 ) + 1 )*3 + 1 ) {
                                    jQuery(this).show();
                                } else if( jQuery.isNumeric( jQuery(this).html() ) ) {
                                    jQuery(this).hide();
                                }
                            });

                            if( Math.floor( ( page_number*1 - 1 ) / 3 ) == 0 ) {
                                obj.parents( '.wpc_pm_project_list' ).find( 'a.wpc_pagination_links.wpc_previous_pages' ).hide();
                            }

                        } else if( obj.hasClass( 'wpc_next_pages' ) )  {

                            obj.parents( '.wpc_pm_project_list' ).find( 'a.wpc_pagination_links' ).removeClass( 'wpc_active_page' );

                            obj.parents( '.wpc_pm_project_list' ).find( 'a.wpc_pagination_links.wpc_previous_pages' ).show();
                            obj.parents( '.wpc_pm_project_list' ).find( 'a.wpc_pagination_links.wpc_previous' ).show();
                            obj.parents( '.wpc_pm_project_list' ).find( 'a.wpc_pagination_links.wpc_first' ).show();

                            if( ( Math.floor( ( page_number*1 - 1 ) / 3 ) + 1 )*3 + 1 > count_pages ) {
                                obj.parents( '.wpc_pm_project_list' ).find( '.wpc_next_pages' ).hide();
                            }

                            if( page_number*1 == count_pages ) {
                                obj.parents( '.wpc_pm_project_list' ).find( 'a.wpc_pagination_links.wpc_next' ).hide();
                                obj.parents( '.wpc_pm_project_list' ).find( 'a.wpc_pagination_links.wpc_last' ).hide();
                            }

                            obj.parents( '.wpc_pm_project_list' ).find( 'a.wpc_pagination_links' ).each( function() {
                                if( jQuery(this).html()*1 > ( Math.floor( ( page_number*1 - 1 ) / 3 ) )*3 && jQuery(this).html()*1 < ( Math.floor( ( page_number*1 - 1 ) / 3 ) + 1 )*3 + 1 ) {
                                    jQuery(this).show();
                                } else if( jQuery.isNumeric( jQuery(this).html() ) ) {
                                    jQuery(this).hide();
                                }

                                if( jQuery(this).html()*1 == page_number*1 ) {
                                    jQuery(this).addClass( 'wpc_active_page' );
                                }
                            });

                        } else if( obj.hasClass( 'wpc_previous_pages' ) ) {
                            obj.parents( '.wpc_pm_project_list' ).find( 'a.wpc_pagination_links' ).removeClass( 'wpc_active_page' );

                            obj.parents( '.wpc_pm_project_list' ).find( 'a.wpc_pagination_links.wpc_next_pages' ).show();
                            obj.parents( '.wpc_pm_project_list' ).find( 'a.wpc_pagination_links.wpc_next' ).show();
                            obj.parents( '.wpc_pm_project_list' ).find( 'a.wpc_pagination_links.wpc_last' ).show();

                            if( Math.floor( ( page_number*1 - 1 ) / 3 ) == 0 ) {
                                obj.parents( '.wpc_pm_project_list' ).find('.wpc_previous_pages').hide();
                            }

                            obj.parents( '.wpc_pm_project_list' ).find( 'a.wpc_pagination_links' ).each( function() {
                                if( jQuery(this).html()*1 > ( Math.floor( ( page_number*1 - 1 ) / 3 ) )*3 && jQuery(this).html()*1 < ( Math.floor( ( page_number*1 - 1 ) / 3 ) + 1 )*3 + 1 ) {
                                    jQuery(this).show();
                                } else if( jQuery.isNumeric( jQuery(this).html() ) ) {
                                    jQuery(this).hide();
                                }

                                if( jQuery(this).html()*1 == page_number*1 ) {
                                    jQuery(this).addClass( 'wpc_active_page' );
                                }
                            });

                        } else {

                            obj.parents( '.wpc_pm_project_list' ).find( 'a.wpc_pagination_links' ).removeClass( 'wpc_active_page' );

                            obj.parents( '.wpc_pm_project_list' ).find( 'a.wpc_pagination_links' ).each( function() {
                                if( obj.html() == jQuery(this).html() ) {
                                    jQuery(this).addClass( 'wpc_active_page' );
                                }
                            });

                            if( page_number*1 < count_pages*1 ) {
                                obj.parents( '.wpc_pm_project_list' ).find( 'a.wpc_pagination_links.wpc_next' ).show();
                                obj.parents( '.wpc_pm_project_list' ).find( 'a.wpc_pagination_links.wpc_last' ).show();
                            } else {
                                obj.parents( '.wpc_pm_project_list' ).find( 'a.wpc_pagination_links.wpc_next' ).hide();
                                obj.parents( '.wpc_pm_project_list' ).find( 'a.wpc_pagination_links.wpc_last' ).hide();
                            }

                            if( page_number*1 > 1 ) {
                                obj.parents( '.wpc_pm_project_list' ).find( 'a.wpc_pagination_links.wpc_previous' ).show();
                                obj.parents( '.wpc_pm_project_list' ).find( 'a.wpc_pagination_links.wpc_first' ).show();
                            } else {
                                obj.parents( '.wpc_pm_project_list' ).find( 'a.wpc_pagination_links.wpc_previous' ).hide();
                                obj.parents( '.wpc_pm_project_list' ).find( 'a.wpc_pagination_links.wpc_first' ).hide();
                            }
                        }

                    }
                }
            });

        });


        jQuery(document).on('click', '.projects_list_table tbody tr', function() {
            jQuery(this).toggleClass('active');
        });

        date_filters_html = jQuery('#graphic-icons').html();
        filter_ajax_action( { 'start' : 1 } );
        jQuery('.wpc_pm_project_items').init_project_content();
    });

    function filter_ajax_action( params ) {
        jQuery('.blocks_sortable').wpc_pm_show_load();
        var params_string = '';
        for( var key in params ) {
            if( params[ key ] != '' ) {
                params_string += '&' + key + '=' + params[ key ];
            }
        }
        jQuery.ajax({
            type: "POST",
            url: "<?php echo get_admin_url(); ?>admin-ajax.php",
            data: "action=wpc_pm_pagination&type=projects&user_area=1&archived_flag=<?php echo $archive_flag == 1 ? 2 : 0; ?>&count=0&view_type=table&show_progress=<?php echo $show_progress; ?>" + params_string,
            dataType: "json",
            success: function(data){
                jQuery('.blocks_sortable').wpc_pm_hide_load();
                if( !data.status ) {
                    alert(data.message);
                } else {
                    jQuery('.blocks_sortable').append( data.html );
                    jQuery('.wpc_pm_project_items.ajax_loaded').init_project_content();

                    jQuery('.wpc_pm_project_items.ajax_loaded').removeClass('ajax_loaded');

                    if( data.filter_html != '' ) {
                        jQuery( '.wpc_pm_project_list' ).find('.wpc_show_filters').prop('disabled', false);
                        jQuery( '.wpc_pm_project_list' ).find('.wpc_filter_by').html( data.filter_html ).trigger( 'change' );
                    } else {
                        jQuery( '.wpc_pm_project_list' ).find('.wpc_show_filters').prop('disabled', true);
                    }

                    var pagination_html = '<span class="wpc_projects_counter">' + data.pagination.projects_count + '</span>';
                    if( data.pagination.count_pages > 1 ) {
                        pagination_html +='<a href="javascript:void(0);" class="wpc_pagination_links wpc_first" style="display:none;"> << </a>' +
                            '<a href="javascript:void(0);" class="wpc_pagination_links wpc_previous" style="display:none;"> < </a>' +
                            '<a href="javascript:void(0);" class="wpc_pagination_links wpc_previous_pages" style="display:none;"> ... </a>' +
                            '<a href="javascript:void(0);" class="wpc_pagination_links wpc_active_page"> 1 </a>';
                        if( data.pagination.count_pages <= 3 ) {
                            for( var i = 2; i <= data.pagination.count_pages; i++ ) {
                                pagination_html += '<a href="javascript:void(0);" class="wpc_pagination_links"> ' + i + ' </a>';
                            }
                        } else if ( data.pagination.count_pages > 3 ) {
                            for( var i = 2; i <= 3; i++ ) {
                                pagination_html += '<a href="javascript:void(0);" class="wpc_pagination_links"> ' + i + ' </a>';
                            }
                            for( var i = 4; i <= data.pagination.count_pages; i++ ) {
                                pagination_html += '<a href="javascript:void(0);" class="wpc_pagination_links" style="display:none;"> ' + i + ' </a>';
                            }
                            pagination_html += '<a href="javascript:void(0);" class="wpc_pagination_links wpc_next_pages"> ... </a>';
                        }
                        pagination_html += '<a href="javascript:void(0);" class="wpc_pagination_links wpc_next"> > </a>' +
                            '<a href="javascript:void(0);" class="wpc_pagination_links wpc_last"> >> </a>';
                    }

                    jQuery( '.wpc_projects_pagination' ).html( pagination_html );

                    if( data.current_date != '' ) {
                        jQuery('.date_description').html( data.current_date );
                    } else {
                        jQuerya('.date_description').html( '' );
                    }

                }
            }
        });
    }

    function add_filter( table, filter_by, filter_id ) {
        jQuery.ajax({
            type: 'POST',
            url: "<?php echo get_admin_url(); ?>admin-ajax.php",
            data: 'action=wpc_pm_get_filter_data&filter_by=' + filter_by + '&filter_id=' + filter_id,
            dataType: "json",
            success: function( data ){
                if( !data.status ) {
                    alert( data.message );
                } else {
                    table.find( '.wpc_filters_wrapper' ).append(
                        '<div class="wpc_filter_wrapper" data-filter_by="' + filter_by + '" data-filter_id="' + filter_id + '">' +
                        data.title + ': ' + data.name +
                        '<div class="wpc_remove_filter">&times;</div>' +
                        '</div>'
                    ).trigger( 'change' );
                }
            }
        });
    }

    jQuery.fn.wpc_pm_show_load = function() {
        jQuery(this).addClass('wpc_pm_show_load_bg');
        jQuery(this).html('<tr><td colspan="4"><img' + ' src="<?php echo $wpc_client->plugin_url; ?>images/ajax_big_loading.gif"' + ' width="64"' + ' height="64" /></td></tr>');
        return true;
    };

    jQuery.fn.wpc_pm_hide_load = function() {
        jQuery(this).html('');
        jQuery(this).removeClass('wpc_pm_show_load_bg');
        return true;
    };

    jQuery.fn.init_project_content = function() {
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
<style>
    @media only screen and ( min-width: 320px ) and ( max-width: 768px ),
    only screen and ( min-device-width: 320px ) and ( max-device-width: 768px ) {
        .projects_list_table tr.active td.desc_field:before {
            content: "<?php _e( 'Description', WPC_CLIENT_TEXT_DOMAIN ); ?>:";
        }
        .projects_list_table tr.active td.completion_date_field:before {
            content: "<?php _e( 'Completion Date', WPC_CLIENT_TEXT_DOMAIN ); ?>:";
        }
        .projects_list_table tr.active td.progress_field:before {
            content: "<?php _e( 'Progress', WPC_CLIENT_TEXT_DOMAIN ); ?>:";
        }
    }
</style>
<div class="wpc_pm_project_list">
    <div class="wpc_projects_filter_block" style="float:left;">
        <div class="wpc_filters_select_wrapper">
            <input type="button" class="wpc_show_filters wpc_button" value="<?php _e( 'Add Filter', WPC_CLIENT_TEXT_DOMAIN ) ?>"/>

            <div class="wpc_filters_contect">

                <label><?php _e( 'Filter By', WPC_CLIENT_TEXT_DOMAIN ) ?>&nbsp;
                    <select class="wpc_filter_by wpc_selectbox">
                        <option value="percentage"><?php _e( 'Percentage of Completion', WPC_CLIENT_TEXT_DOMAIN ) ?></option>
                        <option value="date"><?php _e( 'Completion Date', WPC_CLIENT_TEXT_DOMAIN ) ?></option>
                    </select>
                </label>

                <div class="wpc_ajax_content">
                    <div class="wpc_loading_overflow">
                        <div class="wpc_small_ajax_loading"></div>
                    </div>
                    <div class="wpc_overflow_content">
                        <div class="wpc_msg_filter_selectors"></div>
                        <input type="button" value="<?php _e( 'Apply Filter', WPC_CLIENT_TEXT_DOMAIN ) ?>" class="wpc_add_filter wpc_button" />
                    </div>
                </div>
            </div>
        </div>

        <div class="wpc_filters_wrapper"></div>
    </div>

    <div class="wpc_projects_pagination">
        <span class="wpc_projects_counter">
            <?php echo ( isset( $projects ) && $projects > 0 ) ? $projects . ' ' . __( 'items', WPC_CLIENT_TEXT_DOMAIN ) : __( 'No items', WPC_CLIENT_TEXT_DOMAIN ); ?>
        </span>
        <?php if ( $count_pages > 1 ) { ?>
            <a href="javascript:void(0);" class="wpc_pagination_links wpc_first" style="display:none;"><<</a>
            <a href="javascript:void(0);" class="wpc_pagination_links wpc_previous" style="display:none;"><</a>
            <a href="javascript:void(0);" class="wpc_pagination_links wpc_previous_pages" style="display:none;">...</a>
            <a href="javascript:void(0);" class="wpc_pagination_links wpc_active_page">1</a>
            <?php if ( $count_pages <= 3 ) { for( $i=2; $i <= $count_pages; $i++ ) { ?>
                    <a href="javascript:void(0);" class="wpc_pagination_links"><?php echo $i ?></a>
                <?php } } elseif ( $count_pages > 3 ) { for( $i=2; $i <= 3; $i++ ) { ?>
                    <a href="javascript:void(0);" class="wpc_pagination_links"><?php echo $i ?></a>
                <?php } for( $i=4; $i <= $count_pages; $i++ ) { ?>
                    <a href="javascript:void(0);" class="wpc_pagination_links" style="display:none;"><?php echo $i ?></a>
                <?php } ?>
                <a href="javascript:void(0);" class="wpc_pagination_links wpc_next_pages">...</a>
            <?php } ?>
            <a href="javascript:void(0);" class="wpc_pagination_links wpc_next">></a>
            <a href="javascript:void(0);" class="wpc_pagination_links wpc_last">>></a>
        <?php } ?>
    </div>

    <div class="wpc_clear"></div>
    <div class="wpc_pm_project_list_table_block">
        <table class="projects_list_table">
            <thead>
                <tr>
                    <th class="name_field wpc_sortable wpc_th_title">
                        <span class="wpc_cust_sort_title wpc_cust_project_sort"><?php _e( 'Project Name', WPC_CLIENT_TEXT_DOMAIN ) ?></span>
                    </th>
                    <th class="desc_field wpc_th_description">
                        <?php _e( 'Description', WPC_CLIENT_TEXT_DOMAIN ) ?>
                    </th>
                    <th class="progress_field wpc_sortable wpc_th_progress">
                        <span class="wpc_cust_sort_percent wpc_cust_project_sort"><?php _e( 'Progress', WPC_CLIENT_TEXT_DOMAIN ) ?></span>
                    </th>
                    <th class="completion_date_field wpc_sortable wpc_th_due_date">
                        <span class="wpc_cust_sort_due_date wpc_cust_project_sort"><?php _e( 'Completion Date', WPC_CLIENT_TEXT_DOMAIN ) ?></span>
                    </th>
                </tr>
            </thead>
            <tbody class="blocks_sortable height">
                <tr class="wpc_pm_project_items"></tr>
            </tbody>
        </table>
    </div>
</div>