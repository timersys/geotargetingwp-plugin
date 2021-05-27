<?php

use function GeotCore\toArray;

/**
 * Compatibility with POpups plugins
 * Class Geot_Popups
 */
class Geot_Popups {
	public function __construct() {


		add_filter( 'spu/metaboxes/rule_types', [ $this, 'add_popups_rules' ] );

		add_filter( 'spu/rules/rule_values/geot_country', [ $this, 'add_country_choices' ] );
		add_filter( 'spu/rules/rule_values/geot_country_region', [ $this, 'add_country_region_choices' ] );
		add_filter( 'spu/rules/rule_values/geot_city_region', [ $this, 'add_city_region_choices' ] );

		add_filter( 'spu/rules/rule_match/geot_country', [ $this, 'popup_country_match' ], 10, 2 );
		add_filter( 'spu/rules/rule_match/geot_country_region', [ $this, 'popup_country_region_match' ], 10, 2 );
		add_filter( 'spu/rules/rule_match/geot_city_region', [ $this, 'popup_city_region_match' ], 10, 2 );
		add_filter( 'spu/rules/rule_match/geot_state', [ $this, 'popup_state_match' ], 10, 2 );
		add_filter( 'spu/rules/rule_match/geot_zip', [ $this, 'popup_zip_match' ], 10, 2 );
		add_filter( 'spu/rules/rule_match/geot_city', [ $this, 'popup_city_match' ], 10, 2 );

		add_action( 'spu/rules/print_geot_country_field', [ 'Spu_Helper', 'print_select' ], 10, 2 );
		add_action( 'spu/rules/print_geot_country_region_field', [ 'Spu_Helper', 'print_select' ], 10, 2 );
		add_action( 'spu/rules/print_geot_city_region_field', [ 'Spu_Helper', 'print_select' ], 10, 2 );
		add_action( 'spu/rules/print_geot_state_field', [ 'Spu_Helper', 'print_textfield' ], 10, 1 );
		add_action( 'spu/rules/print_geot_city_field', [ 'Spu_Helper', 'print_textfield' ], 10, 1 );
		add_action( 'spu/rules/print_geot_zip_field', [ 'Spu_Helper', 'print_textfield' ], 10, 1 );


		// WP Popups
		add_filter( 'wppopups/rules/options', [ $this, 'add_popups_rules' ] );
		add_filter( 'wppopups/rules/field_type', [ $this, 'geolocation_field_type' ], 10, 2 );

		add_filter( 'wppopups_rules_rule_match_geot_country', [ self::class, 'rule_match_country' ] );
		add_filter( 'wppopups_rules_rule_match_geot_city', [ self::class, 'rule_match_city' ] );
		add_filter( 'wppopups_rules_rule_match_geot_state', [ self::class, 'rule_match_state' ] );
		add_filter( 'wppopups_rules_rule_match_geot_zip', [ self::class, 'rule_match_zip' ] );
		add_filter( 'wppopups_rules_rule_match_geot_country_region', [ self::class, 'rule_match_region' ] );
		add_filter( 'wppopups_rules_rule_match_geot_state_region', [ self::class, 'rule_match_state_region' ] );
		add_filter( 'wppopups_rules_rule_match_geot_zip_region', [ self::class, 'rule_match_zip_region' ] );
		add_filter( 'wppopups_rules_rule_match_geot_city_region', [ self::class, 'rule_match_city_region' ] );
		add_filter( 'wppopups_rules_rule_match_geot_radius', [ self::class, 'rule_match_radius' ] );

		add_filter( 'wppopups_rules/rule_values/geot_country_region', [ self::class, 'rule_values_region' ]);
		add_filter( 'wppopups_rules/rule_values/geot_city_region', [ self::class, 'rule_values_city_region' ]);
		add_filter( 'wppopups_rules/rule_values/geot_state_region', [ self::class, 'rule_values_state_region' ]);
		add_filter( 'wppopups_rules/rule_values/geot_zip_region', [ self::class, 'rule_values_zip_region' ]);

		// Output Field
		add_filter( 'wppopups_output_field_geot_country_region', [ $this, 'field_region' ], 10, 2 );
		add_filter( 'wppopups_sanitize_field_geot_country_region', [ $this, 'sanitize_region' ], 10, 4 );
		add_filter( 'wppopups_output_field_geot_zip_region', [ $this, 'field_region' ], 10, 2 );
		add_filter( 'wppopups_sanitize_field_geot_zip_region', [ $this, 'sanitize_region' ], 10, 4 );
		add_filter( 'wppopups_output_field_geot_city_region', [ $this, 'field_region' ], 10, 2 );
		add_filter( 'wppopups_sanitize_field_geot_city_region', [ $this, 'sanitize_region' ], 10, 4 );
		add_filter( 'wppopups_output_field_geot_state_region', [ $this, 'field_region' ], 10, 2 );
		add_filter( 'wppopups_sanitize_field_geot_state_region', [ $this, 'sanitize_region' ], 10, 4 );
	}

