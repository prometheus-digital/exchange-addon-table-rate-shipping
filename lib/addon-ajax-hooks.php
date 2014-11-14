<?php
/**
 * iThemes Exchange Table Rate Shipping Add-on
 * @package exchange-addon-table-rate-shipping
 * @since 1.0.0
*/

/**
 * AJAX function called to add new content access rule rows
 *
 * @since 1.0.0
*/
function it_exchange_table_rate_shipping_addon_ajax_add_new_table_rate() {
	die( it_exchange_table_rate_shipping_form_table_table_rate_settings() );		
}
add_action( 'wp_ajax_it-exchange-table-rate-shipping-addon-add-new-table-rate', 'it_exchange_table_rate_shipping_addon_ajax_add_new_table_rate' );

/**
 * AJAX function called to get existing zones for a Table Rate
 *
 * @since 1.0.0
*/
function it_exchange_table_rate_shipping_addon_ajax_get_existing_zones() {
	$data = array();
	if ( isset( $_REQUEST['rate_id'] ) ) {
		$zones = get_post_meta( $_REQUEST['rate_id'], '_ite_etrs_rate_zones', true );
		if ( !empty( $zones ) ) {
			foreach( $zones as $zone_id ) {
				$country = get_post_meta( $zone_id, '_it_exchange_etrs_country_zone', true );
				if( empty( $country ) || is_wp_error( $country ) ){
					$country = '*';
				}
				
				$state = get_post_meta( $zone_id, '_it_exchange_etrs_state_zone', true );
				if( empty( $state ) || is_wp_error( $state ) ){
					$state = '*';
				}
				
				$zipcodes = get_post_meta( $zone_id, '_it_exchange_etrs_zipcode_zone', true );
				if( empty( $zipcodes ) || is_wp_error( $zipcodes ) ){
					$zipcode = '*';
				} else {
					$zipcode = key( (array)$zipcodes );
				}
			
				$data[] = array(
					'ZoneID'  => $zone_id,
					'Country' => $country,
					'State'   => $state,
					'Zipcode' => $zipcode,
				);

			}
		}
	}
	wp_send_json_success( $data );
}
add_action( 'wp_ajax_it-exchange-table-rate-shipping-get-existing-zones', 'it_exchange_table_rate_shipping_addon_ajax_get_existing_zones' );

/**
 * AJAX function called to return an HTML formated Zone
 *
 * @since 1.0.0
*/
function it_exchange_table_rate_shipping_addon_ajax_format_zone() {
	$output = '';
	
	if ( isset( $_REQUEST['ZoneID'] ) && isset( $_REQUEST['Country'] ) && isset( $_REQUEST['State'] ) && isset( $_REQUEST['Zipcode'] ) ) {
		$general_settings = it_exchange_get_option( 'settings_general' );
		
		if ( !empty( $_REQUEST['ZoneID'] ) )
			$zone_id = $_REQUEST['ZoneID'];
		else
			$zone_id = 'temp-' . str_replace( '.', '', microtime( true ) );
	
		if ( empty( $_REQUEST['Country'] ) )
			$country = $general_settings['company-base-country'];
		else
			$country = $_REQUEST['Country'];
	
		if ( empty( $_REQUEST['State'] ) )
			$state = $general_settings['company-base-state'];
		else
			$state = $_REQUEST['State'];
	
		$zipcode = empty( $_REQUEST['Zipcode'] ) ? '*' : $_REQUEST['Zipcode'];
				
		$output .= '<div class="item-row block-row">';

		$output .= '<div class="item-column block-column block-column-1">';
		$output .= '<input type="hidden" class="it-exchange-etrs-zone-id" name="etrs-zone[' . $zone_id . '][zone_id]" value="' . $zone_id .'" />';
		$output .= '<select id="it-exchange-etrs-zone-country" name="etrs-zone[' . $zone_id . '][country]">';
		$output .= '<option value="*" ' . selected( '*', $country, false ) . '>' . __( 'All Countries', 'LION' ) . '</option>';
		$countries = it_exchange_get_data_set( 'countries' );
		foreach( $countries as $abbr => $name ) {
			$output .= '<option value="' . $abbr . '" ' . selected( $abbr, $country, false ) . '>' . $name . '</option>';
		}
		$output .= '</select>';
		$output .= '</div>';
		
		$output .= '<div class="item-column block-column block-column-2 it-exchange-etrs-zone-state-column">';
		$states = it_exchange_get_data_set( 'states', array( 'country' => $country ) );
		if ( !empty( $states ) ) {
			$output .= '<select id="it-exchange-etrs-zone-state" name="etrs-zone[' . $zone_id . '][state]">';
			$output .= '<option value="*" ' . selected( '*', $state, false ) . '>' . __( 'All States', 'LION' ) . '</option>';
			foreach( $states as $abbr => $name ) {
				$output .= '<option value="' . $abbr . '" ' . selected( $abbr, $state, false ) . '>' . $name . '</option>';
			}
			$output .= '</select>';
		} else {
			$state = empty( $state ) ? '*' : $state;
			$output .= '	<input type="text" name="etrs-zone[' . $zone_id . '][state]" value="' . $state . '">';
		}
		$output .= '</div>';
		
		$output .= '<div class="item-column block-column block-column-3">';
		$output .= '	<input type="text" name="etrs-zone[' . $zone_id . '][zipcode]" value="' . $zipcode . '">';
		$output .= '</div>'; 
		
		$output .= '<div class="item-column block-column block-column-4">';
		$output .= '	<a href class="it-exchange-table-rate-shipping-addon-delete-zone it-exchange-remove-item">&times;</a>';
		$output .= '</div>';
		
		$output .= '</div>';		
	}
	
	wp_send_json_success( $output );
}
add_action( 'wp_ajax_it-exchange-table-rate-shipping-format-zone', 'it_exchange_table_rate_shipping_addon_ajax_format_zone' );

