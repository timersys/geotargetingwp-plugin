<?php

/**
 * Grab geol settings
 * @return mixed|void
 */
function geotWPL_settings() {
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
 * @param int $id geol_cpt id
 *
 * @return array metadata values
 */
function geotWPL_options( $id ) {
	$defaults = apply_filters( 'geol/metaboxes/defaults', [
			'source_slug'   => '',
			'status_code'   => '302',
			'dest_default'  => '',
			'click_default' => 0,
			'count_click'   => 0,
			'dest'          => [
				'dest_0' =>
					[
						'label'      => '',
						'url'        => '',
						'ref'        => '',
						'countries'  => [],
						'regions'    => [],
						'states'     => '',
						'zipcodes'   => '',
						'cities'     => '',
						'device'     => '',
						'count_dest' => 0,
					],
			],
		]
	);

	$geol = get_post_meta( $id, 'geol_options', true );
	$geol = empty( $geol ) ? [] : $geol;

	$opts = geotWPL_wp_parse_args( $geol, $defaults );

	return apply_filters( 'geol/metaboxes/get_options', $opts, $id );
}

/**
 * Return the devices options
 * @return array metadata values
 */
function geotWPL_devices() {
	return apply_filters( 'geol/devices/defaults', [
		'mobiles' => __( "Mobile Phone", 'geot' ),
		'tablets' => __( "Tablet", 'geot' ),
		'desktop' => __( "Desktop", 'geot' ),
	] );
}

/**
 * Return the format to selectize
 * @return array values
 */
function geotWPL_format_selectize( $data, $format = 'countries' ) {

	$output = [];

	switch ( $format ) {
		case 'countries' :
			foreach ( $data as $list ) {
				$output[] = [ 'text' => $list->country, 'value' => $list->iso_code ];
			}
			break;

		case 'regions' :
			foreach ( $data as $list ) {
				$output[] = [ 'text' => $list['name'], 'value' => $list['name'] ];
			}
			break;
	}

	return $output;
}


function geotWPL_wp_parse_args( &$a, $b ) {
	$a = (array) $a;
	$b = (array) $b;
	$result = $b;
	foreach ( $a as $k => &$v ) {
		if ( is_array( $v ) && isset( $result[ $k ] ) ) {
			$result[ $k ] = geotWPL_wp_parse_args( $v, $result[ $k ] );
		} else {
			$result[ $k ] = $v;
		}
	}
	return $result;
}