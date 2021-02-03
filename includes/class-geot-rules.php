<?php

use Jaybizzle\CrawlerDetect\CrawlerDetect;

/**
 * Main Rules class
 *
 * @package    Geot
 * @subpackage Geot/includes
 */
class GeotWP_R_ules {

	private static $post_id;
	private static $detect;
	private static $referrer;
	private static $browser_language;
	private static $query_string;
	private static $is_category;
	private static $is_archive;
	private static $is_search;
	private static $current_url;

	public static function init() {

		self::$post_id      = \GeotCore\grab_post_id();
		self::$detect       = new Mobile_Detect;
		self::$referrer     = isset( $_SERVER['HTTP_REFERER'] ) ? $_SERVER['HTTP_REFERER'] : '';
		self::$query_string = isset( $_SERVER['QUERY_STRING'] ) ? $_SERVER['QUERY_STRING'] : '';
		self::$browser_language = isset( $_SERVER['HTTP_ACCEPT_LANGUAGE'] ) ? $_SERVER['HTTP_ACCEPT_LANGUAGE'] : '';
		self::$current_url  = \GeotCore\get_current_url();

		if ( defined( 'DOING_AJAX' ) ) {

			if ( isset( $_REQUEST['pid'] ) ) {
				self::$post_id = $_REQUEST['pid'];
			}
			if ( ! empty( $_REQUEST['referrer'] ) ) {
				self::$referrer = $_REQUEST['referrer'];
			}
			if ( ! empty( $_REQUEST['query_string'] ) ) {
				self::$query_string = $_REQUEST['query_string'];
			}
			if ( ! empty( $_REQUEST['is_category'] ) ) {
				self::$is_category = true;
			}
			if ( ! empty( $_REQUEST['is_archive'] ) ) {
				self::$is_archive = true;
			}
			if ( ! empty( $_REQUEST['is_search'] ) ) {
				self::$is_search = true;
			}
			if ( ! empty( $_REQUEST['url'] ) ) {
				self::$current_url = $_REQUEST['url'];
			}
			if ( ! empty( $_REQUEST['browser_language'] ) ) {
				self::$browser_language = $_REQUEST['browser_language'];
			}
		}
		// Geotargeting
		add_filter( 'geot/rules/rule_match/country', [ self::class, 'rule_match_country' ] );
		add_filter( 'geot/rules/rule_match/country_region', [ self::class, 'rule_match_country_region' ] );
		add_filter( 'geot/rules/rule_match/city', [ self::class, 'rule_match_city' ] );
		add_filter( 'geot/rules/rule_match/city_region', [ self::class, 'rule_match_city_region' ] );
		add_filter( 'geot/rules/rule_match/state', [ self::class, 'rule_match_state' ] );
		add_filter( 'geot/rules/rule_match/state_region', [ self::class, 'rule_match_state_region' ] );
		add_filter( 'geot/rules/rule_match/zip', [ self::class, 'rule_match_zip' ] );
		add_filter( 'geot/rules/rule_match/zip_region', [ self::class, 'rule_match_zip_region' ] );
		add_filter( 'geot/rules/rule_match/radius', [ self::class, 'rule_match_radius' ] );
		add_filter( 'geot/rules/rule_match/ip', [ self::class, 'rule_match_ip' ] );

		// User
		add_filter( 'geot/rules/rule_match/user_type', [ self::class, 'rule_match_user_type' ] );
		add_filter( 'geot/rules/rule_match/logged_user', [ self::class, 'rule_match_logged_user' ] );
		add_filter( 'geot/rules/rule_match/left_comment', [ self::class, 'rule_match_left_comment' ] );
		add_filter( 'geot/rules/rule_match/search_engine', [ self::class, 'rule_match_search_engine' ] );
		add_filter( 'geot/rules/rule_match/same_site', [ self::class, 'rule_match_same_site' ] );

		// Post
		add_filter( 'geot/rules/rule_match/post_type', [ self::class, 'rule_match_post_type' ] );
		add_filter( 'geot/rules/rule_match/post_id', [ self::class, 'rule_match_post' ] );
		add_filter( 'geot/rules/rule_match/post', [ self::class, 'rule_match_post' ] );
		add_filter( 'geot/rules/rule_match/post_category', [ self::class, 'rule_match_post_category' ] );
		add_filter( 'geot/rules/rule_match/post_format', [ self::class, 'rule_match_post_format' ] );
		add_filter( 'geot/rules/rule_match/post_status', [ self::class, 'rule_match_post_status' ] );
		add_filter( 'geot/rules/rule_match/taxonomy', [ self::class, 'rule_match_taxonomy' ] );

		// Page
		add_filter( 'geot/rules/rule_match/page', [ self::class, 'rule_match_post' ] );
		add_filter( 'geot/rules/rule_match/page_type', [ self::class, 'rule_match_page_type' ] );
		add_filter( 'geot/rules/rule_match/page_parent', [ self::class, 'rule_match_page_parent' ] );
		add_filter( 'geot/rules/rule_match/page_template', [ self::class, 'rule_match_page_template' ] );

		//Other
		add_filter( 'geot/rules/rule_match/custom_url', [ self::class, 'rule_match_custom_url' ] );
		add_filter( 'geot/rules/rule_match/cookie', [ self::class, 'rule_match_cookie' ] );
		add_filter( 'geot/rules/rule_match/mobiles', [ self::class, 'rule_match_mobiles' ] );
		add_filter( 'geot/rules/rule_match/tablets', [ self::class, 'rule_match_tablets' ] );
		add_filter( 'geot/rules/rule_match/desktop', [ self::class, 'rule_match_desktop' ] );
		add_filter( 'geot/rules/rule_match/referrer', [ self::class, 'rule_match_referrer' ] );
		add_filter( 'geot/rules/rule_match/crawlers', [ self::class, 'rule_match_crawlers' ] );
		add_filter( 'geot/rules/rule_match/query_string', [ self::class, 'rule_match_query_string' ] );
		add_filter( 'geot/rules/rule_match/language', [ self::class, 'rule_match_language' ] );
		add_filter( 'geot/rules/rule_match/browser_language', [ self::class, 'rule_match_browser_language' ] );
	}

