<div class="wppopups-setting-row wppopups-setting-row-<?php echo sanitize_html_class( $args['type'] ); ?> wppopups-clear'" id="wppopups-setting-row-<?php echo wppopups_sanitize_key( $args['id'] ); ?>">

	<span class="wppopups-setting-label">
		<label for="wppopups-setting-<?php echo wppopups_sanitize_key( $args['id'] ); ?>">
			<?php esc_html_e( 'Regions', 'wppopups-geolocation' ); ?>
		</label>
	</span>

	<span class="wppopups-setting-field">
		<div class="wppopups-setting-regions">

			<?php foreach( $regions as $i => $region ) : ?>
				<div class="wppopups-loop-regions" data-loop="<?php echo $i; ?>">
					<input type="text" name="regions[<?php echo $i; ?>][name]" placeholder="<?php esc_html_e('Region Name', 'wppopups-geolocation'); ?>" value="<?php echo $region['name']; ?>">
					<a href="#" data-loop="<?php echo $i; ?>" class="wpp-remove-region" title="<?php esc_html_e('Remove Region', 'wppopups-geolocation'); ?>">-</a>
					<br />
					<span class="choicesjs-select-wrap">
						<select name="regions[<?php echo $i; ?>][countries][]" class="choicesjs-select" multiple="multiple" placeholder="<?php esc_html_e( 'Select Countries', 'wppopups-geolocation' ); ?>">
							<?php foreach( wpp_get_countries() as $country_code => $country_name ) : ?>
								<option value="<?php echo $country_code; ?>" <?php selected( in_array( $country_code, $region['countries'], true ), true, true); ?>>
									<?php echo $country_name; ?>
								</option>
							<?php endforeach; ?>
						</select>
					</span>
				</div>
			<?php endforeach ?>

		</div>

		<a href="#" class="button wpp-add-region" title="Add Region"><?php esc_html_e( 'Add Region', 'wppopups-geolocation' ); ?></a>
	</span>
</div>