/**
 * AJAX function called to get States dataset for a given Country
 *
 * @since 1.0.0
*/
function it_exchange_table_rate_shipping_addon_ajax_get_states() {
	
	$output = '';
	
	if ( !empty( $_REQUEST['ZoneID'] ) && !empty( $_REQUEST['Country'] ) ) {	
		$states = it_exchange_get_data_set( 'states', array( 'country' => $_REQUEST['Country'] ) );
		if ( !empty( $states ) ) {
			$output .= '<select id="it-exchange-etrs-zone-state" name="etrs-zone[' . $_REQUEST['ZoneID'] . '][state]">';
			$output .= '<option value="*">' . __( 'All States', 'LION' ) . '</option>';
			foreach( $states as $abbr => $name ) {
				$output .= '<option value="' . $abbr . '">' . $name . '</option>';
			}
			$output .= '</select>';
		} else {
			$output .= '<input type="text" name="etrs-zone[' . $_REQUEST['ZoneID'] . '][state]" value="*">';
		}
	} else {
		wp_send_json_error( __( 'No Country Defined', 'LION' ) );
	}
	
	wp_send_json_success( $output );
	
}
add_action( 'wp_ajax_it-exchange-table-rate-shipping-get-states', 'it_exchange_table_rate_shipping_addon_ajax_get_states' );

