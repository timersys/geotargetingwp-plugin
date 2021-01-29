<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * Elementor Extension
 *
 * @link       https://geotargetingwp.com/geotargeting-pro
 * @since      1.6.3
 *
 * @package    GeoTarget
 * @subpackage GeoTarget/includes
 * @author     Damian Logghe
 */
class Elementor_GeoState {


	/**
	 *
	 * Get Fields in the Elementor Admin
	 *
	 * @param Class $control
	 *
	 */
	static function get_fields( $control ) {

		$control->start_controls_section(
			'states_section',
			[
				'label' => __( 'States Settings', 'geot' ),
				'tab'   => 'geot',
			]
		);


		$control->add_control(
			'in_header_states',
			[
				'label'     => __( 'Include', 'geot' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$control->add_control(
			'in_help_states',
			[
				//'label' => __( 'Important Note', 'geot' ),
				'type'            => \Elementor\Controls_Manager::RAW_HTML,
				'raw'             => __( 'Type state names or ISO codes separated by commas.', 'geot' ),
				'content_classes' => 'elementor-descriptor',
			]
		);

		$control->add_control(
			'in_states',
			[
				'label'      => __( 'States', 'geot' ),
				'type'       => \Elementor\Controls_Manager::TEXT,
				'input_type' => 'text',
				//'placeholder' => __( 'Choose region name to show content to', 'geot' ),
			]
		);

		$control->add_control(
			'in_regions_states',
			[
				'label'    => __( 'Regions', 'geot' ),
				'type'     => \Elementor\Controls_Manager::SELECT2,
				'multiple' => true,
				'default'  => '',
				'options'  => GeotWP_Elementor::get_regions( 'states' ),
			]
		);

		$control->add_control(
			'ex_header_states',
			[
				'label'     => __( 'Exclude', 'geot' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$control->add_control(
			'ex_help_states',
			[
				'type'            => \Elementor\Controls_Manager::RAW_HTML,
				'raw'             => __( 'Type state names or ISO codes separated by commas.', 'geot' ),
				'content_classes' => 'elementor-descriptor',
			]
		);

		$control->add_control(
			'ex_states',
			[
				'label'      => __( 'States', 'geot' ),
				'type'       => \Elementor\Controls_Manager::TEXT,
				'input_type' => 'text',
			]
		);

		$control->add_control(
			'ex_regions_states',
			[
				'label'    => __( 'Regions', 'geot' ),
				'type'     => \Elementor\Controls_Manager::SELECT2,
				'multiple' => true,
				'default'  => '',
				'options'  => GeotWP_Elementor::get_regions( 'states' ),
			]
		);

		$control->end_controls_section();

	}


	/**
	 *
	 * Conditional if it apply a render
	 *
	 * @param Array $settings
	 *
	 */
	static function is_render( $settings ) {

		$in_states = isset( $settings['in_states'] ) ? trim( $settings['in_states'] ) : '';
		$ex_states = isset( $settings['ex_states'] ) ? trim( $settings['ex_states'] ) : '';

		$in_regions_states = isset( $settings['in_regions_states'] ) && is_array( $settings['in_regions_states'] ) ? GeotWP_Elementor::filter_regions( array_map( 'trim', $settings['in_regions_states'] ), 'states' ) : [];
		$ex_regions_states = isset( $settings['ex_regions_states'] ) && is_array( $settings['ex_regions_states'] ) ? GeotWP_Elementor::filter_regions( array_map( 'trim', $settings['ex_regions_states'] ), 'states' ) : [];


		if ( empty( $in_states ) && empty( $ex_states ) &&
			empty( $in_regions_states ) && empty( $ex_regions_states )
		) {
			return true;
		}

		return geot_target_state( $in_states, $in_regions_states, $ex_states, $ex_regions_states );
	}


	/**
	 *
	 * To Ajax mode, print HTML before
	 *
	 * @param Array $settings
	 *
	 */
	static function ajax_before_render( $settings ) {

		$in_regions_i = $ex_regions_i = '';

		$in_states = isset( $settings['in_states'] ) ? trim( $settings['in_states'] ) : '';
		$ex_states = isset( $settings['ex_states'] ) ? trim( $settings['ex_states'] ) : '';

		$in_regions_states = isset( $settings['in_regions_states'] ) && is_array( $settings['in_regions_states'] ) ? GeotWP_Elementor::filter_regions( array_map( 'trim', $settings['in_regions_states'] ), 'states' ) : [];
		$ex_regions_states = isset( $settings['ex_regions_states'] ) && is_array( $settings['ex_regions_states'] ) ? GeotWP_Elementor::filter_regions( array_map( 'trim', $settings['ex_regions_states'] ), 'states' ) : [];


		if ( empty( $in_states ) && empty( $ex_states ) &&
			empty( $in_regions_states ) && empty( $ex_regions_states )
		) {
			return;
		}
		
		if ( is_array( $in_regions_states ) && count( $in_regions_states ) > 0 ) {
			$in_regions_i = implode( ',', $in_regions_states );
		}

		if ( is_array( $ex_regions_states ) && count( $ex_regions_states ) > 0 ) {
			$ex_regions_i = implode( ',', $ex_regions_states );
		}

		echo '<div class="geot-ajax geot-filter" data-action="state_filter" data-filter="' . $in_states . '" data-region="' . $in_regions_i . '" data-ex_filter="' . $ex_states . '" data-ex_region="' . $ex_regions_i . '">';
	}


	/**
	 *
	 * To Ajax mode, print HTML after
	 *
	 * @param Array $settings
	 *
	 */
	static function ajax_after_render( $settings ) {

		$in_states = isset( $settings['in_states'] ) ? trim( $settings['in_states'] ) : '';
		$ex_states = isset( $settings['ex_states'] ) ? trim( $settings['ex_states'] ) : '';

		$in_regions_states = is_array( $settings['in_regions_states'] ) ? GeotWP_Elementor::filter_regions( array_map( 'trim', $settings['in_regions_states'] ), 'states' ) : [];
		$ex_regions_states = is_array( $settings['ex_regions_states'] ) ? GeotWP_Elementor::filter_regions( array_map( 'trim', $settings['ex_regions_states'] ), 'states' ) : [];


		if ( empty( $in_states ) && empty( $ex_states ) &&
			empty( $in_regions_states ) && empty( $ex_regions_states )
		) {
			return;
		}

		echo '</div>';
	}

}

?>