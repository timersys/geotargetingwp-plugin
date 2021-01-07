<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that its ready for translation.
 *
 * @link       https://geotargetingwp.com/geotargeting-pro
 * @since      1.0.0
 *
 * @package    GeotWP_Links
 * @subpackage GeotWP_Links/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that its ready for translation.
 *
 * @since      1.0.0
 * @package    GeotWP_Links
 * @subpackage GeotWP_Links/includes
 * @author     Your Name <email@example.com>
 */
class GeotWP_Links_i18n {

	/**
	 * The domain specified for this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $domain The domain identifier for this plugin.
	 */
	private $domain = 'geol';

	public function __construct() {
		add_action( 'plugins_loaded', [ $this, 'load_plugin_textdomain' ] );
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			$this->domain,
			false,
			dirname( GEOTWP_L_PLUGIN_BASE ) . '/languages/'
		);

	}
}

new GeotWP_Links_i18n();