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
class Divi_GeoState {

	/**
	 * Add the actual fields
	 *
	 * @return array
	 */
	static function get_fields() {

		$fields['in_states'] = [
			'label'           => esc_html__( 'Include States', 'geot' ),
			'type'            => 'text',
			'option_category' => 'configuration',
			'description'     => esc_html__( 'Type state names or ISO codes separated by comma.', 'geot' ),
			'tab_slug'        => 'geot',
		];

		$fields['in_region_states'] = [
			'label'           => esc_html__( 'Include State Regions', 'geot' ),
			'type'            => 'multiple_checkboxes',
			'option_category' => 'configuration',
			'description'     => esc_html__( 'Choose region name to show content to.', 'geot' ),
			'options'         => GeotWP_Divi::get_regions( 'state' ),
			'tab_slug'        => 'geot',
		];

		$fields['ex_states'] = [
			'label'           => esc_html__( 'Exclude States', 'geot' ),
			'type'            => 'text',
			'option_category' => 'configuration',
			'description'     => esc_html__( 'Type state names or ISO codes separated by comma.', 'geot' ),
			'tab_slug'        => 'geot',
		];

		$fields['ex_region_states'] = [
			'label'           => esc_html__( 'Exclude State Regions', 'geot' ),
			'type'            => 'multiple_checkboxes',
			'option_category' => 'configuration',
			'description'     => esc_html__( 'Choose region name to show content to.', 'geot' ),
			'options'         => GeotWP_Divi::get_regions( 'state' ),
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

		$in_states = isset( $settings['in_states'] ) ? trim( $settings['in_states'] ) : '';
		$ex_states = isset( $settings['ex_states'] ) ? trim( $settings['ex_states'] ) : '';

		$in_region_states = isset( $settings['in_region_states'] ) ? trim( $settings['in_region_states'] ) : '';
		$ex_region_states = isset( $settings['ex_region_states'] ) ? trim( $settings['ex_region_states'] ) : '';

		$in_regions = GeotWP_Divi::format_regions( $in_region_states, '|', $regions );
		$ex_regions = GeotWP_Divi::format_regions( $ex_region_states, '|', $regions );

		if ( empty( $in_states ) && empty( $ex_states ) &&
			count( $in_regions ) == 0 && count( $ex_regions ) == 0
		) {
			return true;
		}

		return geot_target_state( $in_states, $in_regions, $ex_states, $ex_regions );
	}


	/**
	 * if is ajax, apply render
	 *
	 * @return array
	 */
	static function ajax_render( $settings, $regions, $output ) {

		$in_regions_commas = $ex_regions_commas = '';

		$in_states = isset( $settings['in_states'] ) ? trim( $settings['in_states'] ) : '';
		$ex_states = isset( $settings['ex_states'] ) ? trim( $settings['ex_states'] ) : '';

		$in_region_states = isset( $settings['in_region_states'] ) ? trim( $settings['in_region_states'] ) : '';
		$ex_region_states = isset( $settings['ex_region_states'] ) ? trim( $settings['ex_region_states'] ) : '';

		$in_regions = GeotWP_Divi::format_regions( $in_region_states, '|', $regions );
		$ex_regions = GeotWP_Divi::format_regions( $ex_region_states, '|', $regions );

		if ( empty( $in_states ) && empty( $ex_states ) &&
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


		return '<div class="geot-ajax geot-filter" data-action="state_filter" data-filter="' . $in_states . '" data-region="' . $in_regions_commas . '" data-ex_filter="' . $ex_states . '" data-ex_region="' . $ex_regions_commas . '">' . $output . '</div>';
	}

}