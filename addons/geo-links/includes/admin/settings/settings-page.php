<tr valign="top" class="geot-settings-title">
	<th colspan="3"><h3><?php _e( 'Geo Links : ', 'geot' );
			echo GEOTWP_L_VERSION; ?></h3></th>
</tr>
<tr valign="top" class="">
	<th><label for="page_goto"><?php _e( 'Redirection slug', 'geot' ); ?></label></th>
	<td colspan="3">
		<input type="text" id="goto_page" name="geol_settings[goto_page]"
		       value="<?php echo $opts['goto_page']; ?>"/>
		<p class="help"><?php printf( __( 'The slug that will proceed your geolinks: %s', 'geot' ), site_url( $opts['goto_page'] ) . '/{{ geo-link }}' ); ?></p>
	</td>
</tr>
<tr valign="top" class="">
	<th><label for="opt_stats"><?php _e( 'Enable Stats', 'geot' ); ?></label></th>
	<td colspan="3">
		<input type="checkbox" name="geol_settings[opt_stats]" value="1" <?php checked( $opts['opt_stats'], '1' ) ?> />
	</td>
</tr>