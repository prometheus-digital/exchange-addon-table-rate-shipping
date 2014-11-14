<?php
/**
 * iThemes Exchange Table Rate Shipping Add-on
 * @package exchange-addon-table-rate-shipping
 * @since 1.0.0
*/

/**
 * Output the row of settings for a specific table rate ID
 *
 * @since 1.0.0
 *
 * @param int $table_rate_id - Specific Table Rate ID
 * @return string - HTML formated Table Rate
*/
function it_exchange_table_rate_shipping_form_table_table_rate_settings( $table_rate_id = false ) {
	$general_settings = it_exchange_get_option( 'shipping-general' );
	$weight_unit = ( empty( $general_settings['measurements-format'] ) || 'standard' === $general_settings['measurements-format'] ) ? 'lbs' : 'kgs';
	$return = '';
		
	if ( false === $table_rate_id ) {
		//Table Rate doesn't exist yet, create a new one and grab the propert $table_rate_id.
		$post = array(
			'post_title'     => '',
	        'post_status'    => 'publish',
		    'ping_status'    => 'closed',
		    'comment_status' => 'closed',
			'post_type'      => 'ite_table_rate',
		);
		$table_rate_id = wp_insert_post( $post, true );
		
		if ( is_wp_error( $table_rate_id ) ) {
			return $table_rate_id->get_error_messages();
		}
	}
	
	$table_rate = it_exchange_table_rate_shipping_get_table_rate( $table_rate_id );
			
	$return .= '<div id="table-rate-' . $table_rate_id . '" class="table-rate">';
	$return .= '<div class="table-rate-values">';	
	$return .= '<div class="item-row block-row">'; //start block-row

	$return .= '<div class="item-column block-column block-column-1 enable-table-rate-column">';
	if ( 'default' === $table_rate['enabled'] ) {
		$return .= '<span class="it-exchange-table-rate-shipping-addon-enabled-checkmark it-exchange-table-rate-shipping-addon-enabled-checkmark-default"></span>';
	} else {
		$return .= '<span class="it-exchange-table-rate-shipping-addon-enabled-checkmark it-exchange-table-rate-shipping-addon-enabled-checkmark-' . $table_rate['enabled'] . '">';
		$return .= '<input type="hidden" class="it-exchange-table-rate-shipping-addon-enabled-checkmark it-exchange-table-rate-shipping-addon-enabled-checkmark-' . $table_rate['enabled'] . '" name="it-exchange-table-rate-shipping-addon-table-rate[' . $table_rate_id . '][enabled]" value="' . $table_rate['enabled'] . '" />';
		$return .= '</span>';
	}
	$return .= '</div>';
	
	$return .= '<div class="item-column block-column block-column-2">';
	$return .= '<input type="text" class="it-exchange-table-rate-shipping-addon-label" name="it-exchange-table-rate-shipping-addon-table-rate[' . $table_rate_id . '][label]" value="' . $table_rate['label'] . '" />';
	$return .= '</div>';
	
	$return .= '<div class="item-column block-column block-column-3">';
	$return .= '<select class="it-exchange-table-rate-shipping-addon-calculation-type" name="it-exchange-table-rate-shipping-addon-table-rate[' . $table_rate_id . '][calculation-type]">';
		$return .= '<option value="per_order" ' . selected( 'per_order', $table_rate['calculation-type'], false ) . '">' . __( 'Per Order', 'LION' ) . '</option>';
		$return .= '<option value="per_item" ' . selected( 'per_item', $table_rate['calculation-type'], false ) . '">' . __( 'Per Item', 'LION' ) . '</option>';
		$return .= '<option value="per_line" ' . selected( 'per_line', $table_rate['calculation-type'], false ) . '">' . __( 'Per Line', 'LION' ) . '</option>';
	$return .= '</select>';
	$return .= '</div>';
	
	$return .= '<div class="item-column block-column block-column-4">';
	if ( 'default' === $table_rate['enabled'] ) {
		$return .= '<span class="it-exchange-table-rate-shipping-addon-condition">' . __( 'N/A', 'LION' ) . '</span>';
	} else {
		$return .= '<select class="it-exchange-table-rate-shipping-addon-condition" name="it-exchange-table-rate-shipping-addon-table-rate[' . $table_rate_id . '][condition]">';
			$return .= '<option value="price" ' . selected( 'price', $table_rate['condition'], false ) . '">' . __( 'Price', 'LION' ) . '</option>';
			$return .= '<option value="weight" ' . selected( 'weight', $table_rate['condition'], false ) . '">' . sprintf( __( 'Weight (%s)', 'LION' ), $weight_unit ) . '</option>';
			$return .= '<option value="item_count" ' . selected( 'item_count', $table_rate['condition'], false ) . '">' . __( 'Item Count', 'LION' ) . '</option>';
			$return .= '<option value="product_count" ' . selected( 'product_count', $table_rate['condition'], false ) . '">' . __( 'Product Count', 'LION' ) . '</option>';
		$return .= '</select>';
	}
	$return .= '</div>';
	
	if ( 'price' === $table_rate['condition'] ) {
		if ( !empty( $table_rate['min'] ) ) {
			$min = html_entity_decode( it_exchange_format_price( it_exchange_convert_from_database_number( $table_rate['min'] ) ) );
		} else {
			$min = '';
		}
		if ( !empty( $table_rate['max'] ) ) {
			$max = html_entity_decode( it_exchange_format_price( it_exchange_convert_from_database_number( $table_rate['max'] ) ) );
		} else {
			$max = '';
		}
	} else {
		$min = $table_rate['min'];
		$max = $table_rate['max'];
	}
	
	$return .= '<div class="item-column block-column block-column-5">';
	if ( 'default' === $table_rate['enabled'] ) {
		$return .= '<span class="it-exchange-table-rate-shipping-addon-min">' . __( 'N/A', 'LION' ) . '</span>';
	} else {
		$return .= '<input type="text" class="it-exchange-table-rate-shipping-addon-min small-text" name="it-exchange-table-rate-shipping-addon-table-rate[' . $table_rate_id . '][min]" value="' . $min . '" />';
	}
	$return .= '</div>';

	$return .= '<div class="item-column block-column block-column-6">';
	if ( 'default' === $table_rate['enabled'] ) {
		$return .= '<span class="it-exchange-table-rate-shipping-addon-max">' . __( 'N/A', 'LION' ) . '</span>';
	} else {
		$return .= '<input type="text" class="it-exchange-table-rate-shipping-addon-max small-text" name="it-exchange-table-rate-shipping-addon-table-rate[' . $table_rate_id . '][max]" value="' . $max . '" />';
	}
	$return .= '</div>';
	
	$return .= '<div class="item-column block-column block-column-7">';
	$return .= '<input type="text" class="it-exchange-table-rate-shipping-addon-handling-fee small-text" name="it-exchange-table-rate-shipping-addon-table-rate[' . $table_rate_id . '][handling-fee]" value="' . html_entity_decode( it_exchange_format_price( it_exchange_convert_from_database_number( $table_rate['handling-fee'] ) ) ) . '" />';
	$return .= '</div>';
	
	$return .= '<div class="item-column block-column block-column-8">';
	$return .= '<input type="text" class="it-exchange-table-rate-shipping-addon-base-cost small-text" name="it-exchange-table-rate-shipping-addon-table-rate[' . $table_rate_id . '][base-cost]" value="' . html_entity_decode( it_exchange_format_price( it_exchange_convert_from_database_number( $table_rate['base-cost'] ) ) ) . '" />';
	$return .= '</div>';
	
	$return .= '<div class="item-column block-column block-column-9">';
	$return .= '<input type="text" class="it-exchange-table-rate-shipping-addon-item-cost small-text" name="it-exchange-table-rate-shipping-addon-table-rate[' . $table_rate_id . '][item-cost]" value="' . html_entity_decode( it_exchange_format_price( it_exchange_convert_from_database_number( $table_rate['item-cost'] ) ) ) . '" />';
	$return .= '</div>';
	
	$return .= '<div class="item-column block-column block-column-10">';
	if ( 'default' === $table_rate['enabled'] ) {
		$return .= __( 'Default (Everywhere)', 'LION' );
	} else {
		$return .= '<div class="table-rate-zones">';
		$return .= it_exchange_table_rate_shipping_prepare_zone_ouput( $table_rate['geo-restrictions'], $table_rate_id );
		$return .= '</div>';
		$return .= '<a class="edit-table-rate-zones" data-rate-id="' . $table_rate_id . '" href="#">Edit Zones</a>';
	}
	$return .= '</div>';

	$return .= '<div class="item-column block-column block-column-11">';
	if ( 'default' !== $table_rate['enabled'] ) {
		$return .= '<a href class="it-exchange-table-rate-shipping-addon-delete-table-rate it-exchange-remove-item">&times;</a>';
	}
	$return .= '</div>';
	$return .= '</div>';	
	$return .= '</div>';
	$return .= '</div>';
	
	return $return;
}

