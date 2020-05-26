<?php

/**
 * Shortcodes  functions
 *
 * @link       https://geotargetingwp.com/geotargeting-pro
 * @since      1.0.0
 *
 * @package    GeoTarget
 * @subpackage GeoTarget/includes
 * @author     Your Name <email@example.com>
 */
class GeotWP_Shortcodes {

	/**
	 * @since   1.6
	 * @access  private
	 * @var     Array of plugin settings
	 */
	private $opts;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @var      string $GeoTarget The name of this plugin.
	 * @var      string $version The version of this plugin.
	 * @var      class    instance of GeotCore
	 */
	public function __construct() {

		$this->opts = geot_settings();

		add_action( 'init', [ $this, 'register_shortcodes' ] );
	}

	/**
	 * Register shortcodes
	 * @since 1.6
	 */
	public function register_shortcodes() {

		add_filter( 'geot/shortcodes/country_name', [ $this, 'the_english_country_names' ] );

		if ( isset( $this->opts['ajax_mode'] ) && $this->opts['ajax_mode'] == '1' ) {
			return;
		}
		// leave for backward compatibility
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
		add_shortcode( 'geot_debug', 'geot_debug_data' );
		add_shortcode( 'geot_time_zone', [ $this, 'geot_show_time_zone' ] );
		add_shortcode( 'geot_lat', [ $this, 'geot_show_lat' ] );
		add_shortcode( 'geot_lng', [ $this, 'geot_show_lng' ] );

		add_shortcode( 'geot_dropdown', [ $this, 'geot_dropdown' ] );
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
		], $atts ) );


		if ( geot_target( $country, $region, $exclude_country, $exclude_region ) ) {
			return do_shortcode( $content );
		}

		return '';
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
		], $atts ) );


		if ( geot_target_city( $city, $region, $exclude_city, $exclude_region ) ) {
			return do_shortcode( $content );
		}

		return '';
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
			'exclude_state' => '',
		], $atts ) );


		if ( geot_target_state( $state, $exclude_state ) ) {
			return do_shortcode( $content );
		}

		return '';
	}

	/**
	 * Shows provided content only if the location
	 * criteria are met.
	 * [geot_filter_zip zip="33166"]content[/geot_filter_zip]
	 *
	 * @param $atts
	 * @param $content
	 *
	 * @return string
	 */
	function geot_filter_zips( $atts, $content ) {
		extract( shortcode_atts( [
			'zip'         => '',
			'region'      => '',
			'exclude_zip' => '',
			'exclude_region' => '',
		], $atts ) );


		if ( geot_target_zip( $zip, $region, $exclude_zip, $exclude_region ) ) {
			return do_shortcode( $content );
		}

		return '';
	}

	/**
	 * Shows provided content only if the location
	 * criteria are met.
	 * [geot_filter_radius radius_km="100" lat="" lng=""]content[/geot_filter_radius]
	 *
	 * @param $atts
	 * @param $content
	 *
	 * @return string
	 */
	function geot_filter_radius( $atts, $content ) {
		extract( shortcode_atts( [
			'radius_km'   => '',
			'lat'         => '',
			'lng'         => '',
		], $atts ) );


		if ( geot_target_radius( $lat, $lng, $radius_km ) ) {
			return do_shortcode( $content );
		}

		return '';
	}

	/**
	 * Displays the 2 character country for the current user
	 * [geot_country_code]   [geot_country_code]
	 * @return  string country CODE
	 **/
	function geot_show_country_code( $atts ) {
		extract( shortcode_atts( [
			'default' => '',
		], $atts ) );

		$code = geot_country_code();

		return ! empty( $code ) ? $code : $default;
	}

	/**
	 * Displays the continent name for the current user
	 * [geot_continent] ]
	 * @return  string country CODE
	 **/
	function geot_show_continent( $atts ) {
		extract( shortcode_atts( [
			'default' => '',
			'locale'  => 'en',
		], $atts ) );

		$continent = geot_continent( $locale );

		return ! empty( $continent ) ? $continent : $default;
	}


	/**
	 * Displays the country name for the current user
	 * [geot_country_name]
	 * @return  string country name
	 **/
	function geot_show_country_name( $atts ) {
		extract( shortcode_atts( [
			'default' => '',
			'locale'  => 'en',
		], $atts ) );

		$name = geot_country_name( $locale );

		if ( ! empty( $name ) ) {
			return apply_filters( 'geot/shortcodes/country_name', $name );
		}

		return apply_filters( 'geot/shortcodes/country_name_default', $default );
	}

	/**
	 * Display the city name of current user
	 * [geot_city_name]
	 * @return string city name
	 */
	function geot_show_city_name( $atts ) {
		extract( shortcode_atts( [
			'default' => '',
			'locale'  => 'en',
		], $atts ) );

		$name = geot_city_name( $locale );
		if ( ! empty( $name ) ) {
			return apply_filters( 'geot/shortcodes/city_name', $name );
		}

		return apply_filters( 'geot/shortcodes/city_name_default', $default );

	}

	/**
	 * Display the State name of current user
	 * [geot_state]
	 * @return string city name
	 */
	function geot_show_state_name( $atts ) {
		extract( shortcode_atts( [
			'default' => '',
			'locale'  => 'en',
		], $atts ) );

		$state = geot_state_name( $locale );

		if ( ! empty( $state ) ) {
			return apply_filters( 'geot/shortcodes/state_name', $state );
		}

		return apply_filters( 'geot/shortcodes/state_name_default', $default );
	}

	/**
	 * Display the State code of current user
	 * [geot_state_code]
	 * @return string city name
	 */
	function geot_show_state_code( $atts ) {
		extract( shortcode_atts( [
			'default' => '',
		], $atts ) );

		$code = geot_state_code();

		return ! empty( $code ) ? $code : $default;
	}

	/**
	 * Display the Zip code of current user
	 * [geot_zip]
	 * @return string city name
	 */
	function geot_show_zip_code( $atts ) {
		extract( shortcode_atts( [
			'default' => '',
		], $atts ) );

		$zip = geot_zip();

		return ! empty( $zip ) ? $zip : $default;
	}

	/**
	 * Display the regions of current user
	 * [geot_region]
	 * @return string city name
	 */
	function geot_show_regions( $atts ) {
		extract( shortcode_atts( [
			'default' => '',
		], $atts ) );

		$regions = geot_user_country_region( $default );

		if ( is_array( $regions ) ) {
			return implode( ', ', $regions );
		}

		return $regions;
	}

	/**
	 * Display the Timezone of current user
	 * [geot_time_zone]
	 * @return string
	 */
	function geot_show_time_zone( $atts ) {
		extract( shortcode_atts( [
			'default' => '',
		], $atts ) );

		$timezone = geot_time_zone();

		return ! empty( $timezone ) ? $timezone : $default;
	}

	/**
	 * Display the latitude of current user
	 * [geot_lat]
	 * @return string
	 */
	function geot_show_lat( $atts ) {
		extract( shortcode_atts( [
			'default' => '',
		], $atts ) );

		$lat = geot_lat();

		return ! empty( $lat ) ? $lat : $default;
	}

	/**
	 * Display the longitude of current user
	 * [geot_lng]
	 * @return string
	 */
	function geot_show_lng( $atts ) {
		extract( shortcode_atts( [
			'default' => '',
		], $atts ) );

		$lng = geot_lng();

		return ! empty( $lng ) ? $lng : $default;
	}

	public function the_english_country_names( $country_name = '' ) {

		if ( empty( $country_name ) ) {
			return;
		}

		$countries = [
			'Aland Islands',
			'Bahamas',
			'British Indian Ocean Territory',
			'Cayman Islands',
			'Central African Republic',
			'Christmas Island',
			'Cocos (Keeling) Islands',
			'Cook Islands',
			'Czech Republic',
			'Dominican Republic',
			'Falkland Islands (Malvinas)',
			'Faroe Islands',
			'Holy See',
			'Isle of Man',
			'Maldives',
			'Marshall Islands',
			'Northern Mariana Islands',
			'Philippines',
			'Russian Federation',
			'United Arab Emirates',
			'United Kingdom of Great Britain and Northern Ireland',
			'United States of America',
			'Virgin Islands (British)',
			'Virgin Islands (U.S.)',
		];

		if ( in_array( $country_name, $countries ) ) {
			$country_name = 'the ' . $country_name;
		}

		return $country_name;
	}

	/**
	 * Display Widget with flags
	 * @return string
	 */
	public function geot_dropdown( $atts ) {

		extract( shortcode_atts( [
			'regions' => '',
			'flags'   => 1,
		], $atts ) );

		$region_ids    = [];
		$flags_id      = 1;
		$saved_regions = geot_country_regions();
		$regions       = ! empty( $regions ) ? explode( ',', $regions ) : [];


		if ( ! empty( $flags ) ) {
			switch ( $flags ) {
				case 'yes' :
					$flags_id = 1;
					break;
				case 'no' :
					$flags_id = 2;
					break;
				default:
					$flags_id = 1;
			}
		}

		if ( ! empty( $regions ) && ! empty( $saved_regions ) ) {

			$all_regions = wp_list_pluck( $saved_regions, 'name' );

			foreach ( $regions as $nregion ) {

				if ( is_numeric( $nregion ) ) {
					$region_ids[] = (int) $nregion;
				} else {
					$region_ids[] = (int) array_search( $nregion, $all_regions );
				}
			}
		}

		$instance = [
			'flags'   => $flags_id,
			'regions' => $region_ids,
		];

		$args = [ 'before_widget' => '', 'after_widget' => '' ];


		ob_start();
		the_widget( 'GeotWP_Widget', $instance, $args );
		$output = ob_get_clean();

		return $output;
	}
}
