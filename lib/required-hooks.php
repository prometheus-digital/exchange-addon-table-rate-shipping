<?php
/**
 * iThemes Exchange Table Rate Shipping Add-on
 * @package exchange-addon-table-rate-shipping
 * @since 1.0.0
*/

//Just to be absolutely sure that the shipping address requirement is enabled.

/**
 * Shows the nag when needed.
 *
 * @since 1.0.0
 *
 * @return void
*/
function it_exchange_table_rate_shipping_addon_show_version_nag() {
	if ( version_compare( $GLOBALS['it_exchange']['version'], '1.11.2.1', '<' ) ) {
		?>
		<div id="it-exchange-add-on-min-version-nag" class="it-exchange-nag">
			<?php printf( __( 'The Table Rate Shipping add-on requires iThemes Exchange version 1.11.2.1 or greater. %sPlease upgrade Exchange%s.', 'LION' ), '<a href="' . admin_url( 'update-core.php' ) . '">', '</a>' ); ?>
		</div>
		<script type="text/javascript">
			jQuery( document ).ready( function() {
				if ( jQuery( '.wrap > h2' ).length == '1' ) {
					jQuery("#it-exchange-add-on-min-version-nag").insertAfter('.wrap > h2').addClass( 'after-h2' );
				}
			});
		</script>
		<?php
	}
}
add_action( 'admin_notices', 'it_exchange_table_rate_shipping_addon_show_version_nag' );

/**
 * Enqueues Table Rate Shipping scripts to WordPress Dashboard
 *
 * @since 1.0.0
 *
 * @param string $hook_suffix WordPress passed variable
 * @return void
*/
function it_exchange_table_rate_shipping_addon_admin_wp_enqueue_scripts( $hook_suffix ) {
	global $post;
			
	if ( isset( $_REQUEST['post_type'] ) ) {
		$post_type = $_REQUEST['post_type'];
	} else {
		if ( isset( $_REQUEST['post'] ) )
			$post_id = (int) $_REQUEST['post'];
		elseif ( isset( $_REQUEST['post_ID'] ) )
			$post_id = (int) $_REQUEST['post_ID'];
		else
			$post_id = 0;

		if ( $post_id )
			$post = get_post( $post_id );

		if ( isset( $post ) && !empty( $post ) )
			$post_type = $post->post_type;
	}
	
	$url_base = ITUtility::get_url_from_file( dirname( __FILE__ ) );
		
	if ( !empty( $_GET['page'] ) && 'it-exchange-settings' === $_GET['page'] && !empty( $_GET['provider'] ) && 'table-rate-shipping' === $_GET['provider'] ) {
	
		$deps = array( 'jquery' );
		wp_enqueue_script( 'it-exchange-table-rate-shipping-admin-js', $url_base . '/js/admin.js' );
		wp_enqueue_script( 'it-exchange-dialog');

		$deps = array( 'jquery', 'wp-backbone', 'underscore' );
		wp_enqueue_script( 'ite-etrs-addon-zone-model',  $url_base . '/js/models/zone-model.js', $deps );
		$deps[] =  'ite-etrs-addon-zone-model';
		wp_enqueue_script( 'ite-etrs-addon-zone-collections',  $url_base . '/js/collections/zone-collections.js', $deps );
		$deps[] =  'ite-etrs-addon-zone-collections';
		wp_enqueue_script( 'ite-etrs-addon-zones-views',  $url_base . '/js/views/zones-views.js', $deps );
		$deps[] =  'ite-etrs-addon-zones-views';
		wp_enqueue_script( 'ite-etrs-addon-zones-manager', $url_base . '/js/zones-manager.js', $deps );
		wp_enqueue_style( 'ite-etrs-addon-zones-manager', $url_base . '/styles/zones-manager.css' );
	
		add_action( 'it_exchange_table_rate_shipping_print_shipping_tab_footer', 'it_exchange_table_rate_shipping_addon_zones_manager_backbone_template' );

	}
		
}
add_action( 'admin_enqueue_scripts', 'it_exchange_table_rate_shipping_addon_admin_wp_enqueue_scripts' );

