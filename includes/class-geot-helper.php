<?php

class GeotWP_Helper {

	private static $_user_is_targeted = [];
	private static $_geotarget_posts = [];

	/**
	 * Return geotarget posts
	 *
	 * @param null $pid
	 *
	 * @return array|null|object
	 */
	public static function get_geotarget_posts( $pid = null ) {
		global $wpdb;

		if ( ! empty( self::$_geotarget_posts ) ) {
			return self::$_geotarget_posts;
		}

		$sql = "SELECT
					ID, pm2.meta_id as geot_meta_id,
					pm.meta_value as geot_countries,
					pm2.meta_value as geot_options
				FROM
					$wpdb->posts p
				LEFT JOIN
					$wpdb->postmeta pm
				ON
					p.ID = pm.post_id
				LEFT JOIN
					$wpdb->postmeta pm2 ON p.ID = pm2.post_id
				WHERE
					p.post_status = 'publish' AND pm.meta_key = '_geot_post' AND
					pm2.meta_key = 'geot_options' AND pm.meta_value != ''
				";
		if ( $pid ) {
			$sql .= " AND p.ID = " . filter_var( $pid, FILTER_VALIDATE_INT );
		}

		return self::$_geotarget_posts = $wpdb->get_results( $sql );
	}

	/**
	 * Check if user is matched
	 *
	 * @param $opts From post metabox
	 *
	 * @param $post_id
	 *
	 * @param bool $cache
	 *
	 * @return bool
	 */
	public static function user_is_targeted( $opts, $post_id, $cache = true ) {
		if ( isset( self::$_user_is_targeted[ $post_id ] ) && $cache ) {
			return self::$_user_is_targeted[ $post_id ];
		}

		$_user_is_targeted = false;

		$mode           = ! empty( $opts['geot_include_mode'] ) ? $opts['geot_include_mode'] : 'include';
		$country_remove = $state_remove = $city_remove = $zipcode_remove = $radius_remove = false;
		$country_target = $state_target = $city_target = $zipcode_target = $radius_target = null;
		if ( ! empty( $opts['country_code'] ) || ! empty( $opts['region'] ) ) {
			$countries      = ! empty( $opts['country_code'] ) ? $opts['country_code'] : '';
			$regions        = ! empty( $opts['region'] ) ? $opts['region'] : '';
			$country_target = geot_target( $countries, $regions );
			if ( $mode == 'exclude' && $country_target ) {
				$country_remove = true;
			}
		}

		if ( ! empty( $opts['cities'] ) || ! empty( $opts['city_region'] ) ) {
			$cities      = ! empty( $opts['cities'] ) ? $opts['cities'] : '';
			$regions     = ! empty( $opts['city_region'] ) ? $opts['city_region'] : '';
			$city_target = geot_target_city( $cities, $regions );
			if ( $mode == 'exclude' && $city_target ) {
				$city_remove = true;
			}
		}

		if ( ! empty( $opts['states'] ) || ! empty( $opts['state_region'] ) ) {
			$states 	= ! empty( $opts['states'] ) ? $opts['states'] : '';
			$regions 	= ! empty( $opts['state_region'] ) ? $opts['state_region'] : '';
			$state_target = geot_target_state( $states, $regions );

			if ( $mode == 'exclude' && $state_target ) {
				$state_remove = true;
			}
		}

		if ( ! empty( $opts['zipcodes'] ) || ! empty( $opts['zip_region'] ) ) {
			$zipcodes       = ! empty( $opts['zipcodes'] ) ? $opts['zipcodes'] : '';
			$regions        = ! empty( $opts['zip_region'] ) ? $opts['zip_region'] : '';
			$zipcode_target = geot_target_zip( $zipcodes, $regions );

			if ( $mode == 'exclude' && $zipcode_target ) {
				$zipcode_remove = true;
			}
		}

		if ( ! empty( $opts['radius_lat'] ) && ! empty( $opts['radius_lng'] ) && (int) $opts['radius_km'] > 0 ) {

			$radius_target = geot_target_radius( $opts['radius_lat'], $opts['radius_lng'], (int) $opts['radius_km'] );

			if ( $mode == 'exclude' && $radius_target ) {
				$radius_remove = true;
			}
		}

		if ( $mode == 'include' ) {
			$_user_is_targeted = true;
			if ( ( $country_target || $state_target || $city_target || $zipcode_target || $radius_target ) ||
			     ( $country_target === null && $state_target === null && $city_target === null && $zipcode_target === null && $radius_target === null )
			) {
				$_user_is_targeted = false;
			}
		}

		if ( $mode == 'exclude' && ( $country_remove || $state_remove || $city_remove || $zipcode_remove || $radius_remove ) ) {
			$_user_is_targeted = true;
		}

		return apply_filters( 'geot/helper/is_targeted', self::$_user_is_targeted[ $post_id ] = $_user_is_targeted, $opts, $post_id, $cache );
	}