/**
 * Generated the zone HTML for a given zone set
 *
 * @since 1.0.0
 *
 * @param array $zones - Zone set
 * @return string - HTML formated zone row
*/
function it_exchange_table_rate_shipping_prepare_zone_ouput( $zones=false, $table_rate_id ) {
	$return = '';
	if ( !empty( $zones ) ) {
		$zone_count = count( $zones );
		foreach( $zones as $zone_id ) {
			$country = get_post_meta( $zone_id, '_it_exchange_etrs_country_zone', true );
			$countries = it_exchange_get_data_set( 'countries' );
			if( empty( $country ) || is_wp_error( $country ) || empty( $countries[$country] ) ){
				$country_str = __( 'All Countries', 'LION' );
			} else {
				$country_str = $countries[$country];
			}
			
			$state = get_post_meta( $zone_id, '_it_exchange_etrs_state_zone', true );
			$states = it_exchange_get_data_set( 'states', array( 'country' => $country ) );
			if ( empty( $state ) || is_wp_error( $state ) || empty( $states[$state] ) ){
				$state_str = __( 'All States', 'LION' );
			} else if ( 'US' === $country && 'USCONTIGUOUS' === $state ) {
				$state_str = __( 'Contiguous States', 'LION' );
			} else {
				$state_str = $states[$state];
			}
			
			$zipcodes = get_post_meta( $zone_id, '_it_exchange_etrs_zipcode_zone', true );
			if( !empty( $zipcodes ) && !is_wp_error( $zipcodes ) ){
				$zipcode = key( (array)$zipcodes );
			} else {
				$zipcode = __( 'All Postal Codes', 'LION' );
			}
		
			$data[] = $country_str . ', ' . $state_str . ', ' . $zipcode;
		}
		$data[] = "<a class='edit-table-rate-zones' data-rate-id='" . $table_rate_id . "' href='#'>Edit Zones</a>";
		$return .= '<span data-tip-content="' . join( '<br />', $data ) . '" class="it-exchange-tip">' . $zone_count . '</span>';
	} else {
		$return .= '<span class="it-exchange-tip">0</span>';
	}
	return $return;
}