/**
 * Enqueues Table Rate Shipping styles to WordPress Dashboard
 *
 * @since 1.0.0
 *
 * @return void
*/
function it_exchange_table_rate_shipping_addon_admin_wp_enqueue_styles() {
	global $post, $hook_suffix;

	if ( isset( $_REQUEST['post_type'] ) ) {
		$post_type = $_REQUEST['post_type'];
	} else {
		if ( isset( $_REQUEST['post'] ) ) {
			$post_id = (int) $_REQUEST['post'];
		} else if ( isset( $_REQUEST['post_ID'] ) ) {
			$post_id = (int) $_REQUEST['post_ID'];
		} else {
			$post_id = 0;
		}

		if ( $post_id )
			$post = get_post( $post_id );

		if ( isset( $post ) && !empty( $post ) )
			$post_type = $post->post_type;
	}
	
	// US Sales Taxes settings page
	if ( !empty( $_GET['page'] ) && 'it-exchange-settings' === $_GET['page'] && !empty( $_GET['provider'] ) && 'table-rate-shipping' === $_GET['provider'] ) {
		
		wp_enqueue_style( 'it-exchange-table-rate-shipping-addon-admin-style', ITUtility::get_url_from_file( dirname( __FILE__ ) ) . '/styles/admin.css' );
		
	}

}
add_action( 'admin_print_styles', 'it_exchange_table_rate_shipping_addon_admin_wp_enqueue_styles' );

/**
 * Print Shipping sub-tab under Settings area of Exchange
 *
 * @since 1.0.0
 *
 * @param string $callback - Callback function name
 * @return void
*/
function it_exchange_table_rate_shipping_register_settings_tab_callback( $callback ) {
	if ( !empty( $_GET['page'] ) && 'it-exchange-settings' === $_GET['page'] ) {
		if ( !empty( $_GET['provider'] ) && 'table-rate-shipping' === $_GET['provider'] ) {
			$callback = 'it_exchange_table_rate_shipping_print_shipping_tab';
		}
	}
	return $callback; 
}
add_action( 'it_exchange_shipping_register_settings_tab_callback', 'it_exchange_table_rate_shipping_register_settings_tab_callback', 100 );

/**
 * Redirects to General Settings -> Shipping -> Table Rate Shipping from add-on settings page.
 *
 * @since 1.0.0
 *
 * return void
*/
function it_exchange_table_rate_shipping_settings_redirect() {
	$page  = ! empty( $_GET['page'] ) && 'it-exchange-addons' == $_GET['page'];
	$addon = ! empty( $_GET['add-on-settings'] ) && 'table-rate-shipping' == $_GET['add-on-settings'];

	if ( $page && $addon ) {
		wp_redirect( esc_url_raw( add_query_arg( array( 'page' => 'it-exchange-settings', 'tab' => 'shipping', 'provider' => 'table-rate-shipping' ), admin_url( 'admin.php' ) ) ) );
		die();
	}
}
add_action( 'admin_init', 'it_exchange_table_rate_shipping_settings_redirect' );

/**
 * Backbone template for listing all existing zones in the Zones Manager.
 * Invoked by wp.template() and WordPress 
 *
 * Called by add_action( 'it_exchange_table_rate_shipping_print_shipping_tab_footer', 'it_exchange_table_rate_shipping_addon_zones_manager_backbone_template' );
 *
 * @since 1.0.0
 */
