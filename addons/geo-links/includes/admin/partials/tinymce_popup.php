<form name="form" autocomplete="off">
	<div id="geol_editor" class="shortcode_editor" title="Insert Geol Link to page" style="display:none;height:500px">
		<div style="display: none;"><!--hack for chrome-->
			<input type="text" id="PreventChromeAutocomplete" name="PreventChromeAutocomplete"
			       autocomplete="address-level4"/>
		</div>
		<table class="form-table">
			<tr>
				<td colspan="2">
					<p>
						<?php _e( 'Choose which Geol Link you want to insert. This will generate an href link that you can use with text or images', 'geot' ); ?>
					</p>
				</td>
			</tr>
			<tr>
				<th><label for="geol_what"><?php _e( 'Choose:', 'geot' ); ?></label></th>
				<td>
					<select name="geol-post" id="geol-posts">
						<option value=""><?php _e( 'Choose one', 'geot' ); ?></option>
						<?php
						// The Loop
						if ( $geol_results ) {
							foreach ( $geol_results as $geol ) {

								$geol_options = maybe_unserialize( $geol->geol_options );

								if ( isset( $geol_options['source_slug'] ) && ! empty( $geol_options['source_slug'] ) ) {
									echo '<option value="' . $geol_options['source_slug'] . '">' . $geol->geol_title . '</option>';
								}
							}
						}

						?>
					</select>
				</td>
			</tr>
			<tr>
				<th><label for="geol_nofollow"><?php _e( 'NoFollow?:', 'geot' ); ?></label></th>
				<td>
					<label>
						<input type="radio" name="geol_nofollow" value="yes"/><?php _e( 'Yes', 'geot' ); ?>
					</label>
					<label>
						<input type="radio" name="geol_nofollow" value="no"/><?php _e( 'No', 'geot' ); ?>
					</label>
				</td>
			</tr>
			<tr>
				<th><label for="geol_noreferrer "><?php _e( 'NoReferrer ?:', 'geot' ); ?></label></th>
				<td>
					<label>
						<input type="radio" name="geol_noreferrer " value="yes"/><?php _e( 'Yes', 'geot' ); ?>
					</label>
					<label>
						<input type="radio" name="geol_noreferrer " value="no"/><?php _e( 'No', 'geot' ); ?>
					</label>
				</td>
			</tr>

		</table>
	</div>
</form>
