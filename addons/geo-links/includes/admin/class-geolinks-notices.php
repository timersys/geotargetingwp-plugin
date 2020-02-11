<?php

class GeotWP_Links_Notices {
	/**
	 * GeotWP_Links_Notices constructor.
	 */
	public function __construct() {
		add_action( 'admin_notices', [ $this, 'admin_notices' ] );
		add_action( 'wp_ajax_dismiss_geot_notice', [ $this, 'dismiss_notices' ], 1 );
	}

	/**
	 * Dismiss notices captured in ajax
	 */
	public function dismiss_notices() {
		if ( ! isset( $_GET['notice'] ) ) {
			return;
		}
		update_option( $_GET['notice'], true );
		die();
	}

	/**
	 * Show notice if cache plugin exists
	 */
	public function admin_notices() {
		if ( \GeotCore\is_caching_plugin_active() && ! get_option( 'geolinks-cache' ) ) {
			include_once GEOTWP_L_PLUGIN_DIR . 'includes/admin/partials/notice_cache.php';
		}
	}
}

new GeotWP_Links_Notices();