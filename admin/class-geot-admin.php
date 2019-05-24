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
class Geot_Admin {

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @var      string    $GeoTarget       The name of this plugin.
	 * @var      string    $version    The version of this plugin.
	 */
	public function __construct() {

		add_action( 'admin_init', [ $this, 'register_tiny_buttons' ] );
		add_action( 'wp_ajax_geot_get_popup', [ $this, 'add_editor' ] );

		// register dropdown widget
		add_action( 'widgets_init', [ $this, 'register_widgets' ] );

		// Add geot to Advanced custom fields plugin
		add_action( 'acf/include_field_types', [ $this, 'add_geot_to_acfv5' ] );
		add_action( 'acf/register_fields', [ $this, 'add_geot_to_acfv4' ] );

		add_action( 'add_meta_boxes', [ $this, 'add_meta_boxes' ] );
		add_action( 'save_post', [ $this, 'save_meta_options' ] , 20 );

		add_filter('geot/plugin_version', function (){ return GEOT_VERSION;});

		//Rules
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
	}


	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		global $pagenow, $post;

		$post_types = apply_filters( 'geot/rules/post_types', array() );

		if ( !in_array( get_post_type(), $post_types ) || !in_array( $pagenow, array( 'post-new.php', 'edit.php', 'post.php' ) ) )
			return;

		$post_id = isset( $post->ID ) ? $post->ID : '';

		wp_enqueue_script( 'geot-admin-js', plugin_dir_url( __FILE__ ) . 'js/geot-admin.js', array( 'jquery' ), GEOT_VERSION, false );

		wp_enqueue_style( 'geot-admin-css', plugin_dir_url( __FILE__ ) . 'css/geot-admin.css', array(), GEOT_VERSION, 'all' );

		wp_localize_script( 'geot-admin-js', 'geot_js',
				apply_filters('geot/rules/vars_localize',
					array(
						'admin_url' => admin_url( ),
						'nonce' 	=> wp_create_nonce( 'geot_nonce' ),
						'l10n'		=> array (
								'or'	=> '<span>'.__('OR', 'geobl' ).'</span>'
							),
						//'opts'      => Geobl_Helper::get_options($post_id)
					)
				)
		);
	}


	/**
	 * Register the metaboxes on all posts types
	 */
	public function add_meta_boxes() {

		$post_types = apply_filters( 'geot/get_post_types', Geot_Helper::get_post_types() );

		foreach ($post_types as $cpt) {
			if( in_array( $cpt, apply_filters('geot/excluded_post_types', ['geotr_cpt','geobl_cpt'] ) ) )
				continue;
			add_meta_box(
				'geot-settings',
				__( 'GeoTargeting page settings', 'geot' ),
				array( $this, 'geot_options_view' ),
				$cpt,
				'normal',
				'core'
				//array( '__back_compat_meta_box' => true )
			);
		}
	}

	/**
	 * Display the view for Geot metabox options
	 * @return mixed
	 */
	public function geot_options_view( $post, $metabox ) {
		$opts 		= apply_filters('geot/metaboxes/get_cpt_options', Geot_Helper::get_cpt_options( $post->ID ), $post->ID );
		$countries 	= geot_countries();
		$regions 	= geot_country_regions();
		$city_regions 	= geot_city_regions();

		if( !isset( $opts['forbidden_text'] ) )
			$opts['forbidden_text'] = __( 'This content is restricted in your region', $this->GeoTarget);

		if( !isset( $opts['geot_remove_post'] ) )
			$opts['geot_remove_post'] = '';

		if( !isset( $opts['geot_include_mode'] ) )
			$opts['geot_include_mode'] = 'include';


		include GEOT_PLUGIN_DIR . 'admin/partials/metabox-options.php';
	}

	/**
	 * Saves popup options and rules
	 *
	 * @param $post_id
	 *
	 * @return
	 */
	public function save_meta_options( $post_id ) {

		// Verify that the nonce is set and valid.
		if ( !isset( $_POST['geot_options_nonce'] ) || ! wp_verify_nonce( $_POST['geot_options_nonce'], 'geot_options' ) ) {
			return $post_id;
		}

		// can user edit this post?
		if ( ! current_user_can( 'edit_post', $post_id ) )
			return $post_id;

		if( did_action( 'save_post' ) !== 1 )
			return $post_id;

		$opts = $_POST['geot'];
		unset( $_POST['geot'] );

		// save box settings
		update_post_meta( $post_id, 'geot_options', apply_filters( 'geot/metaboxes/sanitized_options', $opts ) );
		// add one post meta to let us retrieve only posts that need to be geotarted ( used on helpers class )
		$geot_post = false;
		if( !empty( $opts['country_code'] ) ||
			!empty( $opts['region'] ) ||
			!empty( $opts['city_region'] ) ||
			!empty( $opts['cities'] ) ||
			!empty( $opts['states'] ) ||
			!empty( $opts['zipcodes'] )
		)
			$geot_post = true;
		update_post_meta( $post_id, '_geot_post', $geot_post );
	}

	/**
	 * Add filters for tinymce buttons
	 */
	public function register_tiny_buttons() {
		add_filter( "mce_external_plugins", array( $this, "add_button" ) );
    	add_filter( 'mce_buttons', array( $this, 'register_button' ) );
	}

	/**
	 * Add buton js file
	 * @param [type] $plugin_array [description]
	 */
	function add_button( $plugin_array ) {

    	$plugin_array['geot'] = plugins_url( 'js/geot-tinymce.js' , __FILE__ );
   	 	return $plugin_array;

	}

	/**
	 * Register button
	 * @param  [type] $buttons [description]
	 * @return [type]          [description]
	 */
	function register_button( $buttons ) {
	    array_push( $buttons, '|', 'geot_button' ); // dropcap', 'recentposts
	    return $buttons;
	}

	/**
	 * Add popup editor for
	 */
	function add_editor() {

		include GEOT_PLUGIN_DIR . 'admin/partials/tinymce-popup.php';
		wp_die();
	}

	/**
	 * Register all plugin widgets
	 * @return mixed
	 */
	public function register_widgets() {

     	register_widget( 'Geot_Widget' );
	}

	/**
	 * Add geot to Advanced custom fields v5
	 * @since 1.0.0
	 */
	function add_geot_to_acfv5(){

		include GEOT_PLUGIN_DIR . 'admin/includes/acf-geot-v5.php';
	}

	/**
	 * Add geot to Advanced custom fields v4
	 * @since 1.0.0
	 */
	function add_geot_to_acfv4(){

		include GEOT_PLUGIN_DIR . 'admin/includes/acf-geot-v4.php';
	}
}
