<?php

use GeotCore\GeotCore;
use GeotWP\GeotargetingWP;

/**
 * Function to get instance of the class
 * @return GeotCore
 */
if ( ! function_exists( 'geotWP' ) ) {
	function geotWP() {
		return GeotCore::instance();
	}
}
/**
 * Grab user data
 *
 * @param $key [continent, country, state, city, geolocation]
 *
 * @return mixed
 */
if ( ! function_exists( 'geot_get' ) ) {
	function geot_get( $key ) {
		$g = geotWP();

		return $g->get( $key );
	}
}
/**
 * Get current user country
 *
 * @param string $locale
 *
 * @return object Current user country Record. Methods are $country->iso_code $country->name $country->names
 */
if ( ! function_exists( 'geot_user_country' ) ) {
	function geot_user_country( $locale = 'en' ) {
		$c = geot_get( 'country' );
		if ( $locale != 'en' && method_exists( $c, 'setDefaultLocale' ) ) {
			$c->setDefaultLocale( $locale );
		}

		return $c;
	}
}
/**
 * Gets User country by ip. Is not ip given current user country will show
 *
 * @param string $ip
 *
 * @return object Current user country record. Methods are $country->iso_code $country->name $country->names
 */
if ( ! function_exists( 'geot_country_by_ip' ) ) {
	function geot_country_by_ip( $ip = '' ) {
		$g = geotWP();

		return $g->getUserData( $ip, 'ip' )->country;
	}
}

/**
 * @param boolean $force to read the cookie
 */
if ( ! function_exists( 'geot_country' ) ) {
	function geot_country( $force = false ) {
		$g = geotWP();

		return $g->getUserData( '', '', $force )->country;
	}
}

/**
 * Grabs the whole result from API
 *
 * @param string $ip
 *
 * @return object
 */
if ( ! function_exists( 'geot_data' ) ) {
	function geot_data( $params = '', $key = 'ip', $force = false ) {
		$g = geotWP();

		return $g->getUserData( $params, $key, $force );
	}
}
/**
 * Displays the 2 character country for the current user
 * [geot_country_code]
 * @return  string country CODE
 **/
if ( ! function_exists( 'geot_country_code' ) ) {
	function geot_country_code() {
		return geot_get( 'country' )->iso_code;
	}
}
/**
 * Displays the country name for the current user
 * [geot_country_name]
 *
 * @param string $locale
 *
 * @return string country name
 */
if ( ! function_exists( 'geot_country_name' ) ) {
	function geot_country_name( $locale = 'en' ) {
		$c = geot_get( 'country' );
		if ( $locale != 'en' && method_exists( $c, 'setDefaultLocale' ) ) {
			$c->setDefaultLocale( $locale );
		}

		return $c->name;
	}
}

/**
 * Display the user city name
 * [geot_city_name]
 *
 * @param string $locale
 *
 * @return string
 */
if ( ! function_exists( 'geot_city_name' ) ) {
	function geot_city_name( $locale = 'en' ) {
		$c = geot_get( 'city' );
		if ( $locale != 'en' && method_exists( $c, 'setDefaultLocale' ) ) {
			$c->setDefaultLocale( $locale );
		}

		return $c->name;
	}
}

/**
 * Display the user state name
 * [geot_state_name]
 *
 * @param string $locale
 *
 * @return string
 */
if ( ! function_exists( 'geot_state_name' ) ) {
	function geot_state_name( $locale = 'en' ) {
		$s = geot_get( 'state' );
		if ( $locale != 'en' && method_exists( $s, 'setDefaultLocale' ) ) {
			$s->setDefaultLocale( $locale );
		}

		return $s->name;
	}
}
/**
 * Display the user state code
 * [geot_state_code]
 * @return string
 */
if ( ! function_exists( 'geot_state_code' ) ) {
	function geot_state_code() {
		return geot_get( 'state' )->iso_code;
	}
}

/**
 * Display the user continent
 * [geot_continent]
 *
 * @param string $locale
 *
 * @return string
 */
if ( ! function_exists( 'geot_continent' ) ) {
	function geot_continent( $locale = 'en' ) {
		$c = geot_get( 'continent' );
		if ( $locale != 'en' && method_exists( $c, 'setDefaultLocale' ) ) {
			$c->setDefaultLocale( $locale );
		}

		return $c->name;
	}
}
/**
 * Displays the zip code
 * [geot_zip]
 * @return  string zip code
 **/
