<?php

/**
 * Class GeotWP_Links_Settings
 */
class GeotWP_Bl_Settings {
	/**
	 * GeotWP_Links_Settings constructor.
	 */
	public function __construct() {
		// no settings page
		//add_action( 'geot/settings_partial/after', [ $this, 'settings_page' ] );
	}


	/**
	 * Settings page for plugin
	 * @since 1.0.3
	 */
	public function settings_page() {

		include GEOTWP_BL_PLUGIN_DIR . '/admin/partials/settings-page.php';
	}
}