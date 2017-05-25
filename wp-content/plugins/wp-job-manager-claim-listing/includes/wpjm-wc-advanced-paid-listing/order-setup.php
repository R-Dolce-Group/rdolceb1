<?php
/**
 * Extend WooCommerce + Paid Listing
 * This handle all Product Data Setup.
 * @since 3.0.0
 */
namespace wpjmcl\wpjm_wc_advanced_paid_listing;
use wpjmcl\claim\Functions as Claim;

if ( ! defined( 'WPINC' ) ) { die; }

/* Load Class */
Order_Setup::get_instance();

/**
 * Setup Class
 */
final class Order_Setup{

	/**
	 * Construct
	 */
	public function __construct(){

		/* Order Created On Checkout */
		add_action( 'woocommerce_checkout_order_processed', array( __CLASS__, 'order_created' ) );

		/* Two hook, but only process this once. Which ever first. */
		add_action( 'woocommerce_order_status_processing', array( $this, 'order_paid' ), 11 );
		add_action( 'woocommerce_order_status_completed', array( $this, 'order_paid' ), 11 );
	}

	/**
	 * Returns the instance.
	 * @since  1.0.0
	 */
	public static function get_instance(){
		static $instance = null;
		if ( is_null( $instance ) ) $instance = new self;
		return $instance;
	}

	/**
	 * Triggered when order created on checkout
	 * set claim status to "pending_order".
	 */
	function order_created( $order_id ){
		$order = wc_get_order( $order_id );

		/* Loop each item, and process. */
		foreach ( $order->get_items() as $item ) {
			$product_id = $item['product_id'];
			$product = wc_get_product( $product_id );

			if ( $product->is_type( array( 'job_package', 'job_package_subscription' ) ) && isset( $item['claim_id'] ) ) {

				$claim_id = $item['claim_id'];

				/* Add order and product info in claim */
				add_post_meta( $claim_id, '_order_id', $order_id );
				add_post_meta( $claim_id, '_package_id', $product_id );

				/* Update claim status and send notification. */
				$old_status = get_post_meta( $claim_id, '_status', true );
				$update = update_post_meta( $claim_id, '_status', 'pending_order' );
				if( $update ){
					do_action( 'wpjmcl_claim_status_updated', $claim_id, $old_status, array( '_order_id' => $order_id, '_package_id' => $product_id, 'context' => 'order_created' ) );
				}

			}
		}
	}

	/**
	 * Triggered when an order is paid
	 * @param  int $order_id
	 */
	public function order_paid( $order_id ) {
		// Get the order obj
		$order = wc_get_order( $order_id );

		/* Only do it once, if not processing/completed. */
		if ( get_post_meta( $order_id, 'wpjmcl_claim_packages_processed', true ) ) {
			return;
		}

		/* Loop each item, and process. */
		foreach ( $order->get_items() as $item ) {
			$product_id = $item['product_id'];
			$product = wc_get_product( $product_id );

			if ( $product->is_type( array( 'job_package', 'job_package_subscription' ) ) && $order->customer_user ) {

				if ( 'yes' !== get_post_meta( $product->get_id(), '_use_for_claims', true ) ) {
					continue;
				}

				/* Auto approve claim ? */
				$auto_approve = get_post_meta( $product_id, '_default_to_claimed', true );
				$new_status = 'yes' == $auto_approve ? 'approved' : 'order_completed';

				$claim_id = isset( $item['claim_id'] ) ? $item[ 'claim_id' ] : false;

				if ( $auto_approve && ! $claim_id ) {
					$claim_id = Claim::create_new_claim( $item[ 'job_id' ], $order->customer_user, __( 'Automatically verified with initial purchase.', 'wp-job-manager-claim-listing' ) );
				}

				$old_status = get_post_meta( $claim_id, '_status', true );
				$notify = $auto_approve ? array( 'admin', 'claimer' ) : array( 'admin' );

				/* Update claim status and send notification. */
				$update = update_post_meta( $claim_id, '_status', $new_status );
				if( $update ){
					do_action( 'wpjmcl_claim_status_updated', $claim_id, $old_status, array( '_send_notification' => $notify, 'context' => 'order_paid' ) );
				}

				/* Approve listing with package */
				if( isset( $item['job_id'] ) ){
					$job = get_post( $item['job_id'] );

					/* Get user package */
					global $wpdb;

					// look for the subscription ID for user packages if exists
					if ( class_exists( 'WC_Subscriptions' ) ) {
						if ( wcs_order_contains_subscription( $order ) ) {
							$subs = wcs_get_subscriptions_for_order( $order_id );

							if ( ! empty( $subs ) ) {
								$sub = current( $subs );
								$order_id = $sub->id;
							}
						}

					}

					$user_package = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wcpl_user_packages WHERE user_id = %d AND order_id = %d AND ( package_count < package_limit OR package_limit = 0 );", $order->customer_user, $order_id ) );

					// Increase the package usage (to be 1/1) and assign the user's package
					if ( $user_package ) {

						// apply the user package
						jwapl_increase_package_count( $order->customer_user, $user_package->id );
						update_post_meta( $job->ID, '_user_package_id', $user_package->id );

						if ( $product->is_type( 'job_package_subscription' ) ) {
							do_action( 'jwapl_switched_subscription', $job->ID, $user_package );
						}
					}
				}
			}
		}

		update_post_meta( $order_id, 'wpjmcl_claim_packages_processed', true );
	}

} // end class

