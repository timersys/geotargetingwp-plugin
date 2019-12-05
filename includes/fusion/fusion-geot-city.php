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
	 * Add the actual fields
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
				'default'		=> '',
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
				'default'		=> '',
				'group'			=> esc_attr__( 'GeoTargeting', 'geot' ),
			]
		];

		return $fields;
	}


	/**
	 * Conditional if render
	 *
	 * @return array
	 */
	static function is_render( $attrs ) {

		extract( $attrs );

		$in_cities = trim( $geot_in_cities );
		$ex_cities = trim( $geot_ex_cities );
		$in_regions = array_map( 'trim', explode( ',', $geot_in_region_cities ) );
		$ex_regions = array_map( 'trim', explode( ',', $geot_ex_region_cities ) );

		if ( empty( $in_cities ) && empty( $ex_cities ) &&
		     count( $in_regions ) == 0 && count( $ex_regions ) == 0
		) {
			return true;
		}

		if ( geot_target_city( $in_cities, $in_regions, $ex_cities, $ex_regions ) ) {
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

		$in_cities = trim( $geot_in_cities );
		$ex_cities = trim( $geot_ex_cities );
		$in_regions = trim( $geot_in_region_cities );
		$ex_regions = trim( $geot_ex_region_cities );

		if ( empty( $in_countries ) && empty( $ex_countries ) &&
		     empty( $in_regions ) && empty( $ex_regions )
		) {
			return $output;
		}

		$in_regions_commas = array_map( 'trim', implode( ',', $in_regions ) );
		$ex_regions_commas = array_map( 'trim', implode( ',', $ex_regions ) );


		return '<div class="geot-ajax geot-filter" data-action="city_filter" data-filter="' . $in_cities . '" data-region="' . $in_regions_commas . '" data-ex_filter="' . $ex_cities . '" data-ex_region="' . $ex_regions_commas . '">' . $output . '</div>';
	}

}