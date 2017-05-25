<?php
	$step = $_GET['step'];
  	$hash = $_GET['hash'];
	if ( '2' == $step ) {
		$export  = get_transient( wc_subs_exporter_get_export_transient_name( $hash ) );
		$message = sprintf( __( 'Found %s order items that contain subscriptions, processing in steps.', 'wc-subs-exporter' ), sizeof( $export->order_item_ids ) );
		$processed_step = 'found_order_item_ids';
	} elseif ( '3' == $step ) {
		$export  =  get_transient( wc_subs_exporter_get_subscriptions_transient_name( $hash ) );
		$message = sprintf( __( 'Found %s subscriptions, this is the last step.', 'wc-subs-exporter' ), sizeof( $export->subscriptions ) );
		$processed_step = 'found_subscription_keys';
	} else {
		$message = __( 'Error: Unknown step.', 'wc-subs-exporter' );
	}
	
?>
<p><?php printf( __( 'Doing export step %s.', 'wc-subs-exporter' ), $step ); ?></p>
<form method="post" action="<?php echo admin_url( 'admin.php?page=wc-subs-exporter' ) ?>" id="postform">
  <div id="poststuff">
    <div class="postbox" id="export-selection">
      <h3>
        <?php _e( 'Selection', 'wc-subs-exporter' ); ?>
      </h3>
      <div class="inside">
        <p class="description">
          <?php echo $message ?>
        </p>
        <p class="submit">
          <input type="submit" value="<?php _e( 'Proceed with export', 'wc-subs-exporter' ); ?>" class="button-primary" />
        </p>
      </div>
    </div>
    <!-- .postbox -->
  </div>
  <!-- #poststuff -->
  <input type="hidden" name="action" value="export" />
  <input type="hidden" name="processed_step" value="<?php echo $processed_step ?>" />
  <input type="hidden" name="hash" value="<?php echo $hash ?>" />
</form>
<?php
	if ( $wc_subs_exporter['debug'] ) {
		$output  = '<h3>' . __( 'Transient Log: ', 'wc-subs-exporter' ) . '</h3>';
		$output .= '<textarea id="sql_log">' . print_r( $export, true ) . '</textarea>';
		echo $output;
	}
?>