<?php

/**
 * Grab geol settings
 * @return mixed|void
 */
function geol_settings() {
	$defaults = apply_filters( 'geol/settings_page/defaults', [
			'goto_page' => 'goto',
			'opt_stats' => '1',
		]
	);

	$opts = wp_parse_args( get_option( 'geol_settings' ), $defaults );

	return apply_filters( 'geol/settings_page/opts', $opts );
}


/**
 * Return the redirection options
 *
 * @param  int $id geol_cpt id
 *
 * @return array metadata values
 */
function geol_options( $id ) {
	$defaults = apply_filters( 'geol/metaboxes/defaults', [
			'source_slug'	=> '',
			'status_code'	=> '302',
			'dest_default'	=> '',
			'click_default'	=> 0,
			'count_click'	=> 0,
			'dest'        => [
				'dest_0' =>
					[
						'url'		=> '',
						'ref'		=> '',
						'countries'	=> array(),
						'regions'	=> array(),
						'states'	=> '',
						'zipcodes'	=> '',
						'cities'	=> '',
						'device'	=> '',
						'count_dest' => 0,
					],
			],
		]
	);

	$opts = wp_parse_args( get_post_meta( $id, 'geol_options', true ), $defaults );

	return apply_filters( 'geol/metaboxes/get_options', $opts, $id );
}

/**
 * Return the devices options
 * @return array metadata values
 */
function geol_devices() {
	return apply_filters( 'geol/devices/defaults', [
		'mobiles' => __( "Mobile Phone", 'geol' ),
		'tablets' => __( "Tablet", 'geol' ),
		'desktop' => __( "Dekstop", 'geol' ),
	] );
}

/**
 * Return the format to selectize
 * @return array values
 */
function format_selectize($data, $format = 'countries') {

	$output = array();

	switch($format) {
		case 'countries' :
				foreach($data as $list) {
					$output[] = array( 'text' => $list->country, 'value' => $list->iso_code );
				}
				break;

		case 'regions' :
				foreach($data as $list) {
					$output[] = array( 'text' => $list['name'], 'value' => $list['name'] );
				}
				break;
	}

	return $output;
}