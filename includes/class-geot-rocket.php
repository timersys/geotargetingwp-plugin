<?php

class GeotWP_Rocket {

	/**
	 * Construct
	 */
	public function __construct() {

		remove_filter( 'rocket_htaccess_mod_rewrite', '__return_false', 72 );
		remove_action( 'geotWP/activated', 'rocket_activate_geotargetingwp', 11 );

		add_filter( 'rocket_htaccess_mod_rewrite', [ $this, 'update_mod_rewrite' ], 999 );
		add_filter( 'before_rocket_htaccess_rules', [ $this, 'update_htaccess'], 999 );

		add_action( 'geotWP/activated', [ $this, 'activate_geotargetingwp'], 12 );
		add_action( 'geotWP/deactivated', [ $this, 'deactivate_geotargetingwp'], 12 );

		add_filter( 'geot_rocket_country', 'geotwp_spaces_by_hyphen', 10, 1);
		add_filter( 'geot_rocket_state', 'geotwp_spaces_by_hyphen', 10, 1);
		add_filter( 'geot_rocket_city', 'geotwp_spaces_by_hyphen', 10, 1);

		//add_action( 'activate_wp-rocket/wp-rocket.php', [ $this, 'activate_rocket' ], 20 );
	}

	/**
	 * Create enviroment variable
	 * @param  STRING $early
	 * @return STRING
	 */
	public function update_htaccess($early) {

		$output = '<IfModule mod_setenvif.c>' . PHP_EOL;
		$output .= 'SetEnvIfNoCase Cookie geot_rocket_country=([^;]+) geot_country=$1' . PHP_EOL;
		$output .= 'SetEnvIfNoCase Cookie geot_rocket_state=([^;]+) geot_state=$1' . PHP_EOL;
		$output .= 'SetEnvIfNoCase Cookie geot_rocket_city=([^;]+) geot_city=$1' . PHP_EOL;
		  $output .='</IfModule>' . PHP_EOL;

		return $output . $early;
	}

	/**
	 * Lets add the geot variable to htaccess
	 * @param  STRING $rules
	 * @return STRING
	 */
	public function update_mod_rewrite($rules) {
		$replaceWith = '}-%{ENV:geot_country}-%{ENV:geot_state}-%{ENV:geot_city}.html';

		$suf_pos = strrpos($rules, 'RewriteCond');
		if ($suf_pos === false) return $rules;

		$prefix = substr ( $rules, 0, $suf_pos);
		$suffix = substr ( $rules, $suf_pos );
		$suffix = str_replace('}.html', $replaceWith, $suffix);
		$suffix = 'RewriteCond %{ENV:geot_country} .+' . PHP_EOL .
					'RewriteCond %{ENV:geot_state} .+' . PHP_EOL .
					'RewriteCond %{ENV:geot_city} .+' . PHP_EOL . $suffix;

		return $prefix . $suffix;
	}

	/**
	 * Activate Geot
	 * @return Mixed
	 */
	public function activate_geotargetingwp() {
		add_filter('rocket_cache_dynamic_cookies', 'rocket_add_geotargetingwp_dynamic_cookies');
		add_filter('rocket_cache_mandatory_cookies', 'rocket_add_geotargetingwp_mandatory_cookie');
	
		$this->rocket_flush();
	}

	/**
	 * Deactivate Geot
	 * @return mixed
	 */
	public function deactivate_geotargetingwp() {
		remove_filter( 'rocket_htaccess_mod_rewrite', [ $this, 'update_mod_rewrite' ], 999 );
		remove_filter( 'before_rocket_htaccess_rules', [ $this, 'update_htaccess'], 999 );

		remove_filter( 'rocket_cache_dynamic_cookies'  , 'rocket_add_geotargetingwp_dynamic_cookies' );
		remove_filter( 'rocket_cache_mandatory_cookies', 'rocket_add_geotargetingwp_mandatory_cookie' );

		$this->rocket_flush();
	}

	/**
	 * Flush Rocket
	 * @return mixed
	 */
	public function rocket_flush() {
		if (function_exists('flush_rocket_htaccess')) {  // just in case whilst helper not in core
			
			// Update the WP Rocket rules on the .htaccess file.
			if ( function_exists('get_home_path')) {
				// if get_home_path function is not loaded Rocket code would 500 error on dashboard
				flush_rocket_htaccess();
			}

			// Regenerate the config file.
			rocket_generate_config_file();

			// Clear WP Rocket cache
			rocket_clean_domain();
		}
	}
}