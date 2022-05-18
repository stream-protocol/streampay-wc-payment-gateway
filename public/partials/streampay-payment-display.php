<section id="streampay-payment-container">
	<!-- Header -->
	<header id="streampay-header" aria-hidden="true" class="streampay__header streampay__payment-control">
		<h2 class="streampay__header__heading"><?php esc_html_e( 'Pay With USDC', 'StreamPay' ); ?></h2>
		<p class="streampay__header__warning">
			<strong><?php esc_html_e( 'Don\'t close the page until redirected to the order received page or your transaction may be lost.', 'StreamPay' ); ?></strong>
		</p>
	</header>

	<!-- Phantom Link -->
	<div id="streampay-phantom-link" class="streampay__payment-control streampay__payment-control__phantom-link-wrapper"
		aria-hidden="true">
		<a target="_blank" rel="noreferrer noopener" class=" streampay__payment-controls__phantom-link"
			href="https://phantom.app/"><?php esc_html_e( 'Connect to Phantom Wallet', 'StreamPay' ); ?></a>
		<p><?php esc_html_e( 'Already installed the Phantom wallet? Try refreshing the page.', 'StreamPay' ); ?></p>
	</div>


	<!-- Place Order Button -->
	<button id="streampay-place-order" type="button" aria-hidden="true"
		class="streampay__payment-control streampay__clickable streampay__payment-control--btn streampay__payment-control__place-order">
		<!-- Content -->
		<?php esc_html_e( 'Pay with', 'streampay' ); ?>
		<img class="streampay__payment-control__place-order-image" src="/wp-content/plugins/streampay/public/front/src/assets/images/solana-sol-logo.svg" alt="" role="presentation"/>
		<?php esc_html_e( 'Pay', 'StreamPay' ); ?>
		<!-- Click Layer -->
		<div id="streampay-place-order-click-layer" class="streampay__clickable__click-layer"></div>
		<!-- Loader -->
		<div id="streampay-place-order-loader" class="streampay__loader" loading="false">
			<div class="lds-default">
				<div></div>
				<div></div>
				<div></div>
				<div></div>
				<div></div>
				<div></div>
				<div></div>
				<div></div>
				<div></div>
				<div></div>
				<div></div>
				<div></div>
			</div>
		</div>
	</button>

	<!-- Transaction Error -->
	<div id="streampay-transaction-error" aria-hidden="true" role="alert"
		class="streampay__payment-control streampay__payment-control__error streampay__alert streampay__alert--error">
		<p id="streampay-transaction-error-content"
			class="streampay__payment-control__error-content streampay__alert__message"></p>
	</div>

	<!-- Transaction Success -->
	<div id="streampay-transaction-success" class="streampay__payment-control streampay__payment-control__success"
		aria-hidden="true" role="alert">
		<div class="streampay__payment-control__success__icon">
			<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="#09b765">
				<path fill-rule="evenodd"
					d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
					clip-rule="evenodd" />
			</svg>
		</div>
		<h3 class="streampay__payment-control__success__heading"><?php esc_html_e( 'Transaction Done', 'StreamPay' ); ?></h3>
		<p class="streampay__payment-control__success__desc">
			<?php esc_html_e( 'Transaction with ', 'StreamPay' ); ?>
			<span class="streampay__payment-control__success__amount-value-wrapper text-med">
				<span id="streampay-transaction-value" class="streampay__payment-control__success__amount-value">-</span>
				<span class="streampay__payment-control__success__amount-value-unit"><?php esc_html_e( 'USDC', 'StreamPay' ); ?></span>
			</span>
			<?php esc_html_e( 'is made successfully', 'StreamPay' ); ?>
		</p>
	</div>
</section>



<!-- Templates -->
<!-- Error Template -->
<template id="streampay-alert-error">
	<li class="streampay__alert streampay__alert--error" role="alert">
		<button type="button" class="streampay__alert__dismiss" aria-lebel="Dismiss message" aria-pressed="false"
			aria-controls="">
			X
		</button>
		<div class="streampay__alert__icon-wrapper">
			<svg class="streampay__alert__icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20"
				viewBox="0 0 20 20" fill="currentColor">
				<path fill-rule="evenodd"
					d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
					clip-rule="evenodd" />
			</svg>
		</div>
		<div class="streampay__alert__message"></div>
	</li>
</template>

<!-- Success Template -->
<template id="streampay-alert-success">
	<li class="streampay__alert streampay__alert--success" role="alert">
		<button type="button" class="streampay__alert__dismiss" aria-lebel="Dismiss message" aria-pressed="false"
			aria-controls="">
			X
		</button>
		<div class="streampay__alert__icon-wrapper">
			<svg class="streampay__alert__icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
				fill="currentColor">
				<path fill-rule="evenodd"
					d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
					clip-rule="evenodd" />
			</svg>
		</div>
		<div class="streampay__alert__message"></div>
	</li>
</template>

<!-- Info Template -->
<template id="streampay-alert-info">
	<li class="streampay__alert streampay__alert--info" role="alert">
		<button type="button" class="streampay__alert__dismiss" aria-lebel="Dismiss message" aria-pressed="false"
			aria-controls="">
			X
		</button>
		<div class="streampay__alert__icon-wrapper">
			<svg class="streampay__alert__icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20"
				viewBox="0 0 20 20" fill="currentColor">
				<path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z" />
				<path fill-rule="evenodd"
					d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z"
					clip-rule="evenodd" />
			</svg>
		</div>
		<div class="streampay__alert__message"></div>
	</li>
</template>