if ( ! function_exists( 'geot_zip' ) ) {
	function geot_zip() {
		return geot_get( 'city' )->zip;
	}
}
/**
 * Gets user geolocation data
 *
 * @return object ->longitude() , ->latitude(), ->time_zone()
 */
if ( ! function_exists( 'geot_location' ) ) {
	function geot_location() {
		return geot_get( 'geolocation' );
	}
}
/**
 * [geot_time_zone]
 * @return string time_zone
 */
if ( ! function_exists( 'geot_time_zone' ) ) {
	function geot_time_zone() {
		return geot_get( 'geolocation' )->time_zone;
	}
}
/**
 * Accuracy radius, where higher means less accurate
 * [geot_radius]
 * @return string radius
 */
if ( ! function_exists( 'geot_radius' ) ) {
	function geot_radius() {
		return geot_get( 'geolocation' )->accuracy_radius;
	}
}
/**
 * [geot_lat]
 * @return string latitude
 */
if ( ! function_exists( 'geot_lat' ) ) {
	function geot_lat() {
		return geot_get( 'geolocation' )->latitude;
	}
}
/**
 * [geot_lng]
 * @return string longitude
 */
if ( ! function_exists( 'geot_lng' ) ) {
	function geot_lng() {
		return geot_get( 'geolocation' )->longitude;
	}
}
/**
 * Gets User state by ip. Is not ip given current user country will show
 *
 * @param string $ip
 *
 * @return object Current user state. Values are $state->isoCode $state->name
 */
if ( ! function_exists( 'geot_state_by_ip' ) ) {
	function geot_state_by_ip( $ip = '' ) {
		$data = geot_data( $ip, 'ip' );

		return $data->state;
	}
}
/**
 * Get cities in database
 *
 * @param string $country
 *
 * @return object cities names with country codes
 */
if ( ! function_exists( 'geot_get_cities' ) ) {
	function geot_get_cities( $country = 'US' ) {

		$cities = get_option( 'geot_cities' . $country );

		if ( false === $cities ) {
			$cities = GeotargetingWP::getCities( $country );
			update_option( 'geot_cities' . $country, $cities );
		}

		return $cities;

	}
}
/**
 * Return json data for choices js select
 *
 * @param $country
 *
 * @return string
 */
if ( ! function_exists( 'geot_get_cities_choices' ) ) {
	function geot_get_cities_choices( $country ) {
		$cities  = geot_get_cities( $country );
		$choices = json_encode( array_map(
			function ( $a ) {
				return [ 'name' => $a->city, 'id' => $a->city ];
			}, json_decode( $cities ) ) );

		return $choices;
	}
}
/**
 * Check for current user if belong to any regions and return the name of them
 * or return default
 *
 * @param string $default
 *
 * @return Array/String
 */
if ( ! function_exists( 'geot_user_country_region' ) ) {
	function geot_user_country_region( $default = '' ) {

		$country_code = geot_country_code();
		$regions      = geot_country_regions();

		if ( empty( $regions ) || ! is_array( $regions ) || empty( $country_code ) ) {
			return $default;
		}

		$user_regions = [];
		foreach ( $regions as $region ) {
			if ( isset( $region['countries'] ) && in_array( $country_code, $region['countries'] ) ) {
				$user_regions[] = $region['name'];
			}
		}

		return empty( $user_regions ) ? $default : $user_regions;

	}
}

/**
 * Check for current user if belong to any city regions and return the name of them
 * or return default
 *
 * @param string $default
 *
 * @return Array/String
 */
if ( ! function_exists( 'geot_user_city_region' ) ) {
	function geot_user_city_region( $default = '' ) {

		$city_name = geot_city_name();
		$regions   = geot_city_regions();

		if ( empty( $regions ) || ! is_array( $regions ) || empty( $city_name ) ) {
			return $default;
		}

		$user_regions = [];
		foreach ( $regions as $region ) {
			if ( in_array( $city_name, $region['cities'] ) ) {
				$user_regions[] = $region['name'];
			}
		}

		return empty( $user_regions ) ? $default : $user_regions;

	}
}

