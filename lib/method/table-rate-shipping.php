<?php

/**
 * iThemes Exchange Table Rate Shipping Add-on
 * @package exchange-addon-table-rate-shipping
 * @since   1.0.0
 */
class IT_Exchange_Table_Rate_Shipping_Method extends IT_Exchange_Shipping_Method {

	private $table_rate_args;

	/**
	 * Class constructor. Needed to call parent constructor
	 *
	 * @since 1.4.0
	 *
	 * @param int|bool $product_id optional product id for current product
	 * @param array    $args
	 */
	public function __construct( $product_id = false, $args ) {

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
	public function set_slug() {
		$this->slug = $this->table_rate_args['slug'];
	}

	/**
	 * Sets the label for this shipping method
	 *
	 * @since 1.4.0
	 *
	 * @return void
	 */
	public function set_label() {
		$this->label = $this->table_rate_args['label'];
	}

	/**
	 * Sets the Shipping Features that this method uses.
	 *
	 * @since 1.4.0
	 *
	 * @return void
	 */
	public function set_features() {
		$this->shipping_features = array(
			'core-weight',
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
	public function set_enabled() {
		$options       = it_exchange_table_rate_shipping_get_table_rate( $this->table_rate_args['ID'] );
		$this->enabled = ! empty( $options['enabled'] );
	}

	/**
	 * Determines if this shipping method is available to the product and sets the property value
	 *
	 * @since 1.4.0
	 *
	 * @return void
	 */
	public function set_availability() {
		$this->available = $this->enabled;
	}

	/**
	 * Define any setting fields that you want this method to include on the Provider settings page
	 *
	 * @since 1.4.0
	 *
	 * @return void
	 */
	public function set_settings() {

	}

	/**
	 * @inheritdoc
	 */
	public function get_shipping_cost_for_product( $cart_product ) {

		$item_count = it_exchange_get_cart_product_quantity_by_product_id( $cart_product['product_id'] );

		if ( 'default-table-rate-shipping-method' === $this->slug ) {
			$method_slug = 0;
		} else {
			$method_slug = $this->slug;
		}

		$table_rate_settings = it_exchange_table_rate_shipping_get_table_rate( $method_slug );

		if ( 'checked' !== $table_rate_settings['enabled'] && 'default' !== $table_rate_settings['enabled'] ) {
			return 0;
		}

		$handling      = it_exchange_convert_from_database_number( $table_rate_settings['handling-fee'] );
		$base_cost     = it_exchange_convert_from_database_number( $table_rate_settings['base-cost'] );
		$per_item_cost = it_exchange_convert_from_database_number( $table_rate_settings['item-cost'] );

		switch ( $table_rate_settings['calculation-type'] ) {
			case 'per_item':
			case 'per_line':
				$cost = ( $handling + $base_cost + $per_item_cost ) * $item_count;
				break;

			case 'per_order':
			default:
				$cost = $handling + $base_cost + ( $per_item_cost * $item_count );
				$cost /= it_exchange_get_cart_products_count( false, 'shipping' );
				break;
		}

		return $cost;
	}

	/**
	 * @inheritdoc
	 */
	public function get_additional_cost_for_cart( ITE_Cart $cart ) {

		// Make sure we have a class index and it corresponds to a defined class
		$cart_total_item_count = it_exchange_get_cart_products_count( true, 'shipping' );
		$cart_product_count    = it_exchange_get_cart_products_count( false, 'shipping' );

		if ( 'default-table-rate-shipping-method' === $this->slug ) {
			$shipping_method = 0;
		} else {
			$shipping_method = $this->slug;
		}

		$table_rate_settings = it_exchange_table_rate_shipping_get_table_rate( $shipping_method );

		if ( 'checked' !== $table_rate_settings['enabled'] && 'default' !== $table_rate_settings['enabled'] ) {
			return 0;
		}

		$existing_cost = 0;

		/** @var ITE_Shipping_Line_Item $shipping */
		foreach ( $cart->get_items( 'shipping', true ) as $shipping ) {
			if ( $shipping->get_method()->slug === $this->slug && $shipping->get_aggregate() ) {
				$existing_cost += $shipping->get_amount() * $shipping->get_quantity();
			}
		}

		$handling      = it_exchange_convert_from_database_number( $table_rate_settings['handling-fee'] );
		$base_cost     = it_exchange_convert_from_database_number( $table_rate_settings['base-cost'] );
		$per_item_cost = it_exchange_convert_from_database_number( $table_rate_settings['item-cost'] );

		switch ( $table_rate_settings['calculation-type'] ) {
			case 'per_item':
				return 0;

			case 'per_line':
				return 0;

			case 'per_order':
			default:
				$cart_cost = $handling + $base_cost + ( $per_item_cost * $cart_total_item_count );
				break;
		}

		return $cart_cost - $existing_cost;
	}
}