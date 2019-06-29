<?php

namespace GeotCore;

use GeotWP\GeotargetingWP;

class GeotUpdates {

	private $api_url = '';
	private $api_data = [];
	private $name = '';
	private $slug = '';
	private $version = '';
	private $wp_override = false;
	private $cache_key = '';

	/**
	 * Class constructor.
	 *
	 * @param string $_plugin_file Path to the plugin file.
	 * @param array $_api_data Optional data to send with API calls.
	 *
	 * @uses plugin_basename()
	 * @uses hook()
	 *
	 */
	public function __construct( $_plugin_file, $_api_data = null ) {

		global $geot_plugin_data;

		$this->api_url     = GeotargetingWP::api_url() . 'plugins/';
		$this->api_data    = $_api_data;
		$this->name        = plugin_basename( $_plugin_file );
		$this->slug        = basename( $_plugin_file, '.php' );
		$this->version     = $_api_data['version'];
		$this->wp_override = isset( $_api_data['wp_override'] ) ? (bool) $_api_data['wp_override'] : false;
		$this->cache_key   = 'geot_' . md5( serialize( $this->slug ) );

		$geot_plugin_data[ $this->slug ] = $this->api_data;

		// Set up hooks.
		$this->init();

	}

	/**
	 * Set up WordPress filters to hook into WP's update process.
	 *
	 * @return void
	 * @uses add_filter()
	 *
	 */
	public function init() {

		add_filter( 'pre_set_site_transient_update_plugins', [ $this, 'check_update' ] );
		add_filter( 'plugins_api', [ $this, 'plugins_api_filter' ], 10, 3 );
	}

	/**
	 * Check for Updates at the defined API endpoint and modify the update array.
	 *
	 * @param array $_transient_data Update array build by WordPress.
	 *
	 * @return array Modified update array with custom plugin data.
	 * @uses api_request()
	 *
	 */
	public function check_update( $_transient_data ) {

		global $pagenow;

		if ( ! is_object( $_transient_data ) ) {
			$_transient_data = new \stdClass();
		}

		if ( 'plugins.php' == $pagenow && is_multisite() ) {
			return $_transient_data;
		}

		if ( ! empty( $_transient_data->response ) && ! empty( $_transient_data->response[ $this->name ] ) && false === $this->wp_override ) {
			return $_transient_data;
		}

		$plugin_info = $this->get_cached_version_info();

		if ( false === $plugin_info ) {
			$plugin_info = $this->api_request( 'info' );
		}

		if ( false !== $plugin_info && is_object( $plugin_info ) && isset( $plugin_info->version ) ) {

			if ( version_compare( $this->version, $plugin_info->version, '<' ) ) {
				$_transient_data->response[ $this->name ] = (object) [
					'new_version' => $plugin_info->version,
					'package'     => $plugin_info->download_link,
					'slug'        => $this->slug,
				];
			}

			$_transient_data->last_checked           = current_time( 'timestamp' );
			$_transient_data->checked[ $this->name ] = $this->version;

		}

		return $_transient_data;
	}

	public function get_cached_version_info( $cache_key = '' ) {

		if ( empty( $cache_key ) ) {
			$cache_key = $this->cache_key;
		}

		$cache = get_option( $cache_key );

		if ( empty( $cache['timeout'] ) || current_time( 'timestamp' ) > $cache['timeout'] ) {
			return false; // Cache is expired
		}

		return json_decode( $cache['value'] );

	}

	/**
	 * Calls the API and, if successfull, returns the object delivered by the API.
	 *
	 * @param string $_action The requested action.
	 *
	 * @return false|object
	 * @uses is_wp_error()
	 *
	 * @uses get_bloginfo()
	 * @uses wp_remote_post()
	 */
	private function api_request( $_action ) {

		$geot_api_request_transient = $this->get_cached_version_info( $this->cache_key );

		if ( ! empty( $geot_api_request_transient ) ) {
			return $geot_api_request_transient;
		}

		$api_params = [
			'slug' => $this->slug,
		];
		$url        = add_query_arg( $api_params, $this->api_url . $_action );

		$request = wp_remote_get( $url, [ 'timeout' => 15 ] );

		if ( is_wp_error( $request ) || isset( $request->error ) ) {
			return;
		}
		$request = json_decode( wp_remote_retrieve_body( $request ) );

		$data = $this->parseRequest( $request );
		$this->set_version_info_cache( $data );

		return $data;
	}

	/**
	 * Convert API response to wordpress plugins API needed object
	 *
	 * @param $request
	 *
	 * @return object
	 */
	private function parseRequest( $request ) {
		// if request fail or plugin don't exit
		if ( ! isset( $request->id ) ) {
			return;
		}

		$res = (object) [
			'name'          => isset( $request->name ) ? $request->name : '',
			'version'       => $request->version,
			'slug'          => $request->slug,
			'download_link' => $request->download_link,

			'tested'       => isset( $request->tested ) ? $request->tested : '',
			'requires'     => isset( $request->requires ) ? $request->requires : '',
			'last_updated' => isset( $request->updated_at ) ? $request->updated_at : '',
			'homepage'     => isset( $request->plugin_url ) ? $request->plugin_url : '',

			'sections' => [
				'description' => $request->description,
				'changelog'   => $request->changelog,
			],

			'banners' => [
				'low'  => isset( $request->banner_low ) ? $request->banner_low : '',
				'high' => isset( $request->banner_high ) ? $request->banner_high : '',
			],

			'external' => true,
		];

		return $res;
	}

	public function set_version_info_cache( $value = '' ) {

		$data = [
			'timeout' => strtotime( '+3 hours', current_time( 'timestamp' ) ),
			'value'   => json_encode( $value ),
		];

		update_option( $this->cache_key, $data );

	}

	/**
	 * Updates information on the "View version x.x details" page with custom data.
	 *
	 * @param mixed $_data
	 * @param string $_action
	 * @param object $_args
	 *
	 * @return object $_data
	 * @uses api_request()
	 *
	 */
	public function plugins_api_filter( $_data, $_action = '', $_args = null ) {

		if ( $_action != 'plugin_information' ) {
			return $_data;
		}


		if ( ! isset( $_args->slug ) || ( $_args->slug != $this->slug ) ) {
			return $_data;
		}

		$_data = $this->api_request( 'info' );

		// Convert sections into an associative array, since we're getting an object, but Core expects an array.
		if ( isset( $_data->sections ) && ! is_array( $_data->sections ) ) {
			$new_sections = [];
			foreach ( $_data->sections as $key => $data ) {
				$new_sections[ $key ] = $data;
			}
			$_data->sections = $new_sections;
		}

		// Convert banners into an associative array, since we're getting an object, but Core expects an array.
		if ( isset( $_data->banners ) && ! is_array( $_data->banners ) ) {
			$new_banners = [];
			foreach ( $_data->banners as $key => $data ) {
				$new_banners[ $key ] = $data;
			}
			$_data->banners = $new_banners;
		}

		return $_data;
	}

}