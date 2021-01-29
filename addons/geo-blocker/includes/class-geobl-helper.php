<?php

/**
 * Helper class
 *
 * @package    Geobl
 * @subpackage Geobl/includes
 */
class GeotWP_Bl_Helper {

	/**
	 * Return the redirection options
	 *
	 * @param int $id geoblcpt id
	 *
	 * @return array metadata values
	 * @since  2.0
	 */
	public static function get_options( $id ) {
		$defaults = [

			'block_message' => '<h3>' . __( 'Sorry but your access to this area is restricted', 'geot' ) . '</h3>',
			'exclude_se'    => '1',
			'remove_iso'    => '0',
			'exclude_child' => '1',
			'whitelist'     => '',
		];

		$opts = apply_filters( 'geobl/metaboxes/box_options', get_post_meta( $id, 'geobl_options', true ), $id );

		return wp_parse_args( $opts, apply_filters( 'geobl/metaboxes/default_options', $defaults ) );
	}


	/**
	 * @param array $args
	 */
	public static function get_template( $id = null ) {

		if ( is_null( $id ) ) {
			return false;
		}

		$located = GeotWP_Bl_Helper::get_template_from_theme( $id );

		// use default one
		if ( ! $located ) {
			$located = GEOTWP_BL_PLUGIN_DIR . '/public/partials/geobl-template.php';
		}

		// Allow 3rd party plugin filter template file from their plugin.
		$located = apply_filters( 'geobl/include_template', $located, $id );

		ob_start();

		do_action( 'geobl/before_template', $located, $id );

		include $located;

		do_action( 'geobl/after_template', $located, $id );

		return ob_get_clean();
	}

	/**
	 * Get a template from the theme. Either one with ID or global one
	 *
	 * @param $post_id
	 *
	 * @return mixed|void
	 */
	public static function get_template_from_theme( $post_id ) {

		$template_name = sprintf( 'geobl-template-%d.php', $post_id );
		$template_path = apply_filters( 'geobl/theme_path', 'geobl/' );
		// try a individual template.
		$located = locate_template( [ trailingslashit( $template_path ) . $template_name, $template_name ] );
		// try with a global template
		if ( '' == $located ) {
			$template_name = 'geobl-template.php';
			$located       = locate_template( [ trailingslashit( $template_path ) . $template_name, $template_name ] );
		}

		return apply_filters( 'geobl/get_template_from_theme', $located, $post_id );
	}
}
