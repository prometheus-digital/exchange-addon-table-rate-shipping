var ETRS_ZoneManager = ETRS_ZoneManager || {};

(function ($) {
	'use strict';

	/**
	 * Variants Collection
	 * Does not include variant values
	*/
	ETRS_ZoneManager.Zones = Backbone.Collection.extend({
		model: ETRS_ZoneManager.Zone,
		//url: ite_aust_ajax.ajax_url,,
		
		/**
		 * Overrides Backbone.Collection.sync
		 *
		 * @param {String} method
		 * @param {Backbone.Model} model
		 * @param {Object} [options={}]
		 * @returns {Promise}
		 */
		sync: function( method, model, options ) {
			var args, fallback;

			// Overload the read method so ExistingZones.fetch() functions correctly.
			if ( 'read' === method ) {
				options = options || {};
				options.context = this;
				options.data = _.extend( options.data || {}, {
					action: 'it-exchange-table-rate-shipping-get-existing-zones',
					rate_id: options.rate_id
				});
				return wp.ajax.send( options );
				
			// Otherwise, fall back to `Backbone.sync()`.
			} else {
				/**
				 * Call `sync` directly on Backbone.Model
				 */
				return Backbone.Model.prototype.sync.apply( this, arguments );
			}

		}
	});
	
	ETRS_ZoneManager.zones = new ETRS_ZoneManager.Zones();
	
}(jQuery));