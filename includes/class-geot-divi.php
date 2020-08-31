<?php

/**
 * Divi Extension
 *
 * @link       https://geotargetingwp.com/geotargeting-pro
 * @since      1.6.3
 *
 * @package    GeoTarget
 * @subpackage GeoTarget/includes
 * @author     Damian Logghe
 */
class GeotWP_Divi {

	public function __construct() {
		add_filter( 'et_pagebuilder_module_init', [ $this, 'module_init' ], 10, 1 );
		add_filter( 'et_builder_main_tabs', [ $this, 'add_tabs' ], 10, 1 );
		add_filter( 'et_module_shortcode_output', [ $this, 'render' ], 10, 3 );

		foreach ( $this->get_modules() as $module_slug ) {
			add_filter( 'et_pb_all_fields_unprocessed_' . $module_slug, [ $this, 'get_fields' ], 10, 1 );
		}
	}

	/**
	 * Get Modules
	 * @return array
	 */
	public function get_modules() {

		$array_modules = [
			'et_pb_section',
			'et_pb_row',
			'et_pb_row_inner',
			'et_pb_column',
			'et_pb_accordion',
			'et_pb_audio',
			'et_pb_counters',
			'et_pb_blog',
			'et_pb_blurb',
			'et_pb_button',
			'et_pb_circle_counter',
			'et_pb_code',
			'et_pb_comments',
			'et_pb_contact_form',
			'et_pb_countdown_timer',
			'et_pb_cta',
			'et_pb_divider',
			'et_pb_filterable_portfolio',
			'et_pb_fullwidth_code',
			'et_pb_fullwidth_header',
			'et_pb_fullwidth_image',
			'et_pb_fullwidth_map',
			'et_pb_fullwidth_menu',
			'et_pb_fullwidth_portfolio',
			'et_pb_fullwidth_post_slider',
			'et_pb_fullwidth_post_title',
			'et_pb_fullwidth_slider',
			'et_pb_gallery',
			'et_pb_image',
			'et_pb_login',
			'et_pb_map',
			'et_pb_number_counter',
			'et_pb_portfolio',
			'et_pb_post_slider',
			'et_pb_post_title',
			'et_pb_post_nav',
			'et_pb_pricing_tables',
			'et_pb_search',
			'et_pb_shop',
			'et_pb_sidebar',
			'et_pb_signup',
			'et_pb_slider',
			'et_pb_social_media_follow',
			'et_pb_tabs',
			'et_pb_team_member',
			'et_pb_testimonial',
			'et_pb_text',
			'et_pb_toggle',
			'et_pb_video',
			'et_pb_video_slider',
			'et_pb_accordion_item',
			'et_pb_counter',
			'et_pb_contact_field',
			'et_pb_map_pin',
			'et_pb_pricing_table',
			'et_pb_signup_custom_field',
			'et_pb_slide',
			'et_pb_social_media_follow_network',
			'et_pb_tab',
		];

		return apply_filters( 'geot/divi/get_modules', $array_modules );
	}

	/**
	 * Format regions and normalize
	 *
	 * @param $check_multi
	 * @param string $separator
	 * @param $regions
	 *
	 * @return array
	 */
	static function format_regions( $check_multi, $separator = '|', $regions ) {

		if ( empty( $check_multi ) || empty( $regions ) || strpos( $check_multi, $separator ) === false ) {
			return [];
		}

		$output_regions = [];

		foreach ( explode( $separator, $check_multi ) as $key => $onoff ) {
			if ( strtolower( $onoff ) == 'on' && isset( $regions[ $key ] ) ) {
				$output_regions[] = $regions[ $key ];
			}
		}

		return $output_regions;
	}

	/**
	 * Module Init
	 *
	 * @return array
	 */
	public function module_init() {
		require_once GEOWP_PLUGIN_DIR . 'includes/divi/divi-geot-country.php';
		require_once GEOWP_PLUGIN_DIR . 'includes/divi/divi-geot-city.php';
		require_once GEOWP_PLUGIN_DIR . 'includes/divi/divi-geot-state.php';
		require_once GEOWP_PLUGIN_DIR . 'includes/divi/divi-geot-zipcode.php';
		require_once GEOWP_PLUGIN_DIR . 'includes/divi/divi-geot-radius.php';
	}


