<?php

/*
  Plugin Name: WordPress Users & WooCommerce Customers Import Export(BASIC)
  Plugin URI: https://wordpress.org/plugins/users-customers-import-export-for-wp-woocommerce/
  Description: Export and Import User/Customers details From and To your WordPress/WooCommerce.
  Author: WebToffee
  Author URI: https://www.webtoffee.com/product/wordpress-users-woocommerce-customers-import-export/
  Version: 1.1.3
  WC tested up to: 3.4.4
  Text Domain: wf_customer_import_export
  License: GPLv3
  License URI: https://www.gnu.org/licenses/gpl-3.0.html
 */



if (!defined('ABSPATH') || !is_admin()) {
    return;
}

/**
 * Function to check whether Premium version of User Import Export plugin is installed or not
 */
function wf_wordpress_user_import_export_premium_check(){
	if ( is_plugin_active('customer-import-export-for-woocommerce/customer-import-export.php') ){
		deactivate_plugins( basename( __FILE__ ) );
		wp_die(__("You already have the Premium version installed. For any issues, kindly contact our <a target='_blank' href='https://www.xadapter.com/online-support/'>support</a>.", "wf_customer_import_export"), "", array('back_link' => 1 ));
	}
}
register_activation_hook( __FILE__, 'wf_wordpress_user_import_export_premium_check' );


if( !defined('WF_CUSTOMER_IMP_EXP_ID') )
{
	define("WF_CUSTOMER_IMP_EXP_ID", "wf_customer_imp_exp");
}

if( !defined('HF_WORDPRESS_CUSTOMER_IM_EX') )
{
	define("HF_WORDPRESS_CUSTOMER_IM_EX", "hf_wordpress_customer_im_ex");
}

