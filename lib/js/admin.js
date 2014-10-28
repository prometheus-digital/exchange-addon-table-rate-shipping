jQuery(document).ready(function($) {
	$( '#new-table-rate' ).on( 'click', function(event) {
		event.preventDefault();
		var parent = $( this ).parent();
		var data = {
			'action': 'it-exchange-table-rate-shipping-addon-add-new-table-rate',
		}
		$.post( ajaxurl, data, function( response ) {
			$( '#table-rate-shipping-table-rates' ).append( response );
		});
	});
	
	$( '#table-rate-shipping-table-rates' ).on( 'click', '.it-exchange-table-rate-shipping-addon-enabled-checkmark', function( event ) {
		event.preventDefault();
		if ( $( this ).hasClass( 'it-exchange-table-rate-shipping-addon-enabled-checkmark-checked' ) ) {
			$( this ).removeClass( 'it-exchange-table-rate-shipping-addon-enabled-checkmark-checked' );
			$( this ).addClass( 'it-exchange-table-rate-shipping-addon-enabled-checkmark-unchecked' );
			$( 'input.it-exchange-table-rate-shipping-addon-enabled-checkmark', this ).val( 'unchecked' );
		} else {
			$( this ).removeClass( 'it-exchange-table-rate-shipping-addon-enabled-checkmark-unchecked' );
			$( this ).addClass( 'it-exchange-table-rate-shipping-addon-enabled-checkmark-checked' );
			$( 'input.it-exchange-table-rate-shipping-addon-enabled-checkmark', this ).val( 'checked' );
		}
	});

	$( '#table-rate-shipping-table-rates' ).on( 'click', '.it-exchange-table-rate-shipping-addon-delete-table-rate', function(event) {
		event.preventDefault();
		$( this ).closest( '.item-row' ).remove();
	});
	
	// Format prices
	$( '#table-rate-shipping-table-rates' ).on( 'change', 'input.it-exchange-table-rate-shipping-addon-handling-fee, input.it-exchange-table-rate-shipping-addon-base-cost, input.it-exchange-table-rate-shipping-addon-item-cost', function() {
		console.log( 'here' );
		var self = this;
		var parent = $( this ).parent().parent();
		var data = {
			'action': 'it-exchange-table-rate-shipping-addon-format-pricing',
			'input': $( this ).val(),
		}
		$.post( ajaxurl, data, function( response ) {
			console.log( response );
			if ( '' != response ) {
				$( self, parent ).val( response );
			}
		});
	});
	$( '#table-rate-shipping-table-rates' ).on( 'change', 'input.it-exchange-table-rate-shipping-addon-min, input.it-exchange-table-rate-shipping-addon-max', function() {
		console.log( 'here' );
		var self = this;
		var parent = $( this ).parent().parent();
		if ( 'price' === $( 'select.it-exchange-table-rate-shipping-addon-condition option:selected', parent ).val() ) {
			var data = {
				'action': 'it-exchange-table-rate-shipping-addon-format-pricing',
				'input': $( this ).val(),
			}
			$.post( ajaxurl, data, function( response ) {
				console.log( response );
				if ( '' != response ) {
					$( self, parent ).val( response );
				}
			});
		}
	});
});