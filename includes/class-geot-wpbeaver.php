<?php

/**
 * WpBeaver Extension
 *
 * @link       https://geotargetingwp.com/geotargeting-pro
 * @since      1.6.3
 *
 * @package    GeoTarget
 * @subpackage GeoTarget/includes
 * @author     Damian Logghe
 */
class GeotWP_WPBeaver {

	/**
	 * Initializes the class once all plugins have loaded.
	 */
	public function __construct() {
		add_action( 'plugins_loaded', [ $this, 'module_init' ], 1 );

		add_filter( 'fl_builder_register_settings_form', [ $this, 'get_fields' ], 10, 2);
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );

		add_filter( 'fl_builder_template_path', [ $this, 'template_path' ], 10, 3 );
		add_filter( 'fl_builder_row_template_slug', [ $this, 'template_slug' ], 10, 2 );
		add_filter( 'fl_builder_module_template_slug', [ $this, 'template_slug' ], 10, 2 );

		add_filter( 'geot/wpbeaver/template/row', [ $this, 'render' ], 10, 2);
		add_filter( 'geot/wpbeaver/template/module', [ $this, 'render' ], 10, 2);
	}
	
	/**
	 * Setup hooks if the builder is installed and activated.
	 */
	public function module_init() {
		
		if ( ! class_exists( 'FLBuilder' ) )
			return;	
		
		require_once GEOWP_PLUGIN_DIR . 'includes/wpbeaver/wpbeaver-geot-country.php';
		require_once GEOWP_PLUGIN_DIR . 'includes/wpbeaver/wpbeaver-geot-city.php';
		require_once GEOWP_PLUGIN_DIR . 'includes/wpbeaver/wpbeaver-geot-state.php';
		require_once GEOWP_PLUGIN_DIR . 'includes/wpbeaver/wpbeaver-geot-zipcode.php';
		require_once GEOWP_PLUGIN_DIR . 'includes/wpbeaver/wpbeaver-geot-radius.php';
	}


	public function enqueue_scripts() {
		if ( ! class_exists( 'FLBuilder' ) )
			return;	

		if ( ! FLBuilderModel::is_builder_active() )
			return;

		wp_enqueue_style( 'geot-wpbeaver', GEOWP_PLUGIN_URL . 'admin/css/geot-wpbeaver.css' );
	}
	

	public function get_fields($form, $id) {

		// TODO $id
		//layout, col, row, module_advanced, audio, html, photo, rich-text

		$section_countries	= WPBeaver_GeoCountry::get_fields();
		$section_city		= WPBeaver_GeoCity::get_fields();
		$section_states		= WPBeaver_GeoState::get_fields();
		$section_zipcodes	= WPBeaver_GeoZipcode::get_fields();
		$section_radius		= WPBeaver_GeoRadius::get_fields();


		$tab = [
			'geotargeting' => [
				'title'		=> esc_html__( 'Geo Targeting', 'geot' ),
				'sections'	=> [
					'geot-countries'	=> $section_countries,
					'geot-cities'		=> $section_city,
					'geot-states'		=> $section_states,
					'geot-zipcodes'		=> $section_zipcodes,
					'geot-radius'		=> $section_radius,
				],
			],
		];


		if( isset($form['tabs']) )
			$form['tabs'] = array_merge( $form['tabs'], $tab );
		else
			$form = array_merge( $form, $tab );
		

		return apply_filters( 'geot/wpbeaver/get_fields', $form );
	}


	public function render( $output, $data ) {

		if ( FLBuilderModel::is_builder_active() )
			return $output;

		$opts = geot_settings();

		if ( isset( $opts['ajax_mode'] ) && $opts['ajax_mode'] == '1' ) {

			$output = WPBeaver_GeoRadius::ajax_render( $data->settings, $output );
			$output = WPBeaver_GeoZipcode::ajax_render( $data->settings, $output );
			$output = WPBeaver_GeoState::ajax_render( $data->settings, $output );
			$output = WPBeaver_GeoCity::ajax_render( $data->settings, $output );
			$output = WPBeaver_GeoCountry::ajax_render( $data->settings, $output );

		} else {

			if ( ! WPBeaver_GeoCountry::is_render( $data->settings ) ||
			     ! WPBeaver_GeoCity::is_render( $data->settings ) ||
			     ! WPBeaver_GeoState::is_render( $data->settings ) ||
			     ! WPBeaver_GeoZipcode::is_render( $data->settings ) ||
			     ! WPBeaver_GeoRadius::is_render( $data->settings )
			) {
				return '';
			}
		}

		return $output;
	}


	public function template_path($template_path, $template_base, $slug) {
		if( $slug != 'geot' )
			return $template_path;

		if( $template_base != 'row' && $template_base != 'module' )
			return $template_path;

		return apply_filters( 'geot/wpbeaver/template', GEOWP_PLUGIN_DIR . 'includes/wpbeaver/templates/'.$template_base.'.php' );
	}


	public function template_slug($slug, $data) {

		if ( FLBuilderModel::is_builder_active() )
			return $slug;

		if( ! $this->has_geot_opts( $data->settings ) )
			return $slug;

		return 'geot';
	}


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
			if ( ! empty( $props->$key ) ) {
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