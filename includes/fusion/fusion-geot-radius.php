<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * Fusion Geo Module
 *
 * @link       https://geotargetingwp.com/geotargeting-pro
 * @since      1.6.3
 *
 * @package    GeoTarget
 * @subpackage GeoTarget/includes
 * @author     Damian Logghe
 */
class Fusion_GeoRadius {

	/**
	 * Geot fields to State
	 *
	 * @return array
	 */
	static function get_fields() {

		$fields = [
			[
				'type'			=> 'textfield',
				'heading'		=> esc_attr__( 'Radius (km)', 'geot' ),
				'description'	=> esc_attr__( 'Type the range.', 'geot' ),
				'param_name'	=> 'geot_radius_km',
				'default'		=> '',
				'group'			=> esc_attr__( 'GeoTargeting', 'geot' ),
			],[
				'type'			=> 'textfield',
				'heading'		=> esc_attr__( 'Latitude', 'geot' ),
				'description'	=> esc_attr__( 'Type the latitude.', 'geot' ),
				'param_name'	=> 'geot_radius_lat',
				'default'		=> '',
				'group'			=> esc_attr__( 'GeoTargeting', 'geot' ),
			],[
				'type'			=> 'textfield',
				'heading'		=> esc_attr__( 'Longitude', 'geot' ),
				'description'	=> esc_attr__( 'Type the longitude.', 'geot' ),
				'param_name'	=> 'geot_radius_lng',
				'default'		=> '',
				'group'			=> esc_attr__( 'GeoTargeting', 'geot' ),
			]
		];

		return $fields;
	}


	/**
	 * Add the actual fields
	 *
	 * @return bool
	 */
	static function is_render( $attrs ) {

		$geot_radius_km		= isset( $attrs['geot_radius_km'] ) ? trim( $attrs['geot_radius_km'] ) : '';
		$geot_radius_lat	= isset( $attrs['geot_radius_lat'] ) ? trim( $attrs['geot_radius_lat'] ) : '';
		$geot_radius_lng	= isset( $attrs['geot_radius_lng'] ) ? trim( $attrs['geot_radius_lng'] ) : '';

		if ( empty( $geot_radius_km ) || empty( $geot_radius_lat ) || empty( $geot_radius_lng ) ) {
			return true;
		}

		return geot_target_radius( $geot_radius_lat, $geot_radius_lng, $geot_radius_km );
	}


	/**
	 * if is ajax, apply render
	 *
	 * @return string
	 */
	static function ajax_render( $attrs, $output ) {

		//extract( $attrs );
		$geot_radius_km		= isset( $attrs['geot_radius_km'] ) ? trim( $attrs['geot_radius_km'] ) : '';
		$geot_radius_lat	= isset( $attrs['geot_radius_lat'] ) ? trim( $attrs['geot_radius_lat'] ) : '';
		$geot_radius_lng	= isset( $attrs['geot_radius_lng'] ) ? trim( $attrs['geot_radius_lng'] ) : '';

		if ( empty( $geot_radius_km ) || empty( $geot_radius_lat ) || empty( $geot_radius_lng ) ) {
			return $output;
		}

		return '<div class="geot-ajax geot-filter" data-action="radius_filter" data-filter="' . $geot_radius_km . '" data-region="' . $geot_radius_lat . '" data-ex_filter="' . $geot_radius_lng . '">' . $output . '</div>';
	}
}