/**
 * Gets table rate settings for specific table rate ID
 *
 * @since 1.0.0
 *
 * @param int $table_rate_id - Specific Table Rate ID
 * @return array - Table Rate settings
*/
function it_exchange_table_rate_shipping_get_table_rate( $table_rate_id ) {

	if ( 0 === $table_rate_id ) {
		if ( !( $table_rate = get_option( '_it_exchange_table_rate_shipping_default' ) ) ) {
			$table_rate = array(
				'enabled'          => 'default',
				'label'            => 'Default Table Rate Shipping',
				'condition'        => 'price',     //price, weight, count
				'min'              => '',
				'max'              => '',
				'handling-fee'     => '',
				'base-cost'        => '',
				'item-cost'        => '',
				'calculation-type' => 'per_order', //per order, item, line
				'geo-restrictions'  => array(),
			);
		}
	} else {
		$table_rate = array(
			'enabled'          => ( 'unchecked' === get_post_meta( $table_rate_id, '_it_exchange_table_rate_enabled', true ) ) ? 'unchecked' : 'checked',
			'label'            => get_the_title( $table_rate_id ),
			'condition'        => get_post_meta( $table_rate_id, '_it_exchange_table_rate_condition', true ), //price, weight, count
			'min'              => get_post_meta( $table_rate_id, '_it_exchange_table_rate_min', true ),
			'max'              => get_post_meta( $table_rate_id, '_it_exchange_table_rate_max', true ),
			'handling-fee'     => get_post_meta( $table_rate_id, '_it_exchange_table_rate_handling_fee', true ),
			'base-cost'        => get_post_meta( $table_rate_id, '_it_exchange_table_rate_base_cost', true ),
			'item-cost'        => get_post_meta( $table_rate_id, '_it_exchange_table_rate_item_cost', true ),
			'calculation-type' => get_post_meta( $table_rate_id, '_it_exchange_table_rate_calculation_type', true ), //per order, item, line
			'geo-restrictions' => get_post_meta( $table_rate_id, '_ite_etrs_rate_zones', true ),
		);
	}
		
	return $table_rate;
}