function it_exchange_table_rate_shipping_addon_zones_manager_backbone_template() {
	?>
	<div id="it-exchange-table-rate-shipping-zones-manager-wrapper" class="it-exchange-hidden"></div>
	<script type="text/template" id="tmpl-it-exchange-table-rate-shipping-zones-manager-container">
		<span class="it-exchange-etrs-close-zones-manager"><a href="">&times;</a></span>
		<div id="it-exchange-table-rate-shipping-zones-manager">
			<div id="it-exchange-table-rate-shipping-zones-manager-title-area">
				<h3 class="it-exchange-etrs-zones-manager-title">
					<?php _e( 'Zones Manager', 'LION' ); ?>
				</h3>
			</div>
		
			<div id="it-exchange-table-rate-shipping-zones-manager-content-area">
				<div id="it-exchange-table-rate-shipping-zones-manager-error-area"></div>
				<form id="it-exchange-add-on-etrs-zone-manager-form" name="it-exchange-add-on-etrs-zone-manager-form" action="POST">
					<div id="table-rate-shipping-zones-table">
						<div id="table-rate-shipping-zones-table-headings">
						<?php
							$headings = array(
								__( 'Country', 'LION' ), 
								__( 'State', 'LION' ),
								sprintf( __( 'Postal Code(s) %s', 'LION' ), it_exchange_admin_tooltip( __( 'An asterisk (*) would apply the cost to all postal codes within the designated country/state. You can specify a range of postal codes by separating two postal codes with a dash (-); e.g. 73013-73018.', 'LION' ), false ) ),
								__( 'Delete', 'LION' ), 
							);
							echo '<div class="heading-row block-row">';
							$column = 0;
							foreach ( $headings as $heading ) {
								$column++;
								echo '<div class="heading-column block-column block-column-' . $column . '">';
								echo '<p class="heading">' . $heading . '</p>';
								echo '</div>';
							}
							echo '</div>';
						?>
						</div>
						<div id="table-rate-shipping-zones-table-zones"></div>
					</div>
					<input type="hidden" name="rate-id" value="{{{ data.rate_id }}}" />
					<?php
						submit_button( 'Add New Zone', 'secondary', 'add_new_etrs_zone', true );
					?>
				</form>
				<p>
				<?php
					submit_button( 'Save Changes', 'primary', 'save_all_etrs_zone', false );
					echo '&nbsp;';
					submit_button( 'Cancel Changes', 'secondary', 'cancel_etrs_zone', false );
				?>
				</p>
			</div>
		</div>
	</script>
	<?php
}

/**
 * Adds shipping-address as a valid super-widget state
 *
 * @since 1.0.0
 *
 * @param array $valid_states existing valid states
 * @return array
*/
function it_exchange_table_rate_shipping_modify_valid_sw_states( $valid_states ) {
	$valid_states[] = 'shipping-address';
	return $valid_states;
}
add_filter( 'it_exchange_super_widget_valid_states', 'it_exchange_table_rate_shipping_modify_valid_sw_states' );