	/**
	 * Get post meta option
	 *
	 * @param int $post_id [description]
	 *
	 * @return array
	 */
	public static function get_cpt_options( $post_id ) {

		$opts = get_post_meta( $post_id, 'geot_options', true );
		if ( ! $opts ) {
			return [];
		}

		return $opts;
	}

	/**
	 * Return available posts types. Used in filters
	 *
	 * @param array $exclude cpt to explude
	 * @param array $include cpts to include
	 *
	 * @return array  Resulting cpts
	 */
	public static function get_post_types( $exclude = [], $include = [] ) {

		// get all custom post types
		$post_types = get_post_types();

		// core include / exclude
		$spu_includes = array_merge( [], $include );
		$spu_excludes = array_merge( [ 'spucpt', 'acf', 'revision', 'nav_menu_item', 'attachment' ], $exclude );

		// include
		foreach ( $spu_includes as $p ) {
			if ( post_type_exists( $p ) ) {
				$post_types[ $p ] = $p;
			}
		}

		// exclude
		foreach ( $spu_excludes as $p ) {
			unset( $post_types[ $p ] );
		}

		return $post_types;
	}


	/**
	 * Check if user is matched in country
	 *
	 * @param $opts From metabox
	 *
	 * @return bool
	 */
	public static function is_targeted_country( $opts ) {

		$in_countries = isset( $opts['in_countries'] ) ? $opts['in_countries'] : '';
		$ex_countries = isset( $opts['ex_countries'] ) ? $opts['ex_countries'] : '';

		$in_countries_regions = isset( $opts['in_countries_regions'] ) ? $opts['in_countries_regions'] : [];
		$ex_countries_regions = isset( $opts['ex_countries_regions'] ) ? $opts['ex_countries_regions'] : [];

		if ( empty( $in_countries ) && empty( $ex_countries ) &&
		     count( $in_countries_regions ) == 0 && count( $ex_countries_regions ) == 0
		) {
			return true;
		}

		return geot_target( $in_countries, $in_countries_regions, $ex_countries, $ex_countries_regions );

	}


	/**
	 * Check if user is matched in city
	 *
	 * @param $opts From metabox
	 *
	 * @return bool
	 */
	public static function is_targeted_city( $opts ) {

		$in_cities = isset( $opts['in_cities'] ) ? $opts['in_cities'] : '';
		$ex_cities = isset( $opts['ex_cities'] ) ? $opts['ex_cities'] : '';

		$in_cities_regions = isset( $opts['in_cities_regions'] ) ? $opts['in_cities_regions'] : [];
		$ex_cities_regions = isset( $opts['ex_cities_regions'] ) ? $opts['ex_cities_regions'] : [];

		if ( empty( $in_cities ) && empty( $ex_cities ) &&
		     count( $in_cities_regions ) == 0 && count( $ex_cities_regions ) == 0
		) {
			return true;
		}

		return geot_target_city( $in_cities, $in_cities_regions, $ex_cities, $ex_cities_regions );

	}


	/**
	 * Check if user is matched in state
	 *
	 * @param $opts From metabox
	 *
	 * @return bool
	 */
	public static function is_targeted_state( $opts ) {

		$in_states = isset( $opts['in_states'] ) ? $opts['in_states'] : '';
		$ex_states = isset( $opts['ex_states'] ) ? $opts['ex_states'] : '';

		$in_states_regions = isset( $opts['in_states_regions'] ) ? $opts['in_states_regions'] : [];
		$ex_states_regions = isset( $opts['ex_states_regions'] ) ? $opts['ex_states_regions'] : [];


		if ( empty( $in_states ) && empty( $ex_states ) &&
			count( $in_states_regions ) == 0 && count( $ex_states_regions ) == 0
		) {
			return true;
		}

		return geot_target_state( $in_states, $in_states_regions, $ex_states, $ex_states_regions );
	}


