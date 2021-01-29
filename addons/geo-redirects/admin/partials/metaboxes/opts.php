<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>

<table class="form-table">

	<?php do_action( 'geotr/metaboxes/before_options', $opts ); ?>

	<tr valign="top">
		<th><label for="geotr_trigger"><?php _e( 'Destination URL', 'geot' ); ?></label></th>
		<td>
			<input type="text" class="widefat" name="geotr[url]" min="0"
			       value="<?php echo esc_attr( $opts['url'] ); ?>"/>
			<p class="help"><?php _e( 'Enter redirection url. You can create dynamic urls by using placeholders like :', 'geot' ); ?></p>
			<ul>
				<li>{{country_code}} <?php _e( 'Two letter iso code', 'geot' ); ?></li>
				<li>{{state_code}} <?php _e( 'Two letter state code', 'geot' ); ?></li>
				<li>{{zip}} <?php _e( 'Zip code', 'geot' ); ?></li>
				<li>
					{{requested_uri}} <?php _e( 'Original requested url. Eg: http://geotargetingwp.com/geo-redirects', 'geot' ); ?></li>
				<li>{{requested_path}} <?php _e( 'Original requested path. Eg: geo-redirects', 'geot' ); ?></li>
				<li>{{last_path}} <?php _e( 'Last path in url. Eg: http://geotargetingwp.com/geo-redirects/redirect1 => redirect1', 'geot' ); ?></li>
			</ul>
		</td>
	</tr>
	<?php
	if ( function_exists( 'icl_object_id' ) ) {
	?>
	<tr valign="top">
		<th><label for="geotr_trigger"><?php _e( 'WPML/Polylang Language code', 'geot' ); ?></label></th>
		<td>
			<input type="text" name="geotr[wpml]" value="<?php echo esc_attr( $opts['wpml'] ); ?>"
			       placeholder=""/>
			<p class="help"><?php _e( 'Enter 2 letter language code to automatically translate destination slug to that language if available.', 'geot' ); ?></p>
		</td>
	</tr><?php
	}
	?>
	<tr valign="top">
		<th><label for="geotr_trigger"><?php _e( 'Exclude child pages from redirect ?', 'geot' ); ?></label></th>
		<td>
			<select id="exclude_child" name="geotr[exclude_child]" class="widefat">
				<option value="1" <?php selected( $opts['exclude_child'], '1' ); ?> > <?php _e( 'Yes', 'geot' ); ?></option>
				<option value="0" <?php selected( $opts['exclude_child'], '0' ); ?> > <?php _e( 'No', 'geot' ); ?></option>
			</select>
			<p class="help"><?php _e( 'If your final destination it\'s /es/ for spanish users, this option will prevent /es/page-slug to be redirected', 'geot' ); ?></p>
		</td>
	</tr>
	<tr valign="top">
		<th><label for="geotr_trigger"><?php _e( 'One time redirect ?', 'geot' ); ?></label></th>
		<td>
			<select id="one_time_redirect" name="geotr[one_time_redirect]" class="widefat">
				<option value="0" <?php selected( $opts['one_time_redirect'], '0' ); ?> > <?php _e( 'No', 'geot' ); ?></option>
				<option value="1" <?php selected( $opts['one_time_redirect'], '1' ); ?> > <?php _e( 'Yes', 'geot' ); ?></option>
				<option value="2" <?php selected( $opts['one_time_redirect'], '2' ); ?> > <?php _e( 'Yes, one per user session', 'geot' ); ?></option>
			</select>
			<p class="help"><?php _e( 'Select if user will be redirected every time, once per browser session or only once in total', 'geot' ); ?></p>
		</td>
	</tr>
	<tr valign="top">
		<th><label for="geotr_trigger"><?php _e( 'Remove two letter codes from this redirect ?', 'geot' ); ?></label></th>
		<td>
			<select id="exclude_se" name="geotr[remove_iso]" class="widefat">
				<option value="0" <?php selected( $opts['remove_iso'], '0' ); ?> > <?php _e( 'No', 'geot' ); ?></option>
				<option value="1" <?php selected( $opts['remove_iso'], '1' ); ?> > <?php _e( 'Yes', 'geot' ); ?></option>
			</select>
			<p class="help"><?php _e( 'Enable if you are using two letter codes in your redirects to remove them from request path', 'geot' ); ?></p>
		</td>
	</tr>
	<tr valign="top">
		<th><label for="geotr_trigger"><?php _e( 'Exclude Search Engines ?', 'geot' ); ?></label></th>
		<td>
			<select id="exclude_se" name="geotr[exclude_se]" class="widefat">
				<option value="0" <?php selected( $opts['exclude_se'], '0' ); ?> > <?php _e( 'No', 'geot' ); ?></option>
				<option value="1" <?php selected( $opts['exclude_se'], '1' ); ?> > <?php _e( 'Yes', 'geot' ); ?></option>
			</select>
			<p class="help"><?php _e( 'Exclude bots and crawlers from being redirected', 'geot' ); ?></p>
		</td>
	</tr>
	<tr valign="top">
		<th><label for="pass_query"><?php _e( 'Pass query string ?', 'geot' ); ?></label></th>
		<td>
			<select id="pass_query" name="geotr[pass_query_string]" class="widefat">
				<option value="1" <?php selected( $opts['pass_query_string'], '1' ); ?> > <?php _e( 'Yes', 'geot' ); ?></option>
				<option value="0" <?php selected( $opts['pass_query_string'], '0' ); ?> > <?php _e( 'No', 'geot' ); ?></option>
			</select>
			<p class="help"><?php _e( 'Original url may contain a query string such as ?utm_source=adwords. By default they are passed to new url', 'geot' ); ?></p>
		</td>
	</tr>
	<tr valign="top">
		<th><label for="geotr_trigger"><?php _e( 'Redirection code?', 'geot' ); ?></label></th>
		<td>
			<input type="text" name="geotr[status]" value="<?php echo esc_attr( $opts['status'] ); ?>"
			       placeholder="302"/>
			<p class="help"><?php _e( 'Add redirection code. Default to 302', 'geot' ); ?></p>
		</td>
	</tr>
	<tr valign="top">
		<th><label for="geotr_trigger"><?php _e( 'IP Whitelist', 'geot' ); ?></label></th>
		<td>
			<textarea class="widefat" name="geotr[whitelist]"><?php echo esc_attr( $opts['whitelist'] ); ?></textarea>
			<p class="help"><?php _e( 'Exclude the following IPs from being redirected. Enter one per line', 'geot' ); ?></p>
		</td>
	</tr>
	<?php do_action( 'geotr/metaboxes/after_options', $opts ); ?>
</table>
<?php wp_nonce_field( 'geotr_options', 'geotr_options_nonce' ); ?>
