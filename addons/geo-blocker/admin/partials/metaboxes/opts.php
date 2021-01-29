<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>

<table class="form-table">

	<?php do_action( 'geobl/metaboxes/before_options', $opts ); ?>

	<tr valign="top">
		<th><label for="geobl_trigger"><?php _e( 'Template', 'geot' ); ?></label></th>
		<td>
			<?php $located = GeotWP_Bl_Helper::get_template_from_theme( $post->ID ); ?>

			<?php if ( $located ) : ?>
				<p class="help"><?php printf( __( 'Currently you have a template in <b>%s</b>.', 'geot' ), $located ); ?></p>
				<p class="help">
					<?php printf( __( '<a href="%s" target="_blank">View your template</a>', 'geot' ), wp_nonce_url( admin_url( 'admin-ajax.php?action=geo_template&id=' . $post->ID ), 'nonce-template', 'wp-nonce' ) ); ?>
				</p>
			<?php else : ?>
				<p class="help"><?php printf( __( 'If you want to build your own block template, you need to place a file called <b>%s</b> in your current theme for overriding this template or a global file called <b>%s</b> to override all templates.', 'geot' ), 'geobl/geobl-template-' . $post->ID . '.php', 'geobl/geobl-template.php' ); ?></p>
				<p class="help">
					<?php printf( __( '<a href="%s" target="_blank">View the default template</a>', 'geot' ), wp_nonce_url( admin_url( 'admin-ajax.php?action=geo_template&id=' . $post->ID ), 'nonce-template', 'wp-nonce' ) ) ?>
				</p>
			<?php endif; ?>

		</td>
	</tr>

	<tr valign="top">
		<th><label for="geobl_trigger"><?php _e( 'Message', 'geot' ); ?></label></th>
		<td>
			<!--textarea class="widefat" name="geobl[block_message]"><?php //echo esc_attr($opts['block_message']); ?></textarea-->

			<?php
			$editor_id = 'block_message';
			$settings  = [ 'textarea_name' => 'geobl[block_message]', 'textarea_rows' => 5 ];
			//$content = esc_attr($opts['block_message']);
			$content = $opts['block_message'];

			wp_editor( $content, $editor_id, $settings );
			?>
			<p class="help"><?php _e( 'Display a message to users being blocked.', 'geot' ); ?></p>
		</td>
	</tr>
	<tr valign="top">
		<th><label for="geobl_trigger"><?php _e( 'Exclude Search Engines ?', 'geot' ); ?></label></th>
		<td>
			<select id="exclude_se" name="geobl[exclude_se]" class="widefat">
				<option value="0" <?php selected( $opts['exclude_se'], '0' ); ?> > <?php _e( 'No', 'geot' ); ?></option>
				<option value="1" <?php selected( $opts['exclude_se'], '1' ); ?> > <?php _e( 'Yes', 'geot' ); ?></option>
			</select>
			<p class="help"><?php _e( 'Exclude bots and crawlers from being blocked', 'geot' ); ?></p>
		</td>
	</tr>

	<tr valign="top">
		<th><label for="geobl_trigger"><?php _e( 'IP Whitelist', 'geot' ); ?></label></th>
		<td>
			<textarea class="widefat" name="geobl[whitelist]"><?php echo esc_attr( $opts['whitelist'] ); ?></textarea>
			<p class="help"><?php _e( 'Exclude the following IPs from being blocked. Enter one per line', 'geot' ); ?></p>
		</td>
	</tr>
	<?php do_action( 'geobl/metaboxes/after_options', $opts ); ?>
</table>
<?php wp_nonce_field( 'geobl_options', 'geobl_options_nonce' ); ?>
