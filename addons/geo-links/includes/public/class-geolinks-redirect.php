<?php
/**
 * The redirect-facing functionality of the plugin.
 *
 * @link       https://timersys.com
 * @since      1.0.0
 *
 * @package    Geotr
 * @subpackage Geotr/public
 */

/**
 * @package    Geol
 * @subpackage Geol/Redirect
 * @author     Damian Logghe <damian@timersys.com>
 */
class Geol_Redirects {

	/**
	 * The detected mobile or tablet.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string
	 */
	private static $detect;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */

	public function __construct() {
		self::$detect = new Mobile_Detect;

		add_action( 'wp_enqueue_scripts', [ $this, 'public_scripts' ] );
		add_action( 'template_redirect', [ $this, 'redirect_link' ] );
		add_shortcode( 'geo-link', [ $this, 'add_shortcode' ], 10, 2 );
	}

	/**
	 * Visual editors run on front, so this bar needs to go here
	 */
	public function public_scripts(){
		wp_localize_script( 'jquery', 'geol_tinymce',
			[
				'icon' => GEOTWP_L_PLUGIN_URL . 'includes/admin/img/geol_link.png',
			]
		);
	}
	/**
	 * Apply redirect
	 *
	 * @param
	 *
	 * @since 1.0.0
	 */
	public function redirect_link() {

		if ( is_singular( 'geol_cpt' ) ) {

			$post_id  = get_the_id();
			$opts     = geotWPL_options( $post_id );
			$settings = geotWPL_settings();

			$this->count_click( 'click', $post_id );

			// check redirections to see if we have any match
			foreach ( $opts['dest'] as $key => $redirect ) {

				$redirect = apply_filters( 'geol/redirect_params', $redirect, $post_id );
				// if not url in the redirect continue to next rule
				if( empty( $redirect['url'] ) ) {
					continue;
				}
				// validate redirect
				if ( $this->validate_redirection( $redirect ) ) {

					// last change to abort
					if ( apply_filters( 'geol/redirect_cancel', false, $redirect, $post_id ) ) {
						return;
					}

					$this->count_click( 'dest', $post_id, $key );

					wp_redirect( esc_url_raw( $redirect['url'] ), $opts['status_code'] );
					exit();
				}
			}

			if ( isset( $opts['dest_default'] ) && ! empty( $opts['dest_default'] ) ) {
				$url_default = apply_filters( 'geol/redirect_default', esc_url_raw( $opts['dest_default'] ), $post_id );
			} else {
				$url_default = site_url();
			}

			$this->count_click( 'default', $post_id );

			wp_redirect( $url_default, $opts['status_code'] );
			exit();
		}
	}

	function count_click( $field, $post_id, $dest_key = '' ) {

		$opts = geotWPL_options( $post_id );

		switch ( $field ) {
			case 'click' :
				if ( isset( $opts['count_click'] ) && is_numeric( $opts['count_click'] ) ) {
					$opts['count_click'] ++;
				} else {
					$opts['count_click'] = 1;
				}

				break;
			case 'default' :
				if ( isset( $opts['click_default'] ) && is_numeric( $opts['click_default'] ) ) {
					$opts['click_default'] ++;
				} else {
					$opts['click_default'] = 1;
				}

				break;

			case 'dest' :
				if ( isset( $opts['dest'][ $dest_key ]['count_dest'] ) &&
				     is_numeric( $opts['dest'][ $dest_key ]['count_dest'] )
				) {
					$opts['dest'][ $dest_key ]['count_dest'] ++;
				} else {
					$opts['dest'][ $dest_key ]['count_dest'] = 1;
				}

				break;
		}

		// save box settings
		update_post_meta( $post_id, 'geol_options', apply_filters( 'geol/redirect/count_click', $opts ) );

	}

	/**
	 * conditional geo validation
	 *
	 * @param $redirect is cpt values
	 * @param $geo is geot targeting
	 *
	 * @return bool
	 * @since 1.0.0
	 */
	private function validate_redirection( $redirect ) {

		$referrer = isset( $_SERVER['HTTP_REFERER'] ) ? $_SERVER['HTTP_REFERER'] : '';

		// Ref
		if ( ! empty( $dest['ref'] ) && strpos( $referrer, $redirect['ref'] ) === false ) {
			return false;
		}

		//Devices Mobiles
		if ( $redirect['device'] == 'mobiles' && ! self::$detect->isMobile() ) {
			return false;
		}

		//Devices Tablets
		if ( $redirect['device'] == 'tablets' && ! self::$detect->isTablet() ) {
			return false;
		}

		//Devices Desktop
		if ( $redirect['device'] == 'desktop' && (  self::$detect->isTablet() || self::$detect->isMobile() ) ) {
			return false;
		}

		// Country
		if ( ! empty( $redirect['countries'] ) && ! geot_target( $redirect['countries'] ) ) {
			return false;
		}

		// regions
		if ( ! empty( $redirect['regions'] ) && ! geot_target( '', $redirect['regions'] ) ) {
			return false;
		}

		// Cities
		if ( ! empty( $redirect['cities'] ) && ! geot_target_city( $redirect['cities'] ) && ! geot_target_city( '', $redirect['cities'] ) ) {
			return false;
		}

		// States
		if ( ! empty( $redirect['states'] ) && ! geot_target_state( $redirect['states'] ) && ! geot_target_state( '', $redirect['states'] ) ) {
			return false;
		}

		// Zipcodes
		if ( ! empty( $redirect['zipcodes'] ) && ! geot_target_zip( $redirect['zipcodes'] ) && ! geot_target_zip( '', $redirect['zipcodes'] ) ) {
			return false;
		}

		// Zipcodes
		if ( ! empty( $redirect['radius_km'] ) &&
			! empty( $redirect['radius_lat'] ) &&
			! empty( $redirect['radius_lng'] ) &&
			! geot_target_radius( $redirect['radius_lat'], $redirect['radius_lng'], $redirect['radius_km'] ) ) {
			return false;
		}
		
		return true;
	}

	/**
	 * Add Shortcode
	 *
	 * @param
	 *
	 * @param string $content
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public function add_shortcode( $atts, $content = '' ) {
		$atts = shortcode_atts( [
			'slug'       => 'geo-slug',
			'nofollow'   => 'no',
			'noreferrer' => 'no',
		], $atts, 'geo-link' );

		$return = '{Slug is not matching any Geo link}';
		$post   = get_page_by_path( $atts['slug'], OBJECT, 'geol_cpt' );

		if ( isset( $post->ID ) ) {

			$rel      = apply_filters( 'geolinks/link_rel_attr', [ 'noopener' ] );
			$content  = ! empty( $content ) ? $content : 'Geo Link';
			$settings = geotWPL_settings();
			$opts     = geotWPL_options( $post->ID );


			// REL
			if ( $atts['nofollow'] == 'yes' ) {
				$rel[] = 'nofollow';
			}

			if ( $atts['noreferrer'] == 'yes' ) {
				$rel[] = 'noreferrer';
			}

			$attr_rel = count( $rel ) > 0 ? 'rel="' . implode( ' ', $rel ) . '"' : '';


			// Output
			$link = add_query_arg( 'nocache', 'true', trailingslashit( site_url( $settings['goto_page'] ) ) . $opts['source_slug'] );

			$return = '<a href="' . esc_url( $link ) . '" ' . esc_attr( $attr_rel ) . ' >' . do_shortcode( $content ) . '</a>';
		}

		return $return;
	}
}

new Geol_Redirects();