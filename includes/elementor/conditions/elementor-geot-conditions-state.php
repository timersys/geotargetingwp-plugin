<?php
use Elementor\Controls_Manager;
use ElementorPro\Modules\ThemeBuilder\Conditions\Condition_Base;
use ElementorPro\Modules\QueryControl\Module as QueryModule;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Elementor_GeoConditions_State extends Condition_Base {

	public static function get_type() {
		return 'geot';
	}

	public static function get_priority() {
		return 40;
	}

	public function get_name() {
		return 'geot_state';
	}

	public function get_label() {
		return __( 'State', 'geot' );
	}

	public function get_all_label() {
		return __( 'All States', 'geot' );
	}

	public function check( $args ) {

		if( isset( $args['id'] ) ) {
			$id = sanitize_text_field( $args['id'] );
			return geot_target_state( $id );
		}

		return false;
	}

	protected function _register_controls() {
		$this->add_control(
			'country',
			[
				'section'	=> 'settings',
				'type'		=> Controls_Manager::TEXT,
			]
		);
	}
}
