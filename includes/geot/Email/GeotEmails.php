<?php

namespace GeotFunctions\Email;

/**
 * Class GeotEmails
 * @package GeotFunctions\Email
 */
class GeotEmails {

	/**
	 * Send email every two hours when user run out of queries in maxmind
	 */
	public static function OutOfQueriesException() {
		if ( false === get_transient( 'geot_OutOfQueriesException' ) ) {
			set_transient( 'geot_OutOfQueriesException', true, 2 * 3600 );
			$message = sprintf( __( 'Your <a href="%s">GeotargetingWP account</a> have run out of queries. Please <a href="%s">add some</a> more to continue using this plugin.', 'geot' ), 'https://geotargetingwp.com/dashboard/credits', 'https://geotargetingwp.com/dashboard/credits' );
			$subject = __( 'Geotargeting plugin Error!', 'geot' );
			$headers = [ 'Content-Type: text/html; charset=UTF-8' ];
			wp_mail( get_bloginfo( 'admin_email' ), $subject, $message, $headers );
		}
	}

	public static function AuthenticationException() {
		if ( false === get_transient( 'geot_AuthenticationException' ) ) {
			set_transient( 'geot_AuthenticationException', true, 2 * 3600 );
			$message = sprintf( __( 'Your <a href="%s">GeotargetingWP</a> license is wrong. Please enter correct one to continue using the plugin.', 'geot' ), 'https://geotargetingwp.com/dashboard/' );
			$subject = __( 'Geotargeting plugin Error!', 'geot' );
			$headers = [ 'Content-Type: text/html; charset=UTF-8' ];
			wp_mail( get_bloginfo( 'admin_email' ), $subject, $message, $headers );
		}
	}

	public static function InvalidSubscriptionException( $getMessage ) {
		if ( false === get_transient( 'geot_InvalidSubscriptionException' ) ) {
			set_transient( 'geot_InvalidSubscriptionException', true, 2 * 3600 );
			$message = sprintf( __( 'Your <a href="%s">GeotargetingWP</a> subscription is not active. Api returned following error: %s.', 'geot' ), 'https://geotargetingwp.com/dashboard/', $getMessage );
			$subject = __( 'Geotargeting plugin Error!', 'geot' );
			$headers = [ 'Content-Type: text/html; charset=UTF-8' ];
			wp_mail( get_bloginfo( 'admin_email' ), $subject, $message, $headers );
		}
	}


}