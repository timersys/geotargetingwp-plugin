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
class WPBeaver_GeoState {

	/**
	 * Add the actual fields
	 *
	 * @return array
	 */
	static function get_fields() {

		$section = [

			'title' => esc_html__( 'Geo States', 'geot' ),
			'fields' => [

				'in_states' => [
					'type' => 'text',
					'label' => __( 'Include States', 'Geot' ),
					'help' => esc_html__( 'Type state names or ISO codes separated by comma.', 'geot' ),
				],
				'in_region_states' => [
					'type' => 'select',
					'multi-select' => true,
					'label' => __( 'Include State Regions', 'Geot' ),
					'options' => GeotWP_WPBeaver::get_regions( 'state' ),
					'help' => esc_html__( 'Choose region name to show content to.', 'geot' ),
				],
				'ex_states' => [
					'type' => 'text',
					'label' => __( 'Exclude States', 'Geot' ),
					'help' => esc_html__( 'Type state names or ISO codes separated by comma.', 'geot' ),
				],
				'ex_region_states' => [
					'type' => 'select',
					'multi-select' => true,
					'label' => __( 'Exclude State Regions', 'Geot' ),
					'options' => GeotWP_WPBeaver::get_regions( 'state' ),
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

		
		$in_states = isset( $settings['in_states'] ) ? trim( $settings['in_states'] ) : '';
		$ex_states = isset( $settings['ex_states'] ) ? trim( $settings['ex_states'] ) : '';

		$in_region_states = isset( $settings['in_region_states'] ) && is_array( $settings['in_region_states'] ) ? array_map( 'trim', $settings['in_region_states'] ) : [];
		$ex_region_states = isset( $settings['ex_region_states'] ) && is_array( $settings['ex_region_states'] ) ? array_map( 'trim', $settings['ex_region_states'] ) : [];


		$in_region_states = !empty( $in_region_states ) &&  !empty( $in_region_states[0] ) ? $in_region_states  : [];
		$ex_region_states = !empty( $ex_region_states ) &&  !empty( $ex_region_states[0] ) ? $ex_region_states  : [];

		if ( empty( $in_states ) && empty( $ex_states ) &&
			count( $in_region_states ) == 0 && count( $ex_region_states ) == 0
		) {
			return true;
		}


		return geot_target_state( $in_states, $in_region_states, $ex_states, $ex_region_states );
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

		
		$in_states = isset( $settings['in_states'] ) ? trim( $settings['in_states'] ) : '';
		$ex_states = isset( $settings['ex_states'] ) ? trim( $settings['ex_states'] ) : '';

		$in_region_states = isset( $settings['in_region_states'] ) && is_array( $settings['in_region_states'] ) ? array_map( 'trim', $settings['in_region_states'] ) : [];
		$ex_region_states = isset( $settings['ex_region_states'] ) && is_array( $settings['ex_region_states'] ) ? array_map( 'trim', $settings['ex_region_states'] ) : [];
		

		$in_region_states = !empty( $in_region_states ) &&  !empty( $in_region_states[0] ) ? $in_region_states  : [];
		$ex_region_states = !empty( $ex_region_states ) &&  !empty( $ex_region_states[0] ) ? $ex_region_states  : [];

		if ( empty( $in_states ) && empty( $ex_states ) &&
			count( $in_region_states ) == 0 && count( $ex_region_states ) == 0
		) {
			return $output;
		}

		if ( count( $in_region_states ) > 0 ) {
			$in_regions_commas = implode( ',', $in_region_states );
		}

		if ( count( $ex_region_states ) > 0 ) {
			$ex_regions_commas = implode( ',', $ex_region_states );
		}

		return '<div class="geot-ajax geot-filter" data-action="state_filter" data-filter="' . $in_states . '" data-region="' . $in_regions_commas . '" data-ex_filter="' . $ex_states . '" data-ex_region="' . $ex_regions_commas . '">' . $output . '</div>';
	}
}