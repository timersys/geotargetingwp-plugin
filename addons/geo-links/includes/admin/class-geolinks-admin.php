<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://timersys.com
 * @since      1.0.0
 *
 * @package    GeotWP_Links
 * @subpackage GeotWP_Links/admin
 */

use GeotCore\GeotUpdates;

/**
 * @subpackage GeotWP_Links/admin
 * @author     Damian Logghe <damian@timersys.com>
 */
class GeotWP_Links_Admin {

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );

		add_action( 'admin_init', [ $this, 'tinymce_init' ] );
		add_action( 'in_admin_footer', [ $this, 'add_editor' ], 100 );
		add_action( 'admin_head', [ $this, 'tinymce_varjs' ] );
	}


	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		global $pagenow, $post;

		if ( get_post_type() !== 'geol_cpt' || ! in_array( $pagenow, [ 'post-new.php', 'post.php' ] ) ) {
			return;
		}

		wp_enqueue_script( 'geol-admin-js', plugin_dir_url( __FILE__ ) . 'js/geol-admin.js', [ 'jquery' ], GEOTWP_L_VERSION, false );

		wp_enqueue_style( 'geol-admin-css', GEOTWP_L_PLUGIN_URL . 'includes/admin/css/geol-admin.css', [], GEOTWP_L_VERSION, 'all' );

		$geowp   = geot_settings();
		$regions = ! empty( $geowp['region'] ) ? $geowp['region'] : [];

		$list_countries = geotWPL_format_selectize( geot_countries(), 'countries' );
		$list_regions   = geotWPL_format_selectize( $regions, 'regions' );

		wp_localize_script( 'geol-admin-js', 'geol_var',
			[
				'ajax_url'  => admin_url( 'admin-ajax.php' ),
				'nonce'     => wp_create_nonce( 'geol_nonce' ),
				'post_id'   => $post->ID,
				'countries' => $list_countries,
				'regions'   => $list_regions,
				'msg_fail'  => __( 'Failed', 'geot' ),
				'msg_ok'    => __( 'Ok', 'geot' ),
				'icon_load' => '<img src="' . admin_url( 'images/loading.gif' ) . '" />',
			]
		);
	}


	/**
	 * Tinymce init
	 *
	 * @return   hooks
	 * @since    1.0.0
	 */
	public function tinymce_init() {
		//If the user can not see the TinyMCE please stop early
		if ( ! current_user_can( 'edit_posts' ) &&
		     ! current_user_can( 'edit_pages' ) &&
		     get_user_option( 'rich_editing' ) == 'true'
		) {
			return;
		}

		//Add a callback request to register the tinymce plugin hook
		add_filter( 'mce_external_plugins', [ $this, 'tinymce_external' ] );

		//Add a callback request to add the button to the TinyMCE toolbar hook
		add_filter( 'mce_buttons', [ $this, 'tinymce_buttons' ], 12 );
	}

	/**
	 * Tinymce call JS
	 *
	 * @return   array
	 * @since    1.0.0
	 */
	public function tinymce_external( $plugin_array ) {

		//set custom js url path
		$plugin_array['geo_link'] = GEOTWP_L_PLUGIN_URL . 'includes/admin/js/geol-tinymce.js';

		return $plugin_array;
	}

	/**
	 * Tinymce add button
	 *
	 * @return   array
	 * @since    1.0.0
	 */
	public function tinymce_buttons( $buttons ) {

		//Set the custom button identifier to the $buttons array
		$buttons[] = 'geo_link';

		return $buttons;
	}

	/**
	 * Tinymce add global variable to JS
	 *
	 * @return   array
	 * @since    1.0.0
	 */
	public function tinymce_varjs() {
		include GEOTWP_L_PLUGIN_DIR . 'includes/admin/partials/tinymce_varjs.php';
	}

	/**
	 * Tinymce get links from DB to the popup
	 *
	 * @return   array
	 * @since    1.0.0
	 */
	public function add_editor() {
		global $wpdb;

		$query = 'SELECT
					p.post_title AS geol_title,
					m.meta_value AS geol_options
				FROM
					' . $wpdb->posts . ' AS p
				LEFT JOIN
					' . $wpdb->postmeta . ' AS m
				ON
					p.ID = m.post_id
				WHERE
					p.post_status = "publish" &&
					p.post_type = "geol_cpt" &&
					m.meta_key = "geol_options"';

		$geol_results = $wpdb->get_results( $query );

		include GEOTWP_L_PLUGIN_DIR . 'includes/admin/partials/tinymce_popup.php';
	}
}

new GeotWP_Links_Admin();