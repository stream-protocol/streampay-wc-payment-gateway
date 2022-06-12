<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://streamprotocol.org
 * @since      1.0.0
 *
 * @package    StreamPay
 * @subpackage StreamPay/public
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    StreamPay WooCommerce Payment Gateway
 * @subpackage StreamPay/public
 * @author     Stream Protocol / StreamPay  <contact@streamprotocol.org>
 */
class StreamPay_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $plugin_name       The name of the plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in StreamPay_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The StreamPay_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/streampay-public.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'app.min.css', plugin_dir_url( __FILE__ ) . 'front/build/app.min.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in StreamPay_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The StreamPay_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/streampay-public.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( 'app.min.js', plugin_dir_url( __FILE__ ) . 'front/build/app.min.js', array( 'jquery' ), $this->version, true );
		
		wp_localize_script( 'app.min.js', 'streampay_text', array(
			'global' => __( 'Something went wrong', 'streampay' ),
			'recipient_acc_not_found' => __( 'Recipient account was not found', 'streampay' ),
			'payer_acc_not_found' => __( 'Payer account was not found', 'streampay' ),
			'account_not_found' => __( 'Please check your account exist or network connectivity', 'streampay' ),
			'order_amount_error' => __( 'Failed to get order amount', 'streampay' ),
			'transaction_not_found' => __( 'Transaction with the following signature was not found: ', 'streampay' ),
			'invalid_wc_checkout_form' => __( 'Please complete the form', 'streampay' ),
			'transaction_creation_failed' => __( 'Failed to create transaction.', 'streampay' ),
			'confirmation_failed' => __( 'Failed to confirm your transaction, checking the transaction signature...', 'streampay' ),
			
			'wallet_connected' => __( 'Wallet connected successfully', 'streampay' ),
			'sending_transaction' => __( 'Sending transaction', 'streampay' ),
			'transaction_confirmed' => __( 'Waiting for transaction to be confirmed.', 'streampay' ),
			'transaction_created' => __( 'Transaction was created successfully', 'streampay' ),
			'placing_order' => __( 'Placing your order', 'streampay' ),
			'paid_order_part_1' => __( 'Already paid for this order with StreamPay on', 'streampay' ),
			'paid_order_part_2' => __( ', press place order to complete the order.', 'streampay' ),
		));
	}
	/**
	 * Get the order total for frontend transactions.
	 *
	 * @return mixed
	 */
	public function streampay_get_order_total() {
		check_ajax_referer( 'streampay-solana', 'security' );
		$total    = 0;
		$order_id = absint( get_query_var( 'order-pay' ) );

		// Gets order total from "pay for order" page.
		if ( 0 < $order_id ) {
			$order = wc_get_order( $order_id );
			if ( $order ) {
				$total = (float) $order->get_total();
			}

			// Gets order total from cart/checkout.
		} elseif ( 0 < WC()->cart->total ) {
			$total = (float) WC()->cart->total;
		}
		wp_send_json_success(
			array(
				'total' => $total,
			)
		);
	}

}