/**
 * This function parses the available shipping methods and removes any that don't match the criteria set by Table Rate Shipping.
 *
 * @since 1.0.0
 *
 * @param array $shipping_methods - Shipping Methods
 * @return array $shipping_methods
*/
function it_exchange_table_rate_shipping_get_available_shipping_methods_for_cart( $shipping_methods ) {
	
	if ( ! $GLOBALS['it_exchange']['shipping']['only_return_methods_available_to_all_cart_products'] ) {
		return $shipping_methods;
	}
	
	if ( count( $shipping_methods ) === 0 ) {
		return $shipping_methods;
	}
	
	$general_settings = it_exchange_get_option( 'shipping-general' );

	$shipping_address = it_exchange_get_cart_shipping_address();
	$cart_total_item_count = it_exchange_get_cart_products_count( true, 'shipping' );
	$cart_product_count = it_exchange_get_cart_products_count( false, 'shipping' );	
	$cart_weight = it_exchange_get_cart_weight();

	$cart_total_args = array();

	if ( ! empty( $general_settings['exclude_non_shippable'] ) ) {
		$cart_total_args['feature'] = 'shipping';
	}

	$cart_total = it_exchange_get_cart_subtotal( false, $cart_total_args );
	
	foreach ( $shipping_methods as $key => $shipping_method ) {
		if ( 'IT_Exchange_Table_Rate_Shipping_Method' !== get_class( $shipping_method ) ) {
			continue;
		}
		
		$unset = false;
		
		if ( 'default-table-rate-shipping-method' === $key ) {
			continue; //We don't need to test default, it doesn't handle a condition, so skip this			
		}

		$table_rate_settings = it_exchange_table_rate_shipping_get_table_rate( $key );
		if ( 'checked' === $table_rate_settings['enabled'] ) {
			if ( !empty( $table_rate_settings['geo-restrictions'] ) ) {
			
				foreach( $table_rate_settings['geo-restrictions'] as $zone_id ) {
					$unset = true; 	//We're just going to assume that we'll hit a zone limit, but if we get a positive match
									//we'll set $unset to false and break out of this loop.
					
					$country = get_post_meta( $zone_id, '_it_exchange_etrs_country_zone', true );
					if ( '*' === $country || trim( $country ) === '' ) {
						$unset = false; 	//Country is the highest level zone, if it's All, then it has to be all States/Postal Codes, 
						break;			//so we don't skip this zone.
					} else if ( $shipping_address['country'] === $country ) {
						$state = get_post_meta( $zone_id, '_it_exchange_etrs_state_zone', true );
						if( '*' === $state || trim ( $state ) === '' ) {
							$unset = false; 	//Country matches and State is a wildcard, so we can skip and break 
							break;
						} else if ( 'USCONTIGUOUS' === $state ) {
							$contiguous_states = it_exchange_get_data_set( 'states', array( 'country' => $country ) );
							unset( $contiguous_states['AK'], $contiguous_states['HI'] ); //Alaska and Hawaii is not contiguous
							if ( !empty( $contiguous_states[$shipping_address['state']] ) ) {
								$unset = false; //Country and State is a semi-wildcard, so we can skip and break 
								break;
							}
						} else if ( $shipping_address['state'] === $state ){
							$zipcodes = get_post_meta( $zone_id, '_it_exchange_etrs_zipcode_zone', true );
							if( ! is_array( $zipcodes ) || count( $zipcodes ) === 0 || ( '*' === $zip_key = key( $zipcodes ) ) || in_array( $shipping_address['zip'], $zipcodes[$zip_key] ) ) {
								$unset = false; //Country and State match, and Postal Code is a wildcard or a match, so we can skip and break 
								break;
							}			
						}
					}
				}
			}
		} else {
			$unset = true; //this table rate is not enabled
		}
		
		if ( !$unset ) {			
			switch( $table_rate_settings['condition'] ) {
				case 'weight':
					if ( !empty( $table_rate_settings['min'] ) && $cart_weight < $table_rate_settings['min'] ) {
						$unset = true; //We need to unset this method, it's not usable in this cart
					} else if ( !empty( $table_rate_settings['max'] ) && $cart_weight > $table_rate_settings['max'] ) {
						$unset = true; //We need to unset this method, it's not usable in this cart
					}							
					break;
					
				case 'item_count':
					if ( !empty( $table_rate_settings['min'] ) && $cart_total_item_count < $table_rate_settings['min'] ) {
						$unset = true; //We need to unset this method, it's not usable in this cart
					} else if ( !empty( $table_rate_settings['max'] ) && $cart_total_item_count > $table_rate_settings['max'] ) {
						$unset = true; //We need to unset this method, it's not usable in this cart
					} 
					break;
					
				case 'product_count':
					if ( !empty( $table_rate_settings['min'] ) && $cart_product_count < $table_rate_settings['min'] ) {
						$unset = true; //We need to unset this method, it's not usable in this cart
					} else if ( !empty( $table_rate_settings['max'] ) && $cart_product_count > $table_rate_settings['max'] ) {
						$unset = true; //We need to unset this method, it's not usable in this cart
					} 
					break;
				
				case 'price':
				default:
					if ( !empty( $table_rate_settings['min'] ) && $cart_total < it_exchange_convert_from_database_number( $table_rate_settings['min'] ) ) {
						$unset = true; //We need to unset this method, it's not usable in this cart
					} else if ( !empty( $table_rate_settings['max'] ) && $cart_total >  it_exchange_convert_from_database_number( $table_rate_settings['max'] ) ) {
						$unset = true; //We need to unset this method, it's not usable in this cart
					} 
					break;
			}
		}
		
		if ( $unset ) {
			unset( $shipping_methods[$key] ); //We need to unset this method, it's not usable in this cart
		} else {
			unset( $shipping_methods['default-table-rate-shipping-method'] ); //We have a matching rate, so we can unset the default rate
		}
	}

	return $shipping_methods;
}
add_filter( 'it_exchange_get_available_shipping_methods_for_cart', 'it_exchange_table_rate_shipping_get_available_shipping_methods_for_cart' );

