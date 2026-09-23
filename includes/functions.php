<?php
/**
 * Helper functions for WebSemicolon Product View History for WooCommerce.
 *
 * @package WebSemicolon_Product_View_History_For_WooCommerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function product_view_history_for_woocommerce_bootstrap() {
	if ( ! class_exists( 'WebSemicolon_Product_View_History_For_WooCommerce_Plugin' ) ) {
		require_once plugin_dir_path( __FILE__ ) . 'class-websemicolon-product-view-history-for-woocommerce.php';
	}

	new WebSemicolon_Product_View_History_For_WooCommerce_Plugin();
}

add_action( 'plugins_loaded', 'product_view_history_for_woocommerce_bootstrap' );
