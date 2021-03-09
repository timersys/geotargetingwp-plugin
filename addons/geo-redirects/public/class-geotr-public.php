<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://timersys.com
 * @since      1.0.0
 *
 * @package    Geotr
 * @subpackage Geotr/public
 */

use function GeotCore\geotWPR_redirections;
use function GeotCore\get_current_url;
use function GeotCore\is_backend;
use function GeotCore\is_builder;
use GeotCore\Session\GeotSession;
use function GeotCore\is_rest_request;
use function GeotCore\textarea_to_array;
use function GeotWP\getUserIP;
use function GeotWP\is_session_started;
use Jaybizzle\CrawlerDetect\CrawlerDetect;

/**
 * @package    Geotr
 * @subpackage Geotr/public
 * @author     Damian Logghe <damian@timersys.com>
 */
class GeotWP_R_Public {
	/**
	 * @var bool to ajaxmode
	 */
	public $ajax_call = false;
	/**
	 * @var Array of Redirection posts
	 */
	private $redirections;

	public function __construct() {
		add_action( 'plugins_loaded', [ $this, 'init_geotWP' ], - 2 );

		if ( ! is_admin() && ! is_backend() && ! defined( 'DOING_AJAX' ) && ! defined( 'DOING_CRON' ) && ! is_builder() ) {
			add_action( apply_filters( 'geotr/action_hook', 'wp' ), [ $this, 'handle_redirects' ] );
		}
	}

	// Call geot once to init session handling
	// otherwise it will fail with georedirects and cache mode turned on

	/**
	 * Print placeholder in front end
	 */
	public static function ajax_placeholder(){
		$opts = geotr_settings();
	?><!-- Geo Redirects plugin https://geotargetingwp.com-->
<div class="geotr-ajax" style="display: none">
	<div>
		<?php do_action( 'geotr/ajax_placeholder' ); ?>
		<?php echo stripslashes( html_entity_decode( $opts['redirect_message'] ) );?>
	</div>
</div>
<style>
	<?php do_action('geotr/ajax_placeholder_styles');?>
	.geotr-ajax {
		position: fixed;
		width: 100%;
		height: 100%;
		background: #fff;
		top: 0;
		left: 0;
		z-index: 9999999999;
		color: #000;
		display: flex;
		align-items: center;
		justify-content: center;
	}

	.geotr-ajax img {
		display: block;
		margin: auto;
	}

	.geotr-ajax div {
		margin: 20px;
		font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
	}
</style>
	<?php
	}

	public function init_geotWP() {
		geotWP();
	}

	public function handle_redirects() {

		GeotWP_R_ules::init();
		$this->redirections = geotWPR_redirections();
		$opts_geot          = geot_settings();
		if ( ! empty( $opts_geot['ajax_mode'] ) ) {
			add_action( 'wp_footer', [ $this, 'ajax_placeholder' ] );
		} else {
			$this->check_for_rules();
		}
	}

	/**
	 * Check for rules and redirect if needed
	 * This will be normal behaviour on site where cache is not active
	 */
	private function check_for_rules() {
		if ( ! empty( $this->redirections ) ) {
			foreach ( $this->redirections as $r ) {
				if ( ! $this->pass_basic_rules( $r ) ) {
					continue;
				}

				$rules       = ! empty( $r->geotr_rules ) ? unserialize( $r->geotr_rules ) : [];
				$do_redirect = GeotWP_R_ules::is_ok( $rules );
				if ( $do_redirect ) {
					return $this->perform_redirect( $r );
					break; // ajax mode won't redirect instantly so we need to break
				}
			}
		}

		return false;
	}

