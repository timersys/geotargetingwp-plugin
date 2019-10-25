<?php

/**
 * Adds GeoTarget to categories
 * @since  1.8
 */
class GeotWP_Taxonomies {

	/**
	 * @since   1.6
	 * @access  private
	 * @var     Array of plugin settings
	 */
	private $opts;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @var      string $GeoTarget The name of this plugin.
	 * @var      string $version The version of this plugin.
	 */
	public function __construct() {

		$this->opts      = geot_settings();
		$this->geot_opts = geotwp_settings();

		// Categories only if ajax mode is disabled
		if ( empty( $this->opts['ajax_mode'] ) ) {
			add_action( 'edit_category_form_fields', [ $this, 'edit_category_fields' ], 10, 1 );
			add_action( 'edited_category', [ $this, 'save_category_fields' ], 10, 1 );
			add_action( 'pre_get_posts', [ $this, 'pre_get_posts' ], 10, 1 );
			add_action( 'pre_get_posts', [ $this, 'pre_get_categories' ], 10, 1 );
			add_action( 'get_terms', [ $this, 'get_terms' ], 10, 4 );

			// Woocommerce - Categories Products
			add_action( 'product_cat_edit_form_fields', [ $this, 'woo_edit_category_fields' ], 20 );
			add_action( 'edited_product_cat', [ $this, 'woo_save_category_fields' ], 10, 1 );
			add_action( 'woocommerce_product_query', [ $this, 'woo_pre_get_posts' ], 10, 1 );
		}
	}

	/**
	 * Render settings Category
	 * @since    1.0.0
	 * @param OBJECT $tag
	 */
	public function edit_category_fields( $tag ) {

		$extra = get_term_meta( $tag->term_id, 'geot', true );
		$geot  = geotwp_format( $extra );


		$regions_countries = wp_list_pluck( geot_country_regions(), 'name' );
		$regions_cities    = wp_list_pluck( geot_city_regions(), 'name' );

		include_once GEOWP_PLUGIN_DIR . 'admin/partials/metabox-category.php';
	}


	/**
	 * Save Settings Category
	 * @since    1.0.0
	 * @param INT $term_id
	 */
	public function save_category_fields( $term_id ) {
		if ( isset( $_POST['geot'] ) ) {

			$array_geot = geotwp_format( $_POST['geot'] );

			$without = array_filter( array_values( $array_geot ) );

			if ( ! empty( $without ) ) {
				update_term_meta( $term_id, 'geot', $array_geot );
			} else {
				delete_term_meta( $term_id, 'geot' );
			}
		}
	}


	/**
	 * Modify query to single post
	 * @since    1.0.0
	 * @param OBJECT $q
	 */
	public function pre_get_posts( $q ) {

		if ( is_admin() || ! $q->is_main_query() || ! $q->is_single )
			return;

		if( isset( $q->query['post_type'] ) && $q->query['post_type'] == 'product' ){
			$post = get_page_by_path( $q->query['name'], OBJECT, 'product' );
			$taxonomy = 'product_cat';
		} else {
			$post = get_page_by_path( $q->query['name'], OBJECT, 'post' );
			$taxonomy = 'category';
		}

		if( ! $post )
			return;

		$post_exclude = $q->get( 'post__not_in' );

		if( in_array($post->ID, $post_exclude) )
			return;

		$cats_ids = wp_get_object_terms($post->ID, $taxonomy, ['fields' => 'ids', 'geot' => true] );

		foreach ( $cats_ids as $term_id ) {
			$geot = get_term_meta( $term_id, 'geot', true );

			if ( ! $geot ) {
				continue;
			}

			if( ! $this->verify_geot($geot) ) {
				$post_exclude[] = $post->ID;
				break;
			}
		}


		if ( count( $post_exclude ) > 0 ) {
			$q->set( 'post__not_in', $post_exclude );
		}
	}


	/**
	 * Modify query to category
	 * @since    1.0.0
	 * @param OBJECT $q
	 */
	public function pre_get_categories( $q ) {

		if ( is_admin() || ! $q->is_main_query() || ! $q->is_category )
			return;

		if( ! isset( $q->query['category_name'] ) || empty( $q->query['category_name'] ) )
			return;
		
		$cat_exclude = [];
		$cats_ids    = get_categories( [ 'fields' => 'ids', 'geot' => true ] );

		foreach ( $cats_ids as $term_id ) {
			$geot = get_term_meta( $term_id, 'geot', true );

			if ( ! $geot )
				continue;

			if( ! $this->verify_geot($geot) )
				$cat_exclude[] = $term_id * ( - 1 );
		}

		if ( count( $cat_exclude ) > 0 ) {
			$q->set( 'cat', implode( ',', $cat_exclude ) );
		}

	}


