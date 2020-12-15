<?php

/**
 * Grab geot settings
 * @return mixed|void
 */
function geotwp_settings() {
	return apply_filters( 'geot_pro/settings_page/opts', get_option( 'geot_pro_settings' ) );
}

/**
 * Get Geot Addons
 * @return ARRAY $opts
 */
function geotwp_addons() {
	$defaults = apply_filters( 'geot/addons/defaults', [
		'geo-flags'     => '0',
		'geo-links'     => '0',
		'geo-redirects' => '0',
		'geo-blocker'   => '0',
	] );
	$opts = get_option( 'geot_pro_addons' );
	$opts = geotwp_parse_args( $opts, $defaults );
	return apply_filters( 'geot_pro/settings_page/addons', $opts );
}

/**
 * Get Geot Stats
 * @return ARRAY $opts
 */
function geotwp_others() {
	$defaults = apply_filters( 'geot/others/defaults', [
		'geo-stats'     => 'no',
	] );
	$opts = get_option( 'geot_pro_others' );
	$opts = geotwp_parse_args( $opts, $defaults );
	return apply_filters( 'geot_pro/settings_page/others', $opts );
}


/**
 * Intercept Geot
 *
 * @param $geot
 *
 * @return mixed
 */
function geotwp_format( $geot ) {
	$output = [];
	foreach ( geotwp_default() as $key => $value ) {
		if ( isset( $geot[ $key ] ) ) {
			$output[ $key ] = is_array( $geot[ $key ] ) ? array_map( 'esc_html', $geot[ $key ] ) : esc_html( $geot[ $key ] );
		} else {
			$output[ $key ] = $value;
		}
	}

	return $output;
}

/**
 * @return mixed|void
 */
function geotwp_default() {
	$default = [
		'in_countries'         => '',
		'ex_countries'         => '',
		'in_countries_regions' => [],
		'ex_countries_regions' => [],
		'in_cities'            => '',
		'ex_cities'            => '',
		'in_cities_regions'    => [],
		'ex_cities_regions'    => [],
		'in_states'            => '',
		'ex_states'            => '',
		'in_states_regions'    => [],
		'ex_states_regions'    => [],
		'in_zipcodes'          => '',
		'ex_zipcodes'          => '',
		'in_zips_regions'      => [],
		'ex_zips_regions'      => [],
		'radius_km'            => '100',
		'radius_lat'           => '',
		'radius_lng'           => '',
	];

	return apply_filters( 'geot_pro/global/default', $default );
}


function geotwp_parse_args( &$a, $b ) {
	$a      = (array) $a;
	$b      = (array) $b;
	$result = $b;
	foreach ( $a as $k => &$v ) {
		if ( is_array( $v ) && isset( $result[ $k ] ) ) {
			$result[ $k ] = geotwp_parse_args( $v, $result[ $k ] );
		} else {
			$result[ $k ] = $v;
		}
	}

	return $result;
}

function geotwp_version_compare( $version1, $version2, $operator = null ) {
	$p        = '#(\.0+)+($|-)#';
	$version1 = preg_replace( $p, '', $version1 );
	$version2 = preg_replace( $p, '', $version2 );

	return isset( $operator ) ?
		version_compare( $version1, $version2, $operator ) :
		version_compare( $version1, $version2 );
}


/**
 * Geot SQL to upgrade
 * @param  ARRAY $args
 * @return mixed
 */
function geotwp_update_like($args = []) {
	global $wpdb;

	if( empty($args) || count($args) == 0 )
		return;	

	$ini_find 		= isset( $args['ini_find'] ) ? $args['ini_find'] : '';
	$ini_replace 	= isset( $args['ini_replace'] ) ? $args['ini_replace'] : '';
	$fin_find 		= isset( $args['fin_find'] ) ? $args['fin_find'] : '';
	$fin_replace 	= isset( $args['fin_replace'] ) ? $args['fin_replace'] : '';
	$like 			= isset( $args['like'] ) ? $args['like'] : '';
	$notlike 		= isset( $args['notlike'] ) ? $args['notlike'] : '';

	$update = 'UPDATE
					'.$wpdb->posts.'
				SET
					post_content = REPLACE(post_content, %s, %s),
					post_content = REPLACE(post_content, %s, %s)
				WHERE
					post_content LIKE "%s" AND
					post_content NOT LIKE "%s"';

	$query = $wpdb->prepare($update, $ini_find, $ini_replace, $fin_find, $fin_replace, $like, $notlike);
	
	$wpdb->query($query);
}

/**
 * Geot replace spaces by hyphen
 * @param  STRING $string
 * @return STRING
 */
function geotwp_spaces_by_hyphen($string) {

	return str_replace(' ', '-', $string);
}


if( ! function_exists('geot_dropdown') ) {
	/**
	 * Display Widget with flags
	 *
	 * @param $atts
	 *
	 * @return string
	 */
	function geot_dropdown( $atts ) {

		extract( shortcode_atts( [
			'regions' => '',
			'flags'   => 1,
		], $atts ) );

		$region_ids    = [];
		$flags_id      = 1;
		$saved_regions = geot_country_regions();
		$regions       = ! empty( $regions ) ? explode( ',', $regions ) : [];


		if ( ! empty( $flags ) ) {
			switch ( $flags ) {
				case 'yes' :
					$flags_id = 1;
					break;
				case 'no' :
					$flags_id = 2;
					break;
				default:
					$flags_id = 1;
			}
		}

		if ( ! empty( $regions ) && ! empty( $saved_regions ) ) {

			$all_regions = wp_list_pluck( $saved_regions, 'name' );

			foreach ( $regions as $nregion ) {

				if ( is_numeric( $nregion ) ) {
					$region_ids[] = (int) $nregion;
				} else {
					$region_ids[] = (int) array_search( $nregion, $all_regions );
				}
			}
		}

		$instance = [
			'flags'   => $flags_id,
			'regions' => $region_ids,
		];

		$args = [ 'before_widget' => '', 'after_widget' => '' ];


		ob_start();
		the_widget( 'GeotWP_Widget', $instance, $args );
		$output = ob_get_clean();

		return $output;
	}
}