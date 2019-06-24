<?php

/**
 * Grab geot settings
 * @return mixed|void
 */
function geotwp_settings() {
	return apply_filters( 'geot_pro/settings_page/opts', get_option( 'geot_pro_settings' ) );
}

function geotwp_addons() {
	return apply_filters( 'geot_pro/settings_page/addons', get_option( 'geot_pro_addons' ) );
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