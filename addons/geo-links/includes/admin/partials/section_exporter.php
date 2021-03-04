<div class="geol_import">
	<div class="header">
		<div class="title"><h1><?php esc_html_e( 'Export', 'geot' ); ?></h1></div>
	</div>

	<?php if( ! empty( $errors ) ) : ?>
		<div class="notice notice-error"><?php echo $errors; ?></div>
	<?php endif; ?>

	<div class="body">
		
		<h2><?php esc_html_e( 'Export', 'geot' ); ?></h2>
		<p><?php esc_html_e( 'This tool allows you to generate and download a CSV file containing a list of all GeoLinks.', 'geot' ); ?></p>

		<div class="panel">
			<form action="" method="POST">
				<div class="form-body">
					<table class="form-table">
						<tbody>
							<tr>
								<th>
									<?php esc_html_e( 'Which Geolink should be exported?', 'geot' ); ?>
								</th>
								<td>
									<select multiple="multiple" class="geot-chosen-select-multiple" name="geol[posts][]" data-placeholder="<?php esc_html_e( 'Type the Geolinks', 'geot' ); ?>">
										<?php foreach( $geol_list as $geol ) : ?>
											<option value="<?php echo $geol->ID; ?>"><?php echo $geol->post_title; ?></option>
										<?php endforeach; ?>
									</select>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
				<div class="form-footer">
					<?php wp_nonce_field( 'geol_wpnonce', 'wpnonce' ); ?>
					<input type="hidden" name="geol[section]" value="export" />
					<input type="submit" class="button button-primary" value="<?php esc_html_e( 'Export CSV', 'geot' ); ?>" />
				</div>
			</form>
		</div>
		<a class="return_link" href="<?php echo esc_url( add_query_arg( [ 'page' => 'geol-importer' ] ) ); ?>"><span class="dashicons dashicons-arrow-left-alt2"></span>&nbsp;<?php esc_html_e( 'Import Geolinks', 'geot' ); ?></a>
	</div>
</div>