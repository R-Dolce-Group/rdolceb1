<?php
/*
 * Plugin Name: Claim Listing for WP Job Manager
 * Plugin URI: https://astoundify.com/downloads/wp-job-manager-claim-listing/
 * Description: Allow listings to be "claimed" to indicate verified ownership. A fee can be charged using WooCommerce.
 * Version: 3.4.0
 * Author: Astoundify
 * Author URI: http://astoundify.com
 * License: GPLv3 or later
 * Text Domain: wp-job-manager-claim-listing
 * Domain Path: /languages
 */
if ( ! defined( 'WPINC' ) ) { die; }


/* Constants
------------------------------------------ */

define( 'WPJMCL_VERSION', '3.4.0' );
define( 'WPJMCL_PLUGIN', plugin_basename( __FILE__ ) );
define( 'WPJMCL_FILE', __FILE__ );

define( 'WPJMCL_PATH', trailingslashit( plugin_dir_path(__FILE__) ) );
define( 'WPJMCL_URI', trailingslashit( plugin_dir_url( __FILE__ ) ) );


/* PHP Version Check.
========================================== */
if ( version_compare( phpversion(), "5.3", ">=" ) ) {

	/* Setup
	------------------------------------------ */

	/* Plugin Init */
	add_action( 'plugins_loaded', 'wpjmcl_init' );


	/**
	 * Init Files
	 * @since 1.0.0
	 */
	function wpjmcl_init(){

		/* Load Text Domain */
		load_plugin_textdomain( 'wp-job-manager-claim-listing', false, dirname( WPJMCL_PLUGIN ) . '/languages/' );

		/* Load files only if WP Job Manager active */
		if( class_exists( 'WP_Job_Manager' ) ){

			/* Load Meta Box Library */
			require_once( WPJMCL_PATH . 'library/fx-meta-box/fx-meta-box.php' );

			/* Settings */
			require_once( WPJMCL_PATH . 'includes/settings/settings.php' );

			/* Claim */
			require_once( WPJMCL_PATH . 'includes/claim/claim.php' );

			/* Job Listing: WP Job Manager */
			require_once( WPJMCL_PATH . 'includes/job-listing/job-listing.php' );

			/* Submit Claim Form */
			require_once( WPJMCL_PATH . 'includes/submit-claim/submit-claim.php' );

			/* Email Notification */
			require_once( WPJMCL_PATH . 'includes/notification/notification.php' );

			/* WooCommerce Add Ons */
			if( class_exists( 'WooCommerce' ) ){

				/* WPJM WooCommerce Paid Listings */
				if( function_exists( 'wp_job_manager_wcpl_init' ) ){
					require_once( WPJMCL_PATH . 'includes/wpjm-wc-paid-listing/wpjm-wc-paid-listing.php' );
				}
				/* WPJM WooCommerce Advanced Paid Listings */
				elseif( function_exists( 'jwapl_init' ) ){
					require_once( WPJMCL_PATH . 'includes/wpjm-wc-advanced-paid-listing/wpjm-wc-advanced-paid-listing.php' );
				}

				/* WP Job Manager - Products */
				if( class_exists( 'WP_Job_Manager_Products' ) ){
					require_once( WPJMCL_PATH . 'includes/wpjm-products/wpjm-products.php' );
				}
			}
		}

		/* Not active, notify user */
		else{

			/* Add admin notice */
			add_action( 'admin_notices', 'wpjmcl_wp_job_manager_inactive_notice' );
		}

	}

	/**
	 * Notify user: WP Job Manager Not Active.
	 * @since 3.0.0
	 */
	function wpjmcl_wp_job_manager_inactive_notice(){
		?>
		<div class="notice notice-info is-dismissible">
			<p><?php printf( __( '%s requires %s plugin.', 'wp-job-manager-claim-listing' ), '<strong>' . __( 'WP Job Manager - Claim Listing', 'wp-job-manager-claim-listing' ) . '</strong>', '<a href="https://wpjobmanager.com/">' . __( 'WP Job Manager', 'wp-job-manager-claim-listing' ) . '</a>' ); ?></p>
		</div>
		<?php
	}

	/**
	* Updater
	*/
	function wpjmcl_updater(){
		require_once( WPJMCL_PATH . 'library/astoundify/plugin-updater/astoundify-pluginupdater.php' );
		$updater = new Astoundify_PluginUpdater( __FILE__ );

		// ensure custom setting can be used
		new Astoundify_PluginUpdater_Integration_WPJobManager( __FILE__ );
	}
	add_action( 'admin_init', 'wpjmcl_updater', 9 );

}
 

/* Using Old PHP Version.
========================================== */
else{

	/* Admin notice */
	add_action( 'admin_notices', 'fx_wpjmcl_old_php_version_admin_notice' );

	/**
	 * Add admin notice about minimum PHP Version.
	 * @since 1.0.0
	 */
	function fx_wpjmcl_old_php_version_admin_notice() {
		?>
		<div class="updated error">
			<p><?php printf( __( '%s requires at least PHP 5.3+ (You have version %s).', 'wp-job-manager-claim-listing' ), '<strong>' . __( 'WP Job Manager - Claim Listing', 'wp-job-manager-claim-listing' ) . '</strong>', phpversion() ); ?></p>
		</div>
		<?php
	}
}

