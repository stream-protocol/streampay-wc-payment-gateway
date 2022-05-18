<?php

/**
 * All the helper functions we need to use through the plugin.
 *
 * @link       https://plugins.streampay.info
 * @since      1.0.0
 *
 * @package    StreamPay 
 */
if ( ! function_exists( 'add_to_streampay_log' ) ) {
	/**
	 * Add Messages to StreamPay Log file.
	 *
	 * @param string $message the message we need to add to StreamPay Log.
	 * @param string $type the message type (Can be error, success, .. ).
	 * @see https://www.php.net/manual/en/function.date.php
	 * @see https://www.php.net/manual/en/function.error-log.php
	 * @return void
	 **/
	function add_to_streampay_log( $message, $type = 'error' ) {

			$date = date( 'F j, Y, g:i:s a' );
			error_log( "[$date]" . " : $type message received " . " [$message] " . "\r\n", 3, STREAMPAY_ROOT . '/streampay.log' );

	}
}
