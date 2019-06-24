<?php

/**
 * Fired during plugin activation
 *
 * @link       https://geotargetingwp.com/geotargeting-pro
 * @since      1.0.0
 *
 * @package    GeoTarget
 * @subpackage GeoTarget/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    GeoTarget
 * @subpackage GeoTarget/includes
 * @author     Your Name <email@example.com>
 */
class GeotWP_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

		if ( version_compare( PHP_VERSION, '5.6' ) < 0 ) {

			deactivate_plugins( GEOWP_PLUGIN_FILE );
			wp_die(
				'<p>' . __( 'Hey, we\'ve noticed that you\'re running an outdated version of PHP. PHP is the programming language that WordPress and this plugin are built on. The version that is currently used for your site is no longer supported. Newer versions of PHP are both faster and more secure. In fact, your version of PHP no longer receives security updates.' ) . '</p>' .
				'<p>' . __( 'Geotargeting PRO requires at least PHP 5.6 and you are running PHP ' ) . PHP_VERSION . '</p>'
			);
		}
		// deactivate old plugins
		$addons = [];
		// GeotargetingPro
		if ( is_plugin_active( 'geotargeting-pro/geotargeting-pro.php' ) )
			deactivate_plugins( 'geotargeting-pro/geotargeting-pro.php', true );

		// Geo Redirect
		if ( is_plugin_active( 'geo-redirects/geo-redirects.php' ) ) {
			update_option('geot_plugin_geo_redirect', 1);
			deactivate_plugins( 'geo-redirects/geo-redirects.php' , true );
			$addons['geo-redirect'] = 1;
		}

		// Geo Blocker
		if ( is_plugin_active( 'geo-blocker/geo-blocker.php' ) ) {
			update_option('geot_plugin_geo_blocker', 1);
			deactivate_plugins( 'geo-blocker/geo-blocker.php' , true );
		}

		// Geo Links
		if ( is_plugin_active( 'geo-links/geo-links.php' ) ) {
			update_option('geot_plugin_geo_links', 1);
			deactivate_plugins( 'geo-links/geo-links.php', true );
		}

		// Geo Flags
		if ( is_plugin_active( 'geo-flags/geo-flags.php' ) ) {
			update_option('geot_plugin_geo_flags', 1);
			deactivate_plugins( 'geo-flags/geo-flags.php' , true );
		}

		update_option('geot_activated', 1);
		

		GeotFunctions\add_countries_to_db();
		GeotFunctions\geot_activate();

		do_action( 'geotWP/activated' );
	}
}
