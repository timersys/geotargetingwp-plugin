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
class Elementor_GeoZipcode {

	/**
	 * Get Fields in the Elementor Admin
	 *
	 * @param Class $control
	 *
	 */
	static function get_fields( $control ) {

		$control->start_controls_section(
			'zipcodes_section',
			[
				'label' => __( 'ZipCodes Settings', 'geot' ),
				'tab'   => 'geot',
			]
		);


		$control->add_control(
			'in_header_zipcodes',
			[
				'label'     => __( 'Include', 'geot' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$control->add_control(
			'in_help_zipcodes',
			[
				//'label' => __( 'Important Note', 'geot' ),
				'type'            => \Elementor\Controls_Manager::RAW_HTML,
				'raw'             => __( 'Type zip codes separated by commas.', 'geot' ),
				'content_classes' => 'elementor-descriptor',
			]
		);

		$control->add_control(
			'in_zipcodes',
			[
				'label'      => __( 'ZipCodes', 'geot' ),
				'type'       => \Elementor\Controls_Manager::TEXT,
				'input_type' => 'text',
				//'placeholder' => __( 'Choose region name to show content to', 'geot' ),
			]
		);

		$control->add_control(
			'in_regions_zips',
			[
				'label'    => __( 'Regions', 'geot' ),
				'type'     => \Elementor\Controls_Manager::SELECT2,
				'multiple' => true,
				'default'  => '',
				'options'  => GeotWP_Elementor::get_regions( 'zips' ),
			]
		);

		$control->add_control(
			'ex_header_zipcodes',
			[
				'label'     => __( 'Exclude', 'geot' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$control->add_control(
			'ex_help_zicodes',
			[
				'type'            => \Elementor\Controls_Manager::RAW_HTML,
				'raw'             => __( 'Type zip codes separated by commas.', 'geot' ),
				'content_classes' => 'elementor-descriptor',
			]
		);

		$control->add_control(
			'ex_zipcodes',
			[
				'label'      => __( 'ZipCodes', 'geot' ),
				'type'       => \Elementor\Controls_Manager::TEXT,
				'input_type' => 'text',
			]
		);

		$control->add_control(
			'ex_regions_zips',
			[
				'label'    => __( 'Regions', 'geot' ),
				'type'     => \Elementor\Controls_Manager::SELECT2,
				'multiple' => true,
				'default'  => '',
				'options'  => GeotWP_Elementor::get_regions( 'zips' ),
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

		$in_zipcodes = isset( $settings['in_zipcodes'] ) ? trim( $settings['in_zipcodes'] ) : '';
		$ex_zipcodes = isset( $settings['ex_zipcodes'] ) ? trim( $settings['ex_zipcodes'] ) : '';

		$in_regions_zips = isset( $settings['in_regions_zips'] ) && is_array( $settings['in_regions_zips'] ) ? GeotWP_Elementor::filter_regions( array_map( 'trim', $settings['in_regions_zips'] ) , 'zips' ): [];
		$ex_regions_zips = isset( $settings['ex_regions_zips'] ) && is_array( $settings['ex_regions_zips'] ) ? GeotWP_Elementor::filter_regions( array_map( 'trim', $settings['ex_regions_zips'] ) , 'zips') : [];


		if ( empty( $in_zipcodes ) && empty( $ex_zipcodes ) &&
			 empty( $in_regions_zips ) && empty( $ex_regions_zips )
		) {
			return true;
		}


		return geot_target_zip( $in_zipcodes, $in_regions_zips, $ex_zipcodes, $ex_regions_zips );
	}

	/**
	 *
	 * To Ajax mode, print HTML before
	 *
	 * @param Array $settings
	 *
	 */
	static function ajax_before_render( $settings ) {

		$in_zipcodes = isset( $settings['in_zipcodes'] ) ? trim( $settings['in_zipcodes'] ) : '';
		$ex_zipcodes = isset( $settings['ex_zipcodes'] ) ? trim( $settings['ex_zipcodes'] ) : '';

		$in_regions_zips = isset( $settings['in_regions_zips'] ) && is_array( $settings['in_regions_zips'] ) ? GeotWP_Elementor::filter_regions( array_map( 'trim', $settings['in_regions_zips'] ), 'zips' ): [];
		$ex_regions_zips = isset( $settings['ex_regions_zips'] ) && is_array( $settings['ex_regions_zips'] ) ? GeotWP_Elementor::filter_regions( array_map( 'trim', $settings['ex_regions_zips'] ), 'zips' ): [];

		$in_regions_i = $ex_regions_i = '';
		if ( empty( $in_zipcodes ) && empty( $ex_zipcodes ) &&
			empty( $in_regions_zips ) && empty( $ex_regions_zips )
		) {
			return;
		}

		if ( is_array( $in_regions_zips ) && count( $in_regions_zips ) > 0 ) {
			$in_regions_i = implode( ',', $in_regions_zips );
		}

		if ( is_array( $ex_regions_zips ) && count( $ex_regions_zips ) > 0 ) {
			$ex_regions_i = implode( ',', $ex_regions_zips );
		}

		echo '<div class="geot-ajax geot-filter" data-action="zip_filter" data-filter="' . $in_zipcodes . '" data-region="' . $in_regions_i . '" data-ex_filter="' . $ex_zipcodes . '" data-ex_region="' . $ex_regions_i . '">';
	}


	/**
	 *
	 * To Ajax mode, print HTML after
	 *
	 * @param Array $settings
	 *
	 */
	static function ajax_after_render( $settings ) {

		$in_zipcodes = isset( $settings['in_zipcodes'] ) ? trim( $settings['in_zipcodes'] ) : '';
		$ex_zipcodes = isset( $settings['ex_zipcodes'] ) ? trim( $settings['ex_zipcodes'] ) : '';

		$in_regions_zips = is_array( $settings['in_regions_zips'] ) ? GeotWP_Elementor::filter_regions( array_map( 'trim', $settings['in_regions_zips'] ), 'zips' ) : [];
		$ex_regions_zips = is_array( $settings['ex_regions_zips'] ) ? GeotWP_Elementor::filter_regions( array_map( 'trim', $settings['ex_regions_zips'] ), 'zips' ) : [];
		

		if ( empty( $in_zipcodes ) && empty( $ex_zipcodes ) &&
			empty( $in_regions_zips ) && empty( $ex_regions_zips )
		) {
			return;
		}

		echo '</div>';
	}

}

?>