	/*
	*  check_rules
	*
	* @since 1.0.0
	*/
	public static function is_ok( $rules = '' ) {

		if ( empty( $rules ) ) {
			return false;
		}
		$do_redirect = false;
		foreach ( $rules as $group_id => $group ) {

			$match_group = true;
			if ( is_array( $group ) ) {
				foreach ( $group as $rule_id => $rule ) {
					$match = apply_filters( 'geot/rules/rule_match/' . $rule['param'], $rule );

					if ( ! $match ) {
						$match_group = false;
						// if one rule fails we don't need to check the rest of the rules in the group
						// that way if we add geo rules down it won't get executed and will save credits
						break;
					}
				}
			}
			// all rules must have matched!
			if ( $match_group ) {
				$do_redirect = true;
			}
		}

		return $do_redirect;
	}

	/**
	 * Hook each rule to a field to print
	 */
	public static function set_rules_fields() {
		// GEO
		add_action( 'geot/rules/print_country_field', [ 'GeotWP_Helper', 'print_select' ], 10, 2 );
		add_action( 'geot/rules/print_country_region_field', [ 'GeotWP_Helper', 'print_select' ], 10, 2 );
		add_action( 'geot/rules/print_city_region_field', [ 'GeotWP_Helper', 'print_select' ], 10, 2 );
		add_action( 'geot/rules/print_city_field', [ 'GeotWP_Helper', 'print_textfield' ], 10, 2 );
		add_action( 'geot/rules/print_state_field', [ 'GeotWP_Helper', 'print_textfield' ], 10, 1 );
		add_action( 'geot/rules/print_state_region_field', [ 'GeotWP_Helper', 'print_select' ], 10, 2 );
		add_action( 'geot/rules/print_zip_region_field', [ 'GeotWP_Helper', 'print_select' ], 10, 2 );
		add_action( 'geot/rules/print_zip_field', [ 'GeotWP_Helper', 'print_textfield' ], 10, 1 );
		add_action( 'geot/rules/print_radius_field', [ 'GeotWP_Helper', 'print_textfield' ], 10, 2 );
		add_action( 'geot/rules/print_ip_field', [ 'GeotWP_Helper', 'print_textfield' ], 10, 1 );

		// User
		add_action( 'geot/rules/print_user_type_field', [ 'GeotWP_Helper', 'print_select' ], 10, 2 );
		add_action( 'geot/rules/print_logged_user_field', [ 'GeotWP_Helper', 'print_select' ], 10, 2 );
		add_action( 'geot/rules/print_left_comment_field', [ 'GeotWP_Helper', 'print_select' ], 10, 2 );
		add_action( 'geot/rules/print_search_engine_field', [ 'GeotWP_Helper', 'print_select' ], 10, 2 );
		add_action( 'geot/rules/print_same_site_field', [ 'GeotWP_Helper', 'print_select' ], 10, 2 );

		// Post
		add_action( 'geot/rules/print_post_type_field', [ 'GeotWP_Helper', 'print_select' ], 10, 2 );
		add_action( 'geot/rules/print_post_id_field', [ 'GeotWP_Helper', 'print_textfield' ], 10, 1 );
		add_action( 'geot/rules/print_post_field', [ 'GeotWP_Helper', 'print_select' ], 10, 2 );
		add_action( 'geot/rules/print_post_category_field', [ 'GeotWP_Helper', 'print_select' ], 10, 2 );
		add_action( 'geot/rules/print_post_format_field', [ 'GeotWP_Helper', 'print_select' ], 10, 2 );
		add_action( 'geot/rules/print_post_status_field', [ 'GeotWP_Helper', 'print_select' ], 10, 2 );
		add_action( 'geot/rules/print_taxonomy_field', [ 'GeotWP_Helper', 'print_select' ], 10, 2 );

		// Page
		add_action( 'geot/rules/print_page_field', [ 'GeotWP_Helper', 'print_select' ], 10, 2 );
		add_action( 'geot/rules/print_page_type_field', [ 'GeotWP_Helper', 'print_select' ], 10, 2 );
		add_action( 'geot/rules/print_page_parent_field', [ 'GeotWP_Helper', 'print_select' ], 10, 2 );
		add_action( 'geot/rules/print_page_template_field', [ 'GeotWP_Helper', 'print_select' ], 10, 2 );

		//Other
		add_action( 'geot/rules/print_custom_url_field', [ 'GeotWP_Helper', 'print_textfield' ], 10, 1 );
		add_action( 'geot/rules/print_mobiles_field', [ 'GeotWP_Helper', 'print_select' ], 10, 2 );
		add_action( 'geot/rules/print_desktop_field', [ 'GeotWP_Helper', 'print_select' ], 10, 2 );
		add_action( 'geot/rules/print_tablets_field', [ 'GeotWP_Helper', 'print_select' ], 10, 2 );
		add_action( 'geot/rules/print_crawlers_field', [ 'GeotWP_Helper', 'print_select' ], 10, 2 );
		add_action( 'geot/rules/print_referrer_field', [ 'GeotWP_Helper', 'print_textfield' ], 10, 1 );
		add_action( 'geot/rules/print_query_string_field', [ 'GeotWP_Helper', 'print_textfield' ], 10, 1 );
		add_action( 'geot/rules/print_cookie_field', [ 'GeotWP_Helper', 'print_textfield' ], 10, 1 );
		add_action( 'geot/rules/print_language_field', [ 'GeotWP_Helper', 'print_textfield' ], 10, 1 );
		add_action( 'geot/rules/print_browser_language_field', [ 'GeotWP_Helper', 'print_textfield' ], 10, 1 );
	}

