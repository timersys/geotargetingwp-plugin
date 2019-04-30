<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * Dashboard. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://geotargetingwp.com/
 * @since             1.0.1
 * @package           GeoTarget
 *
 * @wordpress-plugin
 * Plugin Name:       GeoTargetingwp Pro
 * Plugin URI:        https://geotargetingwp.com/
 * Description:       Geo Targeting for WordPress will let you country-target your content based on users IP's and Geocountry Ip database
 * Version:           1.0
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

define( 'GEOT_PLUGIN_FILE' , __FILE__);
define( 'GEOT_VERSION' , '1.0' );
define( 'GEOT_DB_VERSION' , '1.0' );
define( 'GEOT_PLUGIN_DIR' , plugin_dir_path(__FILE__) );
define( 'GEOT_PLUGIN_URL' , plugin_dir_url(__FILE__) );
define( 'GEOT_ADDONS_DIR' , plugin_dir_path(__FILE__) . 'addons/' );
define( 'GEOT_ADDONS_URL' , plugin_dir_url(__FILE__) . 'addons/' );
define( 'GEOT_PLUGIN_HOOK' , basename( dirname( __FILE__ ) ) . '/' . basename( __FILE__ ) );
if( !defined('GEOTROOT_PLUGIN_FILE'))
	define( 'GEOTROOT_PLUGIN_FILE', GEOT_PLUGIN_FILE );

/**
 * The code that runs during plugin activation.
 */
require_once GEOT_PLUGIN_DIR . 'includes/class-geot-activator.php';

/**
 * The code that runs during plugin deactivation.
 */
require_once GEOT_PLUGIN_DIR . 'includes/class-geot-deactivator.php';

/** This action is documented in includes/class-geot-activator.php */
register_activation_hook( __FILE__, array( 'Geot_Activator', 'activate' ) );

/** This action is documented in includes/class-geot-deactivator.php */
register_deactivation_hook( __FILE__, array( 'Geot_Deactivator', 'deactivate' ) );

/**
 * The core plugin class that is used to define internationalization,
 * dashboard-specific hooks, and public-facing site hooks.
 */
require_once GEOT_PLUGIN_DIR . 'includes/class-geot.php';

/**
 * Store the plugin global
 */
global $geot;

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
