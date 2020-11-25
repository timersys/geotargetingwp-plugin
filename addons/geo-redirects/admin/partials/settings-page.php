<tr valign="top" class="geot-settings-title">
	<th colspan="3"><h3><?php _e( 'Geo Redirect : ', 'geot' ); ?></h3></th>
</tr>
<tr valign="top" class="">
	<td colspan="4">
		<?php
		$editor_id = 'block_message';
		$settings  = [ 'textarea_name' => 'geotr[redirect_message]', 'textarea_rows' => 10 ];
		$content = $opts['redirect_message'];

		wp_editor( stripslashes( html_entity_decode( $content ) ), $editor_id, $settings );
		?>
	</td>
</tr>