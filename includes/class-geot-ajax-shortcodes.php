<?php

use function GeotCore\is_builder;

/**
 * Shortcodes  functions for AJAX mode
 *
 * @link       https://geotargetingwp.com/geotargeting-pro
 * @since      1.0.0
 *
 * @package    GeoTarget
 * @subpackage GeoTarget/includes
 * @author     Your Name <email@example.com>
 */
class GeotWP_Ajax_Shortcodes {

	/**
	 * @since   1.6
	 * @access  private
	 * @var     Array of plugin settings
	 */
	private $geot_opts;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @var      string $GeoTarget The name of this plugin.
	 * @var      string $version The version of this plugin.
	 */
	public function __construct() {

		$this->geot_opts = geotwp_settings();
		$this->opts = geot_settings();

		add_action( 'init', [ $this, 'register_shortcodes' ] );
	}

	/**
	 * Register shortcodes
	 * @since 1.6
	 */
	public function register_shortcodes() {

		if ( ! isset( $this->opts['ajax_mode'] ) || $this->opts['ajax_mode'] != '1' || is_builder() ) {
			return;
		}

		add_shortcode( 'geot', [ $this, 'geot_filter' ] );
		add_shortcode( 'geot_city', [ $this, 'geot_filter_cities' ] );
		add_shortcode( 'geot_state', [ $this, 'geot_filter_states' ] );

		add_shortcode( 'geot_filter', [ $this, 'geot_filter' ] );
		add_shortcode( 'geot_filter_city', [ $this, 'geot_filter_cities' ] );
		add_shortcode( 'geot_filter_state', [ $this, 'geot_filter_states' ] );
		add_shortcode( 'geot_filter_zip', [ $this, 'geot_filter_zips' ] );
		add_shortcode( 'geot_filter_radius', [ $this, 'geot_filter_radius' ] );

		add_shortcode( 'geot_country_code', [ $this, 'geot_show_country_code' ] );
		add_shortcode( 'geot_country_name', [ $this, 'geot_show_country_name' ] );
		add_shortcode( 'geot_city_name', [ $this, 'geot_show_city_name' ] );
		add_shortcode( 'geot_state_name', [ $this, 'geot_show_state_name' ] );
		add_shortcode( 'geot_state_code', [ $this, 'geot_show_state_code' ] );
		add_shortcode( 'geot_continent', [ $this, 'geot_show_continent' ] );
		add_shortcode( 'geot_zip', [ $this, 'geot_show_zip_code' ] );
		add_shortcode( 'geot_region', [ $this, 'geot_show_regions' ] );
		add_shortcode( 'geot_debug', [ $this, 'geot_debug_data' ] );
		add_shortcode( 'geot_time_zone', [ $this, 'geot_show_time_zone' ] );
		add_shortcode( 'geot_lat', [ $this, 'geot_show_lat' ] );
		add_shortcode( 'geot_lng', [ $this, 'geot_show_lng' ] );
		add_shortcode( 'geot_dropdown',  'geot_dropdown' );

		add_shortcode( 'geot_placeholder', [ $this, 'geot_placeholder' ] );
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
	function geot_filter( $atts, $content ) {
		extract( shortcode_atts( [
			'country'         => '',
			'region'          => '',
			'exclude_country' => '',
			'exclude_region'  => '',
			'html_tag'        => 'div',
		], $atts ) );

		return '<' . $html_tag . ' class="geot-ajax geot-filter" data-action="country_filter" data-filter="' . $country . '" data-region="' . $region . '" data-ex_filter="' . $exclude_country . '" data-ex_region="' . $exclude_region . '">' . do_shortcode( $content ) . '</' . $html_tag . '>';

	}

	/**
	 * Shows provided content only if the location
	 * criteria are met.
	 * [geot_city city="Miami, New York"]content[/geot_city]
	 * [geot_city region="my_city_region"]content[/geot_city]
	 *
	 * @param $atts
	 * @param $content
	 *
	 * @return string
	 */
	function geot_filter_cities( $atts, $content ) {
		extract( shortcode_atts( [
			'city'           => '',
			'region'         => '',
			'exclude_city'   => '',
			'exclude_region' => '',
			'html_tag'       => 'div',
		], $atts ) );

		return '<' . $html_tag . ' class="geot-ajax geot-filter" data-action="city_filter" data-filter="' . $city . '" data-region="' . $region . '" data-ex_filter="' . $exclude_city . '" data-ex_region="' . $exclude_region . '">' . do_shortcode( $content ) . '</' . $html_tag . '>';

	}

	/**
	 * Shows provided content only if the location
	 * criteria are met.
	 * [geot_state state="Florida"]content[/geot_state]
	 *
	 * @param $atts
	 * @param $content
	 *
	 * @return string
	 */
	function geot_filter_states( $atts, $content ) {
		extract( shortcode_atts( [
			'state'         => '',
			'region'         => '',
			'exclude_state' => '',
			'exclude_region' => '',
			'html_tag'      => 'div',
		], $atts ) );

		return '<' . $html_tag . ' class="geot-ajax geot-filter" data-action="state_filter" data-filter="' . $state . '" data-region="' . $region . '"  data-ex_filter="' . $exclude_state . '" data-ex_region="' . $exclude_region . '" >' . do_shortcode( $content ) . '</' . $html_tag . '>';

	}

	/**
	 * Shows provided content only if the location
	 * criteria are met.
	 * [geot_filter_zip zip="1212"]content[/geot_zip]
	 *
	 * @param $atts
	 * @param $content
	 *
	 * @return string
	 */
	function geot_filter_zips( $atts, $content ) {
		extract( shortcode_atts( [
			'zip'				=> '',
			'region'			=> '',
			'exclude_zip'		=> '',
			'exclude_region'	=> '',
			'html_tag'			=> 'div',
		], $atts ) );

		return '<' . $html_tag . ' class="geot-ajax geot-filter" data-action="zip_filter" data-filter="' . $zip . '" data-region="' . $region . '" data-ex_filter="' . $exclude_zip . '" data-ex_region="' . $exclude_region . '">' . do_shortcode( $content ) . '</' . $html_tag . '>';

	}
	/**
	 * Shows provided content only if the location
	 * criteria are met.
	 * [geot_filter_radius radius_km="100" lat="" lng="" geo_mode="include"]content[/geot_zip]
	 *
	 * @param $atts
	 * @param $content
	 *
	 * @return string
	 */
	function geot_filter_radius( $atts, $content ) {
		extract( shortcode_atts( [
			'radius_km'			=> '100',
			'lat'			    => '',
			'lng'		        => '',
			'html_tag'			=> 'div',
			'geo_mode'          => 'show'
		], $atts ) );

		return '<' . $html_tag . ' class="geot-ajax geot-filter" data-geo_mode="'. $geo_mode .'" data-action="radius_filter" data-filter="' . $radius_km . '" data-region="' . $lat . '" data-ex_filter="' . $lng . '" >' . do_shortcode( $content ) . '</' . $html_tag . '>';

	}


	/**
	 * Displays the 2 character country for the current user
	 * [geot_country_code]   [geot_country_code]
	 * @return  string country CODE
	 **/
	function geot_show_country_code( $atts ) {
		extract( shortcode_atts( [
			'default'  => '',
			'html_tag' => 'span',
		], $atts ) );

		return '<' . $html_tag . ' class="geot-ajax" data-action="country_code" data-default="' . do_shortcode( $default ) . '"></' . $html_tag . '>';
	}


	/**
	 * Displays the country name for the current user
	 * [geot_country_name]
	 * @return  string country name
	 **/
	function geot_show_country_name( $atts ) {
		extract( shortcode_atts( [
			'default'  => '',
			'locale'   => 'en',
			'html_tag' => 'span',
		], $atts ) );

		return '<' . $html_tag . ' class="geot-ajax" data-locale="' . $locale . '" data-action="country_name" data-default="' . do_shortcode( $default ) . '"></' . $html_tag . '>';

	}

	/**
	 * Display the city name of current user
	 * [geot_city_name]
	 * @return string city name
	 */
	function geot_show_city_name( $atts ) {
		extract( shortcode_atts( [
			'default'  => '',
			'locale'   => 'en',
			'html_tag' => 'span',
		], $atts ) );

		return '<' . $html_tag . ' class="geot-ajax" data-locale="' . $locale . '" data-action="city_name" data-default="' . do_shortcode( $default ) . '"></' . $html_tag . '>';

	}

	/**
	 * Display the State name of current user
	 * [geot_state]
	 * @return string city name
	 */
	function geot_show_state_name( $atts ) {
		extract( shortcode_atts( [
			'default'  => '',
			'locale'   => 'en',
			'html_tag' => 'span',
		], $atts ) );

		return '<' . $html_tag . ' class="geot-ajax" data-locale="' . $locale . '" data-action="state_name" data-default="' . do_shortcode( $default ) . '"></' . $html_tag . '>';
	}

	/**
	 * Display the State code of current user
	 * [geot_state_code]
	 * @return string city name
	 */
	function geot_show_state_code( $atts ) {
		extract( shortcode_atts( [
			'default'  => '',
			'html_tag' => 'span',
		], $atts ) );

		return '<' . $html_tag . ' class="geot-ajax" data-action="state_code" data-default="' . do_shortcode( $default ) . '"></' . $html_tag . '>';
	}


	/**
	 * Display the Continent of current user
	 * [geot_continent]
	 * @return string continent name
	 */
	function geot_show_continent( $atts ) {
		extract( shortcode_atts( [
			'default'  => '',
			'locale'   => 'en',
			'html_tag' => 'span',
		], $atts ) );

		return '<' . $html_tag . ' class="geot-ajax" data-locale="' . $locale . '" data-action="continent_name" data-default="' . do_shortcode( $default ) . '"></' . $html_tag . '>';
	}

	/**
	 * Display the Zip code of current user
	 * [geot_zip]
	 * @return string city name
	 */
	function geot_show_zip_code( $atts ) {
		extract( shortcode_atts( [
			'default'  => '',
			'html_tag' => 'span',
		], $atts ) );

		return '<' . $html_tag . ' class="geot-ajax" data-action="zip" data-default="' . do_shortcode( $default ) . '"></' . $html_tag . '>';
	}

	/**
	 * Display the Regions of current user
	 * [geot_region]
	 * @return string
	 */
	function geot_show_regions( $atts ) {
		extract( shortcode_atts( [
			'default'  => '',
			'html_tag' => 'span',
		], $atts ) );

		return '<' . $html_tag . ' class="geot-ajax" data-action="region" data-default="' . do_shortcode( $default ) . '"></' . $html_tag . '>';
	}

	/**
	 * Display the Timezone of current user
	 * [geot_time_zone]
	 * @return string
	 */
	function geot_show_time_zone( $atts ) {
		extract( shortcode_atts( [
			'default'  => '',
			'html_tag' => 'span',
		], $atts ) );

		return '<' . $html_tag . ' class="geot-ajax" data-action="time_zone" data-default="' . do_shortcode( $default ) . '"></' . $html_tag . '>';
	}

	/**
	 * Display the latitude of current user
	 * [geot_lat]
	 * @return string
	 */
	function geot_show_lat( $atts ) {
		extract( shortcode_atts( [
			'default'  => '',
			'html_tag' => 'span',
		], $atts ) );

		return '<' . $html_tag . ' class="geot-ajax" data-action="latitude" data-default="' . do_shortcode( $default ) . '"></' . $html_tag . '>';
	}

	/**
	 * Display the longitude of current user
	 * [geot_lng]
	 * @return string
	 */
	function geot_show_lng( $atts ) {
		extract( shortcode_atts( [
			'default'  => '',
			'html_tag' => 'span',
		], $atts ) );

		return '<' . $html_tag . ' class="geot-ajax" data-action="longitude" data-default="' . do_shortcode( $default ) . '"></' . $html_tag . '>';
	}

	/**
	 * Display placeholder when the ajax is executing
	 * [geot_placeholder]
	 * @return string
	 */
	function geot_placeholder( $atts = [], $content = '' ) {

		return '<div class="geot-ajax geot-placeholder" style="display:none;">' . do_shortcode( $content ) . '</div>';
	}


	function geot_debug_data() {
		return '<div class="geot-ajax geot-debug-data"></div>';
	}

}	
