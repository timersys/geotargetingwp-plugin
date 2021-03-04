<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://geotargetingwp.com/geotargeting-pro
 * @since      1.0.0
 *
 * @package    GeoTarget
 * @subpackage GeoTarget/public
 */

use GeotCore\Session\GeotSession;
use function GeotCore\is_builder;

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the dashboard-specific stylesheet and JavaScript.
 *
 * @package    GeoTarget
 * @subpackage GeoTarget/public
 * @author     Your Name <email@example.com>
 */
class GeotWP_Public {
	/**
	 * Plugin settings
	 * @var array
	 */
	protected $opts;
	protected $geot_opts;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 *
	 */
	public function __construct() {

		$this->opts      = geot_settings();
		$this->geot_opts = geotwp_settings();

		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_styles' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );

		add_action( 'wp_footer', [ $this, 'print_overlay' ], 11 );
		add_action( 'wp_footer', [ $this, 'print_debug_info' ], 999 );

		// disable in rest
		if( apply_filters( 'geot/disable_in_rest', true ) || ! defined( 'REST_REQUEST' ) || ! REST_REQUEST  ) {
			add_filter( 'posts_where', [ $this, 'handle_geotargeted_posts' ], PHP_INT_MAX );
			add_filter( 'the_content', [ $this, 'check_if_geotargeted_content' ], 99 );
			add_filter( 'woocommerce_short_description', [ $this, 'check_if_geotargeted_content' ], 99 );
			//woocommerce
			add_filter( 'woocommerce_product_related_posts_query', [ $this, 'woocommerce_related_products' ], 99 );
			// if no ajax also filter purchase capabilities. If ajax, let's js handle it
			if ( ! isset( $this->opts['ajax_mode'] ) || $this->opts['ajax_mode'] != '1' ) {
				add_action( 'wp', [ $this, 'remove_woo_product' ] );
				add_filter( 'wp', [ $this, 'disable_woo_product' ] );
			}
		}
		// exclude js from autooptimize, too many problems
		add_filter('autoptimize_filter_js_exclude', [ $this, 'exclude_from_ao_cache' ] );

		// register dropdown widget
		add_action( 'widgets_init', [ $this, 'register_widgets' ] );
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( 'geot-css', plugin_dir_url( __FILE__ ) . 'css/geotarget-public.css', [], false, 'all' );

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		$src = 'js/geotarget-public.js';

		wp_enqueue_script( 'geot-js', plugin_dir_url( __FILE__ ) . $src, [ 'jquery' ], GEOWP_VERSION, true );
		wp_enqueue_script( 'geot-slick', plugin_dir_url( __FILE__ ) . 'js/min/selectize.min.js', [ 'jquery' ], GEOWP_VERSION, true );
		$wpml_lang = defined( 'ICL_LANGUAGE_CODE' ) ? ICL_LANGUAGE_CODE : '';
		$ajax_url = admin_url( 'admin-ajax.php' );
		if ( ! empty( $wpml_lang ) ) {
			$ajax_url = add_query_arg( 'lang', $wpml_lang, $ajax_url );
		}
		wp_localize_script( 'geot-js', 'geot', [
			'ajax_url'          => $ajax_url,
			'ajax'              => isset( $this->opts['ajax_mode'] ) ?  $this->opts['ajax_mode'] : '',
			'pid'               => get_queried_object_id(),
			'is_archive'        => is_archive(),
			'is_search'         => is_search(),
			'is_singular'       => is_singular(),
			'is_front_page'     => is_front_page(),
			'is_category'       => is_category(),
			'is_page'           => is_page(),
			'is_single'         => is_single(),
			'is_builder'        => is_builder(),
			'has_geo_posts'     => ! empty( GeotWP_Helper::get_geotarget_posts() ),
			'dropdown_search'   => apply_filters( 'geot/dropdown_widget/disable_search', false ),
			'dropdown_redirect' => apply_filters( 'geot/dropdown_widget/redirect_url', '' ),
			'elementor_popup'	=> apply_filters( 'geot/elementor/popup', true ),
			'hide_class'	     => apply_filters( 'geot/ajax_mode/hide_class', '' ),
			'hide_override_class'	    => apply_filters( 'geot/ajax_mode/hide_override_class', '' ),
			'remove_class'	    => apply_filters( 'geot/ajax_mode/remove_class', '' ),
			'remove_override_class'	    => apply_filters( 'geot/ajax_mode/remove_override_class', '' ),

			'geoloc_enable'		=> isset($this->opts['geolocation']) ? $this->opts['geolocation'] : 0,
			'geoloc_force'		=> isset($this->opts['force_geot']) ? $this->opts['force_geot'] : '',
			'geoloc_fail'		=> esc_html__( 'Geolocation is not supported by this browser', 'geot' ),
			'geoloc_img_opera'		=> '<img src="' . GEOWP_PLUGIN_URL . 'public/images/geolocation_opera.gif" alt="Geolocation Opera" />',
			'geoloc_img_safari'		=> '<img src="' . GEOWP_PLUGIN_URL . 'public/images/geolocation_safari.gif" alt="Geolocation Safari" />',
			'geoloc_img_chrome'		=> '<img src="' . GEOWP_PLUGIN_URL . 'public/images/geolocation_chrome.gif" alt="Geolocation Chrome" />',
			'geoloc_img_firefox'	=> '<img src="' . GEOWP_PLUGIN_URL . 'public/images/geolocation_firefox.gif" alt="Geolocation Firefox" />',
			'geoloc_img_edge'	=> '<img src="' . GEOWP_PLUGIN_URL . 'public/images/geolocation_edge.gif" alt="Geolocation Edge" />',
			
			'geoloc_consent_opera'		=> '<img src="' . GEOWP_PLUGIN_URL . 'public/images/give_consent_opera.gif" alt="Consent Opera" />',
			'geoloc_consent_safari'		=> '<img src="' . GEOWP_PLUGIN_URL . 'public/images/give_consent_chrome.gif" alt="Consent Safari" />',
			'geoloc_consent_chrome'		=> '<img src="' . GEOWP_PLUGIN_URL . 'public/images/give_consent_chrome.gif" alt="Consent Chrome" />',
			'geoloc_consent_firefox'	=> '<img src="' . GEOWP_PLUGIN_URL . 'public/images/give_consent_firefox.gif" alt="Consent Firefox" />',
			'geoloc_consent_edge'	=> '<img src="' . GEOWP_PLUGIN_URL . 'public/images/give_consent_edge.gif" alt="Consent Edge" />',
		] );
	}

