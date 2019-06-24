<?php

/**
 * Geo flags plugin
 *
 * @link              https://geotargetingwp.com/geo-flags
 * @since             1.0.0
 * @package           GeoTarget
 *
 * @wordpress-plugin
 * Plugin Name:       Geo Flags
 * Plugin URI:        https://geotargetingwp.com/geo-flags
 * Description:       Country Flags based on user location
 * Version:           1.0.7
 * Author:            Timersys
 * Author URI:        https://geotargetingwp.com/geo-flags
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       geotarget
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'GEOTWP_F_PLUGIN_FILE', __FILE__ );
define( 'GEOTWP_F_VERSION', '1.0.7' );
define( 'GEOTWP_F_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'GEOTWP_F_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'GEOTWP_F_PLUGIN_HOOK', basename( dirname( __FILE__ ) ) . '/' . basename( __FILE__ ) );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'lib/GeoFlags.php';


global $geotf;
/**
 * Begins execution of the plugin.
 * @since    1.0.0
 */
function run_geotWp_f() {

	return GeotWP_Flags::instance();

}

$GLOBALS['geotf'] = run_geotWp_f();

