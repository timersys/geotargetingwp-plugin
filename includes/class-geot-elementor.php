<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * Elementor Extension
 *
 * @link       https://geotargetingwp.com/geotargeting-pro
 * @since      1.6.3
 *
 * @package    GeoTarget
 * @subpackage GeoTarget/includes
 * @author     Damian Logghe
 */
class GeotWP_Elementor {

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @var      string $GeoTarget The name of this plugin.
	 * @var      string $version The version of this plugin.
	 */
	public function __construct() {

		add_action( 'plugins_loaded', [ $this, 'register_init' ] );
		add_action( 'elementor/editor/before_enqueue_styles', [ $this, 'enqueue_styles' ] );
		add_action( 'elementor/element/after_section_end', [ $this, 'register_controls' ], 10, 3 );
		add_action( 'elementor/frontend/section/should_render', [ $this, 'is_render' ], 10, 2 );
		add_action( 'elementor/frontend/widget/should_render', [ $this, 'is_render' ], 10, 2 );
		add_action( 'elementor/frontend/column/should_render', [ $this, 'is_render' ], 10, 2 );
		add_action( 'elementor/frontend/before_render', [ $this, 'ajax_before_render' ], 10, 1 );
		add_action( 'elementor/frontend/after_render', [ $this, 'ajax_after_render' ], 10, 1 );

		// Conditions ( element pro )
		add_action( 'elementor/theme/register_conditions', [ $this, 'condition_register' ], 10, 1 );
		add_filter( 'elementor/query/get_autocomplete/geot', [ $this, 'condition_autocomplete' ], 10, 2 );
		add_filter( 'elementor/query/get_value_titles/geot', [ $this, 'condition_value' ], 10, 2 );
	}