/**
 * Filter the available shipping methods for a given product.
 * 
 * @since 1.0.0
 * 
 * @param array $shipping_methods
 * @param array $product
 *
 * @return array
 */
function it_exchange_table_rate_shipping_get_available_shipping_methods_for_product_provider_methods( $shipping_methods, $product ) {

	if ( ! $product || is_admin() ) {
		return $shipping_methods;
	}

	if ( $GLOBALS['it_exchange']['shipping']['only_return_methods_available_to_all_cart_products'] ) {
		return $shipping_methods;
	}

	if ( $shipping_methods ) {
		return $shipping_methods;
	}

	$shipping_address = it_exchange_get_cart_shipping_address();

	$item_count = it_exchange_get_cart_product_quantity_by_product_id( $product->ID );

    $pm             = get_post_meta( $product->ID, '_it_exchange_core_weight', true );
    $weight         = empty( $pm['weight'] ) ? 0 : $pm['weight'];
	$product_weight = $weight * $item_count;

	$product_total = it_exchange_get_cart_product_base_price( array( 'product_id' => $product->ID ), false ) * $item_count;
	$product_overriding_default_methods = it_exchange_get_shipping_feature_for_product( 'core-available-shipping-methods', $product->ID );

	foreach ( $shipping_methods as $shipping_method ) {

		if ( ! $shipping_method || empty( $GLOBALS['it_exchange']['shipping']['methods'][ $shipping_method ] )) {
			continue;
		}

		$class = $GLOBALS['it_exchange']['shipping']['methods'][$shipping_method]['class'];

		if ( $class !== 'IT_Exchange_Table_Rate_Shipping_Method' ) {
			continue;
		}

		if ( 'default-table-rate-shipping-method' === $shipping_method ) {
			continue; //We don't need to test default, it doesn't handle a condition, so skip this
		}

		$unset = false;

        if ( false !== $product_overriding_default_methods && empty( $product_overriding_default_methods->$shipping_method ) ) { //If this shipping method has been disable in the product, disable it here as well
            //We need to do this because of how we're handling the Default shipping rate
            //Otherwise a "matching" rate that is disabled would unset the default rate
            $unset = true;
        } else {
			$table_rate_settings = it_exchange_table_rate_shipping_get_table_rate( $shipping_method );
			if ( 'checked' === $table_rate_settings['enabled'] ) {

				if ( !empty( $table_rate_settings['geo-restrictions'] ) ) {

					foreach( $table_rate_settings['geo-restrictions'] as $zone_id ) {
						$unset = true; 	//We're just going to assume that we'll hit a zone limit, but if we get a positive match
										//we'll set $unset to false and break out of this loop.

						$country = get_post_meta( $zone_id, '_it_exchange_etrs_country_zone', true );
						if ( '*' === $country || trim( $country ) === '' ) {
							$unset = false; 	//Country is the highest level zone, if it's All, then it has to be all States/Postal Codes,
							break;			//so we don't skip this zone.
						} else if ( $shipping_address['country'] === $country ) {
							$state = get_post_meta( $zone_id, '_it_exchange_etrs_state_zone', true );

							if ( '*' === $state || trim( $state ) === '' ) {
								$unset = false; 	//Country matches and State is a wildcard, so we can skip and break
								break;
							} else if ( 'USCONTIGUOUS' === $state ) {
								$contiguous_states = it_exchange_get_data_set( 'states', array( 'country' => $country ) );
								unset( $contiguous_states['AK'], $contiguous_states['HI'] ); //Alaska and Hawaii is not contiguous
								if ( !empty( $contiguous_states[$shipping_address['state']] ) ) {
									$unset = false; //Country and State is a semi-wildcard, so we can skip and break
									break;
								}
							} else if ( $shipping_address['state'] === $state ){
								$zipcodes = get_post_meta( $zone_id, '_it_exchange_etrs_zipcode_zone', true );
								if( ! is_array( $zipcodes ) || count( $zipcodes ) === 0 || ( '*' === $zip_key = key( $zipcodes ) ) || in_array( $shipping_address['zip'], $zipcodes[$zip_key] ) ) {
									$unset = false; //Country and State match, and Postal Code is a wildcard or a match, so we can skip and break
									break;
								}
							}
						}
					}
				}
			} else {
				$unset = true; //this table rate is not enabled
			}

			if ( !$unset ) {
				switch( $table_rate_settings['condition'] ) {
					case 'weight':
						if ( !empty( $table_rate_settings['min'] ) && $product_weight < $table_rate_settings['min'] ) {
							$unset = true; //We need to unset this method, it's not usable in this cart
						} else if ( !empty( $table_rate_settings['max'] ) && $product_weight > $table_rate_settings['max'] ) {
							$unset = true; //We need to unset this method, it's not usable in this cart
						}
						break;

					case 'item_count':
						if ( !empty( $table_rate_settings['min'] ) && $item_count < $table_rate_settings['min'] ) {
							$unset = true; //We need to unset this method, it's not usable in this cart
						} else if ( !empty( $table_rate_settings['max'] ) && $item_count > $table_rate_settings['max'] ) {
							$unset = true; //We need to unset this method, it's not usable in this cart
						}
						break;

					case 'product_count': //there is only 1 product in the cart for this call
						if ( !empty( $table_rate_settings['min'] ) && 1 < $table_rate_settings['min'] ) {
							$unset = true; //We need to unset this method, it's not usable in this cart
						} else if ( !empty( $table_rate_settings['max'] ) && 1 > $table_rate_settings['max'] ) {
							$unset = true; //We need to unset this method, it's not usable in this cart
						}
						break;

					case 'price':
					default:
						if ( !empty( $table_rate_settings['min'] ) && $product_total < it_exchange_convert_from_database_number( $table_rate_settings['min'] ) ) {
							$unset = true; //We need to unset this method, it's not usable in this cart
						} else if ( !empty( $table_rate_settings['max'] ) && $product_total >  it_exchange_convert_from_database_number( $table_rate_settings['max'] ) ) {
							$unset = true; //We need to unset this method, it's not usable in this cart
						}
						break;
				}
			}
		}

		if ( $unset ) {
			if ( false !== $key = array_search( $shipping_method, $shipping_methods ) ) {
				unset( $shipping_methods[$key] ); //We need to unset this method, it's not usable in this cart
			}
		} else {
			if ( false !== $key = array_search( 'default-table-rate-shipping-method', $shipping_methods ) ) {
				unset( $shipping_methods[$key] ); //We need to unset this method, it's not usable in this cart
			}
		}

	}

	return $shipping_methods;
}
add_filter( 'it_exchange_get_available_shipping_methods_for_product_provider_methods', 'it_exchange_table_rate_shipping_get_available_shipping_methods_for_product_provider_methods', 10, 2 );

