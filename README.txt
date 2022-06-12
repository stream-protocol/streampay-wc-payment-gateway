=== StreamPay - WooCommerce Payment Gateway ===

Web3 Payment Plugin for WordPress websites using Solana blockchain: A completely free, open source, permissionless, censorship-resistant, decentralized WordPress/WooCommerce payment gateway extension plugin using the power of the Solana blockchain.

Contributors: streampayplugins
Donate link: https://streamprotocol.org
Tags: solana pay, stream protocol, woocommerce, payment, payment gateway, solana, crypto, phantom, phantom wallet
Requires at least: 4.7
Tested up to: 5.9
Stable tag: 1.0.0
Requires PHP: 7.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Stream Pay / Solana Pay for Woocommerce websites. Permissionless, open source, and fast payments.

== Description ==
Allows for Stream Pay Payment Gateway on WooCommerce Stores. Currently supports STRM, USDC on Solana and Phantom wallet. More coming soon.

Adds a connect Phantom wallet button to the checkout page, after connecting wallet, users may pay in SOL, STRM and USDC tokens on Solana after clicking StreamPay button.

== Installation ==

1. Download this repository as .zip and upload to the `/wp-content/plugins/` directory
or Use WordPress’ Add New Plugin feature, search “streampay”,
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Add in your merchant wallet address and adjust any settings you would like

== Frequently Asked Questions ==

= Why Stream Pay? =

Stream Pay is a permissionless, open source and micro-fee way for merchants to allow purchase on their store to cryptocurrency users.

You can learn more on StreamPay and Solanapay.com 

== Screenshots ==

1. User frontend with Wallet connected at checkout and Solana Pay button displaying.
2. WordPress administrator/VooCommerce merchant settings display.

== Changelog ==

= 1.0 =
* Initial launch of plugin supporting Phantom wallet and USDC on Solana.

== Implementation ==

1. Add Solana and Stream Token (STRM) payment feature
3. Create Cryptocurrency Payments & Cross-Chain Support 
2. Develop. "company" Fee wallet option, and store % commission features
3. Create STRM_TOKEN_KEY 
4. Add StreamPay styles, color presets, scss, css: #2D3139
5. Create API & Webpack

== Need to Fix ==

Fix composer autoload! Fatal error: Cannot redeclare composerRequirea04b97bdcca75453ab894eb4b3c770e2()