	/**
	 * Rules options
	 * @return mixed
	 */
	public static function get_rules_choices() {
		$choices = [
			__( "Geotargeting", 'geot' ) => [
				'country'			=> __( 'Country', 'geot' ),
				'country_region'	=> __( 'Country Region', 'geot' ),
				'city'				=> __( 'City', 'geot' ),
				'city_region'		=> __( 'City Region', 'geot' ),
				'state'				=> __( 'State', 'geot' ),
				'state_region'		=> __( 'State Region', 'geot' ),
				'zip'				=> __( 'Zip Code', 'geot' ),
				'zip_region'		=> __( 'Zip Region', 'geot' ),
				'radius'			=> __( 'Lat|Lng|Radius(km)', 'geot' ),
				'ip'				=> __( 'IP', 'geot' ),
			],
			__( "User", 'geot' )         => [
				'user_type'     => __( "User role", 'geot' ),
				'logged_user'   => __( "User is logged", 'geot' ),
				'left_comment'  => __( "User never left a comment", 'geot' ) . ' *',
				'search_engine' => __( "User came via a search engine", 'geot' ),
				'same_site'     => __( "User did not arrive via another page on your site", 'geot' ),
			],
			__( "Post", 'geot' )         => [
				'post'          => __( "Post", 'geot' ),
				'post_id'       => __( "Post ID", 'geot' ),
				'post_type'     => __( "Post Type", 'geot' ),
				'post_category' => __( "Post Category", 'geot' ),
				'post_format'   => __( "Post Format", 'geot' ),
				'post_status'   => __( "Post Status", 'geot' ),
				'taxonomy'      => __( "Post Taxonomy", 'geot' ),
			],
			__( "Page", 'geot' )         => [
				'page'          => __( "Page", 'geot' ),
				'page_type'     => __( "Page Type", 'geot' ),
				'page_parent'   => __( "Page Parent", 'geot' ),
				'page_template' => __( "Page Template", 'geot' ),
			],
			__( "Other", 'geot' )        => [
				'custom_url'   => __( "Custom Url", 'geot' ),
				'cookie'       => __( "Cookie exists", 'geot' ),
				'referrer'     => __( "Referrer", 'geot' ),
				'query_string' => __( "Query String", 'geot' ),
				'mobiles'      => __( "Mobile Phone", 'geot' ),
				'tablets'      => __( "Tablet", 'geot' ),
				'desktop'      => __( "Desktop", 'geot' ),
				'crawlers'     => __( "Bots/Crawlers", 'geot' ),
				'browser_language'     => __( "Browser Language", 'geot' ),
			],
		];
		// WPML or Polylang
		if ( function_exists( 'icl_object_id' ) || function_exists( 'pll_current_language' ) ) {
			$choices[ __( 'Other', 'wp-popups-lite' ) ]['language'] = __( 'Language', 'wp-popups-lite' );
		}
		// allow custom rules rules
		return apply_filters( 'geot/metaboxes/rule_types', $choices );
	}