	/**
	 * Add rules to Popups plugin
	 *
	 * @param $choices
	 *
	 * @return mixed
	 */
	public function add_popups_rules( $choices ) {
		$choices['Geotargeting'] = [
			'geot_country'        => 'Country',
			'geot_country_region' => 'Country Region',
			'geot_city_region'    => 'City Region',
			'geot_zip_region'     => 'Zip Region',
			'geot_state_region'   => 'State Region',
			'geot_state'          => 'State',
			'geot_city'           => 'City',
			'geot_zip'            => 'Zip',
			'geot_radius'         => 'Lat|Lng|Radius',
		];

		return $choices;
	}

	/**
	 * Return countries for popup rules
	 *
	 * @param $choices
	 *
	 * @return mixed
	 */
	public function add_country_choices( $choices ) {
		$countries = geot_countries();
		foreach ( $countries as $c ) {
			$choices[ $c->iso_code ] = $c->country;
		}

		return $choices;
	}

	/**
	 * Return countries regions for popup rules
	 *
	 * @param $choices
	 *
	 * @return mixed
	 */
	public function add_country_region_choices( $choices ) {
		$regions = geot_country_regions();
		foreach ( $regions as $r ) {

			$choices[ $r['name'] ] = $r['name'];
		}

		return $choices;
	}

	/**
	 * Return cities regions for popup rules
	 *
	 * @param $choices
	 *
	 * @return mixed
	 */
	public function add_city_region_choices( $choices ) {
		$regions = geot_city_regions();
		foreach ( $regions as $r ) {

			$choices[ $r['name'] ] = $r['name'];
		}

		return $choices;
	}

	/**
	 * [rule_match_logged_user description]
	 *
	 * @param bool $match false default
	 * @param array $rule rule to compare
	 *
	 * @return boolean true if match
	 */
	function popup_country_match( $match, $rule ) {

		if ( $rule['operator'] == "==" ) {

			return geot_target( $rule['value'] );

		} else {

			return ! geot_target( $rule['value'] );
		}
	}

	/**
	 * @param bool $match false default
	 * @param array $rule rule to compare
	 *
	 * @return boolean true if match
	 */
	function popup_country_region_match( $match, $rule ) {

		if ( $rule['operator'] == "==" ) {

			return geot_target( '', $rule['value'] );

		} else {

			return ! geot_target( '', $rule['value'] );
		}
	}

	/**
	 * @param bool $match false default
	 * @param array $rule rule to compare
	 *
	 * @return boolean true if match
	 */
	function popup_city_region_match( $match, $rule ) {

		if ( $rule['operator'] == "==" ) {
			return geot_target_city( '', $rule['value'], '', '' );

		} else {

			return ! geot_target_city( '', $rule['value'], '', '' );
		}

	}

	/**
	 * @param bool $match false default
	 * @param array $rule rule to compare
	 *
	 * @return boolean true if match
	 */
	function popup_state_match( $match, $rule ) {

		if ( $rule['operator'] == "==" ) {

			return geot_target_state( $rule['value'], '' );

		} else {

			return ! geot_target_state( $rule['value'], '' );
		}
	}
	/**
	 * @param bool $match false default
	 * @param array $rule rule to compare
	 *
	 * @return boolean true if match
	 */
	function popup_city_match( $match, $rule ) {

		if ( $rule['operator'] == "==" ) {

			return geot_target_city( $rule['value'], '' );

		} else {

			return ! geot_target_city( $rule['value'], '' );
		}
	}
	/**
	 * @param bool $match false default
	 * @param array $rule rule to compare
	 *
	 * @return boolean true if match
	 */
	function popup_zip_match( $match, $rule ) {

		if ( $rule['operator'] == "==" ) {

			return geot_target_zip( $rule['value'] );

		} else {

			return ! geot_target_zip( $rule['value'] );
		}
	}


