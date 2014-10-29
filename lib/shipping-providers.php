<?php
/**
 * iThemes Exchange Table Rate Shipping Add-on
 * @package exchange-addon-table-rate-shipping
 * @since 1.0.0
*/

/**
 * Register our Shipping Provider
 *
 * @since 1.0.0
 *
 * @return void
*/
function it_exchange_table_rate_shipping_addon_register_shipping_provider() {
	$methods = array( 'default-table-rate-shipping-method' ); //default method, needs to be added to the list first
	
	$args = array(
		'post_type'      => 'ite_table_rate',
		'posts_per_page' => -1,
		'order'	         => 'ASC',
	);
	$table_rates = get_posts( $args );
	if ( !empty( $table_rates ) ) {
		foreach( $table_rates as $table_rate ) {
	        $methods[] = $table_rate->post_name;
		}
	}
	
    $options = array(
        'label'            => __( 'Table Rate Shipping', 'LION' ),
        'shipping-methods' => $methods,
        'provider-settings' => array( array( 'type' => 'table-rate-shipping-provider-placeholder-type', 'slug' => 'table-rate-shipping-provider-placeholder-slug' ) ),
    );
    it_exchange_register_shipping_provider( 'table-rate-shipping', $options );
}
add_filter( 'it_exchange_enabled_addons_loaded', 'it_exchange_table_rate_shipping_addon_register_shipping_provider' );