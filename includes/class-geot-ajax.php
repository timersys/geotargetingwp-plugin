<?php

/**
 * Ajax callbacks
 *
 * @link       https://geotargetingwp.com/geotargeting-pro
 * @since      1.6
 *
 * @package    GeoTarget
 * @subpackage GeoTarget/includes
 * @author     Your Name <email@example.com>
 */
class GeotWP_Ajax {
	/**
	 * $_POST data sent on ajax request
	 * @var Array
	 */
	protected $data;

	/**
	 * Plugin functions
	 *
	 * @since    1.6
	 * @access   private
	 * @var      object    Plugin functions
	 */
	private $functions;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.6
	 * @var      string $GeoTarget The name of this plugin.
	 * @var      string $version The version of this plugin.
	 */
	public function __construct() {
		add_action( 'wp_ajax_geot_ajax', [ $this, 'geot_ajax' ], 1 );
		add_action( 'wp_ajax_nopriv_geot_ajax', [ $this, 'geot_ajax' ], 1 );

		add_action( 'wp_ajax_geot/field_group/render_rules', [ 'GeotWP_Helper', 'ajax_render_rules' ], 1 );
		add_action( 'wp_ajax_geot/field_group/render_operator', [ 'GeotWP_Helper', 'ajax_render_operator' ], 1 );

		add_filter( 'geot/ajax/country_name', [ $this, 'country_name' ] );
		add_filter( 'geot/ajax/city_name', [ $this, 'city_name' ] );
		add_filter( 'geot/ajax/state_name', [ $this, 'state_name' ] );
		add_filter( 'geot/ajax/continent_name', [ $this, 'continent_name' ] );
		add_filter( 'geot/ajax/state_code', [ $this, 'state_code' ] );
		add_filter( 'geot/ajax/zip', [ $this, 'zip' ] );
		add_filter( 'geot/ajax/time_zone', [ $this, 'time_zone' ] );
		add_filter( 'geot/ajax/latitude', [ $this, 'latitude' ] );
		add_filter( 'geot/ajax/longitude', [ $this, 'longitude' ] );
		add_filter( 'geot/ajax/region', [ $this, 'region' ] );
		add_filter( 'geot/ajax/country_code', [ $this, 'country_code' ] );
		add_filter( 'geot/ajax/country_filter', [ $this, 'country_filter' ] );
		add_filter( 'geot/ajax/city_filter', [ $this, 'city_filter' ] );
		add_filter( 'geot/ajax/state_filter', [ $this, 'state_filter' ] );
		add_filter( 'geot/ajax/zip_filter', [ $this, 'zip_filter' ] );
		add_filter( 'geot/ajax/radius_filter', [ $this, 'radius_filter' ] );
		add_filter( 'geot/ajax/menu_filter', [ $this, 'menu_filter' ] );
		add_filter( 'geot/ajax/widget_filter', [ $this, 'widget_filter' ] );
		add_filter( 'geot/ajax/geo_flag', [ $this, 'geo_flag' ] );
	}

	/**
	 * Main function that execute all shortcodes
	 * put the returned data into a array and send the ajax response
	 * @return string
	 */
	public function geot_ajax() {
		define( 'DOING_GEOT_AJAX', true );
		$geots    = $posts = [];
		$settings = geot_settings();
		$this->data = $_POST;

		if ( isset( $settings['geolocation'] ) && ( $settings['geolocation'] == 'by_html5' || $settings['geolocation'] == 'by_html5_mobile' ) &&
		     isset( $_COOKIE['geot-gps'] ) && $_COOKIE['geot-gps'] == 'yes'
		) {
			geot_set_coords( $this->data['geot_lat'], $this->data['geot_lng'] );
		}

		$debug = $redirect = $blocker = $geo = "";
		$posts = $this->get_geotargeted_posts();


		if ( isset( $this->data['geot_redirects'] ) && $this->data['geot_redirects'] == 1 ) {
			$redirect = $this->geo_redirects();
		}

		if ( isset( $this->data['geot_blockers'] ) && $this->data['geot_blockers'] == 1 ) {
			$blocker = $this->geo_blockers();
		}


		if ( isset( $this->data['geots'] ) ) {
			foreach ( $this->data['geots'] as $id => $geot ) {
				if ( method_exists( $this, $geot['action'] ) ) {
					$geots[] = [
						'id'     => $id,
						'action' => $geot['action'],
						'value'  => apply_filters( "geot/ajax/{$geot['action']}",  $geot ),
					];
				}
			}


		}
		// only call debug info if we ran any geo action before to save requests
		if ( count( $posts['remove'] ) > 0
		     || count( $posts['hide'] ) > 0
		     || ! empty( $geots )
		     || ! empty( $blocker )
		     || ! empty( $redirect )
		) {
			$debug = $this->getDebugInfo();
			$geo   = geot_data();
		}
		$result = [
			'success'  => 1,
			'data'     => $geots,
			'posts'    => $posts,
			'redirect' => $redirect,
			'blocker'  => $blocker,
			'debug'    => $debug,
			'geo'      => $geo,
		];
		echo json_encode( $result );
		die();
	}

