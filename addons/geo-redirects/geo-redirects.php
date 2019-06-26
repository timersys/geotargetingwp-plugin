<?php

/**
 * @link              https://timersys.com
 * @since             1.0.0
 * @package           Geotr
 *
 * @wordpress-plugin
 * Plugin Name:       Geo Redirects
 * Plugin URI:        https://geotargetingwp.com/
 * Description:       Create redirects based on Countries, Cities or States. Add multiple rules
 * Version:           1.3.7
 * Author:            Timersys
 * Author URI:        https://timersys.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       geotr
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'GEOTWP_R_VERSION', '1.3.7' );
define( 'GEOTWP_R_PLUGIN_FILE', __FILE__ );
define( 'GEOTWP_R_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'GEOTWP_R_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'GEOTWP_R_PLUGIN_HOOK', basename( dirname( __FILE__ ) ) . '/' . basename( __FILE__ ) );


/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require GEOTWP_R_PLUGIN_DIR . 'includes/class-geotr.php';

/**
 * Begins execution of the plugin.
 * @since    1.0.0
 */
global $geotr;
function run_geotWP_R() {

	return GeotWP_R::instance();
}

$GLOBALS['geotr'] = run_geotWP_R();
