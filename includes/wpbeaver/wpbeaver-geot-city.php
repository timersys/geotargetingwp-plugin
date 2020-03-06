<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * WPBeaver Geo Module
 *
 * @link       https://geotargetingwp.com/geotargeting-pro
 * @since      1.6.3
 *
 * @package    GeoTarget
 * @subpackage GeoTarget/includes
 * @author     Damian Logghe
 */
class WPBeaver_GeoCity {

	/**
	 * Add the actual fields
	 *
	 * @return array
	 */
	static function get_fields() {

		$section = [

			'title' => esc_html__( 'Geo Cities', 'geot' ),
			'fields' => [

				'in_cities' => [
					'type' => 'text',
					'label' => __( 'Include Cities', 'Geot' ),
					'help' => esc_html__( 'Type city names separated by comma.', 'geot' ),
				],
				'in_region_cities' => [
					'type' => 'select',
					'multi-select' => true,
					'label' => __( 'Include City Regions', 'Geot' ),
					'options' => GeotWP_WPBeaver::get_regions( 'city' ),
					'help' => esc_html__( 'Choose region name to show content to.', 'geot' ),
				],
				'ex_cities' => [
					'type' => 'text',
					'label' => __( 'Exclude Cities', 'Geot' ),
					'help' => esc_html__( 'Type city names separated by comma.', 'geot' ),
				],
				'ex_region_cities' => [
					'type' => 'select',
					'multi-select' => true,
					'label' => __( 'Exclude City Regions', 'Geot' ),
					'options' => GeotWP_WPBeaver::get_regions( 'city' ),
					'help' => esc_html__( 'Choose region name to show content to.', 'geot' ),
				],
			],
		];

		return $section;
	}


	/**
	 * Conditional if render
	 *
	 * @return array
	 */
	static function is_render( $settings ) {

		extract((array)$settings);
		$in_region_cities = !empty( (array)$settings['in_region_cities'] ) ? (array) $settings['in_region_cities']  : '';
		$ex_region_cities = !empty( (array)$settings['ex_region_cities'] ) ? (array) $settings['ex_region_cities']  : '';

		if ( empty( $in_cities ) && empty( $ex_cities ) &&
		     count( $in_region_cities ) == 0 && count( $ex_region_cities ) == 0
		) {
			return true;
		}


		if ( geot_target_city( $in_cities, $in_region_cities, $ex_cities, $ex_region_cities ) ) {
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

		$in_regions_commas = $ex_regions_commas = '';

		extract( (array)$settings );

		if ( empty( $in_cities ) && empty( $ex_cities ) &&
		     count( (array)$in_region_cities ) == 0 && count( (array)$ex_region_cities ) == 0
		) {
			return $output;
		}


		if ( count( (array)$in_region_cities ) > 0 ) {
			$in_regions_commas = implode( ',', (array)$in_region_cities );
		}

		if ( count( (array)$ex_region_cities ) > 0 ) {
			$ex_regions_commas = implode( ',', (array)$ex_region_cities );
		}


		return '<div class="geot-ajax geot-filter" data-action="city_filter" data-filter="' . $in_countries . '" data-region="' . $in_regions_commas . '" data-ex_filter="' . $ex_countries . '" data-ex_region="' . $ex_regions_commas . '">' . $output . '</div>';
	}

}