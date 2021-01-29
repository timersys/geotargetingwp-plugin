<div class="geol_import">
	<div class="header">
		<div class="title"><h1><?php esc_html_e( 'Import', 'geot' ); ?></h1></div>
	</div>

	<div class="body">

		<div class="panel">

			<div class="form-body">
				<div class="success-icon">
					<span class="dashicons dashicons-yes-alt"></span>
				</div>
				<p class="success-msg">
					<?php
						printf(
							esc_html__( 'Import complete! %d Geolink(s) updated', 'geot' ),
							$counter
						);
					?>
				</p>
			</div>
			<div class="form-footer">
				<a href="<?php echo admin_url('edit.php?post_type=geol_cpt'); ?>" class="button button-primary"><?php esc_html_e( 'View Geolinks', 'geot' ); ?></a>
			</div>
		</div>
		<a class="return_link" href="<?php echo esc_url( remove_query_arg( 'step' ) ); ?>"><span class="dashicons dashicons-arrow-left-alt2"></span>&nbsp;<?php esc_html_e( 'Import other file', 'geot' ); ?></a>
	</div>
</div>