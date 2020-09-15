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

				'in_zipcodes' => [
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
				'ex_zipcodes' => [
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

		if( is_object( $settings ) )
			$settings = get_object_vars($settings);

		
		$in_zipcodes = isset( $settings['in_zipcodes'] ) ? trim( $settings['in_zipcodes'] ) : '';
		$ex_zipcodes = isset( $settings['ex_zipcodes'] ) ? trim( $settings['ex_zipcodes'] ) : '';

		$in_region_zips = isset( $settings['in_region_zips'] ) && is_array( $settings['in_region_zips'] ) ? array_map( 'trim', $settings['in_region_zips'] ) : [];
		$ex_region_zips = isset( $settings['ex_region_zips'] ) && is_array( $settings['ex_region_zips'] ) ? array_map( 'trim', $settings['ex_region_zips'] ) : [];

		
		$in_region_zips = !empty( $in_region_zips ) &&  !empty( $in_region_zips[0] ) ? $in_region_zips  : [];
		$ex_region_zips = !empty( $ex_region_zips ) &&  !empty( $ex_region_zips[0] ) ? $ex_region_zips  : [];


		if ( empty( $in_zipcodes ) && empty( $ex_zipcodes ) &&
			count( $in_region_zips ) == 0 && count( $ex_region_zips ) == 0
		) {
			return true;
		}


		return geot_target_zip( $in_zipcodes, $in_region_zips, $ex_zipcodes, $ex_region_zips );
	}


	/**
	 * if is ajax, apply render
	 *
	 * @return array
	 */
	static function ajax_render( $settings, $output ) {

		$in_regions_commas = $ex_regions_commas = '';

		if( is_object( $settings ) )
			$settings = get_object_vars($settings);

		
		$in_zipcodes = isset( $settings['in_zipcodes'] ) ? trim( $settings['in_zipcodes'] ) : '';
		$ex_zipcodes = isset( $settings['ex_zipcodes'] ) ? trim( $settings['ex_zipcodes'] ) : '';

		$in_region_zips = isset( $settings['in_region_zips'] ) && is_array( $settings['in_region_zips'] ) ? array_map( 'trim', $settings['in_region_zips'] ) : [];
		$ex_region_zips = isset( $settings['ex_region_zips'] ) && is_array( $settings['ex_region_zips'] ) ? array_map( 'trim', $settings['ex_region_zips'] ) : [];
		

		$in_region_zips = !empty( $in_region_zips ) &&  !empty( $in_region_zips[0] ) ? $in_region_zips  : [];
		$ex_region_zips = !empty( $ex_region_zips ) &&  !empty( $ex_region_zips[0] ) ? $ex_region_zips  : [];

		if ( empty( $in_zipcodes ) && empty( $ex_zipcodes ) &&
			count( $in_region_zips ) == 0 && count( $ex_region_zips ) == 0
		) {
			return $output;
		}


		if ( count( $in_region_zips ) > 0 ) {
			$in_regions_commas = implode( ',', $in_region_zips );
		}

		if ( count( $ex_region_zips ) > 0 ) {
			$ex_regions_commas = implode( ',', $ex_region_zips );
		}


		return '<div class="geot-ajax geot-filter" data-action="zip_filter" data-filter="' . $in_zipcodes . '" data-region="' . $in_regions_commas . '" data-ex_filter="' . $ex_zipcodes . '" data-ex_region="' . $ex_regions_commas . '">' . $output . '</div>';
	}
}