	/**
	 * Check if user is matched in zipcode
	 *
	 * @param $opts From metabox
	 *
	 * @return bool
	 */
	public static function is_targeted_zipcode( $opts ) {

		$in_zipcodes = isset( $opts['in_zipcodes'] ) ? $opts['in_zipcodes'] : '';
		$ex_zipcodes = isset( $opts['ex_zipcodes'] ) ? $opts['ex_zipcodes'] : '';

		$in_zips_regions = isset( $opts['in_zips_regions'] ) ? $opts['in_zips_regions'] : [];
		$ex_zips_regions = isset( $opts['ex_zips_regions'] ) ? $opts['ex_zips_regions'] : [];


		if ( empty( $in_zipcodes ) && empty( $ex_zipcodes ) &&
		     count( $in_zips_regions ) == 0 && count( $ex_zips_regions ) == 0
		) {
			return true;
		}

		return geot_target_zip( $in_zipcodes, $in_zips_regions, $ex_zipcodes, $ex_zips_regions );
	}


	/************************************** RULES ********************************************/


	/**
	 *  ajax_render_operator
	 *
	 * @description creates the HTML for the field group operator metabox. Called from both Ajax and PHP
	 * @since 1.0.0
	 *  I took these functions from the awesome Advanced custom fields plugin http://www.advancedcustomfields.com/ and modified for my plugin
	 */
	public static function ajax_render_operator( $options = [] ) {
		// defaults
		$defaults = [
			'group_id' => 0,
			'rule_id'  => 0,
			'value'    => null,
			'param'    => null,
		];

		$is_ajax = false;

		if ( isset( $_POST['nonce'] ) && wp_verify_nonce( $_POST['nonce'], 'geot_nonce' ) ) {
			$is_ajax = true;
		}

		// Is AJAX call?
		if ( $is_ajax ) {
			$options         = array_merge( $defaults, $_POST );
			$options['name'] = 'geot_rules[' . $options['group_id'] . '][' . $options['rule_id'] . '][operator]';
		} else {
			$options = array_merge( $defaults, $options );
		}


		// default for all rules
		$choices = [
			'==' => __( "is equal to", 'geot' ),
			'!=' => __( "is not equal to", 'geot' ),
		];

		if ( $options['param'] == 'local_time' ) {
			$choices = [
				'<' => __( "less than", 'geot' ),
				'>' => __( "greater than", 'geot' ),
			];
		}

		if ( $options['param'] == 'radius' ) {
			$choices = [
				'inside'	=> __( "Inside", 'geot' ),
				'outside'	=> __( "Outside", 'geot' ),
			];
		}

		// allow custom operators
		$choices = apply_filters( 'geot/metaboxes/rule_operators', $choices, $options );

		self::print_select( $options, $choices );

		// ajax?
		if ( $is_ajax ) {
			die();
		}
	}

	/**
	 * Helper function to print select fields for rules
	 *
	 * @param array $choices options values for select
	 * @param array $options array to pass group, id, rule_id etc
	 *
	 * @return echo  the select field
	 * @since  2.0
	 */
	static function print_select( $options, $choices ) {

		// value must be array
		if ( ! is_array( $options['value'] ) ) {
			// perhaps this is a default value with new lines in it?
			if ( strpos( $options['value'], "\n" ) !== false ) {
				// found multiple lines, explode it
				$options['value'] = explode( "\n", $options['value'] );
			} else {
				$options['value'] = [ $options['value'] ];
			}
		}

		// trim value
		$options['value'] = array_map( 'trim', $options['value'] );

		// determin if choices are grouped (2 levels of array)
		if ( is_array( $choices ) ) {
			foreach ( $choices as $k => $v ) {
				if ( is_array( $v ) ) {
					$optgroup = true;
				}
			}
		}

		echo '<select id="geot_rule_' . $options['group_id'] . '_rule_' . $options['rule_id'] . '" class="select" name="' . $options['name'] . '">';

		// loop through values and add them as options
		if ( is_array( $choices ) ) {
			foreach ( $choices as $key => $value ) {
				if ( isset( $optgroup ) ) {

					// this select is grouped with optgroup
					if ( $key != '' ) {
						echo '<optgroup label="' . $key . '">';
					}

					if ( is_array( $value ) ) {
						foreach ( $value as $id => $label ) {

							$selected = in_array( $id, $options['value'] ) ? 'selected="selected"' : '';
							echo '<option value="' . $id . '" ' . $selected . '>' . $label . '</option>';
						}
					}

					if ( $key != '' ) {
						echo '</optgroup>';
					}
				} else {
					$selected = in_array( $key, $options['value'] ) ? 'selected="selected"' : '';
					echo '<option value="' . $key . '" ' . $selected . '>' . $value . '</option>';
				}
			}
		}

		echo '</select>';
	}

