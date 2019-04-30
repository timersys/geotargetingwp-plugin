<div class="geot-notice notice notice-error is-dismissible" data-notice-id="geolinks-cache">
	<h3><i class=" dashicons-before dashicons-admin-site"></i> GeoLinks</h3>
	<p><?php _e('We detected that your have a cache plugin active.', 'geol'); ?></p>
	<p><?php _e('Please be sure to whitelist the geol_cpt custom post type in your cache plugin.', 'geol'); ?></p>
</div>
<script>
	jQuery(document).on('click', '.geot-notice .notice-dismiss', function() {
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