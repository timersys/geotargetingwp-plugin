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
		// deactivate lite
		// Geotargetinglite
		if ( is_plugin_active( 'geotargeting/geotargeting.php' ) ) {
			deactivate_plugins( 'geotargeting/geotargeting.php', true );
		}
		// deactivate old plugins in old versions.
		$addons = get_option('geot_pro_addons');
		if( ! $addons ) {
			$addons = [];
			// GeotargetingPro
			if ( is_plugin_active( 'geotargeting-pro/geotargeting-pro.php' ) ) {
				deactivate_plugins( 'geotargeting-pro/geotargeting-pro.php', true );
			}

			// Geo Redirect
			if ( is_plugin_active( 'geo-redirects/geo-redirects.php' ) ) {
				deactivate_plugins( 'geo-redirects/geo-redirects.php', true );
				$addons['geo-redirects'] = 1;
			}

			// Geo Blocker
			if ( is_plugin_active( 'geo-blocker/geo-blocker.php' ) ) {
				deactivate_plugins( 'geo-blocker/geo-blocker.php', true );
				$addons['geo-blocker'] = 1;
			}

			// Geo Links
			if ( is_plugin_active( 'geo-links/geo-links.php' ) ) {
				deactivate_plugins( 'geo-links/geo-links.php', true );
				$addons['geo-links'] = 1;
			}

			// Geo Flags
			if ( is_plugin_active( 'geo-flags/geo-flags.php' ) ) {
				deactivate_plugins( 'geo-flags/geo-flags.php', true );
				$addons['geo-flags'] = 1;
			}

			update_option( 'geot_pro_addons', $addons );
		}
		// Check ajax mode
		if( ! get_option('geot_wp_ajax_checked') ) {
			$opts   = get_option( 'geot_pro_settings' );
			$r_opts = get_option( 'geotr_settings' );
			$l_opts = get_option( 'geobl_settings' );
			if ( ( isset( $opts['ajax_mode'] ) && $opts['ajax_mode'] == 1 ) ||
			     ( isset( $r_opts['ajax_mode'] ) && $r_opts['ajax_mode'] == 1 ) ||
			     ( isset( $l_opts['ajax_mode'] ) && $l_opts['ajax_mode'] == 1 ) ) {
				$wpopts              = geot_settings();
				$wpopts['ajax_mode'] = 1;
				update_option( 'geot_settings', $wpopts );
				update_option( 'geot_wp_ajax_checked', 1 );
			}
		}

		update_option('geot_version', GEOWP_VERSION);

		GeotCore\geot_activate();

		delete_option( 'geotWP-deactivated' );

		do_action( 'geotWP/activated' );
	}
}
