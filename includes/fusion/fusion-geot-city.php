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
class Fusion_GeoCity {

	/**
	 * Geot fields to City
	 *
	 * @return array
	 */
	static function get_fields() {

		$fields = [
			[
				'type'			=> 'textfield',
				'heading'		=> esc_attr__( 'Include Cities', 'geot' ),
				'description'	=> esc_attr__( 'Type city names separated by commas.', 'geot' ),
				'param_name'	=> 'geot_in_cities',
				'default'		=> '',
				'group'			=> esc_attr__( 'GeoTargeting', 'geot' ),
			],
			[
				'type'			=> 'multiple_select',
				'heading'		=> esc_attr__( 'Include City Regions', 'geot' ),
				'description'	=> esc_attr__( 'Choose region name to show content to.', 'geot' ),
				'param_name'	=> 'geot_in_region_cities',
				'value'			=> GeotWP_Fusion::get_regions( 'city' ),
				'default'		=> 'null',
				'group'			=> esc_attr__( 'GeoTargeting', 'geot' ),
			],
			[
				'type'			=> 'textfield',
				'heading'		=> esc_attr__( 'Exclude Cities', 'geot' ),
				'description'	=> esc_attr__( 'Type city names separated by commas.', 'geot' ),
				'param_name'	=> 'geot_ex_cities',
				'default'		=> '',
				'group'			=> esc_attr__( 'GeoTargeting', 'geot' ),
			],
			[
				'type'			=> 'multiple_select',
				'heading'		=> esc_attr__( 'Exclude City Regions', 'geot' ),
				'description'	=> esc_attr__( 'Choose region name to show content to.', 'geot' ),
				'param_name'	=> 'geot_ex_region_cities',
				'value'			=> GeotWP_Fusion::get_regions( 'city' ),
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

		$in_cities = isset( $attrs['geot_in_cities'] ) ?  trim( $attrs['geot_in_cities'] ) : '';
		$ex_cities = isset( $attrs['geot_ex_cities'] ) ?  trim( $attrs['geot_ex_cities'] ) : '';
		
		$in_regions = isset( $attrs['geot_in_region_cities'] ) ? GeotWP_Fusion::clean_region( $attrs['geot_in_region_cities'] ) : [];
		$ex_regions = isset( $attrs['geot_ex_region_cities'] ) ? GeotWP_Fusion::clean_region( $attrs['geot_ex_region_cities'] ) : [];


		if ( empty( $in_cities ) && empty( $ex_cities ) &&
		     count( $in_regions ) == 0 && count( $ex_regions ) == 0
		) {
			return true;
		}

		return geot_target_city( $in_cities, $in_regions, $ex_cities, $ex_regions );
	}


	/**
	 * if is ajax, apply render
	 *
	 * @return string
	 */
	static function ajax_render( $attrs, $output ) {

		$in_cities = isset( $attrs['geot_in_cities'] ) ?  trim( $attrs['geot_in_cities'] ) : '';
		$ex_cities = isset( $attrs['geot_ex_cities'] ) ?  trim( $attrs['geot_ex_cities'] ) : '';

		$in_regions = isset( $attrs['geot_in_region_cities'] ) ? $attrs['geot_in_region_cities'] : '';
		$ex_regions = isset( $attrs['geot_ex_region_cities'] ) ? $attrs['geot_ex_region_cities'] : '';


		if ( empty( $in_cities ) && empty( $ex_cities ) &&
		     ( empty( $in_regions ) || 'null' == $in_regions ) && ( empty( $ex_regions ) || 'null' == $ex_regions )
		) {
			return $output;
		}

		return '<div class="geot-ajax geot-filter" data-action="city_filter" data-filter="' . $in_cities . '" data-region="' . $in_regions . '" data-ex_filter="' . $ex_cities . '" data-ex_region="' . $ex_regions . '">' . $output . '</div>';
	}

}