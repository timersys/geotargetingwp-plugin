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
class Divi_GeoCity {

	/**
	 * Add the actual fields
	 *
	 * @return array
	 */
	static function get_fields() {

		$fields['in_cities'] = [
			'label'           => esc_html__( 'Include Cities', 'geot' ),
			'type'            => 'text',
			'option_category' => 'configuration',
			'description'     => esc_html__( 'Type city names separated by comma.', 'geot' ),
			'tab_slug'        => 'geot',
		];

		$fields['in_region_cities'] = [
			'label'           => esc_html__( 'Include City Regions', 'geot' ),
			'type'            => 'multiple_checkboxes',
			'option_category' => 'configuration',
			'description'     => esc_html__( 'Choose region name to show content to.', 'geot' ),
			'options'         => GeotWP_Divi::get_regions( 'city' ),
			'tab_slug'        => 'geot',
		];

		$fields['ex_cities'] = [
			'label'           => esc_html__( 'Exclude Cities', 'geot' ),
			'type'            => 'text',
			'option_category' => 'configuration',
			'description'     => esc_html__( 'Type city names separated by comma.', 'geot' ),
			'tab_slug'        => 'geot',
		];

		$fields['ex_region_cities'] = [
			'label'           => esc_html__( 'Exclude City Regions', 'geot' ),
			'type'            => 'multiple_checkboxes',
			'option_category' => 'configuration',
			'description'     => esc_html__( 'Choose region name to show content to.', 'geot' ),
			'options'         => GeotWP_Divi::get_regions( 'city' ),
			'tab_slug'        => 'geot',
		];

		return $fields;
	}


	/**
	 * Add the actual fields
	 *
	 * @return array
	 */
	static function is_render( $settings, $regions ) {

		$in_cities = isset( $settings['in_cities'] ) ? trim( $settings['in_cities'] ) : '';
		$ex_cities = isset( $settings['ex_cities'] ) ? trim( $settings['ex_cities'] ) : '';

		$in_region_cities = isset( $settings['in_region_cities'] ) ? trim( $settings['in_region_cities'] ) : '';
		$ex_region_cities = isset( $settings['ex_region_cities'] ) ? trim( $settings['ex_region_cities'] ) : '';


		$in_regions = GeotWP_Divi::format_regions( $in_region_cities, '|', $regions );
		$ex_regions = GeotWP_Divi::format_regions( $ex_region_cities, '|', $regions );

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
	 * @return array
	 */
	static function ajax_render( $settings, $regions, $output ) {

		$in_regions_commas = $ex_regions_commas = '';

		$in_cities = isset( $settings['in_cities'] ) ? trim( $settings['in_cities'] ) : '';
		$ex_cities = isset( $settings['ex_cities'] ) ? trim( $settings['ex_cities'] ) : '';

		$in_region_cities = isset( $settings['in_region_cities'] ) ? trim( $settings['in_region_cities'] ) : '';
		$ex_region_cities = isset( $settings['ex_region_cities'] ) ? trim( $settings['ex_region_cities'] ) : '';
		

		$in_regions = GeotWP_Divi::format_regions( $in_region_cities, '|', $regions );
		$ex_regions = GeotWP_Divi::format_regions( $ex_region_cities, '|', $regions );

		if ( empty( $in_cities ) && empty( $ex_cities ) &&
		     count( $in_regions ) == 0 && count( $ex_regions ) == 0
		) {
			return $output;
		}


		if ( count( $in_regions ) > 0 ) {
			$in_regions_commas = implode( ',', $in_regions );
		}

		if ( count( $ex_regions ) > 0 ) {
			$ex_regions_commas = implode( ',', $ex_regions );
		}


		return '<div class="geot-ajax geot-filter" data-action="city_filter" data-filter="' . $in_cities . '" data-region="' . $in_regions_commas . '" data-ex_filter="' . $ex_cities . '" data-ex_region="' . $ex_regions_commas . '">' . $output . '</div>';
	}

}