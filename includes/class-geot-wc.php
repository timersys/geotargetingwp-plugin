<?php

/**
 * Woocommerce Extension
 *
 * @link       https://geotargetingwp.com/geotargeting-pro
 * @since      1.6.3
 *
 * @package    GeoTarget
 * @subpackage GeoTarget/includes
 * @author     Damian Logghe
 */
class GeotWP_WC {

	public function __construct() {

		// Default Customer Location
		add_filter( 'woocommerce_get_geolocation', [ $this, 'get_geolocation' ], 10, 2 );
	}


	public function get_geolocation( $location = [], $ip = '' ) {

		$country_code = geot_country_code();

		if( empty( $country_code ) )
			return $location;

		$state_code = geot_state_code();
		$city_name = geot_city_name();
		$zip_code = geot_zip();

		$location = [
			'country'	=> $country_code,
			'state'		=> $state_code,
			'city'		=> $city_name,
			'postcode'	=> $zip_code,
		];

		return apply_filters('geot/woocommerce/geolocation', $location, $ip );
	}
}