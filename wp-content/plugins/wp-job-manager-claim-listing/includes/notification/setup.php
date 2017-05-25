<?php
/**
 * Setup Email Notification: Sending Email.
 * @since 3.0.0
**/
namespace wpjmcl\notification;
use wpjmcl\claim\Functions as Claim;
if ( ! defined( 'WPINC' ) ) { die; }

/* Load Class */
Setup::get_instance();

/**
 * Setup Class
 */
final class Setup{

	/**
	 * Construct
	 */
	public function __construct(){

		/* Send Email Notification On New Claim */
		add_action( 'wpjmcl_create_new_claim', array( $this, 'mail_claimer_new_claim' ), 10, 2 );
		add_action( 'wpjmcl_create_new_claim', array( $this, 'mail_admin_new_claim' ), 10, 2 );

		/* Send Email Notification On Claim Status Update */
		add_action( 'wpjmcl_claim_status_updated', array( $this, 'mail_claimer_claim_status_updated' ), 10, 3 );
		add_action( 'wpjmcl_claim_status_updated', array( $this, 'mail_admin_claim_status_updated' ), 10, 3 );

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
	 * New Claim Notification
	 * @since 3.0.0
	 */
	function mail_claimer_new_claim( $claim_id, $context ){
		if( 'front' != $context ) return false; // only send on front end submission.

		/* Mail Args */
		$args = array(
			'to'          => '%claimer_email%',
			'subject'     => __( 'Your Claim Information', 'wp-job-manager-claim-listing' ),
			'message'     => __(
				"Hi %claimer_name%," . "\n" .
				"On %claim_date% you submitted a claim for a listing. Here's the details." . "\n\n" .
				"Listing URL: %listing_url%" . "\n" .
				"Claimed by: %claimer_name%" . "\n" .
				"Claim Status: %claim_status%" . "\n\n" .
				"You can also view your claim online: %claim_url%" . "\n\n" .
				"Thank you." . "\n"
			,'wp-job-manager-claim-listing' ),
		);
		$args = apply_filters( 'wpjmcl_notification_mail_claimer_new_claim_args', $args );
		if( !$args ) return false;

		/* Add data to message */
		$data = Claim::get_data( $claim_id );
		if( !$data ) return false;
		foreach( $data as $key => $val ){
			$args['to']      = str_replace( "%{$key}%", $val, $args['to'] );
			$args['subject'] = str_replace( "%{$key}%", $val, $args['subject'] );
			$args['message'] = str_replace( "%{$key}%", $val, $args['message'] );
		}

		/* Send Mail */
		Functions::send_mail( $args );
	}


	/**
	 * New Claim Notification
	 * @since 3.0.0
	 */
	function mail_admin_new_claim( $claim_id, $context ){
		if( 'front' != $context ) return false; // only send on front end submission.

		/* Mail Args */
		$args = array(
			'to'          => get_bloginfo( 'admin_email' ),
			'subject'     => __( '[WP Job Man] New Claim Submitted', 'wp-job-manager-claim-listing' ),
			'message'     => __(
				"Hi Admin," . "\n" .
				"New claim submitted, here's the details." . "\n\n" .
				"Listing URL: %listing_url%" . "\n" .
				"Claimed by: %claimer_name%" . "\n" .
				"Claim Status: %claim_status%" . "\n\n" .
				"Edit Claim: %claim_edit_url%" . "\n\n" .
				"Thank you." . "\n"
			,'wp-job-manager-claim-listing' ),
		);

		$args = apply_filters( 'wpjmcl_notification_mail_admin_new_claim_args', $args );
		if( !$args ) return false;

		/* Add data to message */
		$data = Claim::get_data( $claim_id );
		if( !$data ) return false;
		foreach( $data as $key => $val ){
			$args['to']      = str_replace( "%{$key}%", $val, $args['to'] );
			$args['subject'] = str_replace( "%{$key}%", $val, $args['subject'] );
			$args['message'] = str_replace( "%{$key}%", $val, $args['message'] );
		}

		/* Send Mail */
		Functions::send_mail( $args );
	}


	/**
	 * Mail Claimer that Claim Status Updated
	 * @since 3.0.0
	 */
	public function mail_claimer_claim_status_updated( $claim_id, $old_status, $request ){
		/* Check! */
		if( ! is_admin() ) return false;
		if( ! isset( $request['_send_notification'] ) ) return false;
		if( ! is_array( $request['_send_notification'] ) ) return false;
		if( ! in_array( 'claimer', $request['_send_notification'] ) ) return false;

		/* Mail Args */
		$args = array(
			'to'          => '%claimer_email%',
			'subject'     => __( 'Your Claim For "%listing_title%" is %claim_status%', 'wp-job-manager-claim-listing' ),
			'message'     => __(
				"Hi %claimer_name%," . "\n" .
				"On %claim_date% you submitted a claim for a listing. Your claim status is updated. Here's the details." . "\n\n" .
				"Listing URL: %listing_url%" . "\n" .
				"Claimed by: %claimer_name%" . "\n\n" .
				"Previous Claim Status: %claim_status_old%" . "\n" .
				"New Claim Status: %claim_status%" . "\n\n" .
				"Thank you." . "\n"
			,'wp-job-manager-claim-listing' ),
		);

		$args = apply_filters( 'wpjmcl_notification_mail_claimer_claim_status_updated_args', $args );
		if( !$args ) return false;

		/* Add data to message */
		$data = Claim::get_data( $claim_id );
		if( !$data ) return false;
		foreach( $data as $key => $val ){
			$args['to']      = str_replace( "%{$key}%", $val, $args['to'] );
			$args['subject'] = str_replace( "%{$key}%", $val, $args['subject'] );
			$args['message'] = str_replace( "%{$key}%", $val, $args['message'] );
		}
		/* Old Status Label */
		$statuses = Claim::claim_statuses();
		$claim_old_status = array_key_exists( $old_status, $statuses ) ? $statuses[$old_status] : $old_status;
		$args['message'] = str_replace( "%claim_status_old%", $claim_old_status, $args['message'] );

		/* Send Mail */
		Functions::send_mail( $args );
	}


	/**
	 * Mail Admin that Claim Status Updated
	 * @since 3.0.0
	 */
	public function mail_admin_claim_status_updated( $claim_id, $old_status, $request ){
		/* Check! */
		if( ! is_admin() ) return false;
		if( ! isset( $request['_send_notification'] ) ) return false;
		if( ! is_array( $request['_send_notification'] ) ) return false;
		if( ! in_array( 'admin', $request['_send_notification'] ) ) return false;


		/* Mail Args */
		$args = array(
			'to'          => get_option( 'admin_email' ),
			'subject'     => __( '[WP Job Man] Claim for %listing_title% is updated to %claim_status%', 'wp-job-manager-claim-listing' ),
			'message'     => __(
				"Hi Admin," . "\n" .
				"Claim status for listing %listing_title% is updated, here's the details." . "\n\n" .
				"Listing URL: %listing_url%" . "\n" .
				"Claimed by: %claimer_name%" . "\n\n" .
				"Previous Claim Status: %claim_status_old%" . "\n" .
				"New Claim Status: %claim_status%" . "\n\n" .
				"You can edit this claim: %claim_edit_url%" . "\n\n" .
				"Thank you." . "\n"
			,'wp-job-manager-claim-listing' ),
		);

		$args = apply_filters( 'wpjmcl_notification_mail_admin_claim_status_updated_args', $args );
		if( !$args ) return false;

		/* Add data to message */
		$data = Claim::get_data( $claim_id );
		if( !$data ) return false;
		foreach( $data as $key => $val ){
			$args['to']      = str_replace( "%{$key}%", $val, $args['to'] );
			$args['subject'] = str_replace( "%{$key}%", $val, $args['subject'] );
			$args['message'] = str_replace( "%{$key}%", $val, $args['message'] );
		}
		/* Old Status Label */
		$statuses = Claim::claim_statuses();
		$claim_old_status = array_key_exists( $old_status, $statuses ) ? $statuses[$old_status] : $old_status;
		$args['message'] = str_replace( "%claim_status_old%", $claim_old_status, $args['message'] );

		/* Send Mail */
		Functions::send_mail( $args );

	}

}

