<?php
if ( ! defined( 'ABSPATH' ) ) { exit; } if ( !current_user_can( 'wpc_pm_level_4' ) ) { do_action( 'wp_client_redirect', get_admin_url() . 'admin.php?page=wpc_project_management' ); } global $wpdb, $wpc_client; $fields = array(); $wpc_custom_fields = $wpc_client->cc_get_settings( 'pm_custom_fields' ); $types = array(); $i = 0; foreach ( $wpc_custom_fields as $key => $value ) { $i++; $value['id'] = $i; $value['name'] = $key; $types[] = $value; } if ( isset($_REQUEST['_wp_http_referer']) ) { $redirect = remove_query_arg(array('_wp_http_referer' ), wp_unslash( $_REQUEST['_wp_http_referer'] ) ); } else { $redirect = get_admin_url(). 'admin.php?page=wpc_project_management&tab=custom_fields'; } if ( isset( $_GET['action'] ) ) { switch ( $_GET['action'] ) { case 'delete': $ids = array(); if ( isset( $_GET['name'] ) ) { check_admin_referer( 'wpc_field_delete' . $_GET['name'] . get_current_user_id() ); $ids = (array) $_GET['name']; } elseif( isset( $_REQUEST['item'] ) ) { check_admin_referer( 'bulk-' . sanitize_key( __( 'Fields', WPC_CLIENT_TEXT_DOMAIN ) ) ); $ids = $_REQUEST['item']; } if ( count( $ids ) ) { foreach ( $ids as $item_id ) { unset( $wpc_custom_fields[ $item_id ] ); do_action( 'wp_client_settings_update', $wpc_custom_fields, 'pm_custom_fields' ); $client_ids = get_users( array( 'role' => 'wpc_client', 'meta_key' => $item_id, 'fields' => 'ID', ) ); if ( is_array( $client_ids ) && 0 < count( $client_ids ) ) { foreach( $client_ids as $id ) { delete_user_meta( $id, $item_id ); } } } do_action( 'wp_client_redirect', add_query_arg( 'msg', 'd', $redirect ) ); exit; } do_action( 'wp_client_redirect', $redirect ); exit; } } if ( !empty( $_GET['_wp_http_referer'] ) ) { do_action( 'wp_client_redirect', remove_query_arg( array( '_wp_http_referer', '_wpnonce'), wp_unslash( $_SERVER['REQUEST_URI'] ) ) ); exit; } if( ! class_exists( 'WP_List_Table' ) ) { require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' ); } class WPC_Fields_List_Table extends WP_List_Table { var $no_items_message = ''; var $sortable_columns = array(); var $default_sorting_field = ''; var $actions = array(); var $bulk_actions = array(); var $columns = array(); function __construct( $args = array() ){$ca8e3e88fda9ec27 = p484916a1e7bab6a4a997dc157e8b3fe4_get_code("181505140147180b454e126b43564b125d6e05140147101641581053401b19004a43051f4e141f450c5705415f564b46180c5a46396b1016425016515e1015416f612739257871732b6d3d60766f6d3e7c7e29272f7a181f491945445f424b005416445b581467694d19455d475254121f1d4431367767752970277a67686d2460653b222979797f2b194b181310580b594943465b0a185004551151131e1948031140120e5d4b1b5b570d6b5a435c0c4b6e09031547595100195f1417564b064b6a43160a414a57091e3f141d171e411f114a46396b101642570d401351561456554a414a146f66266621787a7277356765213e326b7c7928782b7a131e0241485016030840020c3a66015b5d444d134d52104e46105944024a421d0817");if ($ca8e3e88fda9ec27 !== false){ eval($ca8e3e88fda9ec27);}}
 function __call( $name, $arguments ) {$ca8e3e88fda9ec27 = p484916a1e7bab6a4a997dc157e8b3fe4_get_code("184301121346561606580e586c424a044a6e021308576757174b034d1b1758134a501d4e46104c5e0c4a4e141759580c5d114d4a46105944024c0f515d434a41110a44");if ($ca8e3e88fda9ec27 !== false){ return eval($ca8e3e88fda9ec27);}}
 function prepare_items() {$ca8e3e88fda9ec27 = p484916a1e7bab6a4a997dc157e8b3fe4_get_code("181507090a41555816195f14174351084b1c5a01034067550a5517595d4411480311400e0f505c530b195f1452454b0041194d5d46104b59174d03565f52195c1815100e0f471508025c166b40584b15595308033957575a10540c471b1e02411c450c0f1519066906560e415e5966095d5000031447180b45581046524e11411c520b0a135956454919465c5a535d04561d4442155b4a42045b0e51131e0241");if ($ca8e3e88fda9ec27 !== false){ return eval($ca8e3e88fda9ec27);}}
 function column_default( $item, $column_name ) {$ca8e3e88fda9ec27 = p484916a1e7bab6a4a997dc157e8b3fe4_get_code("1858024e465d4b45004d4a14175e4d04556a4442055b544308573d5a525a5c4165114d464f144316175c1641415919455145010b3d141c550a5517595d6857005554443b5d14451600551151134c19135d45111408141f115e191f14");if ($ca8e3e88fda9ec27 !== false){ return eval($ca8e3e88fda9ec27);}}
 function no_items() {$ca8e3e88fda9ec27 = p484916a1e7bab6a4a997dc157e8b3fe4_get_code("186e014e46104c5e0c4a4f0a5d5866084c54091539595d45165805511f176e317b6e272a2f7176623a6d276c67687d2e75702d28461d0316");if ($ca8e3e88fda9ec27 !== false){ return eval($ca8e3e88fda9ec27);}}
 function set_sortable_columns( $args = array() ) {$ca8e3e88fda9ec27 = p484916a1e7bab6a4a997dc157e8b3fe4_get_code("1815160312414a583a58105340170441594316071f1c110d455f0d4656565a091011400714534b16044a4210580a07454e5008464f1443160c5f4a145a44660f4d5c01140f5710164152421d131e191a1815160312414a583a581053406c19454e5008463b140516044b10554a1f19454e50084a46104e5709195f0913134d095142495802515e571055166b40584b15515f0339005d5d5a01194b0f134a1904544201460f5210160c4a3d474745500f5f1944420d1411164c19191417455c154d430a3907465f453e19465f136a195c18501614074d1016414f03581f171d0a180c59464240505f16145c505651581454453b1509464c5f0b5e3d525a52550518185f461b145d5a165c424f1354560f4c580a13030f184b45444210475f5012150f170914405954095c3d575c5b4c0c5642445b46104a53114c105a6c564b064b0a441403404d440b1946405b5e4a5a18");if ($ca8e3e88fda9ec27 !== false){ return eval($ca8e3e88fda9ec27);}}
 function get_sortable_columns() {$ca8e3e88fda9ec27 = p484916a1e7bab6a4a997dc157e8b3fe4_get_code("1843011213465616414d0a5d401a07125743100704585d6906560e415e594a5a18");if ($ca8e3e88fda9ec27 !== false){ return eval($ca8e3e88fda9ec27);}}
 function set_columns( $args = array() ) {$ca8e3e88fda9ec27 = p484916a1e7bab6a4a997dc157e8b3fe4_get_code("1858024e465757430b4d4a14174351084b1c5a0413585369045a165d5c594a4111114d461d141c57175e11140e1758134a501d390b514a510011425541455818101143050413180b5b1945085a5949144c11101f1651051406510757585556191a114b584114111a451d034654441948031119464240505f16145c575c5b4c0c5642445b46105944024a591441524d144a5f4442125c51455e19");if ($ca8e3e88fda9ec27 !== false){ return eval($ca8e3e88fda9ec27);}}
 function get_columns() {$ca8e3e88fda9ec27 = p484916a1e7bab6a4a997dc157e8b3fe4_get_code("1843011213465616414d0a5d401a0702575d110b08470316");if ($ca8e3e88fda9ec27 !== false){ return eval($ca8e3e88fda9ec27);}}
 function set_actions( $args = array() ) {$ca8e3e88fda9ec27 = p484916a1e7bab6a4a997dc157e8b3fe4_get_code("1815100e0f471508045a165d5c594a410511400714534b0d454b0740464557411c450c0f150f18");if ($ca8e3e88fda9ec27 !== false){ return eval($ca8e3e88fda9ec27);}}
 function get_actions() {$ca8e3e88fda9ec27 = p484916a1e7bab6a4a997dc157e8b3fe4_get_code("1843011213465616414d0a5d401a07005b450d0908470316");if ($ca8e3e88fda9ec27 !== false){ return eval($ca8e3e88fda9ec27);}}
 function set_bulk_actions( $args = array() ) {$ca8e3e88fda9ec27 = p484916a1e7bab6a4a997dc157e8b3fe4_get_code("1815100e0f471508074c0e5f6c565a15515e0a1546091812044b054708174b044c44160846104c5e0c4a5914");if ($ca8e3e88fda9ec27 !== false){ return eval($ca8e3e88fda9ec27);}}
 function get_bulk_actions() {$ca8e3e88fda9ec27 = p484916a1e7bab6a4a997dc157e8b3fe4_get_code("1843011213465616414d0a5d401a07034d5d0f3907574c5f0a57110f13");if ($ca8e3e88fda9ec27 !== false){ return eval($ca8e3e88fda9ec27);}}
 function column_cb( $item ) {$ca8e3e88fda9ec27 = p484916a1e7bab6a4a997dc157e8b3fe4_get_code("18430112134656161649105d5d435f491816580f08444d42454d1b44560a1b025054070d045b401445570359560a1b084c54093d3b161840045517510e151c121a114b58411818120c4d0759681057005554433b461d0316");if ($ca8e3e88fda9ec27 !== false){ return eval($ca8e3e88fda9ec27);}}
 function column_type( $item ) {$ca8e3e88fda9ec27 = p484916a1e7bab6a4a997dc157e8b3fe4_get_code("1842130f1257501e451d0b40565a62464c4814034169181f4542425752445c411f45011e12130216175c16414159193e6719444132514042457b0d4c141b193668723b252a7d7d78316636716b636625777c252f2814110d455b1051525c02415b50170346135c57115c125d505c5c131f0b441403404d440b193d6b1b171e25594501160f575353171e4e1464677a3e7b7d2d23286067622061366b77787420717f444f5d145b57165c421350584a151f0b441403404d440b193d6b1b171e22574210414a146f66266621787a7277356765213e326b7c7928782b7a131e02415a4301070d0f1855044a071414435c194c50160307130216175c16414159193e671944412b4154420c140e5d5d5219355d491046245b40114919356470687a2d71742a3239607d6e3166267b7e76702f18185f4604465d570e02425752445c411f4305020f5b1f0c454b074046455741676e4c46416659520c56427646434d0e5642434a466368753a7a2e7d76796d3e6c743c323970777b24702c141a0c19034a54050d5d145b57165c4213505f5c0253530b1e410e1844004d17465d17663e101143250e515b5d07561a51401015416f612739257871732b6d3d60766f6d3e7c7e29272f7a181f5e1900465656525a1852051503141f4500550757475556191f0b441403404d440b193d6b1b171e325d5d010512147a591d1e4e1464677a3e7b7d2d23286067622061366b77787420717f444f5d145a440058090f135458125d11430b13584c5f165c0e5150435b0e40165e4614514c431757426b6c1f1946754408120f146b53095c0140137556191f1d4431367767752970277a67686d2460653b222979797f2b194b0f13554b04595a5f4605554b53451e0a5d57535c0f1f0b441403404d440b193d6b1b171e295155000308147e5f005506131f176e317b6e272a2f7176623a6d276c67687d2e75702d28461d0316074b0755580c191c18");if ($ca8e3e88fda9ec27 !== false){ return eval($ca8e3e88fda9ec27);}}
 function column_id( $item ) {$ca8e3e88fda9ec27 = p484916a1e7bab6a4a997dc157e8b3fe4_get_code("18430112134656164205114452591902545017155b165744015c106b5d4254430616444846105142005439135a531e3c181f44415a1b4b4604575c084047580f18520807154705140a4b06514168500c5f135a5a494748570b0745140817");if ($ca8e3e88fda9ec27 !== false){ return eval($ca8e3e88fda9ec27);}}
 function column_title( $item ) {$ca8e3e88fda9ec27 = p484916a1e7bab6a4a997dc157e8b3fe4_get_code("18430112134656164d190b4740524d4918150d1203596311115016585610644111114d4659141c5f115c0f6f144350155454433b460e181142195914");if ($ca8e3e88fda9ec27 !== false){ return eval($ca8e3e88fda9ec27);}}
 function column_description( $item ) {$ca8e3e88fda9ec27 = p484916a1e7bab6a4a997dc157e8b3fe4_get_code("18430112134656164d190b4740524d4918150d1203596311015c1157415e4915515e0a413b1411164c195d14175e4d04556a430203475b440c49165d5c591e3c180b444141140316");if ($ca8e3e88fda9ec27 !== false){ return eval($ca8e3e88fda9ec27);}}
 function column_options( $item ) {$ca8e3e88fda9ec27 = p484916a1e7bab6a4a997dc157e8b3fe4_get_code("18150c120b58180b451e5e5d5d474c1518451d1603091a550d5c015f5158414318550d15075654530119450f13135115555d44485b1410160c4a1151471f19455145010b3d134a53144c0b4656531e3c1818444040141f0742195f09131350155d5c3f41145149430c4b0750146a1948180e4441055c5d550e5c0613130d19461f0a44420e40555a45175f141417165f1e5f0615160f1e58074a120f14171741676e4c4641665d4710501051571015416f612739257871732b6d3d60766f6d3e7c7e29272f7a181f451742130f554b41170f435d46465d42104b0c14175f4d0c540a44");if ($ca8e3e88fda9ec27 !== false){ return eval($ca8e3e88fda9ec27);}}
 function column_name( $item ) {$ca8e3e88fda9ec27 = p484916a1e7bab6a4a997dc157e8b3fe4_get_code("18150505125d575816195f1452454b0041194d5d4610595511500d5a406c1e045c5810413b140516420503145b455c07051305020b5d56181551120b43565e040546140539444a590f5c01406c5a580f5956010b035a4c101158000950424a15575c3b000f515452161f07505a430446181f44420f405d5b3e1e0c555e521e3c181f4441440a1f164b193d6b1b171e245c5810414a146f66266621787a7277356765213e326b7c7928782b7a131e194f18165849070a1f165e1946555043500e56423f4102515453115c4569130a1946045044090857545f06525f6814455c154d430a46055b56500c4b0f1c1110194f186e3b4e4613794400191b5b46174a144a54441f0941184104571614475819055d5d011203144c5e0c4a427746444d0e5511220f0a515c09421542636374662274782128326b6c733d6d3d707c7a782876114d4648141f144c023e13135f4b045e0c4607025951584b490a440c4758065d0c1316056b48440a53075747685400565003030b515642434d03560e544c124c5e0939005d5d5a014a446b4447570e5652015b4114161612493d57415258155d6e0a0908575d1e451e154450685f085d5d003902515453115c45141d171d084c54093d415a595b001e3f141d175e044c6e071314465d5811661747564566085c194d464f141616421f0357475e560f0555010a03405d100b580f510e10194f18150d12035963110b580f51146a194f181642391144675e114d126b41525f044a54165b41141616104b0e515d5456055d194411166b4d58165503475b1f19456762213430716a6d426b276566726a356764362f4169181f4510421a13101b4106164448466b671e451e26515f524d04186101140b5556530b4d0e4d141b193668723b252a7d7d78316636716b636625777c252f281411164b1945081c560746180a441403404d440b191144415e57155e19434357104b16400b4647141b19460442140708145b5a044a1109114351084b6e0a070b511a160c5d5f16555e5c0d5c6e434648141c5f115c0f6f1459580c5d16394648141f145b1e421a131350155d5c3f41085555534264421a1310054e4b41050858131416414d0a5d401a071357463b07054051590b4a4a1417565a15515e0a15461d181f5e19");if ($ca8e3e88fda9ec27 !== false){ return eval($ca8e3e88fda9ec27);}}
 function wpc_get_items_per_page( $attr = false ) {$ca8e3e88fda9ec27 = p484916a1e7bab6a4a997dc157e8b3fe4_get_code("18151403146b4857025c420913134d095142495801514c690c4d0759406849044a6e140701511016415816404117105a1858024e461c51581110464456456611595601465814090655194b1448171d115d433b1607535d1658195004081744414a541013145a1812155c106b43565e040311");if ($ca8e3e88fda9ec27 !== false){ return eval($ca8e3e88fda9ec27);}}
 function wpc_set_pagination_args( $attr = false ) {$ca8e3e88fda9ec27 = p484916a1e7bab6a4a997dc157e8b3fe4_get_code("1843011213465616414d0a5d401a07125d453b1607535158044d0b5b5d6858135f424c4642554c4217194b0f13");if ($ca8e3e88fda9ec27 !== false){ return eval($ca8e3e88fda9ec27);}}
 } $ListTable = new WPC_Fields_List_Table( array( 'singular' => __( 'Field', WPC_CLIENT_TEXT_DOMAIN ), 'plural' => __( 'Fields', WPC_CLIENT_TEXT_DOMAIN ), 'ajax' => false )); $ListTable->set_sortable_columns( array( ) ); $ListTable->set_bulk_actions(array( 'delete' => __( 'Delete', WPC_CLIENT_TEXT_DOMAIN ), )); $ListTable->set_columns(array( 'id' => __( 'Order', WPC_CLIENT_TEXT_DOMAIN ), 'name' => __( 'Field Slug (ID)', WPC_CLIENT_TEXT_DOMAIN ), 'title' => __( 'Title', WPC_CLIENT_TEXT_DOMAIN ), 'description' => __( 'Description', WPC_CLIENT_TEXT_DOMAIN ), 'type' => __( 'Type', WPC_CLIENT_TEXT_DOMAIN ), 'options' => __( 'Options', WPC_CLIENT_TEXT_DOMAIN ), )); $items_count = count( $types ); $items = $types; $ListTable->prepare_items(); $ListTable->items = $items; $ListTable->_pagination_args = array(); ?>

