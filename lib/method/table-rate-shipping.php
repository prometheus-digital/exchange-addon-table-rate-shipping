<?php
/**
 * iThemes Exchange Table Rate Shipping Add-on
 * @package exchange-addon-table-rate-shipping
 * @since 1.0.0
*/

class IT_Exchange_Table_Rate_Shipping_Method extends IT_Exchange_Shipping_Method {

	private $table_rate_args;

	/**
	 * Class constructor. Needed to call parent constructor
	 *
	 * @since 1.4.0
	 *
	 * @param integer $product_id optional product id for current product
	 * @return void
	*/
	function __construct( $product_id=false, $args ) {
		
		$this->table_rate_args = $args;
		
		// Set slug
		$this->set_slug();

		// Set label
		$this->set_label();

		// Set the product
		$this->set_product( $product_id );

		// Set whether this is enabled
		$this->set_enabled();

		// Set the availability of this method to this product
		$this->set_availability();

		// Set the shipping features for this method
		$this->set_features();
	}

	/**
	 * Sets the identifying slug for this shipping method
	 *
	 * @since 1.4.0
	 *
	 * @return void
	*/
	function set_slug() {
		$this->slug = $this->table_rate_args['slug'];
	}

	/**
	 * Sets the label for this shipping method
	 *
	 * @since 1.4.0
	 *
	 * @return void
	*/
	function set_label( ) {
		$this->label = $this->table_rate_args['label'];
	}

	/**
	 * Sets the Shipping Features that this method uses.
	 *
	 * @since 1.4.0
	 *
	 * @return void
	*/
	function set_features() {
		$this->shipping_features = array(
			'core-from-address',
			'core-weight-dimensions',
			'core-available-shipping-methods'
		);
	}

	/**
	 * Determines if this shipping method is enabled and sets the property value
	 *
	 * @since 1.4.0
	 *
	 * @return void
	*/
	function set_enabled() {
		$options = it_exchange_table_rate_shipping_get_table_rate( $this->table_rate_args['ID'] );
		$this->enabled = ! empty( $options['enabled'] );
	}

	/**
	 * Determines if this shipping method is available to the product and sets the property value
	 *
	 * @since 1.4.0
	 *
	 * @return void
	*/
	function set_availability() {
		$this->available = $this->enabled;
	}

	/**
	 * Define any setting fields that you want this method to include on the Provider settings page
	 *
	 * @since 1.4.0
	 *
	 * @return void
	*/
	function set_settings() {
		return;
	}
	
	function get_shipping_cost_for_product( $cart_product ) {
		return 0; //Table Rate Shipping is calculated per cart and per product, we need use a different hook
	}
}