	/**
	 *
	 * Get Regions
	 *
	 * @param string $slug_region
	 *
	 */
	static function get_regions( $slug_region = 'countries' ) {

		$dropdown_values = [];

		switch ( $slug_region ) {
			case 'cities':
				$regions = geot_city_regions();
				break;
			case 'states':
				$regions = geot_state_regions();
				break;
			case 'zips':
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

	/**
	 * Put css in admin
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( 'geo-elementor', GEOWP_PLUGIN_URL . 'admin/css/admin.css', [], GEOWP_VERSION );
	}

	/**
	 * Register Tab
	 * @since    1.0.0
	 */
	public function register_init() {
		// Check if Elementor installed and activated
		if ( ! did_action( 'elementor/loaded' ) ) {
			return;
		}

		//Register Tab
		\Elementor\Controls_Manager::add_tab(
			'geot',
			__( 'Geotargeting', 'geot' )
		);

		global $geot_widgets;
		$GLOBALS['geot_widgets'] = [];

		require_once GEOWP_PLUGIN_DIR . 'includes/elementor/elementor-geot-country.php';
		require_once GEOWP_PLUGIN_DIR . 'includes/elementor/elementor-geot-city.php';
		require_once GEOWP_PLUGIN_DIR . 'includes/elementor/elementor-geot-state.php';
		require_once GEOWP_PLUGIN_DIR . 'includes/elementor/elementor-geot-zipcode.php';
		require_once GEOWP_PLUGIN_DIR . 'includes/elementor/elementor-geot-radius.php';

		do_action( 'geot/elementor/register_init' );
	}

	/**
	 * All Controls
	 *
	 * @param class $control
	 * @param string $section_id
	 * @param array $args
	 */
	public function register_controls( $control, $section_id, $args ) {
		global $geot_register, $geot_widgets;

		if ( ! in_array( $control->get_name(), $GLOBALS['geot_widgets'] ) ) {
			$GLOBALS['geot_register'] = true;
		}


		if ( $GLOBALS['geot_register'] ) {

			$GLOBALS['geot_register']  = false;
			$GLOBALS['geot_widgets'][] = $control->get_name();

			Elementor_GeoCountry::get_fields( $control );
			Elementor_GeoCity::get_fields( $control );
			Elementor_GeoState::get_fields( $control );
			Elementor_GeoZipcode::get_fields( $control );
			Elementor_GeoRadius::get_fields( $control );
			do_action('geot/elementor/register_controls', $control);
		}
	}


	/**
	 * Is Render in the front
	 *
	 * @param String $should_render
	 * @param Class $element
	 */
	public function is_render( $should_render, $element ) {

		$opts 		= geot_settings();
		$settings 	= $element->get_settings_for_display();

		if ( ! isset( $opts['ajax_mode'] ) || $opts['ajax_mode'] != '1' ) {

			if ( ! Elementor_GeoCountry::is_render( $settings ) ||
			     ! Elementor_GeoCity::is_render( $settings ) ||
			     ! Elementor_GeoState::is_render( $settings ) ||
			     ! Elementor_GeoZipcode::is_render( $settings ) ||
			     ! Elementor_GeoRadius::is_render( $settings )
			) {
				return false;
			}
		}

		return $should_render;
	}


	/**
	 *
	 * To Ajax mode, print HTML before
	 *
	 * @param Class $element
	 *
	 */
	public function ajax_before_render( $element ) {

		$opts 		= geot_settings();
		$settings 	= $element->get_active_settings();

		if ( isset( $opts['ajax_mode'] ) && $opts['ajax_mode'] == '1' ) {

			Elementor_GeoCountry::ajax_before_render( $settings );
			Elementor_GeoCity::ajax_before_render( $settings );
			Elementor_GeoState::ajax_before_render( $settings );
			Elementor_GeoZipcode::ajax_before_render( $settings );
			Elementor_GeoRadius::ajax_before_render( $settings );
			do_action('geot/elementor/ajax_before_render', $settings);
		}

	}


	/**
	 *
	 * To Ajax mode, print HTML after
	 *
	 * @param Class $element
	 *
	 */
	public function ajax_after_render( $element ) {

		$opts 		= geot_settings();
		$settings 	= $element->get_active_settings();

		if ( isset( $opts['ajax_mode'] ) && $opts['ajax_mode'] == '1' ) {

			Elementor_GeoCountry::ajax_after_render( $settings );
			Elementor_GeoCity::ajax_after_render( $settings );
			Elementor_GeoState::ajax_after_render( $settings );
			Elementor_GeoZipcode::ajax_after_render( $settings );
			Elementor_GeoRadius::ajax_after_render( $settings );
			do_action('geot/elementor/ajax_after_render', $settings);
		}
	}


	/**
	 * Register conditions
	 * @param  [type] $elementor [description]
	 * @return [type]            [description]
	 */
	public function condition_register( $elementor ) {

		require_once GEOWP_PLUGIN_DIR . 'includes/elementor/conditions/elementor-geot-conditions-base.php';
		require_once GEOWP_PLUGIN_DIR . 'includes/elementor/conditions/elementor-geot-conditions-country.php';
		require_once GEOWP_PLUGIN_DIR . 'includes/elementor/conditions/elementor-geot-conditions-region-country.php';
		require_once GEOWP_PLUGIN_DIR . 'includes/elementor/conditions/elementor-geot-conditions-city.php';
		require_once GEOWP_PLUGIN_DIR . 'includes/elementor/conditions/elementor-geot-conditions-region-city.php';
		require_once GEOWP_PLUGIN_DIR . 'includes/elementor/conditions/elementor-geot-conditions-state.php';
		require_once GEOWP_PLUGIN_DIR . 'includes/elementor/conditions/elementor-geot-conditions-region-state.php';
		require_once GEOWP_PLUGIN_DIR . 'includes/elementor/conditions/elementor-geot-conditions-zip.php';
		require_once GEOWP_PLUGIN_DIR . 'includes/elementor/conditions/elementor-geot-conditions-region-zip.php';
		do_action('geot/elementor/condition_register');
		$elementor->get_condition( 'general' )->register_sub_condition( new Elementor_GeoConditions_Base );
	}

	/**
	 * [condition_autocomplete description]
	 * @param  [type] $results [description]
	 * @param  [type] $data    [description]
	 * @return [type]          [description]
	 */
	public function condition_autocomplete( $results, $data ) {
		
		if( ! isset( $data['autocomplete']['object'] ) )
			return $results;

		if( $data['autocomplete']['object'] == 'geot_country' ) {
			foreach( geot_countries() as $country ) {
				if( ! empty( $data['q'] ) && stripos( $country->country, $data['q'] ) === FALSE )
					continue;

				$results[] = [
					'id'	=> $country->iso_code,
					'text'	=> $country->country,
				];
			}
		} else {

			switch( $data['autocomplete']['object'] ) {

				case 'geot_region_city':
					$regions = geot_city_regions();
					break;
				case 'geot_region_state':
					$regions = geot_state_regions();
					break;
				case 'geot_region_zip':
					$regions = geot_zip_regions();
					break;
				default:
					$regions = geot_country_regions();
			}

			
			foreach ( $regions as $r ) {

				if( ! empty( $data['q'] ) && stripos( $r['name'], $data['q'] ) === FALSE )
					continue;

				$results[] = [
					'id'	=> $r['name'],
					'text'	=> $r['name'],
				];
			}
		}
		
		return $results;
	}


	/**
	 * [condition_value description]
	 * @param  [type] $result  [description]
	 * @param  [type] $request [description]
	 * @return [type]          [description]
	 */
	public function condition_value( $result, $request ) {

		if( ! isset( $request['get_titles']['object'] ) || ! isset( $request['id'] ) )
			return $result;

		if( $request['get_titles']['object'] == 'geot_country' ) {

			foreach( geot_countries() as $country ) {

				if( $country->iso_code == $request['id'] ) {
					$result[ $country->iso_code ] = $country->country;
					break;
				}
			}

		} else {

			switch( $request['get_titles']['object'] ) {

				case 'geot_region_country':
				case 'geot_region_city':
				case 'geot_region_state':
				case 'geot_region_zip':
					$result[ $request['id'] ] = $request['id'];
					break;
			}
		}
		
		return $result;
	}

	/**
	 * Check if regions are the ones stored in admin
	 * @param $regions
	 * @param $slug
	 *
	 * @return array
	 */
	static function filter_regions( $regions, $slug ) {
		$saved_regions = GeotWP_Elementor::get_regions( $slug );
		if( is_array( $regions ) ) {
			foreach ($regions as $i => $r) {
				if( ! in_array( $r, $saved_regions ) ) {
					unset($regions[$i]);
				}
			}
		}
		return $regions;
	}
}