<?php

namespace GeotFunctions\Session;

use EAMann\Sessionz\Handlers\EncryptionHandler;
use EAMann\Sessionz\Handlers\MemoryHandler;
use EAMann\Sessionz\Manager;
use EAMann\WPSession\DatabaseHandler;
use EAMann\WPSession\OptionsHandler;

/**
 * GeotSession wrapper Class
 *
 * @since 1.5
 */
class GeotSession {

	// Hold the class instance.
	private static $_instance = null;

	/**
	 * Holds our session data
	 *
	 * @var array
	 * @access private
	 * @since 1.5
	 */
	private $session;

	/**
	 * Session index prefix
	 *
	 * @var string
	 * @access private
	 * @since 2.3
	 */
	private $prefix = '';

	/**
	 * Get things started
	 *
	 * Defines our WP_Session constants, includes the necessary libraries and
	 * retrieves the WP Session instance
	 *
	 * @since 1.5
	 */
	public function __construct() {

		if ( ! $this->should_start_session() ) {
			return;
		}

		// Queue up the session stack
		$wp_session_handler = Manager::initialize();
		if ( defined( 'WP_SESSION_USE_OPTIONS' ) && WP_SESSION_USE_OPTIONS ) {
			$wp_session_handler->addHandler( new OptionsHandler() );
		} else {
			$wp_session_handler->addHandler( new DatabaseHandler() );
		}

		if ( defined( 'WP_SESSION_ENC_KEY' ) && WP_SESSION_ENC_KEY ) {
			$wp_session_handler->addHandler( new EncryptionHandler( WP_SESSION_ENC_KEY ) );
		}

		$wp_session_handler->addHandler( new MemoryHandler() );

		// Create the required table.
		add_action( 'admin_init', [ 'EAMann\WPSession\DatabaseHandler', 'create_table' ] );
		add_action( 'wp_session_init', [ 'EAMann\WPSession\DatabaseHandler', 'create_table' ] );
		add_action( 'wp_install', [ 'EAMann\WPSession\DatabaseHandler', 'create_table' ] );

		// Start up session management, if we're not in the CLI
		if ( ! defined( 'WP_CLI' ) || false === WP_CLI ) {
			add_action( 'plugins_loaded', [ $this, 'wp_session_manager_start_session' ], 10, 0 );
		}
	}

	/**
	 * Determines if we should start sessions
	 *
	 * @return bool
	 * @since  2.5.11
	 */
	public function should_start_session() {
		$start_session = true;
		if ( ! empty( $_SERVER['REQUEST_URI'] ) ) {
			$blacklist = $this->get_blacklist();
			$uri       = ltrim( $_SERVER['REQUEST_URI'], '/' );
			$uri       = untrailingslashit( $uri );
			if ( in_array( $uri, $blacklist ) ) {
				$start_session = false;
			}
			if ( false !== strpos( $uri, 'feed=' ) ) {
				$start_session = false;
			}
		}
		if ( isset( $_GET['page'] ) && 'geot-debug-data' == $_GET['page'] ) {
			$start_session = false;
		}

		return apply_filters( 'geot/sessions/start_session', $start_session );
	}

	/**
	 * Retrieve the URI blacklist
	 *
	 * These are the URIs where we never start sessions
	 *
	 * @return array
	 * @since  2.5.11
	 */
	public function get_blacklist() {
		$blacklist = apply_filters( 'geot/sessions/session_start_uri_blacklist', [
			'feed',
			'feed/rss',
			'feed/rss2',
			'feed/rdf',
			'feed/atom',
			'comments/feed',
		] );
		// Look to see if WordPress is in a sub folder or this is a network site that uses sub folders
		$folder = str_replace( network_home_url(), '', get_site_url() );
		if ( ! empty( $folder ) ) {
			foreach ( $blacklist as $path ) {
				$blacklist[] = $folder . '/' . $path;
			}
		}

		return $blacklist;
	}

	/**
	 * Main GeotSession Instance
	 *
	 * Ensures only one instance is loaded or can be loaded.
	 *
	 * @return GeotSession - Main instance
	 * @since 1.0.0
	 * @static
	 *
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * If a session hasn't already been started by some external system, start one!
	 */
	function wp_session_manager_start_session() {
		if ( session_status() !== PHP_SESSION_ACTIVE ) {
			session_start();
		}
	}

	/**
	 * Cloning is forbidden.
	 * @since 1.0.0
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'wsi' ), '2.1' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 * @since 1.0.0
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'wsi' ), '2.1' );
	}

	/**
	 * Retrieve a session variable
	 *
	 * @access public
	 *
	 * @param string $key Session key
	 *
	 * @return mixed Session variable
	 * @since 1.5
	 *
	 */
	public function get( $key ) {
		$key = sanitize_key( $key );

		return isset( $_SESSION[ $key ] ) ? json_decode( $_SESSION[ $key ] ) : false;
	}

	/**
	 * Set a session variable
	 *
	 * @param string $key Session key
	 * @param int|string|array $value Session variable
	 *
	 * @return mixed Session variable
	 * @since 1.5
	 *
	 */
	public function set( $key, $value ) {
		$key = sanitize_key( $key );

		$_SESSION[ $key ] = wp_json_encode( $value );

		return $_SESSION[ $key ];
	}
}