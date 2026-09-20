<?php
/**
 * Helper functions for Product View History for WooCommerce.
 *
 * @package Product_View_History_For_WooCommerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function product_view_history_for_woocommerce_bootstrap() {
	if ( ! class_exists( 'Product_View_History_For_WooCommerce_Plugin' ) ) {
		require_once plugin_dir_path( __FILE__ ) . 'class-product-view-history-for-woocommerce.php';
	}

	new Product_View_History_For_WooCommerce_Plugin();
}

add_action( 'plugins_loaded', 'product_view_history_for_woocommerce_bootstrap' );