/**
 * Check for current user if belong to any state regions and return the name of them
 * or return default
 *
 * @param string $default
 *
 * @return Array/String
 */
if ( ! function_exists( 'geot_user_state_region' ) ) {
	function geot_user_state_region( $default = '' ) {

		$state_name = geot_state_name();
		$state_code = geot_state_code();
		$regions   = geot_state_regions();

		if ( empty( $regions ) || ! is_array( $regions ) || ( empty( $state_code ) && empty( $state_name ) ) ) {
			return $default;
		}

		$user_regions = [];
		foreach ( $regions as $region ) {
			if ( in_array( $state_name, $region['states'] ) || in_array( $state_code, $region['states'] )) {
				$user_regions[] = $region['name'];
			}
		}

		return empty( $user_regions ) ? $default : $user_regions;

	}
}


/**
 * Check for current user if belong to any zip regions and return the name of them
 * or return default
 *
 * @param string $default
 *
 * @return Array/String
 */
if ( ! function_exists( 'geot_user_zip_region' ) ) {
	function geot_user_zip_region( $default = '' ) {

		$zip = geot_zip();

		if ( empty( $regions ) || ! is_array( $regions ) || empty( $zip ) ) {
			return $default;
		}

		$user_regions = [];
		foreach ( $regions as $region ) {
			if ( in_array( $zip, $region['zips'] ) ) {
				$user_regions[] = $region['name'];
			}
		}

		return empty( $user_regions ) ? $default : $user_regions;

	}
}

/**
 * if( ! function_exists( 'that M a i {
 * function that return is current user target the given countries / regions or not
 * Originally was to target also cities so I left that just in case but now we use geot_target_city
 *
 * @param string $include
 * @param string $place_region
 * @param string $exclude
 * @param string $exclude_region
 *
 * @param string $key
 *
 * @return bool
 */
if ( ! function_exists( 'geot_target' ) ) {
	function geot_target( $include = '', $place_region = '', $exclude = '', $exclude_region = '', $key = 'country' ) {
		$g    = geotWP();
		$args = [
			'include'        => $include,
			'exclude'        => $exclude,
			'region'         => $place_region,
			'exclude_region' => $exclude_region,
		];

		return $g->target( $key, $args );
	}
}
/**
 * if( ! function_exists( 'that M a i {
 * function that return is current user target the given city / regions or not
 *
 * @param string $city single city or comma list of cities
 * @param string $city_region
 * @param string $exclude
 * @param string $exclude_region
 *
 * @return bool
 */
if ( ! function_exists( 'geot_target_city' ) ) {
	function geot_target_city( $city = '', $city_region = '', $exclude = '', $exclude_region = '' ) {
		return geot_target( $city, $city_region, $exclude, $exclude_region, 'city' );
	}
}
/**
 * if( ! function_exists( 'that M a i {
 * function that return is current user target the given state or not
 *
 * @param string $state single state or comma separated list of states
 * @param string $exclude
 *
 * @return bool
 */
if ( ! function_exists( 'geot_target_state' ) ) {
	function geot_target_state( $state = '', $state_region = '', $exclude = '', $exclude_region = '' ) {
		return geot_target( $state, $state_region, $exclude, $exclude_region, 'state' );
	}
}
/**
 * if( ! function_exists( 'that M a i {
 * function that return is current user target the given state or not
 *
 * @param string $zip single state or comma separated list of states
 * @param string $exclude
 *
 * @return bool
 */
if ( ! function_exists( 'geot_target_zip' ) ) {
	function geot_target_zip( $zip = '', $zip_region = '', $exclude = '', $exclude_region = '' ) {
		return geot_target( $zip, $zip_region, $exclude, $exclude_region, 'zip' );
	}
}

if ( ! function_exists( 'geot_target_radius' ) ) {
	function geot_target_radius( $radius_lat = '', $radius_lng = '', $radius_km = 100 ) {
		$g = geotWP();

		return $g->targetRadius( $radius_lat, $radius_lng, $radius_km );
	}
}

/**
 * Grab geot settings
 * @return mixed|void
 */
if ( ! function_exists( 'geot_settings' ) ) {
	function geot_settings() {

		$settings = get_option( 'geot_settings' );

		return apply_filters( 'geot/settings_page/opts', $settings );
	}
}
/**
 * Return Country Regions
 * @return mixed
 */
