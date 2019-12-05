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
				'heading'		=> esc_attr__( 'Include States', 'geot' ),
				'description'	=> esc_attr__( 'Type states names separated by commas.', 'geot' ),
				'param_name'	=> 'geot_in_states',
				'default'		=> '',
				'group'			=> esc_attr__( 'GeoTargeting', 'geot' ),
			],
			[
				'type'			=> 'textfield',
				'heading'		=> esc_attr__( 'Exclude States', 'geot' ),
				'description'	=> esc_attr__( 'Type city names separated by commas.', 'geot' ),
				'param_name'	=> 'geot_ex_states',
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

		$in_states = trim( $geot_in_states );
		$ex_states = trim( $geot_ex_states );

		if ( empty( $in_states ) && empty( $ex_states ) ) {
			return true;
		}


		if ( geot_target_state( $in_states, $ex_states ) ) {
			return true;
		}

		return false;
	}


	/**
	 * if is ajax, apply render
	 *
	 * @return array
	 */
	static function ajax_render( $attrs, $output ) {

		$in_regions_commas = $ex_regions_commas = '';

		extract( $attrs );

		$in_states = trim( $geot_in_states );
		$ex_states = trim( $geot_ex_states );

		if ( empty( $in_states ) && empty( $ex_states ) ) {
			return $output;
		}

		return '<div class="geot-ajax geot-filter" data-action="state_filter" data-filter="' . $in_states . '" data-ex_filter="' . $ex_states . '">' . $output . '</div>';
	}
}