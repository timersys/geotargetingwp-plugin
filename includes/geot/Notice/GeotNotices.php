<?php

namespace GeotCore\Notice;

use function GeotCore\hosting_has_db;

class GeotNotices {


	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.3.1
	 */
	public function __construct() {

		if ( isset( $_GET['geot_notice'] ) ) {
			update_option( 'geot_' . esc_attr( $_GET['geot_notice'] ), true );
		}

		if ( hosting_has_db() && ! get_option( 'geot_hostingdb_dismiss' ) ) {
			add_action( 'admin_notices', [ self::class, 'hostingdb' ] );
		}

	}

	public static function hostingdb() {
		?>
		<div class="notice-info error">
		<h3><i class=" dashicons-before dashicons-admin-site"></i> GeotargetingWP Hosting Database</h3>
		<p>We detected that your Hosting enabled a local database.</p>
		<p>Please go to the <a href="<?php echo admin_url( 'admin.php?page=geot-settings' ); ?>">settings page</a> and
			enable it for using it with the GeotargetingWP plugin.</p>
		<p><a href="<?= admin_url( '?geot_notice=hostingdb_dismiss' ); ?>"
		      class="button-primary"><?php _e( 'Dismiss', 'geot' ); ?></a></p>
		</div><?php
	}

}
