=== Recently Viewed Products ===
Contributors: saeedamini
Donate link: https://github.com/saeedamini
Tags: woocommerce, products, recently viewed, shortcode
Requires at least: 6.0
Tested up to: 6.5
Requires PHP: 7.4
Stable tag: 1.3
License: GPL-2.0-or-later

Displays a clean and responsive grid of the products a visitor has recently viewed on a WooCommerce store.

== Description ==
Recently Viewed Products is a lightweight WooCommerce plugin that tracks the products a visitor views and shows them in a polished product grid using the shortcode [recently_viewed_products].

It is ideal for increasing engagement and encouraging repeat purchases by reminding visitors of products they have already explored. The plugin is fully responsive, works with most themes, and can be customized from the WordPress admin area.

== Installation ==
1. Upload the plugin files to the /wp-content/plugins/recently-viewed-products directory.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Place the shortcode [recently_viewed_products] on any page, post, or template where you want the section to appear.

== Usage ==
Use the default shortcode:

[recently_viewed_products]

You can customize the output:

[recently_viewed_products limit="6" title="Recently Seen" show_price="no"]

== Frequently Asked Questions ==

= Does this work without WooCommerce? =
No. The plugin requires WooCommerce to be active.

= Can I change the number of products shown? =
Yes. You can set the number of products from the plugin settings screen or use the limit attribute in the shortcode.

= Can I hide the product price? =
Yes. Use show_price="no" in the shortcode.

== Changelog ==
= 1.3 =
* Added advanced admin settings for title, price display, and number of products.
* Increased maximum supported display count to 10.
* Improved mobile responsiveness for two-column layout.
