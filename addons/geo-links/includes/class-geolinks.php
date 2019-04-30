<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the dashboard.
 *
 * @link       https://geotargetingwp.com/geo-links
 * @since      1.0.0
 *
 * @package    GeoLinks
 * @subpackage GeoLinks/includes
 */

use GeotFunctions\Setting\GeotSettings;


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
 * @package    GeoLinks
 * @subpackage GeoLinks/includes
 * @author     Your Name <email@example.com>
 */
class GeoLinks {

	/**
	 * @var GeoLinks_Redirect $redirect
	 */
	public $redirect;

	/**
	 * @var GeoLinks_Admin $admin
	 */
	public $admin;
	/**
	 * @var GeoLinks_Settings $settings
	 */
	public $settings;


	/**
	 * Plugin Instance
	 * @since 1.0.0
	 * @var The Geot plugin instance
	 */
	protected static $_instance = null;

	/**
	 * Main Geot Instance
	 *
	 * Ensures only one instance of WSI is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see GEOL()
	 * @return GeoLinks
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
	 * @since 1.0.0
	 * @return mixed
	 */
	public function __get( $key ) {
		if ( in_array( $key, [ 'payment_gateways', 'shipping', 'mailer', 'checkout' ] ) ) {
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
		$this->set_locale();
		$this->set_objects();
	}

	/**
	 * Load the required dependencies for this plugin.
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		require_once GEOL_PLUGIN_DIR . 'includes/functions.php';
		require_once GEOL_PLUGIN_DIR . 'includes/class-geolinks-i18n.php';
		require_once GEOL_PLUGIN_DIR . 'includes/admin/class-geolinks-cache.php';
		require_once GEOL_PLUGIN_DIR . 'includes/global/class-geolinks-cpt.php';
		require_once GEOL_PLUGIN_DIR . 'includes/admin/class-geolinks-permalinks.php';
		require_once GEOL_PLUGIN_DIR . 'includes/admin/class-geolinks-settings.php';
		require_once GEOL_PLUGIN_DIR . 'includes/admin/class-geolinks-notices.php';
		require_once GEOL_PLUGIN_DIR . 'includes/admin/class-geolinks-admin.php';
		require_once GEOL_PLUGIN_DIR . 'includes/admin/class-geolinks-ajax.php';
		require_once GEOL_PLUGIN_DIR . 'includes/public/class-geolinks-redirect.php';
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the GeoLinks_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new GeoLinks_i18n();
		$plugin_i18n->set_domain( 'geolinks' );

		add_action( 'plugins_loaded', [ $plugin_i18n, 'load_plugin_textdomain' ] );

	}

	/**
	 * Set all global objects
	 */
	private function set_objects() {
		$this->admin    = new GeoLinks_Admin();
		$this->settings = new GeoLinks_Settings();
		$this->redirect = new Geol_Redirects();
	}
}