	/*
	* rule_match_country
	* @since 1.0.0
	*/
	public static function rule_match_country( $rule ) {

		$country_code = geot_country_code();

		if ( $rule['operator'] == "==" ) {
			return ( $country_code == $rule['value'] );
		}

		return ( $country_code != $rule['value'] );

	}

	/*
	* rule_match_country_region
	* @since 1.0.0
	*/
	public static function rule_match_country_region( $rule ) {

		if ( $rule['operator'] == "==" ) {
			return ( geot_target( '', $rule['value'] ) );
		}

		return ( ! geot_target( '', $rule['value'] ) );

	}

	/*
		* rule_match_city
		* @since 1.0.0
		*/
	public static function rule_match_city( $rule ) {

		$city = strtolower( geot_city_name() );

		$array_value = array_map('strtolower', array_map('trim', explode(',', $rule['value'])));

		if ( $rule['operator'] == "==" ) {
			return ( in_array( $city, $array_value ) );
		}

		return ( ! in_array( $city, $array_value ) );

	}

	/*
	* rule_match_state_region
	* @since 1.0.0
	*/
	public static function rule_match_state_region( $rule ) {

		if ( $rule['operator'] == "==" ) {
			return ( geot_target_state( '', $rule['value'] ) );
		}

		return ( ! geot_target_state( '', $rule['value'] ) );

	}

	/*
	* rule_match_state
	* @since 1.0.0
	*/
	public static function rule_match_state( $rule ) {

		$state      = strtolower( geot_state_name() );
		$state_code = strtolower( geot_state_code() );

		$array_value = array_map('strtolower', array_map('trim', explode(',', $rule['value'])));

		if ( $rule['operator'] == "==" ) {
			return ( in_array( $state, $array_value ) || in_array( $state_code, $array_value ) );
		}

		return ! ( in_array( $state, $array_value ) || in_array( $state_code, $array_value ) );

	}

	/*
	* rule_match_city_region
	* @since 1.0.0
	*/
	public static function rule_match_city_region( $rule ) {

		if ( $rule['operator'] == "==" ) {
			return ( geot_target_city( '', $rule['value'] ) );
		}

		return ( ! geot_target_city( '', $rule['value'] ) );

	}

	/*
	* rule_match_zip_region
	* @since 1.0.0
	*/
	public static function rule_match_zip_region( $rule ) {

		if ( $rule['operator'] == "==" ) {
			return ( geot_target_zip( '', $rule['value'] ) );
		}

		return ( ! geot_target_zip( '', $rule['value'] ) );

	}

