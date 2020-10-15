<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Shortcode attributes
 * @var $country
 * @var $exclude_country
 * @var $region
 * @var $exclude_region
 * @var $this WPBakeryShortCode_VC_Geot
 */
$atts    = vc_map_get_attributes( $this->getShortcode(), $atts );

$country = isset( $atts['country'] ) ? trim( $atts['country'] ) : '';
$exclude_country = isset( $atts['exclude_country'] ) ? trim( $atts['exclude_country'] ) : '';

$region = isset( $atts['region'] ) ? trim( $atts['region'] ) : '';
$exclude_region = isset( $atts['exclude_region'] ) ? trim( $atts['exclude_region'] ) : '';


$opts = geot_settings();

if( \GeotCore\is_builder() ) {
	echo wpb_js_remove_wpautop( $content );
} else {

	if ( isset( $opts['ajax_mode'] ) && $opts['ajax_mode'] == '1' ) {
		echo '<div class="geot-ajax geot-filter" data-action="country_filter" data-filter="' . $country . '" data-region="' . $region . '" data-ex_filter="' . $exclude_country . '" data-ex_region="' . $exclude_region . '">' . wpb_js_remove_wpautop( $content ) . '</div>';
	} else {
		if ( geot_target( $country, $region, $exclude_country, $exclude_region ) ) {
			echo wpb_js_remove_wpautop( $content );
		}
	}
}