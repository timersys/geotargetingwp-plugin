<?php
/**
 * @subpackage Geotr/admin
 * @author     Damian Logghe <damian@timersys.com>
 */
class Geotr_Admin {

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
		add_filter( 'manage_edit-geotr_cpt_columns', [ $this, 'set_custom_cpt_columns' ], 10, 2 );
		add_action( 'manage_geotr_cpt_posts_custom_column',  [ $this, 'custom_columns' ], 10, 2 );
	}


	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		global $pagenow, $post;

		if ( get_post_type() !== 'geotr_cpt' || !in_array( $pagenow, array( 'post-new.php', 'edit.php', 'post.php' ) ) )
			return;

		$post_id = isset( $post->ID ) ? $post->ID : '';

		wp_enqueue_script( 'geotr-admin-js', plugin_dir_url( __FILE__ ) . 'js/geotr-admin.js', array( 'jquery' ), GEOTR_VERSION, false );

		wp_enqueue_style( 'geotr-admin-css', plugin_dir_url( __FILE__ ) . 'css/geotr-admin.css', array(), GEOTR_VERSION, 'all' );

		wp_localize_script( 'geotr-admin-js', 'geotr_js',
				array(
					'admin_url' => admin_url( ),
					'nonce' 	=> wp_create_nonce( 'geotr_nonce' ),
					'l10n'		=> array (
							'or'	=> '<span>'.__('OR', 'geotr' ).'</span>'
						),
					'opts'      => Geotr_Helper::get_options($post_id)
				)
		);
	}


	/**
	 * Add callbacks for custom colums
	 * @param  array $column  [description]
	 * @param  int $post_id [description]
	 * @return echo html
	 * @since  1.2
	 */
	function custom_columns( $column, $post_id ) {
		global $wpdb;

		$opts =  Geotr_Helper::get_options($post_id);

		switch ( $column ) {

			case 'url' :
				echo esc_attr($opts['url']);
				break;
		}
	}


	/**
	 * Add custom columns to cpt
	 *
	 * @param [type] $columns [description]
	 *
	 * @since  1.2
	 * @return mixed
	 */
	public function set_custom_cpt_columns( $columns ){
		$new_column = [];

		foreach ($columns as $key => $value ){
			if( $key == 'date')
				$new_column['url']        = __( 'Destination URL', 'geotr' );
			$new_column[$key] = $value;
		}

		return $new_column;
	}
}
