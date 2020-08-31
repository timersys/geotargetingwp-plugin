<?php

/**
 * Metabox settings
 *
 *
 * @link       https://geotargetingwp.com/geotargeting-pro
 * @since      1.0.0
 *
 * @package    GeoTarget
 * @subpackage GeoTarget/admin/partials
 */
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>
<table class="form-table geot_table">

	<?php do_action( 'geot/metaboxes/before_display_options', $opts ); ?>
	<tr valign="top">
		<th><label for="geot_position"><?php _e( 'Choose:', 'geot' ); ?></label></th>
		<td>

			<input type="radio" class="geot_include_mode" name="geot[geot_include_mode]"
			       value="include" <?php checked( $opts['geot_include_mode'], 'include', true ); ?>> <strong>Show
				in:</strong><br/>
			<input type="radio" class="geot_include_mode" name="geot[geot_include_mode]"
			       value="exclude" <?php checked( $opts['geot_include_mode'], 'exclude', true ); ?>> <strong>Never show
				in</strong><br/>

		</td>
		<td colspan="2"></td>
	</tr>

	<tr valign="top">
		<th><label for="geot_position"><?php _e( 'Country Regions:', 'geot' ); ?></label></th>
		<td>
			<?php
			if ( is_array( $regions ) ) { ?>
				<select name="geot[region][]" multiple class="geot-chosen-select-multiple"
				        data-placeholder="<?php _e( 'Choose country region name...', 'geot' ); ?>">
					<?php
					if ( is_array( $regions ) ) {
						foreach ( $regions as $r ) {
							if ( ! is_array( $opts ) || ! isset( $r['name'] ) ) {
								continue;
							}
							?>
							<option value="<?php echo $r['name']; ?>" <?php
							if ( isset( $opts['region'] ) ) {
								selected( true, @in_array( $r['name'], $opts['region'] ) );
							}
							?>> <?php echo $r['name']; ?></option>
							<?php
						}
					}
					?>
				</select>
				<?php
			} else { ?>

				<p> Add some regions first.</p>

				<?php
			} ?>
		</td>
		<td colspan="2"></td>
	</tr>
	<tr valign="top">
		<th><label for="geot_position"><?php _e( 'Countries:', 'geot' ); ?></label></th>
		<td>
			<select name="geot[country_code][]" multiple class="geot-chosen-select-multiple"
			        data-placeholder="<?php _e( 'Choose country name...', 'geot' ); ?>">
				<?php
				if ( is_array( $countries ) ) {
					foreach ( $countries as $c ) {
						if ( ! is_array( $opts ) || ! isset( $c->iso_code ) ) {
							continue;
						}
						?>
						<option value="<?php echo $c->iso_code; ?>" <?php
						if ( isset( $opts['country_code'] ) ) {
							selected( true, @in_array( $c->iso_code, @(array) $opts['country_code'] ) );
						}
						?>> <?php echo $c->country; ?></option>
						<?php
					}
				}
				?>
			</select>
		</td>
		<td colspan="2"></td>
	</tr>
	<tr valign="top">
		<th><label for="geot_position"><?php _e( 'City Regions:', 'geot' ); ?></label></th>
		<td>
			<?php
			if ( is_array( $city_regions ) ) { ?>
				<select name="geot[city_region][]" multiple class="geot-chosen-select-multiple"
				        data-placeholder="<?php _e( 'Choose city region name...', 'geot' ); ?>">
					<?php
					if ( is_array( $city_regions ) ) {
						foreach ( $city_regions as $r ) {
							if ( ! is_array( $opts ) || ! isset( $r['name'] ) ) {
								continue;
							}
							?>
							<option value="<?php echo $r['name']; ?>" <?php
							if ( isset( $opts['city_region'] ) ) {
								selected( true, @in_array( $r['name'], $opts['city_region'] ) );
							}
							?>> <?php echo $r['name']; ?></option>
							<?php
						}
					}
					?>
				</select>
				<?php
			} else { ?>

				<p> Add some regions first.</p>

				<?php
			} ?>
		</td>
		<td colspan="2"></td>
	</tr>
	<tr valign="top">
		<th><label for="gcities"><?php _e( 'Cities:', 'geot' ); ?></label></th>
		<td>

			<input id="gcities" type="text" class="widefat geot_text" name="geot[cities]"
			       value="<?php echo ! empty( $opts['cities'] ) ? $opts['cities'] : ''; ?>"
			       placeholder="<?php _e( 'Type cities (comma separated):', 'geot' ); ?>"/>

		</td>
		<td colspan="2"></td>
	</tr>
	<tr valign="top">
		<th><label for="geot_position"><?php _e( 'State Regions:', 'geot' ); ?></label></th>
		<td>
			<?php if ( is_array( $state_regions ) ) : ?>
				<select name="geot[state_region][]" multiple class="geot-chosen-select-multiple" data-placeholder="<?php _e( 'Choose state region name...', 'geot' ); ?>">
					<?php foreach ( $state_regions as $r ) : ?>
						<?php
							if ( ! is_array( $opts ) || ! isset( $r['name'] ) ) {
								continue;
							}
						?>
						<option value="<?php echo $r['name']; ?>" <?php echo isset( $opts['state_region'] ) ? selected( true, @in_array( $r['name'], $opts['state_region'] ), true ) : ''; ?>>
							<?php echo $r['name']; ?>
						</option>
					<?php endforeach; ?>
				</select>
			
			<?php else : ?>
				<p><?php _e('Add some regions first.','geot'); ?></p>
			<?php endif; ?>
		</td>
		<td colspan="2"></td>
	</tr>
	<tr valign="top">
		<th><label for="gstates"><?php _e( 'States:', 'geot' ); ?></label></th>
		<td>

			<input type="text" id="gstates" class="widefat geot_text" name="geot[states]"
			       value="<?php echo ! empty( $opts['states'] ) ? $opts['states'] : ''; ?>"
			       placeholder="<?php _e( 'Type states (comma separated):', 'geot' ); ?>"/>

		</td>
		<td colspan="2"></td>
	</tr>
	<tr valign="top">
		<th><label for="geot_position"><?php _e( 'Zip Regions:', 'geot' ); ?></label></th>
		<td>
			<?php if ( is_array( $zip_regions ) ) : ?>
				<select name="geot[zip_region][]" multiple class="geot-chosen-select-multiple" data-placeholder="<?php _e( 'Choose zip region name...', 'geot' ); ?>">
					<?php foreach ( $zip_regions as $r ) : ?>
						<?php
							if ( ! is_array( $opts ) || ! isset( $r['name'] ) ) {
								continue;
							}
						?>
						<option value="<?php echo $r['name']; ?>" <?php echo isset( $opts['zip_region'] ) ? selected( true, @in_array( $r['name'], $opts['zip_region'] ), true ) : ''; ?>>
							<?php echo $r['name']; ?>
						</option>
					<?php endforeach; ?>
				</select>
			
			<?php else : ?>
				<p><?php _e('Add some regions first.','geot'); ?></p>
			<?php endif; ?>
		</td>
		<td colspan="2"></td>
	</tr>
	<tr valign="top">
		<th><label for="gstates"><?php _e( 'Zipcodes:', 'geot' ); ?></label></th>
		<td>

			<input type="text" id="gzipcodes" class="widefat geot_text" name="geot[zipcodes]"
			       value="<?php echo ! empty( $opts['zipcodes'] ) ? $opts['zipcodes'] : ''; ?>"
			       placeholder="<?php _e( 'Type Zipcodes (comma separated):', 'geot' ); ?>"/>

		</td>
		<td colspan="2"></td>
	</tr>
	<tr valign="top">
		<th><label for="gstates"><?php _e( 'Given Radius:', 'geot' ); ?></label></th>
		<td>

			<input type="text" id="radius_km" class="geot_text" name="geot[radius_km]"
			       value="<?php echo ! empty( $opts['radius_km'] ) ? $opts['radius_km'] : ''; ?>"
			       placeholder="<?php _e( '100', 'geot' ); ?>"/> km within
			<input type="text" id="radius_lat" class="geot_text" name="geot[radius_lat]"
			       value="<?php echo ! empty( $opts['radius_lat'] ) ? $opts['radius_lat'] : ''; ?>"
			       placeholder="<?php _e( 'Enter latitude', 'geot' ); ?>"/>
			<input type="text" id="radius_lng" class="geot_text" name="geot[radius_lng]"
			       value="<?php echo ! empty( $opts['radius_lng'] ) ? $opts['radius_lng'] : ''; ?>"
			       placeholder="<?php _e( 'Enter longitude', 'geot' ); ?>"/>

		</td>
		<td colspan="2"></td>
	</tr>
	<tr valign="top">
		<th><label for="geot_position"><?php _e( 'Remove post from loop:', 'geot' ); ?></label></th>
		<td>

			<input type="checkbox" class="geot_remove_post" name="geot[geot_remove_post]"
			       value="1" <?php checked( $opts['geot_remove_post'], '1', true ); ?>> <?php _e( 'If checked post will be removed from loop otherwise show message below', 'geot' ); ?>
			<br/>

		</td>
		<td colspan="2"></td>
	</tr>
	<tr valign="top">
		<th><label for="geot_position"><?php _e( 'Show if user is not allowed to see content:', 'geot' ); ?></label>
		</th>
		<td>
			<textarea class="geot_text" style="width:100%;height: 50px;" name="geot[forbidden_text]"
			          data-placeholder="<?php _e( 'Type the text that user will see if not allowed to view content', 'geot' ); ?>"><?php echo $opts['forbidden_text']; ?></textarea>

		</td>
		<td colspan="2"></td>
	</tr>
</table>
<?php wp_nonce_field( 'geot_options', 'geot_options_nonce' ); ?>