if ( ! function_exists( 'geot_country_regions' ) ) {
	function geot_country_regions() {
		return apply_filters( 'geot/get_country_regions', [] );
	}
}
/**
 * Return City Regions
 * @return mixed
 */
if ( ! function_exists( 'geot_city_regions' ) ) {
	function geot_city_regions() {
		return apply_filters( 'geot/get_city_regions', [] );
	}
}
/**
 * Return State Regions
 * @return mixed
 */
if ( ! function_exists( 'geot_state_regions' ) ) {
	function geot_state_regions() {
		return apply_filters( 'geot/get_state_regions', [] );
	}
}
/**
 * Return Zip Regions
 * @return mixed
 */
if ( ! function_exists( 'geot_zip_regions' ) ) {
	function geot_zip_regions() {
		return apply_filters( 'geot/get_zip_regions', [] );
	}
}
/**
 * Grab countries from database
 * @return mixed
 */
if ( ! function_exists( 'geot_countries' ) ) {
	function geot_countries() {
		return apply_filters( 'geot/get_countries', [] );
	}
}
/**
 * Grab user IP from different possible sources
 * @return string
 */
if ( ! function_exists( 'geot_ips' ) ) {
	function geot_ips() {
		return apply_filters( 'geot/user_ip', '' );
	}
}

if ( ! function_exists( 'geot_set_coords' ) ) {
	function geot_set_coords( $lat, $lng ) {
		$g = geotWP();

		return $g->set_coords( $lat, $lng );
	}
}

/**
 * Prints geo debug data
 * @return bool|string
 */
if ( ! function_exists( 'geot_debug_data' ) ) {
	function geot_debug_data() {
		$user_data = geot_data();
		$opts = geot_settings();
		if ( empty( $user_data->country ) ) {
			return false;
		}
		ob_start();
		?>
		Country: <?php echo $user_data->country->name . PHP_EOL . '<br>'; ?>
		Country code: <?php echo $user_data->country->iso_code . PHP_EOL . '<br>'; ?>
		State: <?php echo $user_data->state->name . PHP_EOL . '<br>'; ?>
		State code: <?php echo $user_data->state->iso_code . PHP_EOL . '<br>'; ?>
		City: <?php echo $user_data->city->name . PHP_EOL . '<br>'; ?>
		Zip: <?php echo $user_data->city->zip . PHP_EOL . '<br>'; ?>
		Continent: <?php echo $user_data->continent->name . PHP_EOL . '<br>'; ?>
		Geolocation: { <br>
		Time zone: <?php echo $user_data->geolocation->time_zone . PHP_EOL . '<br>'; ?>
		Accuracy radius: <?php echo $user_data->geolocation->accuracy_radius . PHP_EOL . '<br>'; ?>
		Lat: <?php echo $user_data->geolocation->latitude . PHP_EOL . '<br>'; ?>
		Lng: <?php echo $user_data->geolocation->longitude . PHP_EOL . '<br>'; ?>
		}<br>
		Default IP: <?php echo GeotWP\getUserIP() . PHP_EOL . '<br>'; ?>
		Ip being used: <?php echo apply_filters( 'geot/user_ip', GeotWP\getUserIP() ) . PHP_EOL . '<br>'; ?>
		Geot Version: <?php echo defined( 'GEOT_VERSION' ) ? GEOT_VERSION . PHP_EOL . '<br>' : ''; ?>
		PHP Version: <?php echo phpversion() . PHP_EOL. '<br>' ; ?>
		Geo Method: <?php echo $opts['geolocation'] . PHP_EOL. '<br>' ; ?>
		Host:   <?= $_SERVER['HTTP_HOST'] ?: 'no set' . PHP_EOL . '<br>' ?>
		<?php
		$html = ob_get_contents();
		ob_end_clean();

		return $html;
	}
}
/**
 * Return Predefined Country Regions
 * @return mixed
 */
