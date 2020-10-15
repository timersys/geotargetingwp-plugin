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
$atts = vc_map_get_attributes( $this->getShortcode(), $atts );

$zip = isset( $atts['zip'] ) ? trim( $atts['zip'] ) : '';
$exclude_zip = isset( $atts['exclude_zip'] ) ? trim( $atts['exclude_zip'] ) : '';

$region = isset( $atts['region'] ) ? trim( $atts['region'] ) : '';
$exclude_region = isset( $atts['exclude_region'] ) ? trim( $atts['exclude_region'] ) : '';


$opts = geot_settings();
if( \GeotCore\is_builder() ) {
	echo wpb_js_remove_wpautop( $content );
} else {
	if ( isset( $opts['ajax_mode'] ) && $opts['ajax_mode'] == '1' ) {
		echo '<div class="geot-ajax geot-filter" data-action="zip_filter" data-filter="' . $zip . '" data-region="' . $region . '" data-ex_filter="' . $exclude_zip . '" data-ex_region="' . $exclude_region . '">' . wpb_js_remove_wpautop( $content ) . '</div>';
	} else {
		if ( geot_target_zip( $zip, $region, $exclude_zip, $exclude_region ) ) {
			echo wpb_js_remove_wpautop( $content );
		}
	}
}