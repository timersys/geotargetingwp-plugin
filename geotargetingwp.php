<?php

/**
 *
 * @link              https://geotargetingwp.com/
 * @since             1.0.1
 * @package           GeoTarget
 *
 * @wordpress-plugin
 * Plugin Name:       GeoTargetingWP
 * Plugin URI:        https://geotargetingwp.com/
 * Description:       Geo Targeting for WordPress will let you country-target your content based on users IP's and Geo country Ip database
 * Version:           3.3.5.3
 * Author:            Timersys
 * Author URI:        https://geotargetingwp.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       geotarget
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'GEOWP_PLUGIN_FILE', __FILE__ );
define( 'GEOWP_VERSION', '3.3.5.3' );
define( 'GEOWP_DB_VERSION', '1.0' );
define( 'GEOWP_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'GEOWP_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'GEOWP_ADDONS_DIR', plugin_dir_path( __FILE__ ) . 'addons/' );
define( 'GEOWP_ADDONS_URL', plugin_dir_url( __FILE__ ) . 'addons/' );
define( 'GEOWP_PLUGIN_HOOK', basename( dirname( __FILE__ ) ) . '/' . basename( __FILE__ ) );
if ( ! defined( 'GEOTROOT_PLUGIN_FILE' ) ) {
	define( 'GEOTROOT_PLUGIN_FILE', GEOWP_PLUGIN_FILE );
}
// load composer
require GEOWP_PLUGIN_DIR . 'vendor/autoload.php';
/**
 * The code that runs during plugin activation.
 */
require_once GEOWP_PLUGIN_DIR . 'includes/class-geot-activator.php';

/**
 * The code that runs during plugin deactivation.
 */
require_once GEOWP_PLUGIN_DIR . 'includes/class-geot-deactivator.php';

/** This action is documented in includes/class-geot-activator.php */
register_activation_hook( __FILE__, [ 'GeotWP_Activator', 'activate' ] );

/** This action is documented in includes/class-geot-deactivator.php */
register_deactivation_hook( __FILE__, [ 'GeotWP_Deactivator', 'deactivate' ] );

/**
 * The core plugin class that is used to define internationalization,
 * dashboard-specific hooks, and public-facing site hooks.
 */
require_once GEOWP_PLUGIN_DIR . 'includes/class-geot.php';


/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */

function iniGeot() {
	return Geot::instance();
}

$GLOBALS['geot'] = iniGeot();