	/**
	 *  ajax_render_rules
	 *
	 * @description creates the HTML for the field group rules metabox. Called from both Ajax and PHP
	 * @since 2.0
	 *  I took this functions from the awesome Advanced custom fields plugin http://www.advancedcustomfields.com/
	 */

	public static function ajax_render_rules( $options = [] ) {

		// defaults
		$defaults = [
			'group_id' => 0,
			'rule_id'  => 0,
			'value'    => null,
			'param'    => null,
		];

		$is_ajax = false;

		if ( isset( $_POST['nonce'] ) && wp_verify_nonce( $_POST['nonce'], 'geot_nonce' ) ) {
			$is_ajax = true;
		}

		if ( is_array( $options ) ) {
			$options = array_merge( $defaults, $options );
		}

		// Is AJAX call?
		if ( $is_ajax ) {
			$options         = array_merge( $defaults, $_POST );
			$options['name'] = 'geot_rules[' . $options['group_id'] . '][' . $options['rule_id'] . '][value]';
		}

		// vars
		$choices = [];

		// some case's have the same outcome
		if ( $options['param'] == "page_parent" ) {
			$options['param'] = "page";
		}


		switch ( $options['param'] ) {
			case "country":
				$countries = geot_countries();
				foreach ( $countries as $c ) {
					$choices[ $c->iso_code ] = $c->country;
				}
				break;
			case "country_region":
				$regions = geot_country_regions();
				foreach ( $regions as $r ) {
					$choices[ $r['name'] ] = $r['name'];
				}
				break;
			case "city_region":
				$regions = geot_city_regions();
				foreach ( $regions as $r ) {
					$choices[ $r['name'] ] = $r['name'];
				}
				break;
			case "state_region":
				$regions = geot_state_regions();
				foreach ( $regions as $r ) {
					$choices[ $r['name'] ] = $r['name'];
				}
				break;
			case "zip_region":
				$regions = geot_zip_regions();
				foreach ( $regions as $r ) {
					$choices[ $r['name'] ] = $r['name'];
				}
				break;
			case "post_type":
				// all post types except attachment
				$choices = apply_filters( 'geot/get_post_types', get_post_types(), [ 'attachment' ] );
				break;

			case "page":
				$post_type = 'page';
				$args      = [
					'posts_per_page'         => - 1,
					'post_type'              => $post_type,
					'orderby'                => 'menu_order title',
					'order'                  => 'ASC',
					'post_status'            => 'any',
					'suppress_filters'       => false,
					'update_post_meta_cache' => false,
				];

				$posts = get_posts( apply_filters( 'geot/rules/page_args', $args ) );

				if ( $posts ) {
					// sort into hierachial order!
					if ( is_post_type_hierarchical( $post_type ) ) {
						$posts = get_page_children( 0, $posts );
					}

					foreach ( $posts as $page ) {
						$title     = '';
						$ancestors = get_ancestors( $page->ID, 'page' );

						if ( $ancestors ) {
							foreach ( $ancestors as $a ) {
								$title .= '- ';
							}
						}

						$title .= apply_filters( 'the_title', $page->post_title, $page->ID );

						// status
						if ( $page->post_status != "publish" ) {
							$title .= " ($page->post_status)";
						}

						$choices[ $page->ID ] = $title;
					}
				}
				break;

			case "page_type" :

				$choices = [
					'all_pages'     => __( "All Pages", 'geot' ),
					'front_page'    => __( "Front Page", 'geot' ),
					'posts_page'    => __( "Posts Page", 'geot' ) . ' *',
					'category_page' => __( "Category Page", 'geot' ) . ' *',
					'search_page'   => __( "Search Page", 'geot' ) . ' *',
					'archive_page'  => __( "Archives Page", 'geot' ) . ' *',
					'top_level'     => __( "Top Level Page (parent of 0)", 'geot' ),
					'parent'        => __( "Parent Page (has children)", 'geot' ),
					'child'         => __( "Child Page (has parent)", 'geot' ),
				];

				break;

			case "page_template" :

				$choices = [ 'default' => __( "Default Template", 'geot' ), ];

				$templates = get_page_templates();
				foreach ( $templates as $k => $v ) {
					$choices[ $v ] = $k;
				}

				break;

			case "post" :

				$post_types = get_post_types();

				unset( $post_types['page'], $post_types['attachment'], $post_types['revision'], $post_types['nav_menu_item'] );

				foreach ( apply_filters( 'geot/exclude/post_types', [] ) as $ptype ) {
					if ( isset( $post_types[ $ptype ] ) ) {
						unset( $post_types[ $ptype ] );
					}
				}

				if ( $post_types ) {
					foreach ( $post_types as $post_type ) {
						$args = [
							'numberposts'      => '-1',
							'post_type'        => $post_type,
							'post_status'      => [ 'publish', 'private', 'draft', 'inherit', 'future' ],
							'suppress_filters' => false,
						];

						$posts = get_posts( apply_filters( 'geot/rules/post_args', $args ) );

						if ( $posts ) {
							$choices[ $post_type ] = [];

							foreach ( $posts as $post ) {
								$title = apply_filters( 'the_title', $post->post_title, $post->ID );

								// status
								if ( $post->post_status != "publish" ) {
									$title .= " ($post->post_status)";
								}

								$choices[ $post_type ][ $post->ID ] = $title;
							}
							// foreach($posts as $post)
						}
						// if( $posts )
					}
					// foreach( $post_types as $post_type )
				}
				// if( $post_types )
				break;

			case "post_category" :

				$categories = get_terms( 'category', [ 'get' => 'all', 'fields' => 'id=>name' ] );
				$choices    = apply_filters( 'geot/rules/categories', $categories );
				break;

			case "post_format" :

				$choices = get_post_format_strings();
				break;

			case "post_status" :

				$choices = get_post_stati();
				break;

			case "user_type" :

				global $wp_roles;

				$choices = $wp_roles->get_names();

				if ( is_multisite() ) {
					$choices['super_admin'] = __( 'Super Admin' );
				}

				break;

			case "taxonomy" :
				$choices = apply_filters( 'geot/get_taxonomies', self::get_taxonomies() );
				break;

			case "logged_user" :
			case "mobiles" :
			case "tablets" :
			case "desktop" :
			case "crawlers" :
			case "left_comment" :
			case "search_engine" :
			case "same_site" :
				$choices = [ 'true' => __( 'True', 'geot' ) ];
				break;
		}


		// allow custom rules rules
		$choices = apply_filters( 'geot/rules/rule_values/' . $options['param'], $choices );

		// Custom fields for rules
		do_action( 'geot/rules/print_' . $options['param'] . '_field', $options, $choices );

		// ajax?
		if ( $is_ajax ) {
			die();
		}
	}