	/**
	 * Get Terms Hook
	 * @since    1.0.0
	 * @param ARRAY $terms
	 * @param ARRAY $taxonomies
	 * @param ARRAY $args
	 * @param OBJECT $term_query
	 * @return ARRAY $terms
	 */
	public function get_terms( $terms, $taxonomies, $args, $term_query ) {

		if ( ! is_admin() && ! isset( $args['geot'] ) && is_array( $taxonomies ) &&
		     ( in_array( 'category', $taxonomies ) || in_array( 'product_cat', $taxonomies ) )
		) {
			foreach ( $terms as $id => $term ) {
				if ( ! isset( $term->term_id ) ) {
					continue;
				}

				$geot = get_term_meta( $term->term_id, 'geot', true );

				if ( ! $geot ) {
					continue;
				}

				if( ! $this->verify_geot($geot) )
					unset( $terms[ $id ] );
			}
		}

		return $terms;
	}


	/**
	 * Edit category thumbnail field.
	 * @since    1.0.0
	 * @param OBJECT $tag
	 */
	public function woo_edit_category_fields( $tag ) {
		$extra = get_term_meta( $tag->term_id, 'geot', true );
		$geot  = geotwp_format( $extra );

		$regions_countries = wp_list_pluck( geot_country_regions(), 'name' );
		$regions_cities    = wp_list_pluck( geot_city_regions(), 'name' );

		include_once GEOWP_PLUGIN_DIR . 'admin/partials/metabox-woo-category.php';
	}

	/**
	 * Save category fields
	 * @since    1.0.0
	 * @param int $term_id Term ID being saved.
	 */
	public function woo_save_category_fields( $term_id ) {

		if ( isset( $_POST['geot'] ) ) {

			$array_geot = geotwp_format( $_POST['geot'] );

			$without = array_filter( array_values( $array_geot ) );

			if ( ! empty( $without ) ) {
				update_term_meta( $term_id, 'geot', $array_geot );
			} else {
				delete_term_meta( $term_id, 'geot' );
			}
		}
	}

	/**
	 * Pre Get Post to Woocommerce
	 * @since    1.0.0
	 * @param OBJECT $q
	 */
	public function woo_pre_get_posts( $q ) {

		$cat_exclude = [];
		$cats_ids    = get_categories( [ 'fields' => 'ids', 'taxonomy' => 'product_cat', 'geot' => true ] );

		foreach ( $cats_ids as $term_id ) {
			$geot = get_term_meta( $term_id, 'geot', true );

			if ( ! $geot ) {
				continue;
			}

			if( ! $this->verify_geot($geot) )
				$cat_exclude[] = $term_id;
		}


		if ( count( $cat_exclude ) > 0 ) {

			$tax_query = (array) $q->get( 'tax_query' );

			$tax_query[] = [
				'taxonomy' => 'product_cat',
				'field'    => 'term_id',
				'terms'    => $cat_exclude,
				'operator' => 'NOT IN',
			];

			$q->set( 'tax_query', $tax_query );
		}
	}


	/**
	 * Verify if geotargeting
	 * @since    1.0.0
	 * @param ARRAY $geot
	 * @return boolean
	 */
	protected function verify_geot($geot) {
		$geot_country = $geot_city = $geot_state = $geot_zipcode = false;

		// Country
		if( ! empty( $geot['in_countries'] ) || ! empty( $geot['ex_countries'] ) ||
			! empty( $geot['in_countries_regions'] ) || ! empty( $geot['ex_countries_regions'] )
		) $geot_country = GeotWP_Helper::is_targeted_country( $geot );

		// City
		if( ! empty( $geot['in_cities'] ) || ! empty( $geot['ex_cities'] ) ||
			! empty( $geot['in_cities_regions'] ) || ! empty( $geot['ex_cities_regions'] )
		) $geot_city = GeotWP_Helper::is_targeted_city( $geot );

		// State
		if( ! empty( $geot['in_states'] ) || ! empty( $geot['ex_states'] ) )
			$geot_state = GeotWP_Helper::is_targeted_state( $geot );

		// Zipcode
		if( ! empty( $geot['in_zipcodes'] ) || ! empty( $geot['ex_zipcodes'] ) )
			$geot_zipcode = GeotWP_Helper::is_targeted_zipcode( $geot );

		// Verify
		if( $geot_country || $geot_city || $geot_state || $geot_zipcode )
			return true;

		return false;
	}
}
?>