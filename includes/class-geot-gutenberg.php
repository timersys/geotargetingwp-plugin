<?php

/**
 * Gutenberg Extension
 *
 * @link       https://geotargetingwp.com/geotargeting-pro
 * @since      1.6.3
 *
 * @package    GeoTarget
 * @subpackage GeoTarget/includes
 * @author     Damian Logghe
 */
class GeotWP_Gutenberg {

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @var      string $GeoTarget The name of this plugin.
	 * @var      string $version The version of this plugin.
	 */
	public function __construct() {

		add_action( 'init', [ $this, 'register_init' ] );
		add_filter( 'block_categories', [ $this, 'register_category' ], 10, 2 );
		add_action( 'enqueue_block_editor_assets', [ $this, 'register_block' ] );
	}

	public function register_category( $categories, $post ) {

		return array_merge(
			$categories,
			[
				[
					'slug'  => 'geot-block',
					'title' => __( 'Geotargeting', 'geot' ),
					'icon'  => '',
				],
			]
		);
	}

	/**
	 * Register Blocks
	 * @var
	 */
	public function register_init() {

		if ( function_exists( 'register_block_type' ) ) {

			register_block_type( 'geotargeting-pro/gutenberg-country',
				[ 'render_callback' => [ $this, 'save_gutenberg_country' ] ]
			);

			register_block_type( 'geotargeting-pro/gutenberg-city',
				[ 'render_callback' => [ $this, 'save_gutenberg_city' ] ]
			);

			register_block_type( 'geotargeting-pro/gutenberg-state',
				[ 'render_callback' => [ $this, 'save_gutenberg_state' ] ]
			);

			register_block_type( 'geotargeting-pro/gutenberg-zipcode',
				[ 'render_callback' => [ $this, 'save_gutenberg_zipcode' ] ]
			);
		}
	}

	/**
	 * Save Country Block
	 * @var    string $attributes
	 * @var    string $content
	 */
	public function save_gutenberg_country( $attributes, $content ) {

		$in_countries = $ex_countries = $in_regions = $ex_regions = $in_regions_i = $ex_regions_i = '';

		extract( $attributes );

		if ( is_array( $in_regions ) && count( $in_regions ) > 0 ) {
			$in_regions_i = implode( ',', $in_regions );
		}

		if ( is_array( $ex_regions ) && count( $ex_regions ) > 0 ) {
			$ex_regions_i = implode( ',', $ex_regions );
		}

		$opts = geot_settings();

		if ( isset( $opts['ajax_mode'] ) && $opts['ajax_mode'] == '1' ) {
			return '<div class="geot-ajax geot-filter" data-action="country_filter" data-filter="' . $in_countries . '" data-region="' . $in_regions_i . '" data-ex_filter="' . $ex_countries . '" data-ex_region="' . $ex_regions_i . '">' . $content . '</div>';
		} else {
			if ( geot_target( $in_countries, $in_regions, $ex_countries, $ex_regions ) ) {
				return $content;
			}
		}

		return '';
	}

	/**
	 * Save City Block
	 * @var    string $attributes
	 * @var    string $content
	 */
	public function save_gutenberg_city( $attributes, $content ) {
		$in_cities = $ex_cities = $in_regions = $ex_regions = $in_regions_i = $ex_regions_i = '';

		extract( $attributes );

		if ( is_array( $in_regions ) && count( $in_regions ) > 0 ) {
			$in_regions_i = implode( ',', $in_regions );
		}

		if ( is_array( $ex_regions ) && count( $ex_regions ) > 0 ) {
			$ex_regions_i = implode( ',', $ex_regions );
		}

		$opts = geot_settings();

		if ( isset( $opts['ajax_mode'] ) && $opts['ajax_mode'] == '1' ) {
			return '<div class="geot-ajax geot-filter" data-action="city_filter" data-filter="' . $in_cities . '" data-region="' . $in_regions_i . '" data-ex_filter="' . $ex_cities . '" data-ex_region="' . $ex_regions_i . '">' . $content . '</div>';
		} else {
			if ( geot_target_city( $in_cities, $in_regions, $ex_cities, $ex_regions ) ) {
				return $content;
			}
		}

		return '';
	}