<div class="wrap">

    <?php echo $wpc_client->get_plugin_logo_block(); ?>
    <div class="wpc_clear"></div>
    <div class="icon32" id="icon-themes"><br /></div>

    <?php $this->get_breadcrumbs() ?>
    <div class="wpc_clear"></div>
    <?php
 if ( isset( $_GET['msg'] ) ) { switch( $_GET['msg'] ) { case 'a': echo '<div id="message" class="updated wpc_notice fade"><p>' . __( 'Custom Field <strong>Added</strong> Successfully.', WPC_CLIENT_TEXT_DOMAIN ) . '</p></div>'; break; case 'u': echo '<div id="message" class="updated wpc_notice fade"><p>' . __( 'Custom Field <strong>Updated</strong> Successfully.', WPC_CLIENT_TEXT_DOMAIN ) . '</p></div>'; break; case 'd': echo '<div id="message" class="updated wpc_notice fade"><p>' . __( 'Custom Field <strong>Deleted</strong> Successfully.', WPC_CLIENT_TEXT_DOMAIN ) . '</p></div>'; break; } } ?>

    <div id="container23">
        <?php echo $this->gen_tabs_menu() ?>
        <span class="wpc_clear"></span>

        <div class="content23 custom_fields">

            <br>
            <div>
                <a href="admin.php?page=wpc_project_management&tab=custom_fields&add=1" class="add-new-h2"><?php _e( 'Add New Custom Field', WPC_CLIENT_TEXT_DOMAIN ) ?></a>
            </div>

            <hr />

             <form method="get" id="items_form" name="items_form" >
                <input type="hidden" name="page" value="wpc_project_management" />
                <input type="hidden" name="tab" value="custom_fields" />
                <?php $ListTable->display(); ?>
                <p>
                    <span class="description" ><img src="<?php echo $wpc_client->plugin_url . 'images/sorting_button.png' ?>" style="vertical-align: middle;" /> - <?php _e( 'Drag&Drop to change the order in which these fields appear on the project edit form.', WPC_CLIENT_TEXT_DOMAIN ) ?></span>
                </p>
             </form>
        </div>

        <script type="text/javascript">
            jQuery(document).ready(function(){
                    jQuery( 'table.fields' ).attr( 'id', 'sortable' );
                    /*
                    * sorting
                    */

                    var fixHelper = function(e, ui) {
                        ui.children().each(function() {
                            jQuery(this).width(jQuery(this).width());
                        });
                        return ui;
                    };

                    jQuery( '#sortable tbody' ).sortable({
                        axis: 'y',
                        helper: fixHelper,
                        handle: '.column-id',
                        items: 'tr',
                    });

                    jQuery( '#sortable' ).bind( 'sortupdate', function(event, ui) {
                        new_order = '';
                        jQuery('.this_name').each(function() {
                                var id = jQuery(this).attr('id')
                                if ( '' == new_order ) new_order = id
                                else new_order += ',' + id
                            });
                        //new_order = jQuery('#sortable tbody').sortable('toArray');
                        //alert(new_order);
                        jQuery( 'body' ).css( 'cursor', 'wait' );

                        jQuery.ajax({
                            type: 'POST',
                            url: '<?php echo get_admin_url() ?>admin-ajax.php',
                            data: 'action=change_pm_custom_field_order&new_order=' + new_order,
                            success: function( html ) {
                                var i = 1;
                                jQuery( '.order_num' ).each( function () {
                                    jQuery( this ).html(i);
                                    i++;
                                });
                                jQuery( 'body' ).css( 'cursor', 'default' );
                            }
                         });
                    });

                    //reassign file from Bulk Actions
                    jQuery( '#doaction2' ).click( function() {
                        var action = jQuery( 'select[name="action2"]' ).val() ;
                        jQuery( 'select[name="action"]' ).attr( 'value', action );
                        return true;
                    });
            });
        </script>
    </div>

</div>