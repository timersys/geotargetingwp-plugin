<?php
/**
 * Different helpers filters used around
 */

add_filter( 'geot/get_country_regions', function () {
	$settings = geot_settings();
	$regions  = isset( $settings['region'] ) ? $settings['region'] : [];

	return array_merge( $regions, geot_predefined_regions() );
} );

add_filter( 'geot/get_city_regions', function () {
	$settings = geot_settings();
	$regions  = isset( $settings['city_region'] ) ? $settings['city_region'] : [];

	return $regions;
} );

add_filter( 'geot/get_state_regions', function () {
	$settings = geot_settings();
	$regions  = isset( $settings['state_region'] ) ? $settings['state_region'] : [];

	return $regions;
} );

add_filter( 'geot/get_zip_regions', function () {
	$settings = geot_settings();
	$regions  = isset( $settings['zip_region'] ) ? $settings['zip_region'] : [];

	return $regions;
} );

add_filter( 'geot/get_countries', function () {
	$countries = wp_cache_get( 'geot_countries' );
	if ( false === $countries ) {
		global $wpdb;

		$countries = $wpdb->get_results( "SELECT iso_code, country FROM {$wpdb->base_prefix}geot_countries ORDER BY country" );

		wp_cache_set( 'geot_countries', $countries );
	}

	return $countries;
} );