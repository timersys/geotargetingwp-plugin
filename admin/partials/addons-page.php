<div class="wrap geot-settings">
	<form name="geot-settings" method="post" enctype="multipart/form-data">
		<table class="form-table">
			<tr valign="top" class="geot-settings-title">
				<th colspan="3"><h3><?php _e( 'Geotargeting Pro AddOns:', 'geot' ); ?></h3></th>
			</tr>

			<?php do_action( 'geot/addons/before', $opts ); ?>

			<tr valign="top" class="">
				<th><label for="geo-flags"><?php _e( 'Geo Flags', 'geot' ); ?></label></th>
				<td colspan="3">
					<label><input type="checkbox" id="geo-flags" name="geot_addons[geo-flags]"
					              value="1" <?php checked( $opts['geo-flags'], '1' ); ?>/>
						<p class="help"><?php _e( 'Country Flags based on user location', 'geot' ); ?></p>
				</td>
			</tr>
			<tr valign="top" class="">
				<th><label for="geo-links"><?php _e( 'Geo Links', 'geot' ); ?></label></th>
				<td colspan="3">
					<label><input type="checkbox" id="geo-links" name="geot_addons[geo-links]"
					              value="1" <?php checked( $opts['geo-links'], '1' ); ?>/>
						<p class="help"><?php _e( 'Geo Links for WordPress will let you geo-target your affiliate links', 'geot' ); ?></p>
				</td>
			</tr>
			<tr valign="top" class="">
				<th><label for="geo-redirects"><?php _e( 'Geo Redirects', 'geot' ); ?></label></th>
				<td colspan="3">
					<label><input type="checkbox" id="geo-redirects" name="geot_addons[geo-redirects]"
					              value="1" <?php checked( $opts['geo-redirects'], '1' ); ?>/>
						<p class="help"><?php _e( 'Create redirects based on Countries, Cities or States. Add multiple rules', 'geot' ); ?></p>
				</td>
			</tr>
			<tr valign="top" class="">
				<th><label for="geo-blocker"><?php _e( 'Geo Blocker', 'geot' ); ?></label></th>
				<td colspan="3">
					<label><input type="checkbox" id="geo-blocker" name="geot_addons[geo-blocker]"
					              value="1" <?php checked( $opts['geo-blocker'], '1' ); ?>/>
						<p class="help"><?php _e( 'Geo Blocker let you block access to your site based on geolocation', 'geot' ); ?></p>
				</td>
			</tr>

			<?php do_action( 'geot/addons/after', $opts ); ?>

			<tr>
				<td><input type="submit" name="geot_addons[button]" class="button-primary"
				           value="<?php _e( 'Save settings', 'geot' ); ?>"/></td>
				<input type="hidden" name="geot_return" value="<?php echo $return; ?>"/>
				<?php wp_nonce_field( 'geot_pro_save_settings', 'geot_nonce' ); ?>
		</table>
	</form>
</div>
