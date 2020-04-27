<?php

namespace GeotCore\Record;

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

	/**
	 * Find most common variables used by hostings such as Kinsta/WPEngine or litespeed
	 * @return mixed
	 * @throws \Exception
	 */
	public static function hosting_db() {
		if ( empty( $_SERVER['GEOIP_COUNTRY_CODE'] )
		     && empty( getenv( 'HTTP_GEOIP_COUNTRY_CODE' ) )
		     && empty( $_SERVER['HTTP_GEOIP_COUNTRY_CODE'] )
		     && empty( $_SERVER['HTTP_GEOIP_CITY_COUNTRY_CODE'] ) ) {
			throw new \Exception( 'Your hosting db failed to return record' );
		}
		$city_name = '';
		if(  isset( $_SERVER['GEOIP_CITY'] ) ){
			$city_name = [ 'en' => $_SERVER['GEOIP_CITY'] ];
		} elseif( isset( $_SERVER['HTTP_GEOIP_CITY'] ) ) {
			$city_name = [ 'en' => $_SERVER['HTTP_GEOIP_CITY'] ];
		} elseif( getenv( 'HTTP_GEOIP_CITY' ) ) {
			$city_name = [ 'en' => getenv( 'HTTP_GEOIP_CITY' ) ];
		}

		$city_zip = '';
		if(  isset( $_SERVER['HTTP_GEOIP_POSTAL_CODE'] ) ){
			$city_zip = $_SERVER['HTTP_GEOIP_POSTAL_CODE'];
		} elseif( isset( $_SERVER['GEOIP_POSTAL_CODE'] ) ) {
			$city_zip = $_SERVER['GEOIP_POSTAL_CODE'];
		} elseif( getenv( 'HTTP_GEOIP_POSTAL_CODE' ) ) {
			$city_zip = getenv( 'HTTP_GEOIP_POSTAL_CODE' );
		}

		$continent = '';
		if(  isset( $_SERVER['GEOIP_CONTINENT_NAME'] ) ){
			$continent = [ 'en' => $_SERVER['GEOIP_CONTINENT_NAME'] ];
		} elseif( isset( $_SERVER['HTTP_GEOIP_CITY_CONTINENT_CODE'] ) ) {
			$continent = [ 'en' => $_SERVER['HTTP_GEOIP_CITY_CONTINENT_CODE'] ];
		} elseif( getenv( 'HTTP_GEOIP_CITY_CONTINENT_CODE' ) ) {
			$continent = [ 'en' => getenv( 'HTTP_GEOIP_CITY_CONTINENT_CODE' ) ];
		}

		$continent_code = '';
		if(  isset( $_SERVER['GEOIP_CONTINENT_CODE'] ) ){
			$continent_code = $_SERVER['GEOIP_CONTINENT_CODE'];
		} elseif( isset( $_SERVER['HTTP_GEOIP_CITY_CONTINENT_CODE'] ) ) {
			$continent_code = $_SERVER['HTTP_GEOIP_CITY_CONTINENT_CODE'];
		} elseif( getenv( 'HTTP_GEOIP_CITY_CONTINENT_CODE' ) ) {
			$continent_code = getenv( 'HTTP_GEOIP_CITY_CONTINENT_CODE' );
		}

		$country_code = '';
		if(  isset( $_SERVER['GEOIP_COUNTRY_CODE'] ) ){
			$country_code = $_SERVER['GEOIP_COUNTRY_CODE'];
		} elseif( isset( $_SERVER['HTTP_GEOIP_CITY_COUNTRY_CODE'] ) ) {
			$country_code = $_SERVER['HTTP_GEOIP_CITY_COUNTRY_CODE'];
		} elseif( getenv( 'HTTP_GEOIP_COUNTRY_CODE' ) ) {
			$country_code = getenv( 'HTTP_GEOIP_COUNTRY_CODE' );
		}

		$country_name = '';
		if(  isset( $_SERVER['GEOIP_COUNTRY_NAME'] ) ){
			$country_name = [ 'en' => $_SERVER['GEOIP_COUNTRY_NAME'] ];
		} elseif( isset( $_SERVER['HTTP_GEOIP_CITY_COUNTRY_NAME'] ) ) {
			$country_name = [ 'en' => $_SERVER['HTTP_GEOIP_CITY_COUNTRY_NAME'] ];
		} elseif( getenv( 'HTTP_GEOIP_COUNTRY_NAME' ) ) {
			$country_name = [ 'en' => getenv( 'HTTP_GEOIP_COUNTRY_NAME' ) ];
		}

		$state_code = '';
		if(  isset( $_SERVER['GEOIP_REGION'] ) ){
			$state_code = $_SERVER['GEOIP_REGION'];
		} elseif( isset( $_SERVER['HTTP_GEOIP_REGION'] ) ) {
			$state_code = $_SERVER['HTTP_GEOIP_REGION'];
		} elseif( getenv( 'HTTP_GEOIP_AREA_CODE' ) ) {
			$state_code = getenv( 'HTTP_GEOIP_AREA_CODE' );
		}

		$state_name = '';
		if(  isset( $_SERVER['GEOIP_REGION_NAME'] ) ){
			$state_name = [ 'en' => $_SERVER['GEOIP_REGION_NAME'] ];
		} elseif( isset( $_SERVER['HTTP_GEOIP_REGION_NAME'] ) ) {
			$state_name = [ 'en' => $_SERVER['HTTP_GEOIP_REGION_NAME'] ];
		} elseif( getenv( 'HTTP_GEOIP_REGION' ) ) {
			$state_name = [ 'en' => getenv( 'HTTP_GEOIP_REGION' ) ];
		}

		$lat = '';
		if(  isset( $_SERVER['GEOIP_LATITUDE'] ) ){
			$lat = $_SERVER['GEOIP_LATITUDE'];
		} elseif( isset( $_SERVER['HTTP_GEOIP_LATITUDE'] ) ) {
			$lat = $_SERVER['HTTP_GEOIP_LATITUDE'];
		} elseif( getenv( 'HTTP_GEOIP_LATITUDE' ) ) {
			$lat = getenv( 'HTTP_GEOIP_LATITUDE' );
		}

		$lng = '';
		if(  isset( $_SERVER['GEOIP_LONGITUDE'] ) ){
			$lng = $_SERVER['GEOIP_LONGITUDE'];
		} elseif( isset( $_SERVER['HTTP_GEOIP_LONGITUDE'] ) ) {
			$lng = $_SERVER['HTTP_GEOIP_LONGITUDE'];
		} elseif( getenv( 'HTTP_GEOIP_LONGITUDE' ) ) {
			$lng = getenv( 'HTTP_GEOIP_LONGITUDE' );
		}
		self::$geot_record                                   = [];
		self::$geot_record['city']['names']                  = $city_name;
		self::$geot_record['city']['zip']                    = $city_zip;
		self::$geot_record['continent']['names']             = $continent;
		self::$geot_record['continent']['iso_code']          = $continent_code;
		self::$geot_record['country']['iso_code']            = $country_code;
		self::$geot_record['country']['names']               = $country_name;
		self::$geot_record['state']['iso_code']              = $state_code;
		self::$geot_record['state']['names']                 = $state_name;
		self::$geot_record['geolocation']['latitude']        = $lat;
		self::$geot_record['geolocation']['longitude']       = $lng;
		self::$geot_record['geolocation']['accuracy_radius'] = '';
		self::$geot_record['geolocation']['time_zone']       = '';

		return json_decode( json_encode( self::$geot_record ) );
	}
}