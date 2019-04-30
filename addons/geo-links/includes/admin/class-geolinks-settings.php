<?php

/**
 * Class GeoLinks_Settings
 */
class GeoLinks_Settings {
	/**
	 * GeoLinks_Settings constructor.
	 */
	public function __construct() {
		add_filter( 'geot/settings_tabs', [$this, 'add_tab']);
		add_action( 'geot/settings_geo-links_panel', [ $this, 'settings_page'] );
		add_action( 'admin_init', [ $this, 'save_settings' ] );
	}

	/**
	 * Register tab for settings page
	 * @param $tabs
	 *
	 * @return mixed
	 */
	function add_tab( $tabs ){
		$tabs['geo-links'] = ['name' => 'Geo Links'];
		return $tabs;
	}


	/**
	 * Settings page for plugin
	 * @since 1.0.3
	 */
	public function settings_page() {
		$opts 	= geol_settings();
		$return	= 'admin.php?'.http_build_query( $_GET );

		include GEOL_PLUGIN_DIR . 'includes/admin/settings/settings-page.php';
	}

	/**
	 * Save Settings page
	 * @since 1.0.3
	 */
	public function save_settings() {
		if ( isset( $_POST['geol_settings'] ) &&
		     isset( $_POST['geol_nonce'] ) &&
		     wp_verify_nonce( $_POST['geol_nonce'], 'geol_save_settings' )
		) {

			$settings = esc_sql( $_POST['geol_settings'] );
			$redirect = wp_unslash( $_POST['geol_return'] );

			$settings['opt_stats'] = isset($settings['opt_stats']) ? $settings['opt_stats'] : 0;

			update_option( 'geol_settings', $settings );

			GeoLinks_Permalinks::set_flush_needed();

			// We redirected to the same place to refresh permalinks
			wp_redirect(admin_url($redirect));
			exit();
		}
	}
}