<?php
if ( ! defined( 'ABSPATH' ) ) { exit; } ?>
<script type="text/javascript">
    var wpc_activity_type = '';
    jQuery(document).ready(function() {
        jQuery('.cancel_button').click(function() {
            window.location = '<?php echo add_query_arg( array( 'page' => 'wpc_project_management' ), admin_url('admin.php') ); ?>';
        });

    });

</script>
<div class='wrap'>
    <?php echo $wpc_client->get_plugin_logo_block(); ?>
    <div class="wpc_clear"></div>
    <div class="icon32" id="icon-themes"><br /></div>

    <?php $this->get_breadcrumbs() ?>

    <div class="wpc_clear"></div>
    <?php if( $action_msg ) { ?>
        <div id="message" class="error">
            <p>
                <?php echo $action_msg; ?>
            </p>
        </div>
        <div class="wpc_clear"></div>
    <?php } ?>
    <?php
 if( isset( $_GET['act'] ) && 'edit' == $_GET['act'] ) { echo $this->gen_project_tabs_menu(); } else { echo $this->gen_tabs_menu(); } ?>
    <div class="wpc_clear"></div>
    <div class="wpc_pm_main_block">
        <div class="project_form">
            <div class="col-wrap">
                <form action="" method="post">
                    <p>
                        <label>
                            <?php printf( __( '%s Name (required)', WPC_CLIENT_TEXT_DOMAIN ), $wpc_client->custom_titles['project']['s'] ); ?>:<br />
                            <input type="text" name="title" class="title_field regular-text" value="<?php echo( isset( $title ) ? esc_attr( $title ) : '' ); ?>" />
                        </label>
                    </p>
                    <p class="business_sector_outer">
                        <label>
                            <?php echo $wpc_client->custom_titles['business_sector']['s']; ?>:<br />
                            <select name="business_sector" id="business_sector_select">
                                <option value="0"><?php _e( 'None', WPC_CLIENT_TEXT_DOMAIN ); ?></option>
                                <?php foreach( $sectors as $sector ) { ?>
                                    <option value="<?php echo $sector['id']; ?>" <?php selected( isset( $business_sector ) ? $business_sector : 0, $sector['id'] ); ?>><?php echo $sector['title']; ?></option>
                                <?php } ?>
                            </select>
                        </label>
                    </p>
                    <p>
                        <label>
                            <?php _e( 'Description', WPC_CLIENT_TEXT_DOMAIN ); ?>:<br />
                            <textarea name="description" rows="10" cols="30" class="regular-text description_field"><?php echo( isset( $description ) ? esc_attr( $description ) : '' ); ?></textarea>
                        </label>
                    </p>
                    <p>
                        <?php
 $counter = ''; if( isset( $client_id ) && is_numeric( $client_id ) && 0 < $client_id ) { $business_name = get_user_meta( $client_id, 'wpc_cl_business_name', true ); $client = get_userdata( $client_id ); $counter = $business_name ? $business_name : isset( $client->data->user_login ) ? $client->data->user_login : ''; } $link_array = array( 'title' => __( 'Select Client', WPC_CLIENT_TEXT_DOMAIN ), 'text' => __( 'Select Client', WPC_CLIENT_TEXT_DOMAIN ), 'data-marks' => 'radio' ); $input_array = array( 'name' => 'wpc_clients', 'id' => 'wpc_clients', 'value' => isset( $client_id ) ? $client_id : '' ); $additional_array = array( 'counter_value' => $counter ); $wpc_client->acc_assign_popup( 'client', isset( $current_page ) ? $current_page : '', $link_array, $input_array, $additional_array ); ?>
                    </p>
                    <p>
                        <label>
                            <?php _e( 'Projected Completion Date', WPC_CLIENT_TEXT_DOMAIN ); ?>:<br />
                            <input type="text" name="fake_due_date" class="due_date_field custom_datepicker_field regular-text" value="" style="width: 300px;" />
                            <input type="hidden" name="due_date" value="<?php echo( !empty( $due_date ) && is_numeric( $due_date ) ? $due_date : '' ); ?>" />
                        </label>
                    </p>
                    <p>&nbsp;</p>

                        <label>
                            <?php _e( 'Assigned to the project', WPC_CLIENT_TEXT_DOMAIN ); ?>:<br />
                            <?php
 $link_array = array( 'title' => sprintf( __( 'Assign %s and %s', WPC_CLIENT_TEXT_DOMAIN ), $wpc_client->custom_titles['project_manager']['p'], $wpc_client->custom_titles['admin']['p'] ), 'text' => sprintf( __( 'Assign %s and %s', WPC_CLIENT_TEXT_DOMAIN ), $wpc_client->custom_titles['project_manager']['p'], $wpc_client->custom_titles['admin']['p'] ), 'data-type' => 'wpc_project_manager' ); $input_array = array( 'name' => 'wpc_project_manager', 'id' => 'wpc_project_manager', 'value' => isset( $wpc_project_managers ) ? implode(',', $wpc_project_managers) : '' ); $additional_array = array( 'counter_value' => isset( $wpc_project_managers ) ? count( $wpc_project_managers ) : 0, 'wpc_ajax_prefix' => 'wpc_pm' ); $wpc_client->acc_assign_popup('wpc_project_manager', isset( $current_page ) ? $current_page : '', $link_array, $input_array, $additional_array ); ?>
                            <br />
                            <?php
 $link_array = array( 'title' => sprintf( __( 'Assign %s', WPC_CLIENT_TEXT_DOMAIN ), $wpc_client->custom_titles['teammate']['p'] ), 'text' => sprintf( __( 'Assign %s', WPC_CLIENT_TEXT_DOMAIN ), $wpc_client->custom_titles['teammate']['p'] ), 'data-type' => 'wpc_teammate' ); $input_array = array( 'name' => 'wpc_teammate', 'id' => 'wpc_teammate', 'value' => isset( $wpc_teammate ) ? implode(',', $wpc_teammate) : '' ); $additional_array = array( 'counter_value' => isset( $wpc_teammate ) ? count( $wpc_teammate ) : 0, 'only_link' => true, 'wpc_ajax_prefix' => 'wpc_pm' ); $wpc_client->acc_assign_popup('wpc_teammate', isset( $current_page ) ? $current_page : '', $link_array, $input_array, $additional_array ); ?>
                            <br />
                            <?php
 $link_array = array( 'title' => sprintf( __( 'Assign %s', WPC_CLIENT_TEXT_DOMAIN ), $wpc_client->custom_titles['freelancer']['p'] ), 'text' => sprintf( __( 'Assign %s', WPC_CLIENT_TEXT_DOMAIN ), $wpc_client->custom_titles['freelancer']['p'] ), 'data-type' => 'wpc_freelancer' ); $input_array = array( 'name' => 'wpc_freelancer', 'id' => 'wpc_freelancer', 'value' => isset( $wpc_freelancer ) ? implode(',', $wpc_freelancer) : '' ); $additional_array = array( 'counter_value' => isset( $wpc_freelancer ) ? count( $wpc_freelancer ) : 0, 'only_link' => true, 'wpc_ajax_prefix' => 'wpc_pm' ); $wpc_client->acc_assign_popup('wpc_freelancer', isset( $current_page ) ? $current_page : '', $link_array, $input_array, $additional_array ); ?>
                            <br />
                            <?php
 $link_array = array( 'title' => sprintf( __( 'Assign %s', WPC_CLIENT_TEXT_DOMAIN ), $wpc_client->custom_titles['team']['p'] ), 'text' => sprintf( __( 'Assign %s', WPC_CLIENT_TEXT_DOMAIN ), $wpc_client->custom_titles['team']['p'] ), 'data-type' => 'wpc_team' ); $input_array = array( 'name' => 'wpc_team', 'id' => 'wpc_team', 'value' => isset( $wpc_team ) ? implode(',', $wpc_team) : '' ); $additional_array = array( 'counter_value' => isset( $wpc_team ) ? count( $wpc_team ) : 0, 'wpc_ajax_prefix' => 'wpc_pm' ); $wpc_client->acc_assign_popup('wpc_team', isset( $current_page ) ? $current_page : '', $link_array, $input_array, $additional_array ); ?>
                        </label>

                    <?php
 if ( is_array( $custom_fields ) && 0 < count( $custom_fields ) ) { $wpc_client->add_custom_fields_scripts(); foreach( $custom_fields as $key => $value ) { if ( 'hidden' == $value['type'] ) { echo $value['field']; } elseif ( 'checkbox' == $value['type'] || 'radio' == $value['type'] ) { echo '<p>&nbsp;</p><p>'; echo ( !empty( $value['label'] ) ) ? $value['label'] . '<br />' : ''; if ( !empty( $value['field'] ) ) foreach ( $value['field'] as $field ) { echo $field . '<br />'; } echo ( !empty( $value['description'] ) ) ? $value['description'] : ''; echo '</p>'; } else { echo '<p>&nbsp;</p><p>'; echo ( !empty( $value['label'] ) ) ? $value['label'] . '<br />' : ''; echo ( !empty( $value['field'] ) ) ? $value['field'] : ''; echo ( !empty( $value['description'] ) ) ? '<br />' . $value['description']: ''; echo '</p>'; } } } ?>

                    <p>&nbsp;</p>
                    <p>
                        <table width="100%" border="0" cellpadding="2" cellspacing="1">
                            <tr>
                                <td width="50%">
                                    <?php _e( 'Estimated Value of Project', WPC_CLIENT_TEXT_DOMAIN ); ?><br />
                                    <input type="text" name="cost" id="cost" value="<?php echo( ( isset( $cost ) && $cost > 0 ) ? $cost : '' ); ?>" />
                                </td>
                                <td width="50%">
                                    <?php _e( 'Currency', WPC_CLIENT_TEXT_DOMAIN ); ?><br />
                                    <select name="currency" id="currency">
                                        <?php foreach( $wpc_currency as $val ) { $select = $val['default']; if( isset( $currency ) && !empty( $currency ) ) { if( $currency == $val['code'] ) { $select = 1; } else { $select = 0; } } ?>
                                            <option value="<?php echo $val['code']; ?>" <?php selected( $select, 1 ); ?>><?php echo $val['title']; ?></option>
                                        <?php
 } ?>
                                    </select>
                                </td>
                            </tr>
                        </table>
                    </p>
                    <?php if( isset( $_GET['action'] ) && 'duplicate' == $_GET['action'] ) { ?>
                        <p>
                            <?php _e( 'Additional Settings', WPC_CLIENT_TEXT_DOMAIN ); ?><br />
                            <label>
                                <input type="hidden" name="additional_settings[duplicate_milestones]" value="0" />
                                <input type="checkbox" name="additional_settings[duplicate_milestones]" value="1" <?php checked( !( isset( $additional_settings['duplicate_milestones'] ) && $additional_settings['duplicate_milestones'] == 0 ) ); ?> />
                                <?php _e( 'Duplicate Milestones', WPC_CLIENT_TEXT_DOMAIN ); ?><br />
                            </label> <br />
                            <label>
                                <input type="hidden" name="additional_settings[duplicate_tasks]" value="0" />
                                <input type="checkbox" name="additional_settings[duplicate_tasks]" value="1" <?php checked( !( isset( $additional_settings['duplicate_tasks'] ) && $additional_settings['duplicate_tasks'] == 0 ) ); ?> />
                                <?php _e( 'Duplicate Tasks', WPC_CLIENT_TEXT_DOMAIN ); ?><br />
                            </label>
                        </p>
                    <?php } ?>
                    <p align="left">
                        <?php
 $action = isset( $_GET['action'] ) ? $_GET['action'] : ''; switch( $action ) { case 'add': ?>
                                    <input type="submit" class="button-primary" value="<?php printf( __( 'Create %s', WPC_CLIENT_TEXT_DOMAIN ), $wpc_client->custom_titles['project']['s'] ); ?>" />
                                <?php
 break; case 'duplicate': ?>
                                    <input type="submit" class="button-primary" value="<?php printf( __( 'Duplicate %s', WPC_CLIENT_TEXT_DOMAIN ), $wpc_client->custom_titles['project']['s'] ); ?>" />
                                <?php
 break; default: ?>
                                    <input type="submit" class="button-primary" value="<?php printf( __( 'Edit %s', WPC_CLIENT_TEXT_DOMAIN ), $wpc_client->custom_titles['project']['s'] ); ?>" />
                                <?php
 break; } ?>
                        <input type="button" name="cancel" class="cancel_button button" value="Cancel">
                    </p>
                </form>
            </div>
        </div>

        <div class="wpc_pm_activities_block">
            <div class="activity_show_border"><div class="activity_show_button"><?php _e( 'Activities', WPC_CLIENT_TEXT_DOMAIN ) ?></div></div>
            <h2 class="title"><?php _e( 'Activities', WPC_CLIENT_TEXT_DOMAIN ) ?></h2>
            <?php $this->show_activities(); ?>
            <a id="wpc_next_page" href="javascript:void(0);"></a>
        </div>
    </div>
</div>