<?php
/**
 * Importer Class
 * @since  1.0.0
 * @package Includes / Admin / Importer
 */
class GeotWP_Links_Importer {

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		// CSS && JS
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );

		// Add/Remove page to menu
		add_action( 'admin_menu', [ $this, 'add_to_menu' ] );
		add_action( 'admin_head', [ $this, 'hide_from_menu' ] );

		// Import the CSV file
		add_action( 'admin_init', [ $this, 'set_fields' ], 10 );
		add_action( 'admin_init', [ $this, 'import_save' ], 11 );
		add_action( 'admin_init', [ $this, 'export_save' ], 11 );
	}


	/**
	 * Fields
	 */
	public function set_fields() {
		
		$this->dest = [
			'dest_label'		=> esc_html__( 'Destination Label', 'geol' ),
			'dest_url'			=> esc_html__( 'Destination URL', 'geol' ),
			'dest_countries'	=> esc_html__( 'Geot Countries', 'geol' ),
			'dest_regions'		=> esc_html__( 'Geot Regions', 'geol' ),
			'dest_cities'		=> esc_html__( 'Geot Cities', 'geol' ),
			'dest_states'		=> esc_html__( 'Geot States', 'geol' ),
			'dest_zipcodes'		=> esc_html__( 'Geot Zipcodes', 'geol' ),
			'dest_radius'		=> esc_html__( 'Geot Radius', 'geol' ),
			'dest_device'		=> esc_html__( 'Geot Device', 'geol' ),
			'dest_ref'			=> esc_html__( 'Geot Referrer URL', 'geol' ),
			'dest_count_dest'	=> esc_html__( 'Stats by Destination', 'geol' ),
		];

		$this->main = [
			'post_id'			=> esc_html__( 'ID', 'geol' ),
			'post_title'		=> esc_html__( 'Title', 'geol' ),
			'source_slug'		=> esc_html__( 'Link Slug', 'geol' ),
			'status_code'		=> esc_html__( 'Redirection code', 'geol' ),
			'dest_default'		=> esc_html__( 'Default URL', 'geol' ),
			'count_click'		=> esc_html__( 'Stats Total Clicks', 'geol' ),
			'click_default'		=> esc_html__( 'Default Total Click', 'geol' ),
		];

		$this->fields = array_merge( $this->main, $this->dest );

		return true;
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		global $pagenow;

		if( isset( $_GET['post_type'] ) && $_GET['post_type'] == 'geol_cpt' && $pagenow == 'edit.php'  ) {
		
			wp_enqueue_script( 'geol-import-js', GEOTWP_L_PLUGIN_URL . 'includes/admin/js/geol-import.js', [ 'jquery' ], GEOTWP_L_VERSION, false );

			wp_localize_script( 'geol-import-js', 'geol_var', [
				'import_id'		=> 'geol_import',
				'import_text'	=> esc_html__( 'Import', 'geol' ),
				'import_link'	=> admin_url( 'admin.php?page=geol-importer' ),
				'export_id'		=> 'geol_export',
				'export_text'	=> esc_html__( 'Export', 'geol' ),
				'export_link'	=> admin_url( 'admin.php?page=geol-exporter' ),
				'nonce'			=> wp_create_nonce( 'geol_nonce' ),
			]);
		}


		if( isset( $_GET['page'] ) && in_array( $_GET['page'], [ 'geol-importer', 'geol-exporter' ] ) ) {
			wp_enqueue_style( 'geol-import-css', GEOTWP_L_PLUGIN_URL . 'includes/admin/css/geol-import.css', [], GEOTWP_L_VERSION, 'all' );
		}
	}


	/**
	 * Add menu items for our custom importers.
	 * @return mixed
	 */
	public function add_to_menu() {

		if ( ! current_user_can( 'edit_posts' ) )
			return;

		add_submenu_page(
			'geot-settings',
			esc_html__( 'Importer', 'geol' ),
			esc_html__( 'Importer', 'geol' ),
			'manage_options',
			'geol-importer',
			[ $this, 'importer' ]
		);

		add_submenu_page(
			'geot-settings',
			esc_html__( 'Exporter', 'geol' ),
			esc_html__( 'Exporter', 'geol' ),
			'manage_options',
			'geol-exporter',
			[ $this, 'exporter' ]
		);
	}

	/**
	 * Hide menu items from view so the pages exist, but the menu items do not.
	 * @return mixed
	 */
	public function hide_from_menu() {
		global $submenu;

		if ( isset( $submenu[ 'geot-settings' ] ) ) {
			foreach ( $submenu[ 'geot-settings' ] as $key => $menu ) {
				if ( 'geol-importer' === $menu[2] || 'geol-exporter' === $menu[2] ) {
					unset( $submenu[ 'geot-settings' ][ $key ] );
				}
			}
		}
	}

	/**
	 * Form Importer
	 * @return mixed
	 */
	public function importer() {

		if( isset( $_GET['step'] ) && $_GET['step'] == 'mapping' ) {

			$transient_name = sprintf( 'geolinks_import_%s', get_current_user_id() );
			$content = get_transient( $transient_name );

			$content_header = $content[0];
			$content_example = $content[1];

			$fields = $this->main;

			$select_key = 0;

			for( $i=0; $i<16; $i++ ) {
				foreach( $this->dest as $dest_key => $dest_value ) {
					$new_key = str_replace('dest_', 'dest_' . $i . '_', $dest_key );
					$fields[ $new_key ] = $dest_value . ' ' . $i;
				}
			}

			$fields['no_import'] = esc_html__( 'Do not Import', 'geol' );

			include_once GEOTWP_L_PLUGIN_DIR . 'includes/admin/partials/section_importer_2.php';
		} else
			include_once GEOTWP_L_PLUGIN_DIR . 'includes/admin/partials/section_importer.php';
	}

	/**
	 * Form Importer
	 * @return mixed
	 */
	public function exporter() {

		$args = [
			'post_type'		=> 'geol_cpt',
			'limit'			=> -1,
			'post_status'	=> 'publish',
		];

		$geol_list = get_posts( $args );
		$geol_fields = $this->fields;
		$errors = '';

		if( isset( $this->error ) )
			$errors = $this->error->get_error_message();

		include_once GEOTWP_L_PLUGIN_DIR . 'includes/admin/partials/section_exporter.php';
	}
	

	/**
	 * Create post with the KML file
	 * @return mixed
	 */
	public function import_save() {

		// WP Permissions
		if( ! current_user_can( 'manage_options' ) )
			return;

		// Variables
		if( ! isset( $_POST['geol'], $_POST['geol']['section'] ) )
			return;

		$this->error = new WP_Error();

		// Nonce
		if( ! isset( $_POST['wpnonce'] ) ||
			! wp_verify_nonce( $_POST['wpnonce'], 'geol_wpnonce' ) ) {
			$this->error->add( 'error', esc_html__( 'Busted!', 'geol' ) );
			return;
		}

		$post_data = $_POST['geol'];

		if( $post_data['section'] != 'import' )
			return;

		// Name Transient
		$transient_name = sprintf( 'geolinks_import_%s', get_current_user_id() );
			
		// If it is mapping step
		if( isset( $_GET['step'] ) && $_GET['step'] == 'mapping' ) {

			$to_save = [];
			$fields_keys = array_map( 'esc_html', $post_data['fields'] );
			$content = get_transient( $transient_name );

			foreach( $content as $line_key => $line_data ) {

				// Ignore header
				if( $line_key == 0 )
					continue;

				foreach( $fields_keys as $key => $field ) {
					if( $line_data[ $key ] == 'no_import' )
						continue;

					$to_save[$line_key][ $field ] = $line_data[ $key ];
				}
			}

			update_option('luko_55', print_r($to_save,true));

			$this->error->add( 'success', esc_html__( 'Successful import', 'geol' ) );

			foreach( $to_save as $fields_save )
				$this->import_items( $fields_save );

			delete_transient( $transient_name );
			
			wp_redirect( remove_query_arg( 'step' ) );

		} else {

			// Check if exist $_FILE
			if( ! isset( $_FILES['geol'] ) ) {
				$this->error->add( 'error', esc_html__( 'Empty File', 'geol' ) );
				return;
			}

			// Check if it was uploaded using HTTP POST 
			if( ! is_uploaded_file( $_FILES['geol']['tmp_name'] ) ) {
				$this->error->add( 'error', esc_html__( 'File was not uploaded', 'geol' ) );
				return;
			}

			// Check if it has errors
			if( ! empty( $_FILES['geol']['error'] ) ) {
				$this->error->add( 'error', esc_html__( 'Error in the file', 'geol' ) );
				return;
			}

			$mimes_allowed = [ 'application/vnd.ms-excel','text/plain','text/csv','text/tsv' ];
			$mime_type = mime_content_type( $_FILES['geol']['tmp_name'] );

			if( ! in_array( $mime_type, $mimes_allowed ) ) {
				$this->error->add( 'error', esc_html__( 'Only is allowed the CSV files', 'geol' ) );
				return;
			}

			// file path
			$file_path = $_FILES['geol']['tmp_name'];
			$content = [];
			
			$handle = fopen( $file_path, 'r');

			while( ( $line = fgetcsv( $handle, 1000, ',' ) ) !== FALSE ) {
				$content[] = $line;
			}

			fclose( $handle );

			set_transient( $transient_name, $content, HOUR_IN_SECONDS );

			wp_redirect( add_query_arg( [
				'step' => 'mapping',
			] ) );
		}

		return true;
	}


	/**
	 * Export to CSV
	 * @return mixed
	 */
	public function export_save() {

		// WP Permissions
		if( ! current_user_can( 'manage_options' ) )
			return;

		// Variables
		if( ! isset( $_POST['geol'], $_POST['geol']['section'] ) )
			return;

		$this->error = new WP_Error();

		// Nonce
		if( ! isset( $_POST['wpnonce'] ) ||
			! wp_verify_nonce( $_POST['wpnonce'], 'geol_wpnonce' ) ) {
			$this->error->add( 'error', esc_html__( 'Busted!', 'geol' ) );
			return;
		}

		$post_data = $_POST['geol'];

		if( $post_data['section'] != 'export' )
			return;

		if( ! isset( $post_data['posts'] ) || empty( $post_data['posts'] ) ) {
			$this->error->add( 'error', esc_html__( 'GeoLinks empty', 'geol' ) );
			return;
		}

		$fields_values = [];
		
		// Get posts to export
		$posts = get_posts( [ 'post_type' => 'geol_cpt', 'include' => $post_data['posts'] ] );

		// Loop
		foreach( $posts as $post ) {

			if( ! empty( $post_data['fields'] ) ) {

				// Match fields
				$imain = array_intersect( $post_data['fields'], array_keys( $this->main ) );
				$idests = array_intersect( $post_data['fields'], array_keys( $this->dest ) );

				$fields_values[] = $this->fields_values( $post, $imain, $idests );
			} else {

				$fields_values[] = $this->fields_values(
					$post, array_keys( $this->main ), array_keys( $this->dest )
				);
			}
		}

		// Chekc if it is empty
		if( empty( $fields_values ) ) {
			$this->error->add( 'error', esc_html__( 'Export empty', 'geol' ) );
			return;
		}

		ob_start();

		$filename = 'GEOLINKS_' . time() . '.CSV';

		$fh = @fopen( 'php://output', 'w' );
		fprintf( $fh, chr(0xEF) . chr(0xBB) . chr(0xBF) );
		header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
		header( 'Content-Description: File Transfer' );
		header( 'Content-type: text/csv' );
		header( "Content-Disposition: attachment; filename={$filename}" );
		header( 'Expires: 0' );
		header( 'Pragma: public' );
		
		fputcsv( $fh, array_keys( $fields_values[0] ) );
		
		foreach( $fields_values as $field_value )
			fputcsv( $fh, $field_value );

		fclose( $fh );

		ob_end_flush();

		die();
	}


	/**
	 * Get Values from fields
	 * @param  WP_POSTS $post
	 * @param  array    $fields_main
	 * @param  array    $fields_dest
	 * @return mixed
	 */
	public function fields_values( $post = null, $fields_main = [], $fields_dest = [] ) {

		$opts = get_post_meta( $post->ID, 'geol_options', true );

		$output = [];

		// Main fields
		foreach( $fields_main as $field_main ) {

			if( $field_main == 'post_id' )
				$output[ 'post_id' ] = $post->ID;
			elseif( $field_main == 'post_title' )
				$output[ 'post_title' ] = $post->post_title;
			elseif( isset( $opts[ $field_main ] ) )
				$output[ $field_main ] = $opts[ $field_main ];
		}

		// Destinations
		if( ! empty( $fields_dest ) && isset( $opts['dest'] ) && ! empty( $opts['dest'] ) ) {

			foreach( $opts['dest'] as $dest_key => $dest_data ) {
				foreach( $fields_dest as $field_dest ) {
					$field_key = str_replace( 'dest_', '', $field_dest );
					$new_key = $dest_key . '_' . $field_key;

					if( ! isset( $dest_data[ $field_key ] ) )
						$output[ $new_key ] = '';
					elseif( is_array( $dest_data[ $field_key ] ) )
						$output[ $new_key ] = implode( ',', $dest_data[ $field_key ] );
					else
						$output[ $new_key ] = $dest_data[ $field_key ];
				}
			}
		}

		return $output;
	}



	public function import_items( $fields = [] ) {

		update_option('luko_51', print_r($fields,true));

		// Post Title
		if( ! isset( $fields['post_title'] ) || empty( $fields['post_title'] ) )
			$post_title = 'GeoLinks Title Default';
		else
			$post_title = sanitize_text_field( $fields['post_title'] );


		// Create Posts
		if( ( ! isset( $fields['post_id'] ) || empty( $fields['post_id'] ) ) ||
		  empty( get_post( absint( $fields['post_id'] ) ) ) ) {

			$args = [
				'post_title'	=> $post_title,
				'post_type'		=> 'geol_cpt',
				'post_status'	=> 'publish',
			];

			$post_id = wp_insert_post( $args );
		
		} else {

			if( $post_title != 'GeoLinks Title Default' ) {

				$args = [
					'ID'			=> absint( $fields['post_id'] ),
					'post_title'	=> $post_title,
				];

				$post_id = wp_update_post( $args );
			} else
				$post_id = absint( $fields['post_id'] );
		}

		update_option('luko_52', print_r($post_id,true));

		$post 		= get_post( $post_id );
		$input		= geotWPL_options( $post_id );
		$settings 	= geotWPL_settings();

		update_option('luko_53', print_r($input,true));

		// Post Name
		if( isset( $fields['source_slug'] ) ) {

			if( isset( $post->post_name ) )
				$input['source_slug'] = $post->post_name == $fields['source_slug'] ? sanitize_title( $fields['source_slug'] ) : $post->post_name;
			else
				$input['source_slug'] = sanitize_title( $fields['source_slug'] );

			unset( $fields['source_slug'] );
		}

		// Status Code
		if( isset( $fields['status_code'] ) && is_numeric( $fields['status_code'] ) ) {
			$input['status_code'] = sanitize_title( $fields['status_code'] );
			unset( $fields['status_code'] );
		}


		// Dest Default
		if( isset( $fields['dest_default'] ) && ! empty( $fields['dest_default'] ) ) {
			$input['dest_default'] = esc_url_raw( $input['dest_default'] );
			unset( $fields['dest_default'] );
		}

		// Count Click
		if( isset( $fields['count_click'] ) ) {
			$input['count_click'] = $fields['count_click'];
			unset( $fields['count_click'] );
		}

		// Click Default
		if( isset( $fields['click_default'] ) ) {
			$input['click_default'] = $fields['click_default'];
			unset( $fields['click_default'] );
		}

		// Destinations
		if( isset( $fields ) && ! empty( $fields ) ) {
			foreach( $fields as $dest_key => $dest_value ) {
				if( strpos('dest_', $dest_key) == FALSE )
					continue;

				$akey = explode( '_', $dest_key );
				$key = absint( $akey[1] );
				$field = sanitize_key( $akey[2] );

				if( in_array( $field, [ 'country', 'regions' ] ) )
					$input['dest'][ $key ][ $field ] = explode( ',', $dest_value );
				else
					$input['dest'][ $key ][ $field ] = $dest_value;
			}
		}

		update_option('luko_54', print_r($input,true));

		$input = apply_filters( 'geol/import/options', $input, $post_id );

		// save box settings
		update_post_meta( $post_id, 'geol_options', $input );

		return $post_id;
	}
}

new GeotWP_Links_Importer();