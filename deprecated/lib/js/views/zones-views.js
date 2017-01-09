var ETRS_ZoneManager = ETRS_ZoneManager || {};

(function ($) {
	'use strict';
	
	ETRS_ZoneManager.AddEditZonesView = Backbone.View.extend({
	
		// Metabox container
		el : function() {
			return $( '#it-exchange-table-rate-shipping-zones-manager-wrapper' );
		},

		template: wp.template( 'it-exchange-table-rate-shipping-zones-manager-container' ),
		
		initialize : function( options ) {
			var self = this;
			options = options || {};
			ETRS_ZoneManager.zones.fetch({ rate_id: options.rate_id, reset: true }).done( function(){ self.render(); } );
			this.rate_id = options.rate_id;
		},

		/**
		 * Event Handlers
		*/
		events : {
			'click #add_new_etrs_zone'                      : 'addNewZone',
			'click .it-exchange-etrs-close-zones-manager a' : 'fadeOutZonesPopup',
			'click #cancel_etrs_zone'                       : 'fadeOutZonesPopup',
			'click #save_all_etrs_zone'                     : 'saveAllZones',
		},
		
		render : function() {
			// Empty container
			this.$el.empty();

			// Render
			var data = new Object();
			data.rate_id = this.rate_id;
			this.$el.html( this.template( data ) );
			
			this.$zones = $( '#table-rate-shipping-zones-table-zones' );
			this.$zones.empty();
			this.addAll();
			
			this.$el.fadeIn();

			return this;
		},
		
		addNewZone : function ( event ) {
			event.preventDefault();
	        var zone = new ETRS_ZoneManager.Zone();
	        ETRS_ZoneManager.zones.add( zone );
			this.addOne( zone );
		},
		
		addAll : function() {
			ETRS_ZoneManager.zones.each( this.addOne, this );
		},
		
		addOne : function( zone ) {
			var view = new ETRS_ZoneManager.ZoneView({ model: zone });
			this.$zones.append( view.render().el );
		},
		
		fadeOutZonesPopup : function ( event ) {
			event.preventDefault();
			this.$el.fadeOut();
		},
		
		saveAllZones : function ( event ) {
			event.preventDefault();
			var self = this;
			this.clearErrors( this.$el );
			var post_data = this.getFormData( this.$el.find('form#it-exchange-add-on-etrs-zone-manager-form') );
			this.saveZones(post_data).done( function( saved_zones_data ) {
				ETRS_ZoneManager.zones.fetch();
				$( '#table-rate-' + self.rate_id + ' .table-rate-zones' ).html( saved_zones_data.zone_output );
				var parent_data = self.getFormData( $( 'form#it-exchange-add-on-table-rate-shipping-settings' ) );
				self.saveRates(parent_data).done( function( saved_parent_data ) {
					self.$el.fadeOut();
				}).fail( function( errors ) {
					$( '#it-exchange-table-rate-shipping-zones-manager', self.$el ).scrollTop(0);
					self.displayErrors( self.$el, errors );
				});
			}).fail( function( errors ) {
				$( '#it-exchange-table-rate-shipping-zones-manager', self.$el ).scrollTop(0);
				self.displayErrors( self.$el, errors );
			});
		},
		
		//Auxiliar functions
		saveZones : function ( post_data ) {
			return wp.ajax.post( 'it-exchange-table-rate-shipping-save-zones', post_data );
		},
		
		saveRates : function ( post_data ) {
			return wp.ajax.post( 'it-exchange-table-rate-shipping-save-rates', post_data );
		},
		
		clearErrors : function ( self ) {
			$( '#it-exchange-table-rate-shipping-zones-manager-error-area', self ).empty();
		},
		
		getFormData : function ( form ) {
			var unindexed_array = form.serializeArray();
			var indexed_array = {};
			
			$.map(unindexed_array, function(n, i){
				indexed_array[n['name']] = n['value'];
			});
			
			return indexed_array;
		},
		
		displayErrors : function ( self, errors ) {
			var elements = $();
			elements = '<ul class="it-exchange-messages it-exchange-errors">';
			$.each( errors, function( index, value ) {
			    elements += '<li>'+value+'</li>';
			});
			elements += '</ul>' ;
			$( '#it-exchange-table-rate-shipping-zones-manager-error-area', self ).append( elements );
		},
		
	});
		
	ETRS_ZoneManager.ZoneView = Backbone.View.extend({

		tagName : 'div',

		className : 'ite-etrs-zone',

		template : wp.template( 'it-exchange-table-rate-shipping-list-zones-container' ),
		
		events : {
			'click .it-exchange-table-rate-shipping-addon-delete-zone' : 'deleteZone',
			'change #it-exchange-etrs-zone-country' : 'refreshState',
		},
		
		initialize : function() {},

		render : function () {
			var self = this;
			this.$el.empty();
			var data = this.model.toJSON();
			wp.ajax.post( 'it-exchange-table-rate-shipping-format-zone', data ).done( function( result ) {
				self.model.id = $( 'input.it-exchange-etrs-zone-id', result ).val();
				self.$el.html( result );
			}).fail( function( errors ) {
				console.log( errors );
			});
			return this;
		},
				
		deleteZone : function ( event ) {
			event.preventDefault();
			var self = this;
			var data = this.model.toJSON();
			if ( confirm( 'Are you sure you want to delete this zone?' ) ) {
				self.$el.empty();
			}
		},
		
		refreshState : function ( event ) {
			event.preventDefault;
			var self = this;
			var post_data = new Object();
		    post_data.Country = $( '#it-exchange-etrs-zone-country', this.$el ).val();
		    post_data.ZoneID = this.model.id;
			wp.ajax.post( 'it-exchange-table-rate-shipping-get-states', post_data ).done( function( result ) {
				$( '.it-exchange-etrs-zone-state-column', self.$el ).html( result );
			}).fail( function( errors ) {
				console.log( errors );
			});
		},

	});
		
})(jQuery);
