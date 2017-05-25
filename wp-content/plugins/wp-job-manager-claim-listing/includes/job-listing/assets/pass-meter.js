jQuery( document ).ready( function($) {

	/* Password Field */
	var pass_field = $( '#account_password' );
	var pass_meter = $( '#password-strength' );

	/* Use password strength to check on key up. */
	pass_field.on( 'keyup', pass_field, function( event ) { 
		pass_meter.show();
		check_pass_strength( pass_field, pass_meter );
	} );

	/**
	 * Check Password Strength and display it
	 */
	function check_pass_strength( $field, $status ) {
		var pw = $field.val();
		var strength = wp.passwordStrength.meter( pw, '', pw );

		/* Results */
		switch ( strength ) {

			case 2:
				$status.removeClass().addClass( 'bad' ).html( pwsL10n.bad );
			break;

			case 3:
				$status.removeClass().addClass( 'good' ).html( pwsL10n.good );
			break;

			case 4:
				$status.removeClass().addClass( 'strong' ).html( pwsL10n.strong );
			break;

			case 5:
				$status.removeClass().addClass( 'bad' ).html( pwsL10n.mismatch );
			break;

			default:
				$status.removeClass().addClass( 'bad' ).html( pwsL10n.short );

		}
	}

});