	/**
	 * Get all post that are geotargeted
	 *
	 * @return array|void
	 */
	private function get_geotargeted_posts() {
		global $wpdb;

		$posts_to_exclude = [];
		$content_to_hide  = [];

		// let users cancel the removal of posts
		// for example they can check if is_search() and show the post in search results
		if ( apply_filters( 'geot/posts_where', false, $this->data ) ) {
			return [
				'remove' => $posts_to_exclude,
				'hide'   => $content_to_hide,
			];
		};

		// by default we get all posts in case a widget or similar it's used so they are removed
		// but add filter to give user the opportunity to remove just one post like for example just current page by passint this->data['pid'] if this->data['is_singular']
		// this will make ajax mode save requests but won't be much efficient
		$geot_posts = GeotWP_Helper::get_geotarget_posts( apply_filters( 'geot/get_geotargeted_posts_pass_id', null, $this->data ) );

		if ( $geot_posts ) {
			foreach ( $geot_posts as $p ) {
				$options = unserialize( $p->geot_options );
				$target  = GeotWP_Helper::user_is_targeted( $options, $p->ID );
				if ( $target ) {
					if ( ! isset( $options['geot_remove_post'] ) || '1' != $options['geot_remove_post'] ) {
						$content_to_hide[] = [
							'id'  => $p->ID,
							'msg' => apply_filters( 'geot/forbidden_text', $options['forbidden_text'], false, $p ),
						];
					} else {
						$posts_to_exclude[] = $p->ID;
					}
				}
			}
		}

		return [
			'remove' => $posts_to_exclude,
			'hide'   => $content_to_hide,
		];
	}

	/**
	 * Print geot Redirect
	 *
	 * @param $geot
	 *
	 * @return string
	 */
	private function geo_redirects() {
		$GeoRedirect = new GeotWP_R_Public();

		return $GeoRedirect->handle_ajax_redirects();
	}

	/**
	 * Print geot Blocks
	 *
	 * @param $geot
	 *
	 * @return string
	 */
	private function geo_blockers() {
		$GeoBlocker = new GeotWP_Bl_Public();

		return $GeoBlocker->handle_ajax_blockers();
	}

	/**
	 * Grab debug info to print in footer
	 * @return string|void
	 */
	private function getDebugInfo() {
		return '<!--' . geot_debug_data() . '-->';
	}

	/**
	 * Get user country name
	 *
	 * @param $geot
	 *
	 * @return string
	 */
	public function country_name( $geot ) {
		if ( ! isset( $geot['locale'] ) ) {
			$geot['locale'] = 'en';
		}
		$name = geot_country_name( $geot['locale'] );

		if ( ! empty( $name ) ) {
			return apply_filters( 'geot/shortcodes/country_name', $name );
		}

		return apply_filters( 'geot/shortcodes/country_name_default', $geot['default'] );

	}

	/**
	 * Get user city name
	 *
	 * @param $geot
	 *
	 * @return string
	 */
	public function city_name( $geot ) {
		if ( ! isset( $geot['locale'] ) ) {
			$geot['locale'] = 'en';
		}
		$name = geot_city_name( $geot['locale'] );

		if ( ! empty( $name ) ) {
			return apply_filters( 'geot/shortcodes/city_name', $name );
		}

		return apply_filters( 'geot/shortcodes/city_name_default', $geot['default'] );

	}

	/**
	 * Get user state name
	 *
	 * @param $geot
	 *
	 * @return string
	 */
	public function state_name( $geot ) {
		if ( ! isset( $geot['locale'] ) ) {
			$geot['locale'] = 'en';
		}
		$name = geot_state_name( $geot['locale'] );

		if ( ! empty( $name ) ) {
			return apply_filters( 'geot/shortcodes/state_name', $name );
		}

		return apply_filters( 'geot/shortcodes/state_name_default', $geot['default'] );

	}

	/**
	 * Get user continent name
	 *
	 * @param $geot
	 *
	 * @return string
	 */
	public function continent_name( $geot ) {
		if ( ! isset( $geot['locale'] ) ) {
			$geot['locale'] = 'en';
		}
		$name = geot_continent( $geot['locale'] );

		if ( ! empty( $name ) ) {
			return apply_filters( 'geot/shortcodes/continent_name', $name );
		}

		return apply_filters( 'geot/shortcodes/continent_name_default', $geot['default'] );

	}

