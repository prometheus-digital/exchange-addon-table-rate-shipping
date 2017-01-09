<?php
/**
 * iThemes Exchange Table Rate Shipping Add-on
 * @package exchange-addon-table-rate-shipping
 * @since 1.0.0
*/

/**
 * Registers the Shipping Methods we need for Exchange Table Rate Shipping add-on
 *
 * @since 1.0.0
 *
 * @return void
*/
function it_exchange_table_rate_shipping_addon_register_shipping_methods() {	
	$default_table_rate = it_exchange_table_rate_shipping_get_table_rate( 0 ); //default always set rate
	$args = array(
		'ID'    => 0,
		'label' => $default_table_rate['label'],
		'slug'  => 'default-table-rate-shipping-method',
	);
	it_exchange_register_shipping_method( 'default-table-rate-shipping-method', 'IT_Exchange_Table_Rate_Shipping_Method', $args );

	$args = array(
		'post_type'      => 'ite_table_rate',
		'posts_per_page' => -1,
		'order'	         => 'ASC',
	);
	$table_rates = get_posts( $args );
	if ( !empty( $table_rates ) ) {
		foreach( $table_rates as $table_rate ) {
			$args = array(
				'ID'    => $table_rate->ID,
				'label' => $table_rate->post_title,
				'slug'  => $table_rate->post_name,
			);
        	it_exchange_register_shipping_method( $table_rate->post_name, 'IT_Exchange_Table_Rate_Shipping_Method', $args );
		}
	}
}
add_action( 'it_exchange_enabled_addons_loaded', 'it_exchange_table_rate_shipping_addon_register_shipping_methods' );