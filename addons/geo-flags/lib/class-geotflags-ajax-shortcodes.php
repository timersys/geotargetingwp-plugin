<?php

/**
 * Shortcodes  functions for AJAX mode
 *
 * @link       http://wp.timersys.com/geotargeting/
 * @since      1.0.0
 *
 * @package    GeoTarget
 * @subpackage GeoTarget/includes
 * @author     Your Name <email@example.com>
 */
class GeotWP_Flags_Ajax_Shortcodes {

	/**
	 * @since   1.6
	 * @access  private
	 * @var     Array of plugin settings
	 */
	private $opts;
	private $geot_opts;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->geot_opts = geot_settings();

		add_action( 'init', [ $this, 'register_shortcodes' ] );
	}

	/**
	 * Register shortcodes
	 * @since 1.6
	 */
	public function register_shortcodes() {

		if ( ! isset( $this->geot_opts['ajax_mode'] ) || $this->geot_opts['ajax_mode'] != '1' ) {
			return;
		}

		add_shortcode( 'geo-flag', [ $this, 'geo_flag' ] );
	}

	/**
	 * Shows provided content only if the location
	 * criteria are met.
	 * [geot country="US,CA"]content[/geot]
	 * [geot region="my_region"]content[/geot]
	 *
	 * @param $atts
	 * @param $content
	 *
	 * @return string
	 */
	function geo_flag( $atts, $content ) {
		extract( shortcode_atts( [
			'country_code' => '',
			'squared'      => false,
			'size'         => "30px",
			'html_tag'     => "span",
		], $atts ) );
		$squared = $squared && $squared !== 'false' ? 'flag-icon-squared' : '';

		return '<span class="geot-ajax" data-exclude_region="' . $html_tag . '" data-region="' . esc_attr( $size ) . '" data-action="geo_flag" data-filter="' . $country_code . '" data-default="' . $squared . '"></span>';
	}
}	
