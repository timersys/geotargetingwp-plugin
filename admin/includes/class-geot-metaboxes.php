<?php

/**
 * The cpt metaboxes functionality of the plugin.
 *
 * @link       https://timersys.com
 * @since      1.0.0
 *
 * @package    Geot
 * @subpackage Geot/admin/includes
 */

/**
 * @subpackage Geotr/admin/includes
 * @author     Damian Logghe <damian@timersys.com>
 */
class GeotWP_Metaboxes {

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		// Add geot to Advanced custom fields plugin
		add_action( 'acf/include_field_types', [ $this, 'add_geot_to_acfv5' ] );
		add_action( 'acf/register_fields', [ $this, 'add_geot_to_acfv4' ] );

		// geotargeting pro
		add_action( 'add_meta_boxes', [ $this, 'add_meta_boxes' ] );
		add_action( 'save_post', [ $this, 'save_meta_options' ], 20 );

		// Geo Redirects
		add_action( 'add_meta_boxes_geotr_cpt', [ $this, 'add_meta_boxes_geotr_cpt' ] );
		add_action( 'save_post_geotr_cpt', [ $this, 'save_meta_options_geotr_cpt' ] );

		//Geo Block
		add_action( 'add_meta_boxes_geobl_cpt', [ $this, 'add_meta_boxes_geobl_cpt' ] );
		add_action( 'save_post_geobl_cpt', [ $this, 'save_meta_options_geobl_cpt' ] );

