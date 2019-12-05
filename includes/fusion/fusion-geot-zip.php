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
class Fusion_GeoState {

	/**
	 * Add the actual fields
	 *
	 * @return array
	 */
	static function get_fields() {

		$fields = [
			[
				'type'			=> 'textfield',
				'heading'		=> esc_attr__( 'Include Zips', 'geot' ),
				'description'	=> esc_attr__( 'Type zips codes separated by commas.', 'geot' ),
				'param_name'	=> 'geot_in_zips',
				'default'		=> '',
				'group'			=> esc_attr__( 'GeoTargeting', 'geot' ),
			],
			[
				'type'			=> 'textfield',
				'heading'		=> esc_attr__( 'Exclude Zips', 'geot' ),
				'description'	=> esc_attr__( 'Type zips codes separated by commas.', 'geot' ),
				'param_name'	=> 'geot_ex_zips',
				'default'		=> '',
				'group'			=> esc_attr__( 'GeoTargeting', 'geot' ),
			]
		];

		return $fields;
	}


	/**
	 * Add the actual fields
	 *
	 * @return array
	 */
	static function is_render( $attrs ) {

		extract( $attrs );

		$in_zips = trim( $geot_in_zips );
		$ex_zips = trim( $geot_ex_zips );

		if ( empty( $in_zips ) && empty( $ex_zips ) ) {
			return true;
		}


		if ( geot_target_zip( $in_zips, $ex_zips ) ) {
			return true;
		}

		return false;
	}


	/**
	 * if is ajax, apply render
	 *
	 * @return array
	 */
	static function ajax_render( $settings, $output ) {

		extract( $settings );

		if ( empty( $in_states ) && empty( $ex_states ) ) {
			return $output;
		}


		return '<div class="geot-ajax geot-filter" data-action="state_filter" data-filter="' . $in_states . '" data-ex_filter="' . $ex_states . '">' . $output . '</div>';
	}
}