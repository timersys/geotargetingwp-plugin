<?php

/**
 * Helper class
 *
 * @package    Geotr
 * @subpackage Geotr/includes
 */
class GeotWP_R_Helper {

	/**
	 * Return the redirection options
	 *
	 * @param int $id geotrcpt id
	 *
	 * @return array metadata values
	 * @since  2.0
	 */
	public static function get_options( $id ) {
		$defaults = [

			'url'               => '',
			'one_time_redirect' => '',
			'exclude_se'        => '1',
			'remove_iso'        => '',
			'exclude_child'     => '1',
			'pass_query_string' => '',
			'whitelist'         => '',
			'status'            => 302,
		];

		$opts = apply_filters( 'geotr/metaboxes/box_options', get_post_meta( $id, 'geotr_options', true ), $id );

		return wp_parse_args( $opts, apply_filters( 'geotr/metaboxes/default_options', $defaults ) );
	}
}
