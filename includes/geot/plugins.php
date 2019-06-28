<?php

namespace GeotFunctions;

/**
 * Regenerate WpRocket settings on activation
 */
function wp_rocket_loaded() {
	add_filter( 'rocket_htaccess_mod_rewrite', '__return_false' );
	add_filter( 'rocket_cache_dynamic_cookies', 'rocket_add_geotargetingwp_dynamic_cookies' );
	add_filter( 'rocket_cache_mandatory_cookies', 'rocket_add_geotargetingwp_mandatory_cookie' );

	// Update the WP Rocket rules on the .htaccess file.
	flush_rocket_htaccess();
	// Regenerate the config file.
	rocket_generate_config_file();
}

function wp_rocket_activated() {
	add_action( 'wp_rocket_loaded', 'GeotFunctions\wp_rocket_loaded' );
}

add_action( 'activate_wp-rocket/wp-rocket.php', 'GeotFunctions\wp_rocket_activated', 20 );