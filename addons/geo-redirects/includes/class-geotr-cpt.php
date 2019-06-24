<?php
/**
 *
 *
 * @link       https://timersys.com
 * @since      1.0.0
 *
 * @package    Geotr
 * @subpackage Geotr/admin
 */

class GeotWP_R_Cpt {

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		add_action( 'init', [ $this, 'register_cpt' ] );
	}


	/**
	 * Register custom post types
	 * @return void
	 * @since     1.0.0
	 */
	public function register_cpt() {

		$labels = [
			'name'               => 'Geo Redirects v' . GEOTWP_R_VERSION,
			'singular_name'      => _x( 'Geo Redirects', 'post type singular name', 'popups' ),
			'menu_name'          => _x( 'Geo Redirects', 'admin menu', 'popups' ),
			'name_admin_bar'     => _x( 'Geo Redirects', 'add new on admin bar', 'popups' ),
			'add_new'            => _x( 'Add New', 'Geo Redirection', 'popups' ),
			'add_new_item'       => __( 'Add New Geo Redirection', 'popups' ),
			'new_item'           => __( 'New Geo Redirection', 'popups' ),
			'edit_item'          => __( 'Edit Geo Redirection', 'popups' ),
			'view_item'          => __( 'View Geo Redirection', 'popups' ),
			'all_items'          => __( 'Geo Redirects', 'popups' ),
			'search_items'       => __( 'Search Geo Redirection', 'popups' ),
			'parent_item_colon'  => __( 'Parent Geo Redirection:', 'popups' ),
			'not_found'          => __( 'No Geo Redirection found.', 'popups' ),
			'not_found_in_trash' => __( 'No Geo Redirection found in Trash.', 'popups' ),
		];

		$args = [
			'labels'              => $labels,
			'public'              => false,
			'publicly_queryable'  => true,
			'show_ui'             => true,
			'show_in_menu'        => 'geot-settings',
			'query_var'           => true,
			'exclude_from_search' => true,
			'rewrite'             => [ 'slug' => 'geotr_cpt' ],
			'capability_type'     => 'post',
			'capabilities'        => [
				'publish_posts'       => apply_filters( 'geotr/settings_page/roles', 'manage_options' ),
				'edit_posts'          => apply_filters( 'geotr/settings_page/roles', 'manage_options' ),
				'edit_others_posts'   => apply_filters( 'geotr/settings_page/roles', 'manage_options' ),
				'delete_posts'        => apply_filters( 'geotr/settings_page/roles', 'manage_options' ),
				'delete_others_posts' => apply_filters( 'geotr/settings_page/roles', 'manage_options' ),
				'read_private_posts'  => apply_filters( 'geotr/settings_page/roles', 'manage_options' ),
				'edit_post'           => apply_filters( 'geotr/settings_page/roles', 'manage_options' ),
				'delete_post'         => apply_filters( 'geotr/settings_page/roles', 'manage_options' ),
				'read_post'           => apply_filters( 'geotr/settings_page/roles', 'manage_options' ),
			],
			'has_archive'         => false,
			'hierarchical'        => false,
			'menu_position'       => 10,
			'supports'            => [ 'title' ],
		];

		register_post_type( 'geotr_cpt', $args );

	}
}

new GeotWP_R_Cpt();