if ( ! function_exists( 'geot_predefined_regions' ) ) {
	function geot_predefined_regions() {

		$regions =
			[
				[
					'name'      => 'africa',
					'countries' => [
						'AO',
						'BF',
						'BI',
						'BJ',
						'BW',
						'CD',
						'CF',
						'CG',
						'CI',
						'CM',
						'CV',
						'DJ',
						'DZ',
						'EG',
						'EH',
						'ER',
						'ET',
						'GA',
						'GH',
						'GM',
						'GN',
						'GQ',
						'GW',
						'KE',
						'KM',
						'LR',
						'LS',
						'LY',
						'MA',
						'MG',
						'ML',
						'MR',
						'MU',
						'MW',
						'MZ',
						'NA',
						'NE',
						'NG',
						'RE',
						'RW',
						'SC',
						'SD',
						'SH',
						'SL',
						'SN',
						'SO',
						'ST',
						'SZ',
						'TD',
						'TG',
						'TN',
						'TZ',
						'UG',
						'YT',
						'ZA',
						'ZM',
						'ZW',
					],
				],

				[ 'name' => 'antarctica', 'countries' => [ 'AQ', 'BV', 'GS', 'HM', 'TF' ] ],

				[
					'name'      => 'asia',
					'countries' => [
						'AE',
						'AF',
						'AM',
						'AP',
						'AZ',
						'BD',
						'BH',
						'BN',
						'BT',
						'CC',
						'CN',
						'CX',
						'CY',
						'GE',
						'HK',
						'ID',
						'IL',
						'IN',
						'IO',
						'IQ',
						'IR',
						'JO',
						'JP',
						'KG',
						'KH',
						'KP',
						'KR',
						'KW',
						'KZ',
						'LA',
						'LB',
						'LK',
						'MM',
						'MN',
						'MO',
						'MV',
						'MY',
						'NP',
						'OM',
						'PH',
						'PK',
						'PS',
						'QA',
						'SA',
						'SG',
						'SY',
						'TH',
						'TJ',
						'TL',
						'TM',
						'TW',
						'UZ',
						'VN',
						'YE',
					],
				],

				[
					'name'      => 'europe',
					'countries' => [
						'AD',
						'AL',
						'AT',
						'AX',
						'BA',
						'BE',
						'BG',
						'BY',
						'CH',
						'CZ',
						'DE',
						'DK',
						'EE',
						'ES',
						'EU',
						'FI',
						'FO',
						'FR',
						'FX',
						'GB',
						'GG',
						'GI',
						'GR',
						'HR',
						'HU',
						'IE',
						'IM',
						'IS',
						'IT',
						'JE',
						'LI',
						'LT',
						'LU',
						'LV',
						'MC',
						'MD',
						'ME',
						'MK',
						'MT',
						'NL',
						'NO',
						'PL',
						'PT',
						'RO',
						'RS',
						'RU',
						'SE',
						'SI',
						'SJ',
						'SK',
						'SM',
						'TR',
						'UA',
						'VA',
					],
				],

				[
					'name'      => 'north-america',
					'countries' => [
						'AG',
						'AI',
						'AN',
						'AW',
						'BB',
						'BL',
						'BM',
						'BS',
						'BZ',
						'CA',
						'CR',
						'CU',
						'DM',
						'DO',
						'GD',
						'GL',
						'GP',
						'GT',
						'HN',
						'HT',
						'JM',
						'KN',
						'KY',
						'LC',
						'MF',
						'MQ',
						'MS',
						'MX',
						'NI',
						'PA',
						'PM',
						'PR',
						'SV',
						'TC',
						'TT',
						'US',
						'VC',
						'VG',
						'VI',
					],
				],

				[
					'name'      => 'oceania',
					'countries' => [
						'AS',
						'AU',
						'CK',
						'FJ',
						'FM',
						'GU',
						'KI',
						'MH',
						'MP',
						'NC',
						'NF',
						'NR',
						'NU',
						'NZ',
						'PF',
						'PG',
						'PN',
						'PW',
						'SB',
						'TK',
						'TO',
						'TV',
						'UM',
						'VU',
						'WF',
						'WS',
					],
				],

				[
					'name'      => 'south-america',
					'countries' => [
						'AR',
						'BO',
						'BR',
						'CL',
						'CO',
						'EC',
						'FK',
						'GF',
						'GY',
						'PE',
						'PY',
						'SR',
						'UY',
						'VE',
					],
				],

			];

		return apply_filters( 'geot/get_predefined_regions', $regions );
	}
}