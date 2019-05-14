<div class="geot-setup-content">
	<form action="" method="POST">
		<?php wp_nonce_field( 'geot-setup' ); ?>

		<?php do_action( 'geot/wizard/addons/before' ); ?>

		<p><?php _e('The wizard below will help you configure your Geotargeting plugin and start working quickly.','letsgo'); ?></p>

		<div class="location-row">
			<label for="geo-flags" class="location-label">
				<input type="checkbox" id="geo-flags" name="geot_addons[geo-flags]" value="1" class="location-checkbox" <?php echo checked($opts['geo-flags'], true); ?> />
				<?php _e( 'Geo Flags', 'geot' ); ?>
			</label>
			<div class="location-help"><?php _e( 'Country Flags based on user location', 'geot' ); ?></div>
		</div>

		<div class="location-row">
			<label for="geo-links" class="location-label">
				<input type="checkbox" id="geo-links" name="geot_addons[geo-links]" value="1" class="location-checkbox" <?php echo checked($opts['geo-links'], true); ?> />
				<?php _e( 'Geo Links', 'geot' ); ?>
			</label>
			<div class="location-help"><?php _e( 'Geo Links for WordPress will let you geo-target your affiliate links', 'geot' ); ?></div>
		</div>

		<div class="location-row">
			<label for="geo-redirects" class="location-label">
				<input type="checkbox" id="geo-redirects" name="geot_addons[geo-redirects]" value="1" class="location-checkbox" <?php echo checked($opts['geo-redirects'], true); ?> />
				<?php _e( 'Geo Redirects', 'geot' ); ?>
			</label>
			<div class="location-help"><?php _e( 'Create redirects based on Countries, Cities or States. Add multiple rules', 'geot' ); ?></div>
		</div>

		<div class="location-row">
			<label for="geo-blocker" class="location-label">
				<input type="checkbox" id="geo-blocker" name="geot_addons[geo-blocker]" value="1" class="location-checkbox" <?php echo checked($opts['geo-blocker'], true); ?> />
				<?php _e( 'Geo Blocker', 'geot' ); ?>
			</label>
			<div class="location-help"><?php _e( 'Geo Blocker let you block access to your site based on geolocation', 'geot' ); ?></div>
		</div>

		<?php do_action( 'geot/wizard/addons/after' ); ?>

		<div class="location-row text-center">
			<input type="hidden" name="save_step" value="1" />
			<button class="button-primary button button-hero button-next location-button" name="geot_addons[button]"><?php _e('Next','geot'); ?></button>
		</div>
	</form>
</div>