	/**
	 * Get user state code
	 *
	 * @param $geot
	 *
	 * @return string
	 */
	public function state_code( $geot ) {

		$code = geot_state_code();

		return ! empty( $code ) ? $code : $geot['default'];

	}

	/**
	 * Get user zip code
	 *
	 * @param $geot
	 *
	 * @return string
	 */
	public function zip( $geot ) {

		$code = geot_zip();

		return ! empty( $code ) ? $code : $geot['default'];
	}

	/**
	 * Get user timezone
	 *
	 * @param $geot
	 *
	 * @return string
	 */
	public function time_zone( $geot ) {

		$code = geot_time_zone();

		return ! empty( $code ) ? $code : $geot['default'];
	}

	/**
	 * Get user latitude
	 *
	 * @param $geot
	 *
	 * @return string
	 */
	public function latitude( $geot ) {

		$code = geot_lat();

		return ! empty( $code ) ? $code : $geot['default'];
	}

	/**
	 * Get user longitude
	 *
	 * @param $geot
	 *
	 * @return string
	 */
	public function longitude( $geot ) {

		$code = geot_lng();

		return ! empty( $code ) ? $code : $geot['default'];
	}

	/**
	 * Get user current regions
	 *
	 * @param $geot
	 *
	 * @return string
	 */
	public function region( $geot ) {

		$regions = geot_user_country_region( $geot['default'] );

		if ( is_array( $regions ) ) {
			return implode( ', ', $regions );
		}

		return $regions;

	}

	/**
	 * Get user country code
	 *
	 * @param $geot
	 *
	 * @return string
	 */
	public function country_code( $geot ) {

		$code = geot_country_code();

		return ! empty( $code ) ? $code : $geot['default'];

	}

	/**
	 * Filter function for countries
	 *
	 * @param $geot
	 *
	 * @return boolean
	 */
	public function country_filter( $geot ) {

		if ( geot_target( $geot['filter'], $geot['region'], $geot['ex_filter'], $geot['ex_region'] ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Filter function for cities
	 *
	 * @param $geot
	 *
	 * @return boolean
	 */
	public function city_filter( $geot ) {

		if ( geot_target_city( $geot['filter'], $geot['region'], $geot['ex_filter'], $geot['ex_region'] ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Filter function for states
	 *
	 * @param $geot
	 *
	 * @return boolean
	 */
	public function state_filter( $geot ) {

		if ( geot_target_state( $geot['filter'], $geot['region'], $geot['ex_filter'], $geot['ex_region'] ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Filter function for zip
	 *
	 * @param $geot
	 *
	 * @return boolean
	 */
	public function zip_filter( $geot ) {

		if ( geot_target_zip( $geot['filter'], $geot['region'], $geot['ex_filter'], $geot['ex_region'] ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Filter function for radius
	 * region = lat, exfilter =lng, filter =radius_km
	 *
	 * @param $geot
	 *
	 * @return boolean
	 */
	public function radius_filter( $geot ) {

		$target = geot_target_radius( $geot['region'], $geot['ex_filter'], $geot['filter'] );

		return $geot['geo_mode'] == 'include' ||  $geot['geo_mode'] == 'show' ? $target : ! $target ;
	}

	/**
	 * Filter function for menus
	 *
	 * @param $geot
	 *
	 * @return boolean
	 */
	public function menu_filter( $geot ) {

		$target = unserialize( base64_decode( $geot['filter'] ) );

		if ( GeotWP_Helper::user_is_targeted( $target, $geot['ex_filter'] ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Filter function for Widgets
	 *
	 * @param $geot
	 *
	 * @return boolean
	 */
	public function widget_filter( $geot ) {

		$target = unserialize( base64_decode( $geot['filter'] ) );
		if ( GeotWP_Helper::user_is_targeted( $target, $geot['ex_filter'] ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Print geot flag
	 *
	 * @param $geot
	 *
	 * @return string
	 */
	public function geo_flag( $geot ) {
		$country_code = ! empty( $geot['filter'] ) ? $geot['filter'] : geot_country_code();

		$squared = $geot['default'] ?: '';
		$size    = $geot['region'] ?: '30px';
		$html    = isset( $geot['html_tag'] ) ? esc_attr( $geot['html_tag'] ) : 'span';

		return '<' . $html . ' style="font-size:' . esc_attr( $size ) . '" class="flag-icon flag-icon-' . strtolower( esc_attr( $country_code ) ) . ' ' . $squared . '"></' . $html . '>';

	}
}
