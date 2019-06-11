<?php

/**
 * Class GeoLinks_Settings
 */
class Geobl_Settings {
	/**
	 * GeoLinks_Settings constructor.
	 */
	public function __construct() {
		add_action( 'geot/settings_partial/after', [ $this, 'settings_page' ] );
	}


	/**
	 * Settings page for plugin
	 * @since 1.0.3
	 */
	public function settings_page() {

		include GEOBL_PLUGIN_DIR . '/admin/partials/settings-page.php';
	}
}