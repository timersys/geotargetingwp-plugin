<?php

/**
 * @subpackage Geotr/admin
 * @author     Damian Logghe <damian@timersys.com>
 */
class GeotWP_R_Admin {

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		//add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
		add_filter( 'manage_edit-geotr_cpt_columns', [ $this, 'set_custom_cpt_columns' ], 10, 2 );
		add_action( 'manage_geotr_cpt_posts_custom_column', [ $this, 'custom_columns' ], 10, 2 );
	}


	/**
	 * Add callbacks for custom colums
	 *
	 * @param array $column [description]
	 * @param int $post_id [description]
	 *
	 * @return echo html
	 * @since  1.2
	 */
	function custom_columns( $column, $post_id ) {
		global $wpdb;

		$opts = GeotWP_R_Helper::get_options( $post_id );

		switch ( $column ) {

			case 'url' :
				echo esc_attr( $opts['url'] );
				break;
		}
	}


	/**
	 * Add custom columns to cpt
	 *
	 * @param [type] $columns [description]
	 *
	 * @return mixed
	 * @since  1.2
	 */
	public function set_custom_cpt_columns( $columns ) {
		$new_column = [];

		foreach ( $columns as $key => $value ) {
			if ( $key == 'date' ) {
				$new_column['url'] = __( 'Destination URL', 'geot' );
			}
			$new_column[ $key ] = $value;
		}

		return $new_column;
	}
}
