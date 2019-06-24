<?php

/**
 * Responsible for flushing the permalinks when needed.
 *
 * @link       https://timersys.com
 * @since      1.0.1
 *
 * @package    GeotWP_Links
 * @subpackage GeotWP_Links/Permalink
 */
class GeotWP_Links_Permalinks {

	/**
	 * Register actions and filters.
	 *
	 * @since    2.0.1
	 */
	public static function init() {
		add_action( 'admin_init', [ __CLASS__, 'check_if_flush_needed' ] );
	}

	/**
	 * Check if a flushing of the permalinks is needed.
	 *
	 * @since    2.0.1
	 */
	public static function check_if_flush_needed() {

		if ( '1' === get_option( 'geol_flush', '1' ) ) {
			flush_rewrite_rules();
			update_option( 'geol_flush', '0' );
		}
	}

	/**
	 * Set that a flush is needed.
	 *
	 * @since    2.0.1
	 */
	public static function set_flush_needed() {
		update_option( 'geol_flush', '1' );
	}
}

GeotWP_Links_Permalinks::init();