	/**
	 * Modify query for woocommerce related products
	 *
	 * @param $query
	 *
	 * @return mixed
	 */
	public function woocommerce_related_products( $query ) {
		$query['where'] = $this->handle_geotargeted_posts( $query['where'], true );

		return $query;
	}

	/**
	 * Filter where argument of main query to exclude geotargeted posts
	 *
	 * @param $where
	 *
	 * @param bool $woocommerce_related . Related posts from woocommerce add alias to table, so regular key won't work
	 *
	 * @return string
	 */
	public function handle_geotargeted_posts( $where, $woocommerce_related = false ) {
		global $wpdb;

		// let users cancel the removal of posts
		// for example they can check if is_search() and show the post in search results
		if ( apply_filters( 'geot/cancel_posts_where', false, $where ) ) {
			return $where;
		}
		// If we have ajax mode we can't filter results before they are printed to avoid caching issues
		// but we need to allow further processing in case a custom ajax call it's done
		if ( isset( $this->opts['ajax_mode'] ) && $this->opts['ajax_mode'] == '1' && ! apply_filters('geot/allow_custom_ajax_call', false ) ) {
			return $where;
		}
		// If we allow custom ajax, be sure we are not doing our ajax call.
		if ( defined( 'DOING_GEOT_AJAX' ) ) {
			return $where;
		}
		// Only in front or ajax calls
		if ( ! is_admin() || apply_filters('geot/allow_custom_ajax_call', false ) ) {
			// Get all posts that are being geotargeted
			$post_to_exclude = $this->get_geotargeted_posts();
			$key             = "{$wpdb->posts}.ID";
			if ( $woocommerce_related ) {
				$key = "p.ID";
			}
			if ( ! empty( $post_to_exclude ) ) {
				$where .= " AND {$key} NOT IN ('" . implode( "','", $post_to_exclude ) . "')";
				// Sticky posts needs to be filtered differently
				add_filter( 'option_sticky_posts', function ( $posts ) use ( $post_to_exclude ) {
					if ( ! empty( $posts ) ) {
						foreach ( $posts as $key => $id ) {
							if ( in_array( $id, $post_to_exclude ) ) {
								unset( $posts[ $key ] );
							}
						}
					}

					return $posts;
				} );
			}
		}

		return $where;
	}

