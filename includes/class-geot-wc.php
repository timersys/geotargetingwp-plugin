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

		// Add field to Geotargeting
		add_filter( 'woocommerce_general_settings', [ $this, 'add_geolocation_field' ], 10, 1 );

		// Default Customer Location
		add_filter( 'woocommerce_get_geolocation', [ $this, 'apply_geolocation' ], 10, 2 );
	}

	public function add_geolocation_field( $fields = [] ) {

		$output = [];

		foreach( $fields as $field ) {
			
			if( $field['id'] == 'woocommerce_calc_taxes' ) {
				$output[] = [
					'title'    => esc_html__( 'Enable GeotargetingWP', 'geot' ),
					'desc'     => esc_html__( 'Use GeotargetingWP for geolocation', 'geot' ),
					'id'       => 'woocommerce_geot_geolocate',
					'default'  => 'no',
					'type'     => 'checkbox',
					'desc_tip' => esc_html__( 'If you checked this option, to geolocate customer location will use GeotargetingWP.', 'geot' ),
				];
			}

			$output[] = $field;
		}


		return $output;
	}


	public function apply_geolocation( $location = [], $ip = '' ) {

		$geolocate_settings = get_option('woocommerce_geot_geolocate', 'no');

		if( $geolocate_settings == 'no' )
			return $location;

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