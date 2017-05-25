<?php
if ( ! defined( 'ABSPATH' ) ) { exit; } global $wpc_client; ?>
<script type="text/javascript">
    jQuery(document).ready(function() {
        var user_block_width = 100;
        jQuery('.wpc_pm_user_list .wpc_user').each(function() {
            if( jQuery(this).width() > user_block_width ) {
                user_block_width = jQuery(this).width();
            }
        });

        jQuery('.wpc_pm_user_list .wpc_user').width( user_block_width );

        jQuery('.wpc_pm_user_list .wpc_user').live( 'click', function() {
            var checkbox = jQuery(this).find('.wpc_pm_user_checkbox');
            checkbox.prop( "checked", !checkbox.is(":checked") );
            jQuery(this).toggleClass('checked');
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
 switch( $_GET['message'] ) { case '1': _e( 'Team added successfully.', WPC_CLIENT_TEXT_DOMAIN ); break; case '2': _e( 'Team updated successfully.', WPC_CLIENT_TEXT_DOMAIN ); break; case '3': _e( 'Team deleted successfully.', WPC_CLIENT_TEXT_DOMAIN ); break; } ?>
            </p>
        </div>
        <div class="wpc_clear"></div>
    <?php } else if( $action_msg ) { ?>
        <div id="message" class="error">
            <p>
                <?php echo $action_msg; ?>
            </p>
        </div>
        <div class="wpc_clear"></div>
    <?php } ?>
    <?php echo $this->gen_tabs_menu(); ?>
    <div class="wpc_clear"></div>
    <div class="wpc_pm_main_block teams_page">
        <div class="wpc_right_column">
            <div class="col-wrap">
                <p>
                    <a href="<?php echo admin_url('admin.php?page=wpc_project_management&tab=teams&action=add'); ?>" class="add-new-h2"><?php _e( 'Add New Team', WPC_CLIENT_TEXT_DOMAIN ); ?></a>
                </p>
                <p><?php _e( "Click 'Add New Team' button above to create a new team and assign members.<br />
                To add/remove members from an existing Team, simply click on the Team below and use the pane on the right to select/deselect members", WPC_CLIENT_TEXT_DOMAIN ); ?></p>
                <ul class="teams_list">
                    <?php foreach( $teams_list as $val ) { ?>
                        <li>
                            <a href="admin.php?page=wpc_project_management&tab=teams&action=edit&id=<?php echo $val['id']; ?>" class="team <?php echo ( isset( $_GET['id'] ) && $_GET['id'] == $val['id'] ) ? 'active' : ''; ?>"><?php echo $val['title']; ?></a>
                            <a href="admin.php?page=wpc_project_management&tab=teams&action=delete&id=<?php echo $val['id']; ?>" class="close_button" onclick="return confirm('<?php _e('Are you sure you want to delete this team?', WPC_CLIENT_TEXT_DOMAIN); ?>');"></a>
                        </li>
                    <?php } ?>
                </ul>
            </div>
        </div>
        <div class="wpc_left_column" <?php echo ( isset( $_GET['action'] ) ? '' : 'style="display: none;"' ); ?>>
            <div class="col-wrap">
                <form class="team_data" method="POST">
                    <p>
                        <label>
                            <?php _e( 'Team Name', WPC_CLIENT_TEXT_DOMAIN ); ?>:<br />
                            <input type="text" name="name" id="team_name" value="<?php echo( isset( $name ) ? esc_attr( $name ) : '' ); ?>" />
                        </label>
                    </p>
                    <p>
                        <label>
                            <?php _e( 'Team Description', WPC_CLIENT_TEXT_DOMAIN ); ?>:<br />
                            <textarea name="description" id="description" rows="5" cols="30"><?php echo( isset( $description ) ? $description : '' ); ?></textarea>
                        </label>
                    </p>
                    <p align="right">
                        <input type="submit" class="button-primary" value="<?php _e( 'Submit', WPC_CLIENT_TEXT_DOMAIN ); ?>" />
                    </p>

                    <div class="wpc_pm_user_list">
                        <?php foreach( $users as $val ) { ?>
                            <div class="wpc_user <?php echo implode( ' ', $val->roles );?> <?php echo( ( isset( $users_in_team ) && is_array( $users_in_team ) && in_array( $val->ID, $users_in_team ) ) ? 'checked' : '' ); ?>">
                                <div class="checkbox"></div>
                                <div class="avatar">
                                    <?php echo $wpc_client->cc_user_avatar( $val->ID ); ?>
                                </div>
                                <br class="wpc_clear" />
                                <?php echo ( !empty( $val->data->display_name ) ? $val->data->display_name : $val->data->user_login ); ?>
                                <input type="checkbox" name="wpc_pm_user[]" class="wpc_pm_user_checkbox" value="<?php echo $val->ID; ?>" <?php checked( ( isset( $users_in_team ) && is_array( $users_in_team ) && in_array( $val->ID, $users_in_team ) ) ? true : false ); ?> />
                            </div>
                        <?php } ?>
                    </div>
                    <div class="wpc_team_legend">
                        <div class="wpc_user wpc_project_manager"></div><div class="legend_label"><?php echo $wpc_client->custom_titles['project_manager']['s']; ?></div>
                        <div class="wpc_user wpc_teammate"></div><div class="legend_label"><?php echo $wpc_client->custom_titles['teammate']['s']; ?></div>
                        <div class="wpc_user wpc_freelancer"></div><div class="legend_label"><?php echo $wpc_client->custom_titles['freelancer']['s']; ?></div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>