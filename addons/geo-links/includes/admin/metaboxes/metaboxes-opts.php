<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<table class="form-table">

	<?php do_action( 'geol/metaboxes/before_source', $opts ); ?>

	<tr valign="top">
		<th><label for="source_slug"><?php _e( 'Link Slug', 'geot' ); ?></label></th>
		<td id="source">
			<input type="text" id="source_slug" class="widefat" name="geol[source_slug]"
			       value="<?php echo isset( $opts['source_slug'] ) ? esc_attr( $opts['source_slug'] ) : ''; ?>"/>
			<span id="source_msg"></span>
			<small>* <?php _e( 'Minimum length accepted: 3', 'geot' ); ?></small>
			<p class="help"><strong>URL:</strong> <?php echo site_url( $settings['goto_page'] ); ?>/<span><?php echo $opts['source_slug']; ?></span>
				<br><strong>SHORTCODE:</strong> [geo-link
				slug="<span><?php echo $opts['source_slug']; ?></span>" nofollow="yes" noreferrer="no"]...[/geo-link]
			</p>
		</td>
	</tr>

	<tr valign="top">
		<th><label for="status_code"><?php _e( 'Redirection code', 'geot' ); ?></label></th>
		<td id="code">
			<input type="number" id="status_code" class="widefat" name="geol[status_code]"
			       value="<?php echo isset( $opts['status_code'] ) ? esc_attr( $opts['status_code'] ) : ''; ?>"/>
			<p class="help"><?php _e( 'Add redirection code. Default to 302', 'geot' ); ?></p>
		</td>
	</tr>

	<tr valign="top">
		<th><label for="dest_default"><?php _e( 'Default URL', 'geot' ); ?></label></th>
		<td>
			<input type="text" class="widefat" name="geol[dest_default]"
			       value="<?php echo isset( $opts['dest_default'] ) ? esc_attr( $opts['dest_default'] ) : ''; ?>"
			       placeholder="<?php _e( 'https://:', 'geot' ); ?>"/>
			<p class="help-text"><?php _e( 'Default url will be used when none of the rules below match the user', 'geot' ); ?></p>
		</td>
	</tr>

	<?php do_action( 'geol/metaboxes/after_source', $opts ); ?>

</table>

<?php
// nonce in last metabox
wp_nonce_field( 'geol_options', 'geol_options_nonce' );
?>