/**
 * This function parses the available shipping methods and removes any that don't match the criteria set by Table Rate Shipping.
 *
 * @since 1.0.0
 *
 * @param float  $cart_cost - Current cost of shipping
 * @param int    $shipping_method - Selected Shipping Method
 * @param array  $cart_products - Current Cart's products
 * @param bool   $format_price - Whether or not to format the price
 *
 * @return float
 */
function it_exchange_table_rate_shipping_get_cart_shipping_cost( $cart_cost, $shipping_method, $cart_products, $format_price ) {

	if ( !  $shipping_method || empty( $GLOBALS['it_exchange']['shipping']['methods'][ $shipping_method ] ) ) {
		return $cart_cost;
	}

	$class = $GLOBALS['it_exchange']['shipping']['methods'][ $shipping_method ]['class'];

	if ( $class !== 'IT_Exchange_Table_Rate_Shipping_Method' ) {
		return $cart_cost;
	}

	// Make sure we have a class index and it corresponds to a defined class
	$cart_total_item_count = it_exchange_get_cart_products_count( true, 'shipping' );
	$cart_product_count = it_exchange_get_cart_products_count( false, 'shipping' );

	if ( 'default-table-rate-shipping-method' === $shipping_method ) {
		$shipping_method = 0;
	}

	$table_rate_settings = it_exchange_table_rate_shipping_get_table_rate( $shipping_method );

	if ( 'checked' !== $table_rate_settings['enabled'] && 'default' !== $table_rate_settings['enabled'] ) {
		return $cart_cost;
	}

	$handling      = it_exchange_convert_from_database_number( $table_rate_settings['handling-fee'] );
	$base_cost     = it_exchange_convert_from_database_number( $table_rate_settings['base-cost'] );
	$per_item_cost = it_exchange_convert_from_database_number( $table_rate_settings['item-cost'] );

	switch( $table_rate_settings['calculation-type'] ) {
		case 'per_item':
			$cart_cost = ( ( $handling + $base_cost ) * $cart_total_item_count ) + ( $per_item_cost * $cart_total_item_count );
			break;

		case 'per_line':
			$cart_cost = ( ( $handling + $base_cost ) * $cart_product_count ) + ( $per_item_cost * $cart_total_item_count );
			break;

		case 'per_order':
		default:
			$cart_cost = $handling + $base_cost + ( $per_item_cost * $cart_total_item_count );
			break;
	}

	return $format_price ? it_exchange_format_price( $cart_cost ) : $cart_cost;
}

