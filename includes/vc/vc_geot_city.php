<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Shortcode attributes
 * @var $city
 * @var $exclude_city
 * @var $region
 * @var $exclude_region
 * @var $this WPBakeryShortCode_VC_Geot
 */
$atts = vc_map_get_attributes( $this->getShortcode(), $atts );

$city = isset( $atts['city'] ) ? trim( $atts['city'] ) : '';
$exclude_city = isset( $atts['exclude_city'] ) ? trim( $atts['exclude_city'] ) : '';

$region = isset( $atts['region'] ) ? trim( $atts['region'] ) : '';
$exclude_region = isset( $atts['exclude_region'] ) ? trim( $atts['exclude_region'] ) : '';

$opts = geot_settings();

if( \GeotCore\is_builder() ) {
	echo wpb_js_remove_wpautop( $content );
} else {
	if ( isset( $opts['ajax_mode'] ) && $opts['ajax_mode'] == '1' ) {
		echo '<div class="geot-ajax geot-filter" data-action="city_filter" data-filter="' . $city . '" data-region="' . $region . '" data-ex_filter="' . $exclude_city . '" data-ex_region="' . $exclude_region . '">' . wpb_js_remove_wpautop( $content ) . '</div>';
	} else {
		if ( geot_target_city( $city, $region, $exclude_city, $exclude_region ) ) {
			echo wpb_js_remove_wpautop( $content );
		}
	}
}