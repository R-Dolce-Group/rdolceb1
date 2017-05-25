<?php
if ( ! defined( 'ABSPATH' ) ) { exit; } ?>

<style>
    .ui-button-text-only .ui-button-text {
        padding: 2px 5px;
    }
</style>
<script type="text/javascript">
    jQuery(document).ready(function() {
        var digest_show = <?php echo isset( $user_settings['digest']['wpc_pm_visibility'] ) ? $user_settings['digest']['wpc_pm_visibility'] : 0 ?>;
        var outlook_show = <?php echo isset( $user_settings['outlook']['wpc_pm_visibility'] ) ? $user_settings['outlook']['wpc_pm_visibility'] : 0 ?>;
        jQuery( ".wpc_pm_digest_visibility" ).buttonset();
        jQuery( ".wpc_pm_digest_period" ).buttonset();
        jQuery( ".wpc_pm_outlooks_visibility" ).buttonset();
        jQuery( ".wpc_pm_outlooks_period" ).buttonset();

        if( !digest_show ) {
            jQuery('.wpc_pm_digest').hide();
        }
        if( !outlook_show ) {
            jQuery('.wpc_pm_outlook').hide();
        }

        jQuery(".wpc_pm_digest_visibility input").on("change", function () {
            if( this.value == 1 ) {
                jQuery('.wpc_pm_digest').show();
            } else {
                jQuery('.wpc_pm_digest').hide();
            }
        });

        jQuery(".wpc_pm_outlooks_visibility input").on("change", function () {
            if( this.value == 1 ) {
                jQuery('.wpc_pm_outlook').show();
            } else {
                jQuery('.wpc_pm_outlook').hide();
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
 switch( $_GET['message'] ) { case '1': _e( 'Settings updated successfully.', WPC_CLIENT_TEXT_DOMAIN ); break; } ?>
            </p>
        </div>
        <div class="wpc_clear"></div>
    <?php } ?>
    <?php echo $this->gen_tabs_menu(); ?>
    <div class="wpc_clear"></div>
    <div class="wpc_pm_main_block teams_page">
        <form action="" method="post" name="wpc_settings" id="wpc_settings">
            <span class="description"><?php _e( "Use this section to select personal notification settings", WPC_CLIENT_TEXT_DOMAIN ) ?></span>
            <hr />
            <h3><?php _e( "Send me an email when:", WPC_CLIENT_TEXT_DOMAIN ) ?></h3>
            <?php if( current_user_can('wpc_pm_level_3') ) { ?>
                <table border="0" cellpadding="0" cellspacing="0" class="wpc_pm_settings_table">
                    <tr>
                        <td width="30">
                            <input type="checkbox" name="settings[file_uploaded_task_project]" class="wpc_pm_send_email" value="1" <?php checked( isset( $user_settings['file_uploaded_task_project'] ) ? $user_settings['file_uploaded_task_project'] : 0, 1 ); ?> />
                        </td>
                        <td>
                            <?php _e( 'A file is uploaded in my Task(s)/Project(s)', WPC_CLIENT_TEXT_DOMAIN ) ?>
                        </td>
                    </tr>
                    <tr>
                        <td width="30">
                            <input type="checkbox" name="settings[user_assigned_to_project]" class="wpc_pm_send_email" value="1" <?php checked( isset( $user_settings['user_assigned_to_project'] ) ? $user_settings['user_assigned_to_project'] : 0, 1 ); ?> />
                        </td>
                        <td>
                            <?php _e( 'I am assigned to a new Project', WPC_CLIENT_TEXT_DOMAIN ) ?>
                        </td>
                    </tr>
                    <tr>
                        <td width="30">
                            <input type="checkbox" name="settings[task_status_updated_in_project]" class="wpc_pm_send_email" value="1" <?php checked( isset( $user_settings['task_status_updated_in_project'] ) ? $user_settings['task_status_updated_in_project'] : 0, 1 ); ?> />
                        </td>
                        <td>
                            <?php _e( 'A Task status is updated in my Project(s)', WPC_CLIENT_TEXT_DOMAIN ) ?>
                        </td>
                    </tr>
                    <tr>
                        <td width="30">
                            <input type="checkbox" name="settings[client_posted_message]" class="wpc_pm_send_email" value="1" <?php checked( isset( $user_settings['client_posted_message'] ) ? $user_settings['client_posted_message'] : 0, 1 ); ?> />
                        </td>
                        <td>
                            <?php _e( 'Client posts message in Client Talk in my Project(s)', WPC_CLIENT_TEXT_DOMAIN ) ?>
                        </td>
                    </tr>
                    <?php if( current_user_can('wpc_pm_level_2') ) { ?>
                        <tr>
                            <td width="30">
                                <input type="checkbox" name="settings[team_talk_posted]" class="wpc_pm_send_email" value="1" <?php checked( isset( $user_settings['team_talk_posted'] ) ? $user_settings['team_talk_posted'] : 0, 1 ); ?> />
                            </td>
                            <td>
                                <?php _e( 'A message is posted in Team Talk in my Project(s)', WPC_CLIENT_TEXT_DOMAIN ) ?>
                            </td>
                        </tr>
                    <?php } ?>
                    <tr>
                        <td width="30">
                            <input type="checkbox" name="settings[task_talk_posted]" class="wpc_pm_send_email" value="1" <?php checked( isset( $user_settings['task_talk_posted'] ) ? $user_settings['task_talk_posted'] : 0, 1 ); ?> />
                        </td>
                        <td>
                            <?php _e( 'A user adds message to a Task in my Project(s)', WPC_CLIENT_TEXT_DOMAIN ) ?>
                        </td>
                    </tr>
                    <tr>
                        <td width="30">
                            <input type="checkbox" name="settings[developer_assigned_to_project]" class="wpc_pm_send_email" value="1" <?php checked( isset( $user_settings['developer_assigned_to_project'] ) ? $user_settings['developer_assigned_to_project'] : 0, 1 ); ?> />
                        </td>
                        <td>
                            <?php _e( 'A new Teammate/Freelancer is assigned to my Project(s)', WPC_CLIENT_TEXT_DOMAIN ) ?>
                        </td>
                    </tr>
                </table>
            <?php } else { ?>
                <table border="0" cellpadding="0" cellspacing="0" class="wpc_pm_settings_table">
                    <?php if( isset( $global_settings['user_assigned_task_visibility'] ) && $global_settings['user_assigned_task_visibility'] ) { ?>
                        <tr>
                            <td width="30">
                                <input type="checkbox" name="settings[user_assigned_task]" class="wpc_pm_send_email" value="1" <?php checked( isset( $user_settings['user_assigned_task'] ) ? $user_settings['user_assigned_task'] : 0, 1 ); ?> />
                            </td>
                            <td>
                                <?php _e( 'A Task is assigned to me', WPC_CLIENT_TEXT_DOMAIN ) ?>
                            </td>
                        </tr>
                    <?php } if( isset( $global_settings['user_assigned_to_project_visibility'] ) && $global_settings['user_assigned_to_project_visibility'] ) { ?>
                        <tr>
                            <td width="30">
                                <input type="checkbox" name="settings[user_assigned_to_project]" class="wpc_pm_send_email" value="1" <?php checked( isset( $user_settings['user_assigned_to_project'] ) ? $user_settings['user_assigned_to_project'] : 0, 1 ); ?> />
                            </td>
                            <td>
                                <?php _e( 'I am assigned to a Project', WPC_CLIENT_TEXT_DOMAIN ) ?>
                            </td>
                        </tr>
                    <?php } if( isset( $global_settings['message_posted_to_my_task_visibility'] ) && $global_settings['message_posted_to_my_task_visibility'] ) { ?>
                        <tr>
                            <td width="30">
                                <input type="checkbox" name="settings[message_posted_to_my_task]" class="wpc_pm_send_email" value="1" <?php checked( isset( $user_settings['file_uploaded_task_project'] ) ? $user_settings['file_uploaded_task_project'] : 0, 1 ); ?> />
                            </td>
                            <td>
                                <?php _e( 'A message is posted on my assigned Task(s)', WPC_CLIENT_TEXT_DOMAIN ) ?>
                            </td>
                        </tr>
                    <?php } if( isset( $global_settings['file_uploaded_to_my_task_visibility'] ) && $global_settings['file_uploaded_to_my_task_visibility'] ) { ?>
                        <tr>
                            <td width="30">
                                <input type="checkbox" name="settings[file_uploaded_to_my_task]" class="wpc_pm_send_email" value="1" <?php checked( isset( $user_settings['file_uploaded_to_my_task'] ) ? $user_settings['file_uploaded_to_my_task'] : 0, 1 ); ?> />
                            </td>
                            <td>
                                <?php _e( 'A file is uploaded to my assigned Task(s)', WPC_CLIENT_TEXT_DOMAIN ) ?>
                            </td>
                        </tr>
                    <?php } if( isset( $global_settings['team_talk_posted_visibility'] ) && $global_settings['team_talk_posted_visibility'] ) { ?>
                        <tr>
                            <td width="30">
                                <input type="checkbox" name="settings[team_talk_posted]" class="wpc_pm_send_email" value="1" <?php checked( isset( $user_settings['team_talk_posted'] ) ? $user_settings['team_talk_posted'] : 0, 1 ); ?> />
                            </td>
                            <td>
                                <?php _e( 'A message is posted in Team Talk', WPC_CLIENT_TEXT_DOMAIN ) ?>
                            </td>
                        </tr>
                    <?php } if( isset( $global_settings['file_uploaded_to_my_project_visibility'] ) && $global_settings['file_uploaded_to_my_project_visibility'] ) { ?>
                        <tr>
                            <td width="30">
                                <input type="checkbox" name="settings[file_uploaded_to_my_project]" class="wpc_pm_send_email" value="1" <?php checked( isset( $user_settings['file_uploaded_to_my_project'] ) ? $user_settings['file_uploaded_to_my_project'] : 0, 1 ); ?> />
                            </td>
                            <td>
                                <?php _e( 'A file is uploaded to my assigned Project(s)', WPC_CLIENT_TEXT_DOMAIN ) ?>
                            </td>
                        </tr>
                    <?php } if( isset( $global_settings['priority_modified_on_my_task_visibility'] ) && $global_settings['priority_modified_on_my_task_visibility'] ) { ?>
                        <tr>
                            <td width="30">
                                <input type="checkbox" name="settings[priority_modified_on_my_task]" class="wpc_pm_send_email" value="1" <?php checked( isset( $user_settings['priority_modified_on_my_task'] ) ? $user_settings['priority_modified_on_my_task'] : 0, 1 ); ?> />
                            </td>
                            <td>
                                <?php _e( 'The priority is modified on my assigned Task(s)', WPC_CLIENT_TEXT_DOMAIN ) ?>
                            </td>
                        </tr>
                    <?php } if( isset( $global_settings['due_date_modified_on_my_task_visibility'] ) && $global_settings['due_date_modified_on_my_task_visibility'] ) { ?>
                        <tr>
                            <td width="30">
                                <input type="checkbox" name="settings[due_date_modified_on_my_task]" class="wpc_pm_send_email" value="1" <?php checked( isset( $user_settings['due_date_modified_on_my_task'] ) ? $user_settings['due_date_modified_on_my_task'] : 0, 1 ); ?> />
                            </td>
                            <td>
                                <?php _e( 'The due date is modified on my assigned Task(s)', WPC_CLIENT_TEXT_DOMAIN ) ?>
                            </td>
                        </tr>
                    <?php } ?>
                </table>
            <?php }?>

            <h3>
                <?php _e( "Receive email Digests?", WPC_CLIENT_TEXT_DOMAIN ) ?>
                <span class="wpc_pm_digest_visibility">
                    <input type="radio" id="wpc_pm_digest_hide" name="settings[digest][wpc_pm_visibility]" value="0" <?php checked( isset( $user_settings['digest']['wpc_pm_visibility'] ) ? $user_settings['digest']['wpc_pm_visibility'] : 0, 0 ); ?>><label for="wpc_pm_digest_hide"><?php _e( 'No', WPC_CLIENT_TEXT_DOMAIN ) ?></label>
                    <input type="radio" id="wpc_pm_digest_show" name="settings[digest][wpc_pm_visibility]" value="1" <?php checked( isset( $user_settings['digest']['wpc_pm_visibility'] ) ? $user_settings['digest']['wpc_pm_visibility'] : 0, 1 ); ?>><label for="wpc_pm_digest_show"><?php _e( 'Yes', WPC_CLIENT_TEXT_DOMAIN ) ?></label>
                </span>
                <span class="wpc_pm_digest_period">
                    <input type="checkbox" id="wpc_pm_digest_daily" name="settings[digest][wpc_pm_period][]" value="1" <?php checked( isset( $user_settings['digest']['wpc_pm_period'] ) && is_array( $user_settings['digest']['wpc_pm_period'] ) && in_array( 1, $user_settings['digest']['wpc_pm_period'] ) ); ?>><label for="wpc_pm_digest_daily"><?php _e( 'Daily', WPC_CLIENT_TEXT_DOMAIN ) ?></label>
                    <input type="checkbox" id="wpc_pm_digest_weekly" name="settings[digest][wpc_pm_period][]" value="7" <?php checked( isset( $user_settings['digest']['wpc_pm_period'] ) && is_array( $user_settings['digest']['wpc_pm_period'] ) && in_array( 7, $user_settings['digest']['wpc_pm_period'] ) ); ?>><label for="wpc_pm_digest_weekly"><?php _e( 'Weekly', WPC_CLIENT_TEXT_DOMAIN ) ?></label>
                </span>
            </h3>
            <h3 class="wpc_pm_digest"><?php _e( "Include in Digest:", WPC_CLIENT_TEXT_DOMAIN ) ?></h3>
            <?php if( current_user_can('wpc_pm_level_3') ) { ?>
                <table border="0" cellpadding="0" cellspacing="0" class="wpc_pm_settings_table wpc_pm_digest">
                    <tr>
                        <td width="30">
                            <input type="checkbox" name="settings[digest][assigned_projects]" class="wpc_pm_send_email" value="1" <?php checked( isset( $user_settings['digest']['assigned_projects'] ) ? $user_settings['digest']['assigned_projects'] : 0, 1 ); ?> />
                        </td>
                        <td>
                            <?php _e( 'Assigned Projects', WPC_CLIENT_TEXT_DOMAIN ) ?>
                        </td>
                    </tr>
                    <tr>
                        <td width="30">
                            <input type="checkbox" name="settings[digest][completed_tasks]" class="wpc_pm_send_email" value="1" <?php checked( isset( $user_settings['digest']['completed_tasks'] ) ? $user_settings['digest']['completed_tasks'] : 0, 1 ); ?> />
                        </td>
                        <td>
                            <?php _e( 'Tasks completed', WPC_CLIENT_TEXT_DOMAIN ) ?>
                        </td>
                    </tr>
                    <tr>
                        <td width="30">
                            <input type="checkbox" name="settings[digest][uploaded_files]" class="wpc_pm_send_email" value="1" <?php checked( isset( $user_settings['digest']['uploaded_files'] ) ? $user_settings['digest']['uploaded_files'] : 0, 1 ); ?> />
                        </td>
                        <td>
                            <?php _e( 'Uploaded Files', WPC_CLIENT_TEXT_DOMAIN ) ?>
                        </td>
                    </tr>
                    <tr>
                        <td width="30">
                            <input type="checkbox" name="settings[digest][task_messages]" class="wpc_pm_send_email" value="1" <?php checked( isset( $user_settings['digest']['task_messages'] ) ? $user_settings['digest']['task_messages'] : 0, 1 ); ?> />
                        </td>
                        <td>
                            <?php _e( 'Messages in assigned Tasks', WPC_CLIENT_TEXT_DOMAIN ) ?>
                        </td>
                    </tr>
                    <tr>
                        <td width="30">
                            <input type="checkbox" name="settings[digest][team_talk]" class="wpc_pm_send_email]" value="1" <?php checked( isset( $user_settings['digest']['team_talk'] ) ? $user_settings['digest']['team_talk'] : 0, 1 ); ?> />
                        </td>
                        <td>
                            <?php _e( 'Team Talk posts', WPC_CLIENT_TEXT_DOMAIN ) ?>
                        </td>
                    </tr>
                    <tr>
                        <td width="30">
                            <input type="checkbox" name="settings[digest][client_talk]" class="wpc_pm_send_email" value="1" <?php checked( isset( $user_settings['digest']['client_talk'] ) ? $user_settings['digest']['client_talk'] : 0, 1 ); ?> />
                        </td>
                        <td>
                            <?php _e( 'Client Talk posts', WPC_CLIENT_TEXT_DOMAIN ) ?>
                        </td>
                    </tr>
                </table>
            <?php } else { ?>
                <table border="0" cellpadding="0" cellspacing="0" class="wpc_pm_settings_table wpc_pm_digest">
                    <tr>
                        <td width="30">
                            <input type="checkbox" name="settings[digest][assigned_tasks]" class="wpc_pm_send_email" value="1" <?php checked( isset( $user_settings['digest']['assigned_tasks'] ) ? $user_settings['digest']['assigned_tasks'] : 0, 1 ); ?> />
                        </td>
                        <td>
                            <?php _e( 'Assigned Tasks', WPC_CLIENT_TEXT_DOMAIN ) ?>
                        </td>
                    </tr>
                    <tr>
                        <td width="30">
                            <input type="checkbox" name="settings[digest][assigned_projects]" class="wpc_pm_send_email" value="1" <?php checked( isset( $user_settings['digest']['assigned_projects'] ) ? $user_settings['digest']['assigned_projects'] : 0, 1 ); ?> />
                        </td>
                        <td>
                            <?php _e( 'Assigned Projects', WPC_CLIENT_TEXT_DOMAIN ) ?>
                        </td>
                    </tr>
                    <tr>
                        <td width="30">
                            <input type="checkbox" name="settings[digest][uploaded_files]" class="wpc_pm_send_email" value="1" <?php checked( isset( $user_settings['digest']['uploaded_files'] ) ? $user_settings['digest']['uploaded_files'] : 0, 1 ); ?> />
                        </td>
                        <td>
                            <?php _e( 'Uploaded Files', WPC_CLIENT_TEXT_DOMAIN ) ?>
                        </td>
                    </tr>
                    <tr>
                        <td width="30">
                            <input type="checkbox" name="settings[digest][task_messages]" class="wpc_pm_send_email" value="1" <?php checked( isset( $user_settings['digest']['task_messages'] ) ? $user_settings['digest']['task_messages'] : 0, 1 ); ?> />
                        </td>
                        <td>
                            <?php _e( 'Messages in assigned Tasks', WPC_CLIENT_TEXT_DOMAIN ) ?>
                        </td>
                    </tr>
                    <?php if( current_user_can('wpc_pm_level_2') ) { ?>
                        <tr>
                            <td width="30">
                                <input type="checkbox" name="settings[digest][team_talk]" class="wpc_pm_send_email" value="1" <?php checked( isset( $user_settings['digest']['team_talk'] ) ? $user_settings['digest']['team_talk'] : 0, 1 ); ?> />
                            </td>
                            <td>
                                <?php _e( 'Team Talk posts', WPC_CLIENT_TEXT_DOMAIN ) ?>
                            </td>
                        </tr>
                    <?php } ?>
                </table>
            <?php }?>

            <h3>
                <?php _e( "Receive email Outlooks?", WPC_CLIENT_TEXT_DOMAIN ); ?>
                <span class="wpc_pm_outlooks_visibility">
                    <input type="radio" id="wpc_pm_outlooks_hide" name="settings[outlook][wpc_pm_visibility]" value="0" <?php checked( isset( $user_settings['outlook']['wpc_pm_visibility'] ) ? $user_settings['outlook']['wpc_pm_visibility'] : 0, 0 ); ?>><label for="wpc_pm_outlooks_hide"><?php _e( 'No', WPC_CLIENT_TEXT_DOMAIN ) ?></label>
                    <input type="radio" id="wpc_pm_outlooks_show" name="settings[outlook][wpc_pm_visibility]" value="1" <?php checked( isset( $user_settings['outlook']['wpc_pm_visibility'] ) ? $user_settings['outlook']['wpc_pm_visibility'] : 0, 1 ); ?>><label for="wpc_pm_outlooks_show"><?php _e( 'Yes', WPC_CLIENT_TEXT_DOMAIN ) ?></label>
                </span>
                <span class="wpc_pm_outlooks_period">
                    <?php _e( "Start report", WPC_CLIENT_TEXT_DOMAIN ); ?> <input type="text" id="wpc_pm_outlooks_period" name="settings[outlook][wpc_pm_period]" value="<?php echo ( isset( $user_settings['outlook']['wpc_pm_period'] ) ? $user_settings['outlook']['wpc_pm_period'] : 1 ); ?>" size="2" />
                    <?php _e( "day(s) before the end", WPC_CLIENT_TEXT_DOMAIN ); ?>
                </span>
            </h3>
            <h3 class="wpc_pm_outlook"><?php _e( "Include in Outlook:", WPC_CLIENT_TEXT_DOMAIN ) ?></h3>
            <table border="0" cellpadding="0" cellspacing="0" class="wpc_pm_settings_table wpc_pm_outlook">
                <tr>
                    <td width="30">
                        <input type="checkbox" name="settings[outlook][task_due_dates]" class="wpc_pm_send_email" value="1" <?php checked( isset( $user_settings['outlook']['task_due_dates'] ) ? $user_settings['outlook']['task_due_dates'] : 0, 1 ); ?> />
                    </td>
                    <td>
                        <?php _e( 'Upcoming Task due dates', WPC_CLIENT_TEXT_DOMAIN ) ?>
                    </td>
                </tr>
                <tr>
                    <td width="30">
                        <input type="checkbox" name="settings[outlook][project_due_dates]" class="wpc_pm_send_email" value="1" <?php checked( isset( $user_settings['outlook']['project_due_dates'] ) ? $user_settings['outlook']['project_due_dates'] : 0, 1 ); ?> />
                    </td>
                    <td>
                        <?php _e( 'Upcoming Project due dates', WPC_CLIENT_TEXT_DOMAIN ) ?>
                    </td>
                </tr>
            </table>

            <p>
                <input type='submit' name='update' id="update" class='button-primary' value='<?php _e( 'Update Settings', WPC_CLIENT_TEXT_DOMAIN ) ?>' />
            </p>
        </form>
    </div>
</div>