<?php

/**
 * The dashboard-specific functionality of the plugin.
 *
 * @link       https://geotargetingwp.com/geotargeting-pro
 * @since      1.0.0
 *
 * @package    GeoTarget
 * @subpackage GeoTarget/admin
 */


/**
 * The dashboard-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the dashboard-specific stylesheet and JavaScript.
 *
 * @package    GeoTarget
 * @subpackage GeoTarget/admin
 * @author     Your Name <email@example.com>
 */
class Geot_Settings {


	public function __construct() {

		// settings page
		add_action( 'admin_init', [ $this, 'save_settings' ] );

		add_filter('geot/settings_tabs', [$this, 'add_tab']);
		add_action('geot/settings_geotargeting-settings_panel', [ $this, 'settings_page'] );
		add_action('geot/settings_geotargeting-addons_panel', [ $this, 'addons_page'] );
	}


	/**
	 * Register tab for settings page
	 * @param $tabs
	 *
	 * @return mixed
	 */
	function add_tab( $tabs ){
		$tabs['geotargeting-settings'] = [ 'name' => __('Settings', 'geot') ];
		$tabs['geotargeting-addons'] = [ 'name' => __('AddOns', 'geot') ];
		return $tabs;
	}

	/**
	 * Render settings page
	 */
	function settings_page(){
		$defaults = [
			'ajax_mode'                 => '0',
			'disable_menu_integration'  => '0',
			'disable_widget_integration'=> '0',
		];
		$opts = geot_pro_settings();
		$opts = wp_parse_args( $opts,  $defaults );

		$return = 'admin.php?'.http_build_query( $_GET );

		include GEOT_PLUGIN_DIR .'admin/partials/settings-page.php';
	}

	/**
	 * Render Addons page
	 */
	function addons_page() {
		$defaults = [
						'geo-flags'		=> '0',
						'geo-links'		=> '0',
						'geo-redirects'	=> '0',
						'geo-blocker'	=> '0',
					];

		$defaults = apply_filters('geot/addons/defaults', $defaults);

		$opts = geot_pro_addons();
		$opts = geot_wp_parse_args( $opts,  $defaults );

		$return = 'admin.php?'.http_build_query( $_GET );

		include GEOT_PLUGIN_DIR .'admin/partials/addons-page.php';
	}

	function save_settings(){
		if (  isset( $_POST['geot_nonce'] ) && wp_verify_nonce( $_POST['geot_nonce'], 'geot_pro_save_settings' ) ) {

			//Settings
			if( isset($_POST['geot_settings']) ) {
				$settings = isset($_POST['geot_settings']) ? esc_sql( $_POST['geot_settings'] ) : '';
				update_option( 'geot_pro_settings' ,  $settings);
			}

			//addons
			if( isset($_POST['geot_addons']) ) {
				$settings = isset($_POST['geot_addons']) ? esc_sql( $_POST['geot_addons'] ) : '';
				update_option( 'geot_pro_addons' ,  $settings);
			}


			do_action('geot/settings/save', $_POST);

			if( isset($_POST['geot_return']) ) {
				wp_redirect(esc_url_raw(admin_url($_POST['geot_return'])));
				exit();
			}
		}
	}
}