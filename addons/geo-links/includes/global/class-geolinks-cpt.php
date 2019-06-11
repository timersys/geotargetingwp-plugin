<?php

/**
 * Class GeoLinksCpt will handle all stuff related to custom post type
 * @since 1.0.0
 */
class Geolinks_Cpt {

	/**
	 * GeoLinksCpt constructor.
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

		$settings = geol_settings();

		$labels = [
			'name'               => 'Geo Links v' . GEOL_VERSION,
			'singular_name'      => _x( 'Geo Links', 'post type singular name', 'popups' ),
			'menu_name'          => _x( 'Geo Links', 'admin menu', 'popups' ),
			'name_admin_bar'     => _x( 'Geo Links', 'add new on admin bar', 'popups' ),
			'add_new'            => _x( 'Add New', 'Geo Links', 'popups' ),
			'add_new_item'       => __( 'Add New Geo Links', 'popups' ),
			'new_item'           => __( 'New Geo Links', 'popups' ),
			'edit_item'          => __( 'Edit Geo Links', 'popups' ),
			'view_item'          => __( 'View Geo Links', 'popups' ),
			'all_items'          => __( 'Geo Links', 'popups' ),
			'search_items'       => __( 'Search Geo Links', 'popups' ),
			'parent_item_colon'  => __( 'Parent Geo Links:', 'popups' ),
			'not_found'          => __( 'No Geo Links found.', 'popups' ),
			'not_found_in_trash' => __( 'No Geo Links found in Trash.', 'popups' ),
		];

		$args = [
			'labels'              => $labels,
			'public'              => false,
			'publicly_queryable'  => true,
			'show_ui'             => true,
			'show_in_menu'        => 'geot-settings',
			'query_var'           => true,
			'exclude_from_search' => true,
			'rewrite'             => [ 'slug' => $settings['goto_page'] ],
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