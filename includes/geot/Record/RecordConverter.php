<?php

namespace GeotFunctions\Record;

/**
 * Holds the record that API will return
 * @property  city
 * @package app\Http
 */
class RecordConverter {
	protected static $geot_record;
	protected $city;
	protected $continent;
	protected $country;
	protected $state;

	/**
	 * Normalize Maxmind to match our API results
	 *
	 * @param $record
	 *
	 * @return string
	 * @throws \Exception
	 */
	public static function maxmindRecord( $record ) {
		if ( isset( $record->error ) ) {
			throw new \Exception( $record->error );
		}

		self::$geot_record                                   = [];
		self::$geot_record['city']['names']                  = isset( $record['city'] ) && isset( $record['city']['names'] ) ? $record['city']['names'] : '';
		self::$geot_record['city']['zip']                    = isset( $record['postal'] ) && isset( $record['postal']['code'] ) ? $record['postal']['code'] : '';
		self::$geot_record['continent']['names']             = isset( $record['continent'] ) && isset( $record['continent']['names'] ) ? $record['continent']['names'] : '';
		self::$geot_record['continent']['iso_code']          = isset( $record['continent'] ) && isset( $record['continent']['code'] ) ? $record['continent']['code'] : '';
		self::$geot_record['country']['iso_code']            = isset( $record['country'] ) && isset( $record['country']['iso_code'] ) ? $record['country']['iso_code'] : '';
		self::$geot_record['country']['names']               = isset( $record['country'] ) && isset( $record['country']['names'] ) ? $record['country']['names'] : '';
		self::$geot_record['state']['iso_code']              = isset( $record['subdivisions'] ) && isset( $record['subdivisions'][0] ) && isset( $record['subdivisions'][0]['iso_code'] ) ? $record['subdivisions'][0]['iso_code'] : '';
		self::$geot_record['state']['names']                 = isset( $record['subdivisions'] ) && isset( $record['subdivisions'][0] ) && isset( $record['subdivisions'][0]['names'] ) ? $record['subdivisions'][0]['names'] : '';
		self::$geot_record['geolocation']['latitude']        = isset( $record['location'] ) && isset( $record['location']['latitude'] ) ? $record['location']['latitude'] : '';
		self::$geot_record['geolocation']['longitude']       = isset( $record['location'] ) && isset( $record['location']['longitude'] ) ? $record['location']['longitude'] : '';
		self::$geot_record['geolocation']['accuracy_radius'] = isset( $record['location'] ) && isset( $record['location']['accuracy_radius'] ) ? $record['location']['accuracy_radius'] : '';
		self::$geot_record['geolocation']['time_zone']       = isset( $record['location'] ) && isset( $record['location']['time_zone'] ) ? $record['location']['time_zone'] : '';

		return json_decode( json_encode( self::$geot_record ) );
	}

	/**
	 * Normalize Ip2location to match our api Results
	 *
	 * @param $record
	 *
	 * @return string
	 * @throws \Exception
	 */
	public static function ip2locationRecord( $record ) {
		if ( isset( $record['error'] ) ) {
			throw new \Exception( $record['error'] );
		}

		self::$geot_record                                   = [];
		self::$geot_record['city']['names']                  = isset( $record['cityName'] ) ? [ 'en' => $record['cityName'] ] : '';
		self::$geot_record['city']['zip']                    = isset( $record['zipCode'] ) ? $record['zipCode'] : '';
		self::$geot_record['continent']['names']             = '';
		self::$geot_record['continent']['iso_code']          = '';
		self::$geot_record['country']['iso_code']            = isset( $record['countryCode'] ) ? $record['countryCode'] : '';
		self::$geot_record['country']['names']               = isset( $record['countryName'] ) ? [ 'en' => $record['countryName'] ] : '';
		self::$geot_record['state']['iso_code']              = '';
		self::$geot_record['state']['names']                 = isset( $record['regionName'] ) ? [ 'en' => $record['regionName'] ] : '';
		self::$geot_record['geolocation']['latitude']        = $record['latitude'] ?: '';
		self::$geot_record['geolocation']['longitude']       = $record['longitude'] ?: '';
		self::$geot_record['geolocation']['accuracy_radius'] = $record['accuracyRadius'] ?: '';
		self::$geot_record['geolocation']['time_zone']       = $record['timeZone'] ?: '';

		return json_decode( json_encode( self::$geot_record ) );
	}

	public static function wpEngine() {
		if ( getenv( 'HTTP_GEOIP_COUNTRY_CODE' ) === false ) {
			throw new \Exception( 'WPEngine failed to return record' );
		}

		self::$geot_record                                   = [];
		self::$geot_record['city']['names']                  = getenv( 'HTTP_GEOIP_CITY' ) ? [ 'en' => getenv( 'HTTP_GEOIP_CITY' ) ] : '';
		self::$geot_record['city']['zip']                    = getenv( 'HTTP_GEOIP_POSTAL_CODE' ) ?: '';
		self::$geot_record['continent']['names']             = '';
		self::$geot_record['continent']['iso_code']          = '';
		self::$geot_record['country']['iso_code']            = getenv( 'HTTP_GEOIP_COUNTRY_CODE' ) ?: '';
		self::$geot_record['country']['names']               = getenv( 'HTTP_GEOIP_COUNTRY_NAME' ) ? [ 'en' => getenv( 'HTTP_GEOIP_COUNTRY_NAME' ) ] : '';
		self::$geot_record['state']['iso_code']              = getenv( 'HTTP_GEOIP_AREA_CODE' ) ?: '';
		self::$geot_record['state']['names']                 = getenv( 'HTTP_GEOIP_REGION' ) ? [ 'en' => getenv( 'HTTP_GEOIP_REGION' ) ] : '';
		self::$geot_record['geolocation']['latitude']        = getenv( 'HTTP_GEOIP_LATITUDE' ) ?: '';
		self::$geot_record['geolocation']['longitude']       = getenv( 'HTTP_GEOIP_LONGITUDE' ) ?: '';
		self::$geot_record['geolocation']['accuracy_radius'] = '';
		self::$geot_record['geolocation']['time_zone']       = '';

		return json_decode( json_encode( self::$geot_record ) );
	}

