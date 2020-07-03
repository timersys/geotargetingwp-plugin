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
class Divi_GeoZipcode {

	/**
	 * Add the actual fields
	 *
	 * @return array
	 */
	static function get_fields() {

		$fields['in_zipcodes'] = [
			'label'           => esc_html__( 'Include ZipCodes', 'geot' ),
			'type'            => 'text',
			'option_category' => 'configuration',
			'description'     => esc_html__( 'Type zip codes separated by commas.', 'geot' ),
			'tab_slug'        => 'geot',
		];

		$fields['in_region_zips'] = [
			'label'           => esc_html__( 'Include Zip Regions', 'geot' ),
			'type'            => 'multiple_checkboxes',
			'option_category' => 'configuration',
			'description'     => esc_html__( 'Choose region name to show content to.', 'geot' ),
			'options'         => GeotWP_Divi::get_regions( 'zip' ),
			'tab_slug'        => 'geot',
		];

		$fields['ex_zipcodes'] = [
			'label'           => esc_html__( 'Exclude ZipCodes', 'geot' ),
			'type'            => 'text',
			'option_category' => 'configuration',
			'description'     => esc_html__( 'Type zip codes separated by comma.', 'geot' ),
			'tab_slug'        => 'geot',
		];

		$fields['ex_region_zips'] = [
			'label'           => esc_html__( 'Exclude Zip Regions', 'geot' ),
			'type'            => 'multiple_checkboxes',
			'option_category' => 'configuration',
			'description'     => esc_html__( 'Choose region name to show content to.', 'geot' ),
			'options'         => GeotWP_Divi::get_regions( 'zip' ),
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

		extract( $settings );

		$in_regions = GeotWP_Divi::format_regions( $in_region_zips, '|', $regions );
		$ex_regions = GeotWP_Divi::format_regions( $ex_region_zips, '|', $regions );

		if ( empty( $in_zipcodes ) && empty( $ex_zipcodes ) &&
			count( $in_regions ) == 0 && count( $ex_regions ) == 0
		) {
			return true;
		}

		return geot_target_zip( $in_zipcodes, $in_regions, $ex_zipcodes, $ex_regions );

	}


	/**
	 * if is ajax, apply render
	 *
	 * @return array
	 */
	static function ajax_render( $settings, $output ) {

		$in_regions_commas = $ex_regions_commas = '';

		extract( $settings );

		$in_regions = GeotWP_Divi::format_regions( $in_region_zips, '|', $regions );
		$ex_regions = GeotWP_Divi::format_regions( $ex_region_zips, '|', $regions );

		if ( empty( $in_zipcodes ) && empty( $ex_zipcodes ) &&
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


		return '<div class="geot-ajax geot-filter" data-action="zip_filter" data-filter="' . $in_zipcodes . '" data-region="' . $in_regions_commas . '" data-ex_filter="' . $ex_zipcodes . '" data-ex_region="' . $ex_regions_commas . '">' . $output . '</div>';
	}

}