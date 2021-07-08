<?php

namespace GeotCore\Email;

/**
 * Class GeotEmails
 * @package GeotCore\Email
 */
class GeotEmails {

	/**
	 * Send email every two hours when user run out of queries in maxmind
	 */
	public static function OutOfQueriesException() {
		if ( false === get_transient( 'geot_OutOfQueriesException' ) ) {
			set_transient( 'geot_OutOfQueriesException', true, 4 * 3600 );
			$message = sprintf( __( 'Your <a href="%s">GeotargetingWP account</a> have run out of requests. Please <a href="%s">renew your billing cycle</a> to continue using this plugin.', 'geot' ), 'https://geotargetingwp.com/dashboard/stats', 'https://geotargetingwp.com/dashboard/stats' );
			$subject = __( 'Geotargeting plugin Error!', 'geot' );
			$headers = [ 'Content-Type: text/html; charset=UTF-8' ];
			wp_mail( get_bloginfo( 'admin_email' ), $subject, $message, $headers );
		}
	}

	public static function AuthenticationException($msg = "") {
		if ( false === get_transient( 'geot_AuthenticationException' ) ) {
			$args = geot_settings();
			$host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : gethostname();
			set_transient( 'geot_AuthenticationException', true, DAY_IN_SECONDS );
			$message = '<p>'. sprintf( __( 'Your <a href="%s">GeotargetingWP</a> license is wrong (%s). Please enter correct one to continue using the plugin on %s.', 'geot' ),
				'https://geotargetingwp.com/dashboard/',
				$args['license'],
				$host
			) . '</p><p>'.esc_attr($msg).'</p>';
			$subject = __( 'Geotargeting plugin Error!', 'geot' );
			$headers = [ 'Content-Type: text/html; charset=UTF-8' ];
			wp_mail( get_bloginfo( 'admin_email' ), $subject, $message, $headers );
		}
	}

	public static function InvalidSubscriptionException( $getMessage ) {
		if ( false === get_transient( 'geot_InvalidSubscriptionException' ) ) {
			$host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : gethostname();
			set_transient( 'geot_InvalidSubscriptionException', true, DAY_IN_SECONDS );
			$message = sprintf( __( 'Your <a href="%s">GeotargetingWP</a> subscription is not active in %s. Api returned following error: %s.', 'geot' ),
				'https://geotargetingwp.com/dashboard/',
				$host,
				$getMessage
			);
			$subject = __( 'Geotargeting plugin Error!', 'geot' );
			$headers = [ 'Content-Type: text/html; charset=UTF-8' ];
			wp_mail( get_bloginfo( 'admin_email' ), $subject, $message, $headers );
		}
	}


}