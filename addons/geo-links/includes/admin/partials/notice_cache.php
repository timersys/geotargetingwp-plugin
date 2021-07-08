<?php
$settings = geotWPL_settings();
?>
<div class="geot-notice notice notice-error is-dismissible" data-notice-id="geolinks-cache">
	<h3><i class=" dashicons-before dashicons-admin-site"></i> GeotWP_Links</h3>
	<p><?php _e( 'We detected that your have a cache plugin active.', 'geot' ); ?></p>
	<p><?php echo sprintf( __( 'Please be sure to whitelist in your cache plugin the Geolinks root url: %s ', 'geot' ), $settings['goto_page'] .'/' ); ?></p>
</div>
<script>
    jQuery(document).on('click', '.geot-notice .notice-dismiss', function () {
        var notice_id = jQuery(this).parent('.geot-notice').data('notice-id');
        jQuery.ajax({
            url: ajaxurl,
            data: {
                action: 'dismiss_geot_notice',
                notice: notice_id,
            }
        });
    });
</script>