	/**
	 * Get taxonomies. Used in filters rules
	 *
	 * @param boolean $simple_value [description]
	 *
	 * @return array [type]                [description]
	 */
	public static function get_taxonomies( $simple_value = true ) {
		$choices = [];

		// vars
		$post_types = get_post_types();

		if ( $post_types ) {
			foreach ( $post_types as $post_type ) {
				$post_type_object = get_post_type_object( $post_type );
				$taxonomies       = get_object_taxonomies( $post_type );

				if ( $taxonomies ) {
					foreach ( $taxonomies as $taxonomy ) {
						if ( 'nav_menu' == $taxonomy ) {
							continue;
						}

						$terms = get_terms( $taxonomy, [ 'hide_empty' => true ] );

						if ( $terms ) {
							foreach ( $terms as $term ) {
								$value = $taxonomy . ':' . $term->term_id;

								if ( $simple_value ) {
									$value = $term->term_id;
								}

								$choices[ $post_type_object->label . ': ' . $taxonomy ][ $value ] = $term->name;
							}
						}
					}
				}
			}
		}

		return $choices;
	}

	/**
	 * Prints a text field rule
	 *
	 * @param $options
	 */
	static function print_textfield( $options ) {
		echo '<input type="text" name="' . $options['name'] . '" value="' . $options['value'] . '" id="geot_rule_' . $options['group_id'] . '_rule_' . $options['rule_id'] . '" />';
	}

