<?php
/**
 * Grab geotr settings
 * @return mixed|void
 */
function geotWPR_redirections() {
	global $wpdb;

	$sql = "SELECT ID, 
	MAX(CASE WHEN pm1.meta_key = 'geotr_rules' then pm1.meta_value ELSE NULL END) as geotr_rules,
	MAX(CASE WHEN pm1.meta_key = 'geotr_options' then pm1.meta_value ELSE NULL END) as geotr_options
    FROM $wpdb->posts p LEFT JOIN $wpdb->postmeta pm1 ON ( pm1.post_id = p.ID)  WHERE post_type='geotr_cpt' AND post_status='publish' GROUP BY p.ID";

	$redirections = wp_cache_get( md5( $sql ), 'geotr_posts' );
	if ( $redirections === false ) {
		$redirections = $wpdb->get_results( $sql, OBJECT );
		wp_cache_add( md5( $sql ), $redirections, 'geotr_posts' );
	}

	return $redirections;
}
