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
			'key'			=> esc_html__( 'Dest Key', 'geot' ),
			'label'			=> esc_html__( 'Dest Label', 'geot' ),
			'url'			=> esc_html__( 'Dest URL', 'geot' ),
			'countries'		=> esc_html__( 'Geot Countries', 'geot' ),
			'regions'		=> esc_html__( 'Geot Regions', 'geot' ),
			'cities'		=> esc_html__( 'Geot Cities', 'geot' ),
			'states'		=> esc_html__( 'Geot States', 'geot' ),
			'zipcodes'		=> esc_html__( 'Geot Zipcodes', 'geot' ),
			'radius_km'		=> esc_html__( 'Geot Radius KM', 'geot' ),
			'radius_lat'	=> esc_html__( 'Geot Radius Lat', 'geot' ),
			'radius_lng'	=> esc_html__( 'Geot Radius Lng', 'geot' ),
			'device'		=> esc_html__( 'Geot Device', 'geot' ),
			'ref'			=> esc_html__( 'Geot Referrer URL', 'geot' ),
			//'count_dest'	=> esc_html__( 'Stats by Destination', 'geot' ),
		];

		$this->main = [
			'post_id'			=> esc_html__( 'ID', 'geot' ),
			'post_title'		=> esc_html__( 'Title', 'geot' ),
			'source_slug'		=> esc_html__( 'Link Slug', 'geot' ),
			'status_code'		=> esc_html__( 'Redirection code', 'geot' ),
			'dest_default'		=> esc_html__( 'Default URL', 'geot' ),
			//'count_click'		=> esc_html__( 'Stats Total Clicks', 'geot' ),
			//'click_default'		=> esc_html__( 'Default Total Click', 'geot' ),
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
				'import_text'	=> esc_html__( 'Import', 'geot' ),
				'import_link'	=> admin_url( 'admin.php?page=geol-importer' ),
				'export_id'		=> 'geol_export',
				'export_text'	=> esc_html__( 'Export', 'geot' ),
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
			esc_html__( 'Importer', 'geot' ),
			esc_html__( 'Importer', 'geot' ),
			'manage_options',
			'geol-importer',
			[ $this, 'importer' ]
		);

		add_submenu_page(
			'geot-settings',
			esc_html__( 'Exporter', 'geot' ),
			esc_html__( 'Exporter', 'geot' ),
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

		if( isset( $_GET['step'] ) && $_GET['step'] == 'done' ) {
			$counter = isset( $_GET['counter'] ) ? absint( $_GET['counter'] ) : 0;
			include_once GEOTWP_L_PLUGIN_DIR . 'includes/admin/partials/section_importer_done.php';
		
		} else
			include_once GEOTWP_L_PLUGIN_DIR . 'includes/admin/partials/section_importer_file.php';
	}

	/**
	 * Form Importer
	 * @return mixed
	 */
	public function exporter() {

		$args = [
			'post_type'		=> 'geol_cpt',
			'numberposts'	=> -1,
			'post_status'	=> 'publish',
		];

		$geol_list = get_posts( $args );
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
			$this->error->add( 'error', esc_html__( 'Busted!', 'geot' ) );
			return;
		}

		$post_data = $_POST['geol'];

		if( $post_data['section'] != 'import' )
			return;			
		
		// Check if exist $_FILE
		if( ! isset( $_FILES['geol'] ) ) {
			$this->error->add( 'error', esc_html__( 'Empty File', 'geot' ) );
			return;
		}

		// Check if it was uploaded using HTTP POST 
		if( ! is_uploaded_file( $_FILES['geol']['tmp_name'] ) ) {
			$this->error->add( 'error', esc_html__( 'File was not uploaded', 'geot' ) );
			return;
		}

		// Check if it has errors
		if( ! empty( $_FILES['geol']['error'] ) ) {
			$this->error->add( 'error', esc_html__( 'Error in the file', 'geot' ) );
			return;
		}

		$mimes_allowed = [ 'application/vnd.ms-excel','text/plain','text/csv','text/tsv' ];
		$mime_type = mime_content_type( $_FILES['geol']['tmp_name'] );

		if( ! in_array( $mime_type, $mimes_allowed ) ) {
			$this->error->add( 'error', esc_html__( 'Only is allowed the CSV files', 'geot' ) );
			return;
		}

		// file path
		$file_path = $_FILES['geol']['tmp_name'];
		$content = [];
		
		$handle = fopen( $file_path, 'r');

		while( ( $line = fgetcsv( $handle, 10000, ',' ) ) !== FALSE ) {
			$content[] = $line;
		}

		fclose( $handle );


		// Delete the first element
		if( isset( $post_data['ignore'] ) && $post_data['ignore'] == 'yes' )
			array_shift( $content );

		$to_save = [];

		foreach( $content as $line_data ) {

			$to_line = array_combine( array_keys( $this->fields ), $line_data );

			// If redirect key not exists, continue
			if( ! isset( $to_line[ 'key' ] ) || empty( $to_line[ 'key' ] ) )
				continue;

			// If the first line is empty
			if( empty( $to_line[ 'post_id' ] ) && ! isset( $post_id ) )
				continue;

			// Set post ID to a temporary aux variable
			if( ! empty( $to_line[ 'post_id' ] ) )
				$post_id = $to_line[ 'post_id' ];

			// Save to output array
			if( ! isset( $to_save[ $post_id ] ) ) {

				// Principal fields
				foreach( array_keys( $this->main ) as $main_slug  )
					$to_save[ $post_id ][ $main_slug ] = $to_line[ $main_slug ];
			}

			// Destinations fields
			foreach( array_keys( $this->dest ) as $dest_slug ) {

				// If it is countries or regions, it must be an array
				if( in_array( $dest_slug, [ 'countries', 'regions' ] ) )
					$to_save[ $post_id ][ 'dest' ][ $to_line[ 'key' ] ][ $dest_slug ] = explode( ',', $to_line[ $dest_slug ] );
				else	
					$to_save[ $post_id ][ 'dest' ][ $to_line[ 'key' ] ][ $dest_slug ] = $to_line[ $dest_slug ];
			}
		}


		$counter = 0;
		foreach( $to_save as $fields_save ) {
			if( $this->import_items( $fields_save ) )
				$counter++;
		}


		wp_redirect( add_query_arg( [
			'step' 		=> 'done',
			'counter'	=> $counter,
		] ) );


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
			$this->error->add( 'error', esc_html__( 'Busted!', 'geot' ) );
			return;
		}

		$post_data = $_POST['geol'];

		if( $post_data['section'] != 'export' )
			return;

		if( ! isset( $post_data['posts'] ) || empty( $post_data['posts'] ) ) {
			$this->error->add( 'error', esc_html__( 'GeoLinks empty', 'geot' ) );
			return;
		}

		// Initialize export content 
		$export_content = [];
		
		// Get posts to export
		$posts = get_posts( [ 'post_type' => 'geol_cpt', 'include' => $post_data['posts'] ] );

		// Loop
		foreach( $posts as $post ) {

			$opts = get_post_meta( $post->ID, 'geol_options', true );

			if( ! isset( $opts['dest'] ) )
				continue;
			
			// initialize row
			$export_main = [];

			// Principal fields
			foreach( array_keys( $this->main ) as $main_slug ) {

				if( $main_slug == 'post_id' )
					$export_main[ 'post_id' ] = $post->ID;
				elseif( $main_slug == 'post_title' )
					$export_main[ 'post_title' ] = $post->post_title;
				elseif( isset( $opts[ $main_slug ] ) )
					$export_main[ $main_slug ] = $opts[ $main_slug ];
			}

			$loop = 0;

			// Destinations fields
			foreach( $opts['dest'] as $dest_key => $dest_data ) {

				// Generate Key
				$export_key = $post->ID . '_' . $dest_key;

				// Principal fields
				if( $loop == 0 )
					$export_content[ $export_key ] = $export_main;
				else {
					// from the 2nd line onwards the values must be empty
					foreach( array_keys( $export_main ) as $key_main )
						$export_content[ $export_key ][ $key_main ] = '';
				}

				// Destination
				foreach( array_keys( $this->dest ) as $dest_slug ) {

					// If it is key and it is no set
					if( $dest_slug == 'key' && ! isset( $dest_data[ 'key' ] ) ) {
						$export_content[ $export_key ][ 'key' ] = $dest_key;
						continue;
					}

					// If the slug not exist
					if( ! isset( $dest_data[ $dest_slug ] ) ) {
						$export_content[ $export_key ][ $dest_slug ] = '';
						continue;
					}

					// If it is an array
					if( is_array( $dest_data[ $dest_slug ] ) ) {
						$export_content[ $export_key ][ $dest_slug ] = implode( ',', $dest_data[ $dest_slug ] );
						continue;
					}
					
					$export_content[ $export_key ][ $dest_slug ] = $dest_data[ $dest_slug ];
				}

				$loop++;
			}
		}

		// Chekc if it is empty
		if( empty( $export_content ) ) {
			$this->error->add( 'error', esc_html__( 'Export empty', 'geot' ) );
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
		
		fputcsv( $fh, array_values( $this->fields ) );
		
		foreach( $export_content as $content )
			fputcsv( $fh, array_values( $content ) );

		fclose( $fh );

		ob_end_flush();

		die();
	}


	/**
	 * Import save meta
	 * @param  array  $fields
	 * @return mixed
	 */
	public function import_items( $fields = [] ) {

		if( empty( $fields ) )
			return false;

		// Post Title
		$post_title = ! empty( $fields['post_title'] ) ? sanitize_text_field( $fields['post_title'] ) : esc_html__( 'GeoLinks Title Default', 'geot' );

		$post_name = ! empty( $fields['post_slug'] ) ? sanitize_title( $fields['post_slug'] ) : sanitize_title( $post_title );

		// Create Posts
		if( empty( $fields['post_id'] ) || empty( get_post( absint( $fields['post_id'] ) ) ) ) {

			$mode = 'insert';

			$args = [
				'post_title'	=> $post_title,
				'post_name'		=> $post_name,
				'post_type'		=> 'geol_cpt',
				'post_status'	=> 'publish',
			];

			$post_id = wp_insert_post( $args );
		
		} else {

			$mode = 'update';

			$args = [
				'ID'			=> absint( $fields['post_id'] ),
				'post_title'	=> $post_title,
			];

			$post_id = wp_update_post( $args );
		}


		$post 		= get_post( $post_id );
		$input		= geotWPL_options( $post_id );
		//$settings 	= geotWPL_settings();


		update_option('loop_31', print_r($fields,true));
		update_option('loop_32', print_r($input,true));

		// Source Slug
		$input['source_slug'] = $post->post_name == $fields['source_slug'] ? sanitize_title( $fields['source_slug'] ) : $post->post_name;

		// Status Code
		$input['status_code'] = ! empty( $fields['status_code'] ) && is_numeric( $fields['status_code'] ) ? sanitize_title( $fields['status_code'] ) : 302;

		// Dest Default
		$input['dest_default'] = ! empty( $input['dest_default'] ) ? esc_url_raw( $input['dest_default'] ) : '';

		// If it is new, the counter clicks are 0
		if( $mode == 'insert' ) {
			$input['count_click'] = 0;
			$input['click_default'] = 0;
		}


		// Destinations
		if( ! empty( $fields['dest'] ) ) {

			// Keys to delete
			$delete = array_diff( array_keys( $input['dest'] ), array_keys( $fields['dest'] ) );

			// Delete destinations keys
			foreach( $delete as $delete_key )
				unset( $input['dest'][ $delete_key ] );

			// Loop destination to update
			foreach( $fields['dest'] as $dest_key => $dest_data ) {

				if( empty( $dest_key ) )
					continue;

				// Counter click to 0 if it is a new destination
				if( ! isset( $input['dest'][ $dest_key ] ) )
					$input['dest'][ $dest_key ]['count_dest'] = 0;

				// Loop slug destination
				foreach( $dest_data as $slug => $value )
					$input['dest'][ $dest_key ][ $slug ] = $value;
			}
		}


		update_option('loop_33', print_r($input,true));

		$input = apply_filters( 'geol/import/options', $input, $post_id );

		// save box settings
		update_post_meta( $post_id, 'geol_options', $input );

		return $post_id;
	}
}

new GeotWP_Links_Importer();