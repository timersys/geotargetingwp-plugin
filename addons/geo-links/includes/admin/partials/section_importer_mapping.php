<div class="geol_import">
	<div class="header">
		<div class="title"><h1><?php esc_html_e( 'Import', 'geot' ); ?></h1></div>
	</div>

	<div class="body">

		<h2><?php esc_html_e( 'Map CSV fields to Geolinks', 'geot' ); ?></h2>
		<p><?php esc_html_e( 'Select fields from your CSV file to map against Geolinks fields, or to ignore during import.', 'geot' ); ?></p>

		<div class="panel">
			<form action="" method="POST" enctype="multipart/form-data">
				<div class="form-body">
					<table class="form-table striped">
						<thead>
							<th><?php esc_html_e( 'Column Name', 'geot' );?></th>
							<th><?php esc_html_e( 'Map to field', 'geot' );?></th>
						</thead>
						<tbody>
							<?php foreach( $content_header as $key => $header ) : ?>
							<tr>
								<td>
									<?php echo $header; ?><br />
									<span class="help"><?php printf( esc_html__( 'Sample : %s', 'geot' ), '<code>' . $content_example[ $key ] . '</code>' ); ?></span>
								</td>
								<td>
									<?php $a = 0; ?>
									<select name="geol[fields][<?php echo $key; ?>]">
										<?php foreach( $fields as $ikey => $ivalue ) : ?>
											<option value="<?php echo $ikey; ?>" <?php selected($select_key, $a); ?>><?php echo $ivalue; ?></option>
										<?php $a++; endforeach; ?>
									</select>
								</td>
							</tr>
							<?php $select_key++; endforeach; ?>
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
		<a class="return_link" href="<?php echo esc_url( remove_query_arg( 'step' ) ); ?>"><span class="dashicons dashicons-arrow-left-alt2"></span>&nbsp;<?php esc_html_e( 'Import other file', 'geot' ); ?></a>
	</div>
</div>