	/**
	 * Then we get all the posts with geotarget options and
	 * check each of them to see which one we need to exclude from loop
	 *
	 * @return array|void
	 */
	private function get_geotargeted_posts() {
		global $wpdb;

		$posts_to_exclude = [];
		// get all posts with geo options set ( ideally would be to retrieve just for the post type queried but I can't get post_type
		$geot_posts = GeotWP_Helper::get_geotarget_posts();

		if ( $geot_posts ) {
			foreach ( $geot_posts as $p ) {
				$options = unserialize( $p->geot_options );
				// if remove for loop is off continue
				if ( ! isset( $options['geot_remove_post'] )
				     || '1' != $options['geot_remove_post']
				) {
					continue;
				}


				$target = GeotWP_Helper::user_is_targeted( $options, $p->ID );
				if ( $target ) {
					$posts_to_exclude[] = $p->ID;
				}

			}
		}

		return $posts_to_exclude;
	}

	/**
	 * Function that filter the_content and show message if post is geotargeted
	 *
	 * @param $content
	 *
	 * @return mixed|void
	 */
	public function check_if_geotargeted_content( $content ) {
		global $post;

		if ( isset( $this->opts['ajax_mode'] ) && $this->opts['ajax_mode'] == '1' ) {
			return $content;
		}

		if ( ! isset( $post->ID ) ) {
			return $content;
		}

		$opts = get_post_meta( $post->ID, 'geot_options', true );

		if ( GeotWP_Helper::user_is_targeted( $opts, $post->ID ) ) {
			return apply_filters( 'geot/forbidden_text', '<p>' . $opts['forbidden_text'] . '</p>', $content, $post );
		}

		return $content;
	}

	/**
	 * Check if user is targeted for post and disable woo product
	 * On ajax mode this function will consume an extra credit to the user
	 * if cache mode is off
	 */
	public function disable_woo_product() {
		global $post;
		if ( ! class_exists( 'WooCommerce' ) || ! isset( $post->ID ) ) {
			return;
		}

		if ( ! is_product() ) {
			return;
		}

		$opts = get_post_meta( $post->ID, 'geot_options', true );

		if ( ! isset( $opts['geot_include_mode'] ) || empty( $opts['geot_include_mode'] ) ) {
			return;
		}

		if ( GeotWP_Helper::user_is_targeted( $opts, $post->ID ) ) {
			add_filter( 'woocommerce_is_purchasable', '__return_false' );
		}
	}

	/**
	 * if user is targeted remove product from cart
	 *
	 */
	public function remove_woo_product() {

		if ( is_admin() || ! class_exists( 'WooCommerce' ) || ( isset(WC()->cart ) && WC()->cart->is_empty() ) ) {
			return;
		}

		if ( ! is_cart() && ! is_checkout() ) {
			return;
		}

		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
			$_product = $cart_item['data'];
			$post_id  = $_product->get_id();

			$opts = get_post_meta( $post_id, 'geot_options', true );

			if ( ! isset( $opts['geot_include_mode'] ) || empty( $opts['geot_include_mode'] ) ) {
				continue;
			}

			if ( GeotWP_Helper::user_is_targeted( $opts, $post_id ) ) {
				WC()->cart->remove_cart_item( $cart_item_key );
			}
		}
	}


	public function print_overlay() {
		echo '<!-- Geotargeting GeoLocation START -->
		<div class="geotloc_overlay_box" style="display: none;">
			<div class="geotloc_overlay_remove"></div>
			<div class="geotloc_overlay_content">
			<h3>'. __('Please share your location to continue', 'geot') . '</h3>
			</div>
		</div>
		<!-- Geotargeting GeoLocation END -->';
	}


	/**
	 * Print current user data in footer
	 */
	public function print_debug_info() {
		$opts = geot_settings();
		// only show if we use get parameter
		if( ! isset( $_GET['geot_debug'] ) ) {
			return;
		}
		?>
		<!-- Geotargeting plugin Debug Info START-->
		<div id="geot-debug-info" style="display: none;"><!--<?php if ( empty( $this->opts['ajax_mode'] ) ) {
				echo geot_debug_data();
			} ?>--></div>
		<!-- Geotargeting plugin Debug Info END-->
		<?php
	}

	/**
	 * Register all plugin widgets
	 * @return mixed
	 */
	public function register_widgets() {
		register_widget( 'GeotWP_Widget' );
	}

	/**
	 * Automatically exclude our file from autooptimize
	 * @return mixed|void
	 */
	public function exclude_from_ao_cache( $js ) {
		if( is_array( $js ) ) {
			$js[] = '/wp-content/plugins/geotargetingwp/public/js/geotarget-public.js';
		} else {
			$js .= ',/wp-content/plugins/geotargetingwp/public/js/geotarget-public.js';
		}

		return $js;
	}
}