/**
 * AJAX function called to add new Zones to a Rate
 *
 * @since 1.0.0
*/
function it_exchange_table_rate_shipping_addon_ajax_add_zones() {
	
	$errors = array();
		
	if ( !empty( $_REQUEST['rate-id'] ) ) {
		
		if ( is_numeric( $_REQUEST['rate-id'] ) ) {
	
			if ( !empty( $_REQUEST['etrs-zone'] ) ) {
						
				$zones = (array)get_post_meta( $_REQUEST['rate-id'], '_ite_etrs_rate_zones', true );
				$saved_zones = array();
							
				foreach( $_REQUEST['etrs-zone'] as $key => $zone ) {
					
					if ( false !== strpos( $key, 'temp-' ) ) {
						//Need to create a new zone (post type), then add the zone taxonomies
						$zone_id = substr( $key, 5 );
						
						$post = array(
							'post_title'     => $_REQUEST['rate-id'] . '-zone-' . $zone_id,
					        'post_status'    => 'publish',
						    'ping_status'    => 'closed',
						    'comment_status' => 'closed',
							'post_type'      => 'ite_etrs_zone',
						);
						$table_rate_zone_id = wp_insert_post( $post, true );
						
						if ( is_wp_error( $table_rate_zone_id ) ) {
							$errors[] = $table_rate_zone_id->get_error_messages();
						} else {
							$saved_zones[] = $table_rate_zone_id; //add it to our internal array
							if ( empty( $zone['country'] ) ) {
								$zone['country'] = '*';
							}
							update_post_meta( $table_rate_zone_id, '_it_exchange_etrs_country_zone', $zone['country'] );
							
							if ( empty( $zone['state'] ) ) {
								$zone['state'] = '*';
							}
							update_post_meta( $table_rate_zone_id, '_it_exchange_etrs_state_zone', $zone['state'] );
	
							it_exchange_table_rate_shipping_addon_setup_zipcode_meta( $table_rate_zone_id, $zone['zipcode'] );
						}
						
					} else {
						//Need to verify existing zone (post type)
						$table_rate_zone = get_post( $key );
						if ( !empty( $table_rate_zone ) ) {
							$saved_zones[] = $table_rate_zone->ID;
	
							if ( empty( $zone['country'] ) ) {
								$zone['country'] = '*';
							}
							update_post_meta( $table_rate_zone->ID, '_it_exchange_etrs_country_zone', $zone['country'] );
							
							if ( empty( $zone['state'] ) ) {
								$zone['state'] = '*';
							}
							update_post_meta( $table_rate_zone->ID, '_it_exchange_etrs_state_zone', $zone['state'] );
	
							//Do we need to update the postal code(s) for this zone?
							if ( empty( $zone['zipcode'] ) ) {
								$zone['zipcode'] = '*';
							}
							
							$zipcodes = get_post_meta( $table_rate_zone->ID, '_it_exchange_etrs_zipcode_zone', true );
							$zipcode = key( (array)$zipcodes );
							//We only want to go through this if the zipcodes have change, save some processing
							if ( $zone['zipcode'] !== $zipcode ) {
								it_exchange_table_rate_shipping_addon_setup_zipcode_meta( $table_rate_zone->ID, $zone['zipcode'] );
							}
							
						} else {
							
							$errors[] = __( 'Something went wrong, the table rate zone could not be found', 'LION' );
							
						}
						
					}
					
				}
				
				if ( empty( $errors ) ) {
					//Update zones array for this table rate ID
					update_post_meta( $_REQUEST['rate-id'], '_ite_etrs_rate_zones', $saved_zones );
					
					if ( array_diff( $zones, $saved_zones ) || array_diff( $saved_zones, $zones ) ) {
						foreach( $zones as $zone ) {
							if ( !in_array( $zone, $saved_zones ) ) {
								//We removed some zones, now we need to delete them.
								wp_delete_post( $zone, true ); //permanently!
							}
						}
					}
	
					wp_send_json_success( array( 'zone_output' => it_exchange_table_rate_shipping_prepare_zone_ouput( $saved_zones, $_REQUEST['rate-id'] ) ) );
		
				} else {
			
					wp_send_json_error( $errors );
				
				}
				
			} else {
				
				//Empty zones, remove them all
				$zones = (array)get_post_meta( $_REQUEST['rate-id'], '_ite_etrs_rate_zones', true );
				foreach( $zones as $zone ) {
					//We removed some zones, now we need to delete them.
					wp_delete_post( $zone, true ); //permanently!
				}
				delete_post_meta( $_REQUEST['rate-id'], '_ite_etrs_rate_zones' );

				wp_send_json_success( array( 'zone_output' => it_exchange_table_rate_shipping_prepare_zone_ouput( false, $_REQUEST['rate-id'] ) ) );

			}
			
		} else {
			
			wp_send_json_error( __( 'Invalid Data', 'LION' ) );
						
		}
	
	} else {
			
		wp_send_json_error( __( 'Missing Data', 'LION' ) );
	
	}
	
}
add_action( 'wp_ajax_it-exchange-table-rate-shipping-save-zones', 'it_exchange_table_rate_shipping_addon_ajax_add_zones' );

/**
 * AJAX function called to save rates
 *
 * @since 1.0.0
*/
function it_exchange_table_rate_shipping_addon_ajax_save_rates() {
	
	$form_values = it_exchange_get_option( 'addon_table_rate_shipping' );
	
	if ( ! empty( $_POST ) && is_admin() ) {
		$form_values = apply_filters( 'it_exchange_save_add_on_settings_table_rate_shipping', $form_values, $_POST );
	}

	if ( !empty( $form_values['errors'] ) ) {
		$messages = array();
		foreach( $form_values['errors'] as $error ) {
			$messages[] = ITUtility::show_error_message( $error );
		}
		wp_send_json_error( join( ',', $messages ) );
	} else if ( !empty( $form_values['settings_saved'] ) ) {
		wp_send_json_success();
	}	
	
}
add_action( 'wp_ajax_it-exchange-table-rate-shipping-save-rates', 'it_exchange_table_rate_shipping_addon_ajax_save_rates' );

/**
 * AJAX function called to format priciing information in Table Rate fields
 *
 * @since 1.0.0
*/
function it_exchange_table_rate_shipping_addon_ajax_format_pricing() {
	$price = '';
	if ( !empty( $_POST['input'] ) || ( empty( $_POST['input'] ) && 0 == $_POST['input'] ) ) {
		$price = html_entity_decode( it_exchange_format_price( it_exchange_convert_from_database_number( it_exchange_convert_to_database_number( $_POST['input'] ) ) ) );
	}
	die( $price );		
}
add_action( 'wp_ajax_it-exchange-table-rate-shipping-addon-format-pricing', 'it_exchange_table_rate_shipping_addon_ajax_format_pricing' );