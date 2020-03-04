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
class WPBeaver_GeoZipcode {

	/**
	 * Add the actual fields
	 *
	 * @return array
	 */
	static function get_fields() {

		$section = [

			'title' => esc_html__( 'Geo ZipCodes', 'geot' ),
			'fields' => [

				'in_states' => [
					'type' => 'text',
					'label' => __( 'Include ZipCodes', 'Geot' ),
					'help' => esc_html__( 'Type zip codes separated by commas.', 'geot' ),
				],
				'in_region_zips' => [
					'type' => 'select',
					'multi-select' => true,
					'label' => __( 'Include Zip Regions', 'Geot' ),
					'options' => GeotWP_WPBeaver::get_regions( 'zip' ),
					'help' => esc_html__( 'Choose region name to show content to.', 'geot' ),
				],
				'ex_states' => [
					'type' => 'text',
					'label' => __( 'Exclude ZipCodes', 'Geot' ),
					'help' => esc_html__( 'Type zip codes separated by commas.', 'geot' ),
				],
				'ex_region_zips' => [
					'type' => 'select',
					'multi-select' => true,
					'label' => __( 'Exclude Zip Regions', 'Geot' ),
					'options' => GeotWP_WPBeaver::get_regions( 'zip' ),
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
		$in_region_zips = !empty( $settings['in_region_zips'] ) ? (array) $settings['in_region_zips']  : '';
		$ex_region_zips = !empty( $settings['ex_region_zips'] ) ? (array) $settings['ex_region_zips']  : '';


		if ( empty( $in_zipcodes ) && empty( $ex_zipcodes ) &&
			count( (array)$in_region_zips ) == 0 && count( (array)$ex_region_zips ) == 0
		) {
			return true;
		}


		if ( geot_target_zip( $in_zipcodes, $in_region_zips, $ex_zipcodes, $ex_region_zips ) ) {
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

		if ( empty( $in_zipcodes ) && empty( $ex_zipcodes ) &&
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


		return '<div class="geot-ajax geot-filter" data-action="zip_filter" data-filter="' . $in_zipcodes . '" data-region="' . $in_regions_commas . '" data-ex_filter="' . $ex_zipcodes . '" data-ex_region="' . $ex_regions_commas . '">' . $output . '</div>';
	}
}