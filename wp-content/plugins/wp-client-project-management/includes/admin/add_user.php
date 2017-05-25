<?php
if ( ! defined( 'ABSPATH' ) ) { exit; } ?>
<script type="text/javascript">
    jQuery(document).ready(function() {

        jQuery.data( document.body, 'role', jQuery('.role').val() );
        jQuery('.role').change(function() {
            var selected = jQuery(this).val();

            <?php if( !$this->wpc_pm_m_enable ) { ?>
                if (selected == 'wpc_project_manager') {
                    jQuery(this).children('option[value=' + jQuery.data(document.body, 'role') + ']').attr('selected', true);
                    return false;
                }
            <?php } ?>

            <?php if( !$this->wpc_pm_t_enable ) { ?>
                if (selected == 'wpc_teammate') {
                    jQuery(this).children('option[value=' + jQuery.data(document.body, 'role') + ']').attr('selected', true);
                    return false;
                }
            <?php } ?>

            jQuery.data( document.body, 'role', jQuery(this).val() );
        });

    });
</script>
<div class='wrap'>
    <?php echo $wpc_client->get_plugin_logo_block(); ?>
    <div class="wpc_clear"></div>
    <div class="icon32" id="icon-themes"><br /></div>

    <?php $this->get_breadcrumbs() ?>

    <div class="wpc_clear"></div>
    <?php if( isset( $error ) && !empty( $error ) ) { ?>
        <div class="updated"><?php echo $error; ?></div>
        <div class="wpc_clear"></div>
    <?php } ?>
    <?php echo $this->gen_tabs_menu(); ?>
    <div class="wpc_clear"></div>

    <div class="wpc_pm_main_block">
        <h3><?php echo $button_text ?>:</h3>
        <form name="edit_manager" id="edit_manager" method="post" enctype="multipart/form-data">
            <table class="form-table">
                <tbody>
                    <tr>
                        <th scope="row">
                            <label for="user_login"><?php _e( 'Role', WPC_CLIENT_TEXT_DOMAIN ) ?> <span class="description"><?php _e( '(required)', WPC_CLIENT_TEXT_DOMAIN ) ?></span></label>
                        </th>
                        <td>
                            <select name="user_data[role]" class="role">
                                <option value="wpc_project_manager" <?php selected( isset( $user_data['role'] ) && ( ( is_string( $user_data['role'] ) && 'wpc_project_manager' == $user_data['role'] ) || ( is_array( $user_data['role'] ) && in_array( 'wpc_project_manager', $user_data['role'] ) ) ) ); ?>><?php echo $wpc_client->custom_titles['project_manager']['s']; ?></option>
                                <option value="wpc_teammate" <?php selected( isset( $user_data['role'] ) && ( ( is_string( $user_data['role'] ) && 'wpc_teammate' == $user_data['role'] ) || ( is_array( $user_data['role'] ) && in_array( 'wpc_teammate', $user_data['role'] ) ) ) ); ?>><?php echo $wpc_client->custom_titles['teammate']['s']; ?></option>
                                <option value="wpc_freelancer" <?php selected( isset( $user_data['role'] ) && ( ( is_string( $user_data['role'] ) && 'wpc_freelancer' == $user_data['role'] ) || ( is_array( $user_data['role'] ) && in_array( 'wpc_freelancer', $user_data['role'] ) ) ) ); ?>><?php echo $wpc_client->custom_titles['freelancer']['s']; ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="user_login"><?php _e( 'Username', WPC_CLIENT_TEXT_DOMAIN ) ?> <span class="description"><?php _e( '(required)', WPC_CLIENT_TEXT_DOMAIN ) ?></span></label>
                        </th>
                        <td>
                            <?php if ( 'add' == $_GET['action'] ): ?>
                                <input type="text" name="user_data[user_login]" id="user_login" value="<?php echo ( isset( $user_data['user_login'] ) ) ? $user_data['user_login'] : '' ?>" />
                            <?php else: ?>
                                <input type="text" disabled id="user_login" value="<?php echo ( isset( $user_data['user_login'] ) ) ? $user_data['user_login'] : '' ?>" />
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="email"><?php _e( 'E-mail', WPC_CLIENT_TEXT_DOMAIN ) ?> <span class="description"><?php _e( '(required)', WPC_CLIENT_TEXT_DOMAIN ) ?></span></label>
                        </th>
                        <td>
                            <input type="text" name="user_data[email]" id="email" value="<?php echo ( isset( $user_data['user_email'] ) ) ? $user_data['user_email'] : '' ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="first_name"><?php _e( 'First Name', WPC_CLIENT_TEXT_DOMAIN ) ?> </label>
                        </th>
                        <td>
                            <input type="text" name="user_data[first_name]" id="first_name" value="<?php echo ( isset( $user_data['first_name'] ) ) ? esc_attr( $user_data['first_name'] ) : '' ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="last_name"><?php _e( 'Last Name', WPC_CLIENT_TEXT_DOMAIN ) ?> </label>
                        </th>
                        <td>
                            <input type="text" name="user_data[last_name]" id="last_name" value="<?php echo ( isset( $user_data['last_name'] ) ) ? esc_attr( $user_data['last_name'] ) : '' ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="display_name"><?php _e( 'Display Name', WPC_CLIENT_TEXT_DOMAIN ) ?> </label>
                        </th>
                        <td>
                            <input type="text" name="user_data[display_name]" id="display_name" value="<?php echo ( isset( $user_data['display_name'] ) ) ? esc_attr( $user_data['display_name'] ) : '' ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label><?php _e( 'Avatar', WPC_CLIENT_TEXT_DOMAIN ) ?></label>
                        </th>
                        <td>
                            <?php echo $wpc_client->build_avatar_field('user_data[avatar]', isset( $user_data['avatar'] ) ? $user_data['avatar'] : '', isset( $_GET['id'] ) ? $_GET['id'] : false ); ?>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="pass1"><?php _e( 'Password', WPC_CLIENT_TEXT_DOMAIN ) ?> <span class="description"><?php _e( '(twice, required)', WPC_CLIENT_TEXT_DOMAIN ) ?></span></label>
                        </th>
                        <td>
                            <input type="password" name="user_data[pass1]" class="wpc_generate_password_field" autocomplete="off" id="pass1" value="" />
                            <input type="button" class="wpc_generate_password_button button" value="<?php _e( 'Generate Password', WPC_CLIENT_TEXT_DOMAIN ) ?>" />
                            <br>
                            <input type="password" name="user_data[pass2]" class="wpc_generate_password_field" autocomplete="off" id="pass2" value="" />
                        </td>
                    </tr>
                    <tr>
                        <th>&nbsp;</th>
                        <td>
                            <div id="pass-strength-result" style="display: block;"><?php _e( 'Strength indicator', WPC_CLIENT_TEXT_DOMAIN ) ?></div>
                            <div class="description indicator-hint" style="clear:both"><?php _e( 'Hint: The password should be at least seven characters long. To make it stronger, use upper and lower case letters, numbers and symbols like ! " ? $ % ^ & ).', WPC_CLIENT_TEXT_DOMAIN ) ?>
                        </td>
                    </tr>
                </tbody>
            </table>

            <p class="submit">
                    <input type="submit" value="<?php echo $button_text ?>" class="button-primary" id="update_user" name="update_user">
            </p>
        </form>
    </div>
