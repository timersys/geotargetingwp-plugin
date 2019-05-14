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
use GeotFunctions\Setting\GeotSettings;
use GeotFunctions\Setting\GeotWizard;


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
	 * @var Geot_Public $public
	 */
	public $public;

	/**
	 * @var Geot_VC $vc
	 */
	public $vc;

	/**
	 * @var Geot_Admin $admin
	 */
	public $admin;

	/**
	 * @var Geot_Settings $settings
	 */
	public $settings;

	/**
	 * @var Geot_Updater $updater
	 */
	public $updater;

	/**
	 * @var Geot_Widgets $widget
	 */
	public $widget;

	/**
	 * @var Geot_Menus $menus
	 */
	public $menus;

	/**
	 * @var Geot_Categories $cats
	 */
	public $taxs;

	/**
	 * Instance of GetFunctions
	 * @var object
	 */
	public $functions;

	/**
	 * Plugin Instance
	 * @since 1.0.0
	 * @var The Geot plugin instance
	 */
	protected static $_instance = null;
	
	/**
	 * @var GeoTarget_Gutenberg
	 */
	public $gutenberg;

	/**
	 * @var GeoTarget_Elementor
	 */
	public $elementor;

	/*
	 * @var GeoTarget_Gutenberg
	 */
	public $divi;


	/**
	 * Main Geot Instance
	 *
	 * Ensures only one instance of WSI is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see GEOT()
	 * @return Geot - Main instance
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
	 * @param mixed $key
	 * @since 1.0.0
	 * @return mixed
	 */
	public function __get( $key ) {
		if ( in_array( $key, array( 'payment_gateways', 'shipping', 'mailer', 'checkout' ) ) ) {
			return $this->$key();
		}
	}

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
		$this->set_rules();
		$this->set_addons();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Geot_i18n. Defines internationalization functionality.
	 * - Geot_Admin. Defines all hooks for the dashboard.
	 * - Geot_Public. Defines all hooks for the public side of the site.
	 * - Geot_Function. Defines all main functions for targeting
	 * - Geot_shortcodes. Defines all plugin shortcodes
	 * - Geot_Widget. Defines plugin widget
	 * - Geot_Widgets. Target all widgets with geot
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		require GEOT_PLUGIN_DIR . 'vendor/autoload.php';
		require_once GEOT_PLUGIN_DIR . 'includes/functions.php';
		require_once GEOT_PLUGIN_DIR . 'includes/functions-ajax.php';
		require_once GEOT_PLUGIN_DIR . 'includes/class-geot-i18n.php';
		require_once GEOT_PLUGIN_DIR . 'includes/class-geot-shortcodes.php';
		require_once GEOT_PLUGIN_DIR . 'includes/class-geot-ajax-shortcodes.php';
		require_once GEOT_PLUGIN_DIR . 'includes/class-geot-ajax.php';
		require_once GEOT_PLUGIN_DIR . 'includes/class-geot-vc.php';
		require_once GEOT_PLUGIN_DIR . 'includes/class-geot-divi.php';
		require_once GEOT_PLUGIN_DIR . 'includes/class-geot-gutenberg.php';
		require_once GEOT_PLUGIN_DIR . 'includes/class-geot-elementor.php';
		require_once GEOT_PLUGIN_DIR . 'includes/class-geot-helpers.php';
		require_once GEOT_PLUGIN_DIR . 'includes/class-geot-updater.php';
		require_once GEOT_PLUGIN_DIR . 'includes/class-geot-taxonomies.php';

		require_once GEOT_PLUGIN_DIR . 'public/class-geot-public.php';

		if( is_admin() ) {
			require_once GEOT_PLUGIN_DIR . 'admin/class-geot-admin.php';
			require_once GEOT_PLUGIN_DIR . 'admin/class-geot-settings.php';
			require_once GEOT_PLUGIN_DIR . 'admin/includes/class-geot-dropdown-widget.php';
			require_once GEOT_PLUGIN_DIR . 'admin/includes/class-geot-widgets.php';
			require_once GEOT_PLUGIN_DIR . 'admin/includes/class-geot-menus.php';	
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

		$plugin_i18n = new Geot_i18n();
		$plugin_i18n->set_domain( 'geot' );

		add_action( 'plugins_loaded', [ $plugin_i18n, 'load_plugin_textdomain'] );
	}

	/**
	 * Register all of the hooks related to the dashboard functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_objects_admin() {

		if( !is_admin() ) return;

		$this->admin 	= new Geot_Admin();
		$this->settings = new Geot_Settings();
		$this->updater 	= new Geot_Updater();
		$this->widget 	= new Geot_Widgets();
		$this->menus = new Geot_Menus();

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_objects_public() {

		$this->public 		= new Geot_Public();
		$this->vc 			= new Geot_VC();
		$this->gutenberg 	= new Geot_Gutenberg();

		$this->elementor 	= new Geot_Elementor();
		$this->divi 		= new Geot_Divi();
		$this->taxs 		= new Geot_Taxonomies();
	}

	/**
	 * Register shortcodes
	 * @access   private
	 */
	private function register_shortcodes() {
		$shortcodes 		= new Geot_Shortcodes();
		$ajax_shortcodes 	= new Geot_Ajax_Shortcodes();
	}

	/**
	 * Register Ajax Calls
	 * @access   private
	 */
	private function register_ajax_calls() {
		$this->ajax = new Geot_Ajax();
	}


	private function set_rules() {
		// Popups rules // PENDIENTE
		add_action( 'spu/rules/print_geot_country_field', [ 'Spu_Helper', 'print_select' ], 10, 2 );
		add_action( 'spu/rules/print_geot_country_region_field', [ 'Spu_Helper', 'print_select' ], 10, 2 );
		add_action( 'spu/rules/print_geot_city_region_field', [ 'Spu_Helper', 'print_select' ], 10, 2 );
		add_action( 'spu/rules/print_geot_state_field', [ 'Spu_Helper', 'print_textfield' ], 10, 1 );
	}


	public function set_addons() {
		$defaults = [ 
						'geo-flags'		=> '0',
						'geo-links'		=> '0',
						'geo-redirects'	=> '0',
						'geo-blocker'	=> '0',
					];

		$defaults = apply_filters('geot/addons/defaults', $defaults);

		$opts = geot_pro_addons();
		$opts = geot_wp_parse_args( $opts,  $defaults );

		foreach($opts as $key => $value) {
			if( $value != 1 ) continue;

			$addon_index = apply_filters('geot/addons/file', GEOT_ADDONS_DIR . $key . '/' . $key . '.php');

			if( file_exists( $addon_index ) )
				require $addon_index;
		}
	}
}
