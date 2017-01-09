/*global jQuery */
var ETRS_ZoneManager = ETRS_ZoneManager || {};

jQuery(document).ready(function($) {
		
	$( '#table-rate-shipping-rates-table' ).on( 'click', '.edit-table-rate-zones', function( event ) {
		event.preventDefault();
		var rate_id = $( this ).data( 'rate-id' );
		new ETRS_ZoneManager.AddEditZonesView( { rate_id: rate_id } );
	});
});