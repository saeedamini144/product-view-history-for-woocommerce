<?php
/**
 * Main plugin class for WebSemicolon Product View History for WooCommerce.
 *
 * @package WebSemicolon_Product_View_History_For_WooCommerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WebSemicolon_Product_View_History_For_WooCommerce_Plugin {

	private static $styles_printed = false;

	public function __construct() {
		add_action( 'init', array( $this, 'register_shortcode' ) );
		add_action( 'template_redirect', array( $this, 'save_viewed_product' ) );
		add_action( 'admin_menu', array( $this, 'register_admin_menu' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
	}

	public function register_shortcode() {
		add_shortcode( 'product_view_history_for_woocommerce', array( $this, 'render_products' ) );
	}

	public function register_admin_menu() {
		add_menu_page(
			__( 'Product View History for WooCommerce', 'websemicolon-product-view-history-for-woocommerce' ),
			__( 'Product View History for WooCommerce', 'websemicolon-product-view-history-for-woocommerce' ),
			'manage_options',
			'websemicolon-product-view-history-for-woocommerce',
			array( $this, 'render_admin_page' ),
			'dashicons-visibility',
			26
		);
	}

	public function register_settings() {
		register_setting(
			'product_view_history_for_woocommerce_settings',
			'product_view_history_for_woocommerce_display_count',
			array(
				'type'              => 'integer',
				'sanitize_callback' => 'absint',
			)
		);
		register_setting(
			'product_view_history_for_woocommerce_settings',
			'product_view_history_for_woocommerce_title',
			array(
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
			)
		);
		register_setting(
			'product_view_history_for_woocommerce_settings',
			'product_view_history_for_woocommerce_show_price',
			array(
				'type'              => 'string',
				'sanitize_callback' => function( $value ) {
					return in_array( strtolower( $value ), array( 'yes', 'no' ), true ) ? 'yes' : 'no';
				},
			)
		);
	}

	public function render_admin_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$display_count = absint( get_option( 'product_view_history_for_woocommerce_display_count', 5 ) );
		$display_title = sanitize_text_field( get_option( 'product_view_history_for_woocommerce_title', __( 'Product View History for WooCommerce', 'websemicolon-product-view-history-for-woocommerce' ) ) );
		$show_price = in_array( strtolower( get_option( 'product_view_history_for_woocommerce_show_price', 'yes' ) ), array( 'yes', 'no' ), true ) ? 'yes' : 'no';
		?>
		<div class="wrap">
			<h1><?php echo esc_html__( 'Product View History for WooCommerce', 'websemicolon-product-view-history-for-woocommerce' ); ?></h1>
			<p><?php echo esc_html__( 'Use the shortcode below to display the product view history anywhere on your site.', 'websemicolon-product-view-history-for-woocommerce' ); ?></p>
			<p><code>[product_view_history_for_woocommerce]</code></p>
			<p><?php echo esc_html__( 'You can also override the default settings with shortcode attributes:', 'websemicolon-product-view-history-for-woocommerce' ); ?></p>
			<p><code>[product_view_history_for_woocommerce limit="6" title="Recently Seen" show_price="no"]</code></p>
			<form method="post" action="options.php">
				<?php settings_fields( 'product_view_history_for_woocommerce_settings' ); ?>
				<table class="form-table">
					<tr>
						<th scope="row">
							<label for="product_view_history_for_woocommerce_display_count"><?php echo esc_html__( 'Number of products to display', 'websemicolon-product-view-history-for-woocommerce' ); ?></label>
						</th>
						<td>
							<input type="number" min="1" max="10" name="product_view_history_for_woocommerce_display_count" id="product_view_history_for_woocommerce_display_count" value="<?php echo esc_attr( $display_count ); ?>" class="small-text" />
							<p class="description"><?php echo esc_html__( 'Maximum value is 10.', 'websemicolon-product-view-history-for-woocommerce' ); ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="product_view_history_for_woocommerce_title"><?php echo esc_html__( 'Section title', 'websemicolon-product-view-history-for-woocommerce' ); ?></label>
						</th>
						<td>
							<input type="text" name="product_view_history_for_woocommerce_title" id="product_view_history_for_woocommerce_title" value="<?php echo esc_attr( $display_title ); ?>" class="regular-text" />
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="product_view_history_for_woocommerce_show_price"><?php echo esc_html__( 'Show product price', 'websemicolon-product-view-history-for-woocommerce' ); ?></label>
						</th>
						<td>
							<select name="product_view_history_for_woocommerce_show_price" id="product_view_history_for_woocommerce_show_price">
									<option value="yes" <?php selected( $show_price, 'yes' ); ?>><?php echo esc_html__( 'Yes', 'websemicolon-product-view-history-for-woocommerce' ); ?></option>
									<option value="no" <?php selected( $show_price, 'no' ); ?>><?php echo esc_html__( 'No', 'websemicolon-product-view-history-for-woocommerce' ); ?></option>
							</select>
						</td>
					</tr>
				</table>
				<?php submit_button(); ?>
			</form>
		</div>
		<?php
	}

	public function save_viewed_product() {
		if ( ! is_singular( 'product' ) || ! class_exists( 'WooCommerce' ) ) {
			return;
		}

		if ( ! isset( $GLOBALS['post'] ) || ! $GLOBALS['post'] instanceof WP_Post ) {
			return;
		}

		$product_id = absint( $GLOBALS['post']->ID );
		if ( ! $product_id ) {
			return;
		}

		$cookie_name = 'product_view_history_for_woocommerce';
		$viewed_products = array();

		if ( isset( $_COOKIE[ $cookie_name ] ) ) {
			$cookie_value = sanitize_text_field( wp_unslash( $_COOKIE[ $cookie_name ] ) );
			if ( is_string( $cookie_value ) ) {
				$decoded = json_decode( $cookie_value, true );
				if ( is_array( $decoded ) ) {
					$viewed_products = array_map( 'absint', $decoded );
				}
			}
		}

		$viewed_products = array_values( array_filter( $viewed_products ) );
		$viewed_products = array_values( array_unique( array_merge( $viewed_products, array( $product_id ) ) ) );
		$viewed_products = array_slice( $viewed_products, -10 );

		$cookie_value = wp_json_encode( $viewed_products );
		if ( ! $cookie_value ) {
			$cookie_value = '[]';
		}

		$expiration = time() + ( 30 * DAY_IN_SECONDS );
		$path       = defined( 'COOKIEPATH' ) ? COOKIEPATH : '/';
		$domain     = defined( 'COOKIE_DOMAIN' ) ? COOKIE_DOMAIN : '';

		if ( ! headers_sent() ) {
			setcookie( $cookie_name, $cookie_value, $expiration, $path, $domain, is_ssl(), true );
		}
	}

	public function render_products( $atts = array() ) {
		if ( ! class_exists( 'WooCommerce' ) ) {
			return '<p>' . esc_html__( 'WooCommerce is required for this plugin to work.', 'websemicolon-product-view-history-for-woocommerce' ) . '</p>';
		}

		$atts = shortcode_atts(
			array(
				'limit'      => get_option( 'product_view_history_for_woocommerce_display_count', 5 ),
				'title'      => get_option( 'product_view_history_for_woocommerce_title', __( 'Product View History for WooCommerce', 'websemicolon-product-view-history-for-woocommerce' ) ),
				'show_price' => get_option( 'product_view_history_for_woocommerce_show_price', 'yes' ),
			),
			$atts,
			'product_view_history_for_woocommerce'
		);

		$limit = min( 10, max( 1, absint( $atts['limit'] ) ) );
		$title = sanitize_text_field( $atts['title'] );
		$show_price = 'yes' === strtolower( $atts['show_price'] );

		$cookie_name = 'product_view_history_for_woocommerce';
		if ( ! isset( $_COOKIE[ $cookie_name ] ) ) {
			return '';
		}

		$cookie_value = sanitize_text_field( wp_unslash( $_COOKIE[ $cookie_name ] ) );
		if ( ! is_string( $cookie_value ) ) {
			return '';
		}

		$decoded = json_decode( $cookie_value, true );
		if ( ! is_array( $decoded ) || empty( $decoded ) ) {
			return '';
		}

		$viewed_products_ids = array_values( array_unique( array_map( 'absint', $decoded ) ) );
		$viewed_products_ids = array_reverse( $viewed_products_ids );
		$viewed_products_ids = array_slice( $viewed_products_ids, 0, $limit );

		if ( empty( $viewed_products_ids ) ) {
			return '';
		}

		$args = array(
			'post_type'           => 'product',
			'post_status'         => 'publish',
			'posts_per_page'      => count( $viewed_products_ids ),
			'post__in'            => $viewed_products_ids,
			'orderby'             => 'post__in',
			'ignore_sticky_posts' => true,
		);

		$products_query = new WP_Query( $args );
		if ( ! $products_query->have_posts() ) {
			wp_reset_postdata();
			return '';
		}

		if ( ! self::$styles_printed ) {
			wp_register_style( 'websemicolon-product-view-history-for-woocommerce', false, array(), '1.3' );
			wp_enqueue_style( 'websemicolon-product-view-history-for-woocommerce' );
			wp_add_inline_style( 'websemicolon-product-view-history-for-woocommerce', $this->get_inline_styles() );
			self::$styles_printed = true;
		}

		ob_start();
		?>
		<div class="product-view-history-for-woocommerce-wrapper">
			<span class="pvh-section-title"><?php echo esc_html( $title ); ?></span>
			<div class="pvh-product-grid">
				<?php
				while ( $products_query->have_posts() ) {
					$products_query->the_post();
					$product = wc_get_product( get_the_ID() );
					if ( ! $product ) {
						continue;
					}
					?>
					<div class="pvh-product-item">
						<a class="pvh-product-link" href="<?php echo esc_url( get_permalink() ); ?>">
							<?php echo get_the_post_thumbnail( get_the_ID(), 'woocommerce_thumbnail' ); ?>
							<h3 class="pvh-product-title"><?php echo esc_html( get_the_title() ); ?></h3>
							<?php if ( $show_price && $product->get_price_html() ) : ?>
								<span class="pvh-product-price"><?php echo wp_kses_post( $product->get_price_html() ); ?></span>
							<?php endif; ?>
						</a>
					</div>
					<?php
				}
				wp_reset_postdata();
				?>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}

	private function get_inline_styles() {
		return '
		.product-view-history-for-woocommerce-wrapper {
			margin: 2rem 0;
			padding: 1.25rem;
			background: #ffffff;
			border: 1px solid #e5e7eb;
			border-radius: 12px;
			box-shadow: 0 6px 18px rgba(15, 23, 42, 0.06);
		}
		.pvh-section-title {
			margin: 0 0 1rem;
			font-size: 1.25rem;
			font-weight: 700;
			line-height: 1.3;
			color: #111827;
		}
		.pvh-product-grid {
			display: grid;
			grid-template-columns: repeat(5, minmax(0, 1fr));
			gap: 1rem;
		}
		.pvh-product-item {
			background: #f9fafb;
			border: 1px solid #e5e7eb;
			border-radius: 10px;
			padding: 0.75rem;
			transition: transform 0.2s ease, box-shadow 0.2s ease;
		}
		.pvh-product-item:hover {
			transform: translateY(-2px);
			box-shadow: 0 10px 20px rgba(15, 23, 42, 0.08);
		}
		.pvh-product-link {
			display: block;
			color: inherit;
			text-decoration: none;
		}
		.pvh-product-link img {
			width: 100%;
			height: auto;
			max-height: 200px;
			object-fit: scale-down;
			border-radius: 8px;
			margin-bottom: 0.75rem;
			display: block;
		}
		.pvh-product-title {
			margin: 0 0 0.4rem;
			font-size: 0.95rem;
			font-weight: 600;
			line-height: 1.4;
			color: #1f2937;
		}
		.pvh-product-price {
			display: inline-block;
			font-size: 0.95rem;
			font-weight: 700;
			color: #2563eb;
		}
		@media (max-width: 1024px) {
			.pvh-product-grid {
				grid-template-columns: repeat(3, minmax(0, 1fr));
			}
		}
		@media (max-width: 767px) {
			.pvh-product-grid {
				grid-template-columns: repeat(2, minmax(0, 1fr));
			}
		}
		@media (max-width: 480px) {
			.pvh-product-grid {
				grid-template-columns: repeat(2, minmax(0, 1fr));
			}
		}
		';
	}
}
