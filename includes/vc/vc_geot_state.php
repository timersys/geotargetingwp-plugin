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
$state = $exclude_state = $region = $exclude_region = '';
$atts  = vc_map_get_attributes( $this->getShortcode(), $atts );
extract( $atts );

$opts = geot_settings();

if ( isset( $opts['ajax_mode'] ) && $opts['ajax_mode'] == '1' ) {
	echo '<div class="geot-ajax geot-filter" data-action="state_filter" data-filter="' . $state . '" data-region="' . $region . '" data-ex_filter="' . $exclude_state . '" data-ex_region="' . $exclude_region . '">' . wpb_js_remove_wpautop( $content ) . '</div>';
} else {
	if ( geot_target_state( $state, $region, $exclude_state, $exclude_region ) ) {
		echo wpb_js_remove_wpautop( $content );
	}
}