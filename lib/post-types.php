<?php
/**
 * ExchangeWP Table Rate Shipping Add-on
 * @package exchange-addon-table-rate-shipping
 * @since 1.0.0
*/

/**
 * Registers the Table Rate Shipping Post Type
 *
 * @since 1.0.0
 *
 * @return void
*/
function it_exchange_table_rate_shipping_addon_register_post_types() {
	// Variant Post Type Args
	$labels    = array(
		'name'          => __( 'Table Rates', 'LION' ),
		'singular_name' => __( 'Table Rate', 'LION' ),
		'edit_item'     => __( 'Edit Table Rate', 'LION' ),
		'view_item'     => __( 'View Table Rate', 'LION' ),
	);
	$args = array(
		'labels' => $labels,
		'description'  => __( 'An ExchangeWP Post Type for storing Table Rates in the system', 'LION' ),
		'public'       => false,
		'show_ui'      => false,
		'hierarchical' => false,
		'supports'     => array( 'title', 'custom-fields', ),
	);

	$args = apply_filters( 'it_exchange_table_rate_shipping_addon_register_ite_table_rate_post_types_args', $args );

	// Register the variant post type
	register_post_type( 'ite_table_rate', $args );

	// Variant Post Type Args
	$labels    = array(
		'name'          => __( 'Table Rate Zones', 'LION' ),
		'singular_name' => __( 'Table Rate Zone', 'LION' ),
		'edit_item'     => __( 'Edit Table Rate Zone', 'LION' ),
		'view_item'     => __( 'View Table Rate Zone', 'LION' ),
	);
	$args = array(
		'labels' => $labels,
		'description'  => __( 'An ExchangeWP Post Type for storing Table Rate Zones in the system', 'LION' ),
		'public'       => false,
		'show_ui'      => false,
		'hierarchical' => false,
		'supports'     => array( 'title', 'custom-fields', ),
	);

	$args = apply_filters( 'it_exchange_table_rate_shipping_addon_register_ite_table_rate_zone_post_types_args', $args );

	// Register the variant post type
	register_post_type( 'ite_etrs_zone', $args );
}
add_action( 'init', 'it_exchange_table_rate_shipping_addon_register_post_types' );
