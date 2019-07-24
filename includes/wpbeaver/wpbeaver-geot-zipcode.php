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
				'ex_states' => [
					'type' => 'text',
					'label' => __( 'Exclude ZipCodes', 'Geot' ),
					'help' => esc_html__( 'Type zip codes separated by commas.', 'geot' ),
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


		if ( empty( $in_zipcodes ) && empty( $ex_zipcodes ) ) {
			return true;
		}


		if ( geot_target_zip( $in_zipcodes, $ex_zipcodes ) ) {
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

		extract( (array)$settings );

		if ( empty( $in_zipcodes ) && empty( $ex_zipcodes ) ) {
			return $output;
		}


		return '<div class="geot-ajax geot-filter" data-action="zip_filter" data-filter="' . $in_zipcodes . '" data-ex_filter="' . $ex_zipcodes . '">' . $output . '</div>';
	}
}