	/**
	 * Return true
	 *
	 * @param array $data all the rules
	 *
	 * @return true
	 * @since  1.0.0
	 */
	public static function save_rules( $post_id, $input, $meta_key = 'geot_rules' ) {

		if ( ! isset( $input['geot_rules'] ) || ! is_array( $input['geot_rules'] ) ) {
			return;
		}

		$keys_geot = apply_filters( 'geot/metaboxes/keys_geot', [
			'country',
			'country_region',
			'city',
			'city_region',
			'state',
			'state_region',
			'zip',
			'zip_region',
		] );


		$data = $input['geot_rules'];

		// clean array keys
		$groups = array_values( $data );
		unset( $data );

		// Ordering the Rulers
		foreach ( $groups as $group_id => $group ) {
			if ( is_array( $group ) ) {

				$output_geot = [];
				$group_wkey  = array_values( $group );

				foreach ( $group_wkey as $item_key => $items ) {
					if ( in_array( $items['param'], $keys_geot ) ) {
						$output_geot[] = $items;
					} else {
						$output_groups[ $group_id ][] = $items;
					}
				}

				if ( count( $output_geot ) > 0 ) {
					foreach ( $output_geot as $item_geot ) {
						$output_groups[ $group_id ][] = $item_geot;
					}
				}
			}
		}

		update_post_meta( $post_id, $meta_key, apply_filters( 'geot/metaboxes/sanitized_rules', $output_groups, $meta_key ) );

		return true;
	}

	public static function html_rules( $post, $meta_key, $args = [] ) {

		$params = wp_parse_args(
			$args,
			[
				'title' => __( "Show if", 'geot' ),
				'desc'  => __( 'Create a set of rules', 'geot' ),
			]
		);

		$groups = apply_filters( 'geot/metaboxes/get_rules', GeotWP_Helper::get_rules( $post->ID, $meta_key ), $post->ID );

		include GEOWP_PLUGIN_DIR . '/admin/partials/metaboxes/rules.php';
	}

	/**
	 * Return the redirection rules
	 *
	 * @param int $id
	 *
	 * @return array metadata values
	 * @since  1.0.0
	 */
	public static function get_rules( $id, $meta_key = 'geot_rules' ) {
		$defaults = [
			// group_0
			[
				// rule_0
				[
					'param'    => 'page_type',
					'operator' => '==',
					'value'    => 'all_pages',
					'order_no' => 0,
					'group_no' => 0,
				],
			],
		];

		$rules = get_post_meta( $id, $meta_key, true );

		if ( empty( $rules ) ) {
			return apply_filters( 'geot/metaboxes/default_rules', $defaults );
		}

		return $rules;
	}