	/*
	* rule_match_zip
	* @since 1.0.0
	*/
	public static function rule_match_zip( $rule ) {
		$zip = geot_zip();

		$array_value = array_map( 'trim', explode( ',', $rule['value'] ) );

		if ( $rule['operator'] == "==" ) {
			return ( in_array( $zip, $array_value ) );
		}

		return ( ! in_array( $zip, $array_value ) );

	}

	/*
	* rule_match_radius
	* @since 1.0.0
	*/
	public static function rule_match_radius( $rule ) {
		$array_value = array_map( 'trim', explode( '|', $rule['value'] ) );

		// Lat|Lng|Radius(km)
		if( count( $array_value ) != 3 )
			return false;

		if ( $rule['operator'] == 'inside' ) {
			return ( geot_target_radius( $array_value[0], $array_value[1], $array_value[2] ) );
		}

		return ( ! geot_target_radius( $array_value[0], $array_value[1], $array_value[2] ) );
	}

	/*
	* rule_match_ip
	* @since 1.0.0
	*/
	public static function rule_match_ip( $rule ) {
		$ip = geot_ips();

		$array_value = array_map('trim', explode( ',', $rule['value'] ) );

		if ( $rule['operator'] == "==" ) {
			return ( in_array( $ip, $array_value ) ) || GeotWP_Helper::checkIP( $ip, $array_value )  ;
		}

		return ( ! in_array( $ip, $array_value ) && ! GeotWP_Helper::checkIP( $ip, $array_value ) );

	}

	/*
	*  rule_match_post
	*
	* @since 1.0.0
	*/
	public static function rule_match_post( $rule ) {

		$post_id = self::$post_id;

		// in case multiple ids are passed
		$ids = array_map( 'trim', explode( ',', $rule['value'] ) );

		if ( $rule['operator'] == "==" ) {
			$match = in_array( $post_id, $ids );
		} elseif ( $rule['operator'] == "!=" ) {
			$match = ! in_array( $post_id, $ids );
		}

		return $match;

	}

	/**
	 * [rule_match_logged_user description]
	 *
	 * @param array $rule rule to compare
	 *
	 * @return boolean true if match
	 */
	public static function rule_match_logged_user( $rule ) {

		if ( $rule['operator'] == "==" ) {
			return is_user_logged_in();
		}

		return ! is_user_logged_in();
	}

	/**
	 * [rule_match_mobiles description]
	 *
	 * @param array $rule rule to compare
	 *
	 * @return boolean true if match
	 */
	public static function rule_match_mobiles( $rule ) {

		if ( $rule['operator'] == "==" ) {
			return self::$detect->isMobile();
		}

		return ! self::$detect->isMobile();
	}

	/**
	 * [rule_match_tablets description]
	 *
	 * @param array $rule rule to compare
	 *
	 * @return boolean true if match
	 */
	public static function rule_match_tablets( $rule ) {

		if ( $rule['operator'] == "==" ) {
			return self::$detect->isTablet();
		}

		return ! self::$detect->isTablet();
	}

	/**
	 * [rule_match_desktop description]
	 *
	 * @param array $rule rule to compare
	 *
	 * @return boolean true if match
	 */
	public static function rule_match_desktop( $rule ) {

		if ( $rule['operator'] == "==" ) {
			return ( ! self::$detect->isTablet() && ! self::$detect->isMobile() );
		}

		return ( self::$detect->isTablet() || self::$detect->isMobile() );

	}

	/**
	 * [rule_match_left_comment description]
	 *
	 * @param array $rule rule to compare
	 *
	 * @return boolean true if match
	 */
	public static function rule_match_left_comment( $rule ) {

		if ( $rule['operator'] == "==" ) {
			return ! empty( $_COOKIE[ 'comment_author_' . COOKIEHASH ] );
		}

		return empty( $_COOKIE[ 'comment_author_' . COOKIEHASH ] );
	}

	/**
	 * [rule_match_search_engine description]
	 *
	 * @param array $rule rule to compare
	 *
	 * @return boolean true if match
	 */
	public static function rule_match_search_engine( $rule ) {

		$ref = self::$referrer;

		$SE = apply_filters( 'geot/rules/search_engines', [
			'/search?',
			'.google.',
			'web.info.com',
			'search.',
			'del.icio.us/search',
			'soso.com',
			'/search/',
			'.yahoo.',
			'.bing.',
		] );
		foreach ( $SE as $url ) {
			if ( strpos( $ref, $url ) !== false ) {
				return $rule['operator'] == "==" ? true : false;
			}
		}

		return $rule['operator'] == "==" ? false : true;

	}

