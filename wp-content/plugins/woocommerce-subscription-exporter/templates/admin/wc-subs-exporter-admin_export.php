<?php
	$subscription_statuses = wc_subs_exporter_get_subscription_statuses();

// initialize subscription status count
	foreach( $subscription_statuses as $key => $status ) {
		$subscription_count[ $key ] = WC_Subscriptions::get_subscription_count( array( 'subscription_status' => $key ) );
	}
	
?>
<p>
  <?php _e( 'Make a selection below to export entries. When you click the export button below, Subscription Exporter will create a CSV file for you to save to your computer.', 'wc-subs-exporter' ); ?>
</p>
<form method="post" action="<?php echo add_query_arg( array( 'failed' => null, 'empty' => null ) ); ?>" id="postform">
  <div id="poststuff">
    <div class="postbox" id="export-selection">
      <h3>
        <?php _e( 'Selection', 'wc-subs-exporter' ); ?>
      </h3>
      <div class="inside">
        <p class="description">
          <?php _e( 'Select the data you want to export.', 'wc-subs-exporter' ); ?>
        </p>
        <table class="form-table">
          <tr>
            <th><label for="subscriptions">
              <?php _e( 'Subscriptions', 'wc-subs-exporter' ); ?>
              </label></th>
            <td><span class="description">(<?php echo array_sum( $subscription_count ); ?>)</span> </td>
          </tr>
          <?php				
		foreach( $subscription_statuses as $status => $label ) { 
?>
          <tr>
            <td><label>
              <input type="checkbox" name="status[<?php echo $status ?>]" value="1"<?php if ( $subscription_count[$status] ) echo ' checked="checked"'; ?> />
              <?php echo $label ?> </label></td>
            <td><span class="description">(<?php echo $subscription_count[ $status ] ?>)</span> </td>
          </tr>
          <?php 
		}
?>
          <tr>
            <th><label for="date">
              <?php _e('Start date', 'wc-subs-exporter' ); ?>
              </label></th>
            <td><input id="date" style="width: 10em;display:inline-table" name="from_date" type="text" class="date" value="<?php echo $_POST['from_date'] ?>" />
              <span class="description">
              <?php _e( 'Select the starting date of subscription start.', 'wc-subs-exporter' ); ?>
              </span> </td>
          </tr>
          <tr>
            <th><label for="date2">
              <?php _e('End date', 'wc-subs-exporter' ); ?>
              </label></th>
            <td><input id="date2" style="width: 10em;display:inline-table" name="to_date" type="text" class="date" value="<?php echo $_POST['to_date'] ?>" />
              <span class="description">
              <?php _e( 'Select the end date of subscription start.', 'wc-subs-exporter' ); ?>
              </span> </td>
          </tr>
        </table>
        <p class="submit">
          <input type="submit" value="<?php _e( 'Export', 'wc-subs-exporter' ); ?>" class="button-primary" />
        </p>
      </div>
    </div>
    <!-- .postbox -->
  </div>
  <!-- #poststuff -->
  <input type="hidden" name="action" value="export" />
</form>
