<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the dashboard.
 *
 * @link       https://geotargetingwp.com/
 * @since      1.0.0
 *
 * @package    GeoTarget
 * @subpackage GeoTarget/includes
 */

use GeotCore\Setting\GeotSettings;
use GeotCore\Setting\GeotWizard;


/**
 * The core plugin class.
 *
 * This is used to define internationalization, dashboard-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    GeoTarget
 * @subpackage GeoTarget/includes
 * @author     Your Name <email@example.com>
 */
class Geot {

	/**
	 * Plugin Instance
	 * @since 1.0.0
	 * @var The Geot plugin instance
	 */
	protected static $_instance = null;
	/**
	 * @var GeotWP_Public $public
	 */
	public $public;
	/**
	 * @var GeotWP_VC $vc
	 */
	public $vc;
	/**
	 * @var GeotWP_Admin $admin
	 */
	public $admin;
	/**
	 * @var GeotWP_Settings $settings
	 */
	public $settings;
	/**
	 * @var GeotWP_Updater $updater
	 */
	public $updater;
	/**
	 * @var GeotWP_Widgets $widget
	 */
	public $widget;
	/**
	 * @var GeotWP_Menus $menus
	 */
	public $menus;
	/**
	 * @var GeotWP_Categories $cats
	 */
	public $taxs;
	/**
	 * Instance of GetFunctions
	 * @var object
	 */
	public $functions;
	/**
	 * @var GeoTarget_Gutenberg
	 */
	public $gutenberg;

	/**
	 * @var GeotWP_Elementor
	 */
	public $elementor;

	/*
	 * @var GeoTarget_Gutenberg
	 */
	public $divi;
	/**
	 * @var GeotWP_Metaboxes
	 */
	public $metaboxes;

	/*
	 * @var GeoTarget_Fusion
	 */
	public $fusion;

	/*
	 * @var GeotWP_Stats
	 */
	public $stats;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the Dashboard and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->load_dependencies();
		GeotSettings::init();
		GeotWizard::init();

		$this->set_locale();
		$this->set_objects_public();
		$this->register_shortcodes();
		$this->set_objects_admin();
		$this->register_ajax_calls();
		$this->set_addons();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - GeotWP_i18n. Defines internationalization functionality.
	 * - GeotWP_Admin. Defines all hooks for the dashboard.
	 * - GeotWP_Public. Defines all hooks for the public side of the site.
	 * - GeotWP_Function. Defines all main functions for targeting
	 * - GeotWP_shortcodes. Defines all plugin shortcodes
	 * - GeotWP_Widget. Defines plugin widget
	 * - GeotWP_Widgets. Target all widgets with geot
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		require_once GEOWP_PLUGIN_DIR . 'includes/functions.php';
		require_once GEOWP_PLUGIN_DIR . 'includes/functions-ajax.php';
		require_once GEOWP_PLUGIN_DIR . 'includes/class-geot-i18n.php';
		require_once GEOWP_PLUGIN_DIR . 'includes/class-geot-shortcodes.php';
		require_once GEOWP_PLUGIN_DIR . 'includes/class-geot-ajax-shortcodes.php';
		require_once GEOWP_PLUGIN_DIR . 'includes/class-geot-ajax.php';
		require_once GEOWP_PLUGIN_DIR . 'includes/class-geot-vc.php';
		require_once GEOWP_PLUGIN_DIR . 'includes/class-geot-divi.php';
		require_once GEOWP_PLUGIN_DIR . 'includes/class-geot-wpbeaver.php';
		require_once GEOWP_PLUGIN_DIR . 'includes/class-geot-gutenberg.php';
		require_once GEOWP_PLUGIN_DIR . 'includes/class-geot-elementor.php';
		require_once GEOWP_PLUGIN_DIR . 'includes/class-geot-fusion.php';
		require_once GEOWP_PLUGIN_DIR . 'includes/class-geot-rules.php';
		require_once GEOWP_PLUGIN_DIR . 'includes/class-geot-helper.php';
		require_once GEOWP_PLUGIN_DIR . 'includes/class-geot-updater.php';
		require_once GEOWP_PLUGIN_DIR . 'includes/class-geot-taxonomies.php';
		require_once GEOWP_PLUGIN_DIR . 'includes/class-geot-rocket.php';
		require_once GEOWP_PLUGIN_DIR . 'includes/class-geot-stats.php';
		require_once GEOWP_PLUGIN_DIR . 'includes/class-geot-wc.php';
		require_once GEOWP_PLUGIN_DIR . 'includes/class-geot-popups.php';


