<?php
/**
 * Settings page template
 * @since  1.0.0
 */


$opts = geot_settings();

if ( empty( $opts['region'] ) ) {
	$opts['region'] = [ [ 'name', 'countries' ] ];
}
if ( empty( $opts['state_region'] ) ) {
	$opts['state_region'] = [ [ 'name', 'states' ] ];
}
if ( empty( $opts['city_region'] ) ) {
	$opts['city_region'] = [ [ 'name', 'cities' ] ];
}
if ( empty( $opts['zip_region'] ) ) {
	$opts['zip_region'] = [ [ 'name', 'zips' ] ];
}

$countries = geot_countries();
?>
<script>
    var geot_countries = <?php echo json_encode( array_map( function ( $a ) {
		return [ 'text' => $a->country, 'value' => $a->iso_code ];
	}, (array) $countries ) );?>;
</script>
<div class="wrap geot-settings">
	<form name="geot-settings" method="post" enctype="multipart/form-data">
		<table class="form-table">

			<?php do_action( 'geot/regions_page/before' ); ?>
			<tr valign="top" class="geot-settings-title">
				<th colspan="3"><h3><?php _e( 'Regions:', 'geot' ); ?></h3></th>
			</tr>

			<?php if ( apply_filters( 'geot/enable_predefined_regions', true ) ) : ?>
				<tr valign="top" class="region-title">
					<th colspan="3"><h3><?php _e( 'Continents:', 'geot' ); ?></h3></th>

				</tr>
				<tr valign="top" class="">
					<th><label for="region"><?php _e( 'Continents:', 'geot' ); ?></label></th>
					<td colspan="3">
						<p class="help"><?php _e( 'We have created some predefined regions in case you need them:', 'geot' ); ?></p>
						<ul class="help">
							<li>- <?php _e( '<b>north-america</b> (North America)', 'geot' ); ?></li>
							<li>- <?php _e( '<b>south-america</b> (South America)', 'geot' ); ?></li>
							<li>- <?php _e( '<b>europe</b> (Europe)', 'geot' ); ?></li>
							<li>- <?php _e( '<b>asia</b> (Asia)', 'geot' ); ?></li>
							<li>- <?php _e( '<b>africa</b> (Africa)', 'geot' ); ?></li>
							<li>- <?php _e( '<b>oceania</b> (Oceania)', 'geot' ); ?></li>
							<li>- <?php _e( '<b>antarctica</b> (Antarctica)', 'geot' ); ?></li>
						</ul>
					</td>
				</tr>

			<?php endif; ?>

			<tr valign="top" class="region-title">
				<th  colspan="3"><h3><?php _e( 'Countries:', 'geot' ); ?></h3></th>
			</tr>
			<tr valign="top" class="">
				<th><label for="region"><?php _e( 'Create new region', 'geot' ); ?></label></th>
				<td colspan="3">
					<?php

					if ( ! empty( $opts['region'] ) ) {
						$i = 0;
						foreach ( $opts['region'] as $region ) {
							$i ++; ?>

							<div class="region-group" data-id="<?php echo $i; ?>">

								<input type="text" class="region-name" placeholder="Enter region name"
								       name="geot_settings[region][<?php echo $i; ?>][name]"
								       value="<?php echo ! empty( $region['name'] ) ? esc_attr( $region['name'] ) : ''; ?>"/>
								<a href="#" class="remove-region" title="<?php _e( 'Remove Region', 'geot' ); ?>">-</a>
								<select name="geot_settings[region][<?php echo $i; ?>][countries][]" multiple
								        class="geot-chosen-select-multiple"
								        data-placeholder="<?php _e( 'Type country name...', 'geot' ); ?>">
									<?php
									foreach ( $countries as $c ) {
										?>
										<option value="<?php echo $c->iso_code ?>" <?php isset( $region['countries'] ) && is_array( $region['countries'] ) ? selected( true, in_array( $c->iso_code, $region['countries'] ) ) : ''; ?>> <?php echo $c->country; ?></option>
										<?php
									}
									?>
								</select>

							</div>
						<?php }
					} ?>
					<a href="#" class="add-region button">Add Region</a>
					<p class="help"><?php _e( 'Add as many countries you need for each region', 'geot' ); ?></p>
				</td>

			</tr>
			<tr valign="top" class="region-title">
				<th colspan="3"><h3><?php _e( 'Cities:', 'geot' ); ?></h3></th>
			</tr>
			<tr valign="top" class="">
				<th><label for="region"><?php _e( 'Create new region', 'geot' ); ?></label></th>
				<td colspan="3">
					<?php

					if ( ! empty( $opts['city_region'] ) ) {
						$j = 0;
						foreach ( $opts['city_region'] as $city_region ) {
							$j ++; ?>

							<div class="city-region-group" data-id="<?php echo $j; ?>">
								<input type="text" class="region-name" placeholder="Enter region name"
								       name="geot_settings[city_region][<?php echo $j; ?>][name]"
								       value="<?php echo ! empty( $city_region['name'] ) ? esc_attr( $city_region['name'] ) : ''; ?>"/>

								<a href="#" class="remove-city-region"
								   title="<?php _e( 'Remove Region', 'geot' ); ?>">-</a>
								<select name="geot_settings[city_region][<?php echo $j; ?>][countries][]"
								        class="country_ajax" data-counter="<?php echo $j; ?>"
								        data-placeholder="<?php _e( 'Type country name...', 'geot' ); ?>">
									<option value=""><?php _e( 'Choose a Country', 'geot' ); ?></option>
									<?php
									foreach ( $countries as $c ) {
										?>
										<option value="<?php echo $c->iso_code ?>" <?php isset( $city_region['countries'] ) && is_array( $city_region['countries'] ) ? selected( true, in_array( $c->iso_code, $city_region['countries'] ) ) : ''; ?>> <?php echo $c->country; ?></option>
										<?php
									}
									?>
								</select>

								<select name="geot_settings[city_region][<?php echo $j; ?>][cities][]" multiple
								        class="cities_container" id="<?php echo 'cities' . $j; ?>"
								        data-placeholder="<?php _e( 'First choose a country', 'geot' ); ?>">
									<?php
									/*

									$country_cities = false;
									if( isset( $city_region['countries'] ) && is_array( $city_region['countries'] ) ) {
										$country_cities = reset($city_region['countries']);
									}
									$cities = json_decode(geot_get_cities($country_cities));
									if( $cities ) {
										foreach ($cities as $option_city) {
											echo '<option value="'.$option_city->city.'" ';
											echo isset($city_region['cities']) && is_array($city_region['cities']) ? selected(true, in_array($option_city->city, $city_region['cities'])) : '';
											echo '>'.$option_city->city.'</option>';
										}
									}*/
									?>
								</select>
								<script>
                                    var select_<?= $j;?> = jQuery('#<?php echo 'cities' . $j;?>').selectize({
                                        plugins: ['remove_button'],
                                        valueField: 'name',
                                        labelField: 'name',
                                        searchField: 'name',
                                        options: <?php echo isset( $city_region['cities'] ) && is_array( $city_region['cities'] ) ? json_encode( array_map( function ( $a ) {
											return [ 'name' => stripslashes($a) ];
										}, $city_region['cities'] ) ) : '""'; ?>,
                                        items: ['<?php echo isset( $city_region['cities'] ) && is_array( $city_region['cities'] ) ? implode( "','", $city_region['cities'] ) : '';?>'],
                                        render: function (item, escape) {
                                            return '<div>' + escape(item.name) + '</div>';
                                        },
                                        create: function(input) {
                                            const inst = select_<?= $j;?>[0].selectize;
                                            inst.addOption({name:input});
                                            inst.addItem(input);
                                            inst.refreshOptions(true);
                                            inst.refreshItems(true);
                                            inst.setTextboxValue('');
                                            return {
                                                value: input,
                                                text: input
                                            }
                                        },
                                        preload: true,
                                        openOnFocus: true,
                                        onFocus: function () {
                                            const inst = select_<?= $j;?>[0].selectize;
                                            if (inst.loaded)
                                                return;
                                            inst.disable();
                                            jQuery.ajax({
                                                url: geot.ajax_url,
                                                type: 'POST',
                                                dataType: 'json',
                                                data: {
                                                    action: 'geot_cities_by_country',
                                                    country: '<?= ! empty( $city_region['countries'] ) ? reset( $city_region['countries'] ) : '';?>'
                                                },
                                                error: function () {

                                                },
                                                success: function (res) {
                                                    inst.loaded = true;
                                                    inst.enable();
                                                    inst.addOption(res);
                                                    inst.refreshOptions(true);
                                                }
                                            });
                                        }
                                    });
								</script>
							</div>
							<hr>
						<?php }
					} ?>
					<a href="#" class="add-city-region button">Add City Region</a>
					<p class="help"><?php _e( 'Add as many cities you need for each region', 'geot' ); ?></p>
				</td>

			</tr>
			<tr valign="top" class="region-title">
				<th colspan="3"><h3><?php _e( 'States:', 'geot' ); ?></h3></th>

			</tr>
			<tr valign="top" class="">
				<th><label for="region"><?php _e( 'Create new region', 'geot' ); ?></label></th>
				<td colspan="3">
	
					<?php if ( ! empty( $opts['state_region'] ) ) : $i = 0; ?>
						<?php foreach ( $opts['state_region'] as $region ) : $i++; ?>

							<div class="state-region-group" data-id="<?php echo $i; ?>">

								<input type="text" class="state-region-name region-name" placeholder="Enter region name" name="geot_settings[state_region][<?php echo $i; ?>][name]" value="<?php echo ! empty( $region['name'] ) ? esc_attr( $region['name'] ) : ''; ?>" />
								<a href="#" class="remove-state-region" title="<?php _e( 'Remove States', 'geot' ); ?>">-</a>
								<input type="text" class="state-region-list" name="geot_settings[state_region][<?php echo $i; ?>][states]" placeholder="<?php _e( 'Type State iso codes separated with commas...', 'geot' ); ?>" value="<?php echo ! empty( $region['states'] ) ? esc_attr( $region['states'] ) : ''; ?>" />


							</div>
						<?php endforeach; ?>
					<?php endif; ?>
				
					<a href="#" class="add-state-region button"><?php _e( 'Add State Regions', 'geot' ); ?></a>
					<p class="help"><?php _e( 'Add as many states you need for each region', 'geot' ); ?></p>
				</td>
			</tr>
			<tr valign="top" class="region-title">
				<th colspan="3"><h3><?php _e( 'Zip Codes:', 'geot' ); ?></h3></th>

			</tr>
			<tr valign="top" class="">
				<th><label for="region"><?php _e( 'Create new region', 'geot' ); ?></label></th>
				<td colspan="3">
	
					<?php if ( ! empty( $opts['zip_region'] ) ) : $i = 0; ?>
						<?php foreach ( $opts['zip_region'] as $region ) : $i++; ?>

							<div class="zip-region-group" data-id="<?php echo $i; ?>">

								<input type="text" class="zip-region-name region-name" placeholder="Enter zip region name" name="geot_settings[zip_region][<?php echo $i; ?>][name]" value="<?php echo ! empty( $region['name'] ) ? esc_attr( $region['name'] ) : ''; ?>" />
								<a href="#" class="remove-zip-region" title="<?php _e( 'Remove Zip', 'geot' ); ?>">-</a>
								<input type="text" class="zip-region-list" name="geot_settings[zip_region][<?php echo $i; ?>][zips]" data-placeholder="<?php _e( 'Type Zip Code separate with commas...', 'geot' ); ?>" value="<?php echo ! empty( $region['zips'] ) ? esc_attr( $region['zips'] ) : ''; ?>" />
								<p class="help"><?php _e('Write the zip codes separate by commas. You can use * to wide mach zips. For example 546* will match all zips codes starting with 546.','geot'); ?></p>

							</div>
						<?php endforeach; ?>
					<?php endif; ?>
				
					<a href="#" class="add-zip-region button"><?php _e( 'Add Zip Regions', 'geot' ); ?></a>
					<p class="help"><?php _e( 'Add as many zips you need for each region', 'geot' ); ?></p>
				</td>
			</tr>

			<?php do_action( 'geot/regions_page/after' ); ?>

			<tr>
				<td><input type="submit" class="button-primary" value="<?php _e( 'Save settings', 'geot' ); ?>"/></td>
				<?php wp_nonce_field( 'geot_save_settings', 'geot_nonce' ); ?>
		</table>
	</form>
</div>
