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

define( 'GEOL_PLUGIN_FILE', __FILE__ );
define( 'GEOL_VERSION', '1.0.4' );
define( 'GEOL_DB_VERSION', '1.2' );
define( 'GEOL_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'GEOL_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'GEOL_PLUGIN_HOOK', basename( dirname( __FILE__ ) ) . '/' . basename( __FILE__ ) );
if ( ! defined( 'GEOTROOT_PLUGIN_FILE' ) ) {
	define( 'GEOTROOT_PLUGIN_FILE', GEOL_PLUGIN_FILE );
}

/**
 * The core plugin class that is used to define internationalization,
 * dashboard-specific hooks, and public-facing site hooks.
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/class-geolinks.php';

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-geolinks-activator.php
 */
function activate_geolinks() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-geolinks-activator.php';
	GeoLinks_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-geolinks-deactivator.php
 */
function deactivate_geolinks() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-geolinks-deactivator.php';
	GeoLinks_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_geolinks' );
register_deactivation_hook( __FILE__, 'deactivate_geolinks' );

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

function Geol() {

	return GeoLinks::instance();
}

$GLOBALS['geol'] = Geol();


