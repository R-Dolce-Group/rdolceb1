<?php
if ( ! defined( 'ABSPATH' ) ) { exit; } ?>
<style>
    .ajax_big_loading {
        background-color: #eee;
        opacity: 0.5;
        z-index: 101000;
    }
</style>
<div class='wrap'>
    <?php echo $wpc_client->get_plugin_logo_block(); ?>
    <div class="wpc_clear"></div>
    <div class="icon32" id="icon-themes"><br /></div>

    <?php $this->get_breadcrumbs() ?>

    <div class="wpc_clear"></div>
    <?php echo $this->gen_tabs_menu(); ?>
    <div class="wpc_clear"></div>
    <div id="wpc_pm_scheduler" class="wpc_pm_main_block dhx_cal_container">
        <div  class="dhx_cal_container" style='width:100%;'>
            <div class="dhx_cal_navline">
                <div style="left: 10px;">
                    <label>
                        <?php _e( 'Filter by status:', WPC_CLIENT_TEXT_DOMAIN ) ?>
                        <select class="filter_status">
                            <option value=""><?php _e( 'All', WPC_CLIENT_TEXT_DOMAIN ) ?></option>
                            <option value="new"><?php _e( 'New', WPC_CLIENT_TEXT_DOMAIN ) ?></option>
                            <option value="open"><?php _e( 'Open', WPC_CLIENT_TEXT_DOMAIN ) ?></option>
                            <option value="pending"><?php _e( 'Pending', WPC_CLIENT_TEXT_DOMAIN ) ?></option>
                            <option value="testing"><?php _e( 'Testing', WPC_CLIENT_TEXT_DOMAIN ) ?></option>
                        </select>
                    </label>
                </div>
                <div class="dhx_cal_prev_button">&nbsp;</div>
                <div class="dhx_cal_next_button">&nbsp;</div>
                <div class="dhx_cal_today_button"></div>
                <div class="dhx_minical_icon" id="dhx_minical_icon" onclick="show_minical()" style="right: 215px; left: auto; !important">&nbsp;</div>
                <div class="dhx_cal_date"></div>
            </div>
            <div class="dhx_cal_header">
            </div>
            <div class="dhx_cal_data">
            </div>
        </div>
    </div>
</div>
<div class="ajax_big_loading"></div>

<script type="text/javascript" charset="utf-8">
    var filter = '';
    var last_date = '<?php echo time(); ?>';
    jQuery(document).ready(function($) {

        $('.filter_status').change(function() {
            jQuery('.ajax_big_loading').show();
            filter = $(this).val();
            scheduler.clearAll();
            jQuery.ajax({
                type     : 'POST',
                dataType : 'json',
                url      : '<?php echo get_admin_url(); ?>admin-ajax.php',
                data     : 'action=wpc_pm_pagination&type=calendar&date=' + last_date + '&filter=' + filter,
                success  : function( data ){
                    jQuery('.ajax_big_loading').hide();
                    if( data.status ) {
                        scheduler.parse( data.data,"json");
                    } else {
                        alert( data.message );
                    }
                },
                error: function() {
                    jQuery('.ajax_big_loading').hide();
                    alert('Wrong answer from server');
                }
            });
        });

        jQuery('#wpc_pm_scheduler').css({
            'height' : jQuery( window ).height()// - jQuery('#wpc_pm_scheduler').offset().top
        });
        jQuery('#wpc_pm_scheduler > div').css({
            'height' : jQuery('#wpc_pm_scheduler').height()// - jQuery('#wpc_pm_scheduler').offset().top
        });

        jQuery("#wpc_pm_scheduler").dhx_scheduler({
            xml_date:"%Y-%m-%d %H:%i",
            date:new Date(),
            mode:"month"
        });

        scheduler.config.multi_day = true;
        scheduler.config.month_day = '%d';
        scheduler.config.xml_date="%Y-%m-%d";

        scheduler.templates.event_bar_date = function() {
            return '';
        };

        scheduler.templates.tooltip_text = function( start, end, event ){
            return '<span class="' + event.priority + '"></span>' +
                '<b><?php _e( 'Task #', WPC_CLIENT_TEXT_DOMAIN ) ?>' + event.id_in_project + ': ' + event.text +
                '</b><hr />' +
                ( event.project_title != null ? '<b><?php _e( 'Project', WPC_CLIENT_TEXT_DOMAIN ) ?>:</b> ' + event.project_title + '<br />' : '') +
                '<b><?php _e( 'Pririty', WPC_CLIENT_TEXT_DOMAIN ) ?>:</b> ' + event.priority_title + '<br />' +
                '<b><?php _e( 'Status', WPC_CLIENT_TEXT_DOMAIN ) ?>:</b> ' + event.status_title + '<br />' +
                '<b><?php _e( 'Due Date', WPC_CLIENT_TEXT_DOMAIN ) ?>:</b> ' + event.die_date + '<br />'
                ;
        };

        scheduler.templates.event_bar_text = function( start, end, event ) {
            return '<a href="<?php echo add_query_arg( array( 'page'=> 'wpc_project_management', 'tab' => 'to_do' ), admin_url('admin.php') ); ?>&task_id=' + event.id + '">' +
                    '<span class="' + event.priority + '"></span>' +
                    '<?php _e( 'Task #', WPC_CLIENT_TEXT_DOMAIN ) ?>' + event.id_in_project + ': ' + event.text +
                '</a>';
        };

        scheduler.init('wpc_pm_scheduler',new Date(),"month");
        scheduler.load("<?php echo get_admin_url(); ?>admin-ajax.php?action=wpc_pm_pagination&type=calendar&mode=month&date=<?php echo time(); ?>&project_id=" + wpc_pm_common.project_id, "json", function() {
            jQuery('.ajax_big_loading').hide();
        });

        $(document).on('click', '.dhx_expand_icon', function() {
            $('body').toggleClass('full_screen_calendar');
        });

        scheduler.config.readonly = true;

        scheduler.attachEvent("onViewChange",function( mode, date ){
            jQuery('.ajax_big_loading').show();
            last_date = date.valueOf()/1000;
            jQuery.ajax({
                type     : 'POST',
                dataType : 'json',
                url      : '<?php echo get_admin_url(); ?>admin-ajax.php',
                data     : 'action=wpc_pm_pagination&type=calendar&date=' + last_date + '&filter=' + filter,
                success  : function( data ){
                    jQuery('.ajax_big_loading').hide();
                    if( data.status ) {
                        scheduler.parse( data.data,"json");
                    } else {
                        alert( data.message );
                    }
                },
                error: function() {
                    jQuery('.ajax_big_loading').hide();
                    alert('Wrong answer from server');
                }
            });
        });
    });

    function show_minical(){
        if (scheduler.isCalendarVisible())
            scheduler.destroyCalendar();
        else
            scheduler.renderCalendar({
                position:"dhx_minical_icon",
                date:scheduler._date,
                navigation:true,
                handler:function(date,calendar){
                    scheduler.setCurrentView(date);
                    scheduler.destroyCalendar()
                }
            });
    }
</script>