	/*
	* rule_match_country
	* @since 2.0.0
	*/
	public static function rule_match_country( $rule ) {

		$country_code = strtolower( geot_country_code() );

		$codes = array_map( 'strtolower', array_map( 'trim', explode( ',', $rule['value'] ) ) );

		if ( $rule['operator'] == "==" ) {
			return in_array( $country_code, $codes );
		}

		return ! in_array( $country_code, $codes );

	}


	/*
		* rule_match_city
		* @since 2.0.0
		*/
	public static function rule_match_city( $rule ) {

		$city_name = strtolower( geot_city_name() );

		$cities = array_map( 'strtolower', array_map( 'trim', explode( ',', $rule['value'] ) ) );

		if ( $rule['operator'] == "==" ) {
			return in_array( $city_name, $cities );
		}

		return ! in_array( $city_name, $cities );


	}

	/*
	* rule_match_state
	* @since 2.0.0
	*/
	public static function rule_match_state( $rule ) {

		$state_code = strtolower( geot_state_code() );
		$state      = strtolower( geot_state_name() );

		$codes_or_states = array_map( 'strtolower', array_map( 'trim', explode( ',', $rule['value'] ) ) );

		if ( $rule['operator'] == "==" ) {
			return in_array( $state_code, $codes_or_states ) || in_array( $state, $codes_or_states );
		}

		return ! in_array( $state_code, $codes_or_states ) && ! in_array( $state, $codes_or_states );

	}


	/*
	* rule_match_zip
	* @since 2.0.0
	*/
	public static function rule_match_zip( $rule ) {
		$zip  = strtolower( geot_zip() );
		$zips = array_map( 'strtolower', array_map( 'trim', explode( ',', $rule['value'] ) ) );

		if ( $rule['operator'] == "==" ) {
			return in_array( $zip, $zips );
		}

		return ! in_array( $zip, $zips );

	}


	/**
	 * rule_match_region
	 * @param  ARRAY $rule
	 * @return BOOL
	 */
	public static function rule_match_region( $rule ) {
		$settings = geot_country_regions();
		$country_code = strtolower( geot_country_code() );

		$regions = wp_list_pluck($settings, 'countries', 'name');
		$name = trim( $rule['value'] );

		if ( $rule['operator'] == "==" ) {
			return in_array( strtolower( $country_code ), array_map( 'strtolower', toArray( $regions[$name] ) ) );
		}

		return ! in_array( strtolower( $country_code ), array_map( 'strtolower', toArray( $regions[$name] ) ) );
	}

	/**
	 * rule_match_radius
	 * @param $rule
	 *
	 * @return bool
	 */
	public static function rule_match_radius( $rule ) {
		$array_value = array_map( 'trim', explode( '|', $rule['value'] ) );

		// Lat|Lng|Radius(km)
		if( count( $array_value ) != 3 )
			return false;

		if ( $rule['operator'] == '==' ) {
			return ( geot_target_radius( $array_value[0], $array_value[1], $array_value[2] ) );
		}

		return ( ! geot_target_radius( $array_value[0], $array_value[1], $array_value[2] ) );
	}

	/**
	 * rule_match_region
	 * @param  ARRAY $rule
	 * @return BOOL
	 */
	public static function rule_match_zip_region( $rule ) {
		$settings = geot_zip_regions();
		$zip_code = strtolower( geot_zip() );

		$regions = wp_list_pluck($settings, 'zips', 'name');
		$name = trim( $rule['value'] );

		if ( $rule['operator'] == "==" ) {
			return in_array( strtolower( $zip_code ), array_map( 'strtolower', toArray( $regions[$name] ) ) );
		}

		return ! in_array( strtolower( $zip_code ), array_map( 'strtolower', toArray( $regions[$name] ) ) );
	}	/**
	 * rule_match_region
	 * @param  ARRAY $rule
	 * @return BOOL
	 */
	public static function rule_match_city_region( $rule ) {
		$settings = geot_zip_regions();
		$city_name = strtolower( geot_city_name() );

		$regions = wp_list_pluck($settings, 'cities', 'name');
		$name = trim( $rule['value'] );

		if ( $rule['operator'] == "==" ) {
			return in_array( strtolower( $city_name ), array_map( 'strtolower', toArray( $regions[$name] ) ) );
		}

		return ! in_array( strtolower( $city_name ), array_map( 'strtolower', toArray(  $regions[$name] ) ) );
	}

