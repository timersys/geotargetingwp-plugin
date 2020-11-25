<?php

/**
 * Adds GeoTarget to menus
 * @since  1.8
 */
class GeotWP_Menus {
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
		global $wp_version;

		$this->opts = geot_settings();
		$this->geot_opts = geotwp_settings();

		if ( empty( $this->geot_opts['disable_menu_integration'] ) ) {

			if ( version_compare( $wp_version, '5.4', '>=' ) )
				add_action( 'wp_nav_menu_item_custom_fields', [ $this, 'custom_fields' ], 10, 2 );
			else
				add_filter( 'wp_edit_nav_menu_walker', [ $this, 'admin_menu_walker' ], 150, 2 );

			add_filter( 'wp_setup_nav_menu_item', [ $this, 'add_custom_fields' ] );
			add_filter( 'wp_nav_menu_objects', [ $this, 'geotarget_menus' ], 90, 2 );
			add_action( 'wp_update_nav_menu_item', [ $this, 'save_custom_fields' ], 10, 3 );
		}
	}

	/**
	 * Add custom fields to the menu item
	 *
	 * @param $menu_item
	 *
	 * @return mixed
	 */
	public function add_custom_fields( $menu_item ) {

		$menu_item->geot = get_post_meta( $menu_item->ID, '_menu_item_geot', true );
		if ( empty( $menu_item->geot ) ) {
			$menu_item->geot = [];
		}

		return $menu_item;

	}

	/**
	 * Save custom menu fields data into db
	 *
	 * @param $menu_id
	 * @param $menu_item_db_id
	 * @param $args
	 */
	public function save_custom_fields( $menu_id, $menu_item_db_id, $args ) {

		// Check if element is properly sent
		if ( isset( $_REQUEST['menu-item-geot'] ) && is_array( $_REQUEST['menu-item-geot'] ) ) {
			$geot_value = $_REQUEST['menu-item-geot'][ $menu_item_db_id ];

			$geot_value['city_region'] = $_REQUEST['menu-item-geot'][ $menu_item_db_id ]['cities'];
			$geot_value['state_region'] = $_REQUEST['menu-item-geot'][ $menu_item_db_id ]['state_region'];
			$geot_value['zip_region'] = $_REQUEST['menu-item-geot'][ $menu_item_db_id ]['zipcodes'];

			update_post_meta( $menu_item_db_id, '_menu_item_geot', $geot_value );
		}

	}

	/**
	 * Change admin menu walker for custom one
	 *
	 * @param $walker
	 * @param $menu_id
	 *
	 * @return string
	 */
	public function admin_menu_walker( $walker = "", $menu_id = "" ) {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-geot-admin-menu-walker.php';

		return 'GeotWP_Admin_Menu_Walker';

	}

	/**
	 * Main function that filters wp_nav_menu_objects in frontend and remove menu items accordingly
	 *
	 * @param $sorted_menu_items
	 * @param $args
	 *
	 * @return mixed
	 */
	public function geotarget_menus( $sorted_menu_items, $args ) {

		if ( empty( $sorted_menu_items ) || ! is_array( $sorted_menu_items ) ) {
			return $sorted_menu_items;
		}

		foreach ( $sorted_menu_items as $k => $menu_item ) {

			if ( empty( $menu_item->ID ) || empty( $menu_item->geot ) ) {
				continue;
			}
			$g = $menu_item->geot;
			if( $this->menu_has_geot_settings($g) ) {
				// check at least one condition is filled
				if ( isset( $this->opts['ajax_mode'] ) && $this->opts['ajax_mode'] == '1' ) {
					$menu_item->classes[] = ' geot-ajax geot_menu_item ';
					add_filter( 'nav_menu_link_attributes', [ $this, 'add_geot_info' ], 10, 2 );
					add_filter( 'megamenu_nav_menu_link_attributes', [ $this, 'add_geot_info' ], 10, 2 );
				} else {
					if ( GeotWP_Helper::user_is_targeted( $g, $menu_item->ID ) ) {
						unset( $sorted_menu_items[ $k ] );
					}
				}
			}

		}

		return apply_filters( 'geot/menus/target', $sorted_menu_items, $args );
	}

	/**
	 * Function to add geot info to the menu items to be later handled with ajax
	 *
	 * @param $atts
	 * @param $item
	 *
	 * @return mixed
	 */
	public function add_geot_info( $atts, $item ) {
		global $wp_version;
		$geo_info = $item->geot;
		if ( version_compare( $wp_version, '5.4', '>=' ) ) {
			$geo_info = get_post_meta( $item->ID, '_menu_item_geot', true );
		}
		$atts['data-action']    = 'menu_filter';
		$atts['data-filter']    = base64_encode( serialize( $geo_info ) );
		$atts['data-ex_filter'] = $item->ID;

		return $atts;
	}


	/**
	 * Function to custom field to menu
	 * New functionality from WP 4.0
	 *
	 * @param $item_id
	 * @param $item
	 *
	 * @return mixed
	 */
	public function custom_fields($item_id, $item) {
		wp_nonce_field( 'geot_menu_meta_nonce', '_geot_menu_meta_nonce_name' );
		$geot = get_post_meta( $item_id, '_menu_item_geot', true );
		if( empty( $geot ) ) {
			$geot = [];
		}
		$geot['geot_include_mode'] = isset( $geot['geot_include_mode'] ) ? $geot['geot_include_mode'] : '';
		$geot['region']            = isset( $geot['region'] ) ? $geot['region'] : '';
		$geot['country_code']      = isset( $geot['country_code'] ) ? $geot['country_code'] : '';
		$geot['cities']            = isset( $geot['cities'] ) ? $geot['cities'] : '';
		$geot['states']            = isset( $geot['states'] ) ? $geot['states'] : '';
		$geot['zipcodes']          = isset( $geot['zipcodes'] ) ? $geot['zipcodes'] : '';

		// Radius
		$geot['radius_km']			= isset( $geot['radius_km'] ) ? $geot['radius_km'] : '';
		$geot['radius_lat']			= isset( $geot['radius_lat'] ) ? $geot['radius_lat'] : '';
		$geot['radius_lng']			= isset( $geot['radius_lng'] ) ? $geot['radius_lng'] : '';

		?>

		<input type="hidden" name="custom-menu-meta-nonce" value="<?php echo wp_create_nonce( 'custom-menu-meta-name' ); ?>" />

		<h3 class="geot_menu_title"><?php esc_html_e( 'Geotargeting', 'geot' ); ?></h3>

		<p class="geot_menu description description-wide">
			<input type="hidden" class="nav-menu-id" value="<?php echo $item_id ;?>" />


			<label for="edit-menu-item-geot_country-<?php echo $item_id; ?>">
				<label for="geot_what"><?php esc_html_e( 'Choose:', 'geot' ); ?></label><br/>
				<input type="radio" class="geot_include_mode"
				       name="menu-item-geot[<?php echo $item_id; ?>][geot_include_mode]"
				       value="include" <?php checked( $geot['geot_include_mode'], 'include', true ); ?>>
				<strong>Only show menu item in</strong><br/>
				<input type="radio" class="geot_include_mode"
				       name="menu-item-geot[<?php echo $item_id; ?>][geot_include_mode]"
				       value="exclude" <?php checked( $geot['geot_include_mode'], 'exclude', true ); ?>>
				<strong>Never show menu item in</strong><br/>
				<br>

				<label
						for="geot_position"><?php _e( 'Type regions (comma separated):', 'geot' ); ?></label><br/>
				<input type="text" class="geot_text widefat"
				       name="menu-item-geot[<?php echo $item_id; ?>][region]"
				       value="<?php echo esc_attr( $geot['region'] ); ?>"/>
				<br>

				<label
						for="geot_position"><?php _e( 'Or type countries or country codes (comma separated):', 'geot' ); ?></label><br/>
				<input type="text" class="geot_text widefat"
				       name="menu-item-geot[<?php echo $item_id; ?>][country_code]"
				       value="<?php echo esc_attr( $geot['country_code'] ); ?>"/>
				<br>

				<label
						for="geot_position"><?php _e( 'Or type cities or city regions (comma separated):', 'geot' ); ?></label><br/>
				<input type="text" class="geot_text widefat"
				       name="menu-item-geot[<?php echo $item_id; ?>][cities]"
				       value="<?php echo esc_attr( $geot['cities'] ); ?>"/>
				<br>

				<label
						for="geot_position"><?php _e( 'Or type states or state regions (comma separated):', 'geot' ); ?></label><br/>
				<input type="text" class="geot_text widefat"
				       name="menu-item-geot[<?php echo $item_id; ?>][states]"
				       value="<?php echo esc_attr( $geot['states'] ); ?>"/>

				<br>
				<label
						for="geot_position"><?php _e( 'Or type zipcodes or zip regions (comma separated):', 'geot' ); ?></label><br/>
				<input type="text" class="geot_text widefat"
				       name="menu-item-geot[<?php echo $item_id; ?>][zipcodes]"
				       value="<?php echo esc_attr( $geot['zipcodes'] ); ?>"/>


				<?php do_action( 'geot/menus/fields/radius', $item_id, $item ); ?>

				<label for="geot_position"><?php _e( 'Given Radius:', 'geot' ); ?></label><br/>
				<span class="radius_km">
					<input type="text" id="radius_km" class="geot_text widefat" name="menu-item-geot[<?php echo $item_id; ?>][radius_km]"
							       value="<?php echo esc_attr( $geot['radius_km'] ); ?>"
							       placeholder="<?php _e( '100', 'geot' ); ?>"/> Km within
					
					<input type="text" id="radius_lat" class="geot_text widefat" name="menu-item-geot[<?php echo $item_id; ?>][radius_lat]"
					       value="<?php echo esc_attr( $geot['radius_lat'] ); ?>"
					       placeholder="<?php _e( 'Enter latitude', 'geot' ); ?>"/>

					<input type="text" id="radius_lng" class="geot_text widefat" name="menu-item-geot[<?php echo $item_id; ?>][radius_lng]"
					       value="<?php echo esc_attr( $geot['radius_lng'] ); ?>"
					       placeholder="<?php _e( 'Enter longitude', 'geot' ); ?>"/>
				</span>

				<?php do_action( 'geot/menus/fields/after', $item_id, $item ); ?>
			</label>
		</p>
		<?php
	}

	/**
	 * Checks if any of the menu settings has values
	 * @param $menu
	 *
	 * @return bool
	 */
	private function menu_has_geot_settings($menu) {
		if( ! isset( $menu['geot_include_mode'] ) ) {
			return false;
		}

		if( empty( $menu['region'] ) && empty( $menu['country_code'] ) && empty( $menu['cities'] ) && empty( $menu['states'] ) && empty( $menu['zipcodes'] ) && empty( $menu['radius_km'] ) && empty( $menu['radius_lat'] ) && empty( $menu['radius_lng'] ) ) {
			return apply_filters( 'geot/menus/has_geot', false, $menu );
		}

		return true;
	}
}