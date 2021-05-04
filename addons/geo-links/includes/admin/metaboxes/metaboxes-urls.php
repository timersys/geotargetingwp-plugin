<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<?php if ( $opts['dest'] ) : ?>

	<?php foreach ( $opts['dest'] as $key => $data ) : ?>

		<table class="form-table geol_repeater">
			<tr id="<?php echo $key; ?>">
				<td>
					<div class="geol_border">
						<table class="form-table">

							<?php do_action( 'geol/metaboxes/before_repeater', $opts ); ?>
							<tr valign="top">
								<th><label for="geol_dest"><?php _e( 'Label', 'geot' ); ?></label></th>
								<td>
									<input type="text" class="widefat" name="geol[dest][<?php echo $key; ?>][label]"
									       value="<?php echo isset( $data['label'] ) ? esc_attr( $data['label'] ) : ''; ?>"
									       placeholder=""/>
									<p class="help-text"><?php _e( 'Enter a friendly name to identify your links', 'geot' ); ?></p>
								</td>
							</tr>

							<tr valign="top">
								<th><label for="geol_dest"><?php _e( 'Destination URL', 'geot' ); ?></label></th>
								<td>
									<input type="text" class="widefat" name="geol[dest][<?php echo $key; ?>][url]"
									       value="<?php echo isset( $data['url'] ) ? esc_attr( $data['url'] ) : ''; ?>"
									       placeholder="<?php _e( 'https://:', 'geot' ); ?>"/>
									<p class="help-text"><?php _e( 'Where the user is going to be redirected if rules below match', 'geot' ); ?></p>
								</td>
							</tr>

							<tr valign="top">
								<th colspan="2"><?php _e( 'Geo Options', 'geot' ); ?></label></th>
							</tr>

							<tr valign="top">
								<th><label>&emsp;&emsp;&emsp;&emsp;<?php _e( 'Countries', 'geot' ); ?></label></th>
								<td>
									<select name="geol[dest][<?php echo $key; ?>][countries][]"
									        class="geot-chosen-select-multiple geol_countries"
									        placeholder="<?php _e( 'Choose one or more countries', 'geot' ); ?>"
									        multiple="multiple">
										<?php foreach ( $countries as $c ) : ?>
											<option value="<?php echo $c->iso_code; ?>" <?php isset( $data['countries'] ) && is_array( $data['countries'] ) ? selected( true, in_array( $c->iso_code, $data['countries'] ) ) : ''; ?>> <?php echo $c->country; ?></option>
										<?php endforeach; ?>
									</select>
									<p class="help-text"
									   style="margin-top: -20px;"><?php _e( 'Choose one or more countries', 'geot' ); ?></p>
								</td>
							</tr>
							<tr>
								<th><label>&emsp;&emsp;&emsp;&emsp;<?php _e( 'Regions', 'geot' ); ?></label></th>
								<td>
									<select name="geol[dest][<?php echo $key; ?>][regions][]"
									        class="geot-chosen-select-multiple geol_regions"
									        placeholder="<?php _e( 'Choose one or more regions', 'geot' ); ?>"
									        multiple="multiple">
										<?php if ( isset( $regions ) && ! empty( $regions ) ) : ?>
											<?php foreach ( $regions as $region ) : ?>

												<option value="<?php echo $region['name']; ?>" <?php isset( $data['regions'] ) && is_array( $data['regions'] ) ? selected( true, in_array( $region['name'], $data['regions'] ) ) : ''; ?>> <?php echo $region['name']; ?></option>
											<?php endforeach; ?>
										<?php endif; ?>
									</select>
									<p class="help-text"
									   style="margin-top: -20px;"><?php echo sprintf( __( 'Choose one or more regions. Edit regions in <a href="%s">here</a>', 'geot' ), admin_url( 'admin.php?page=geot-settings' ) ); ?></p>
								</td>
							</tr>
							<tr>
								<th><label>&emsp;&emsp;&emsp;&emsp;<?php _e( 'Cities', 'geot' ); ?></label></th>
								<td>
									<input type="text" class="widefat"
									       name="geol[dest][<?php echo $key; ?>][cities]"
									       value="<?php echo isset( $data['cities'] ) ? esc_attr( $data['cities'] ) : ''; ?>"
									       placeholder="<?php _e( 'Cities / Regions', 'geot' ); ?>"/>
									<p class="help-text"><?php _e( 'Type city names or city regions, comma separated', 'geot' ); ?></p>
								</td>
							</tr>
							<tr>
								<th><label>&emsp;&emsp;&emsp;&emsp;<?php _e( 'States', 'geot' ); ?></label></th>
								<td>
									<input type="text" class="widefat"
									       name="geol[dest][<?php echo $key; ?>][states]"
									       value="<?php echo isset( $data['states'] ) ? esc_attr( $data['states'] ) : ''; ?>"
									       placeholder="<?php _e( 'States', 'geot' ); ?>"/>
									<p class="help-text"><?php _e( 'Type state iso codes or state regions, comma separated', 'geot' ); ?></p>
								</td>

							</tr>
							<tr>
								<th><label>&emsp;&emsp;&emsp;&emsp;<?php _e( 'ZipCodes', 'geot' ); ?></label></th>
								<td>
									<input type="text" class="widefat"
									       name="geol[dest][<?php echo $key; ?>][zipcodes]"
									       value="<?php echo isset( $data['zipcodes'] ) ? esc_attr( $data['zipcodes'] ) : ''; ?>"
									       placeholder="<?php _e( 'Zipcodes', 'geot' ); ?>"/>
									<p class="help-text"><?php _e( 'Type zip codes or zip regions separated by commas.', 'geot' ); ?></p>
								</td>

							</tr>

							<tr>
								<th><label>&emsp;&emsp;&emsp;&emsp;<?php _e( 'Radius', 'geot' ); ?></label></th>
								<td>
									<input type="number" class="widefat" step="1"
									       name="geol[dest][<?php echo $key; ?>][radius_km]"
									       value="<?php echo isset( $data['radius_km'] ) ? esc_attr( $data['radius_km'] ) : ''; ?>"
									       placeholder="<?php _e( '100', 'geot' ); ?>" style="width: 60px;"/> <?php echo \GeotCore\radius_unit()?> within
									<br />
									<input type="number" class="widefat" step="0.000001"
									       name="geol[dest][<?php echo $key; ?>][radius_lat]"
									       value="<?php echo isset( $data['radius_lat'] ) ? esc_attr( $data['radius_lat'] ) : ''; ?>"
									       placeholder="<?php _e( 'Latitude', 'geot' ); ?>" style="width: 160px;" />
									<input type="number" class="widefat" step="0.000001"
									       name="geol[dest][<?php echo $key; ?>][radius_lng]"
									       value="<?php echo isset( $data['radius_lng'] ) ? esc_attr( $data['radius_lng'] ) : ''; ?>"
									       placeholder="<?php _e( 'Longitude', 'geot' ); ?>" style="width: 160px;" />
								</td>

							</tr>

							<tr valign="top">
								<th><label for="geol_trigger"><?php _e( 'Device', 'geot' ); ?></label></th>
								<td>
									<select class="widefat selectized geol_device"
									        name="geol[dest][<?php echo $key; ?>][device]"
									        value="<?php echo esc_attr( $data['device'] ); ?>"
									        placeholder="<?php _e( 'Enter a device', 'geot' ); ?>">
										<option value="all"><?php _e( 'All Devices', 'geot' ); ?></option>
										<?php foreach ( $devices as $key_dev => $name_dev ) : ?>
											<option value="<?php echo $key_dev; ?>" <?php selected( $data['device'], $key_dev ) ?>><?php echo $name_dev; ?></option>
										<?php endforeach; ?>
									</select>
									<p class="help-text"><?php _e( 'Only redirect if using this device', 'geolinks' ); ?></p>
								</td>
							</tr>

							<tr valign="top">
								<th><label for="geol_trigger"><?php _e( 'Referrer URL', 'geot' ); ?></label></th>
								<td>
									<input type="text" class="widefat" name="geol[dest][<?php echo $key; ?>][ref]"
									       value="<?php echo esc_attr( $data['ref'] ); ?>"/>
									<p class="help-text"><?php _e( 'Only redirect if user coming from this url', 'geolinks' ); ?></p>
								</td>
							</tr>

							<?php do_action( 'geol/metaboxes/after_repeater', $opts ); ?>
						</table>
					</div>
				</td>
				<td>
					<div class="box_plus">
						<a href="" class="button geol_plus" title="<?php _e( 'Add', 'geot' ); ?>">
							<?php _e( '+ ADD', 'geot' ); ?>
						</a>
					</div>
					<div class="box_less">
						<a href="" class="button geol_less" title="<?php _e( 'Remove', 'geot' ); ?>">
							<?php _e( '- REMOVE', 'geot' ); ?>
						</a>
					</div>
				</td>

			</tr>
		</table>

	<?php endforeach; ?>
<?php endif; ?>