if (!class_exists('WF_Customer_Import_Export_CSV')) :

    /*
     * Main CSV Import class
     */

    class WF_Customer_Import_Export_CSV {

        /**
         * Constructor
         */
        public function __construct() {
	    if( !defined('WF_CustomerImpExpCsv_FILE') )
	    {
		define('WF_CustomerImpExpCsv_FILE', __FILE__);
	    }

            add_filter('woocommerce_screen_ids', array($this, 'woocommerce_screen_ids'));
            add_filter('plugin_action_links_' . plugin_basename(__FILE__), array($this, 'wf_plugin_action_links'));
            add_action('init', array($this, 'load_plugin_textdomain'));
            add_action('init', array($this, 'catch_export_request'), 20);
            add_action('init', array($this, 'catch_save_settings'), 20);
            add_action('admin_init', array($this, 'register_importers'));

            if (!get_option('UEIPF_Webtoffee_storefrog_admin_notices_dismissed')) {
                add_action('admin_notices', array($this, 'webtoffee_storefrog_admin_notices'));
                add_action('wp_ajax_UEIPF_webtoffee_storefrog_notice_dismiss', array($this, 'webtoffee_storefrog_notice_dismiss'));
            }

            include_once( 'includes/class-wf-customerimpexpcsv-admin-screen.php' );
            include_once( 'includes/importer/class-wf-customerimpexpcsv-importer.php' );

            if (defined('DOING_AJAX')) {
                include_once( 'includes/class-wf-customerimpexpcsv-ajax-handler.php' );
            }
        }

        public function wf_plugin_action_links($links) {
            $plugin_links = array(
                '<a href="' . admin_url('admin.php?page=hf_wordpress_customer_im_ex') . '">' . __('Import Export Users', 'wf_customer_import_export') . '</a>',
                '<a target="_blank" href="https://wordpress.org/support/plugin/users-customers-import-export-for-wp-woocommerce/">' . __('Support', 'wf_customer_import_export') . '</a>',
                '<a target="_blank" href="https://wordpress.org/support/plugin/users-customers-import-export-for-wp-woocommerce/reviews/">' . __('Review', 'wf_customer_import_export') . '</a>',
            );
            return array_merge($plugin_links, $links);
        }

        /**
         * Add screen ID
         */
        public function woocommerce_screen_ids($ids) {
            $ids[] = 'admin'; // For import screen
            return $ids;
        }

        /**
         * Handle localisation
         */
        public function load_plugin_textdomain() {
            load_plugin_textdomain('wf_customer_import_export', false, dirname(plugin_basename(__FILE__)) . '/lang/');
        }

        /**
         * Catches an export request and exports the data. This class is only loaded in admin.
         */
        public function catch_export_request() {
            if (!empty($_GET['action']) && !empty($_GET['page']) && $_GET['page'] == 'hf_wordpress_customer_im_ex') {
                switch ($_GET['action']) {
                    case "export" :
                        $user_ok = $this->hf_user_permission();
                        if ($user_ok) {
                            include_once( 'includes/exporter/class-wf-customerimpexpcsv-exporter.php' );
                            WF_CustomerImpExpCsv_Exporter::do_export();
                        } else {
                            wp_redirect(wp_login_url());
                        }
                        break;
                }
            }
        }

        public function catch_save_settings() {
            if (!empty($_GET['action']) && !empty($_GET['page']) && $_GET['page'] == 'hf_wordpress_customer_im_ex') {
                switch ($_GET['action']) {
                    case "settings" :
                        include_once( 'includes/settings/class-wf-customerimpexpcsv-settings.php' );
                        WF_CustomerImpExpCsv_Settings::save_settings();
                        break;
                }
            }
        }

        /**
         * Register importers for use
         */
        public function register_importers() {
            register_importer('wordpress_hf_user_csv', 'WordPress User/Customers (CSV)', __('Import <strong>users/customers</strong> to your site via a csv file.', 'wf_customer_import_export'), 'WF_CustomerImpExpCsv_Importer::customer_importer');
        }

        private function hf_user_permission() {
            // Check if user has rights to export
            $current_user = wp_get_current_user();
            $user_ok = false;
            $wf_roles = apply_filters('hf_user_permission_roles', array('administrator', 'shop_manager'));
            if ($current_user instanceof WP_User) {
                $can_users = array_intersect($wf_roles, $current_user->roles);
                if (!empty($can_users)) {
                    $user_ok = true;
                }
            }
            return $user_ok;
        }
        
        function webtoffee_storefrog_admin_notices() {

            if (apply_filters('webtoffee_storefrog_suppress_admin_notices', false)) {
                return;
            }
            $screen = get_current_screen();

            $allowed_screen_ids = array('users_page_hf_wordpress_customer_im_ex', 'admin');
            if (in_array($screen->id, $allowed_screen_ids)) {

                $notice = __('<h3>Save Time, Money & Hassle on Your WooCommerce Data Migration?</h3>', 'wf_customer_import_export');
                $notice .= __('<h3>Use StoreFrog Migration Services.</h3>', 'wf_customer_import_export');

                $content = '<style>.webtoffee-storefrog-nav-tab.updated {z-index:2; display: flex;align-items: center;margin: 18px 20px 10px 0;padding:23px;border-left-color: #2c85d7!important}.webtoffee-storefrog-nav-tab ul {margin: 0;}.webtoffee-storefrog-nav-tab h3 {margin-top: 0;margin-bottom: 9px;font-weight: 500;font-size: 16px;color: #2880d3;}.webtoffee-storefrog-nav-tab h3:last-child {margin-bottom: 0;}.webtoffee-storefrog-banner {flex-basis: 20%;padding: 0 15px;margin-left: auto;} .webtoffee-storefrog-banner a:focus{box-shadow: none;}</style>';
                $content .= '<div class="updated woocommerce-message webtoffee-storefrog-nav-tab notice is-dismissible"><ul>' . $notice . '</ul><div class="webtoffee-storefrog-banner"><a href="http://www.storefrog.com/" target="_blank"> <img src="' . plugins_url(basename(plugin_dir_path(WF_CustomerImpExpCsv_FILE))) . '/images/storefrog.png"/></a></div><div style="position: absolute;top: 0;right: 1px;z-index: 10000;" ><button type="button" id="webtoffee-storefrog-notice-dismiss" class="notice-dismiss"><span class="screen-reader-text">' . __('Dismiss this notice.', 'wf_order_import_export') . '</span></button></div></div>';
                echo $content;

                
                $user_js = "jQuery( '#webtoffee-storefrog-notice-dismiss' ).click( function() {
                                            jQuery.post( '" . admin_url("admin-ajax.php") . "', { action: 'UEIPF_webtoffee_storefrog_notice_dismiss' } );
                                            jQuery('.webtoffee-storefrog-nav-tab').fadeOut();
                                        });
                                    ";
                $js = "<!-- User Import JavaScript -->\n<script type=\"text/javascript\">\njQuery(function($) { $user_js });\n</script>\n";
                echo $js;
            }
        }

        function webtoffee_storefrog_notice_dismiss() {

            if (current_user_can('editor') || current_user_can('administrator')) {
                update_option('UEIPF_Webtoffee_storefrog_admin_notices_dismissed', 1);
                wp_die();
            }
            wp_die(-1);
        }

    }

    endif;

new WF_Customer_Import_Export_CSV();