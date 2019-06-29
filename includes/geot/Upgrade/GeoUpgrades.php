<?php

namespace GeotCore\Upgrade;


class GeoUpgrades {
	private static $plugin_version;

	/**
	 * Hook to admin_init
	 */
	public static function init() {
		self::$plugin_version = '1.0.0';

		add_action( 'geotWP/activated', [ GeoUpgrades::class, 'run' ] );
	}

	public static function run() {
		global $wpdb;
		$current_version = get_option( 'geot_functions_v' );
		// first upgrade routine needed
		if ( ! $current_version ) {
			// delete old wp_session records from db
			$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE '_wp_session_expires_%'" );
		}

		// finish updating version
		update_option( 'geot_functions_v', self::$plugin_version );
	}
}