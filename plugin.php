<?php
/**
 * Load Table Rate Shipping plugin.
 *
 * @since   2.0.0
 * @license GPLv2
 */

/**
 * This registers our plugin as a customer pricing addon
 *
 * @since 1.0.0
 *
 * @return void
 */
function it_exchange_register_table_rate_shipping_addon() {
	$options = array(
		'name'              => __( 'Table Rate Shipping', 'LION' ),
		'description'       => __( 'The Table Rate Shipping add-on enables highly customizable shipping options for iThemes Exchange.', 'LION' ),
		'author'            => 'iThemes',
		'author_url'        => 'http://ithemes.com/exchange/table-rate-shipping/',
		'icon'              => ITUtility::get_url_from_file( dirname( __FILE__ ) . '/lib/images/shipping50px.png' ),
		'file'              => dirname( __FILE__ ) . '/init.php',
		'category'          => 'shipping',
		'basename'          => plugin_basename( __FILE__ ),
		'labels'            => array(
			'singular_name' => __( 'Table Rate Shipping', 'LION' ),
		),
		'settings-callback' => 'it_exchange_table_rate_shipping_settings_callback',
	);
	it_exchange_register_addon( 'table-rate-shipping', $options );
}

add_action( 'it_exchange_register_addons', 'it_exchange_register_table_rate_shipping_addon' );

/**
 * Loads the translation data for WordPress
 *
 * @uses  load_plugin_textdomain()
 * @since 1.0.0
 * @return void
 */
function it_exchange_table_rate_shipping_set_textdomain() {
	load_plugin_textdomain( 'LION', false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );
}

it_exchange_table_rate_shipping_set_textdomain();
