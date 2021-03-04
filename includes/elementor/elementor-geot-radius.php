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
class Elementor_GeoRadius {

	/**
	 * Get Fields in the Elementor Admin
	 *
	 * @param Class $control
	 *
	 */
	static function get_fields( $control ) {

		$control->start_controls_section(
			'radius_section',
			[
				'label' => __( 'Radius Settings', 'geot' ),
				'tab'   => 'geot',
			]
		);


		$control->add_control(
			'in_header_radius',
			[
				'label'     => __( 'Radius', 'geot' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		/*$control->add_control(
			'in_help_zipcodes',
			[
				//'label' => __( 'Important Note', 'geot' ),
				'type'            => \Elementor\Controls_Manager::RAW_HTML,
				'raw'             => __( 'Type zip codes separated by commas.', 'geot' ),
				'content_classes' => 'elementor-descriptor',
			]
		);*/
		$control->add_control(
			'radius_mode',
			[
				'label'			=> esc_html__( 'Geo Mode', 'geot' ),
				'type'			=> \Elementor\Controls_Manager::CHOOSE,
				'options' => [
					'include' => [
						'title' => esc_html__( 'Include', 'geot' ),
						'icon' => 'fa fa-check',
					],
					'exclude' => [
						'title' => esc_html__( 'Exclude', 'geot' ),
						'icon' => 'fa fa-times',
					],

				],
				'default' => 'include',
				'toggle' => true,
			]
		);

		$control->add_control(
			'radius_km',
			[
				'label'			=> esc_html__( 'Radius (km)', 'geot' ),
				'type'			=> \Elementor\Controls_Manager::NUMBER,
				'step'			=> '1',
				'placeholder'	=> '100',
			]
		);

		$control->add_control(
			'radius_lat',
			[
				'label'			=> esc_html__( 'Latitude', 'geot' ),
				'type'			=> \Elementor\Controls_Manager::NUMBER,
				'step'			=> '0.0000001',
			]
		);

		$control->add_control(
			'radius_lng',
			[
				'label'			=> esc_html__( 'Longitude', 'geot' ),
				'type'			=> \Elementor\Controls_Manager::NUMBER,
				'step'			=> '0.0000001',
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

		$radius_km	= isset( $settings['radius_km'] ) ? trim( $settings['radius_km'] ) : '';
		$radius_lat = isset( $settings['radius_lat'] ) ? trim( $settings['radius_lat'] ) : '';
		$radius_lng = isset( $settings['radius_lng'] ) ? trim( $settings['radius_lng'] ) : '';


		if ( empty( $radius_km ) || empty( $radius_lat ) || empty( $radius_lng ) ) {
			return true;
		}
		$target = geot_target_radius( $radius_lat, $radius_lng, $radius_km );
		return $settings['radius_mode'] == 'include'  ? $target : ! $target;
	}


	/**
	 *
	 * To Ajax mode, print HTML before
	 *
	 * @param Array $settings
	 *
	 */
	static function ajax_before_render( $settings ) {

		$radius_km	= isset( $settings['radius_km'] ) ? trim( $settings['radius_km'] ) : '';
		$radius_lat = isset( $settings['radius_lat'] ) ? trim( $settings['radius_lat'] ) : '';
		$radius_lng = isset( $settings['radius_lng'] ) ? trim( $settings['radius_lng'] ) : '';
		$radius_mode = isset( $settings['radius_mode'] ) ? trim( $settings['radius_mode'] ) : 'include';


		if ( empty( $radius_km ) || empty( $radius_lat ) || empty( $radius_lng ) ) {
			return;
		}

		echo '<div class="geot-ajax geot-filter" data-geo_mode="'. $radius_mode .'" data-action="radius_filter" data-filter="' . $radius_km . '" data-region="' . $radius_lat . '" data-ex_filter="' . $radius_lng . '">';
	}


	/**
	 *
	 * To Ajax mode, print HTML after
	 *
	 * @param Array $settings
	 *
	 */
	static function ajax_after_render( $settings ) {

		$radius_km	= isset( $settings['radius_km'] ) ? trim( $settings['radius_km'] ) : '';
		$radius_lat = isset( $settings['radius_lat'] ) ? trim( $settings['radius_lat'] ) : '';
		$radius_lng = isset( $settings['radius_lng'] ) ? trim( $settings['radius_lng'] ) : '';


		if ( empty( $radius_km ) || empty( $radius_lat ) || empty( $radius_lng ) ) {
			return;
		}

		echo '</div>';
	}

}

?>