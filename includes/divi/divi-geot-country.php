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
class Divi_GeoCountry {

	/**
	 * Add the actual fields
	 *
	 * @return array
	 */
	static function get_fields() {

		$fields['in_countries'] = [
			'label'           => esc_html__( 'Include Countries', 'geot' ),
			'type'            => 'text',
			'option_category' => 'configuration',
			'description'     => esc_html__( 'Type country names or ISO codes separated by comma.', 'geot' ),
			'tab_slug'        => 'geot',
		];

		$fields['in_region_countries'] = [
			'label'           => esc_html__( 'Include Country Regions', 'geot' ),
			'type'            => 'multiple_checkboxes',
			'option_category' => 'configuration',
			'description'     => esc_html__( 'Choose region name to show content to.', 'geot' ),
			'options'         => GeotWP_Divi::get_regions( 'country' ),
			'option_category' => 'configuration',
			'tab_slug'        => 'geot',
		];

		$fields['ex_countries'] = [
			'label'           => esc_html__( 'Exclude Countries', 'geot' ),
			'type'            => 'text',
			'option_category' => 'configuration',
			'description'     => esc_html__( 'Type country names or ISO codes separated by comma.', 'geot' ),
			'tab_slug'        => 'geot',
		];

		$fields['ex_region_countries'] = [
			'label'           => esc_html__( 'Exclude Country Regions', 'geot' ),
			'type'            => 'multiple_checkboxes',
			'option_category' => 'configuration',
			'description'     => esc_html__( 'Choose region name to show content to.', 'geot' ),
			'options'         => GeotWP_Divi::get_regions( 'country' ),
			'tab_slug'        => 'geot',
		];

		return $fields;
	}


	/**
	 * Conditional if render
	 *
	 * @return array
	 */
	static function is_render( $settings, $regions ) {

		$in_countries = isset( $settings['in_countries'] ) ? trim( $settings['in_countries'] ) : '';
		$ex_countries = isset( $settings['ex_countries'] ) ? trim( $settings['ex_countries'] ) : '';

		$in_region_countries = isset( $settings['in_region_countries'] ) ? trim( $settings['in_region_countries'] ) : '';
		$ex_region_countries = isset( $settings['ex_region_countries'] ) ? trim( $settings['ex_region_countries'] ) : '';

		$in_regions = GeotWP_Divi::format_regions( $in_region_countries, '|', $regions );
		$ex_regions = GeotWP_Divi::format_regions( $ex_region_countries, '|', $regions );

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
	 * @return array
	 */
	static function ajax_render( $settings, $regions, $output ) {

		$in_regions_commas = $ex_regions_commas = '';

		$in_countries = isset( $settings['in_countries'] ) ? trim( $settings['in_countries'] ) : '';
		$ex_countries = isset( $settings['ex_countries'] ) ? trim( $settings['ex_countries'] ) : '';

		$in_region_countries = isset( $settings['in_region_countries'] ) ? trim( $settings['in_region_countries'] ) : '';
		$ex_region_countries = isset( $settings['ex_region_countries'] ) ? trim( $settings['ex_region_countries'] ) : '';
		

		$in_regions = GeotWP_Divi::format_regions( $in_region_countries, '|', $regions );
		$ex_regions = GeotWP_Divi::format_regions( $ex_region_countries, '|', $regions );

		if ( empty( $in_countries ) && empty( $ex_countries ) &&
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


		return '<div class="geot-ajax geot-filter" data-action="country_filter" data-filter="' . $in_countries . '" data-region="' . $in_regions_commas . '" data-ex_filter="' . $ex_countries . '" data-ex_region="' . $ex_regions_commas . '">' . $output . '</div>';
	}

}