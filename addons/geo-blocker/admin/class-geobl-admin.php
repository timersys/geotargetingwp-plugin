<?php
/**
 * @subpackage Geobl/admin
 * @author     Damian Logghe <damian@timersys.com>
 */
class Geobl_Admin {

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct() {

		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
	}


	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		global $pagenow, $post;

		if ( get_post_type() !== 'geobl_cpt' || !in_array( $pagenow, array( 'post-new.php', 'edit.php', 'post.php' ) ) )
			return;

		$post_id = isset( $post->ID ) ? $post->ID : '';

		wp_enqueue_script( 'geobl-admin-js', plugin_dir_url( __FILE__ ) . 'js/geobl-admin.js', array( 'jquery' ), GEOBL_VERSION, false );

		wp_enqueue_style( 'geobl-admin-css', plugin_dir_url( __FILE__ ) . 'css/geobl-admin.css', array(), GEOBL_VERSION, 'all' );

		wp_localize_script( 'geobl-admin-js', 'geobl_js',
				array(
					'admin_url' => admin_url( ),
					'nonce' 	=> wp_create_nonce( 'geobl_nonce' ),
					'l10n'		=> array (
							'or'	=> '<span>'.__('OR', 'geobl' ).'</span>'
						),
					'opts'      => Geobl_Helper::get_options($post_id)
				)
		);
	}
}
