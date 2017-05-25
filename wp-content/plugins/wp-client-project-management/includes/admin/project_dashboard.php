<?php
if ( ! defined( 'ABSPATH' ) ) { exit; } $current_page = 'wpc_edit_project'; ?>
<script type="text/javascript">
    var wpc_activity_type = '';
    var project_id = '<?php echo( ( isset( $_GET['project_id'] ) && is_numeric( $_GET['project_id'] ) ) ? $_GET['project_id'] : 0 ); ?>';
    var wpc_object = 'project';
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
 switch( $_GET['message'] ) { case '2': _e( 'Project updated successfully.', WPC_CLIENT_TEXT_DOMAIN ); break; case '1': _e( 'Project created successfully.', WPC_CLIENT_TEXT_DOMAIN ); break; } ?>
            </p>
        </div>
        <div class="wpc_clear"></div>
    <?php } ?>
    <?php echo $this->gen_project_tabs_menu(); ?>
    <div class="wpc_clear"></div>
    <div class="wpc_pm_main_block">

            <div class="col-wrap">
                <h2>
                    <?php _e( 'Description', WPC_CLIENT_TEXT_DOMAIN ); if( current_user_can('wpc_pm_level_4') ) { ?>
                        <a class="edit-project button-primary" href="admin.php?page=wpc_project_management&action=details&act=edit&project_id=<?php echo( isset( $_GET['project_id'] ) ? $_GET['project_id'] : '' ); ?>" style="float: none; margin-left: 20px"><?php printf( __( 'Edit %s', WPC_CLIENT_TEXT_DOMAIN ), $wpc_client->custom_titles['project']['s'] ); ?></a>
                    <?php } if( current_user_can('wpc_pm_level_3') ) { ?>
                        <a class="add-task button-primary" href="admin.php?page=wpc_project_management&action=details&project_id=<?php echo( isset( $_GET['project_id'] ) ? $_GET['project_id'] : '' ); ?>&sub_tab=tasks&open_add_block=1" style="float: none; margin-left: 5px"><?php _e( 'Add Task', WPC_CLIENT_TEXT_DOMAIN ); ?></a>
                    <?php } ?>
                </h2>
                <p>
                    <?php echo $project_details['description']; ?>
                </p>
                <?php if( $project_details['due_date'] != '' ) { ?>
                    <h3><?php _e( 'Projected Completion Date', WPC_CLIENT_TEXT_DOMAIN ) ?></h3>
                    <p>
                        <?php echo $wpc_client->cc_date_format( $project_details['due_date'], 'date' ); ?>
                    </p>
                <?php } ?>
                <?php if( current_user_can('wpc_pm_level_3') ) { ?>
                    <p>
                        <span class="edit">
                        <?php
 echo __('Client', WPC_CLIENT_TEXT_DOMAIN) . ': '; if( isset( $project_details['client_id'] ) && is_numeric( $project_details['client_id'] ) && 0 < $project_details['client_id'] ) { $business_name = get_user_meta( $project_details['client_id'], 'wpc_cl_business_name', true ); echo( $business_name ? $business_name : ( ( $userdata = get_userdata( $project_details['client_id'] ) ) ? $userdata->data->user_login : '' ) ); } ?>
                        </span>
                    </p>
                    <p>&nbsp;</p>
                        <label>
                            <?php _e( 'Assigned to the project', WPC_CLIENT_TEXT_DOMAIN ); ?>:<br />
                            <?php
 $link_array = array( 'title' => sprintf( __( 'Assign %s and %s', WPC_CLIENT_TEXT_DOMAIN ), $wpc_client->custom_titles['project_manager']['p'], $wpc_client->custom_titles['admin']['p'] ), 'text' => sprintf( __( 'Assign %s and %s', WPC_CLIENT_TEXT_DOMAIN ), $wpc_client->custom_titles['project_manager']['p'], $wpc_client->custom_titles['admin']['p'] ), 'data-type' => 'wpc_project_manager', 'data-ajax' => 1, 'data-id' => $project_id ); $input_array = array( 'name' => 'wpc_project_manager', 'id' => 'wpc_project_manager_' . $project_id, 'value' => isset( $wpc_project_managers ) ? implode(',', $wpc_project_managers) : '' ); $additional_array = array( 'counter_value' => isset( $wpc_project_managers ) ? count( $wpc_project_managers ) : 0, 'wpc_ajax_prefix' => 'wpc_pm' ); $wpc_client->acc_assign_popup('wpc_project_manager', isset( $current_page ) ? $current_page : '', $link_array, $input_array, $additional_array ); ?>
                            <br />
                            <?php
 $link_array = array( 'title' => sprintf( __( 'Assign %s', WPC_CLIENT_TEXT_DOMAIN ), $wpc_client->custom_titles['teammate']['p'] ), 'text' => sprintf( __( 'Assign %s', WPC_CLIENT_TEXT_DOMAIN ), $wpc_client->custom_titles['teammate']['p'] ), 'data-type' => 'wpc_teammate', 'data-ajax' => 1, 'data-id' => $project_id ); $input_array = array( 'name' => 'wpc_teammate', 'id' => 'wpc_teammate_' . $project_id, 'value' => isset( $wpc_teammate ) ? implode(',', $wpc_teammate) : '' ); $additional_array = array( 'counter_value' => isset( $wpc_teammate ) ? count( $wpc_teammate ) : 0, 'only_link' => true, 'wpc_ajax_prefix' => 'wpc_pm' ); $wpc_client->acc_assign_popup('wpc_teammate', isset( $current_page ) ? $current_page : '', $link_array, $input_array, $additional_array ); ?>
                            <br />
                            <?php
 $link_array = array( 'title' => sprintf( __( 'Assign %s', WPC_CLIENT_TEXT_DOMAIN ), $wpc_client->custom_titles['freelancer']['p'] ), 'text' => sprintf( __( 'Assign %s', WPC_CLIENT_TEXT_DOMAIN ), $wpc_client->custom_titles['freelancer']['p'] ), 'data-type' => 'wpc_freelancer', 'data-ajax' => 1, 'data-id' => $project_id ); $input_array = array( 'name' => 'wpc_freelancer', 'id' => 'wpc_freelancer_' . $project_id, 'value' => isset( $wpc_freelancer ) ? implode(',', $wpc_freelancer) : '' ); $additional_array = array( 'counter_value' => isset( $wpc_freelancer ) ? count( $wpc_freelancer ) : 0, 'only_link' => true, 'wpc_ajax_prefix' => 'wpc_pm' ); $wpc_client->acc_assign_popup('wpc_freelancer', isset( $current_page ) ? $current_page : '', $link_array, $input_array, $additional_array ); ?>
                            <br />
                            <?php
 $link_array = array( 'title' => sprintf( __( 'Assign %s', WPC_CLIENT_TEXT_DOMAIN ), $wpc_client->custom_titles['team']['p'] ), 'text' => sprintf( __( 'Assign %s', WPC_CLIENT_TEXT_DOMAIN ), $wpc_client->custom_titles['team']['p'] ), 'data-type' => 'wpc_team', 'data-ajax' => 1, 'data-id' => $project_id ); $input_array = array( 'name' => 'wpc_team', 'id' => 'wpc_team_' . $project_id, 'value' => isset( $wpc_team ) ? implode(',', $wpc_team) : '' ); $additional_array = array( 'counter_value' => isset( $wpc_team ) ? count( $wpc_team ) : 0, 'wpc_ajax_prefix' => 'wpc_pm' ); $wpc_client->acc_assign_popup('wpc_team', isset( $current_page ) ? $current_page : '', $link_array, $input_array, $additional_array ); ?>
                        </label>
                    <p>&nbsp;</p>
                <?php } ?>

                <?php
 $custom_fields = $wpc_client->cc_get_settings( 'pm_custom_fields' ); if( is_array( $custom_fields ) ) { foreach( $custom_fields as $key=>$val ) { if( isset( $val['client_available'] ) && '1' == $val['client_available'] ) { ?>
                                <p><b><?php echo $val['title']; ?></b>:<br /> <?php echo $this->show_custom_field_values( $project_details['id'], $key ); ?></p>
                            <?php } } } ?>

                <h3><?php _e( 'Recently Added Tasks', WPC_CLIENT_TEXT_DOMAIN ) ?></h3>
                <p>
                    <ul type="circle">
                        <?php foreach( $recently_tasks as $task ) { ?>
                            <li>
                                <a href="<?php echo admin_url('admin.php?page=wpc_project_management&action=details&project_id=' . $project_id . '&sub_tab=tasks'); ?>"><?php echo '#' . $task['id_in_project'] . ' ' . $task['title']; ?></a>
                                <?php if( isset( $task['subtasks'] ) && count( $task['subtasks'] ) ) { ?>
                                    <ul style="margin-left: 10px;">
                                        <?php foreach( $task['subtasks'] as $val ) { ?>
                                            <li><a href="<?php echo admin_url('admin.php?page=wpc_project_management&action=details&project_id=' . $project_id . '&sub_tab=tasks'); ?>"><?php echo '#' . $val['id_in_project'] . ' ' . $val['title']; ?></a></li>
                                        <?php } ?>
                                    </ul>
                                <?php } ?>
                            </li>
                        <?php } ?>
                    </ul>
                </p>
                <h3><?php _e( 'Milestones', WPC_CLIENT_TEXT_DOMAIN ) ?></h3>
                <p>
                    <ul type="circle">
                        <?php foreach( $recently_milestones as $milestone ) { ?>
                            <li>
                                <a href="<?php echo admin_url('admin.php?page=wpc_project_management&action=details&project_id=' . $project_id . '&sub_tab=milestones'); ?>"><?php echo $milestone['title']; ?></a>
                            </li>
                        <?php } ?>
                    </ul>
                </p>
            </div>


                <div class="wpc_pm_activities_block">
                    <div class="activity_show_border"><div class="activity_show_button"><?php _e( 'Activities', WPC_CLIENT_TEXT_DOMAIN ) ?></div></div>
                    <h2 class="title">
                        <?php _e( 'Activities', WPC_CLIENT_TEXT_DOMAIN ) ?>
                        <span style="color: #888">&rarr;</span>
                        <div class="dropdown-block" data-value="">
                            <span class="selected">All</span>
                            <ul class="dropdown-menu">
                                <li><a href="#" data-value="milestone"><?php _e( 'Milestones', WPC_CLIENT_TEXT_DOMAIN ); ?></a></li>
                                <li><a href="#" data-value="task"><?php _e( 'Tasks', WPC_CLIENT_TEXT_DOMAIN ); ?></a></li>
                                <li><a href="#" data-value="file"><?php _e( 'Files', WPC_CLIENT_TEXT_DOMAIN ); ?></a></li>
                            </ul>
                        </div>
                    </h2>
                    <?php
 if( isset( $_GET['project_id'] ) && is_numeric( $_GET['project_id'] ) ) { $this->show_activities( 'project', $_GET['project_id'] ); } else { $this->show_activities(); } ?>
                    <a id="wpc_next_page" href="javascript:void(0);"></a>
                </div>

    </div>
</div>