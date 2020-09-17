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
	 * @param $settings
	 *
	 * @return bool
	 */
	static function is_render( $settings ) {

		if( is_object( $settings ) )
			$settings = get_object_vars($settings);

		
		$in_countries = isset( $settings['in_countries'] ) ? trim( $settings['in_countries'] ) : '';
		$ex_countries = isset( $settings['ex_countries'] ) ? trim( $settings['ex_countries'] ) : '';

		$in_region_countries = isset( $settings['in_region_countries'] ) && is_array( $settings['in_region_countries'] ) ? array_map( 'trim', $settings['in_region_countries'] ) : [];
		$ex_region_countries = isset( $settings['ex_region_countries'] ) && is_array( $settings['ex_region_countries'] ) ? array_map( 'trim', $settings['ex_region_countries'] ) : [];



		$in_region_countries = !empty( $in_region_countries )  &&  !empty( $in_region_countries[0] ) ? $in_region_countries  : [];
		$ex_region_countries = !empty( $ex_region_countries )  &&  !empty( $ex_region_countries[0] ) ? $ex_region_countries  : [];

		if ( empty( $in_countries ) && empty( $ex_countries ) &&
		     count( $in_region_countries ) == 0 && count( $ex_region_countries ) == 0
		) {
			return true;
		}


		return geot_target( $in_countries, $in_region_countries, $ex_countries, $ex_region_countries );
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

		
		$in_countries = isset( $settings['in_countries'] ) ? trim( $settings['in_countries'] ) : '';
		$ex_countries = isset( $settings['ex_countries'] ) ? trim( $settings['ex_countries'] ) : '';

		$in_region_countries = isset( $settings['in_region_countries'] ) && is_array( $settings['in_region_countries'] ) ? array_map( 'trim', $settings['in_region_countries'] ) : [];
		$ex_region_countries = isset( $settings['ex_region_countries'] ) && is_array( $settings['ex_region_countries'] ) ? array_map( 'trim', $settings['ex_region_countries'] ) : [];
		

		$in_region_countries = !empty( $in_region_countries )  &&  !empty( $in_region_countries[0] ) ? $in_region_countries  : [];
		$ex_region_countries = !empty( $ex_region_countries )  &&  !empty( $ex_region_countries[0] ) ? $ex_region_countries  : [];

		if ( empty( $in_countries ) && empty( $ex_countries ) &&
		     count( $in_region_countries ) == 0 && count( $ex_region_countries ) == 0
		) {
			return $output;
		}


		if ( count( $in_region_countries ) > 0 ) {
			$in_regions_commas = implode( ',', $in_region_countries );
		}

		if ( count( $ex_region_countries ) > 0 ) {
			$ex_regions_commas = implode( ',', $ex_region_countries );
		}


		return '<div class="geot-ajax geot-filter" data-action="country_filter" data-filter="' . $in_countries . '" data-region="' . $in_regions_commas . '" data-ex_filter="' . $ex_countries . '" data-ex_region="' . $ex_regions_commas . '">' . $output . '</div>';
	}

}