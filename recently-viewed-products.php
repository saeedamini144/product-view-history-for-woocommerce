<?php
/**
 * Plugin Name: Recently Viewed Products
 * Plugin URI: https://github.com/saeedamini/recently-viewed-products
 * Description: Displays a clean, responsive grid of the products a visitor has recently viewed on your WooCommerce store.
 * Version: 1.3
 * Author: Saeed Amini
 * Author URI: https://github.com/saeedamini
 * Text Domain: recently-viewed-products
 * License: GPL-2.0-or-later
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * Tested up to: 6.5
 * Requires Plugins: woocommerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

load_plugin_textdomain( 'recently-viewed-products', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

require_once plugin_dir_path( __FILE__ ) . 'includes/functions.php';