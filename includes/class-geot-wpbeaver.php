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
		add_action( 'plugins_loaded', [ $this, 'module_init' ] );

		add_filter( 'fl_builder_register_settings_form', [$this, 'get_fields'], 10, 2);
		add_filter( 'fl_builder_render_module_content', [$this, 'render'], 10, 2 );
	}
	
	/**
	 * Setup hooks if the builder is installed and activated.
	 */
	public function module_init() {
		
		if ( ! class_exists( 'FLBuilder' ) )
			return;	
		
		require_once GEOWP_PLUGIN_DIR . 'includes/wpbeaver/wpbeaver-geot-country.php';
		//require_once GEOWP_PLUGIN_DIR . 'includes/wpbeaver/wpbeaver-geot-city.php';
		//require_once GEOWP_PLUGIN_DIR . 'includes/wpbeaver/wpbeaver-geot-state.php';
		//require_once GEOWP_PLUGIN_DIR . 'includes/wpbeaver/wpbeaver-geot-zipcode.php';
		
	}
	

	public function get_fields($form, $id) {

		// TODO $id
		//layout, col, row, module_advanced, audio, html, photo, rich-text

		$section_countries  = WPBeaver_GeoCountry::get_fields();
		//$section_city     = WPBeaver_GeoCity::get_fields();
		//$section_states   = WPBeaver_GeoState::get_fields();
		//$section_zipcodes = WPBeaver_GeoZipcode::get_fields();


		$tab = [
				'geotargeting' => [
					'title'		=> esc_html__( 'Geo Targeting', 'geot' ),
					'sections'	=> [
						'geot-countries'	=> $section_countries,
						//'geot-cities'		=> $section_city,
						//'geot-states'		=> $section_states,
						//'geot-zipcodes'	=> $section_zipcodes,
					],
				],
			];



		if( isset($form['tabs']) )
			$form['tabs'] = array_merge($form['tabs'], $tab);
		else
			$form = array_merge($form, $tab);
		

		return apply_filters( 'geot/wpbeaver/get_fields', $form );
	}


	public function render( $output, $module ) {

		if ( FLBuilderModel::is_builder_active() )
			return $output;

		$opts = geot_settings();
		//$reg_countries 	= array_values( self::get_regions( 'country' ) );
		//$reg_cities 	= array_values( self::get_regions( 'city' ) );


		if ( isset( $opts['ajax_mode'] ) && $opts['ajax_mode'] == '1' ) {

			//$output = WPBeaver_GeoZipcode::ajax_render( $module->settings, $output );
			//$output = WPBeaver_GeoState::ajax_render( $module->settings, $output );
			//$output = WPBeaver_GeoCity::ajax_render( $module->settings, $reg_countries, $output );
			$output = WPBeaver_GeoCountry::ajax_render( $module->settings, $output );

		} else {

			if ( ! WPBeaver_GeoCountry::is_render( $module->settings )
			    // ! WPBeaver_GeoCity::is_render( $module->settings, $reg_cities ) ||
			    // ! WPBeaver_GeoState::is_render( $module->settings ) ||
			    // ! WPBeaver_GeoZipcode::is_render( $module->settings )
			) {
				return '';
			}
		}

		return $output;
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