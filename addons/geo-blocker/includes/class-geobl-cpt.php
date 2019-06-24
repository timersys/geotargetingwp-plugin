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
class GeotWP_Bl_Cpt {

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param string $plugin_name The name of this plugin.
	 * @param string $version The version of this plugin.
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
			'name'               => 'Geo Blocker v' . GEOTWP_BL_VERSION,
			'singular_name'      => _x( 'Geo Blocker', 'post type singular name', 'popups' ),
			'menu_name'          => _x( 'Geo Blocker', 'admin menu', 'popups' ),
			'name_admin_bar'     => _x( 'Geo Blocker', 'add new on admin bar', 'popups' ),
			'add_new'            => _x( 'Add New', 'Geo Block', 'popups' ),
			'add_new_item'       => __( 'Add New Geo Block', 'popups' ),
			'new_item'           => __( 'New Geo Block', 'popups' ),
			'edit_item'          => __( 'Edit Geo Block', 'popups' ),
			'view_item'          => __( 'View Geo Block', 'popups' ),
			'all_items'          => __( 'Geo Blocker', 'popups' ),
			'search_items'       => __( 'Search Geo Block', 'popups' ),
			'parent_item_colon'  => __( 'Parent Geo Block:', 'popups' ),
			'not_found'          => __( 'No Geo Blocks found.', 'popups' ),
			'not_found_in_trash' => __( 'No Geo Blocks found in Trash.', 'popups' ),
		];

		$args = [
			'labels'              => $labels,
			'public'              => false,
			'publicly_queryable'  => true,
			'exclude_from_search' => true,
			'show_ui'             => true,
			'show_in_menu'        => 'geot-settings',
			'query_var'           => true,
			'rewrite'             => [ 'slug' => 'geobl_cpt' ],
			'capability_type'     => 'post',
			'capabilities'        => [
				'publish_posts'       => apply_filters( 'geobl/settings_page/roles', 'manage_options' ),
				'edit_posts'          => apply_filters( 'geobl/settings_page/roles', 'manage_options' ),
				'edit_others_posts'   => apply_filters( 'geobl/settings_page/roles', 'manage_options' ),
				'delete_posts'        => apply_filters( 'geobl/settings_page/roles', 'manage_options' ),
				'delete_others_posts' => apply_filters( 'geobl/settings_page/roles', 'manage_options' ),
				'read_private_posts'  => apply_filters( 'geobl/settings_page/roles', 'manage_options' ),
				'edit_post'           => apply_filters( 'geobl/settings_page/roles', 'manage_options' ),
				'delete_post'         => apply_filters( 'geobl/settings_page/roles', 'manage_options' ),
				'read_post'           => apply_filters( 'geobl/settings_page/roles', 'manage_options' ),
			],
			'has_archive'         => false,
			'hierarchical'        => false,
			'menu_position'       => 10,
			'supports'            => [ 'title' ],
		];

		register_post_type( 'geobl_cpt', $args );

	}
}

new GeotWP_Bl_Cpt();