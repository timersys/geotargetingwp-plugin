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
				'ex_states' => [
					'type' => 'text',
					'label' => __( 'Exclude States', 'Geot' ),
					'help' => esc_html__( 'Type state names or ISO codes separated by comma.', 'geot' ),
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

		extract($settings);

		if ( empty( $in_states ) && empty( $ex_states ) ) {
			return true;
		}


		if ( geot_target_state( $in_states, $ex_states ) ) {
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

		if( is_object( $settings ) )
			$settings = get_object_vars($settings);

		extract( $settings );

		if ( empty( $in_states ) && empty( $ex_states ) ) {
			return $output;
		}


		return '<div class="geot-ajax geot-filter" data-action="state_filter" data-filter="' . $in_states . '" data-ex_filter="' . $ex_states . '">' . $output . '</div>';
	}
}