<?php

/**
 * Adds GeoTarget to all Widgets
 * @since  1.0.0
 */
class GeotWP_Widgets {

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

		$this->geot_opts = geotwp_settings();
		$this->opts = geot_settings();

		// give users a way to disable widgets targeting
		if ( empty( $this->geot_opts['disable_widget_integration'] ) ) {

			// Admin Settings
			add_action( 'in_widget_form', [ $this, 'add_geot_to_widgets' ], 5, 3 );
			add_action( 'widget_update_callback', [ $this, 'save_widgets_data' ], 5, 3 );

			if( empty( $this->opts['ajax_mode'] ) ) {
				
				// Validation Display
				add_action( 'widget_display_callback', [ $this, 'target_widgets' ], 10, 3 );
				add_action( 'siteorigin_panels_widget_object', [ $this, 'target_widgets_site_origin' ], 10, 3 );

			} else {
				add_action( 'widget_display_callback', [ $this, 'ajax_widget' ], 10, 3 );
			}
		}
	}

	public function add_geot_to_widgets( $t, $return, $instance ) {

		$countries = geot_countries();
		$regions   = geot_country_regions();

		if ( empty( $instance['geot_include_mode'] ) ) {
			$instance['geot_include_mode'] = '';
		}
		if ( empty( $instance['geot_zipcodes'] ) ) {
			$instance['geot_zipcodes'] = '';
		}
		if ( empty( $instance['geot_states'] ) ) {
			$instance['geot_states'] = '';
		}
		if ( empty( $instance['geot_cities'] ) ) {
			$instance['geot_cities'] = '';
		}
		if ( empty( $instance['geot'] ) ) {
			$instance['geot'] = [];
		}
		if ( empty( $instance['geot']['region'] ) ) {
			$instance['geot']['region'] = [];
		}
		if ( empty( $instance['geot']['country_code'] ) ) {
			$instance['geot']['country_code'] = [];
		}
		if ( empty( $instance['radius_km'] ) ) {
			$instance['radius_km'] = '';
		}
		if ( empty( $instance['radius_lat'] ) ) {
			$instance['radius_lat'] = '';
		}
		if ( empty( $instance['radius_lng'] ) ) {
			$instance['radius_lng'] = '';
		}
		?>

		<div id="geot_widget" class="widget-content">
			<h3>Geotargeting WP</h3>
			<p>
				<label for="geot_what"><?php _e( 'Choose:', 'geot' ); ?></label><br/>
				<input type="radio" class="geot_include_mode"
				       name="<?php echo $t->get_field_name( 'geot_include_mode' ); ?>"
				       value="include" <?php checked( $instance['geot_include_mode'], 'include', true ); ?>> <strong>Only
					show widget in</strong><br/>
				<input type="radio" class="geot_include_mode"
				       name="<?php echo $t->get_field_name( 'geot_include_mode' ); ?>"
				       value="exclude" <?php checked( $instance['geot_include_mode'], 'exclude', true ); ?>> <strong>Never
					show widget in</strong><br/>
			</p>
			<p>
				<label><?php _e( 'Choose regions( country regions ):', 'geot' ); ?></label>
				<?php
				if ( is_array( $regions ) ) { ?>
					<select name="<?php echo $t->get_field_name( 'geot' ); ?>[region][]" multiple
					        class="geot-chosen-select-multiple"
					        data-placeholder="<?php _e( 'Choose region name...', 'geot' ); ?>">
						<?php
						if ( is_array( $regions ) ) {
							foreach ( $regions as $r ) {
								?>
								<option value="<?php echo $r['name']; ?>" <?php selected( in_array( $r['name'], $instance['geot']['region'] ), true, true ); ?>> <?php echo $r['name']; ?></option>
								<?php
							}
						}
						?>
					</select>
					<?php
				} else { ?>

			<p> Add some regions first.</p>

			<?php
			} ?>
			</p>

			<p>
				<label for="geot_position"><?php _e( 'Or choose countries:', 'geot' ); ?></label>

				<select name="<?php echo $t->get_field_name( 'geot' ); ?>[country_code][]" multiple
				        class="geot-chosen-select-multiple"
				        data-placeholder="<?php _e( 'Choose country name...', 'geot' ); ?>">
					<?php
					if ( is_array( $countries ) ) {
						foreach ( $countries as $c ) {
							?>
							<option value="<?php echo $c->iso_code; ?>" <?php selected( in_array( $c->iso_code, $instance['geot']['country_code'] ), true, true ); ?>> <?php echo $c->country; ?></option>
							<?php
						}
					}
					?>
				</select>
			</p>
			<p>
				<label for="geot_position"><?php _e( 'Or type cities or city regions (comma separated):', 'geot' ); ?></label><br/>
				<input type="text" class="geot_text" name="<?php echo $t->get_field_name( 'geot_cities' ); ?>"
				       value="<?php echo esc_attr( $instance['geot_cities'] ); ?>"/>
			</p>
			<p>
				<label for="geot_position"><?php _e( 'Or type states or state regions (comma separated):', 'geot' ); ?></label><br/>
				<input type="text" class="geot_text" name="<?php echo $t->get_field_name( 'geot_states' ); ?>"
				       value="<?php echo esc_attr( $instance['geot_states'] ); ?>"/>
			</p>

			<p>
				<label for="geot_position"><?php _e( 'Or type zipcodes or zip regions (comma separated):', 'geot' ); ?></label><br/>
				<input type="text" class="geot_text" name="<?php echo $t->get_field_name( 'geot_zipcodes' ); ?>"
				       value="<?php echo esc_attr( $instance['geot_zipcodes'] ); ?>"/>
			</p>
			<p>
				<label for="gstates"><?php _e( 'Given Radius:', 'geot' ); ?></label>
				<span class="radius_km">
						<input type="text" id="radius_km" class="geot_text" name="<?php echo $t->get_field_name('radius_km'); ?>"
						       value="<?php echo esc_attr( $instance['radius_km'] ); ?>"
						       placeholder="<?php _e( '100', 'geot' ); ?>"/> Km within
						<input type="text" id="radius_lat" class="geot_text" name="<?php echo $t->get_field_name('radius_lat'); ?>"
						       value="<?php echo esc_attr( $instance['radius_lat'] ); ?>"
						       placeholder="<?php _e( 'Enter latitude', 'geot' ); ?>"/>
						<input type="text" id="radius_lng" class="geot_text" name="<?php echo $t->get_field_name('radius_lng'); ?>"
						       value="<?php echo esc_attr( $instance['radius_lng'] ); ?>"
						       placeholder="<?php _e( 'Enter longitude', 'geot' ); ?>"/>
				</span>

			</p>
		</div>

		<?php
		return [ $t, $return, $instance ];
	}

	/**
	 * Saves widget data
	 *
	 * @param array $instance Current widget instance
	 * @param array $new_instance Saved instance
	 * @param array $old_instance
	 *
	 * @return array
	 */
	public function save_widgets_data( $instance, $new_instance, $old_instance ) {

		$instance['geot']              = isset( $new_instance['geot'] ) ? (array) $new_instance['geot'] : '';
		$instance['geot_include_mode'] = isset( $new_instance['geot_include_mode'] ) ? $new_instance['geot_include_mode'] : '';
		$instance['geot_cities']       = isset( $new_instance['geot_cities'] ) ? $new_instance['geot_cities'] : '';
		$instance['geot_states']       = isset( $new_instance['geot_states'] ) ? $new_instance['geot_states'] : '';
		$instance['geot_zipcodes']     = isset( $new_instance['geot_zipcodes'] ) ? $new_instance['geot_zipcodes'] : '';
		$instance['radius_lat']        = isset( $new_instance['radius_lat'] ) ? $new_instance['radius_lat'] : '';
		$instance['radius_lng']        = isset( $new_instance['radius_lng'] ) ? $new_instance['radius_lng'] : '';
		$instance['radius_km']         = isset( $new_instance['radius_km'] ) ? $new_instance['radius_km'] : '';

		return $instance;
	}

	/**
	 * Check if widgets is being targeted and show it if needed
	 *
	 * @param $instance
	 *
	 * @return bool [type] [description]
	 */
	public function target_widgets( $instance, $widget, $args ) {

		if ( ! empty( $this->opts['ajax_mode'] ) ) {
			return $instance;
		}

		if ( ! $this->target( $instance ) ) {
			return false;
		}

		return $instance;
	}

	/**
	 * Wrapper target function to avoid repeating code
	 *
	 * @param $widget_data
	 *
	 * @return bool
	 */
	private function target( $widget_data ) {


		if ( ! empty( $widget_data['geot']['region'] ) ||
		     ! empty( $widget_data['geot']['country_code'] ) ||
		     ! empty( $widget_data['geot_cities'] ) ||
		     ! empty( $widget_data['geot_states'] ) ||
		     ! empty( $widget_data['geot_zipcodes'] ) ||
		     ( ! empty( $widget_data['radius_km'] ) && ! empty( $widget_data['radius_lng'] ) && ! empty( $widget_data['radius_lat'] ) )
		) {

				if ( ! empty( $widget_data['geot_zipcodes'] ) ) {
					if ( ! geot_target_zip( @$widget_data['geot_zipcodes'], @$widget_data['geot_zipcodes'] ) ) {
						return @$widget_data['geot_include_mode'] == 'include' ?  false : true;
					}
				} elseif ( ! empty( $widget_data['geot_cities'] ) ) {
					if ( ! geot_target_city( @$widget_data['geot_cities'], @$widget_data['geot_cities'] ) ) {
						return @$widget_data['geot_include_mode'] == 'include' ?  false : true;
					}
				} elseif ( ! empty( $widget_data['geot_states'] ) ) {
					if ( ! geot_target_state( @$widget_data['geot_states'], @$widget_data['geot_states'] ) ) {
						return @$widget_data['geot_include_mode'] == 'include' ?  false : true;
					}
				} elseif ( ! empty( $widget_data['radius_km'] ) && ! empty( $widget_data['radius_lng'] ) && ! empty( $widget_data['radius_lat'] ) ) {
					if ( ! geot_target_radius( @$widget_data['radius_lat'], @$widget_data['radius_lng'], @$widget_data['radius_km'] ) ) {
						return @$widget_data['geot_include_mode'] == 'include' ?  false : true;
					}
				} else {
					if ( ! geot_target( @$widget_data['geot']['country_code'], @$widget_data['geot']['region'] ) ) {
						return @$widget_data['geot_include_mode'] == 'include' ?  false : true;
					}
				}
				return @$widget_data['geot_include_mode'] == 'include' ?  true : false;
		}

		return true;
	}

	/**
	 * Check if widgets is being targeted and show it if needed
	 *
	 * @param $the_widget
	 * @param $widget_class
	 * @param $widget_data
	 *
	 * @return bool [type] [description]
	 */
	public function target_widgets_site_origin( $the_widget, $widget_class, $widget_data = null ) {


		// don't work in ajax mode
		if ( ! empty( $this->opts['ajax_mode'] ) ) {
			return $the_widget;
		}

		if ( ! $this->target( $widget_data ) ) {
			add_filter( 'siteorigin_panels_missing_widget', '__return_false' );

			return false;
		}

		return $the_widget;
	}

	/**
	 * Render a placeholder for ajax calls
	 *
	 * @param $instance
	 * @param $widget
	 *
	 * @param $args
	 *
	 * @return mixed
	 */
	public function ajax_widget( $instance, $widget, $args ) {


		if( is_admin()  )
			return $instance;

		if( empty( $instance['geot']['region'] ) && empty( $instance['geot']['country_code'] ) &&
		    empty( $instance['geot_cities'] ) && empty( $instance['geot_states'] ) &&
		    empty( $instance['geot_zipcodes'] ) && (  empty( $instance['radius_km'] ) ||  empty( $instance['radius_lng'] ) || empty( $instance['radius_lat'] ) )
		) return $instance;

		$target = [
			'geot_include_mode' => $instance['geot_include_mode'],
			'country_code'      => ! empty( $instance['geot']['country_code'] ) ? $instance['geot']['country_code'] : [],
			'region'            => ! empty( $instance['geot']['region'] ) ? $instance['geot']['region'] : [],
			'cities'            => ! empty( $instance['geot_cities'] ) ? $instance['geot_cities'] : '',
			'city_region'       => ! empty( $instance['geot_cities'] ) ? $instance['geot_cities'] : '',
			'states'            => ! empty( $instance['geot_states'] ) ? $instance['geot_states'] : '',
			'state_region'		=> ! empty( $instance['geot_states'] ) ? $instance['geot_states'] : '',
			'zipcodes'          => ! empty( $instance['geot_zipcodes'] ) ? $instance['geot_zipcodes'] : '',
			'zip_region'        => ! empty( $instance['geot_zipcodes'] ) ? $instance['geot_zipcodes'] : '',
			'radius_km'         => ! empty( $instance['radius_km'] ) ? $instance['radius_km'] : '',
			'radius_lat'        => ! empty( $instance['radius_lat'] ) ? $instance['radius_lat'] : '',
			'radius_lng'        => ! empty( $instance['radius_lng'] ) ? $instance['radius_lng'] : '',
		];


		if( isset( $widget->id ) ) {
			$filter = base64_encode( serialize( $target ) );
			echo '<style type="text/css" id="css-' . $widget->id . '">#' . $widget->id . '{ display:none;}</style>';
			echo '<div class="geot-ajax geot-widget" data-action="widget_filter" data-filter="' . $filter . '"  data-ex_filter="' . $widget->id . '" data-widget="' . $widget->id . '"></div>';
		}
		return $instance;
	}

} // class GeotWP_Widgets