	/**
	 * Check if ip or range exist in array and match the
	 * user ip so we can exclude it
	 *
	 * @param $ip
	 * @param array $ips_array
	 *
	 * @return bool
	 */
	public static function checkIP( $ip, array $ips_array ) {
		// basic check
		if ( in_array( $ip, $ips_array ) ) {
			return true;
		}
		// ranges check
		foreach ( $ips_array as $ip_to_check ) {
			if ( self::ip_in_range( $ip, $ip_to_check ) ) {
				return true;
			}
		}

		return false;
	}
	/*
 * ip_in_range.php - Function to determine if an IP is located in a
 *                   specific range as specified via several alternative
 *                   formats.
 *
 * Network ranges can be specified as:
 * 1. Wildcard format:     1.2.3.*
 * 2. CIDR format:         1.2.3/24  OR  1.2.3.4/255.255.255.0
 * 3. Start-End IP format: 1.2.3.0-1.2.3.255
 *
 * Return value BOOLEAN : ip_in_range($ip, $range);
 *
 * Copyright 2008: Paul Gregg <pgregg@pgregg.com>
 * 10 January 2008
 * Version: 1.2
 *
 * Source website: http://www.pgregg.com/projects/php/ip_in_range/
 * Version 1.2
 *
 * This software is Donationware - if you feel you have benefited from
 * the use of this tool then please consider a donation. The value of
 * which is entirely left up to your discretion.
 * http://www.pgregg.com/donate/
 *
 * Please do not remove this header, or source attibution from this file.
 * Modified by James Greene <james@cloudflare.com> to include IPV6 support
* (original version only supported IPV4).
* 21 May 2012
*/
// decbin32
// In order to simplify working with IP addresses (in binary) and their
// netmasks, it is easier to ensure that the binary strings are padded
// with zeros out to 32 characters - IP addresses are 32 bit numbers
	private static function decbin32( $dec ) {
		return str_pad( decbin( $dec ), 32, '0', STR_PAD_LEFT );
	}
// ipv4_in_range
// This function takes 2 arguments, an IP address and a "range" in several
// different formats.
// Network ranges can be specified as:
// 1. Wildcard format:     1.2.3.*
// 2. CIDR format:         1.2.3/24  OR  1.2.3.4/255.255.255.0
// 3. Start-End IP format: 1.2.3.0-1.2.3.255
// The function will return true if the supplied IP is within the range.
// Note little validation is done on the range inputs - it expects you to
// use one of the above 3 formats.
	private static function ipv4_in_range( $ip, $range ) {
		if ( strpos( $range, '/' ) !== false ) {
			// $range is in IP/NETMASK format
			list( $range, $netmask ) = explode( '/', $range, 2 );
			if ( strpos( $netmask, '.' ) !== false ) {
				// $netmask is a 255.255.0.0 format
				$netmask     = str_replace( '*', '0', $netmask );
				$netmask_dec = ip2long( $netmask );

				return ( ( ip2long( $ip ) & $netmask_dec ) == ( ip2long( $range ) & $netmask_dec ) );
			} else {
				// $netmask is a CIDR size block
				// fix the range argument
				$x = explode( '.', $range );
				while ( count( $x ) < 4 ) {
					$x[] = '0';
				}
				list( $a, $b, $c, $d ) = $x;
				$range     = sprintf( "%u.%u.%u.%u", empty( $a ) ? '0' : $a, empty( $b ) ? '0' : $b, empty( $c ) ? '0' : $c, empty( $d ) ? '0' : $d );
				$range_dec = ip2long( $range );
				$ip_dec    = ip2long( $ip );

				# Strategy 1 - Create the netmask with 'netmask' 1s and then fill it to 32 with 0s
				#$netmask_dec = bindec(str_pad('', $netmask, '1') . str_pad('', 32-$netmask, '0'));

				# Strategy 2 - Use math to create it
				$wildcard_dec = pow( 2, ( 32 - $netmask ) ) - 1;
				$netmask_dec  = ~$wildcard_dec;

				return ( ( $ip_dec & $netmask_dec ) == ( $range_dec & $netmask_dec ) );
			}
		} else {
			// range might be 255.255.*.* or 1.2.3.0-1.2.3.255
			if ( strpos( $range, '*' ) !== false ) { // a.b.*.* format
				// Just convert to A-B format by setting * to 0 for A and 255 for B
				$lower = str_replace( '*', '0', $range );
				$upper = str_replace( '*', '255', $range );
				$range = "$lower-$upper";
			}

			if ( strpos( $range, '-' ) !== false ) { // A-B format
				list( $lower, $upper ) = explode( '-', $range, 2 );
				$lower_dec = (float) sprintf( "%u", ip2long( $lower ) );
				$upper_dec = (float) sprintf( "%u", ip2long( $upper ) );
				$ip_dec    = (float) sprintf( "%u", ip2long( $ip ) );

				return ( ( $ip_dec >= $lower_dec ) && ( $ip_dec <= $upper_dec ) );
			}

			return false;
		}
	}

