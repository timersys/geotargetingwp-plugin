<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://timersys.com
 * @since      1.0.0
 *
 * @package    GeoLinks
 * @subpackage GeoLinks/admin
 */

use GeotFunctions\GeotUpdates;

/**
 * @subpackage GeoLinks/admin
 * @author     Damian Logghe <damian@timersys.com>
 */
class GeoLinks_Admin {

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->version = GEOL_VERSION;

		add_filter( 'plugin_action_links_' . GEOL_PLUGIN_HOOK, [ $this, 'add_action_links' ] );

		// License and Updates
		add_action( 'admin_init', [ $this, 'handle_updates' ], 0 );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
		
		add_action( 'init', [ $this, 'tinymce_init' ] );
		add_action( 'in_admin_footer', [ $this, 'add_editor' ], 100 );
		add_action( 'admin_head', [ $this, 'tinymce_varjs' ] );
		add_action( 'admin_head', [ $this, 'tinymce_varjs' ] );
	}


	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		global $pagenow, $post;

		if( get_post_type() !== 'geol_cpt' || !in_array( $pagenow, [ 'post-new.php', 'edit.php', 'post.php' ] ) )
			return;

		wp_enqueue_script( 'geol-admin-js', plugin_dir_url( __FILE__ ) . 'js/geol-admin.js', [ 'jquery' ], $this->version, false );

		wp_enqueue_style( 'geol-admin-css', GEOL_PLUGIN_URL . 'includes/admin/css/geol-admin.css', [], $this->version, 'all' );

		$geowp = geot_settings();
		$regions = !empty( $geowp['region'] ) ? $geowp['region'] : array();

		$list_countries = format_selectize(geot_countries(),'countries');
		$list_regions = format_selectize($regions,'regions');

		wp_localize_script( 'geol-admin-js', 'geol_var',
			[
				'ajax_url'	=> admin_url( 'admin-ajax.php' ),
				'nonce'		=> wp_create_nonce( 'geol_nonce' ),
				'post_id'	=> $post->ID,
				'countries'	=> $list_countries,
				'regions'	=> $list_regions,
				'msg_fail'	=> __('Failed','geol'),
				'msg_ok'	=> __('Ok','geol'),
				'icon_load'	=> '<img src="'.admin_url('images/loading.gif').'" />',
			]
		);
	}


	/**
	 * Register direct access link
	 *
	 * @since    1.0.0
	 * @return    Array
	 */
	public function add_action_links( $links ) {

		return array_merge(
			[
				'settings' => '<a href="' . admin_url( 'edit.php?post_type=geol_cpt' ) . '">' . __( 'Create GeoLink', 'geotr' ) . '</a>',
			],
			$links
		);

	}

	/**
	 * Tinymce init
	 *
	 * @since    1.0.0
	 * @return   hooks
	 */
	public function tinymce_init() {
		//If the user can not see the TinyMCE please stop early
		if ( !current_user_can('edit_posts') &&
			!current_user_can('edit_pages') &&
			get_user_option('rich_editing') == 'true'
		) {			
			return;
		}

		//Add a callback request to register the tinymce plugin hook
		add_filter('mce_external_plugins', [ $this, 'tinymce_external' ]);
		
		//Add a callback request to add the button to the TinyMCE toolbar hook
		add_filter('mce_buttons', [ $this, 'tinymce_buttons' ], 12);
	}

	/**
	 * Tinymce call JS
	 *
	 * @since    1.0.0
	 * @return   array
	 */
	public function tinymce_external( $plugin_array ) {

		//set custom js url path
		$plugin_array['geo_link'] = GEOL_PLUGIN_URL . 'includes/admin/js/geol-tinymce.js';
		
		return $plugin_array;
	}

	/**
	 * Tinymce add button
	 *
	 * @since    1.0.0
	 * @return   array
	 */
	public function tinymce_buttons($buttons) {
		
		//Set the custom button identifier to the $buttons array
		$buttons[] = 'geo_link';

		return $buttons;
	}

	/**
	 * Tinymce add global variable to JS
	 *
	 * @since    1.0.0
	 * @return   array
	 */
	public function tinymce_varjs() {
		include GEOL_PLUGIN_DIR . 'includes/admin/partials/tinymce_varjs.php';
	}

	/**
	 * Tinymce get links from DB to the popup
	 *
	 * @since    1.0.0
	 * @return   array
	 */
	public function add_editor() {
		global $wpdb;

		$query = 'SELECT
					p.post_title AS geol_title,
					m.meta_value AS geol_options
				FROM
					'.$wpdb->posts.' AS p
				LEFT JOIN
					'.$wpdb->postmeta.' AS m
				ON
					p.ID = m.post_id
				WHERE
					p.post_status = "publish" &&
					p.post_type = "geol_cpt" &&
					m.meta_key = "geol_options"';

		$geol_results = $wpdb->get_results($query);

		include GEOL_PLUGIN_DIR . 'includes/admin/partials/tinymce_popup.php';
	}


	/**
	 * Handle Licences and updates
	 * @since 1.0.0
	 */
	public function handle_updates() {
		$opts = geot_settings();

		// Setup the updater
		return new GeotUpdates( GEOL_PLUGIN_FILE, [
				'version' => $this->version,
				'license' => isset( $opts['license'] ) ? $opts['license'] : '',
			]
		);
	}
}