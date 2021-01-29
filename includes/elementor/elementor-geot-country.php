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
class Elementor_GeoCountry {

	/**
	 *
	 * Get Fields in the Elementor Admin
	 *
	 * @param Class $control
	 *
	 */
	static function get_fields( $control ) {

		$control->start_controls_section(
			'countries_section',
			[
				'label' => __( 'Countries Settings', 'geot' ),
				'tab'   => 'geot',
			]
		);


		$control->add_control(
			'in_header_countries',
			[
				'label'     => __( 'Include', 'geot' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$control->add_control(
			'in_help_countries',
			[
				//'label' => __( 'Important Note', 'geot' ),
				'type'            => \Elementor\Controls_Manager::RAW_HTML,
				'raw'             => __( 'Type country names or ISO codes separated by commas.', 'geot' ),
				'content_classes' => 'elementor-descriptor',
			]
		);


		$control->add_control(
			'in_countries',
			[
				'label'      => __( 'Countries', 'geot' ),
				'type'       => \Elementor\Controls_Manager::TEXT,
				'input_type' => 'text',
				//'placeholder' => __( 'Choose region name to show content to', 'geot' ),
			]
		);


		$control->add_control(
			'in_regions',
			[
				'label'    => __( 'Regions', 'geot' ),
				'type'     => \Elementor\Controls_Manager::SELECT2,
				'multiple' => true,
				'default'  => '',
				'options'  => GeotWP_Elementor::get_regions( 'countries' ),
			]
		);

		$control->add_control(
			'ex_header_countries',
			[
				'label'     => __( 'Exclude', 'geot' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$control->add_control(
			'ex_help_countries',
			[
				'type'            => \Elementor\Controls_Manager::RAW_HTML,
				'raw'             => __( 'Type country names or ISO codes separated by commas.', 'geot' ),
				'content_classes' => 'elementor-descriptor',
			]
		);

		$control->add_control(
			'ex_countries',
			[
				'label'      => __( 'Countries', 'geot' ),
				'type'       => \Elementor\Controls_Manager::TEXT,
				'input_type' => 'text',
			]
		);

		$control->add_control(
			'ex_regions',
			[
				'label'    => __( 'Regions', 'geot' ),
				'type'     => \Elementor\Controls_Manager::SELECT2,
				'multiple' => true,
				'default'  => '',
				'options'  => GeotWP_Elementor::get_regions( 'countries' ),
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

		$in_countries = isset( $settings['in_countries'] ) ? trim( $settings['in_countries'] ) : '';
		$ex_countries = isset( $settings['ex_countries'] ) ? trim( $settings['ex_countries'] ) : '';

		$in_regions = isset( $settings['in_regions'] ) && is_array( $settings['in_regions'] ) ? GeotWP_Elementor::filter_regions( array_map( 'trim', $settings['in_regions'] ), 'countries' ) : [];
		$ex_regions = isset( $settings['ex_regions'] ) && is_array( $settings['ex_regions'] ) ? GeotWP_Elementor::filter_regions( array_map( 'trim', $settings['ex_regions'] ), 'countries' ) : [];


		if ( empty( $in_countries ) && empty( $ex_countries ) &&
		     empty( $in_regions ) && empty( $ex_regions )
		) {
			return true;
		}

		return geot_target( $in_countries, $in_regions, $ex_countries, $ex_regions );
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
		
		$in_countries = isset( $settings['in_countries'] ) ? trim( $settings['in_countries'] ) : '';
		$ex_countries = isset( $settings['ex_countries'] ) ? trim( $settings['ex_countries'] ) : '';

		$in_regions = isset( $settings['in_regions'] ) && is_array( $settings['in_regions'] ) ? GeotWP_Elementor::filter_regions( array_map( 'trim', $settings['in_regions'] ), 'countries' ) : [];
		$ex_regions = isset( $settings['ex_regions'] ) && is_array( $settings['ex_regions'] ) ? GeotWP_Elementor::filter_regions( array_map( 'trim', $settings['ex_regions'] ), 'countries' ) : [];


		if ( empty( $in_countries ) && empty( $ex_countries ) &&
		     empty( $in_regions ) && empty( $ex_regions )
		) {
			return;
		}

		if ( is_array( $in_regions ) && count( $in_regions ) > 0 ) {
			$in_regions_i = implode( ',', $in_regions );
		}

		if ( is_array( $ex_regions ) && count( $ex_regions ) > 0 ) {
			$ex_regions_i = implode( ',', $ex_regions );
		}


		echo '<div class="geot-ajax geot-filter" data-action="country_filter" data-filter="' . $in_countries . '" data-region="' . $in_regions_i . '" data-ex_filter="' . $ex_countries . '" data-ex_region="' . $ex_regions_i . '">';
	}


	/**
	 *
	 * To Ajax mode, print HTML after
	 *
	 * @param Array $settings
	 *
	 */
	static function ajax_after_render( $settings ) {

		$in_countries = isset( $settings['in_countries'] ) ? trim( $settings['in_countries'] ) : '';
		$ex_countries = isset( $settings['ex_countries'] ) ? trim( $settings['ex_countries'] ) : '';

		$in_regions = is_array( $settings['in_regions'] ) ? GeotWP_Elementor::filter_regions( array_map( 'trim', $settings['in_regions'] ), 'countries' ) : [];
		$ex_regions = is_array( $settings['ex_regions'] ) ? GeotWP_Elementor::filter_regions( array_map( 'trim', $settings['ex_regions'] ), 'countries' ) : [];


		if ( empty( $in_countries ) && empty( $ex_countries ) &&
		     empty( $in_regions ) && empty( $ex_regions )
		) {
			return;
		}

		echo '</div>';
	}

}

?>