<?php
/**
 * Handle the plugin's behavior when Customify is present.
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add customer data to Style Manager cloud request data.
 *
 * @param array $request_data
 *
 * @return array
 */
function pixcare_add_customer_data_to_customify_cloud_request_data( $request_data ) {
	// Get the connected pixelgrade user id
	$connection_user = PixelgradeCare_Admin::get_theme_activation_user();
	if ( empty( $connection_user ) || empty( $connection_user->ID ) ) {
		return $request_data;
	}

	$user_id = get_user_meta( $connection_user->ID, 'pixcare_user_ID', true );
	if ( empty( $user_id ) ) {
		// not authenticated
		return $request_data;
	}

	if ( empty( $request_data['customer_data'] ) ) {
		$request_data['customer_data'] = array();
	}
	$request_data['customer_data']['id'] = absint( $user_id );

	return $request_data;
}
add_filter( 'customify_pixelgrade_cloud_request_data', 'pixcare_add_customer_data_to_customify_cloud_request_data', 10, 1 );

/**
 * Fill up the Style Manager cloud request data.
 *
 * @todo We should remove this at some point when people are past Customify 1.7.4 since it is no longer needed.
 *
 * @param array $request_data
 *
 * @return array
 */
function pixcare_fill_up_customify_cloud_request_data( $request_data ) {
	if ( method_exists('Customify_Style_Manager', 'get_active_theme_data') ) {
		if ( ! isset( $request_data['theme_data'] ) ) {
			$request_data['theme_data'] = Customify_Style_Manager::instance()->get_active_theme_data();
		}

		if ( ! isset( $request_data['site_data'] ) ) {
			$request_data['site_data'] = Customify_Style_Manager::instance()->get_site_data();
		}
	}

	return $request_data;
}
add_filter( 'customify_pixelgrade_cloud_request_data', 'pixcare_fill_up_customify_cloud_request_data', 10, 1 );

/**
 * Add site data to Style Manager cloud request data.
 *
 * @param array $site_data
 *
 * @return array
 */
function pixcare_add_site_data_to_customify_cloud_request_data( $site_data ) {
	if ( empty( $site_data['wp'] ) ) {
		$site_data['wp'] = array();
	}

//	$site_data['wp']['admin_email'] = get_bloginfo('admin_email');
//	if ( is_user_logged_in() ) {
//		$user = wp_get_current_user();
//		if ( ! empty( $user ) ) {
//			$site_data['wp']['user_email'] = $user->user_email;
//			$site_data['wp']['user_nicename'] = $user->user_nicename;
//		}
//	}

	$site_data['wp']['language'] = get_bloginfo('language');
	$site_data['wp']['rtl'] = is_rtl();

	return $site_data;
}
add_filter( 'customify_style_manager_get_site_data', 'pixcare_add_site_data_to_customify_cloud_request_data', 10, 1 );

function pixcare_add_cloud_stats_endpoint( $config ) {
	if ( empty( $config['cloud']['stats'] ) ) {
		$config['cloud']['stats'] = array(
			'method' => 'POST',
			'url' => PIXELGRADE_CLOUD__API_BASE . 'wp-json/pixcloud/v1/front/stats',
		);
	}

	return  $config;
}
add_filter( 'customify_style_manager_external_api_endpoints', 'pixcare_add_cloud_stats_endpoint', 10, 1 );

/**
 * Send Color Palettes data when updating if a custom color palette is in use (on Customizer settings save - Publish).
 *
 * @param bool $custom_palette
 */
function pixcare_send_cloud_stats( $custom_palette ) {
	if ( class_exists( 'Customify_Cloud_Api' ) && ! empty( Customify_Cloud_Api::$externalApiEndpoints['cloud']['stats'] ) ) {
		$cloud_api = new Customify_Cloud_Api();
		$cloud_api->send_stats();
		return;
	}

	// Keep this for a while as it works with the Customify pre-1.7.4.
	// @todo Remove this at some point when users are past Customify pre-1.7.4.
	if ( property_exists( 'Customify_Style_Manager', 'externalApiEndpoints' ) && ! empty( Customify_Style_Manager::$externalApiEndpoints['cloud']['stats'] ) ) {
		$request_data = apply_filters( 'customify_pixelgrade_cloud_request_data', array(
			'site_url' => home_url('/'),
			'theme_data' => Customify_Style_Manager::instance()->get_active_theme_data(),
			'site_data' => Customify_Style_Manager::instance()->get_site_data(),
		), Customify_Style_Manager::instance() );

		$request_args = array(
			'method' => Customify_Style_Manager::$externalApiEndpoints['cloud']['stats']['method'],
			'timeout'   => 5,
			'blocking'  => false,
			'body'      => $request_data,
			'sslverify' => false,
		);
		// Send the data
		wp_remote_request( Customify_Style_Manager::$externalApiEndpoints['cloud']['stats']['url'], $request_args );
	}
}
add_action( 'customify_style_manager_updated_custom_palette_in_use', 'pixcare_send_cloud_stats', 10, 1 );
