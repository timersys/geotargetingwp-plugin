<?php

class Geot_Helper {

	private static $_user_is_targeted = [];
	private static $_geotarget_posts = [];

	/**
	 * Return geotarget posts
	 * @return array|null|object
	 */
	public static function get_geotarget_posts() {
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
		$country_remove = $state_remove = $city_remove = $zipcode_remove = false;
		$country_target = $state_target = $city_target = $zipcode_target = null;
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

		if ( ! empty( $opts['states'] ) ) {
			$states       = ! empty( $opts['states'] ) ? $opts['states'] : '';
			$state_target = geot_target_state( $states );

			if ( $mode == 'exclude' && $state_target ) {
				$state_remove = true;
			}
		}

		if ( ! empty( $opts['zipcodes'] ) ) {
			$zipcodes       = ! empty( $opts['zipcodes'] ) ? $opts['zipcodes'] : '';
			$zipcode_target = geot_target_zip( $zipcodes );

			if ( $mode == 'exclude' && $zipcode_target ) {
				$zipcode_remove = true;
			}
		}

		if ( $mode == 'include' ) {
			$_user_is_targeted = true;
			if ( ( $country_target || $state_target || $city_target || $zipcode_target ) ||
			     ( $country_target === null && $state_target === null && $city_target === null && $zipcode_target === null )
			) {
				$_user_is_targeted = false;
			}
		}

		if ( $mode == 'exclude' && ( $country_remove || $state_remove || $city_remove || $zipcode_remove ) ) {
			$_user_is_targeted = true;
		}

		return self::$_user_is_targeted[ $post_id ] = $_user_is_targeted;
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

		extract( $opts );

		if ( empty( $in_countries ) && empty( $ex_countries ) &&
		     count( $in_countries_regions ) == 0 && count( $ex_countries_regions ) == 0
		) {
			return true;
		}

		if ( geot_target( $in_countries, $in_countries_regions, $ex_countries, $ex_countries_regions ) ) {
			return true;
		}

		return false;
	}


	/**
	 * Check if user is matched in city
	 *
	 * @param $opts From metabox
	 *
	 * @return bool
	 */
	public static function is_targeted_city( $opts ) {

		extract( $opts );

		if ( empty( $in_cities ) && empty( $ex_cities ) &&
		     count( $in_cities_regions ) == 0 && count( $ex_cities_regions ) == 0
		) {
			return true;
		}


		if ( geot_target_city( $in_cities, $in_cities_regions, $ex_cities, $ex_cities_regions ) ) {
			return true;
		}

		return false;
	}


	/**
	 * Check if user is matched in state
	 *
	 * @param $opts From metabox
	 *
	 * @return bool
	 */
	public static function is_targeted_state( $opts ) {

		extract( $opts );

		if ( empty( $in_states ) && empty( $ex_states ) ) {
			return true;
		}


		if ( geot_target_state( $in_states, $ex_states ) ) {
			return true;
		}

		return false;
	}


	/**
	 * Check if user is matched in zipcode
	 *
	 * @param $opts From metabox
	 *
	 * @return bool
	 */
	public static function is_targeted_zipcode( $opts ) {

		extract( $opts );

		if ( empty( $in_zipcodes ) && empty( $ex_zipcodes ) ) {
			return true;
		}


		if ( geot_target_zip( $in_zipcodes, $ex_zipcodes ) ) {
			return true;
		}

		return false;
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

		$data = $input['geot_rules'];

		// clean array keys
		$groups = array_values( $data );
		unset( $data );

		foreach ( $groups as $group_id => $group ) {
			if ( is_array( $group ) ) {

				// clean array keys
				$groups_a[] = array_values( $group );
			}
		}

		/*
		foreach($groups as $group_id => $group ) {
			if( is_array($group) ) {

				$output_geot = [];
				$group_wkey = array_values( $group );

				foreach( $group_wkey as $item_key => $items ) {
					if( in_array($items['param'], $keys_geot) )
						$output_geot[] = $items;
					else
						$output_groups[$group_id][] = $items;
				}

				if( count($output_geot) > 0 ) {
					foreach($output_geot as $item_geot)
						$output_groups[$group_id][] = $item_geot;
				}
			}
		}
		*/

		update_post_meta( $post_id, $meta_key, apply_filters( 'geot/metaboxes/sanitized_rules', $groups_a, $meta_key ) );

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

		$groups = apply_filters( 'geot/metaboxes/get_rules', Geot_Helper::get_rules( $post->ID, $meta_key ), $post->ID );

		include GEOT_PLUGIN_DIR . '/admin/partials/metaboxes/rules.php';
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
}
