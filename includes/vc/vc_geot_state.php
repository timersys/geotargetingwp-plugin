<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Shortcode attributes
 * @var $state
 * @var $exclude_state
 * @var $this WPBakeryShortCode_VC_Geot
 */
$atts  = vc_map_get_attributes( $this->getShortcode(), $atts );

$state = isset( $atts['state'] ) ? trim( $atts['state'] ) : '';
$exclude_state = isset( $atts['exclude_state'] ) ? trim( $atts['exclude_state'] ) : '';

$region = isset( $atts['region'] ) ? trim( $atts['region'] ) : '';
$exclude_region = isset( $atts['exclude_region'] ) ? trim( $atts['exclude_region'] ) : '';


$opts = geot_settings();

if( \GeotCore\is_builder() ) {
	echo wpb_js_remove_wpautop( $content );
} else {
	if ( isset( $opts['ajax_mode'] ) && $opts['ajax_mode'] == '1' ) {
		echo '<div class="geot-ajax geot-filter" data-action="state_filter" data-filter="' . $state . '" data-region="' . $region . '" data-ex_filter="' . $exclude_state . '" data-ex_region="' . $exclude_region . '">' . wpb_js_remove_wpautop( $content ) . '</div>';
	} else {
		if ( geot_target_state( $state, $region, $exclude_state, $exclude_region ) ) {
			echo wpb_js_remove_wpautop( $content );
		}
	}
}