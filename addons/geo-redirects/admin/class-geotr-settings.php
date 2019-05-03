<?php
/**
 * Class Geotr_Settings
 */
class Geotr_Settings {
	/**
	 * Geotr_Settings constructor.
	 */
	public function __construct() {
		add_action( 'geot/settings_partial/after', [$this, 'settings_page']);
	}


	/**
	 * Settings page for plugin
	 * @since 1.0.3
	 */
	public function settings_page() {

		include GEOTR_PLUGIN_DIR . '/admin/partials/settings-page.php';
	}
}