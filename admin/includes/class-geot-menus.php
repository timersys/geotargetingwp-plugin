<?php
/**
 * Adds GeoTarget to menus
 * @since  1.8
 */
class Geot_Menus {
	/**
	 * @since   1.6
	 * @access  private
	 * @var     Array of plugin settings
	 */
	private $geot_opts;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @var      string $GeoTarget The name of this plugin.
	 * @var      string $version The version of this plugin.
	 */
	public function __construct() {
		$this->geot_opts = geot_pro_settings();

		if (  empty( $this->geot_opts['disable_menu_integration'] ) ) {
			add_filter( 'wp_setup_nav_menu_item', [ $this, 'add_custom_fields' ] );
			add_filter( 'wp_edit_nav_menu_walker', [ $this, 'admin_menu_walker' ], 150, 2 );
			add_action( 'wp_update_nav_menu_item', [ $this, 'save_custom_fields' ], 10, 3 );
			add_filter( 'wp_nav_menu_objects', [ $this, 'geotarget_menus' ], 10, 2 );
		}
	}

	/**
	 * Add custom fields to the menu item
	 * @param $menu_item
	 *
	 * @return mixed
	 */
	public function add_custom_fields( $menu_item ) {

        $menu_item->geot = get_post_meta( $menu_item->ID, '_menu_item_geot', true );
		if(empty($menu_item->geot)){
			$menu_item->geot = [];
		}
		return $menu_item;
		
	}

	/**
	 * Save custom menu fields data into db
	 * @param $menu_id
	 * @param $menu_item_db_id
	 * @param $args
	 */
	public function save_custom_fields( $menu_id, $menu_item_db_id, $args ) {

		// Check if element is properly sent
		if ( isset( $_REQUEST['menu-item-geot'] ) && is_array( $_REQUEST['menu-item-geot'] ) ) {
			$geot_country_value = $_REQUEST['menu-item-geot'][$menu_item_db_id];
			update_post_meta( $menu_item_db_id, '_menu_item_geot', $geot_country_value );
		}

	}

	/**
	 * Change admin menu walker for custom one
	 * @param $walker
	 * @param $menu_id
	 *
	 * @return string
	 */
	public function admin_menu_walker( $walker = "",$menu_id = "" ) {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-geot-admin-menu-walker.php';
		return 'Geot_Admin_Menu_Walker';

	}

	/**
	 * Main function that filters wp_nav_menu_objects in frontend and remove menu items accordingly
	 * @param $sorted_menu_items
	 * @param $args
	 *
	 * @return mixed
	 */
	public function geotarget_menus( $sorted_menu_items, $args ){

		if( empty( $sorted_menu_items ) || ! is_array( $sorted_menu_items ) )
			return $sorted_menu_items;

		foreach ( $sorted_menu_items as $k => $menu_item ) {
			$g = $menu_item->geot;
			if( empty($menu_item->ID))
				continue;
			// check at least one condition is filled
			if( isset( $this->geot_opts['ajax_mode'] ) && $this->geot_opts['ajax_mode'] == '1' ) {
				$menu_item->classes[] = 'geot-ajax geot_menu_item';
				add_filter( 'nav_menu_link_attributes', array( $this, 'add_geot_info'), 10, 2 );
			} else {
				if( Geot_Helpers::user_is_targeted($g, $menu_item->ID ) )
					unset( $sorted_menu_items[ $k ] );
			}

		}
		return $sorted_menu_items;
	}

	/**
	 * Function to add geot info to the menu items to be later handled with ajax
	 * @param $atts
	 * @param $item
	 *
	 * @return mixed
	 */
	public function add_geot_info( $atts, $item ) {

		if( !empty( $item->geot ) ) {
			$atts['data-action'] = 'menu_filter';
			$atts['data-filter'] = base64_encode( serialize( $item->geot ) );
			$atts['data-ex_filter'] = $item->ID ;
		}
		return $atts;
	}
}