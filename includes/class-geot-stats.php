<?php

/**
 * The dashboard-specific functionality of the plugin.
 *
 * @link       https://geotargetingwp.com/geotargeting-pro
 * @since      1.0.0
 *
 * @package    GeoTarget
 * @subpackage GeoTarget/Stats
 */


/**
 * The dashboard-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the dashboard-specific stylesheet and JavaScript.
 *
 * @package    GeoTarget
 * @subpackage GeoTarget/Stats
 * @author     Your Name <email@example.com>
 */
class GeotWP_Stats {

	//protected $url_stats = 'https://geotargetingwp.com/data-usage';
	protected $url_stats = 'http://timersys.local/wp-admin/admin-ajax.php?action=received_webhook';
	protected $enable_stats = null;

	public function __construct() {

		add_action( 'plugins_loaded', [ $this, 'manage_task' ], 1, 0 );
		add_filter( 'cron_schedules', [ $this, 'create_weekly' ] );

		add_action( 'send_geot_stats', [ $this, 'send_geot_stats' ] );

		add_action( 'geotWP/deactivated', [ $this, 'deactivated'] );


		//Temporal
		add_action('wp_ajax_received_webhook', [ $this, 'received_webhook' ] );
		add_action('wp_ajax_nopriv_received_webhook', [ $this, 'received_webhook' ] );
		
	}

	//Temporal
	public function received_webhook() {

		update_option('galex_8', print_r($_POST,true));
		update_option('galex_9', print_r($_REQUEST,true));

		$inputJSON	= file_get_contents('php://input');

		update_option('galex_10', print_r($inputJSON,true));

		die();
	}


	public function create_weekly( $schedules ) {
		$schedules[ 'geot-weekly' ] = [
			'interval' => WEEK_IN_SECONDS,
			'display' => __( 'Weekly', 'geot' )
		];

		return $schedules;
	}

	public function manage_task() {

		if( wp_next_scheduled( 'send_geot_stats' ) && ! $this->enable_stats() ) {
			wp_clear_scheduled_hook( 'send_geot_stats' );
		}
			
		if( ! wp_next_scheduled( 'send_geot_stats' ) && $this->enable_stats() ) {
			wp_schedule_event( current_time( 'timestamp' ), 'geot-weekly', 'send_geot_stats' );
		}
	}


	public function deactivated() {
		if( wp_next_scheduled( 'send_geot_stats' ) ) {
			wp_clear_scheduled_hook( 'send_geot_stats' );
		}
	}

	/**
	 * Verify if is enable the share info option
	 * @return [type] [description]
	 */
	protected function enable_stats() {

		if( ! is_null( $this->enable_stats ) )
			return $this->enable_stats;

		$others = get_option( 'geot_pro_others', [] );

		if( isset( $others['geo-stats'] ) && $others['geo-stats'] == 'yes' )
			$this->enable_stats = true;
		else
			$this->enable_stats = false;

		return $this->enable_stats;
	}


	public function ping() {

		$test          = wp_safe_remote_post( $this->url_stats );
		$response_code = wp_remote_retrieve_response_code( $test );

		if ( is_wp_error( $test ) ) {
			/* translators: error message */
			return new WP_Error( 'error', sprintf( __( 'Error: Delivery URL cannot be reached: %s', 'geot' ), $test->get_error_message() ) );
		}

		if ( 200 !== $response_code ) {
			/* translators: error message */
			return new WP_Error( 'error', sprintf( __( 'Error: Delivery URL returned response code: %s', 'geot' ), absint( $response_code ) ) );
		}
	}


	public function send_geot_stats() {

		if( ! $this->enable_stats() ) {
			return;
		}

		if( is_wp_error( $this->ping() ) ) {
			return;
		}

		$start_time = microtime( true );
		
		// Setup request args.
		$http_args = array(
			'method'      => 'POST',
			'timeout'     => MINUTE_IN_SECONDS,
			'redirection' => 0,
			'httpversion' => '1.0',
			'blocking'    => true,
			'user-agent'  => 'Webhook',
			'body'        => trim( wp_json_encode( $this->get_stats_addons() ) ),
			'headers'     => [
				'Content-Type'			=> 'application/json',
				'X-GEOT-Webhook-Source'	=> home_url( '/' ),
			],
			'cookies'     => [],
		);
		$http_args = apply_filters( 'geot/stats/http', $http_args );

		// Webhook away!
		$response = wp_safe_remote_request( $this->url_stats, $http_args );

		return true;
	}



	protected function get_stats_addons() {

		$output = ['geot-redirect' => [], 'geot-block' => [], 'geo-link' => [] ];

		$geo_redirect = get_posts([
				'post_type'		=> ['geotr_cpt', 'geobl_cpt', 'geol_cpt'],
				'post_status'	=> 'publish',
			]
		);


		if( $geo_redirect ) {

			foreach($geo_redirect as $post) {

				switch($post->post_type) {
					case 'geotr_cpt' :
						$output['geot-redirect'][] = [
							'post_id'	=> $post->ID,
							'options'	=> get_post_meta($post->ID, 'geotr_options', true),
							'rules'		=> get_post_meta($post->ID, 'geotr_rules', true),
						];
						break;

					case 'geobl_cpt' :
						$output['geot-block'][] = [
							'post_id'	=> $post->ID,
							'options'	=> get_post_meta($post->ID, 'geobl_options', true),
							'rules'		=> get_post_meta($post->ID, 'geobl_rules', true),
						];
						break;

					case 'geol_cpt' :
						$output['geo-link'][] = [
							'post_id'	=> $post->ID,
							'options'	=> get_post_meta($post->ID, 'geol_options', true),
							'rules'		=> [],
						];
						break;
				}
			}
		}

		update_option('galex_1', print_r($output,true));


		return $output;
	}
	
}

