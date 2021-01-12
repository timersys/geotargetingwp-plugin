<?php

/**
 * Class GeotWP_LinksCpt will handle all stuff related to custom post type
 * @since 1.0.0
 */
class Geolinks_Cpt {

	/**
	 * GeotWP_LinksCpt constructor.
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

		$settings = geotWPL_settings();

		$labels = [
			'name'               => 'Geo Links v' . GEOTWP_L_VERSION,
			'singular_name'      => _x( 'Geo Links', 'post type singular name', 'geol' ),
			'menu_name'          => _x( 'Geo Links', 'admin menu', 'geol' ),
			'name_admin_bar'     => _x( 'Geo Links', 'add new on admin bar', 'geol' ),
			'add_new'            => _x( 'Add New', 'Geo Links', 'geol' ),
			'add_new_item'       => __( 'Add New Geo Links', 'geol' ),
			'new_item'           => __( 'New Geo Links', 'geol' ),
			'edit_item'          => __( 'Edit Geo Links', 'geol' ),
			'view_item'          => __( 'View Geo Links', 'geol' ),
			'all_items'          => __( 'Geo Links', 'geol' ),
			'search_items'       => __( 'Search Geo Links', 'geol' ),
			'parent_item_colon'  => __( 'Parent Geo Links:', 'geol' ),
			'not_found'          => __( 'No Geo Links found.', 'geol' ),
			'not_found_in_trash' => __( 'No Geo Links found in Trash.', 'geol' ),
		];

		$args = [
			'labels'              => $labels,
			'public'              => false,
			'publicly_queryable'  => true,
			'show_ui'             => true,
			'show_in_menu'        => 'geot-settings',
			'query_var'           => true,
			'exclude_from_search' => true,
			'rewrite'             => [
				'slug'       => $settings['goto_page'],
				'with_front' => false
			],
			'capability_type'     => 'post',
			'capabilities'        => [
				'publish_posts'       => apply_filters( 'geol/settings_page/roles', 'manage_options' ),
				'edit_posts'          => apply_filters( 'geol/settings_page/roles', 'manage_options' ),
				'edit_others_posts'   => apply_filters( 'geol/settings_page/roles', 'manage_options' ),
				'delete_posts'        => apply_filters( 'geol/settings_page/roles', 'manage_options' ),
				'delete_others_posts' => apply_filters( 'geol/settings_page/roles', 'manage_options' ),
				'read_private_posts'  => apply_filters( 'geol/settings_page/roles', 'manage_options' ),
				'edit_post'           => apply_filters( 'geol/settings_page/roles', 'manage_options' ),
				'delete_post'         => apply_filters( 'geol/settings_page/roles', 'manage_options' ),
				'read_post'           => apply_filters( 'geol/settings_page/roles', 'manage_options' ),
			],
			'has_archive'         => false,
			'hierarchical'        => false,
			'menu_position'       => 10,
			'supports'            => [ 'title' ],
		];

		register_post_type( 'geol_cpt', $args );
	}
}

new Geolinks_Cpt();