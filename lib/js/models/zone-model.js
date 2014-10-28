var ETRS_ZoneManager = ETRS_ZoneManager || {};

(function ($){
	'use strict';

	ETRS_ZoneManager.Zone = Backbone.Model.extend({
		defaults: {
			ZoneID:  '',
			Country: '',
			State:   '',
			Zipcode: ''
		},
		idAttribute: 'ZoneID',
	});
	
}(jQuery));