	public static function kinsta() {
		if ( empty( $_SERVER['HTTP_GEOIP_CITY_COUNTRY_NAME'] ) ) {
			throw new \Exception( 'Kinsta failed to return record' );
		}

		self::$geot_record                                   = [];
		self::$geot_record['city']['names']                  = isset( $_SERVER['HTTP_GEOIP_CITY'] ) ? [ 'en' => $_SERVER['HTTP_GEOIP_CITY'] ] : '';
		self::$geot_record['city']['zip']                    = isset( $_SERVER['HTTP_GEOIP_POSTAL_CODE'] ) ? $_SERVER['HTTP_GEOIP_POSTAL_CODE'] : '';
		self::$geot_record['continent']['names']             = '';
		self::$geot_record['continent']['iso_code']          = isset( $_SERVER['HTTP_GEOIP_CITY_CONTINENT_CODE'] ) ? $_SERVER['HTTP_GEOIP_CITY_CONTINENT_CODE'] : '';
		self::$geot_record['country']['iso_code']            = isset( $_SERVER['HTTP_GEOIP_CITY_COUNTRY_CODE'] ) ? $_SERVER['HTTP_GEOIP_CITY_COUNTRY_CODE'] : '';
		self::$geot_record['country']['names']               = isset( $_SERVER['HTTP_GEOIP_CITY_COUNTRY_NAME'] ) ? [ 'en' => $_SERVER['HTTP_GEOIP_CITY_COUNTRY_NAME'] ] : '';
		self::$geot_record['state']['iso_code']              = isset( $_SERVER['HTTP_GEOIP_REGION'] ) ? $_SERVER['HTTP_GEOIP_REGION'] : '';
		self::$geot_record['state']['names']                 = isset( $_SERVER['HTTP_GEOIP_REGION_NAME'] ) ? [ 'en' => $_SERVER['HTTP_GEOIP_REGION_NAME'] ] : '';
		self::$geot_record['geolocation']['latitude']        = isset( $_SERVER['HTTP_GEOIP_LATITUDE'] ) ? $_SERVER['HTTP_GEOIP_LATITUDE'] : '';
		self::$geot_record['geolocation']['longitude']       = isset( $_SERVER['HTTP_GEOIP_LONGITUDE'] ) ? $_SERVER['HTTP_GEOIP_LONGITUDE'] : '';
		self::$geot_record['geolocation']['accuracy_radius'] = '';
		self::$geot_record['geolocation']['time_zone']       = '';

		return json_decode( json_encode( self::$geot_record ) );
	}

	public static function litespeed() {
		if ( empty( $_SERVER['GEOIP_COUNTRY_CODE'] ) ) {
			throw new \Exception( 'Litespeed failed to return record' );
		}

		self::$geot_record                       = [];
		self::$geot_record['city']['names']      = isset( $_SERVER['GEOIP_CITY'] ) ? [ 'en' => $_SERVER['GEOIP_CITY'] ] : '';
		self::$geot_record['city']['zip']        = isset( $_SERVER['GEOIP_POSTAL_CODE'] ) ? $_SERVER['GEOIP_POSTAL_CODE'] : '';
		self::$geot_record['continent']['names'] = isset( $_SERVER['GEOIP_CONTINENT_NAME'] ) ? $_SERVER['GEOIP_CONTINENT_NAME'] : '';;
		self::$geot_record['continent']['iso_code']          = isset( $_SERVER['GEOIP_CONTINENT_CODE'] ) ? $_SERVER['GEOIP_CONTINENT_CODE'] : '';
		self::$geot_record['country']['iso_code']            = isset( $_SERVER['GEOIP_COUNTRY_CODE'] ) ? $_SERVER['GEOIP_COUNTRY_CODE'] : '';
		self::$geot_record['country']['names']               = isset( $_SERVER['GEOIP_COUNTRY_NAME'] ) ? [ 'en' => $_SERVER['GEOIP_COUNTRY_NAME'] ] : '';
		self::$geot_record['state']['iso_code']              = isset( $_SERVER['GEOIP_REGION'] ) ? $_SERVER['GEOIP_REGION'] : '';
		self::$geot_record['state']['names']                 = isset( $_SERVER['GEOIP_REGION_NAME'] ) ? [ 'en' => $_SERVER['GEOIP_REGION_NAME'] ] : '';
		self::$geot_record['geolocation']['latitude']        = isset( $_SERVER['GEOIP_LATITUDE'] ) ? $_SERVER['GEOIP_LATITUDE'] : '';
		self::$geot_record['geolocation']['longitude']       = isset( $_SERVER['GEOIP_LONGITUDE'] ) ? $_SERVER['GEOIP_LONGITUDE'] : '';
		self::$geot_record['geolocation']['accuracy_radius'] = '';
		self::$geot_record['geolocation']['time_zone']       = '';

		return json_decode( json_encode( self::$geot_record ) );
	}
}