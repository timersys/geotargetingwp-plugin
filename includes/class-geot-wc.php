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
		add_filter( 'woocommerce_geolocate_ip', [ $this, 'apply_geolocation' ], 100, 2 );

		// Prices per country plugin
		add_filter( 'woocommerce_customer_get_billing_country', [ $this, 'apply_geolocation' ], 100, 2 );
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
					'desc_tip' => esc_html__( 'If you check this option, GeotargetingWP will be used for geolocation.', 'geot' ),
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

		return apply_filters('geot/woocommerce/geolocation', $country_code, $ip );
	}
}