	/**
	 * Check for user referrer
	 *
	 * @param array $rule rule to compare
	 *
	 * @return boolean true if match
	 */
	public static function rule_match_referrer( $rule ) {

		$ref = self::$referrer;

		if ( strpos( $ref, $rule['value'] ) !== false ) {
			return $rule['operator'] == "==" ? true : false;
		}

		return $rule['operator'] == "==" ? false : true;

	}

	/**
	 * Check for custom url
	 *
	 * @param array $rule rule to compare
	 *
	 * @return boolean true if match
	 */
	public static function rule_match_custom_url( $rule ) {

		$wide_search = strpos( $rule['value'], '*' ) !== false ? true : false;

		$custom_url  = untrailingslashit( preg_replace( '#^https?://#', '', $rule['value'] ) );
		$current_url = untrailingslashit( preg_replace( '#^https?://#', '', self::$current_url ) );

		if ( $wide_search ) {
			if ( strpos( $current_url, trim( $custom_url, '*/' ) ) === 0 ) {
				return ( $rule['operator'] == "==" );
			}

			return ! ( $rule['operator'] == "==" );
		}

		if( strpos( $current_url , '?' ) !== false ) {
			$current_url = strtok( $current_url, '?' );
		}

		if ( $rule['operator'] == "==" ) {
			return ( $current_url == $custom_url );
		}

		return ! ( $current_url == $custom_url );
	}

	/**
	 * Check for crawlers / bots
	 *
	 * @param array $rule rule to compare
	 *
	 * @return boolean true if match
	 */
	public static function rule_match_crawlers( $rule ) {

		$detect = new CrawlerDetect;

		if ( $rule['operator'] == "==" ) {
			return $detect->isCrawler();
		}

		return ! $detect->isCrawler();

	}

	/**
	 * Check for query string to see if matchs all given ones
	 *
	 * @param array $rule rule to compare
	 *
	 * @return boolean true if match
	 */
	public static function rule_match_query_string( $rule ) {


		$found = strpos( self::$query_string, str_replace( '?', '', $rule['value'] ) ) > - 1 ? true : false;

		if ( $rule['operator'] == "==" ) {
			return $found;
		}

		return ! $found;

	}

	/**
	 * Check for language WPML or Polylang
	 *
	 * @param $rule
	 *
	 * @return bool
	 */
	public static function rule_match_language( $rule ) {
		$lang = '';
		// polylang
		if ( function_exists( 'pll_current_language' ) ) {
			$lang = pll_current_language();
		}
		// wpml
		if ( function_exists( 'icl_object_id' ) ) {
			$lang = isset( $_GET['lang'] ) ? $_GET['lang'] : ICL_LANGUAGE_CODE;
		}
		// match
		if ( '==' === $rule['operator'] ) {
			return ( $lang === $rule['value'] );
		}

		return ( $lang !== $rule['value'] );
	}

	/**
	 * Check for language in browser
	 *
	 * @param $rule
	 *
	 * @return bool
	 */
	public static function rule_match_browser_language( $rule ) {

		$wide_search = strpos( $rule['value'], '*' ) !== false ? true : false;
		// convert all to _
		$browser_language = strtolower(str_replace('-','_', self::$browser_language ));
		$rule_language = strtolower(str_replace('-','_', $rule['value'] ));

		if ( $wide_search ) {
			if ( strpos( $browser_language, trim( $rule_language, '*' ) ) === 0 ) {
				return ( $rule['operator'] == "==" );
			}

			return ! ( $rule['operator'] == "==" );
		}


		if ( strpos( $browser_language, $rule_language ) !== false ) {
			return $rule['operator'] == "==" ? true : false;
		}

		return $rule['operator'] == "==" ? false : true;
	}

	/**
	 * [rule_match_same_site description]
	 *
	 * @param array $rule rule to compare
	 *
	 * @return boolean true if match
	 */
	public static function rule_match_same_site( $rule ) {

		$ref = self::$referrer;

		$internal = str_replace( [ 'http://', 'https://' ], '', home_url() );

		if ( $rule['operator'] == "==" ) {
			return ! preg_match( '~' . $internal . '~i', $ref );
		}

		return preg_match( '~' . $internal . '~i', $ref );


	}

