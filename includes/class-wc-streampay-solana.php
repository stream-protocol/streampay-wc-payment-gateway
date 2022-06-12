<?php

/**
 * Handle the payments via Stream Protocol & Solana.
 *
 * @link       https://streamprotocol.org
 * @since      1.0.0
 *
 * @package    StreamPay
 * @subpackage StreamPay/includes
 * @link https://streamprotocol.app/woocommerce/payment-gateway-plugin.html
 * @link https://spl-token-faucet.com/ (for Testing)
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Handle the payments via Stream Protocol / Solana.
 *
 * This class defines all code necessary to run to use Solana Blockchain.
 *
 * @since      1.0.0
 * @package    StreamPay
 * @subpackage StreamPay/includes
 * @author     Stream Protocol / StreamPay  <contact@streamprotocol.org>
 */



// PHP Solana PHP SDK.
use Tighten\SolanaPhpSdk\Connection;
use Tighten\SolanaPhpSdk\SolanaRpcClient;

if ( ! class_exists( 'WC_Payment_Gateway' ) ) {
	return;
}

class Wc_StreamPay_Solana extends WC_Payment_Gateway {


	/**
	 * Here we initalize the payment options, hooks.
	 */
	public function __construct() {
		 $this->id                = STREAMPAY_GATEWAY_ID; // payment gateway plugin ID.
		$this->icon               = ''; // URL of the icon that will be displayed on checkout page near your gateway name.
		$this->has_fields         = true; // in case you need a custom credit card form.
		$this->method_title       = esc_html__( 'USDC on Solana', 'streampay' );
		$this->method_description = esc_html__( 'Pay via Solana USDC', 'streampay' ); // will be displayed on the options page.

		// gateways can support subscriptions, refunds, saved payment methods.
		$this->supports = array(
			'products',
		);

		// Method with all the options fields.
		$this->init_form_fields();

		// Load the settings.
		$this->init_settings();
		$this->title       = $this->get_option( 'title' );
		$this->description = $this->get_option( 'description' );
		$this->enabled     = $this->get_option( 'enabled' );
		$this->testmode    = 'yes' === $this->get_option( 'testmode' );
		if ( $this->testmode ) {
			$this->title  .= ' (' . esc_html__( 'Test mode enabled', 'streampay' ) . ')';
			$this->cluster = 'devnet';
		} else {
			$this->cluster = 'mainnet';
		}
		$this->publishable_key = $this->testmode ? $this->get_option( 'test_publishable_key' ) :
			$this->get_option( 'publishable_key' );
		$this->network_url = $this->get_option( 'network_url' );
		$this->streampay_log     = 'yes' === $this->get_option( 'streampay_log' );
		// unique id we are going to use as memo for the transactions.
		if ( ! is_admin() && isset(WC()->session) ) {
			$memo_session = WC()->session->get( STREAMPAY_MEMO_SESSION );
			if ( ! $memo_session ) {
				$this->memo = uniqid() . '-' . time();
				WC()->session->set( STREAMPAY_MEMO_SESSION, $this->memo );
			} else {
				$this->memo = WC()->session->get( STREAMPAY_MEMO_SESSION );
			}
			$this->signature = isset( $_COOKIE[ STREAMPAY_SIGNATURE_STORAGE ] ) ? $_COOKIE[ STREAMPAY_SIGNATURE_STORAGE ] : '';
		}
		// This action hook saves the settings.
		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );

