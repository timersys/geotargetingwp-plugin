<?php

/**
 * The cpt metaboxes functionality of the plugin.
 *
 * @link       https://timersys.com
 * @since      1.0.0
 *
 * @package    Geotr
 * @subpackage Geotr/admin/includes
 */

/**
 * @subpackage Geotr/admin/includes
 * @author     Damian Logghe <damian@timersys.com>
 */
class GeotWP_R_Metaboxes {

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param string $plugin_name The name of this plugin.
	 * @param string $version The version of this plugin.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		add_action( 'add_meta_boxes_geotr_cpt', [ $this, 'add_meta_boxes' ] );
		add_action( 'save_post_geotr_cpt', [ $this, 'save_meta_options' ] );

		add_filter( 'geot/rules/post_types', [ $this, 'rules_js_script' ], 10, 1 );
	}


	/**
	 * Register the metaboxes for our cpt
	 * @return   void
	 * @since    1.0.0
	 */
	public function add_meta_boxes() {
		add_meta_box(
			'geotr-rules',
			__( 'Redirection Rules', 'geot' ),
			[ $this, 'geotr_rules' ],
			'geotr_cpt',
			'normal',
			'core'
		);
		add_meta_box(
			'geotr-opts',
			__( 'Redirection Options', 'geot' ),
			[ $this, 'geotr_opts' ],
			'geotr_cpt',
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
		if ( ! isset( $_POST['geotr_options_nonce'] ) || ! wp_verify_nonce( $_POST['geotr_options_nonce'], 'geotr_options' ) ) {
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

		$opts = $_POST['geotr'];
		unset( $_POST['geotr'] );

		$post = get_post( $post_id );

		// sanitize settings
		$opts['whitelist']         = $opts['whitelist']; // if we sanitize break lines are broken, we sanitize later
		$opts['url']               = sanitize_text_field( $opts['url'] );
		$opts['exclude_se']        = absint( sanitize_text_field( $opts['exclude_se'] ) );
		$opts['exclude_child']     = absint( sanitize_text_field( $opts['exclude_child'] ) );
		$opts['remove_iso']        = absint( sanitize_text_field( $opts['remove_iso'] ) );
		$opts['one_time_redirect'] = absint( sanitize_text_field( $opts['one_time_redirect'] ) );
		$opts['status']            = absint( sanitize_text_field( $opts['status'] ) );

		// save box settings
		update_post_meta( $post_id, 'geotr_options', apply_filters( 'geotr/metaboxes/sanitized_options', $opts ) );

		// Start with rules
		GeotWP_Helper::save_rules( $post_id, $_POST, 'geotr_rules' );
	}

	/**
	 * Register the JavaScript for the admin area.
	 * @since    1.0.0
	 */
	public function rules_js_script( $post_types ) {

		$post_types[] = 'geotr_cpt';

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
	public function geotr_rules( $post, $metabox ) {

		$args = [
			'title' => __( "Perform redirect if", 'geot' ),
			'desc'  => __( "Create a set of rules to determine where the redirect will be performed", 'geot' ),
		];

		GeotWP_Helper::html_rules( $post, 'geotr_rules', $args );

		//$groups = apply_filters('geotr/metaboxes/get_rules', GeotWP_Helper::get_rules( $post->ID, 'geotr_rules' ), $post->ID);


		//include GEOTWP_R_PLUGIN_DIR . '/admin/partials/metaboxes/rules.php';
	}

	/**
	 * Include the metabox view for opts
	 *
	 * @param object $post geotrcpt post object
	 * @param array $metabox full metabox items array
	 *
	 * @since 1.0.0
	 */
	public function geotr_opts( $post, $metabox ) {

		$opts = apply_filters( 'geotr/metaboxes/get_options', GeotWP_R_Helper::get_options( $post->ID ), $post->ID );

		include GEOTWP_R_PLUGIN_DIR . '/admin/partials/metaboxes/opts.php';
	}

}
