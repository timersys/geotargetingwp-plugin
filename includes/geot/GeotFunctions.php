<?php namespace GeotFunctions;

use GeotFunctions\Email\GeotEmails;
use GeotFunctions\Notice\GeotNotices;
use GeotFunctions\Notification\GeotNotifications;
use GeotFunctions\Record\RecordConverter;
use GeotFunctions\Session\GeotSession;
use GeotWP\Exception\AddressNotFoundException;
use GeotWP\Exception\GeotException;
use GeotWP\Exception\InvalidLicenseException;
use GeotWP\Exception\InvalidSubscriptionException;
use GeotWP\Exception\OutofCreditsException;
use GeotWP\GeotargetingWP;
use GeotWP\Record\GeotRecord;
use IP2Location\Database;
use Jaybizzle\CrawlerDetect\CrawlerDetect;
use MaxMind\Db\Reader;
use function GeotWP\generateCallTrace;
use function GeotWP\getUserIP;

/**
 * Class GeotFunctions
 * Bring all wordpress needed functions to make GeotargetingWP work
 * @version 1.1
 * @package GeotFunctions
 */
class GeotFunctions {
	// Hold the class instance.
	private static $_instance = null;
	/**
	 * Api class
	 * @var GeotargetingWP
	 */
	public $geotWP;
	/**
	 * Current user country and cityused everywhere
	 * @var string
	 */
	protected $userCountry;
	protected $userCity;
	protected $userState;
	protected $userContinent;
	/**
	 * Plugin settings
	 * @var array
	 */
	protected $opts;
	/**
	 * Current user IP
	 * @var string
	 */
	private $ip;
	/**
	 * Cache key used internally for class cache
	 * @var string
	 */
	private $cache_key;

	/**
	 * User calculated data
	 * @var Mixed
	 */
	private $user_data;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->set_defaults();

		$this->ip = getUserIP();

		$this->geotWP = new GeotargetingWP( trim( $this->opts['license'] ), trim( $this->opts['api_secret'] ) );

		$this->session = GeotSession::instance();

		add_action( 'geot/user_ip', [ $this, 'rewrite_ip' ], 9, 1 );

		// If we have cache mode turned on, we need to calculate user location before
		// anything gets printed
		if ( ! is_admin()
		     && ! defined( 'DOING_CRON' )
		     && ! empty( $this->opts['cache_mode'] )
		     && ! apply_filters( 'geot/disable_setUserData', false )
		     && ! defined( 'DOING_AJAX' )
		     && ! isset( $_GET['wc_ajax'] )
		) {
			add_action( 'init', [ $this, 'getUserData' ] );
			add_action( 'init', [ $this, 'createRocketCookies' ], 15 );
		}

