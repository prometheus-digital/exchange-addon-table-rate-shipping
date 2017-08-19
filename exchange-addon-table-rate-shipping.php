<?php
/*
 * Plugin Name: ExchangeWP - Table Rate Shipping
 * Version: 1.2.4
 * Description: Adds Table Rate Shipping to ExchangeWP.
 * Plugin URI: https://exchangewp.com/downloads/table-rate-shipping/
 * Author: ExchangeWP
 * Author URI: https://exchangewp.com
 * ExchangeWP Package: exchange-addon-table-rate-shipping

 * Installation:
 * 1. Download and unzip the latest release zip file.
 * 2. If you use the WordPress plugin uploader to install this plugin skip to step 4.
 * 3. Upload the entire plugin directory to your `/wp-content/plugins/` directory.
 * 4. Activate the plugin through the 'Plugins' menu in WordPress Administration.
 *
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
		'description'       => __( 'The Table Rate Shipping add-on enables highly customizable shipping options for ExchangeWP.', 'LION' ),
		'author'            => 'ExchangeWP',
		'author_url'        => 'https://exchangep.com/downloads/table-rate-shipping/',
		'icon'              => ITUtility::get_url_from_file( dirname( __FILE__ ) . '/lib/images/shipping50px.png' ),
		'file'              => dirname( __FILE__ ) . '/init.php',
		'category'          => 'shipping',
		'basename'          => plugin_basename( __FILE__ ),
		'labels'      => array(
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
 * @uses load_plugin_textdomain()
 * @since 1.0.0
 * @return void
*/
function it_exchange_table_rate_shipping_set_textdomain() {
	load_plugin_textdomain( 'LION', false, dirname( plugin_basename( __FILE__  ) ) . '/lang/' );
}
//add_action( 'plugins_loaded', 'it_exchange_table_rate_shipping_set_textdomain' );

/**
 * Registers Plugin with iThemes updater class
 *
 * @since 1.0.0
 *
 * @param object $updater ithemes updater object
 * @return void
*/
function ithemes_exchange_addon_table_rate_shipping_updater_register( $updater ) {
	$updater->register( 'exchange-addon-table-rate-shipping', __FILE__ );
}
add_action( 'ithemes_updater_register', 'ithemes_exchange_addon_table_rate_shipping_updater_register' );
//require( dirname( __FILE__ ) . '/lib/updater/load.php' );

if ( ! class_exists( 'EDD_SL_Plugin_Updater' ) )  {
 	require_once 'EDD_SL_Plugin_Updater.php';
 }

 function exchange_table_rate_shipping_plugin_updater() {

 	// retrieve our license key from the DB
 	// this is going to have to be pulled from a seralized array to get the actual key.
 	// $license_key = trim( get_option( 'exchange_table_rate_shipping_license_key' ) );
 	$exchangewp_table_rate_shipping_options = get_option( 'it-storage-exchange_table_rate_shipping-addon' );
 	$license_key = $exchangewp_table_rate_shipping_options['table_rate_shipping-license-key'];

 	// setup the updater
 	$edd_updater = new EDD_SL_Plugin_Updater( 'https://exchangewp.com', __FILE__, array(
 			'version' 		=> '1.2.4', 				// current version number
 			'license' 		=> $license_key, 		// license key (used get_option above to retrieve from DB)
 			'item_name' 	=> 'table-rate-shipping', 	  // name of this plugin
 			'author' 	  	=> 'ExchangeWP',    // author of this plugin
 			'url'       	=> home_url(),
 			'wp_override' => true,
 			'beta'		  	=> false
 		)
 	);
 	// var_dump($edd_updater);
 	// die();

 }

 add_action( 'admin_init', 'exchange_table_rate_shipping_plugin_updater', 0 );
