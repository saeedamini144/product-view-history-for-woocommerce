<?php
/**
 * Plugin Name: Product View History for WooCommerce
 * Plugin URI: https://github.com/saeedamini/product-view-history-for-woocommerce
 * Description: Displays a clean, responsive grid of the products a visitor has recently viewed on your WooCommerce store.
 * Version: 1.3
 * Author: Saeed Amini
 * Author URI: https://github.com/saeedamini
 * Text Domain: product-view-history-for-woocommerce
 * License: GPL-2.0-or-later
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * Tested up to: 7.1
 * Requires Plugins: woocommerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once plugin_dir_path( __FILE__ ) . 'includes/functions.php';