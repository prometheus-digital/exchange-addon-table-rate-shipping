<?php
/**
 * iThemes Exchange Table Rate Shipping Add-on
 * @package exchange-addon-table-rate-shipping
 * @since 1.0.0
*/

/**
 * Callback function for add-on settings
 *
 * We are using this differently than most add-ons. We want the gear
 * to appear on the add-ons screen so we are registering the callback.
 * It will be intercepted though if the user clicks on it and redirected to
 * The Exchange settings --> shipping tab.
 *
 * @since 1.0.0
 *
 * @return void
*/
function it_exchange_table_rate_shipping_settings_callback() {
	// Store Owners should never arrive here. Add a link just in case the do somehow
	?>
	<div class="wrap">
		<?php ITUtility::screen_icon( 'it-exchange' ); ?>
		<h2><?php _e( 'Table Rate Shipping', 'LION' ); ?></h2>
		<?php
		$url = add_query_arg( array( 'page' => 'it-exchange-settings', 'tab' => 'shipping', 'provider' => 'table-rate-shipping' ), esc_url( admin_url( 'admin.php' ) ) );
		?><p><?php printf( __( 'Settings are located in the %sShipping tab%s on the Exchange Settings page.', 'LION' ), '<a href="' . $url . '">', '</a>' ); ?></p>
	</div>
	<?php
}

/**
 * Prints Table Rate Shipping settings page
 *
 * @since 1.0.0
*/
function it_exchange_table_rate_shipping_print_shipping_tab() {
	$form_values = it_exchange_get_option( 'addon_table_rate_shipping' );
	
	if ( ! empty( $_POST ) && is_admin() 
		&& !empty( $_GET['page'] ) && 'it-exchange-settings' == $_GET['page'] 
		&& !empty( $_GET['provider'] ) && 'table-rate-shipping' == $_GET['provider'] ) {
		$form_values = apply_filters( 'it_exchange_save_add_on_settings_table_rate_shipping', $form_values, $_POST );
	}
	
	$form_options = array(
		'id'      => apply_filters( 'it_exchange_add_on_eu_value_added_taxes', 'it-exchange-add-on-table-rate-shipping-settings' ),
		'enctype' => apply_filters( 'it_exchange_add_on_eu_value_added_taxes_settings_form_enctype', false ),
		'action'  => 'admin.php?page=it-exchange-settings&tab=shipping&provider=table-rate-shipping',
	);
	$form         = new ITForm( $form_values, array( 'prefix' => 'it-exchange-add-on-table-rate-shipping' ) );

	/*
	if ( ! empty ( $this->status_message ) )
		ITUtility::show_status_message( $this->status_message );
	if ( ! empty( $this->error_message ) )
		ITUtility::show_error_message( $this->error_message );
	*/
	?>
	<div class="wrap">
		<?php
		ITUtility::screen_icon( 'it-exchange' );
		// Print Admin Settings Tabs
		$GLOBALS['IT_Exchange_Admin']->print_general_settings_tabs();

		// Print shipping provider tabs
		$GLOBALS['it_exchange']['shipping_object']->print_provider_settings_tabs();
		
		?>
		
		<?php $form->start_form( $form_options, 'it-exchange-table-rate-shipping-settings' ); ?>
			<?php do_action( 'it_exchange_eu_value_added_taxes_settings_form_top' ); ?>
			<?php it_exchange_table_rate_shipping_get_settings_form_table( $form, $form_values ); ?>
			<?php do_action( 'it_exchange_eu_value_added_taxes_settings_form_bottom' ); ?>
			<p class="submit">
				<?php $form->add_submit( 'submit', array( 'value' => __( 'Save Changes', 'LION' ), 'class' => 'button button-primary button-large' ) ); ?>
			</p>
		<?php $form->end_form(); ?>
	</div>
	<?php
	do_action( 'it_exchange_table_rate_shipping_print_shipping_tab_footer' );
}