	/**
	 * Save State Block
	 * @var    string $attributes
	 * @var    string $content
	 */
	public function save_gutenberg_state( $attributes, $content ) {
		$in_states = $ex_states = '';

		extract( $attributes );

		$opts = geot_settings();

		if ( isset( $opts['ajax_mode'] ) && $opts['ajax_mode'] == '1' ) {
			return '<div class="geot-ajax geot-filter" data-action="state_filter" data-filter="' . $in_states . '" data-ex_filter="' . $ex_states . '">' . $content . '</div>';
		} else {
			if ( geot_target_state( $in_states, $ex_states ) ) {
				return $content;
			}
		}

		return '';
	}

	/**
	 * Save Zipcode Block
	 * @var    string $attributes
	 * @var    string $content
	 */
	public function save_gutenberg_zipcode( $attributes, $content ) {
		$in_zipcodes = $ex_zipcodes = '';

		extract( $attributes );

		$opts = geot_settings();

		if ( isset( $opts['ajax_mode'] ) && $opts['ajax_mode'] == '1' ) {
			return '<div class="geot-ajax geot-filter" data-action="zip_filter" data-filter="' . $in_zipcodes . '" data-ex_filter="' . $ex_zipcodes . '">' . $content . '</div>';
		} else {
			if ( geot_target_zip( $in_zipcodes, $ex_zipcodes ) ) {
				return $content;
			}
		}

		return '';
	}

	/**
	 * Register JS Blocks
	 * @var    string $attributes
	 * @var    string $content
	 */
	public function register_block() {

		/********************
		 * JS to Geot
		 *********************/
		$modules_geot = [
			'geotargeting-pro/gutenberg-country',
			'geotargeting-pro/gutenberg-city',
			'geotargeting-pro/gutenberg-state',
			'geotargeting-pro/gutenberg-zipcode',
		];

		$localize_geot = [
			'icon_country'    => GEOWP_PLUGIN_URL . '/admin/img/world.png',
			'icon_city'       => GEOWP_PLUGIN_URL . '/admin/img/cities.png',
			'icon_state'      => GEOWP_PLUGIN_URL . '/admin/img/states.png',
			'icon_zipcode'    => GEOWP_PLUGIN_URL . '/admin/img/states.png',
			'regions_country' => $this->get_regions( 'countries' ),
			'regions_city'    => $this->get_regions( 'cities' ),
			'modules'         => $modules_geot,
		];

		wp_enqueue_script(
			'gutenberg-geo',
			GEOWP_PLUGIN_URL . '/includes/gutenberg/gutenberg-geot.js',
			[ 'wp-blocks', 'wp-element', 'wp-components', 'wp-i18n', 'wp-editor' ],
			$this->version,
			true
		);
		wp_localize_script( 'gutenberg-geo', 'gutgeot', $localize_geot );


		/**********************
		 * JS to Country
		 ***********************/
		wp_enqueue_script(
			'gutenberg-geo-country',
			GEOWP_PLUGIN_URL . '/includes/gutenberg/gutenberg-geot-country.js',
			[ 'gutenberg-geo' ],
			$this->version,
			true
		);


		/**********************
		 * JS to City
		 ***********************/
		wp_enqueue_script(
			'gutenberg-geo-city',
			GEOWP_PLUGIN_URL . '/includes/gutenberg/gutenberg-geot-city.js',
			[ 'gutenberg-geo' ],
			$this->version,
			true

		);


		/**********************
		 * JS to State
		 ***********************/
		wp_enqueue_script(
			'gutenberg-geo-state',
			GEOWP_PLUGIN_URL . '/includes/gutenberg/gutenberg-geot-state.js',
			[ 'gutenberg-geo' ],
			$this->version,
			true
		);


		/**********************
		 * JS to Zipcode
		 ***********************/
		wp_enqueue_script(
			'gutenberg-geo-zipcode',
			GEOWP_PLUGIN_URL . '/includes/gutenberg/gutenberg-geot-zipcode.js',
			[ 'gutenberg-geo' ],
			$this->version,
			true
		);
	}

	/**
	 * Get Regions
	 * @var    string $slug_region
	 */
	protected function get_regions( $slug_region = 'country' ) {

		$dropdown_values = [];

		switch ( $slug_region ) {
			case 'cities':
				$regions = geot_city_regions();
				break;
			default:
				$regions = geot_country_regions();
		}

		if ( ! empty( $regions ) ) {
			foreach ( $regions as $r ) {
				if ( isset( $r['name'] ) ) {
					$dropdown_values[] = [ 'value' => $r['name'], 'label' => $r['name'] ];
				}
			}
		}

		return $dropdown_values;
	}
}