</div>

<script type="text/javascript">
    function wpc_generate_password( pass1, pass2 ) {
        jQuery('.wpc_generate_password_button').click(function() {
            var obj = jQuery(this);
            obj.next('.wpc_show_password').remove();
            jQuery( pass1 ).val('');
            jQuery( pass2 ).val('');
            jQuery( pass1 ).trigger('keyup');
            obj.after(' <span class="wpc_ajax_loading"></span>');
            jQuery.ajax({
                type     : 'POST',
                dataType : 'json',
                url      : '<?php echo get_admin_url() ?>/admin-ajax.php',
                data     : 'action=wpc_generate_password',
                success: function( data ){
                    if( data.status ) {
                        obj.next('.wpc_ajax_loading').remove();
                        obj.after(' <span class="wpc_show_password">' + data.message + '</span>');
                        jQuery( pass1 ).val( data.message );
                        jQuery( pass2 ).val( data.message );
                        jQuery( pass1 ).trigger('keyup');
                    } else {
                        alert( data.message );
                    }
                }
            });
        });
    }

    jQuery( document ).ready( function( $ ) {
        wpc_generate_password( '#pass1', '#pass2' );

        $( '.indicator-hint' ).html( wpc_password_protect.hint_message );

        $( 'body' ).on( 'keyup', '#pass1, #pass2',
            function( event ) {
                checkPasswordStrength(
                    $('#pass1'),
                    $('#pass2'),
                    $('#pass-strength-result'),
                    $('#update_user'),
                    wpc_password_protect.blackList
                );
            }
        );
    });
</script>