/**
 * Prints Table Rate Shipping settings table rate table (tongue twister)
 *
 * @since 1.0.0
*/
function it_exchange_table_rate_shipping_get_settings_form_table( $form, $settings = array() ) {
	if ( !empty( $settings ) )
		foreach ( $settings as $key => $var )
			$form->set_option( $key, $var );
	?>
	
    <div class="it-exchange-addon-settings it-exchange-table-rate-shipping-addon-settings">
        <h4>
        	<?php _e( 'Current Table Rates and Settings', 'LION' ) ?> 
        </h4>
                
        <div id="table-rate-shipping-rates-table">
        	<?php
			$headings = array(
				__( 'Enabled', 'LION' ), 
				sprintf( __( 'Label %s', 'LION' ), it_exchange_admin_tooltip( __( 'The name of your shipping option (ex:  "Free Shipping over $50" or "In State Shipping").', 'LION' ), false ) ),
				sprintf( __( 'Calculation %s', 'LION' ), it_exchange_admin_tooltip( __( 'How the shipping rate is calculated, by order, by number of items or number of products.<ul><li>Per Order - Shipping would be applied only once for the order</li><li>Per Item - Shipping would be applied for each item in the cart</li><li>Per Product - Shipping would be applied based on the number of products in the cart (multiples of a product would only be charged once)</li></ul>', 'LION' ), false ) ),
				sprintf( __( 'Condition %s', 'LION' ), it_exchange_admin_tooltip( __( "The condition that the shipping cost is based on, for instance the price of the customer's order.", 'LION' ), false ) ), 
				sprintf( __( 'Min %s', 'LION' ), it_exchange_admin_tooltip( __( '(optional) The minimum amount for the condition to be met (ex:  Condition is Price, minimum price could be $50 for free shipping or Condition is Item Count - minimum item count could be 10 items to qualify for that shipping price).', 'LION' ), false ) ), 
				sprintf( __( 'Max %s', 'LION' ), it_exchange_admin_tooltip( __( '(optional) The maximum amount for the condition to be met (ex:  Condition is Price, maximum price could be $150 for free shipping or Condition is Item Count - maximum item count could be 20 items to qualify for that shipping price).', 'LION' ), false ) ), 
				sprintf( __( 'Handling %s', 'LION' ), it_exchange_admin_tooltip( __( '(optional) The handling cost for this shipping rate.', 'LION' ), false ) ), 
				sprintf( __( 'Base Cost %s', 'LION' ), it_exchange_admin_tooltip( __( 'The base shipping price for the shipping rate.', 'LION' ), false ) ),
				sprintf( __( 'Per Item Cost %s', 'LION' ), it_exchange_admin_tooltip( __( '(optional) If you chose to charge per item in an order, you would set the price to be charged per item here.', 'LION' ), false ) ),
				sprintf( __( 'Zones %s', 'LION' ), it_exchange_admin_tooltip( __( 'The geographical location for which this shipping cost would be applied.', 'LION' ), false ) ), 
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
        	
			echo '<div id="table-rate-shipping-table-rates">';
		
			echo it_exchange_table_rate_shipping_form_table_table_rate_settings( 0 ); //Default rate
			
			$args = array(
				'post_type'      => 'ite_table_rate',
				'posts_per_page' => -1,
				'order'	         => 'ASC',
			);
			$table_rates = get_posts( $args );

    		if ( !empty( $table_rates ) ) {
    			foreach( $table_rates as $table_rate ) {
    				echo it_exchange_table_rate_shipping_form_table_table_rate_settings( $table_rate->ID );
    			}
    		}
    		echo '</div>';
        	?>
        </div>
        
		<p class="add-new-table-rate">
			<?php $form->add_button( 'new-table-rate', array( 'value' => __( 'Add New Table Rate', 'LION' ), 'class' => 'button button-secondary button-large' ) ); ?>
		</p>

        
	</div>
	<?php
}

/**
 * Saves Table Rate settings
 *
 * @since 1.0.0
 *
 * @param array $form_values - Current form values
 * @param array $post_request - New post request
*/
function it_exchange_save_add_on_settings_table_rate_shipping( $form_values, $post_request ) {

	$current_table_rates = array();
	$errors = array();
		
	if ( !empty( $post_request['it-exchange-table-rate-shipping-addon-table-rate'] ) ) {
	
		foreach( $post_request['it-exchange-table-rate-shipping-addon-table-rate'] as $table_rate_id => $table_rate ) {
			
			if ( 0 === $table_rate_id ) {
			
				//Default, must always exist
				$table_rate['enabled']          = 'default';
				$table_rate['label']            = !empty( $table_rate['label'] )            ? $table_rate['label']            : '';
				$table_rate['condition']        = !empty( $table_rate['condition'] )        ? $table_rate['condition']        : 'price';
				$table_rate['min']              = !empty( $table_rate['min'] )              ? $table_rate['min']              : '';
				$table_rate['max']              = !empty( $table_rate['max'] )              ? $table_rate['max']              : '';
				$table_rate['handling-fee']     = !empty( $table_rate['handling-fee'] )     ? $table_rate['handling-fee']     : '';
				$table_rate['base-cost']        = !empty( $table_rate['base-cost'] )        ? $table_rate['base-cost']        : '';
				$table_rate['item-cost']        = !empty( $table_rate['item-cost'] )        ? $table_rate['item-cost']        : '';
				$table_rate['calculation-type'] = !empty( $table_rate['calculation-type'] ) ? $table_rate['calculation-type'] : 'per_order';
				$table_rate['geo-restrictions']  = array();

				if ( 'price' === $table_rate['condition'] ) {
					$table_rate['min'] = it_exchange_convert_to_database_number( $table_rate['min'] );
					$table_rate['max'] = it_exchange_convert_to_database_number( $table_rate['max'] );
				}
			
				$table_rate['handling-fee'] = it_exchange_convert_to_database_number( $table_rate['handling-fee'] );
				$table_rate['base-cost']    = it_exchange_convert_to_database_number( $table_rate['base-cost'] );
				$table_rate['item-cost']    = it_exchange_convert_to_database_number( $table_rate['item-cost'] );
				
				update_option( '_it_exchange_table_rate_shipping_default', $table_rate );
			} else {
				if ( !in_array( $table_rate_id, $current_table_rates ) )
					$current_table_rates[] = $table_rate_id;
				
				$args = array(
					'p'         => $table_rate_id,
					'post_type' => 'ite_table_rate',
				);
				$table_rates = get_posts( $args );
			
				if ( empty( $table_rates ) ) {
					//Table Rate doesn't exist yet, create a new one and add it to the $form_values list.
					$post = array(
						'post_title'     => $table_rate['label'],
				        'post_status'    => 'publish',
					    'ping_status'    => 'closed',
					    'comment_status' => 'closed',
						'post_type'      => 'ite_table_rate',
					);
					$table_rate_id = wp_insert_post( $post, true );
					
					if ( is_wp_error( $table_rate_id ) ) {
						$errors[] = $table_rate_id->get_error_messages();
					}
				} else {
					$post = array(
						'ID'         => $table_rate_id,
						'post_title' => $table_rate['label'],
						'post_type'  => 'ite_table_rate',
					);
					$table_rate_id = wp_update_post( $post );
					
					if ( is_wp_error( $table_rate_id ) ) {
						$errors[] = $table_rate_id->get_error_messages();
					}
				}
								
				if ( !empty( $table_rate['enabled'] ) && 'checked' === $table_rate['enabled'] )
					update_post_meta( $table_rate_id, '_it_exchange_table_rate_enabled', 'checked' );
				else
					update_post_meta( $table_rate_id, '_it_exchange_table_rate_enabled', 'unchecked' );
					
				if ( !empty( $table_rate['condition'] ) )
					update_post_meta( $table_rate_id, '_it_exchange_table_rate_condition', $table_rate['condition'] );
				else
					update_post_meta( $table_rate_id, '_it_exchange_table_rate_condition', 'price' ); //Default
					
				if ( 'price' === $table_rate['condition'] ) {
					$table_rate['min'] = it_exchange_convert_to_database_number( $table_rate['min'] );
					$table_rate['max'] = it_exchange_convert_to_database_number( $table_rate['max'] );
				}
			
				$table_rate['handling-fee'] = it_exchange_convert_to_database_number( $table_rate['handling-fee'] );
				$table_rate['base-cost']    = it_exchange_convert_to_database_number( $table_rate['base-cost'] );
				$table_rate['item-cost']    = it_exchange_convert_to_database_number( $table_rate['item-cost'] );
				
				if ( !empty( $table_rate['min'] ) )
					update_post_meta( $table_rate_id, '_it_exchange_table_rate_min', $table_rate['min'] );
				else
					delete_post_meta( $table_rate_id, '_it_exchange_table_rate_min' );
					
				if ( !empty( $table_rate['max'] ) )
					update_post_meta( $table_rate_id, '_it_exchange_table_rate_max', $table_rate['max'] );
				else
					delete_post_meta( $table_rate_id, '_it_exchange_table_rate_max' );
					
				if ( !empty( $table_rate['handling-fee'] ) )
					update_post_meta( $table_rate_id, '_it_exchange_table_rate_handling_fee', $table_rate['handling-fee'] );
				else
					delete_post_meta( $table_rate_id, '_it_exchange_table_rate_handling_fee' );
					
				if ( !empty( $table_rate['base-cost'] ) )
					update_post_meta( $table_rate_id, '_it_exchange_table_rate_base_cost', $table_rate['base-cost'] );
				else
					delete_post_meta( $table_rate_id, '_it_exchange_table_rate_base_cost' );
					
				if ( !empty( $table_rate['item-cost'] ) )
					update_post_meta( $table_rate_id, '_it_exchange_table_rate_item_cost', $table_rate['item-cost'] );
				else
					delete_post_meta( $table_rate_id, '_it_exchange_table_rate_item_cost' );
					
				if ( !empty( $table_rate['calculation-type'] ) )
					update_post_meta( $table_rate_id, '_it_exchange_table_rate_calculation_type', $table_rate['calculation-type'] );
				else
					update_post_meta( $table_rate_id, '_it_exchange_table_rate_calculation_type', 'per_order' ); //Default

			}
			
		}
		
		$args = array(
			'post_type'      => 'ite_table_rate',
			'posts_per_page' => -1,
		);
		$table_rates = get_posts( $args );
		// Now we need to remove any table rates that have been deleted
		foreach ( $table_rates as $table_rate ) {
			if ( !in_array( $table_rate->ID, $current_table_rates ) ) {
				$result = wp_delete_post( $table_rate->ID, true ); //force delete the table rate
			}				
		}
		
	} else {
	
		//This should never happen because we ALWAYS have the default rate... but just in case
		$args = array(
			'post_type'      => 'ite_table_rate',
			'posts_per_page' => -1,
		);
		$table_rates = get_posts( $args );
		foreach ( $table_rates as $table_rate ) {
			wp_delete_post( $table_rate->ID, true ); //force delete the table rates
		}
		
	}
	
	it_exchange_save_option( 'addon_table_rate_shipping', $form_values );
		
	if ( !empty( $errors ) ) {
		
		foreach( $errors as $error ) {
			echo "<h1>$error</h1>";
		}
		
	}
	
	return $form_values;
}
add_filter( 'it_exchange_save_add_on_settings_table_rate_shipping', 'it_exchange_save_add_on_settings_table_rate_shipping', 10, 2 );