<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://timersys.com
 * @since      1.0.0
 *
 * @package    GeoLinks
 * @subpackage GeoLinks/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    GeoLinks
 * @subpackage GeoLinks/includes
 * @author     Damian Logghe <damian@timersys.com>
 */
class GeoLinks_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
		do_action('geotWP/deactivated');
	}

}
