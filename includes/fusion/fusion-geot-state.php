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
	 * Geot fields to State
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
				'type'			=> 'multiple_select',
				'heading'		=> esc_attr__( 'Include State Regions', 'geot' ),
				'description'	=> esc_attr__( 'Choose region name to show content to.', 'geot' ),
				'param_name'	=> 'geot_in_region_states',
				'value'			=> GeotWP_Fusion::get_regions( 'state' ),
				'default'		=> 'null',
				'group'			=> esc_attr__( 'GeoTargeting', 'geot' ),
			],
			[
				'type'			=> 'textfield',
				'heading'		=> esc_attr__( 'Exclude States', 'geot' ),
				'description'	=> esc_attr__( 'Type city names separated by commas.', 'geot' ),
				'param_name'	=> 'geot_ex_states',
				'default'		=> '',
				'group'			=> esc_attr__( 'GeoTargeting', 'geot' ),
			],
			[
				'type'			=> 'multiple_select',
				'heading'		=> esc_attr__( 'Exclude State Regions', 'geot' ),
				'description'	=> esc_attr__( 'Choose region name to show content to.', 'geot' ),
				'param_name'	=> 'geot_ex_region_states',
				'value'			=> GeotWP_Fusion::get_regions( 'state' ),
				'default'		=> 'null',
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

		$in_states = isset( $attrs['geot_in_states'] ) ?  trim( $attrs['geot_in_states'] ) : '';
		$ex_states = isset( $attrs['geot_ex_states'] ) ?  trim( $attrs['geot_ex_states'] ) : '';
		
		$in_regions = isset( $attrs['geot_in_region_states'] ) ? GeotWP_Fusion::clean_region( $attrs['geot_in_region_states'] ) : [];
		$ex_regions = isset( $attrs['geot_ex_region_states'] ) ? GeotWP_Fusion::clean_region( $attrs['geot_ex_region_states'] ) : [];


		if ( empty( $in_states ) && empty( $ex_states ) &&
			count( $in_regions ) == 0 && count( $ex_regions ) == 0
		) {
			return true;
		}


		return geot_target_state( $in_states, $in_regions, $ex_states, $ex_regions );
	}


	/**
	 * if is ajax, apply render
	 *
	 * @return string
	 */
	static function ajax_render( $attrs, $output ) {

		$in_states = isset( $attrs['geot_in_states'] ) ?  trim( $attrs['geot_in_states'] ) : '';
		$ex_states = isset( $attrs['geot_ex_states'] ) ?  trim( $attrs['geot_ex_states'] ) : '';

		$in_regions = isset( $attrs['geot_in_region_states'] ) ? $attrs['geot_in_region_states'] : '';
		$ex_regions = isset( $attrs['geot_ex_region_states'] ) ? $attrs['geot_ex_region_states'] : '';


		if ( empty( $in_states ) && empty( $ex_states ) &&
		     ( empty( $in_regions ) || 'null' == $in_regions ) && ( empty( $ex_regions ) || 'null' == $ex_regions )
		) {
			return $output;
		}

		return '<div class="geot-ajax geot-filter" data-action="state_filter" data-filter="' . $in_states . '" data-region="' . $in_regions . '" data-ex_filter="' . $ex_states . '" data-ex_region="' . $ex_regions . '">' . $output . '</div>';
	}
}