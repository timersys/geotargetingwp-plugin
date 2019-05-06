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
		add_action( 'add_meta_boxes_geol_cpt', [ $this, 'add_meta_boxes' ], 99 );
		add_action( 'save_post_geol_cpt', [ $this, 'save_meta_options' ] );
		add_filter( 'manage_geol_cpt_posts_columns', [ $this, 'set_custom_cpt_columns' ] );
		add_action( 'manage_geol_cpt_posts_custom_column', [ $this, 'set_custom_cpt_values' ], 10, 2 );
		add_filter( 'wp_insert_post_data', [ $this, 'modify_post_name' ], 10, 2 );
	}

	/**
	 * Register custom post types
	 * @since     1.0.0
	 * @return void
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


	/**
	 * Add custom columns to cpt
	 *
	 * @param [type] $columns [description]
	 *
	 * @since  1.2
	 * @return mixed
	 */
	function set_custom_cpt_columns( $columns ) {

		$settings 	= geol_settings();
		$new_column = [];

		foreach ( $columns as $key => $value ) {

			$new_column[ $key ] = $value;

			if ( $key == 'title' ) {
				$new_column['source_url']	= __( 'Destination URL', 'geol' );
				$new_column['shortcode']	= __( 'Shortcode', 'geol' );

				if( isset( $settings['opt_stats'] ) && $settings['opt_stats'] == 1 )
					$new_column['count_click']	= __( 'Total Clicks', 'geol' );
			}
		}

		return apply_filters( 'geol/manage_columns/name', $new_column, $columns );
	}


	/**
	 * Add custom values columns to cpt
	 *
	 * @param $column
	 * @param $post_id
	 *
	 * @return mixed
	 * @since  1.2
	 */
	function set_custom_cpt_values( $column, $post_id ) {

		$settings     = geol_settings();
		$opts         = geol_options( $post_id );
		$value_column = '';

		switch ( $column ) {
			case 'source_url' :
				$value_column = get_permalink($post_id);
				break;
			case 'count_click' :
				$value_column = $opts['count_click'];
				break;
			case 'shortcode' :
				$value_column = '<input type="text" value="[geo-link slug=&quot;'. esc_attr__( $opts['source_slug'] ) .'&quot; nofollow=&quot;yes&quot; noreferrer=&quot;no&quot;]...[/geo-link]" readonly />';
				break;
			default:
				$column;
		}

		echo apply_filters( 'geol/manage_columns/value', $value_column, $column, $post_id );
	}


	/**
	 * Register the metaboxes for our cpt
	 * @since    1.0.0
	 * @return   void
	 */
	public function add_meta_boxes() {
		global $wp_meta_boxes;

		// remove all other  metaboxes
		if ( isset( $wp_meta_boxes['geol_cpt']['normal'] ) ) {
			unset( $wp_meta_boxes['geol_cpt']['normal'] );
		}
		if ( isset( $wp_meta_boxes['geol_cpt']['core'] ) ) {
			foreach ( $wp_meta_boxes['geol_cpt']['core'] as $key => $mb ) {
				if ( 'submitdiv' == $key ) {
					continue;
				}
				unset( $wp_meta_boxes['geol_cpt']['core'][ $key ] );
			}
		}

		add_meta_box(
			'geol-opts',
			__( 'Redirection Options', 'geol' ),
			[ $this, 'geol_opts' ],
			'geol_cpt',
			'normal',
			'core'
		);

		add_meta_box(
			'geol-urls',
			__( 'Destinations', 'geol' ),
			[ $this, 'geol_urls' ],
			'geol_cpt',
			'normal',
			'core'
		);

		$settings = geol_settings();

		if( isset($settings['opt_stats']) && $settings['opt_stats'] == 1 ) {
		
			add_meta_box(
				'geol-stats',
				__( 'Stats', 'geol' ),
				[ $this, 'geol_stats' ],
				'geol_cpt',
				'normal',
				'core'
			);
		}
	}


	/**
	 * Include the metabox view for opts
	 *
	 * @param  object $post geotrcpt post object
	 * @param  array $metabox full metabox items array
	 *
	 * @since 1.0.0
	 */
	public function geol_opts( $post, $metabox ) {

		$settings = geol_settings();
		$opts     = geol_options( $post->ID );

		include GEOL_PLUGIN_DIR . '/includes/admin/metaboxes/metaboxes-opts.php';
	}

	public function geol_urls( $post, $metabox ) {

		$opts 		= geol_options( $post->ID );
		$devices 	= geol_devices();
		$countries 	= geot_countries();
		$geowp 		= geot_settings();

		include GEOL_PLUGIN_DIR . '/includes/admin/metaboxes/metaboxes-urls.php';
	}

	public function geol_stats( $post, $metabox ) {

		$opts     = geol_options( $post->ID );

		include GEOL_PLUGIN_DIR . '/includes/admin/metaboxes/metaboxes-stats.php';
	}

	/**
	 * Saves the post meta of redirections
	 * @since 1.0.0
	 */
	function save_meta_options( $post_id ) {

		// Verify that the nonce is set and valid.
		if ( ! isset( $_POST['geol_options_nonce'] ) || ! wp_verify_nonce( $_POST['geol_options_nonce'], 'geol_options' ) ) {
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

		$opts = $_POST['geol'];
		unset( $_POST['geol'] );

		$post = get_post( $post_id );
		$outs = geol_options($post_id);
		$settings = geol_settings();

		if ( isset( $post->post_name ) ) {
			$source_slug          = sanitize_title( $opts['source_slug'] );
			$input['source_slug'] = $post->post_name == $source_slug ? $source_slug : $post->post_name;
		} else
			$input['source_slug'] = sanitize_title( $opts['source_slug'] );

		$input['status_code'] = is_numeric( $opts['status_code'] ) ? sanitize_title( $opts['status_code'] ) : '302';

		$input['dest_default'] = !empty( $opts['dest_default'] ) ? esc_url_raw( $opts['dest_default'] ) : '';

		// Counters
		if( isset($settings['opt_stats']) && $settings['opt_stats'] == 1 ) {
			$input['count_click'] = isset( $outs['count_click'] ) ? $outs['count_click'] : 0;
			$input['click_default'] = isset( $outs['click_default'] ) ? $outs['click_default'] : 0;
		}


		if ( is_array( $opts['dest'] ) && count( $opts['dest'] ) > 0 ) {
			$i = 0;
			foreach ( $opts['dest'] as $data ) {
				$key                              = 'dest_' . $i;
				$input['dest'][ $key ]['url']		= esc_url_raw( $data['url'] );
				$input['dest'][ $key ]['countries']	= isset($data['countries']) ? array_map('esc_attr', $data['countries'] ) : [];
				$input['dest'][ $key ]['regions']	= isset($data['regions']) ? array_map('esc_attr', $data['regions'] ) : [];
				$input['dest'][ $key ]['zipcodes']	= esc_attr( $data['zipcodes'] );
				$input['dest'][ $key ]['states']	= esc_attr( $data['states'] );
				$input['dest'][ $key ]['cities']	= esc_attr( $data['cities'] );
				$input['dest'][ $key ]['device']	= esc_attr( $data['device'] );
				$input['dest'][ $key ]['ref']		= esc_url_raw( $data['ref'] );

				if( isset($settings['opt_stats']) && $settings['opt_stats'] == 1 )
					$input['dest'][ $key ]['count_dest'] = isset( $outs['dest'][ $key ]['count_dest'] ) ? $outs['dest'][ $key ]['count_dest'] : 0;
				$i ++;
			}
		}

		$input = apply_filters( 'geol/metaboxes/sanitized_options', $input, $post_id );

		// save box settings
		update_post_meta( $post_id, 'geol_options', $input);
	}

	/**
	 * Modify post_name
	 * @since 1.0.0
	 *
	 * @param $data
	 * @param $postarr
	 *
	 * @return mixed
	 */
	public function modify_post_name( $data, $postarr ) {

		if ( ! isset( $postarr['geol_options_nonce'] ) ||
		     ! wp_verify_nonce( $postarr['geol_options_nonce'], 'geol_options' ) ||
		     $postarr['post_type'] != 'geol_cpt' ||
		     $postarr['post_status'] != 'publish' ||
		     $postarr['post_parent'] != 0
		) {
			return $data;
		}

		$post_id = isset( $postarr['ID'] ) && is_numeric( $postarr['ID'] ) ? $postarr['ID'] : 0;

		// If this is an autosave, our form has not been submitted, so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $data;
		}
		// same for ajax
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return $data;
		}
		// same for cron
		if ( defined( 'DOING_CRON' ) && DOING_CRON ) {
			return $data;
		}
		// same for posts revisions
		if ( is_int( wp_is_post_autosave( $post_id ) ) ) {
			return $data;
		}

		// can user edit this post?
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return $data;
		}

		$post_type   = $postarr['post_type'];
		$post_status = $postarr['post_status'];
		$post_parent = $postarr['post_parent'];
		$post_name   = sanitize_title( $postarr['geol']['source_slug'] );

		$data['post_name'] = wp_unique_post_slug( $post_name, $post_id, $post_status, $post_type, $post_parent );

		return $data;
	}
}

new Geolinks_Cpt();