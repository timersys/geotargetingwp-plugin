<?php

/**
 * Helper class
 *
 * @package    Geotr
 * @subpackage Geotr/includes
 */
class Geotr_Helper {

	/**
	 * Return the redirection options
	 * @param  int $id geotrcpt id
	 * @since  2.0
	 * @return array metadata values
	 */
	public static function get_options( $id ) {
		$defaults = array(

			'url'			    => '',
			'one_time_redirect'	=> '',
			'exclude_se'		=> '',
			'pass_query_string'	=> '',
			'whitelist'			=> '',
			'status'			=> 302,
		);

		$opts = apply_filters( 'geotr/metaboxes/box_options', get_post_meta( $id, 'geotr_options', true ), $id );

		return wp_parse_args( $opts, apply_filters( 'geotr/metaboxes/default_options', $defaults ) );
	}
}
