<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://timersys.com
 * @since      1.0.0
 *
 * @package    Geotr
 * @subpackage Geotr/includes
 */
use GeotFunctions\Setting\GeotSettings;

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 *
 * @since      1.0.0
 * @package    Geotr
 * @subpackage Geotr/includes
 * @author     Damian Logghe <damian@timersys.com>
 */
class Geotr {

	/**
	 * Public Class instance
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      Geotr_Public    $public Public class instance
	 */
	public $public;

	/**
	 * Admin Class instance
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      Geotr_Admin    $public Admin class instance
	 */
	public $admin;

	/**
	 * Settings Class instance
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      Geotr_Settings    $settings Settings class instance
	 */
	public $settings;

	/**
	 * Metaboxes Class instance
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      Geotr_Metaboxes    $public Metaboxes class instance
	 */
	public $metaboxes;

	/**
	 * Plugin Instance
	 * @since 1.0.0
	 * @var The Fbl plugin instance
	 */
	protected static $_instance = null;

	/**
	 * Main plugin_name Instance
	 *
	 * Ensures only one instance of WSI is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see Geotr()
	 * @return plugin_name - Main instance
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
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->load_dependencies();
		$this->set_locale();
		$this->set_objects_admin();
		$this->set_objects_public();
	}

	/**
	 * Load the required dependencies for this plugin.
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {
		require_once GEOTR_PLUGIN_DIR . 'includes/functions.php';
		require_once GEOTR_PLUGIN_DIR . 'includes/class-geotr-i18n.php';
		require_once GEOTR_PLUGIN_DIR . 'includes/class-geotr-cpt.php';
		require_once GEOTR_PLUGIN_DIR . 'includes/class-geotr-helper.php';
		require_once GEOTR_PLUGIN_DIR . 'public/class-geotr-public.php';

		if( is_admin() ) {
			require_once GEOTR_PLUGIN_DIR . 'admin/class-geotr-admin.php';
			require_once GEOTR_PLUGIN_DIR . 'admin/class-geotr-settings.php';
		}
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Geotr_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Geotr_i18n();
		$plugin_i18n->set_domain( 'geotr' );

		add_action( 'plugins_loaded', [ $plugin_i18n, 'load_plugin_textdomain' ] );

	}


	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_objects_admin() {

		if( !is_admin() ) return;

		$this->admin = new Geotr_Admin();
		$this->settings = new Geotr_Settings();

		Geot_Rules::set_rules_fields();
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_objects_public() {
		$this->public = new Geotr_Public();
	}

}
