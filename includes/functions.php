<?php

/**
 * Grab geot settings
 * @return mixed|void
 */
function geotwp_settings() {
	return apply_filters( 'geot_pro/settings_page/opts', get_option( 'geot_pro_settings' ) );
}

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
		'in_zipcodes'          => '',
		'ex_zipcodes'          => '',
		'in_zips_regions'    => [],
		'ex_zips_regions'    => [],
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

	extract($args);

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