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

use GeotCore\Setting\GeotSettings;

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
class GeotWP_R {

	/**
	 * Plugin Instance
	 * @since 1.0.0
	 * @var The Fbl plugin instance
	 */
	protected static $_instance = null;
	/**
	 * Public Class instance
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      GeotWP_R_Public $public Public class instance
	 */
	public $public;
	/**
	 * Admin Class instance
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      GeotWP_R_Admin $public Admin class instance
	 */
	public $admin;
	/**
	 * Settings Class instance
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      GeotWP_R_Settings $settings Settings class instance
	 */
	public $settings;
	/**
	 * Metaboxes Class instance
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      GeotWP_R_Metaboxes $public Metaboxes class instance
	 */
	public $metaboxes;

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
		$this->set_objects_admin();
		$this->set_objects_public();
	}

	/**
	 * Load the required dependencies for this plugin.
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {
		require_once GEOTWP_R_PLUGIN_DIR . 'includes/functions.php';
		require_once GEOTWP_R_PLUGIN_DIR . 'includes/class-geotr-cpt.php';
		require_once GEOTWP_R_PLUGIN_DIR . 'includes/class-geotr-helper.php';
		require_once GEOTWP_R_PLUGIN_DIR . 'public/class-geotr-public.php';
		require_once GEOTWP_R_PLUGIN_DIR . 'admin/class-geotr-settings.php';

		if ( is_admin() ) {
			require_once GEOTWP_R_PLUGIN_DIR . 'admin/class-geotr-admin.php';
		}
	}


	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_objects_admin() {

		if ( ! is_admin() ) {
			return;
		}

		$this->admin    = new GeotWP_R_Admin();
		$this->settings = new GeotWP_R_Settings();

		GeotWP_R_ules::set_rules_fields();
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_objects_public() {
		$this->public = new GeotWP_R_Public();
	}

	/**
	 * Main plugin_name Instance
	 *
	 * Ensures only one instance of WSI is loaded or can be loaded.
	 *
	 * @return plugin_name - Main instance
	 * @see Geotr()
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
