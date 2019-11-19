<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Shortcode attributes
 * @var $zip
 * @var $exclude_zip
 * @var $this WPBakeryShortCode_VC_Geot
 */
$zip  = $exclude_zip = $region = $exclude_region = '';
$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
extract( $atts );

$opts = geot_settings();

if ( isset( $opts['ajax_mode'] ) && $opts['ajax_mode'] == '1' ) {
	echo '<div class="geot-ajax geot-filter" data-action="zip_filter" data-filter="' . $zip . '" data-region="' . $region . '" data-ex_filter="' . $exclude_zip . '" data-ex_region="' . $exclude_region . '">' . wpb_js_remove_wpautop( $content ) . '</div>';
} else {
	if ( geot_target_zip( $zip, $region, $exclude_zip, $exclude_region ) ) {
		echo wpb_js_remove_wpautop( $content );
	}
}