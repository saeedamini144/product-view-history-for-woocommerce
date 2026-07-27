<?php
/**
 * Helper functions for Recently Viewed Products.
 *
 * @package Recently_Viewed_Products
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function recently_viewed_products_bootstrap() {
	if ( ! class_exists( 'Recently_Viewed_Products_Plugin' ) ) {
		require_once plugin_dir_path( __FILE__ ) . 'class-recently-viewed-products.php';
	}

	new Recently_Viewed_Products_Plugin();
}

add_action( 'plugins_loaded', 'recently_viewed_products_bootstrap' );