	/**
	 * Before Even checking rules, we need some basic validation
	 *
	 * @param $redirection
	 *
	 * @return bool
	 */
	private function pass_basic_rules( $redirection ) {
		if ( empty( $redirection->geotr_options ) ) {
			return false;
		}

		$opts = maybe_unserialize( $redirection->geotr_options );

		$current_url = \GeotCore\get_current_url();

		// check for destination url
		if ( empty( $opts['url'] ) || rtrim( $current_url, '/' ) == rtrim( $this->replaceShortcodes( $opts, true ), '/' ) ) {
			return false;
		}

		// redirect once
		if ( (int) $opts['one_time_redirect'] === 1 ) {
			if ( isset( $_COOKIE[ 'geotr_redirect_' . $redirection->ID ] ) ) {
				return false;
			}
		}
		// redirect 1 per session
		if ( (int) $opts['one_time_redirect'] === 2 ) {
			$session = geotWP()->getSession();
			if ( ! empty( $session->get( 'geotr_redirect_' . $redirection->ID ) ) ) {
				return false;
			}
		}

		// check for child page
		if ( isset( $opts['exclude_child'] ) && 1 === absint( $opts['exclude_child'] ) ) {
			$temp_opts        = $opts;
			$temp_opts['url'] = rtrim( str_replace( '{{requested_path}}', '', $temp_opts['url'] ), '/' );
			// if destination url it's included in the current, means we are in a child. Add / to be sure its child
			if ( strpos( $current_url, $this->replaceShortcodes( $temp_opts, true ) . '/' ) !== false ) {
				return false;
			}
		}

		// check for crawlers
		if ( isset( $opts['exclude_se'] ) && 1 === absint( $opts['exclude_se'] ) ) {
			$detect = new CrawlerDetect();
			if ( $detect->isCrawler() ) {
				return false;
			}
		}

		// dont redirect on rest
		if ( is_rest_request() ) {
			return false;
		}

		// check user IP
		if ( ! empty( $opts['whitelist'] ) && $this->user_is_whitelisted( $opts['whitelist'] ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Replace shortcodes on url
	 *
	 * @param $opts
	 *
	 * @param bool $basic_rules When calling this func from basic rules we don't need to execute geolocation or will consume extra credits
	 *
	 * @return mixed
	 */
	private function replaceShortcodes( $opts, $basic_rules = false ) {
		$url = defined( 'DOING_AJAX' ) && isset( $_REQUEST['url'] ) ? $_REQUEST['url'] : get_current_url();

		// remove query string from URL
		$query_string = parse_url( $url, PHP_URL_QUERY );
		$url          = str_replace( '?' . $query_string, '', $url );

		$replaces                  = [
			'{{requested_uri}}'  => trim( $url, '/' ) ?: '',
			'{{requested_path}}' => trim( parse_url( $url, PHP_URL_PATH ), '/' ) ?: '',
			'{{last_path}}'      => trim( parse_url( $url, PHP_URL_PATH ), '/' ) ?: '',
		];
		$path                      = explode( '/', $replaces['{{requested_path}}'] );
		$replaces['{{last_path}}'] = is_array( $path ) ? array_values( array_slice( $path, - 1 ) )[0] : $replaces['{{requested_path}}'];

		if ( ! $basic_rules ) {
			$replaces['{{country_code}}'] = geot_country_code();
			$replaces['{{state_code}}']   = geot_state_code();
			$replaces['{{zip}}']          = geot_zip();
		}

		// remove country codes from urls automatically to avoid /au/au
		if ( isset( $opts['remove_iso'] ) && 1 === absint( $opts['remove_iso'] ) ) {
			if ( strlen( $path[0] ) === 2 ) {
				$replaces['{{requested_path}}'] = substr( $replaces['{{requested_path}}'], 3 );
			}
		}
		if ( ! empty( apply_filters( 'geotr/remove_from_path', [] ) ) ) {
			$replaces['{{requested_path}}'] = str_replace( apply_filters( 'geotr/remove_from_path', [] ), '', $replaces['{{requested_path}}'] );
		}


		// do the replaces
		$replaces = apply_filters( 'geotr/placeholders', array_map( 'strtolower', $replaces ) );

		$final_url = str_replace( array_keys( $replaces ), array_values( $replaces ), $opts['url'] );

		// if wpml active and language code
		if( ! empty( $opts['wpml'] ) ) {
			// only run for WPML, polylang wpml api is not working
			if( ! function_exists('PLL') ) {
				$final_url = apply_filters( 'wpml_permalink', rtrim( $final_url, '/' ) . '/', $opts['wpml'], apply_filters( 'geot/wpml_permalink/full_resolution_mode', 0 ) );
			} else {
				$links =  PLL()->links_model;
				$path = $links->remove_language_from_link( parse_url( $final_url, PHP_URL_PATH ) );

				// check if exists the destination url in different post types
				if ( ( $p = $this->page_exists( $path ) ) != false ) {
					$tr_id = $links->model->post->get( $p->ID, $opts['wpml'] );
					$final_url = get_permalink( $tr_id );
				}
			}
		}

		// add back query string
		if ( isset( $opts['pass_query_string'] ) && $opts['pass_query_string'] == 1 && ! empty( $query_string ) ) {
			// check if a query string already exist in final url
			if ( strpos( $final_url, '?' ) !== false ) {
				return $final_url . '&' . $query_string;
			} else {
				return $final_url . '?' . $query_string;
			}
		}

		return apply_filters( 'geotr/shortcodes_url', $final_url, $opts, $url );
	}

	/**
	 * Check if current user IP is whitelisted
	 *
	 * @param $ips
	 *
	 * @return bool
	 */
	private function user_is_whitelisted( $ips ) {
		$ips = textarea_to_array( $ips );

		if ( in_array( apply_filters( 'geot/user_ip', getUserIP() ), apply_filters( 'geotr/whitelist_ips', $ips ) ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Perform the actual redirection
	 *
	 * @param $redirection
	 */
	private function perform_redirect( $redirection ) {
		$opts = maybe_unserialize( $redirection->geotr_options );
		// redirect one time uses cookies
		if ( (int) $opts['one_time_redirect'] === 1 ) {
			setcookie( 'geotr_redirect_' . $redirection->ID, true, time() + apply_filters( 'geotr/cookie_expiration', YEAR_IN_SECONDS ), '/' );
		}

		// redirect 1 per session
		if ( (int) $opts['one_time_redirect'] === 2 ) {
			$session = geotWP()->getSession();
			$session->set( 'geotr_redirect_' . $redirection->ID, true );
		}

		// status code is set?
		if ( ! isset( $opts['status'] ) || ! is_numeric( $opts['status'] ) ) {
			$opts['status'] = 302;
		}
		$old_url     = $opts['url'];
		$opts['url'] = $this->replaceShortcodes( $opts );
		$opts['url'] = $this->fixRedirect( $opts['url'] );
		$opts['id']  = $redirection->ID;

		// do one more test to check if url exist only when dynamic shortcodes are used
		if ( ( strpos( $old_url, '{{' ) !== false && ! is_multisite() ) && ! apply_filters('geot/disable_page_exists_check', true ) ) {
			$path = parse_url( $opts['url'], PHP_URL_PATH );
			// check if exists the destination url in different post types
			if ( ! $this->page_exists( $path ) ) {
				return false;
			}
		}
		//last chance to abort
		if ( ! apply_filters( 'geotr/cancel_redirect', false, $opts, $redirection ) ) {
			if ( $this->ajax_call === true ) {
				return $opts;
			} else {
				wp_redirect( apply_filters( 'geotr/final_url', $opts['url'] ), $opts['status'] );
				exit;
			}
		}
	}

	/**
	 *    Verify if the URL has protocol
	 */
	public function fixRedirect( $redirect ) {

		$site = preg_replace( '#^https?://#', '', site_url() );

		$site_scheme     = parse_url( site_url(), PHP_URL_SCHEME );
		$redirect_scheme = parse_url( $redirect, PHP_URL_SCHEME );

		if ( strpos( $redirect, $site ) !== false && $site_scheme != $redirect_scheme ) { //internal URL
			$redirect = str_replace( $redirect_scheme, $site_scheme, $redirect );
		}

		return $redirect;
	}

	/**
	 * Handle Ajax call for redirections, Basically
	 * we call normal redirect logic but cancel it and print results
	 */
	public function handle_ajax_redirects() {
		GeotWP_R_ules::init();
		$this->ajax_call    = true;
		$this->redirections = geotWPR_redirections();

		return $this->check_for_rules();
		die();
	}

	/**
	 * Check if page exists
	 *
	 * @param $path
	 *
	 * @return bool
	 */
	private function page_exists( $path ) {
		$post_types = apply_filters( 'geot/get_page_by_path_post_types', [ 'page', 'post' ] );
		foreach ( $post_types as $pt ) {
			$p = get_page_by_path( $path, OBJECT, $pt );
			if( $p !== null ) {
				return $p;
			}
		}
		return false;
	}

}