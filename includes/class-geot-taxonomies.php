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
			add_action( 'get_terms', [ $this, 'get_terms' ], 10, 4 );

			// Woocommerce - Categories Products
			add_action( 'product_cat_edit_form_fields', [ $this, 'woo_edit_category_fields' ], 20 );
			add_action( 'edited_product_cat', [ $this, 'woo_save_category_fields' ], 10, 1 );
			add_action( 'woocommerce_product_query', [ $this, 'woo_pre_get_posts' ], 10, 1 );
		}
	}

	/**
	 * Render settings Category
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
	 * Pre Get Post
	 */
	public function pre_get_posts( $q ) {

		if ( ! is_admin() && $q->is_main_query() &&
		     isset( $q->query['category_name'] ) && ! empty( $q->query['category_name'] )
		) {
			$cat_exclude = [];
			$cats_ids    = get_categories( [ 'fields' => 'ids', 'geot' => true ] );

			foreach ( $cats_ids as $term_id ) {
				$geot = get_term_meta( $term_id, 'geot', true );

				if ( ! $geot ) {
					continue;
				}

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

				// Exclude ID
				if( ! $geot_country && ! $geot_city && ! $geot_state && ! $geot_zipcode )
					$cat_exclude[] = $term_id * ( - 1 );
			}

			if ( count( $cat_exclude ) > 0 ) {
				$q->set( 'cat', implode( ',', $cat_exclude ) );
			}
		}
	}


	/**
	 * Get Terms Hook
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

				// Exclude ID
				if( ! $geot_country && ! $geot_city && ! $geot_state && ! $geot_zipcode )
					unset( $terms[ $id ] );
			}
		}

		return $terms;
	}


	/**
	 * Edit category thumbnail field.
	 *
	 * @param mixed $term Term (category) being edited.
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
	 *
	 * @param mixed $term_id Term ID being saved.
	 * @param mixed $tt_id Term taxonomy ID.
	 * @param string $taxonomy Taxonomy slug.
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
	 */
	public function woo_pre_get_posts( $q ) {

		$cat_exclude = [];
		$cats_ids    = get_categories( [ 'fields' => 'ids', 'taxonomy' => 'product_cat', 'geot' => true ] );

		foreach ( $cats_ids as $term_id ) {
			$geot = get_term_meta( $term_id, 'geot', true );

			if ( ! $geot ) {
				continue;
			}

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

			// Exclude ID
			if( ! $geot_country && ! $geot_city && ! $geot_state && ! $geot_zipcode )
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
}

?>