	/*
	*  rule_match_post_type
	*
	* @since 1.0.0
	*/

	public static function rule_match_post_type( $rule ) {

		$post_type = get_post_type( self::$post_id );

		if ( $rule['operator'] == "==" ) {
			return ( $post_type === $rule['value'] );
		}

		return ( $post_type !== $rule['value'] );
	}

	/*
	*  rule_match_page_type
	*
	* @since 1.0.0
	*/

	public static function rule_match_page_type( $rule ) {


		$post        = get_post( self::$post_id );
		$post_parent = isset( $post->post_parent ) ? $post->post_parent : '';
		$post_type   = get_post_type( self::$post_id );

		if ( $rule['value'] == 'front_page' ) {

			$front_page = (int) get_option( 'page_on_front' );
			if ( $front_page !== 0 ) {
				if ( $rule['operator'] == "==" ) {
					return ( $front_page == self::$post_id );
				}

				return ( $front_page != self::$post_id );
			}

			if ( $rule['operator'] == "==" ) {
				return ( home_url() == self::$current_url );
			}

			return ! ( home_url() == self::$current_url );


		} elseif ( $rule['value'] == 'category_page' ) {
			if ( $rule['operator'] == "==" ) {
				return is_category();
			}

			return ! is_category();

		} elseif ( $rule['value'] == 'archive_page' ) {
			if ( $rule['operator'] == "==" ) {
				return is_archive();
			}

			return ! is_archive();
		} elseif ( $rule['value'] == 'search_page' ) {
			if ( $rule['operator'] == "==" ) {
				return is_search();
			}

			return ! is_search();
		} elseif ( $rule['value'] == 'posts_page' ) {

			$posts_page = (int) get_option( 'page_for_posts' );

			if ( $posts_page !== 0 ) {
				if ( $rule['operator'] == "==" ) {
					return ( $posts_page == self::$post_id );
				}

				return ( $posts_page != self::$post_id );
			} else {
				if ( $rule['operator'] == "==" ) {
					return is_home();
				}

				return ! is_home();
			}

		} elseif ( $rule['value'] == 'top_level' ) {
			if ( $rule['operator'] == "==" ) {
				return ( $post_parent == 0 );
			}

			return ( $post_parent != 0 );
		} elseif ( $rule['value'] == 'parent' ) {

			$children = get_pages( [
				'post_type' => $post_type,
				'child_of'  => self::$post_id,
			] );

			if ( $rule['operator'] == "==" ) {
				return ( count( $children ) > 0 );
			}

			return ( count( $children ) == 0 );
		} elseif ( $rule['value'] == 'child' ) {
			if ( $rule['operator'] == "==" ) {
				return ( $post_parent != 0 );
			}

			return ( $post_parent == 0 );

		}

		return true;

	}


	/*
	*  rule_match_page_parent
	*
	* @since 1.0.0
	*/

	public static function rule_match_page_parent( $rule ) {

		// validation
		if ( ! self::$post_id ) {
			return false;
		}

		// vars
		$post = get_post( self::$post_id );

		$post_parent = $post->post_parent;

		if ( $rule['operator'] == "==" ) {
			return ( $post_parent == $rule['value'] );
		}

		return ( $post_parent != $rule['value'] );
	}


	/*
	*  rule_match_page_template
	*
	* @since 1.0.0
	*/

	public static function rule_match_page_template( $rule ) {

		$page_template = get_post_meta( self::$post_id, '_wp_page_template', true );

		if ( ! $page_template ) {
			if ( 'page' == get_post_type( self::$post_id ) ) {
				$page_template = "default";
			}
		}

		if ( $rule['operator'] == "==" ) {
			return ( $page_template === $rule['value'] );
		}

		return ( $page_template !== $rule['value'] );

	}


	/*
	*  rule_match_post_category
	*
	* @since 1.0.0
	*/