		// We need custom JavaScript to obtain a token.
		add_action( 'wp_enqueue_scripts', array( $this, 'payment_scripts' ) );
		add_action( 'woocommerce_before_checkout_form', array( $this, 'embed_streampay_alerts' ) );
		// Include connect to wallet method.
		add_action( 'woocommerce_review_order_before_submit', array( $this, 'pay_with_streampay_markup' ) );
	}

	/**
	 * Plugin Options and data, that we can handle and change the plugin frontend data.
	 */
	public function init_form_fields() {
		$this->form_fields = array(
			'enabled'              => array(
				'title'       => esc_html__( 'Enable/Disable', 'streampay' ),
				'label'       => esc_html__( 'Enable StreamPay Solana Pay Gateway', 'streampay' ),
				'type'        => 'checkbox',
				'description' => '',
				'default'     => 'no',
			),
			'streampay_log'          => array(
				'title'       => esc_html__( 'Enable/Disable StreamPay Log', 'streampay' ),
				'label'       => esc_html__( 'Enable StreamPay Debug Log', 'streampay' ),
				'type'        => 'checkbox',
				'description' => esc_html__( '' ),
				'default'     => 'no',
				'desc_tip'    => true,
			),
			'title'                => array(
				'title'       => esc_html__( 'Title', 'streampay' ),
				'type'        => 'text',
				'description' => esc_html__( 'This controls the title which the user sees during checkout.', 'streampay' ),
				'default'     => esc_html__( 'USDC on Solana Payments', 'streampay' ),
				'desc_tip'    => true,
			),
			'description'          => array(
				'title'       => esc_html__( 'Description', 'streampay' ),
				'type'        => 'textarea',
				'description' => esc_html__( 'This controls the description which the user sees during checkout.', 'streampay' ),
				'default'     => esc_html__( 'Pay via USDC on Solana.', 'streampay' ),
			),
			'testmode'             => array(
				'title'       => esc_html__( 'Test mode', 'streampay' ),
				'label'       => esc_html__( 'Enable Test Mode', 'streampay' ),
				'type'        => 'checkbox',
				'description' => esc_html__( 'Place the payment gateway in test mode using test devnet wallet.', 'streampay' ),
				'default'     => 'no',
				'desc_tip'    => true,
			),
			'test_publishable_key' => array(
				'title' => esc_html__( 'Test Devnet Merchant Wallet Address', 'streampay' ),
				'type'  => 'text',
			),
			'publishable_key'      => array(
				'title' => esc_html__( 'Live Merchant Wallet Address', 'streampay' ),
				'type'  => 'text',
			),
			'network_url'      => array(
				'title' => esc_html__( 'RPC Network URL', 'streampay' ),
				'type'  => 'url',
				'description' => esc_html__( 'Leave empty to use mainnet. For Medium/High Volume Woocommerce Stores and/or Increased Performance Please Use: https://streampay.genesysgo.net/', 'streampay' ),
			),
		);
	}
	/**
	 * The payment fields that would appear in the frontend (Checkout page).
	 */
	public function payment_fields() {
		if ( $this->description ) {
			$this->description = trim( $this->description );
			// display the description with <p> tags etc.
			echo wpautop( wp_kses_post( $this->description ) );
		}
		// connect to wallet markup.
		if ( is_readable( STREAMPAY_ROOT . 'public/partials/streampay-connect-wallet.php' ) ) {
			include_once STREAMPAY_ROOT . 'public/partials/streampay-connect-wallet.php';
		}

	}
	/**
	 * Add the JS Scripts we need.
	 */
	public function payment_scripts() {
		// we need JavaScript to process a token only on cart/checkout pages.
		if ( ! is_cart() && ! is_checkout() && ! isset( $_GET['pay_for_order'] ) ) {
			return;
		}
		if ( ! $this->publishable_key ) {
			return;
		}

		// if our payment gateway is disabled, we do not have to enqueue JS too.
		if ( 'no' === $this->enabled ) {
			return;
		}

		// do not work with card detailes without SSL unless your website is in a test mode.
		if ( ! $this->testmode && ! is_ssl() ) {
			return;
		}

		// let's suppose it is our payment processor JavaScript that allows to obtain a token .
		// wp_enqueue_script( 'streampay_solana_js', 'https://www.streamprotocol.app/paymens/api/token.js' );

		// Custom.js that we would work with.
		wp_register_script( 'wc_streampay.js', STREAMPAY_ASSETS_URL . 'main.js', array( 'jquery' ), '1.0.0', true );
		wp_localize_script(
			'wc_streampay.js',
			'streampay',
			array(
				'to_public_key'       => $this->publishable_key,
				'network_url'         => $this->network_url,
				'order_total'         => self::get_order_total(),
				'security'            => wp_create_nonce( 'streampay-solana' ),
				'confirm_transaction' => 'streampay_confirm_transaction',
				'ajax_url'            => admin_url( 'admin-ajax.php' ),
				'test_mode'           => $this->testmode,
				'memo'                => $this->memo,
				'active_currency'     => ( function_exists( 'get_woocommerce_currency' ) ) ? get_woocommerce_currency() : 'USD',
				'get_total_order'     => 'get_order_total',
				'signature_storage'   => STREAMPAY_SIGNATURE_STORAGE,
			)
		);

		wp_enqueue_script( 'wc_streampay.js' );
	}


	/**
	 * Validate the fields.
	 *
	 * @see https://woocommerce.wp-a2z.org/oik_api/wc_add_notice/
	 * @see https://github.com/tighten/solana-php-sdk
	 * @return boolean
	 */
	public function validate_fields() {
		// if we need to validate anything here.
	   if ( $this->testmode ) {
		   $end_point = SolanaRpcClient::DEVNET_ENDPOINT;
		   $transaction_token = 'Gh9ZwEmdLJ8DscKNTkTqPbNwLNNBjuSzaG9Vp2KGtKJr';
	   } else {
		   $end_point = SolanaRpcClient::MAINNET_ENDPOINT;
		   $transaction_token ='EPjFWdd5AufqSSqeM2qN1xzybapC8G4wEGGkZwyTDt1v';
	   }
	   $client          = new SolanaRpcClient( $end_point );
	   $connection      = new Connection( $client );
	   $this->signature = isset( $_COOKIE[ STREAMPAY_SIGNATURE_STORAGE ] ) ? $_COOKIE[ STREAMPAY_SIGNATURE_STORAGE ] : '';

	   if ( $this->signature ) {
		   try {
			   $confirmed = $connection->getConfirmedTransaction( $this->signature );
					if( isset( $confirmed['meta']['postTokenBalances'][0]['mint'] ) && $confirmed['meta']['postTokenBalances'][0]['mint'] === $transaction_token) {
							$schema    = 'Program log: Memo (len ' . strlen( $this->memo ) . '): "' . $this->memo . '"';

							if ( isset( $confirmed['meta']['logMessages'] ) ) {
								if ( $confirmed['meta']['logMessages'][1] !== $schema ) {
									wc_add_notice( esc_html__( 'The signature provided has not valid memo.', 'streampay' ), 'error' );
									if ( $this->streampay_log ) {
										$malicious = $_SERVER['REMOTE_ADDR'];
										add_to_streampay_log( "This IP[$malicious] is a malicious Address" );
									}
									WC()->session->set( STREAMPAY_MEMO_SESSION, '' );
									return false;
								}
							} else {
								if ( $this->streampay_log ) {
									add_to_streampay_log( 'We need an update for the backend lib' );
								}
							}
						} else {
							wc_add_notice( esc_html__( 'Invalid Transaction Token.', 'streampay' ), 'error' );
							return false;
						}
		   } catch ( Exception $e ) {
			   $confirmed = false;
			   wc_add_notice( $e->getMessage(), 'error' );
			   return false;
		   }
		   if ( ! $confirmed ) {
			   wc_add_notice( esc_html__( 'The Transaction is not confirmed.', 'streampay' ), 'error' );
			   return false;
		   }
	   } else {
		   wc_add_notice( esc_html__( 'Not valid signature.', 'streampay' ), 'error' );
		   WC()->session->set( STREAMPAY_MEMO_SESSION, '' );
		   return false;
	   }
	   return true;
   }
	/**
	 * Process the payment through the main library.
	 *
	 * @param integer $order_id the created id for the order.
	 * @return mixed
	 */
	public function process_payment( $order_id ) {
		global $woocommerce;
		// we need it to get any order detailes
		$order = wc_get_order( $order_id );
		update_post_meta( $order_id, 'streampay_memo', $this->memo );
		update_post_meta( $order_id, 'streampay_signature', $this->signature );
		update_post_meta( $order_id, 'streampay_cluster', $this->cluster );
		$solscan = "https://solscan.io/tx/$this->signature?cluster=$this->cluster";

		// we received the payment
		$order->payment_complete();
		$order->reduce_order_stock();

		// some notes to customer (replace true with false to make it private).
		$order->add_order_note( esc_html__( 'Hey, your order is paid! Thank you!, Order memo:' . $this->memo . ' And Transaction Link: ', 'streampay' ) . esc_url( $solscan ), true );
		setcookie( STREAMPAY_SIGNATURE_STORAGE, '', time() - 3600, '/' ); // Expire this cookie.*/
		WC()->session->set( STREAMPAY_MEMO_SESSION, '' );

		// Empty cart
		$woocommerce->cart->empty_cart();

		// Redirect to the thank you page
		return array(
			'result'   => 'success',
			'redirect' => $this->get_return_url( $order ),
		);
	}
	/**
	 * Ihis Markup would display before the submit btn.
	 *
	 * @return void
	 */
	public function pay_with_streampay_markup() {
		if ( is_readable( STREAMPAY_ROOT . 'public/partials/streampay-payment-display.php' ) ) {
			include_once STREAMPAY_ROOT . 'public/partials/streampay-payment-display.php';
		}
	}
	/**
	 * Embed StreamPay Alerts.
	 *
	 * @return void
	 */
	public function embed_streampay_alerts() {
		?>
	<!-- Alerts List -->
<ul id="streampay-alerts" class="streampay__alerts"></ul>
		<?php
	}
}
