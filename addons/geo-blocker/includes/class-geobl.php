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
 * @package    Geobl
 * @subpackage Geobl/includes
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
 * @package    Geobl
 * @subpackage Geobl/includes
 * @author     Damian Logghe <damian@timersys.com>
 */
class GeotWP_Bl {
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
	 * @var      GeotWP_Bl_Public $public Public class instance
	 */
	public $public;
	/**
	 * Admin Class instance
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      GeotWP_Bl_Admin $public Admin class instance
	 */
	public $admin;
	public $settings;

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
		require_once GEOTWP_BL_PLUGIN_DIR . 'includes/functions.php';
		require_once GEOTWP_BL_PLUGIN_DIR . 'includes/class-geobl-cpt.php';
		require_once GEOTWP_BL_PLUGIN_DIR . 'includes/class-geobl-helper.php';
		require_once GEOTWP_BL_PLUGIN_DIR . 'public/class-geobl-public.php';


		if ( is_admin() ) {
			require_once GEOTWP_BL_PLUGIN_DIR . 'admin/class-geobl-admin.php';
			require_once GEOTWP_BL_PLUGIN_DIR . 'admin/class-geobl-settings.php';
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

		$this->admin    = new GeotWP_Bl_Admin();
		$this->settings = new GeotWP_Bl_Settings();

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
		$this->public = new GeotWP_Bl_Public();
	}

	/**
	 * Main plugin_name Instance
	 *
	 * Ensures only one instance of WSI is loaded or can be loaded.
	 *
	 * @return plugin_name - Main instance
	 * @see Geobl()
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
