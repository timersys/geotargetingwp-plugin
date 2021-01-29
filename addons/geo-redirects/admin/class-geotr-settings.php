<?php

/**
 * Class GeotWP_R_Settings
 */
class GeotWP_R_Settings {
	/**
	 * GeotWP_R_Settings constructor.
	 */
	public function __construct() {
		add_action( 'geot/settings/save', [ $this, 'save_settings' ] );
		add_action( 'geot/settings_partial/after', [ $this, 'settings_page' ], 15, 1 );
	}


	/**
	 * Settings page for plugin
	 * @since 1.0.3
	 */
	public function settings_page() {
		$opts = geotr_settings();
		include GEOTWP_R_PLUGIN_DIR . '/admin/partials/settings-page.php';
	}

	/**
	 * Save Settings page
	 * @since 1.0.3
	 */
	public function save_settings() {

		$settings              = $_POST['geotr'];
		$settings['redirect_message'] = htmlentities(wpautop( $settings['redirect_message'] ) );
		update_option( 'geotr_settings', $settings );

	}

	/**
	 * default redirect message
	 * @return mixed
	 */
	public static function default_message() {
		ob_start();?>
		<img class="aligncenter" src="<?php echo GEOTWP_R_PLUGIN_URL; ?>public/img/loading.svg" alt="loading"/>
		<p style="text-align: center"><?php echo apply_filters( 'geotr/ajax_message', __( 'Please wait while you are redirected to the right page...', 'geot' ) ); ?></p>
		<?php
		$html['redirect_message'] = stripslashes( html_entity_decode(ob_get_clean() ) );

		return $html;
	}
}