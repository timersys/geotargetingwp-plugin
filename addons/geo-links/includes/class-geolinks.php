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
 * @package    GeotWP_Links
 * @subpackage GeotWP_Links/includes
 */

use GeotCore\Setting\GeotSettings;


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
 * @package    GeotWP_Links
 * @subpackage GeotWP_Links/includes
 * @author     Your Name <email@example.com>
 */
class GeotWP_Links {

	/**
	 * Plugin Instance
	 * @since 1.0.0
	 * @var The Geot plugin instance
	 */
	protected static $_instance = null;


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
	}

	/**
	 * Load the required dependencies for this plugin.
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		require_once GEOTWP_L_PLUGIN_DIR . 'includes/functions.php';
		require_once GEOTWP_L_PLUGIN_DIR . 'includes/global/class-geolinks-cpt.php';
		require_once GEOTWP_L_PLUGIN_DIR . 'includes/public/class-geolinks-redirect.php';

		if( is_admin() ) {
			require_once GEOTWP_L_PLUGIN_DIR . 'includes/admin/class-geolinks-cache.php';
			require_once GEOTWP_L_PLUGIN_DIR . 'includes/admin/class-geolinks-admin.php';
			require_once GEOTWP_L_PLUGIN_DIR . 'includes/admin/class-geolinks-import.php';
			require_once GEOTWP_L_PLUGIN_DIR . 'includes/admin/class-geolinks-permalinks.php';
			require_once GEOTWP_L_PLUGIN_DIR . 'includes/admin/class-geolinks-settings.php';
			require_once GEOTWP_L_PLUGIN_DIR . 'includes/admin/class-geolinks-notices.php';
			require_once GEOTWP_L_PLUGIN_DIR . 'includes/admin/class-geolinks-ajax.php';
		}
	}


	/**
	 * Main Geot Instance
	 *
	 * Ensures only one instance of WSI is loaded or can be loaded.
	 *
	 * @return GeotWP_Links
	 * @see GEOL()
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
