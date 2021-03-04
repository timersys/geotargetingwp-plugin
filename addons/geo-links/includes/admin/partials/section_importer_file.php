<div class="geol_import">
	<div class="header">
		<div class="title"><h1><?php esc_html_e( 'Import', 'geot' ); ?></h1></div>
	</div>

	<div class="body">

		<h2><?php esc_html_e( 'Import', 'geot' ); ?></h2>
		<p><?php esc_html_e( 'This tool allows you to import (or merge) Geolinks data to your store from a CSV or TXT file.', 'geot' ); ?></p>

		<div class="panel">
			<form action="" method="POST" enctype="multipart/form-data">
				<div class="form-body">
					<table class="form-table">
						<tbody>
							<tr>
								<th>
									<?php esc_html_e( 'Choose a CSV file from your computer:', 'geot' ); ?>
								</th>
								<td>
									<input type="file" name="geol" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" />
								</td>
							</tr>
							<tr>
								<th>
									<?php esc_html_e( 'Ignore first row:', 'geot' ); ?>
								</th>
								<td>
									<input type="checkbox" name="geol[ignore]" value="yes" />
									<?php esc_html_e( 'Checked if the first row are the headers.', 'geot' ); ?>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
				<div class="form-footer">
					<?php wp_nonce_field( 'geol_wpnonce', 'wpnonce' ); ?>
					<input type="hidden" name="geol[section]" value="import" />
					<input type="submit" class="button button-primary" value="<?php esc_html_e( 'Import CSV', 'geot' ); ?>" />
				</div>
			</form>
		</div>
		<a class="return_link" href="<?php echo esc_url( add_query_arg( [ 'page' => 'geol-exporter' ] ) ); ?>"><span class="dashicons dashicons-arrow-left-alt2"></span>&nbsp;<?php esc_html_e( 'Export Geolinks', 'geot' ); ?></a>
	</div>
</div>