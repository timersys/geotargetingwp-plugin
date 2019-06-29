<?php

namespace GeotCore\Notification;

class GeotNotifications {
	private static $msg;

	/**
	 * Display front end notice to admin
	 *
	 * @param $msg
	 */
	public static function notify( $msg ) {
		self::$msg = $msg;

		add_action( 'wp_footer', [ self::class, 'print_message' ], 999 );
		if ( isset( $_GET['page'] ) && 'geot-settings' == $_GET['page'] ) {
			add_action( 'admin_footer', [ self::class, 'print_message' ], 999 );
		}
	}

	public static function print_message() {
		$error = json_decode( self::$msg );
		if ( ! current_user_can( 'administrator' ) || ! isset( $error->error ) ) {
			return;
		}
		echo '<div class="geot-alert">
						GeotargetingWP Error: ' . esc_attr( $error->error ) . '<br/>
						<small>(This message it\'s only visible to admins)</small></div>';
		self::add_style();
	}

	public static function add_style() {
		?>
		<style type="text/css">
			.geot-alert {
				position: fixed;
				bottom: 0;
				z-index: 999999999;
				background: red;
				width: 100%;
				color: #fff;
				padding: 20px;
				font-size: 12px;
			}</style><?php
	}
}