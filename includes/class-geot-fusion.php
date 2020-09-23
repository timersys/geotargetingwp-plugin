<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * Avada Fusion Extension
 *
 * @link       https://geotargetingwp.com/geotargeting-pro
 * @since      1.6.3
 *
 * @package    GeoTarget
 * @subpackage GeoTarget/includes
 * @author     Damian Logghe
 */
class GeotWP_Fusion {

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		add_action( 'fusion_builder_before_init', [ $this, 'init' ] );
		add_filter( 'fusion_builder_element_params', [ $this, 'element_params' ], 10, 2 );
		add_filter( 'do_shortcode_tag', [$this, 'shortcode_tag'], 10, 4 );
	}

	/**
	 * Include the files
	 * @return mixed
	 */
	public function init() {
		require_once GEOWP_PLUGIN_DIR . 'includes/fusion/fusion-geot-country.php';
		require_once GEOWP_PLUGIN_DIR . 'includes/fusion/fusion-geot-city.php';
		require_once GEOWP_PLUGIN_DIR . 'includes/fusion/fusion-geot-state.php';
		require_once GEOWP_PLUGIN_DIR . 'includes/fusion/fusion-geot-zip.php';
		require_once GEOWP_PLUGIN_DIR . 'includes/fusion/fusion-geot-radius.php';
	}

	/**
	 * Lets add params to each element
	 * 
	 * @param  array  $params    element params
	 * @param  string $shortcode shortcode name
	 * @return array  $params    original params and Geot params
	 */
	public function element_params($params = [], $shortcode = '') {

		$geot_keys = [
			'geot_in_countries',
			'geot_in_region_countries',
			'geot_ex_countries',
			'geot_ex_region_countries',
			'geot_in_cities',
			'geot_in_region_cities',
			'geot_ex_cities',
			'geot_ex_region_cities',
			'geot_in_states',
			'geot_in_region_states',
			'geot_ex_states',
			'geot_ex_region_states',
			'geot_in_zips',
			'geot_in_region_zips',
			'geot_ex_zips',
			'geot_ex_region_zips',
			'geot_radius_km',
			'geot_radius_lat',
			'geot_radius_lng',
		];


		if( isset( $params['param_name'] ) && in_array( $params['param_name'], $geot_keys ) )
			return $params;

		$this->init();

		$params = array_merge(
			$params,
			Fusion_GeoCountry::get_fields(),
			Fusion_GeoCity::get_fields(),
			Fusion_GeoState::get_fields(),
			Fusion_GeoZip::get_fields(),
			Fusion_GeoRadius::get_fields()
		);

		return $params;
	}


	/**
	 * Get Regions
	 *
	 * @param string $slug_region
	 * @return array $dropdown_values
	 */
	static function get_regions( $slug_region = 'country' ) {

		$dropdown_values = ['null' => 'None'];

		switch ( $slug_region ) {
			case 'city':
				$regions = geot_city_regions();
				break;
			case 'zip':
				$regions = geot_zip_regions();
				break;
			case 'state':
				$regions = geot_state_regions();
				break;
			default:
				$regions = geot_country_regions();
		}

		if ( ! empty( $regions ) ) {
			foreach ( $regions as $r ) {
				if ( isset( $r['name'] ) ) {
					$dropdown_values[ $r['name'] ] = $r['name'];
				}
			}
		}

		return $dropdown_values;
	}

	/**
	 * Shortocode filter in the front
	 * 
	 * @param  string $output Shortcode output
	 * @param  string $tag    Shortcode name
	 * @param  array  $attrs  Shortcode attributes
	 * @param  array  $m      Regular expression match array.
	 * @return mixed
	 */
	public function shortcode_tag($output, $tag, $attrs, $m) {

		if( substr($tag, 0, 7) != 'fusion_' )
			return $output;

		$found = false;
		$geot_keys = [
			'geot_in_countries',
			'geot_in_region_countries',
			'geot_ex_countries',
			'geot_ex_region_countries',
			'geot_in_cities',
			'geot_in_region_cities',
			'geot_ex_cities',
			'geot_ex_region_cities',
			'geot_in_states',
			'geot_in_region_states',
			'geot_ex_states',
			'geot_ex_region_states',
			'geot_in_zips',
			'geot_in_region_zips',
			'geot_ex_zips',
			'geot_ex_region_zips',
			'geot_radius_km',
			'geot_radius_lat',
			'geot_radius_lng',
		];	

		foreach( $geot_keys as $geot_key ) {
			if( isset( $attrs[$geot_key] ) && !empty( $attrs[$geot_key] ) ) {
				$found = true;
				break;
			}
		}


		if( ! $found )
			return $output;


		$this->init();

		$opts = geot_settings();
		if ( isset( $opts['ajax_mode'] ) && $opts['ajax_mode'] == '1' ) {

			$output = Fusion_GeoCountry::ajax_render( $attrs, $output );
			$output = Fusion_GeoCity::ajax_render( $attrs, $output );
			$output = Fusion_GeoState::ajax_render( $attrs, $output );
			$output = Fusion_GeoZip::ajax_render( $attrs, $output );
			$output = Fusion_GeoRadius::ajax_render( $attrs, $output );

		} else {
			if ( ! Fusion_GeoCountry::is_render( $attrs )
			     || ! Fusion_GeoCity::is_render( $attrs )
			     || ! Fusion_GeoState::is_render( $attrs )
			     || ! Fusion_GeoZip::is_render( $attrs )
			     || ! Fusion_GeoRadius::is_render( $attrs )
			) {
				return '';
			}
		}

		return $output;
	}
	/*
	 * Fusion builder dropdown needs to have a default value
	 * we use this function to clear the null one
	 */
	public static function clean_region( $regions ) {
		$regions = array_filter( array_map( 'trim', explode( ',', $regions ) ) );

		foreach ( $regions as $key => $name ) {
			if( 'null' == $name ) {
				unset($regions[$key]);
			}
		}
		return $regions;
	}

}