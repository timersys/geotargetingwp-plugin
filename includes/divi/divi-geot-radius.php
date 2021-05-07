<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * Divi Geo Module
 *
 * @link       https://geotargetingwp.com/geotargeting-pro
 * @since      1.6.3
 *
 * @package    GeoTarget
 * @subpackage GeoTarget/includes
 * @author     Damian Logghe
 */
class Divi_GeoRadius {

	/**
	 * Add the actual fields
	 *
	 * @return array
	 */
	static function get_fields() {


		$fields['radius_mode'] = [
			'label'				=> esc_html__( 'Geo Mode', 'geot' ),
			'type'				=> 'select',
			'options'           => [ 'show' => esc_html__( 'Show', 'geot' ), 'hide' => esc_html__( 'Hide', 'geot' )],
			'option_category'	=> 'configuration',
			'tab_slug'			=> 'geot',
		];

		$fields['radius_km'] = [
			'label'				=> esc_html__( 'Radius (Km)', 'geot' ),
			'type'				=> 'number',
			'default_unit'		=> 'km',
			'option_category'	=> 'configuration',
			'attributes'		=> [ 'placeholder' => '100', 'step' => 1 ],
			'tab_slug'			=> 'geot',
			'allowed_units'		=> ['km']
		];

		
		$fields['radius_lat'] = [
			'label'           => esc_html__( 'Latitude', 'geot' ),
			'type'            => 'number',
			'option_category' => 'configuration',
			'attributes'		=> [ 'step' => 0.0000001 ],
			'tab_slug'        => 'geot',
		];

		
		$fields['radius_lng'] = [
			'label'           => esc_html__( 'Longitude', 'geot' ),
			'type'            => 'number',
			'option_category' => 'configuration',
			'attributes'		=> [ 'step' => 0.0000001 ],
			'tab_slug'        => 'geot',
		];

		return $fields;
	}


	/**
	 * Add the actual fields
	 *
	 * @return array
	 */
	static function is_render( $settings ) {

		$radius_km	= isset( $settings['radius_km'] ) ? trim( $settings['radius_km'] ) : '';
		$radius_lat = isset( $settings['radius_lat'] ) ? trim( $settings['radius_lat'] ) : '';
		$radius_lng = isset( $settings['radius_lng'] ) ? trim( $settings['radius_lng'] ) : '';
		$radius_mode = !empty( $settings['radius_mode'] ) ? trim( $settings['radius_mode'] ) : 'show';

		if ( empty( $radius_km ) || empty( $radius_lat ) || empty( $radius_lng ) ) {
			return true;
		}

		$target = geot_target_radius( $radius_lat, $radius_lng, $radius_km );
		return $radius_mode == 'include' ? $target : ! $target;
	}


	/**
	 * if is ajax, apply render
	 *
	 * @return array
	 */
	static function ajax_render( $settings, $output ) {

		$radius_km	= isset( $settings['radius_km'] ) ? $settings['radius_km'] : '';
		$radius_lat = isset( $settings['radius_lat'] ) ? $settings['radius_lat'] : '';
		$radius_lng = isset( $settings['radius_lng'] ) ? $settings['radius_lng'] : '';
		$radius_mode = !empty( $settings['radius_mode'] ) ? trim( $settings['radius_mode'] ) : 'show';

		if ( empty( $radius_km ) || empty( $radius_lat ) || empty( $radius_lng ) ) {
			return $output;
		}

		return '<div class="geot-ajax geot-filter" data-geo_mode="'. $radius_mode .'" data-action="radius_filter" data-filter="' . $radius_km . '" data-region="' . $radius_lat . '" data-ex_filter="' . $radius_lng . '">' . do_shortcode( $output ) . '</div>';
	}

}