		require_once GEOWP_PLUGIN_DIR . 'admin/includes/class-geot-menus.php';
		require_once GEOWP_PLUGIN_DIR . 'public/class-geot-public.php';
		require_once GEOWP_PLUGIN_DIR . 'admin/includes/class-geot-widgets.php';
		require_once GEOWP_PLUGIN_DIR . 'admin/includes/class-geot-dropdown-widget.php';

		if ( is_admin() ) {
			require_once GEOWP_PLUGIN_DIR . 'admin/class-geot-admin.php';
			require_once GEOWP_PLUGIN_DIR . 'admin/class-geot-settings.php';
			require_once GEOWP_PLUGIN_DIR . 'admin/includes/class-geot-metaboxes.php';
		}
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the GeoTarget_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new GeotWP_i18n();
		$plugin_i18n->set_domain( 'geot' );

		add_action( 'plugins_loaded', [ $plugin_i18n, 'load_plugin_textdomain' ] );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_objects_public() {

		$this->public 		= new GeotWP_Public();
		$this->vc 			= new GeotWP_VC();
		$this->gutenberg 	= new GeotWP_Gutenberg();
		$this->elementor 	= new GeotWP_Elementor();
		$this->divi 		= new GeotWP_Divi();
		$this->divi 		= new GeotWP_Fusion();
		$this->beaver 		= new GeotWP_WPBeaver();
		$this->taxs 		= new GeotWP_Taxonomies();
		$this->menus 		= new GeotWP_Menus();
		$this->widget 		= new GeotWP_Widgets();
		$this->rocket 		= new GeotWP_Rocket();
		$this->stats 		= new GeotWP_Stats();
		$this->wc 			= new GeotWP_WC();
	}

	/**
	 * Register shortcodes
	 * @access   private
	 */
	private function register_shortcodes() {
		$shortcodes      = new GeotWP_Shortcodes();
		$ajax_shortcodes = new GeotWP_Ajax_Shortcodes();
	}

	/**
	 * Register all of the hooks related to the dashboard functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_objects_admin() {

		if ( ! is_admin() ) {
			return;
		}

		$this->admin     = new GeotWP_Admin();
		$this->settings  = new GeotWP_Settings();
		$this->metaboxes = new GeotWP_Metaboxes();
		$this->updater   = new GeotWP_Updater();

	}

	/**
	 * Register Ajax Calls
	 * @access   private
	 */
	private function register_ajax_calls() {
		$this->ajax = new GeotWP_Ajax();
	}

	public function set_addons() {

		$opts = geotwp_addons();

		foreach ( $opts as $key => $value ) {
			if ( $value != 1 ) {
				continue;
			}

			$addon_index = apply_filters( 'geot/addons/file', GEOWP_ADDONS_DIR . $key . '/' . $key . '.php' );

			if ( file_exists( $addon_index ) ) {
				require $addon_index;
			}
		}
	}


	/**
	 * Main Geot Instance
	 *
	 * Ensures only one instance of WSI is loaded or can be loaded.
	 *
	 * @return Geot - Main instance
	 * @see GEOT()
	 * @since 1.0.0
	 * @static
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Cloning is forbidden.
	 * @since 1.0.0
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'wsi' ), '2.1' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 * @since 1.0.0
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'wsi' ), '2.1' );
	}

	/**
	 * Auto-load in-accessible properties on demand.
	 *
	 * @param mixed $key
	 *
	 * @return mixed
	 * @since 1.0.0
	 */
	public function __get( $key ) {
		if ( in_array( $key, [ 'payment_gateways', 'shipping', 'mailer', 'checkout' ] ) ) {
			return $this->$key();
		}
	}
}
