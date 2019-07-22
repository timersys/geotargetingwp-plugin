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
class WPBeaver_GeoCountry {

	/**
	 * Add the actual fields
	 *
	 * @return array
	 */
	static function get_fields() {

		$section = [

			'title' => esc_html__( 'Geo Countries', 'geot' ),
			'fields' => [

				'in_countries' => [
					'type' => 'text',
					'label' => __( 'Include Countries', 'Geot' ),
					'help' => esc_html__( 'Type country names or ISO codes separated by comma.', 'geot' ),
				],
				'in_region_countries' => [
					'type' => 'select',
					'multi-select' => true,
					'label' => __( 'Include Region Countries', 'Geot' ),
					'options' => GeotWP_WPBeaver::get_regions( 'country' ),
					'help' => esc_html__( 'Choose region name to show content to.', 'geot' ),
				],
				'ex_countries' => [
					'type' => 'text',
					'label' => __( 'Exclude Countries', 'Geot' ),
					'help' => esc_html__( 'Type country names or ISO codes separated by comma.', 'geot' ),
				],
				'ex_region_countries' => [
					'type' => 'select',
					'multi-select' => true,
					'label' => __( 'Exclude Region Countries', 'Geot' ),
					'options' => GeotWP_WPBeaver::get_regions( 'country' ),
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


		if ( empty( $in_countries ) && empty( $ex_countries ) &&
		     count( (array)$in_region_countries ) == 0 && count( (array)$ex_region_countries ) == 0
		) {
			return true;
		}


		if ( geot_target( $in_countries, $in_region_countries, $ex_countries, $ex_region_countries ) ) {
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

		if ( empty( $in_countries ) && empty( $ex_countries ) &&
		     count( (array)$in_region_countries ) == 0 && count( (array)$ex_region_countries ) == 0
		) {
			return $output;
		}


		if ( count( (array)$in_region_countries ) > 0 ) {
			$in_regions_commas = implode( ',', (array)$in_region_countries );
		}

		if ( count( (array)$ex_region_countries ) > 0 ) {
			$ex_regions_commas = implode( ',', (array)$ex_region_countries );
		}


		return '<div class="geot-ajax geot-filter" data-action="country_filter" data-filter="' . $in_countries . '" data-region="' . $in_regions_commas . '" data-ex_filter="' . $ex_countries . '" data-ex_region="' . $ex_regions_commas . '">' . $output . '</div>';
	}

}