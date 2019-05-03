<?php

/**
 * Class GeoLinks_Settings
 */
class GeoLinks_Settings {
	/**
	 * GeoLinks_Settings constructor.
	 */
	public function __construct() {
		add_action( 'geot/settings/save', [ $this, 'save_settings' ] );
		add_action( 'geot/settings_partial/after', [ $this, 'settings_page' ], 11, 1);
	}



	/**
	 * Settings page for plugin
	 * @since 1.0.3
	 */
	public function settings_page() {
		$opts 	= geol_settings();

		include GEOL_PLUGIN_DIR . 'includes/admin/settings/settings-page.php';
	}


	/**
	 * Save Settings page
	 * @since 1.0.3
	 */
	public function save_settings() {

		$settings = esc_sql( $_POST['geol_settings'] );
		$settings['opt_stats'] = isset($settings['opt_stats']) ? $settings['opt_stats'] : 0;

		update_option( 'geol_settings', $settings );

		GeoLinks_Permalinks::set_flush_needed();
	}
}