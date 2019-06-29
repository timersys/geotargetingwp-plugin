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

use GeotCore\Session\GeotSession;
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
 * @var Array of Redirection posts
 */
private $redirections;

public function __construct() {
	add_action( 'plugins_loaded', [ $this, 'init_geot' ], - 2 );

	$action_hook = defined( 'WP_CACHE' ) ? 'init' : 'wp';

	if ( ! is_admin() && ! $this->is_backend() && ! defined( 'DOING_AJAX' ) && ! defined( 'DOING_CRON' ) ) {
		add_action( apply_filters( 'geotr/action_hook', $action_hook ), [ $this, 'handle_redirects' ] );
	}

	add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
	add_action( 'wp_ajax_nopriv_geo_redirects', [ $this, 'handle_ajax_redirects' ], 1 );
	add_action( 'wp_ajax_geo_redirects', [ $this, 'handle_ajax_redirects' ], 1 );
}


/**
 * Check if we are trying to login
 * @return bool
 */
private function is_backend() {
	$ABSPATH_MY = str_replace( [ '\\', '/' ], DIRECTORY_SEPARATOR, ABSPATH );

	return ( ( in_array( $ABSPATH_MY . 'wp-login.php', get_included_files() ) || in_array( $ABSPATH_MY . 'wp-register.php', get_included_files() ) ) || $GLOBALS['pagenow'] === 'wp-login.php' || $_SERVER['PHP_SELF'] == '/wp-login.php' );
}


// Call geot once to init session handling
// otherwise it will fail with georedirects and cache mode turned on

/**
 * Print placeholder in front end
 */
public static function ajax_placeholder(){
?><!-- Geo Redirects plugin https://geotargetingwp.com-->
<div class="geotr-ajax" style="display: none">
	<div>
		<?php do_action( 'geotr/ajax_placeholder' ); ?>
		<img src="<?php echo plugin_dir_url( __FILE__ ); ?>img/loading.svg" alt="loading"/>
		<p><?php _e( 'Please wait while you are redirected to the right page...', 'geotr' ); ?></p>
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
	}

	.geotr-ajax img {
		display: block;
		margin: auto;
	}

	.geotr-ajax div {
		position: absolute;
		top: 0;
		bottom: 0;
		left: 0;
		right: 0;
		margin: auto;
		width: 320px;
		height: 140px;
		font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
		text-align: center;
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
				$this->perform_redirect( $r );
				break; // ajax mode won't redirect instantly so we need to break
			}
		}
	}
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
	if ( empty( $opts['url'] ) || $current_url == $this->replaceShortcodes( $opts, true ) ) {
		return false;
	}

	// check for crawlers
	//if( isset($opts['exclude_se']) && '1' === $opts['exclude_se'] ) {
	if ( isset( $opts['exclude_se'] ) && '1' === absint( $opts['exclude_se'] ) ) {
		$detect = new CrawlerDetect();
		if ( $detect->isCrawler() ) {
			return false;
		}
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
	$url = defined( 'DOING_AJAX' ) && isset( $_REQUEST['url'] ) ? $_REQUEST['url'] : ( ( is_ssl() ? "https" : "http" ) . "://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}" );

	// remove query string from URL
	$query_string = parse_url( $url, PHP_URL_QUERY );
	$url          = str_replace( '?' . $query_string, '', $url );

	$replaces = [
		'{{requested_uri}}'  => trim( $url, '/' ) ?: '',
		'{{requested_path}}' => trim( parse_url( $url, PHP_URL_PATH ), '/' ) ?: '',
	];

	if ( ! $basic_rules ) {
		$replaces['{{country_code}}'] = geot_country_code();
		$replaces['{{state_code}}']   = geot_state_code();
		$replaces['{{zip}}']          = geot_zip();
	}

	// do the replaces
	$replaces  = apply_filters( 'geotr/placeholders', array_map( 'strtolower', $replaces ) );
	$final_url = str_replace( array_keys( $replaces ), array_values( $replaces ), $opts['url'] );
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
		if ( isset( $_COOKIE[ 'geotr_redirect_' . $redirection->ID ] ) ) {
			return;
		}
		setcookie( 'geotr_redirect_' . $redirection->ID, true, time() + apply_filters( 'geotr/cookie_expiration', YEAR_IN_SECONDS ), '/' );
	}

	// redirect 1 per session
	if ( (int) $opts['one_time_redirect'] === 2 ) {
		$session = geotWP()->getSession();

		if ( ! empty( $session->get( 'geotr_redirect_' . $redirection->ID ) ) ) {
			return;
		}
		$session->set( 'geotr_redirect_' . $redirection->ID, true );
	}

	// status code is set?
	if ( ! isset( $opts['status'] ) || ! is_numeric( $opts['status'] ) ) {
		$opts['status'] = 302;
	}

	$opts['url'] = $this->replaceShortcodes( $opts );
	$opts['url'] = $this->fixRedirect( $opts['url'] );

	//last chance to abort
	if ( ! apply_filters( 'geotr/cancel_redirect', false, $opts, $redirection ) ) {
		wp_redirect( apply_filters( 'geotr/final_url', $opts['url'] ), $opts['status'] );
		exit;
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
	} elseif ( empty( $redirect_scheme ) ) { //external URL without scheme
		$redirect = 'http://' . $redirect;
	}

	return $redirect;
}

/**
 * Handle Ajax call for redirections, Basically
 * we call normal redirect logic but cancel it and print results
 */
public function handle_ajax_redirects() {
	GeotWP_R_ules::init();
	$this->redirections = geotWPR_redirections();
	add_filter( 'geotr/cancel_redirect', function ( $redirect, $opts ) {
		echo apply_filters( 'geotr/ajax_cancel_redirect', json_encode( $opts ), $opts );

		return true;
	}, 15, 3 );
	$this->check_for_rules();
	die();
}

/**
 * Enqueue script file
 */
public function enqueue_scripts() {
	wp_enqueue_script( 'geotr-js', plugins_url( 'js/geotr-public.js', __FILE__ ), [ 'jquery' ], GEOTWP_R_VERSION, true );
	wp_localize_script( 'geotr-js', 'geotr', [
		'ajax_url'      => admin_url( 'admin-ajax.php' ),
		'pid'           => get_queried_object_id(),
		'is_front_page' => is_front_page(),
		'is_category'   => is_category(),
		'site_url'      => site_url(),
		'is_archive'    => is_archive(),
		'is_search'     => is_search(),
	] );
}

}