	public static function rule_match_post_category( $rule ) {

		if ( ! self::$post_id ) {
			return false;
		}
		// are we in archive page or single post
		if ( self::$is_archive ) {
			if ( $rule['operator'] == "==" ) {
				return ( self::$post_id == $rule['value'] );
			}
			return ( self::$post_id != $rule['value'] );
		} else {
			// post type
			$post_type = get_post_type( self::$post_id );
			// vars
			$taxonomies = get_object_taxonomies( $post_type );

			$all_terms = get_the_terms( self::$post_id, 'category' );
			if ( $all_terms ) {
				foreach ( $all_terms as $all_term ) {
					$terms[] = $all_term->term_id;
				}
			}

			// no terms at all?
			if ( empty( $terms ) ) {
				// If no ters, this is a new post and should be treated as if it has the "Uncategorized" (1) category ticked
				if ( is_array( $taxonomies ) && in_array( 'category', $taxonomies ) ) {
					$terms[] = '1';
				}
			}


			if ( $rule['operator'] == "==" ) {
				return ( is_array( $terms ) && in_array( $rule['value'], $terms ) );
			}

			return ! ( is_array( $terms ) && in_array( $rule['value'], $terms ) );
		}
	}


	/*
	*  rule_match_user_type
	*
	* @since 1.0.0
	*/

	public static function rule_match_user_type( $rule ) {
		$user = wp_get_current_user();

		if ( $rule['value'] == 'super_admin' ) {
			if ( $rule['operator'] == "==" ) {
				return is_super_admin( $user->ID );
			}

			return ! is_super_admin( $user->ID );
		}
		if ( $rule['operator'] == "==" ) {
			return in_array( $rule['value'], $user->roles );
		}

		return ! in_array( $rule['value'], $user->roles );

	}

	/*
	*  rule_match_post_format
	*
	* @since 1.0.0
	*/

	public static function rule_match_post_format( $rule ) {
		if ( ! self::$post_id ) {
			return false;
		}

		$post_type = get_post_type( self::$post_id );

		// does post_type support 'post-format'
		if ( post_type_supports( $post_type, 'post-formats' ) ) {
			$post_format = get_post_format( self::$post_id );

			if ( $post_format === false ) {
				$post_format = 'standard';
			}

		}


		if ( $rule['operator'] == "==" ) {
			return ( $post_format === $rule['value'] );
		}

		return ( $post_format !== $rule['value'] );

	}


	/*
	*  rule_match_post_status
	*
	* @since 1.0.0
	*/

	public static function rule_match_post_status( $rule ) {
		if ( ! self::$post_id ) {
			return false;
		}
		// vars
		$post_status = get_post_status( self::$post_id );

		// auto-draft = draft
		if ( $post_status == 'auto-draft' ) {
			$post_status = 'draft';
		}

		// match
		if ( $rule['operator'] == "==" ) {
			return ( $post_status === $rule['value'] );
		}

		return ( $post_status !== $rule['value'] );

	}

	/*
	*  rule_match_taxonomy
	*
	* @since 1.0.0
	*/

	public static function rule_match_taxonomy( $rule ) {

		if ( ! self::$post_id ) {
			return false;
		}
		// are we in archive page or single post
		if ( self::$is_archive ) {
			if ( $rule['operator'] == "==" ) {
				return ( self::$post_id == $rule['value'] );
			}
			return ( self::$post_id != $rule['value'] );
		} else {
			// post type
			$post_type = get_post_type( self::$post_id );

			// vars
			$taxonomies = get_object_taxonomies( $post_type );

			if ( is_array( $taxonomies ) ) {
				foreach ( $taxonomies as $tax ) {
					$all_terms = get_the_terms( self::$post_id, $tax );
					if ( $all_terms ) {
						foreach ( $all_terms as $all_term ) {
							$terms[] = $all_term->term_id;
						}
					}
				}
			}

			// no terms at all?
			if ( empty( $terms ) ) {
				// If no ters, this is a new post and should be treated as if it has the "Uncategorized" (1) category ticked
				if ( is_array( $taxonomies ) && in_array( 'category', $taxonomies ) ) {
					$terms[] = '1';
				}

			}

			if ( $rule['operator'] == "==" ) {
				return ( is_array( $terms ) && in_array( $rule['value'], $terms ) );
			}

			return ! ( is_array( $terms ) && in_array( $rule['value'], $terms ) );
		}
	}

	/**
	 * Match cookies
	 *
	 * @param $rule
	 *
	 * @return bool
	 */
	public static function rule_match_cookie( $rule ) {

		if ( $rule['operator'] == "==" ) {
			return isset( $_COOKIE[ $rule['value'] ] );
		}

		return ! isset( $_COOKIE[ $rule['value'] ] );
	}
}