	/**
	 * rule_match_region
	 * @param  ARRAY $rule
	 * @return BOOL
	 */
	public static function rule_match_state_region( $rule ) {
		$settings = geot_state_regions();
		$state_code = strtolower( geot_state_code() );
		$state      = strtolower( geot_state_name() );

		$regions = wp_list_pluck($settings, 'states', 'name');
		$name = trim( $rule['value'] );

		if ( $rule['operator'] == "==" ) {
			if(
				in_array( strtolower( $state_code ), array_map( 'strtolower', toArray( $regions[$name] ) ) )
			) {
				return $rule['operator'] == "==" ? true : false;
			}
			if(
				in_array( strtolower( $state ), array_map( 'strtolower', toArray( $regions[$name] ) ) )
			) {
				return $rule['operator'] == "==" ? true : false;
			}
			return $rule['operator'] == "==" ? false : true;
		}
	}

	/**
	 * rule_values_region
	 * @param  ARRAY $choices
	 * @return ARRAY
	 */
	public static function rule_values_region( $choices ) {
		$regions = geot_country_regions();

		$output = wp_list_pluck($regions, 'name', 'name');

		return $output;
	}

	/**
	 * rule_values_region
	 * @param  ARRAY $choices
	 * @return ARRAY
	 */
	public static function rule_values_city_region( $choices ) {
		$regions = geot_city_regions();

		$output = wp_list_pluck($regions, 'name', 'name');

		return $output;
	}
	/**
	 * rule_values_region
	 * @param  ARRAY $choices
	 * @return ARRAY
	 */
	public static function rule_values_zip_region( $choices ) {
		$regions = geot_zip_regions();

		$output = wp_list_pluck($regions, 'name', 'name');

		return $output;
	}
	/**
	 * rule_values_region
	 * @param  ARRAY $choices
	 * @return ARRAY
	 */
	public static function rule_values_state_region( $choices ) {
		$regions = geot_state_regions();

		$output = wp_list_pluck($regions, 'name', 'name');

		return $output;
	}

	/**
	 * Output Field
	 * @param  string $output
	 * @param  array  $args
	 * @return String
	 */
	public function field_region( $output = '', $args = [] ) {

		$regions = geot_country_regions();

		ob_start();
		include_once GEOWP_PLUGIN_DIR . 'includes/plugins/wppopups/field_region.php';

		$output = ob_get_contents();
		ob_end_clean();

		return $output;
	}
	/**
	 * Sanitize the regions option
	 * @param  ARRAY  $value
	 * @param  STRING $id
	 * @param  ARRAY  $field
	 * @param  STRING $value_prev
	 * @return ARRAY
	 */
	public function sanitize_region( $value, $id, $field, $value_prev ) {

		if( ! isset( $_POST['regions'] ) )
			return geot_country_regions();

		$i = 1;
		$output = [];

		foreach( $_POST['regions'] as $data ) {

			if( empty( $data['name'] ) )
				continue;

			if( ! is_array( $data['countries'] ) || count( $data['countries'] ) == 0 )
				continue;

			$output[$i] = [
				'name'		=> sanitize_title( $data['name'] ),
				'countries'	=> array_map( 'sanitize_text_field', $data['countries'] ),
			];

			$i++;
		}

		return $output;
	}

	/**
	 * Add field type for geolocation
	 *
	 * @param $type
	 * @param $rule
	 *
	 * @return string
	 */
	public function geolocation_field_type( $type, $rule ) {

		switch ( $rule ) {
			case 'geot_country':
			case 'geot_state':
			case 'geot_city':
			case 'geot_zip':
			case 'geot_ip':
				$type = 'text';
				break;
			case 'geot_city_region':
			case 'geot_country_region':
			case 'geot_state_region':
			case 'geot_zip_region':
				$type = 'select';
				break;
		}

		return $type;
	}
}
new Geot_Popups();