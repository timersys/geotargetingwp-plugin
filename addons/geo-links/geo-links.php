<?php

/**
 * Geo links main file
 *
 * @link              https://geotargetingwp.com/geo-links
 * @since             1.0.0
 * @package           GeoTarget
 *
 * @wordpress-plugin
 * Plugin Name:       Geo Links
 * Plugin URI:        https://geotargetingwp.com/geo-links
 * Description:       Geo Links for WordPress will let you geo-target your affiliate links
 * Version:           1.0.4
 * Author:            Timersys
 * Author URI:        https://geotargetingwp.com/geo-links
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       geolinks
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'GEOTWP_L_PLUGIN_FILE', __FILE__ );
define( 'GEOTWP_L_VERSION', '1.0.4' );
define( 'GEOTWP_L_DB_VERSION', '1.2' );
define( 'GEOTWP_L_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'GEOTWP_L_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'GEOTWP_L_PLUGIN_BASE' , plugin_basename( __FILE__ ) );
define( 'GEOTWP_L_PLUGIN_HOOK', basename( dirname( __FILE__ ) ) . '/' . basename( __FILE__ ) );

/**
 * The core plugin class that is used to define internationalization,
 * dashboard-specific hooks, and public-facing site hooks.
 */
require_once GEOTWP_L_PLUGIN_DIR . 'includes/class-geolinks.php';

/**
 * Store the plugin global
 */
global $geol;

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */

function GeotWP_L() {

	return GeotWP_Links::instance();
}

$GLOBALS['geol'] = GeotWP_L();