		//Geo Links
		add_action( 'add_meta_boxes_geol_cpt', [ $this, 'add_meta_boxes_geol_cpt' ], 99 );
		add_action( 'save_post_geol_cpt', [ $this, 'save_meta_options_geol_cpt' ] );
		add_filter( 'manage_geol_cpt_posts_columns', [ $this, 'set_custom_cpt_columns_geol_cpt' ] );
		add_action( 'manage_geol_cpt_posts_custom_column', [ $this, 'set_custom_cpt_values_geol_cpt' ], 10, 2 );
		add_filter( 'wp_insert_post_data', [ $this, 'modify_post_name_geol_cpt' ], 10, 2 );
	}


	/**
	 * Register the metaboxes on all posts types
	 */
	public function add_meta_boxes() {

		$post_types = apply_filters( 'geot/get_post_types', GeotWP_Helper::get_post_types() );

		foreach ( $post_types as $cpt ) {
			if ( in_array( $cpt, apply_filters( 'geot/exclude/post_types', [] ) ) ) {
				continue;
			}

			add_meta_box(
				'geot-settings',
				__( 'GeoTargeting page settings', 'geot' ),
				[ $this, 'geot_options_view' ],
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
		$opts         = apply_filters( 'geot/metaboxes/get_cpt_options', GeotWP_Helper::get_cpt_options( $post->ID ), $post->ID );
		$countries 		= geot_countries();
		$regions 		= geot_country_regions();
		$city_regions 	= geot_city_regions();
		$state_regions 	= geot_state_regions();
		$zip_regions	= geot_zip_regions();

		if ( ! isset( $opts['forbidden_text'] ) ) {
			$opts['forbidden_text'] = __( 'This content is restricted in your region', 'geot' );
		}

		if ( ! isset( $opts['geot_remove_post'] ) ) {
			$opts['geot_remove_post'] = '';
		}

		if ( ! isset( $opts['geot_include_mode'] ) ) {
			$opts['geot_include_mode'] = 'include';
		}


		include GEOWP_PLUGIN_DIR . 'admin/partials/metabox-options.php';
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
		if ( ! isset( $_POST['geot_options_nonce'] ) || ! wp_verify_nonce( $_POST['geot_options_nonce'], 'geot_options' ) ) {
			return $post_id;
		}

		// can user edit this post?
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return $post_id;
		}

		if ( ! isset( $_POST['geot'] ) ) {
			return $post_id;
		}

		$opts = $_POST['geot'];
		unset( $_POST['geot'] );

		// save box settings
		update_post_meta( $_POST['post_ID'], 'geot_options', apply_filters( 'geot/metaboxes/sanitized_options', $opts ) );
		
		// add one post meta to let us retrieve only posts that need to be geotarted ( used on helpers class )
		$geot_post = false;
		if ( ! empty( $opts['country_code'] ) ||
		     ! empty( $opts['region'] ) ||
		     ! empty( $opts['city_region'] ) ||
		     ! empty( $opts['cities'] ) ||
		     ! empty( $opts['states'] ) ||
		     ! empty( $opts['state_region'] ) ||
		     ! empty( $opts['zip_region'] ) ||
		     ! empty( $opts['zipcodes'] ) ||
		     ( ! empty( $opts['radius_km'] ) && ! empty( $opts['radius_lat'] ) && ! empty( $opts['radius_lng'] ) )
		) {
			$geot_post = true;
		}
		update_post_meta( $_POST['post_ID'], '_geot_post', $geot_post );
	}

	/**
	 * Add geot to Advanced custom fields v5
	 * @since 1.0.0
	 */
	function add_geot_to_acfv5() {

		include GEOWP_PLUGIN_DIR . 'admin/includes/acf-geot-v5.php';
	}

	/**
	 * Add geot to Advanced custom fields v4
	 * @since 1.0.0
	 */
	function add_geot_to_acfv4() {

		include GEOWP_PLUGIN_DIR . 'admin/includes/acf-geot-v4.php';
	}


	/***********
	 * GEOTR
	 ************/

	/**
	 * Register the metaboxes for our cpt
	 * @return   void
	 * @since    1.0.0
	 */
	public function add_meta_boxes_geotr_cpt() {
		add_meta_box(
			'geotr-rules',
			__( 'Redirection Rules', 'geot' ),
			[ $this, 'meta_boxes_rules_geotr_cpt' ],
			'geotr_cpt',
			'normal',
			'core'
		);
		add_meta_box(
			'geotr-opts',
			__( 'Redirection Options', 'geot' ),
			[ $this, 'meta_boxes_opts_geotr_cpt' ],
			'geotr_cpt',
			'normal',
			'core'
		);
	}

	/**
	 * Saves the post meta of redirections
	 * @since 1.0.0
	 */
	function save_meta_options_geotr_cpt( $post_id ) {

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
		$opts['one_time_redirect'] = absint( sanitize_text_field( $opts['one_time_redirect'] ) );
		$opts['status']            = absint( sanitize_text_field( $opts['status'] ) );

		// save box settings
		update_post_meta( $post_id, 'geotr_options', apply_filters( 'geotr/metaboxes/sanitized_options', $opts ) );

		// Start with rules
		GeotWP_Helper::save_rules( $post_id, $_POST, 'geotr_rules' );
	}


	/**
	 * Include the metabox view for rules
	 *
	 * @param object $post spucpt post object
	 * @param array $metabox full metabox items array
	 *
	 * @since 1.0.0
	 */
	public function meta_boxes_rules_geotr_cpt( $post, $metabox ) {

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
	public function meta_boxes_opts_geotr_cpt( $post, $metabox ) {

		$opts = apply_filters( 'geot/metaboxes/geotr_cpt/get_options', GeotWP_R_Helper::get_options( $post->ID ), $post->ID );

		include GEOTWP_R_PLUGIN_DIR . '/admin/partials/metaboxes/opts.php';
	}



	/**********
	 * GEOBL
	 ***********/
	/**
	 * Register the metaboxes for our cpt
	 * @return   void
	 * @since    1.0.0
	 */
	public function add_meta_boxes_geobl_cpt() {
		add_meta_box(
			'geobl-rules',
			__( 'Block Rules', 'geot' ),
			[ $this, 'meta_boxes_rules_geobl_cpt' ],
			'geobl_cpt',
			'normal',
			'core'
		);
		add_meta_box(
			'geobl-opts',
			__( 'Block Options', 'geot' ),
			[ $this, 'meta_boxes_opts_geobl_cpt' ],
			'geobl_cpt',
			'normal',
			'core'
		);
	}

	/**
	 * Saves the post meta of redirections
	 * @since 1.0.0
	 */
	function save_meta_options_geobl_cpt( $post_id ) {

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
		$opts['block_message'] = $opts['block_message'];

		// save box settings
		update_post_meta( $post_id, 'geobl_options', apply_filters( 'geobl/metaboxes/sanitized_options', $opts ) );

		// Start with rules
		GeotWP_Helper::save_rules( $post_id, $_POST, 'geobl_rules' );
	}


	/**
	 * Include the metabox view for rules
	 *
	 * @param object $post spucpt post object
	 * @param array $metabox full metabox items array
	 *
	 * @since 1.0.0
	 */
	public function meta_boxes_rules_geobl_cpt( $post, $metabox ) {

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
	public function meta_boxes_opts_geobl_cpt( $post, $metabox ) {

		$opts = apply_filters( 'geobl/metaboxes/get_options', GeotWP_Bl_Helper::get_options( $post->ID ), $post->ID );

		include GEOTWP_BL_PLUGIN_DIR . '/admin/partials/metaboxes/opts.php';
	}




	/****************
	 * GEO LINKS
	 *****************/

	/**
	 * Add custom columns to cpt
	 *
	 * @param [type] $columns [description]
	 *
	 * @return mixed
	 * @since  1.2
	 */
	function set_custom_cpt_columns_geol_cpt( $columns ) {

		$settings   = geotWPL_settings();
		$new_column = [];

		foreach ( $columns as $key => $value ) {

			$new_column[ $key ] = $value;

			if ( $key == 'title' ) {
				$new_column['source_url'] = __( 'Destination URL', 'geot' );
				$new_column['shortcode']  = __( 'Shortcode', 'geot' );

				if ( isset( $settings['opt_stats'] ) && $settings['opt_stats'] == 1 ) {
					$new_column['count_click'] = __( 'Total Clicks', 'geot' );
				}
			}
		}

		return apply_filters( 'geot/manage_columns/geol_cpt/name', $new_column, $columns );
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
	function set_custom_cpt_values_geol_cpt( $column, $post_id ) {

		$settings     = geotWPL_settings();
		$opts         = geotWPL_options( $post_id );
		$value_column = '';

		switch ( $column ) {
			case 'source_url' :
				$value_column = get_permalink( $post_id );
				break;
			case 'count_click' :
				$value_column = $opts['count_click'];
				break;
			case 'shortcode' :
				$value_column = '<input type="text" value="[geo-link slug=&quot;' . esc_attr__( $opts['source_slug'] ) . '&quot; nofollow=&quot;yes&quot; noreferrer=&quot;no&quot;]...[/geo-link]" readonly />';
				break;
			default:
				$column;
		}

		echo apply_filters( 'geot/manage_columns/geol_cpt/value', $value_column, $column, $post_id );
	}


	/**
	 * Register the metaboxes for our cpt
	 * @return   void
	 * @since    1.0.0
	 */
	public function add_meta_boxes_geol_cpt() {
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
			__( 'Redirection Options', 'geot' ),
			[ $this, 'meta_box_opts_geol_cpt' ],
			'geol_cpt',
			'normal',
			'core'
		);

		add_meta_box(
			'geol-urls',
			__( 'Destinations', 'geot' ),
			[ $this, 'meta_box_urls_geol_cpt' ],
			'geol_cpt',
			'normal',
			'core'
		);

		$settings = geotWPL_settings();

		if ( isset( $settings['opt_stats'] ) && $settings['opt_stats'] == 1 ) {

			add_meta_box(
				'geol-stats',
				__( 'Stats', 'geot' ),
				[ $this, 'meta_box_stats_geol_cpt' ],
				'geol_cpt',
				'normal',
				'core'
			);
		}
	}


	/**
	 * Include the metabox view for opts
	 *
	 * @param object $post geotrcpt post object
	 * @param array $metabox full metabox items array
	 *
	 * @since 1.0.0
	 */
	public function meta_box_opts_geol_cpt( $post, $metabox ) {

		$settings = geotWPL_settings();
		$opts     = geotWPL_options( $post->ID );

		include GEOTWP_L_PLUGIN_DIR . '/includes/admin/metaboxes/metaboxes-opts.php';
	}

	public function meta_box_urls_geol_cpt( $post, $metabox ) {

		$opts      = geotWPL_options( $post->ID );
		$devices   = geotWPL_devices();
		$countries = geot_countries();
		$regions   = geot_country_regions();

		include GEOTWP_L_PLUGIN_DIR . '/includes/admin/metaboxes/metaboxes-urls.php';
	}

	public function meta_box_stats_geol_cpt( $post, $metabox ) {

		$opts = geotWPL_options( $post->ID );

		include GEOTWP_L_PLUGIN_DIR . '/includes/admin/metaboxes/metaboxes-stats.php';
	}

	/**
	 * Saves the post meta of redirections
	 * @since 1.0.0
	 */
	public function save_meta_options_geol_cpt( $post_id ) {

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

		$post     = get_post( $post_id );
		$outs     = geotWPL_options( $post_id );
		$settings = geotWPL_settings();

		if ( isset( $post->post_name ) ) {
			$source_slug          = sanitize_title( $opts['source_slug'] );
			$input['source_slug'] = $post->post_name == $source_slug ? $source_slug : $post->post_name;
		} else {
			$input['source_slug'] = sanitize_title( $opts['source_slug'] );
		}

		$input['status_code'] = is_numeric( $opts['status_code'] ) ? sanitize_title( $opts['status_code'] ) : '302';

		$input['dest_default'] = ! empty( $opts['dest_default'] ) ? esc_url_raw( $opts['dest_default'] ) : '';

		// Counters
		if ( isset( $settings['opt_stats'] ) && $settings['opt_stats'] == 1 ) {
			$input['count_click']   = isset( $outs['count_click'] ) ? $outs['count_click'] : 0;
			$input['click_default'] = isset( $outs['click_default'] ) ? $outs['click_default'] : 0;
		}


		if ( is_array( $opts['dest'] ) && count( $opts['dest'] ) > 0 ) {
			$i = 0;
			foreach ( $opts['dest'] as $data ) {
				$key                                = 'dest_' . $i;
				$input['dest'][ $key ]['key']		= $key;
				$input['dest'][ $key ]['label']    	= esc_attr( $data['label'] );
				$input['dest'][ $key ]['url']       = esc_url_raw( $data['url'] );
				$input['dest'][ $key ]['countries'] = isset( $data['countries'] ) ? array_map( 'esc_attr', $data['countries'] ) : [];
				$input['dest'][ $key ]['regions']   = isset( $data['regions'] ) ? array_map( 'esc_attr', $data['regions'] ) : [];
				$input['dest'][ $key ]['zipcodes']  = esc_attr( $data['zipcodes'] );
				$input['dest'][ $key ]['radius_km']  = esc_attr( $data['radius_km'] );
				$input['dest'][ $key ]['radius_lat']  = esc_attr( $data['radius_lat'] );
				$input['dest'][ $key ]['radius_lng']  = esc_attr( $data['radius_lng'] );
				$input['dest'][ $key ]['states']    = esc_attr( $data['states'] );
				$input['dest'][ $key ]['cities']    = esc_attr( $data['cities'] );
				$input['dest'][ $key ]['device']    = esc_attr( $data['device'] );
				$input['dest'][ $key ]['ref']       = esc_url_raw( $data['ref'] );

				if ( isset( $settings['opt_stats'] ) && $settings['opt_stats'] == 1 ) {
					$input['dest'][ $key ]['count_dest'] = isset( $outs['dest'][ $key ]['count_dest'] ) ? $outs['dest'][ $key ]['count_dest'] : 0;
				}
				$i++;
			}
		}

		$input = apply_filters( 'geol/metaboxes/sanitized_options', $input, $post_id );

		// save box settings
		update_post_meta( $post_id, 'geol_options', $input );
	}

	/**
	 * Modify post_name
	 *
	 * @param $data
	 * @param $postarr
	 *
	 * @return mixed
	 * @since 1.0.0
	 *
	 */
	public function modify_post_name_geol_cpt( $data, $postarr ) {

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