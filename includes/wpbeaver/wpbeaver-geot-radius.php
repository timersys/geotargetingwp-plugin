<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * WPBeaver Geo Module
 *
 * @link       https://geotargetingwp.com/geotargeting-pro
 * @since      1.6.3
 *
 * @package    GeoTarget
 * @subpackage GeoTarget/includes
 * @author     Damian Logghe
 */
class WPBeaver_GeoRadius {

	/**
	 * Add the actual fields
	 *
	 * @return array
	 */
	static function get_fields() {

		$section = [

			'title' => esc_html__( 'Geo Radius', 'geot' ),
			'fields' => [

				'radius_km' => [
					'type'			=> 'unit',
					'class'			=> 'geot_radius_km_input',
					'placeholder'	=> '100',
					'label'			=> esc_html__( 'Radius (km)', 'Geot' ),
					'help'			=> esc_html__( 'Type the range.', 'geot' ),
				],
				'radius_lat' => [
					'type'		=> 'unit',
					'class'		=> 'geot_radius_lat_input',
					'label'		=> esc_html__( 'Latitude', 'Geot' ),
					'help'		=> esc_html__( 'Type the latitude.', 'geot' ),
				],
				'radius_lng' => [
					'type'		=> 'unit',
					'class'		=> 'geot_radius_lng_input',
					'label'		=> esc_html__( 'Longitude', 'Geot' ),
					'help'		=> esc_html__( 'Type the longitude.', 'geot' ),
				],
			],
		];

		return $section;
	}


	/**
	 * Conditional if render
	 *
	 * @return array
	 */
	static function is_render( $settings ) {

		if( is_object( $settings ) )
			$settings = get_object_vars($settings);

		$radius_km	= isset( $settings['radius_km'] ) ? trim( $settings['radius_km'] ) : '';
		$radius_lat = isset( $settings['radius_lat'] ) ? trim( $settings['radius_lat'] ) : '';
		$radius_lng = isset( $settings['radius_lng'] ) ? trim( $settings['radius_lng'] ) : '';


		if ( empty( $radius_km ) || empty( $radius_lat ) || empty( $radius_lng ) ) {
			return true;
		}

		return geot_target_radius( $radius_lat, $radius_lng, $radius_km );
	}


	/**
	 * if is ajax, apply render
	 *
	 * @return array
	 */
	static function ajax_render( $settings, $output ) {

		if( is_object( $settings ) )
			$settings = get_object_vars($settings);


		$radius_km	= isset( $settings['radius_km'] ) ? trim( $settings['radius_km'] ) : '';
		$radius_lat = isset( $settings['radius_lat'] ) ? trim( $settings['radius_lat'] ) : '';
		$radius_lng = isset( $settings['radius_lng'] ) ? trim( $settings['radius_lng'] ) : '';


		if ( empty( $radius_km ) || empty( $radius_lat ) || empty( $radius_lng ) ) {
			return $output;
		}


		return '<div class="geot-ajax geot-filter" data-action="radius_filter" data-filter="' . $radius_km . '" data-region="' . $radius_lat . '" data-ex_filter="' . $radius_lng . '">' . $output . '</div>';
	}
}