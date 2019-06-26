<?php

/**
 * @link              https://timersys.com
 * @since             1.0.0
 * @package           Geobl
 *
 * @wordpress-plugin
 * Plugin Name:       Geo Blocker
 * Plugin URI:        https://geotargetingwp/
 * Description:       Geo Blocker let you block access to your site based on geolocation
 * Version:           1.2
 * Author:            Damian Logghe
 * Author URI:        https://timersys.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       geobl
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'GEOTWP_BL_VERSION', '1.2' );
define( 'GEOTWP_BL_PLUGIN_FILE', __FILE__ );
define( 'GEOTWP_BL_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'GEOTWP_BL_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'GEOTWP_BL_PLUGIN_HOOK', basename( dirname( __FILE__ ) ) . '/' . basename( __FILE__ ) );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require GEOTWP_BL_PLUGIN_DIR . 'includes/class-geobl.php';

global $geobl;
/**
 * Begins execution of the plugin.
 * @since    1.0.0
 */
function run_geotwpBl() {

	return GeotWP_Bl::instance();
}

$GLOBALS['geobl'] = run_geotwpBl();