	private static function ip2long6( $ip ) {
		if ( substr_count( $ip, '::' ) ) {
			$ip = str_replace( '::', str_repeat( ':0000', 8 - substr_count( $ip, ':' ) ) . ':', $ip );
		}

		$ip   = explode( ':', $ip );
		$r_ip = '';
		foreach ( $ip as $v ) {
			$r_ip .= str_pad( base_convert( $v, 16, 2 ), 16, 0, STR_PAD_LEFT );
		}

		return base_convert( $r_ip, 2, 10 );
	}

// Get the ipv6 full format and return it as a decimal value.
	private static function get_ipv6_full( $ip ) {
		$pieces      = explode( "/", $ip, 2 );
		$left_piece  = $pieces[0];
		// Extract out the main IP pieces
		$ip_pieces     = explode( "::", $left_piece, 2 );
		$main_ip_piece = isset($ip_pieces[0]) ? $ip_pieces[0] : '';
		$last_ip_piece = isset($ip_pieces[1]) ? $ip_pieces[1] : '';
		// Pad out the shorthand entries.
		$main_ip_pieces = explode( ":", $main_ip_piece );
		foreach ( $main_ip_pieces as $key => $val ) {
			$main_ip_pieces[ $key ] = str_pad( $main_ip_pieces[ $key ], 4, "0", STR_PAD_LEFT );
		}
		// Check to see if the last IP block (part after ::) is set
		$last_piece = "";
		$size       = count( $main_ip_pieces );
		if ( trim( $last_ip_piece ) != "" ) {
			$last_piece = str_pad( $last_ip_piece, 4, "0", STR_PAD_LEFT );

			// Build the full form of the IPV6 address considering the last IP block set
			for ( $i = $size; $i < 7; $i ++ ) {
				$main_ip_pieces[ $i ] = "0000";
			}
			$main_ip_pieces[7] = $last_piece;
		} else {
			// Build the full form of the IPV6 address
			for ( $i = $size; $i < 8; $i ++ ) {
				$main_ip_pieces[ $i ] = "0000";
			}
		}

		// Rebuild the final long form IPV6 address
		$final_ip = implode( ":", $main_ip_pieces );

		return self::ip2long6( $final_ip );
	}
// Determine whether the IPV6 address is within range.
// $ip is the IPV6 address in decimal format to check if its within the IP range created by the cloudflare IPV6 address, $range_ip.
// $ip and $range_ip are converted to full IPV6 format.
// Returns true if the IPV6 address, $ip,  is within the range from $range_ip.  False otherwise.
	private static function ipv6_in_range( $ip, $range_ip ) {
		$pieces      = explode( "/", $range_ip, 2 );
		$left_piece  = $pieces[0];
		// Extract out the main IP pieces
		$ip_pieces     = explode( "::", $left_piece, 2 );
		$main_ip_piece = isset($ip_pieces[0]) ? $ip_pieces[0] : '';
		$last_ip_piece = isset($ip_pieces[1]) ? $ip_pieces[1] : '';
		// Pad out the shorthand entries.
		$main_ip_pieces = explode( ":", $main_ip_piece );
		foreach ( $main_ip_pieces as $key => $val ) {
			$main_ip_pieces[ $key ] = str_pad( $main_ip_pieces[ $key ], 4, "0", STR_PAD_LEFT );
		}
		// Create the first and last pieces that will denote the IPV6 range.
		$first = $main_ip_pieces;
		$last  = $main_ip_pieces;
		// Check to see if the last IP block (part after ::) is set
		$last_piece = "";
		$size       = count( $main_ip_pieces );
		if ( trim( $last_ip_piece ) != "" ) {
			$last_piece = str_pad( $last_ip_piece, 4, "0", STR_PAD_LEFT );

			// Build the full form of the IPV6 address considering the last IP block set
			for ( $i = $size; $i < 7; $i ++ ) {
				$first[ $i ] = "0000";
				$last[ $i ]  = "ffff";
			}
			$main_ip_pieces[7] = $last_piece;
		} else {
			// Build the full form of the IPV6 address
			for ( $i = $size; $i < 8; $i ++ ) {
				$first[ $i ] = "0000";
				$last[ $i ]  = "ffff";
			}
		}
		// Rebuild the final long form IPV6 address
		$first    = self::ip2long6( implode( ":", $first ) );
		$last     = self::ip2long6( implode( ":", $last ) );
		$in_range = ( $ip >= $first && $ip <= $last );

		return $in_range;
	}

	private static function ip_range_type( $range_ip ) {
		if ( strpos( $range_ip, ':' ) !== false ) {
			//if ip range provided is ipv6 then
			//echo "range provided is ipv6";
			return 1;
		} else {
			//if ip range provided is ipv4 then
			//echo "range provided is ipv4";
			return 2;
		}
	}

	public static function ip_in_range( $ip, $range_ip ) {
		//only check IPv4 addresses against IPv4 ranges
		if ( filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 ) && self::ip_range_type( $range_ip ) == 2 ) {
			return self::ipv4_in_range( $ip, $range_ip );
		}
		//only check IPv6 addresses against IPv6 ranges
		if ( filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6 ) && self::ip_range_type( $range_ip ) == 1 ) {
			return self::ipv6_in_range( $ip, $range_ip );
		}
	}
}