	/**
	 * Register Tabs
	 * @return array
	 * @var
	 */
	public function add_tabs( $tabs ) {

		$new_tab         = [];
		$new_tab['geot'] = esc_html__( 'Geotargeting', 'geot' );

		return apply_filters( 'geot/divi/add_tabs', array_merge( $tabs, $new_tab ) );
	}

	/**
	 * Add the actual fields
	 *
	 * @param $fields_unprocessed
	 *
	 * @return array
	 */
	public function get_fields( $fields_unprocessed ) {

		$fields_geot = [];

		$fields_country		= Divi_GeoCountry::get_fields();
		$fields_city		= Divi_GeoCity::get_fields();
		$fields_states		= Divi_GeoState::get_fields();
		$fields_zipcodes	= Divi_GeoZipcode::get_fields();
		$fields_radius		= Divi_GeoRadius::get_fields();

		$fields_geot = array_merge(
			$fields_unprocessed,
			$fields_country,
			$fields_city,
			$fields_states,
			$fields_zipcodes,
			$fields_radius
		);

		return apply_filters( 'geot/divi/get_fields', $fields_geot );
	}


	/**
	 * @param $output
	 * @param $render_slug
	 * @param $module
	 *
	 * @return string
	 */
	public function render( $output, $render_slug, $module ) {

		global $et_fb_processing_shortcode_object;

		// if is builder / edit mode
		if ( $et_fb_processing_shortcode_object == 1 || !$this->has_geot_opts( $module->props ) ) {
			return $output;
		}

		$opts 			= geot_settings();
		$reg_countries 	= array_values( self::get_regions( 'country' ) );
		$reg_cities 	= array_values( self::get_regions( 'city' ) );
		$reg_states		= array_values( self::get_regions( 'state' ) );
		$reg_zips 		= array_values( self::get_regions( 'zip' ) );


		if ( isset( $opts['ajax_mode'] ) && $opts['ajax_mode'] == '1' ) {

			$output = Divi_GeoRadius::ajax_render( $module->props, $output );
			$output = Divi_GeoZipcode::ajax_render( $module->props, $reg_zips, $output );
			$output = Divi_GeoState::ajax_render( $module->props, $reg_states, $output );
			$output = Divi_GeoCity::ajax_render( $module->props, $reg_cities, $output );
			$output = Divi_GeoCountry::ajax_render( $module->props, $reg_countries, $output );

		} else {

			if ( ! Divi_GeoCountry::is_render( $module->props, $reg_countries ) ||
			     ! Divi_GeoCity::is_render( $module->props, $reg_cities ) ||
			     ! Divi_GeoState::is_render( $module->props, $reg_states ) ||
			     ! Divi_GeoZipcode::is_render( $module->props, $reg_zips ) ||
			     ! Divi_GeoRadius::is_render( $module->props )
			) {
				return '';
			}
		}

		return $output;
	}

	/**
	 * Check if values are set
	 *
	 * @param $props
	 *
	 * @return bool
	 */
	private function has_geot_opts( $props ) {
		$keys = [
			'in_countries',
			'in_region_countries',
			'ex_countries',
			'ex_region_countries',
			'in_states',
			'in_region_states',
			'ex_states',
			'ex_region_states',
			'in_cities',
			'in_region_cities',
			'ex_cities',
			'ex_region_cities',
			'in_zipcodes',
			'in_region_zips',
			'ex_zipcodes',
			'ex_region_zips',
			'radius_km',
			'radius_lat',
			'radius_lng',
		];

		// check if any of the valid key has a value
		foreach ( $keys as $key ) {
			if ( ! empty( $props[ $key ] ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Get Regions
	 *
	 * @param string $slug_region
	 *
	 * @return array
	 */
	static function get_regions( $slug_region = 'country' ) {

		$dropdown_values = [];

		switch ( $slug_region ) {
			case 'city':
				$regions = geot_city_regions();
				break;
			case 'state':
				$regions = geot_state_regions();
				break;
			case 'zip':
				$regions = geot_zip_regions();
				break;
			default:
				$regions = geot_country_regions();
		}

		if ( ! empty( $regions ) ) {
			foreach ( $regions as $r ) {
				if ( isset( $r['name'] ) ) {
					$dropdown_values[ $r['name'] ] = $r['name'];
				}
			}
		}

		return $dropdown_values;
	}

}