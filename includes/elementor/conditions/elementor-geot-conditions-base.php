<?php
use ElementorPro\Modules\ThemeBuilder\Conditions\Condition_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Elementor_GeoConditions_Base extends Condition_Base {

	public static function get_type() {
		return 'geot';
	}

	public function get_name() {
		return 'geot';
	}

	public function get_label() {
		return __( 'Geotargeting', 'geot' );
	}

	public function get_all_label() {
		return __( 'Entire Site', 'geot' );
	}

	public function check( $args ) {
		return true;
	}

	public function register_sub_conditions() {
		$GeoConditions_Country = new Elementor_GeoConditions_Country();
		$GeoConditions_Region_Country = new Elementor_GeoConditions_Region_Country();
		$GeoConditions_City = new Elementor_GeoConditions_City();
		$GeoConditions_Region_City = new Elementor_GeoConditions_Region_City();
		$GeoConditions_State = new Elementor_GeoConditions_State();
		$GeoConditions_Region_State = new Elementor_GeoConditions_Region_State();
		$GeoConditions_Zip = new Elementor_GeoConditions_Zip();
		$GeoConditions_Region_Zip = new Elementor_GeoConditions_Region_Zip();

		$this->register_sub_condition( $GeoConditions_Country );
		$this->register_sub_condition( $GeoConditions_Region_Country );
		$this->register_sub_condition( $GeoConditions_City );
		$this->register_sub_condition( $GeoConditions_Region_City );
		$this->register_sub_condition( $GeoConditions_State );
		$this->register_sub_condition( $GeoConditions_Region_State );
		$this->register_sub_condition( $GeoConditions_Zip );
		$this->register_sub_condition( $GeoConditions_Region_Zip );
	}
}
