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
class Fusion_GeoCountry {

	/**
	 * Geot fields to Country
	 *
	 * @return array
	 */
	static function get_fields() {

		$fields = [
			[
				'type'			=> 'textfield',
				'heading'		=> esc_attr__( 'Include Countries', 'geot' ),
				'description'	=> esc_attr__( 'Type country name or ISO code. Also you can write a comma separated list of countries.', 'geot' ),
				'param_name'	=> 'geot_in_countries',
				'default'		=> '',
				'group'			=> esc_attr__( 'GeoTargeting', 'geot' ),
			],
			[
				'type'			=> 'multiple_select',
				'heading'		=> esc_attr__( 'Include Country Regions', 'geot' ),
				'description'	=> esc_attr__( 'Choose region name to show content to.', 'geot' ),
				'param_name'	=> 'geot_in_region_countries',
				'value'			=> GeotWP_Fusion::get_regions( 'country' ),
				'default'		=> 'null',
				'group'			=> esc_attr__( 'GeoTargeting', 'geot' ),
			],
			[
				'type'			=> 'textfield',
				'heading'		=> esc_attr__( 'Exclude Countries', 'geot' ),
				'description'	=> esc_attr__( 'Type country name or ISO code. Also you can write a comma separated list of countries.', 'geot' ),
				'param_name'	=> 'geot_ex_countries',
				'default'		=> '',
				'group'			=> esc_attr__( 'GeoTargeting', 'geot' ),
			],
			[
				'type'			=> 'multiple_select',
				'heading'		=> esc_attr__( 'Exclude Country Regions', 'geot' ),
				'description'	=> esc_attr__( 'Choose region name to show content to.', 'geot' ),
				'param_name'	=> 'geot_ex_region_countries',
				'value'			=> GeotWP_Fusion::get_regions( 'country' ),
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

		$in_countries = isset( $attrs['geot_in_countries'] ) ?  trim( $attrs['geot_in_countries'] ) : '';
		$ex_countries = isset( $attrs['geot_ex_countries'] ) ?  trim( $attrs['geot_ex_countries'] ) : '';
		
		$in_regions = isset( $attrs['geot_in_region_countries'] ) ? GeotWP_Fusion::clean_region( $attrs['geot_in_region_countries'] ) : [];
		$ex_regions = isset( $attrs['geot_ex_region_countries'] ) ? GeotWP_Fusion::clean_region( $attrs['geot_ex_region_countries'] ) : [];


		if ( empty( $in_countries ) && empty( $ex_countries ) &&
		     count( $in_regions ) == 0 && count( $ex_regions ) == 0
		) {
			return true;
		}

		return geot_target( $in_countries, $in_regions, $ex_countries, $ex_regions );
	}


	/**
	 * if is ajax, apply render
	 *
	 * @return string
	 */
	static function ajax_render( $attrs, $output ) {

		$in_countries = isset( $attrs['geot_in_countries'] ) ?  trim( $attrs['geot_in_countries'] ) : '';
		$ex_countries = isset( $attrs['geot_ex_countries'] ) ?  trim( $attrs['geot_ex_countries'] ) : '';

		$in_regions = isset( $attrs['geot_in_region_countries'] ) ? $attrs['geot_in_region_countries']  : '';
		$ex_regions = isset( $attrs['geot_ex_region_countries'] ) ? $attrs['geot_ex_region_countries']  : '';


		if ( empty( $in_countries ) && empty( $ex_countries ) &&
		     ( empty( $in_regions ) || 'null' == $in_regions ) &&  ( empty( $ex_regions ) || 'null' == $ex_regions )
		) {
			return $output;
		}

		return '<div class="geot-ajax geot-filter" data-action="country_filter" data-filter="' . $in_countries . '" data-region="' . $in_regions . '" data-ex_filter="' . $ex_countries . '" data-ex_region="' . $ex_regions . '">' . $output . '</div>';
	}
}