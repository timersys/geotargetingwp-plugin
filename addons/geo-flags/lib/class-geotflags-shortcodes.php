<?php

/**
 * Shortcodes  functions
 *
 * @link       http://wp.timersys.com/geotargeting/
 * @since      1.0.0
 *
 * @package    GeoTarget
 * @subpackage GeoTarget/includes
 * @author     Your Name <email@example.com>
 */
class GeotWP_Flags_Shortcodes {

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
	 * @var      class    instance of GeotCore
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

		if ( isset( $this->geot_opts['ajax_mode'] ) && $this->geot_opts['ajax_mode'] == '1' ) {
			return;
		}
		// leave for backward compatibility
		add_shortcode( 'geo-flag', [ $this, 'geo_flag' ] );

	}

	/**
	 * Shows provided content only if the location
	 * criteria are met.
	 * [geo-flag country_code="" squared="false" size="30px" html_tag="span"]
	 *
	 * @param $atts
	 * @param $content
	 *
	 * @return string
	 */
	function geo_flag( $atts, $content ) {
		extract( shortcode_atts( [
			'country_code' => geot_country_code(),
			'squared'      => false,
			'size'         => "30px",
			'html_tag'     => "span",
		], $atts ) );
		$squared = $squared && $squared !== 'false' ? 'flag-icon-squared' : '';

		return '<' . $html_tag . ' style="font-size:' . esc_attr( $size ) . '" class="flag-icon flag-icon-' . strtolower( $country_code ) . ' ' . $squared . '"></' . $html_tag . '>';
	}


}
