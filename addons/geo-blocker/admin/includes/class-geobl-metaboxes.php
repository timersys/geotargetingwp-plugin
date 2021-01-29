<?php

/**
 * The cpt metaboxes functionality of the plugin.
 *
 * @link       https://timersys.com
 * @since      1.0.0
 *
 * @package    Geobl
 * @subpackage Geobl/admin/includes
 * @author     Damian Logghe <damian@timersys.com>
 */
class GeotWP_Bl_Metaboxes {

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param string $plugin_name The name of this plugin.
	 * @param string $version The version of this plugin.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		add_action( 'add_meta_boxes_geobl_cpt', [ $this, 'add_meta_boxes' ] );
		add_action( 'save_post_geobl_cpt', [ $this, 'save_meta_options' ] );

		add_filter( 'geot/exclude/post_types', [ $this, 'rules_js_script' ], 10, 1 );
	}


	/**
	 * Register the metaboxes for our cpt
	 * @return   void
	 * @since    1.0.0
	 */
	public function add_meta_boxes() {
		add_meta_box(
			'geobl-rules',
			__( 'Block Rules', 'geot' ),
			[ $this, 'geobl_rules' ],
			'geobl_cpt',
			'normal',
			'core'
		);
		add_meta_box(
			'geobl-opts',
			__( 'Block Options', 'geot' ),
			[ $this, 'geobl_opts' ],
			'geobl_cpt',
			'normal',
			'core'
		);
	}

	/**
	 * Saves the post meta of redirections
	 * @since 1.0.0
	 */
	function save_meta_options( $post_id ) {

		// Verify that the nonce is set and valid.
		if ( ! isset( $_POST['geobl_options_nonce'] ) || ! wp_verify_nonce( $_POST['geobl_options_nonce'], 'geobl_options' ) ) {
			return $post_id;
		}
		// If this is an autosave, our form has not been submitted, so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}
		// same for ajax
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return $post_id;
		}
		// same for cron
		if ( defined( 'DOING_CRON' ) && DOING_CRON ) {
			return $post_id;
		}
		// same for posts revisions
		if ( is_int( wp_is_post_autosave( $post_id ) ) ) {
			return $post_id;
		}

		// can user edit this post?
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return $post_id;
		}

		$opts = $_POST['geobl'];
		unset( $_POST['geobl'] );

		$post = get_post( $post_id );

		// sanitize settings
		$opts['whitelist']     = $opts['whitelist']; // if we sanitize break lines are broken, we sanitize later
		$opts['exclude_se']    = absint( sanitize_text_field( $opts['exclude_se'] ) );
		$opts['exclude_child']    = absint( sanitize_text_field( $opts['exclude_child'] ) );
		$opts['remove_iso']    = absint( sanitize_text_field( $opts['remove_iso'] ) );
		$opts['block_message'] = $opts['block_message'];

		// save box settings
		update_post_meta( $post_id, 'geobl_options', apply_filters( 'geobl/metaboxes/sanitized_options', $opts ) );

		// Start with rules
		GeotWP_Helper::save_rules( $post_id, $_POST, 'geobl_rules' );
	}

	/**
	 * Register the JavaScript for the admin area.
	 * @since    1.0.0
	 */
	function rules_js_script( $post_types ) {

		$post_types[] = 'geobl_cpt';

		return $post_types;
	}

	/**
	 * Include the metabox view for rules
	 *
	 * @param object $post spucpt post object
	 * @param array $metabox full metabox items array
	 *
	 * @since 1.0.0
	 */
	public function geobl_rules( $post, $metabox ) {

		$args = [
			'title' => __( "Block user if", 'geot' ),
			'desc'  => __( "Create a set of rules to determine if the user will be blocked", 'geot' ),
		];

		GeotWP_Helper::html_rules( $post, 'geobl_rules', $args );

		//$groups = apply_filters('geobl/metaboxes/get_rules', GeotWP_Helper::get_rules( $post->ID, 'geobl_rules' ), $post->ID);

		//include GEOTWP_BL_PLUGIN_DIR . '/admin/partials/metaboxes/rules.php';
	}

	/**
	 * Include the metabox view for opts
	 *
	 * @param object $post geoblcpt post object
	 * @param array $metabox full metabox items array
	 *
	 * @since 1.0.0
	 */
	public function geobl_opts( $post, $metabox ) {

		$opts = apply_filters( 'geobl/metaboxes/get_options', GeotWP_Bl_Helper::get_options( $post->ID ), $post->ID );

		include GEOTWP_BL_PLUGIN_DIR . '/admin/partials/metaboxes/opts.php';
	}

}