add_filter( 'it_exchange_get_cart_shipping_cost', 'it_exchange_table_rate_shipping_get_cart_shipping_cost', 10, 4 );

/**
 * Calculate the shipping cost for a particular cart item.
 * 
 * @since 1.0.0
 * 
 * @param float  $cost
 * @param string $method_slug
 * @param array  $cart_product
 * @param bool   $format_price
 *
 * @return float
 */
function it_exchange_table_rate_shipping_get_shipping_method_cost_for_cart_item( $cost, $method_slug, $cart_product, $format_price ) {

	if ( ! $method_slug || empty( $GLOBALS['it_exchange']['shipping']['methods'][$method_slug] ) ) {
		return $cost;
	}

	$class = $GLOBALS['it_exchange']['shipping']['methods'][$method_slug]['class'];

	if ( $class !== 'IT_Exchange_Table_Rate_Shipping_Method' ) {
		return $cost;
	}

	$item_count = it_exchange_get_cart_product_quantity_by_product_id( $cart_product['product_id'] );

	if ( 'default-table-rate-shipping-method' === $method_slug ) {
		$method_slug = 0;
	}

	$table_rate_settings = it_exchange_table_rate_shipping_get_table_rate( $method_slug );

	if ( 'checked' !== $table_rate_settings['enabled'] && 'default' !== $table_rate_settings['enabled'] ) {
		return $cost;
	}

	$handling      = it_exchange_convert_from_database_number( $table_rate_settings['handling-fee'] );
	$base_cost     = it_exchange_convert_from_database_number( $table_rate_settings['base-cost'] );
	$per_item_cost = it_exchange_convert_from_database_number( $table_rate_settings['item-cost'] );

	switch( $table_rate_settings['calculation-type'] ) {
		case 'per_item':
		case 'per_line':
			$cost = ( $handling + $base_cost + $per_item_cost ) * $item_count;
			break;

		case 'per_order':
		default:
			$cost = $handling + $base_cost + ( $per_item_cost * $item_count );
			break;
	}

	return $format_price ? it_exchange_format_price( $cost ) : $cost;
}
add_filter( 'it_exchange_get_shipping_method_cost_for_cart_item', 'it_exchange_table_rate_shipping_get_shipping_method_cost_for_cart_item', 10, 4 );