/**
 * Calculate all possible postal codes in a given range, recursively
 *
 * @since 1.0.0
 *
 * @param int $min - Minimum Postal Code
 * @param int $max - Maximum Postal Code
 * @param int|bool $position - Current Position in the incremental substring
 * @return array - Postal Codes
*/
function it_exchange_get_all_possible_zipcodes_in_range( $min, $max, $position=false ) {
	$code_length = strlen( $min ) - 1;
	$codes = array();
	if ( false === $position ) {
		$position = $code_length;
		$codes[] = $min;
	}

	if ( $min !== $max ) {
		$increment_str = substr( $min, $position, 1 );
		if ( '9' === $increment_str || 'Z' === $increment_str ) {
			if ( is_numeric( $increment_str ) ) {
				$min = substr_replace( $min, '0', $position, 1 );
			} else {
				$min = substr_replace( $min, 'A', $position, 1 );
			}
			$position--;
			$codes = array_merge( $codes, it_exchange_get_all_possible_zipcodes_in_range( $min, $max, $position ) );
		} else {
			$increment_str++;
			$min = substr_replace( $min, $increment_str, $position, 1 );
			$codes = array_merge( $codes, it_exchange_get_all_possible_zipcodes_in_range( $min, $max ) );
		}
	}
	return $codes;
}

/**
 * Add postal codes to ZipCode taxonomy to post meta
 *
 * @since 1.0.0
 *
 * @param int $table_rate_zone_id - Table Rate's Zone ID
 * @param int $zipcode - ZipCode being added
 * @return void
*/
function it_exchange_table_rate_shipping_addon_setup_zipcode_meta( $table_rate_zone_id, $zipcode='' ) {
	$zipcode = trim( $zipcode );
	
	// Canada (and others) use spaces in their zipcodes, we need to strip the to properly handle ranges
	// This means that we also need to make sure to strip the zipcodes of spaces when checking for table rates
	$zipcode = preg_replace('/\s+/', '', $zipcode); 
	
	if ( empty( $zipcode ) || 0 === ( $pos = strpos( $zipcode, '*' ) ) ) { //If it starts with an asterisk, it's all
		$zipcode = '*';
	} else if ( false !== $pos = strpos( $zipcode, '-' ) ) { //Possibly a range of Postal Codes
		$range = array_map( 'trim', explode( '-', $zipcode ) );
		if ( sizeof( $range ) === 2 ) {
			if ( strlen( $range[0] ) === strlen( $range[1] ) ) { //we can only do a valid range if both match, otherwise it's just a zipcode
				$min = strtoupper( $range[0] );
				$max = strtoupper( $range[1] );
			}
		}
	}
	
	$zipcode = strtoupper( $zipcode );
	//Now we can try saving in post meta.
	if ( isset( $min ) && isset( $max ) ) { //range or wildcards
		$zipcodes = it_exchange_get_all_possible_zipcodes_in_range( $min, $max );
		update_post_meta( $table_rate_zone_id, '_it_exchange_etrs_zipcode_zone', array( $zipcode => $zipcodes ) );		
	} else if ( !empty( $zipcode ) ) { // all zipcodes or single zipcode
		if ( false !== ( $post = strpos( $zipcode, ',' ) ) ) {
			$zipcodes = array_map( 'trim', explode( ',', $zipcode ) );
			update_post_meta( $table_rate_zone_id, '_it_exchange_etrs_zipcode_zone', array( $zipcode => $zipcodes ) );
		} else {
			update_post_meta( $table_rate_zone_id, '_it_exchange_etrs_zipcode_zone', array( $zipcode => array( $zipcode ) ) );
		}
	} else { // We shouldn't reach this, but just in case, set it as all zipcodes
		update_post_meta( $table_rate_zone_id, '_it_exchange_etrs_zipcode_zone', array( '*' => '*' ) ); //All the zipcodes
	}
}