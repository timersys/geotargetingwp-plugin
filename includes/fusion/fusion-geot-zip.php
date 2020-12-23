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
class Fusion_GeoZip {

	/**
	 * Geot fields to Zip
	 *
	 * @return array
	 */
	static function get_fields() {

		$fields = [
			[
				'type'			=> 'textfield',
				'heading'		=> esc_attr__( 'Include Zips', 'geot' ),
				'description'	=> esc_attr__( 'Type Zip codes separated by commas.', 'geot' ),
				'param_name'	=> 'geot_in_zips',
				'default'		=> '',
				'group'			=> esc_attr__( 'GeoTargeting', 'geot' ),
			],
			[
				'type'			=> 'multiple_select',
				'heading'		=> esc_attr__( 'Include Zip Regions', 'geot' ),
				'description'	=> esc_attr__( 'Choose region name to show content to.', 'geot' ),
				'param_name'	=> 'geot_in_region_zips',
				'value'			=> GeotWP_Fusion::get_regions( 'zip' ),
				'default'		=> 'null',
				'group'			=> esc_attr__( 'GeoTargeting', 'geot' ),
			],
			[
				'type'			=> 'textfield',
				'heading'		=> esc_attr__( 'Exclude Zips', 'geot' ),
				'description'	=> esc_attr__( 'Type Zip codes separated by commas.', 'geot' ),
				'param_name'	=> 'geot_ex_zips',
				'default'		=> '',
				'group'			=> esc_attr__( 'GeoTargeting', 'geot' ),
			],
			[
				'type'			=> 'multiple_select',
				'heading'		=> esc_attr__( 'Exclude Zip Regions', 'geot' ),
				'description'	=> esc_attr__( 'Choose region name to show content to.', 'geot' ),
				'param_name'	=> 'geot_ex_region_zips',
				'value'			=> GeotWP_Fusion::get_regions( 'zip' ),
				'default'		=> 'null',
				'group'			=> esc_attr__( 'GeoTargeting', 'geot' ),
			]
		];

		return $fields;
	}


	/**
	 * Conditional if render
	 *
	 * @return bool
	 */
	static function is_render( $attrs ) {

		$in_zips = isset( $attrs['geot_in_zips'] ) ?  trim( $attrs['geot_in_zips'] ) : '';
		$ex_zips = isset( $attrs['geot_ex_zips'] ) ?  trim( $attrs['geot_ex_zips'] ) : '';
		
		$in_regions = isset( $attrs['geot_in_region_zips'] ) ? GeotWP_Fusion::clean_region( $attrs['geot_in_region_zips'] ) : [];
		$ex_regions = isset( $attrs['geot_ex_region_zips'] ) ? GeotWP_Fusion::clean_region( $attrs['geot_ex_region_zips'] ) : [];


		if ( empty( $in_zips ) && empty( $ex_zips ) &&
		     count( $in_regions ) == 0 && count( $ex_regions ) == 0
		) {
			return true;
		}

		return geot_target_zip( $in_zips, $in_regions, $ex_zips, $ex_regions );
	}


	/**
	 * if is ajax, apply render
	 *
	 * @return string
	 */
	static function ajax_render( $attrs, $output ) {

		$in_zips = isset( $attrs['geot_in_zips'] ) ?  trim( $attrs['geot_in_zips'] ) : '';
		$ex_zips = isset( $attrs['geot_ex_zips'] ) ?  trim( $attrs['geot_ex_zips'] ) : '';

		$in_regions = isset( $attrs['geot_in_region_zips'] ) ? $attrs['geot_in_region_zips'] : '';
		$ex_regions = isset( $attrs['geot_ex_region_zips'] ) ? $attrs['geot_ex_region_zips'] : '';


		if ( empty( $in_zips ) && empty( $ex_zips ) &&
		     ( empty( $in_regions ) || 'null' == $in_regions ) && ( empty( $ex_regions ) || 'null' == $ex_regions )
		) {
			return $output;
		}

		return '<div class="geot-ajax geot-filter" data-action="zip_filter" data-filter="' . $in_zips . '" data-region="' . $in_regions . '" data-ex_filter="' . $ex_zips . '" data-ex_region="' . $ex_regions . '">' . $output . '</div>';
	}

}