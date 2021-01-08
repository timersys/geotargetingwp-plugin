<div class="geol_import">
	<div class="header">
		<div class="title"><h1><?php esc_html_e( 'Import', 'geol' ); ?></h1></div>
	</div>

	<div class="body">

		<h2><?php esc_html_e( 'Import', 'geol' ); ?></h2>
		<p><?php esc_html_e( 'This tool allows you to import (or merge) Geolinks data to your store from a CSV or TXT file.', 'geol' ); ?></p>

		<div class="panel">
			<form action="" method="POST" enctype="multipart/form-data">
				<div class="form-body">
					<table class="form-table">
						<tbody>
							<tr>
								<th>
									<?php esc_html_e( 'Choose a CSV file from your computer:', 'geol' ); ?>
								</th>
								<td>
									<input type="file" name="geol" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" />
								</td>
							</tr>
						</tbody>
					</table>
				</div>
				<div class="form-footer">
					<?php wp_nonce_field( 'geol_wpnonce', 'wpnonce' ); ?>
					<input type="hidden" name="geol[section]" value="import" />
					<input type="submit" class="button button-primary" value="<?php esc_html_e( 'Import CSV', 'geol' ); ?>" />
				</div>
			</form>
		</div>
	</div>
</div>