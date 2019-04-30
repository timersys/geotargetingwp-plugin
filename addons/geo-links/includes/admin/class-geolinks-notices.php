<?php
class GeoLinks_Notices{
	/**
	 * GeoLinks_Notices constructor.
	 */
	public function __construct() {
		add_action( 'admin_notices', [ $this , 'admin_notices'] );
		add_action( 'wp_ajax_dismiss_geot_notice', [ $this, 'dismiss_notices'] );
	}

	/**
	 * Dismiss notices captured in ajax
	 */
	public function dismiss_notices(){
		if( !isset($_GET['notice']) ) {
			return;
		}
		update_option($_GET['notice'], true);
		die();
	}

	/**
	 * Show notice if cache plugin exists
	 */
	public function admin_notices() {
		if( \GeotFunctions\is_caching_plugin_active() && ! get_option('geolinks-cache'))
			include_once GEOL_PLUGIN_DIR . 'includes/admin/partials/notice_cache.php';
	}
}
new GeoLinks_Notices();