		if ( is_admin() ) {
			new GeotNotices();
		}
	}

	/**
	 * Set some default options for the class
	 */
	private function set_defaults() {

		$args = geot_settings();

		$this->opts = wp_parse_args( $args, [
			'license'            => '',
			// similar to disable sessions but also invalidates cookies
			'cache_mode'         => false,
			// php sessions
			'bots_country'       => '',
			// a default country to return if a bot is detected
			'api_secret'         => '',
			// a default country to return if a bot is detected
			'cookie_name'        => 'geot_country',
			// cookie_name to store country iso code
			'maxmind'            => 0,
			// check if maxmind is enabled
			'maxmind_db'         => maxmind_db(),
			// path to db
			'ip2location'        => 0,
			// check if ip2location is enabled
			'ip2location_db'     => ip2location_db(),
			// path to db
			'ip2location_method' => apply_filters( 'geot/ip2location_method', '100001' ),
			// wheter we use io disk or memory for lookup
			'wpengine'           => 0,
			'kinsta'             => 0,
			'litespeed'          => 0,
		] );
	}

	/**
	 * Main Geot Instance
	 *
	 * Ensures only one instance is loaded or can be loaded.
	 *
	 * @return Geot - Main instance
	 * @see GEOT()
	 * @since 1.0.0
	 * @static
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
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
	 * Use user's setting for showing IP
	 * @param $ip
	 *
	 * @return mixed
	 */
	function rewrite_ip( $ip ) {
		$settings = geot_settings();
		$ip = $_SERVER['REMOTE_ADDR'];
		if ( isset( $settings['var_ip'] ) && ! empty( $settings['var_ip'] ) ) {
			$ip = $_SERVER[ $settings['var_ip'] ];
		}
		// if two ips provided, on use the first
		return strstr( $ip, ',') === false ? $ip : strstr( $ip, ',', true);
	}

	/**
	 * Main function that return is user targeted for
	 *
	 * @param $key city, continent, country, state
	 * @param array $args include, exclude, region, exclude_region
	 *
	 * @return bool
	 */
	public function target( $key, $args = [] ) {
		//Push places list into array
		$places         = toArray( $args['include'] );
		$exclude_places = toArray( $args['exclude'] );
		$saved_regions  = apply_filters( 'geot/get_' . $key . '_regions', [] );
		$plural_key     = toPlural( $key );

		$user_state           = new \StdClass();
		$user_state->iso_code = '';
		$user_state->name     = '';
		// for filtering set english locale
		$this->checkLocale( 'en' );

		//Append any regions
		if ( ! empty( $args['region'] ) && ! empty( $saved_regions ) ) {
			$region = toArray( $args['region'] );
			foreach ( $region as $region_name ) {
				foreach ( $saved_regions as $sr_key => $saved_region ) {
					if ( strtolower( $region_name ) == strtolower( $saved_region['name'] ) ) {
						$places = array_merge( (array) $places, (array) $saved_region[ $plural_key ] );
					}
				}
			}
			// if the key is cities and the user it's using region, check also against states. The database it's full of states and people it's using them so why not checking
			if ( 'city' == $key ) {
				$user_state = $this->get( 'state' );
			}
		}
		// append excluded regions to excluded places
		if ( ! empty( $args['exclude_region'] ) && ! empty( $saved_regions ) ) {
			$exclude_region = toArray( $args['exclude_region'] );
			foreach ( $exclude_region as $region_name ) {
				foreach ( $saved_regions as $sr_key => $saved_region ) {
					if ( strtolower( $region_name ) == strtolower( $saved_region['name'] ) ) {
						$exclude_places = array_merge( (array) $exclude_places, (array) $saved_region[ $plural_key ] );
					}
				}
			}
		}

		//set target to false
		$target = false;
		// zip it's really a city property so it's custom
		if ( $key == 'zip' ) {
			// reset locale
			$this->checkLocale();

			return $this->targetZip( $places, $exclude_places );
		}

		$user_place = $this->get( $key );


		if ( ! isset( $user_place ) || empty( (array) $user_place ) ) {
			// reset locale
			$this->checkLocale();

			return apply_filters( 'geot/target_' . $key . '/return_on_user_null', false );
		}

		if ( count( $places ) > 0 ) {
			foreach ( $places as $p ) {
				// cant use isset($user_place->name) in here because it will always return false due to object properties
				if (
					strtolower( @$user_place->name ) == strtolower( trim( $p ) )
					|| strtolower( $user_place->iso_code ) == strtolower( trim( $p ) )
					|| strtolower( @$user_state->name ) == strtolower( trim( $p ) )
					|| strtolower( $user_state->iso_code ) == strtolower( trim( $p ) )
				) {
					$target = true;
				}
			}
		} else {
			// If we don't have places to target return true
			$target = true;
		}

		if ( count( $exclude_places ) > 0 ) {
			foreach ( $exclude_places as $ep ) {
				// cant use isset($user_place->name) in here because it will always return false due to object properties
				if (
					strtolower( @$user_place->name ) == strtolower( trim( $ep ) )
					|| strtolower( $user_place->iso_code ) == strtolower( trim( $ep ) )
					|| strtolower( @$user_state->name ) == strtolower( trim( $ep ) )
					|| strtolower( $user_state->iso_code ) == strtolower( trim( $ep ) )
				) {
					$target = false;
				}
			}
		}

		// reset locale
		$this->checkLocale();

		return $target;
	}

	/**
	 * For API results we can let users change locale
	 * but also we can check against wordpress locale
	 *
	 * @param null $force_locale
	 */
	private function checkLocale( $force_locale = null ) {
		if ( ! $this->user_data[ $this->cache_key ] instanceof GeotRecord || apply_filters( 'geot/cancel_locale_check', false ) ) {
			return;
		}

		$locale = get_locale();
		// get language part of locale
		$wp_locale = strstr( $locale, '_' ) === false ? $locale : strstr( get_locale(), '_', true );
		// normalize some of them to match our locales
		switch ( $wp_locale ) {
			case 'pt':
				$wp_locale = 'pt-BR';
				break;
			case 'zh':
				$wp_locale = 'zh-CN';
				break;
		}
		// when filtering we want english locale to compare city names
		if ( $force_locale ) {
			$wp_locale = $force_locale;
		}
		// set locale on all records
		foreach ( get_object_vars( $this->user_data[ $this->cache_key ] ) as $o ) {
			$o->setDefaultLocale( $wp_locale );
		}
	}

	/**
	 * Get a specif record
	 *
	 * @param $key
	 *
	 * @return mixed
	 */
	public function get( $key ) {
		if ( ! in_array( $key, GeotRecord::getValidRecords() ) ) {
			return 'Invalid GeotRecord classname provided. Valids ones are: ' . implode( ',', GeotRecord::getValidRecords() );
		}

		if ( $this->user_data[ $this->cache_key ] === null ) {
			$this->getUserData();
		}

		if ( isset( $this->user_data[ $this->cache_key ]->$key ) ) {
			return $this->user_data[ $this->cache_key ]->$key;
		}

		return false;
	}

	/**
	 * @param string $ip
	 *
	 * @param bool $force
	 *
	 * @return array|bool|mixed
	 */
	public function getUserData( $ip = "", $force = false ) {
		if ( isset( $_GET['geot_backtrace'] ) || defined( 'GEOT_BACKTRACE' ) ) {
			$this->printBacktrace();
		}
		try {
			$this->check_active_user();

			if ( ! empty( $ip ) ) {
				$this->ip = $ip;
			}

			$this->ip = apply_filters( 'geot/user_ip', $this->ip );

			if ( empty( $this->opts['license'] ) ) {
				throw new InvalidLicenseException( json_encode( [ 'error' => 'License is missing' ] ) );
			}

			$this->cache_key = md5( $this->ip );

			if ( ! empty ( $this->user_data[ $this->cache_key ] ) ) {
				return $this->user_data[ $this->cache_key ];
			}

			$this->initUserData();

			// Easy debug
			if ( isset( $_REQUEST['geot_debug'] ) && ! empty( $_REQUEST['geot_debug'] ) ) {
				return $this->debugData();
			}

			// If user set cookie and not in debug mode. If we pass ip we are forcing to use ip instead of cookies. Eg in dropdown widget
			if ( ! empty( $_COOKIE[ $this->opts['cookie_name'] ] ) && ! $force ) {
				return $this->setData( 'country', 'iso_code', $_COOKIE[ $this->opts['cookie_name'] ] );
			}

			// If we already calculated on session return (if we are not calling by IP & if cache mode (sessions) is turned on)
			if ( $this->opts['cache_mode'] && ! empty ( $this->session->get( 'geot_data' ) ) ) {
				$this->user_data[ $this->cache_key ] = new GeotRecord( $this->session->get( 'geot_data' ) );
				$this->checkLocale();

				return $this->user_data[ $this->cache_key ];
			}
			// check for whitelist Ips
			if ( $this->user_whitelisted() ) {
				return $this->getFallbackCountry();
			}
			// check for crawlers
			$CD = new CrawlerDetect();
			if ( $CD->isCrawler() || $this->treatAsBot() ) {
				return $this->setData( 'country', 'iso_code', ! empty( $this->opts['bots_country'] ) ? $this->opts['bots_country'] : 'US' );
			}

			// WP Engine ?
			if ( isset( $this->opts['wpengine'] ) && $this->opts['wpengine'] ) {
				return $this->wpengine();
			}
			// litespeed?
			if ( isset( $this->opts['litespeed'] ) && $this->opts['litespeed'] ) {
				return $this->litespeed();
			}
			// Kingsta ?
			if ( isset( $this->opts['kinsta'] ) && $this->opts['kinsta'] ) {
				return $this->kinsta();
			}
			// maxmind ?
			if ( isset( $this->opts['maxmind'] ) && $this->opts['maxmind'] ) {
				$record = $this->maxmind();
				$this->checkLocale();

				return $record;
			}
			// ip2location ?
			if ( isset( $this->opts['ip2location'] ) && $this->opts['ip2location'] ) {
				return $this->ip2location();
			}
			//last chance filter to cancel query and pass custom data
			if ( ( $custom_data = apply_filters( 'geot/cancel_query', false ) ) ) {
				return $this->cleanResponse( $custom_data );
			}
			// API
			$record = $this->cleanResponse( $this->geotWP->getData( $this->ip ) );
			$this->checkLocale();

			return $record;

		} catch ( OutofCreditsException $e ) {
			GeotEmails::OutOfQueriesException();
			GeotNotifications::notify( $e->getMessage() );

			return $this->getFallbackCountry();
		} catch ( InvalidSubscriptionException $e ) {
			GeotEmails::InvalidSubscriptionException( $e->getMessage() );
			GeotNotifications::notify( $e->getMessage() );

			return $this->getFallbackCountry();
		} catch ( InvalidLicenseException $e ) {
			GeotEmails::AuthenticationException();
			GeotNotifications::notify( $e->getMessage() );

			return $this->getFallbackCountry();
		} catch ( GeotException $e ) {
			GeotNotifications::notify( $e->getMessage() );

			return $this->getFallbackCountry();
		} catch ( \Exception $e ) {
			GeotNotifications::notify( $e->getMessage() );

			return $this->getFallbackCountry();
		}

	}

	/**
	 * Prints backtrace into footer for debugging
	 */
	private function printBacktrace() {
		$trace = generateCallTrace();
		add_action( 'wp_footer', function () use ( $trace ) {
			echo '<!-- Geot Backtrace START ' . PHP_EOL;
			echo $trace . PHP_EOL;
			echo '<!-- Geot Backtrace END -->' . PHP_EOL;
		}, 99 );

	}

	/**
	 * Check if user has active subscription
	 * @return bool
	 * @throws InvalidLicenseException
	 * @throws InvalidSubscriptionException
	 */
	private function check_active_user() {

		if (
			( ! isset( $this->opts['wpengine'] ) || $this->opts['wpengine'] != '1' || getenv( 'HTTP_GEOIP_COUNTRY_CODE' ) === false )
			&& ( ! isset( $this->opts['maxmind'] ) || $this->opts['maxmind'] != '1' || ! file_exists( maxmind_db() ) )
			&& ( ! isset( $this->opts['ip2location'] ) || $this->opts['ip2location'] != '1' || ! file_exists( ip2location_db() ) )
			&& ( ! isset( $this->opts['kinsta'] ) || $this->opts['kinsta'] != '1' || empty( $_SERVER['HTTP_GEOIP_CITY_COUNTRY_NAME'] ) )
			&& ( ! isset( $this->opts['litespeed'] ) || $this->opts['litespeed'] != '1' || empty( $_SERVER['GEOIP_COUNTRY_CODE'] ) )
		) {
			return true;
		}

		if ( empty( $this->opts['license'] ) ) {
			throw new InvalidLicenseException( json_encode( [ 'error' => 'Missing or invalid license' ] ) );
		}

		if ( false === ( $active_user = get_site_transient( 'geot_active_user' ) ) ) {
			// It wasn't there, so regenerate the data and save the transient
			$active_user = GeotargetingWP::checkSubscription( $this->opts['license'] );
			$result      = json_decode( $active_user );
			if ( ! isset( $result->success ) ) {
				throw new InvalidSubscriptionException( json_encode( [ 'error' => $result->error ] ) );
			}

			set_site_transient( 'geot_active_user', true, DAY_IN_SECONDS );

			return true;
		}

		return $active_user;
	}

	/**
	 * Init empty Object of user data
	 */
	private function initUserData() {
		$this->user_data[ $this->cache_key ] = (object) [
			'continent'   => new \StdClass(),
			'country'     => new \StdClass(),
			'state'       => new \StdClass(),
			'city'        => new \StdClass(),
			'geolocation' => new \StdClass(),
		];
	}

	/**
	 * Return debug data set in query vars
	 */
	private function debugData() {

		$state           = new \stdClass;
		$state->names    = isset( $_REQUEST['geot_state'] ) ? [ filter_var( $_REQUEST['geot_state'], FILTER_SANITIZE_FULL_SPECIAL_CHARS ) ] : '';
		$state->iso_code = isset( $_REQUEST['geot_state_code'] ) ? filter_var( $_REQUEST['geot_state_code'], FILTER_SANITIZE_FULL_SPECIAL_CHARS ) : '';

		$country = new \stdClass;

		$country->names = [ filter_var( $_REQUEST['geot_debug'], FILTER_SANITIZE_FULL_SPECIAL_CHARS ) ];
		$continent      = new \stdClass;

		$continent->names  = isset( $_REQUEST['geot_continent'] ) ? [ filter_var( $_REQUEST['geot_continent'], FILTER_SANITIZE_FULL_SPECIAL_CHARS ) ] : '';
		$country->iso_code = isset( $_REQUEST['geot_debug_iso'] ) ? filter_var( $_REQUEST['geot_debug_iso'], FILTER_SANITIZE_FULL_SPECIAL_CHARS ) : '';
		$city              = new \stdClass;

		$city->names = isset( $_REQUEST['geot_city'] ) ? [ filter_var( $_REQUEST['geot_city'], FILTER_SANITIZE_FULL_SPECIAL_CHARS ) ] : '';
		$city->zip   = isset( $_REQUEST['geot_zip'] ) ? filter_var( $_REQUEST['geot_zip'], FILTER_SANITIZE_FULL_SPECIAL_CHARS ) : '';

		$geolocation                  = new \stdClass();
		$geolocation->accuracy_radius = '';
		$geolocation->longitude       = '';
		$geolocation->latitude        = '';
		$geolocation->time_zone       = '';

		$this->user_data[ $this->cache_key ] = new GeotRecord( (object) [
			'country'     => $country,
			'city'        => $city,
			'state'       => $state,
			'continent'   => $continent,
			'geolocation' => $geolocation,
		] );

		return $this->user_data[ $this->cache_key ];
	}

	/**
	 * Add new values or update in user data
	 *
	 * @param $key
	 * @param $property
	 * @param $value
	 *
	 * @return mixed
	 */
	public function setData( $key, $property, $value ) {
		$this->user_data[ $this->cache_key ]->$key->$property = $value;
		$this->user_data[ $this->cache_key ]                  = new GeotRecord( $this->user_data[ $this->cache_key ] );

		return $this->user_data[ $this->cache_key ];
	}

	/**
	 * Check if the user is whitelisted in settings
	 */
	private function user_whitelisted() {
		$ret = false;
		// Ips check
		$settings = geot_settings();
		if ( isset( $settings['fallback_country_ips'] ) && in_array( $this->ip, textarea_to_array( $settings['fallback_country_ips'] ) ) ) {
			$ret = true;
		}

		return apply_filters( 'geot/treat_request_as_whitelisted', $ret );
	}

	/**
	 * If we have an exception, return
	 * @return array|bool
	 */
	private function getFallbackCountry() {

		if ( empty( $this->opts['fallback_country'] ) ) {
			$this->opts['fallback_country'] = 'US';
		}
		$record = (object) [
			'continent'   => new \StdClass(),
			'country'     => new \StdClass(),
			'state'       => new \StdClass(),
			'city'        => new \StdClass(),
			'geolocation' => new \StdClass(),
		];
		// debug page return empty
		if ( isset( $_GET['page'] ) && 'geot-debug-data' == $_GET['page'] ) {
			return new GeotRecord( $record );
		}

		$record->country = $this->getCountryByIsoCode( $this->opts['fallback_country'] );

		$this->user_data[ $this->cache_key ] = new GeotRecord( $record );

		return $this->user_data[ $this->cache_key ];

	}

	/**
	 * Get country from database and return object like api
	 *
	 * @param $iso_code
	 *
	 * @return StdClass
	 */
	private function getCountryByIsoCode( $iso_code ) {
		global $wpdb;
		$query          = "SELECT * FROM {$wpdb->base_prefix}geot_countries WHERE iso_code = %s";
		$result         = $wpdb->get_row( $wpdb->prepare( $query, [ $iso_code ] ), ARRAY_A );
		$country        = new \StdClass();
		$country->names = new \StdClass();

		$country->names->en = $result['country'];
		$country->iso_code  = $result['iso_code'];

		return $country;
	}

	/**
	 * Check for some urls where geotargeting should be disabled
	 * @return bool
	 */
	private function treatAsBot() {
		$ret = false;
		// exclude login page and some others
		$script = isset( $_SERVER['PHP_SELF'] ) ? basename( $_SERVER['PHP_SELF'] ) : '';
		if ( in_array( $script, [ 'wp-login.php', 'xmlrpc.php', 'wp-cron.php' ] ) ) {
			$ret = true;
		}
		// Some more checks in case above fails

		// Nothing to do if autosave.
		if ( defined( 'DOING_AUTOSAVE' ) ) {
			$ret = true;
		}

		// Nothing to do if XMLRPC request.
		if ( defined( 'XMLRPC_REQUEST' ) ) {
			$ret = true;
		}

		// No go with CRON
		if ( defined( 'DOING_CRON' ) && DOING_CRON ) {
			$ret = true;
		}

		if ( is_feed() ) {
			$ret = true;
		}

		// same fo rCLI
		if ( PHP_SAPI == 'cli' || ( defined( 'WP_CLI' ) && WP_CLI ) ) {
			$ret = true;
		}

		// Ips check
		$settings = geot_settings();
		if ( isset( $settings['bots_country_ips'] ) && in_array( $this->ip, textarea_to_array( $settings['bots_country_ips'] ) ) ) {
			$ret = true;
		}

		return apply_filters( 'geot/treat_request_as_bot', $ret );
	}

	/**
	 * Use WpEngine variables (enterprise plans only)
	 * @return GeotRecord
	 * @throws GeotException
	 */
	private function wpengine() {
		try {
			return $this->cleanResponse( RecordConverter::wpEngine() );
		} catch ( \Exception $e ) {
			throw new GeotException( $e->getMessage() );
		}
	}

	/**
	 * Save user data, save to session and create record
	 * and create GeotRecord class
	 *
	 * @param $response
	 *
	 * @return GeotRecord
	 */
	private function cleanResponse( $response ) {

		if ( $this->opts['cache_mode'] ) {
			$this->session->set( 'geot_data', $response );
		}

		$this->user_data[ $this->cache_key ] = new GeotRecord( $response );

		return $this->user_data[ $this->cache_key ];
	}

	/**
	 * Use Litespped variables (users must add variables)
	 * @return GeotRecord
	 * @throws GeotException
	 */
	private function litespeed() {
		try {
			return $this->cleanResponse( RecordConverter::litespeed() );
		} catch ( \Exception $e ) {
			throw new GeotException( $e->getMessage() );
		}
	}

	/**
	 * Use Kinsta variables (enterprise plans only)
	 * @return GeotRecord
	 * @throws GeotException
	 */
	private function kinsta() {
		try {
			return $this->cleanResponse( RecordConverter::kinsta() );
		} catch ( \Exception $e ) {
			throw new GeotException( $e->getMessage() );
		}
	}

	/**
	 * Use maxmind local db
	 * @return GeotRecord
	 * @throws AddressNotFoundException
	 * @throws GeotException
	 */
	private function maxmind() {

		$reader = new Reader( $this->opts['maxmind_db'] );
		try {
			$record = $reader->get( $this->ip );
			if ( empty( $record ) ) {
				throw new AddressNotFoundException( 'Ip Address not found' );
			}
			$reader->close();

			return $this->cleanResponse( RecordConverter::maxmindRecord( $record ) );
		} catch ( AddressNotFoundException $e ) {
			throw new AddressNotFoundException( (string) $e->getMessage() );
		} catch ( \Exception $e ) {
			throw new GeotException( $e->getMessage() );
		}

	}

	/**
	 * Use ip2location database
	 * @return GeotRecord
	 * @throws GeotException
	 */
	private function ip2location() {
		$db = new Database( $this->opts['ip2location_db'], $this->opts['ip2location_method'] );
		try {
			$record = $db->lookup( $this->ip, Database::ALL );

			return $this->cleanResponse( RecordConverter::ip2locationRecord( $record ) );
		} catch ( \Exception $e ) {
			throw new GeotException( $e->getMessage() );
		}
	}

	/**
	 * Target by ZIP
	 *
	 * @param $places
	 * @param $exclude_places
	 *
	 * @return bool
	 */
	private function targetZip( $places, $exclude_places ) {
		$target     = false;
		$user_place = $this->get( 'city' );
		if ( ! $user_place ) {
			return apply_filters( 'geot/target_zip/return_on_user_null', false );
		}

		if ( count( $places ) > 0 ) {
			foreach ( $places as $zip ) {
				if ( strtolower( $user_place->zip ) == strtolower( $zip ) ) {
					$target = true;
				}
			}
		} else {
			// If we don't have places to target return true
			$target = true;
		}

		if ( count( $exclude_places ) > 0 ) {
			foreach ( $exclude_places as $ezip ) {
				if ( strtolower( $user_place->zip ) == strtolower( $ezip ) ) {
					$target = false;
				}
			}
		}

		return $target;
	}

	/**
	 * Create cookies so WPRocket plugin
	 * can generate different page caches
	 */
	public function createRocketCookies() {

		if ( apply_filters( 'geot/disable_cookies', false ) ) {
			return;
		}

		if ( ! $this->user_data[ $this->cache_key ] instanceof GeotRecord ) {
			return;
		}
		$country = isset( $this->user_data[ $this->cache_key ]->country->iso_code ) ? $this->user_data[ $this->cache_key ]->country->iso_code : 'not detected';
		$state   = isset( $this->user_data[ $this->cache_key ]->state->iso_code ) ? $this->user_data[ $this->cache_key ]->state->iso_code : 'not detected';
		$city    = isset( $this->user_data[ $this->cache_key ]->city->name ) ? $this->user_data[ $this->cache_key ]->city->name : 'not detected';

		setcookie( 'geot_rocket_country', apply_filters( 'geot_rocket_country', $country ), 0, '/' );
		setcookie( 'geot_rocket_state', apply_filters( 'geot_rocket_state', $state ), 0, '/' );
		setcookie( 'geot_rocket_city', apply_filters( 'geot_rocket_city', $city ), 0, '/' );
	}

	public function getSession() {
		return $this->session;
	}
}
