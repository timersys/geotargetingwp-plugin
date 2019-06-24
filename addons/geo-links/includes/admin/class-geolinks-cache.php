<?php

/**
 * Class GeotWP_LinksCpt will handle all stuff related to custom post type
 * @since 1.0.0
 */
class Geolinks_Cache {

	public function __construct() {
		add_action( 'wp', [ $this, 'setConstant_noCache' ] );
	}


	/**
	 * Set Constant to no cache
	 *
	 * @return mixed
	 * @since  1.2
	 */
	public function setConstant_noCache() {
		global $post;

		if ( isset( $post->ID ) && get_post_type( $post ) == 'geol_cpt' ) {

			// To WP Super Cache, W3TC and WP Rocket
			if ( ! defined( 'DONOTCACHEPAGE' ) ) {
				define( 'DONOTCACHEPAGE', true );
			}
			//Besides : WP Rocket
			add_filter( 'do_rocket_generate_caching_files', '__return_false', 11 );
			add_filter( 'rocket_override_donotcachepage', '__return_false', 11 );
		}
	}
}

new Geolinks_Cache();