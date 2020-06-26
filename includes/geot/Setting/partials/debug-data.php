<?php global $wp_version, $wpdb;

$theme_data     = wp_get_theme();
$theme          = $theme_data->Name . ' ' . $theme_data->Version;
$uploads_dir    = wp_upload_dir();
$muplugins      = get_mu_plugins();
$plugins        = get_plugins();
$active_plugins = get_option( 'active_plugins', [] );
$updates        = get_plugin_updates();
$opts           = geot_settings();
$addons         = geotwp_addons();
$geowp_opts     = geotwp_settings();
$geol_opts      = function_exists('geotWPL_settings') ? geotWPL_settings() : [];
?>

<h2>Geolocation data</h2>

<form method="POST" action="<?php echo plugins_url( '/download-debug-data.php', __FILE__ ); ?>">
<textarea readonly="readonly" onclick="this.focus(); this.select()" id="geot-debug-info" name="geot-debug-content">
##Geolocation data##

<?php echo strip_tags( preg_replace( '/\t+/', '', geot_debug_data() ) );
echo PHP_EOL; ?>
Geot Cookie set: <?php echo isset( $_COOKIE[ 'geot_country' ] ) ? 'true' : 'false';
echo PHP_EOL; ?>

##Ip Resolved##

<?php
	echo '$_SERVER[REMOTE_ADDR]            = ';
	echo isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : 'not resolved'; ?><?php echo PHP_EOL;

	echo '$_SERVER[HTTP_CF_CONNECTING_IP]  = ';
	echo isset( $_SERVER['HTTP_CF_CONNECTING_IP'] ) ? $_SERVER['HTTP_CF_CONNECTING_IP'] : 'not resolved'; ?><?php echo PHP_EOL;

	echo '$_SERVER[X-Real-IP]              = ';
	echo isset( $_SERVER['X-Real-IP'] ) ? $_SERVER['X-Real-IP'] : 'not resolved'; ?><?php echo PHP_EOL;

	echo '$_SERVER[HTTP_X_REAL_IP]         = ';
	echo isset( $_SERVER['HTTP_X_REAL_IP'] ) ? $_SERVER['HTTP_X_REAL_IP'] : 'not resolved'; ?><?php echo PHP_EOL;

	echo '$_SERVER[HTTP_X_SUCURI_CLIENTIP] = ';
	echo isset( $_SERVER['HTTP_X_SUCURI_CLIENTIP'] ) ? $_SERVER['HTTP_X_SUCURI_CLIENTIP'] : 'not resolved'; ?><?php echo PHP_EOL;

	echo '$_SERVER[X-FORWARDED-FOR]        = ';
	echo isset( $_SERVER['X-FORWARDED-FOR'] ) ? $_SERVER['X-FORWARDED-FOR'] : 'not resolved'; ?><?php echo PHP_EOL;

	echo '$_SERVER[True-Client-IP]         = ';
	echo isset( $_SERVER['True-Client-IP'] ) ? $_SERVER['True-Client-IP'] : 'not resolved'; ?><?php echo PHP_EOL;

	echo '$_SERVER[HTTP_X_FORWARDED_FOR]   = ';
	echo isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : 'not resolved';
	?>

	<?php echo PHP_EOL; ?>
Hosting DB #1: <?php
echo getenv( 'HTTP_GEOIP_COUNTRY_CODE' ) ? 'Yes' : 'No'; ?><?php echo PHP_EOL; ?><?php
if ( getenv( 'HTTP_GEOIP_COUNTRY_CODE' ) ) {
	echo "getenv( 'HTTP_GEOIP_CITY' )         :";
	echo getenv( 'HTTP_GEOIP_CITY' ) . PHP_EOL;
	echo "getenv( 'HTTP_GEOIP_POSTAL_CODE' )  :";
	echo getenv( 'HTTP_GEOIP_POSTAL_CODE' ) . PHP_EOL;
	echo "getenv( 'HTTP_GEOIP_COUNTRY_CODE' ) :";
	echo getenv( 'HTTP_GEOIP_COUNTRY_CODE' ) . PHP_EOL;
	echo "getenv( 'HTTP_GEOIP_COUNTRY_NAME' ) :";
	echo getenv( 'HTTP_GEOIP_COUNTRY_NAME' ) . PHP_EOL;
	echo "getenv( 'HTTP_GEOIP_AREA_CODE' )    :";
	echo getenv( 'HTTP_GEOIP_AREA_CODE' ) . PHP_EOL;
	echo "getenv( 'HTTP_GEOIP_REGION' )       :";
	echo getenv( 'HTTP_GEOIP_REGION' ) . PHP_EOL;
	echo "getenv( 'HTTP_GEOIP_LATITUDE' )     :";
	echo getenv( 'HTTP_GEOIP_LATITUDE' ) . PHP_EOL;
	echo "getenv( 'HTTP_GEOIP_LONGITUDE' )    :";
	echo getenv( 'HTTP_GEOIP_LONGITUDE' ) . PHP_EOL;
}
?>
Hosting DB #2: <?php
echo ! empty( $_SERVER['HTTP_GEOIP_CITY_COUNTRY_NAME'] ) ? 'Yes' : 'No'; ?><?php echo PHP_EOL; ?>
<?php
if ( ! empty( $_SERVER['HTTP_GEOIP_CITY_COUNTRY_NAME'] ) ) {
	echo '$_SERVER[ "HTTP_GEOIP_CITY_CONTINENT_CODE" ] :';
	echo $_SERVER['HTTP_GEOIP_CITY_CONTINENT_CODE'] . PHP_EOL;
	echo '$_SERVER[ "HTTP_GEOIP_CITY" ]                :';
	echo $_SERVER['HTTP_GEOIP_CITY'] . PHP_EOL;
	echo '$_SERVER[ "HTTP_GEOIP_POSTAL_CODE" ]         :';
	echo $_SERVER['HTTP_GEOIP_POSTAL_CODE'] . PHP_EOL;
	echo '$_SERVER[ "HTTP_GEOIP_CITY_COUNTRY_CODE" ]   :';
	echo $_SERVER['HTTP_GEOIP_CITY_COUNTRY_CODE'] . PHP_EOL;
	echo '$_SERVER[ "HTTP_GEOIP_CITY_COUNTRY_NAME" ]   :';
	echo $_SERVER['HTTP_GEOIP_CITY_COUNTRY_NAME'] . PHP_EOL;
	echo '$_SERVER[ "HTTP_GEOIP_REGION" ]              :';
	echo $_SERVER['HTTP_GEOIP_REGION'] . PHP_EOL;
	echo '$_SERVER[ "HTTP_GEOIP_LATITUDE" ]            :';
	echo $_SERVER['HTTP_GEOIP_LATITUDE'] . PHP_EOL;
	echo '$_SERVER[ "HTTP_GEOIP_LONGITUDE" ]           :';
	echo $_SERVER['HTTP_GEOIP_LONGITUDE'] . PHP_EOL;
}
?>
Hosting DB #3: <?php
echo ! empty( $_SERVER['GEOIP_COUNTRY_CODE'] ) ? 'Yes' : 'No'; ?><?php echo PHP_EOL; ?>
<?php
if ( ! empty( $_SERVER['GEOIP_COUNTRY_CODE'] ) ) {
	echo '$_SERVER[ "GEOIP_CONTINENT_CODE" ] :';
	echo $_SERVER['GEOIP_CONTINENT_CODE'] . PHP_EOL;
	echo '$_SERVER[ "GEOIP_CITY" ]                :';
	echo $_SERVER['GEOIP_CITY'] . PHP_EOL;
	echo '$_SERVER[ "GEOIP_POSTAL_CODE" ]         :';
	echo $_SERVER['GEOIP_POSTAL_CODE'] . PHP_EOL;
	echo '$_SERVER[ "GEOIP_COUNTRY_CODE" ]   :';
	echo $_SERVER['GEOIP_COUNTRY_CODE'] . PHP_EOL;
	echo '$_SERVER[ "GEOIP_COUNTRY_NAME" ]   :';
	echo $_SERVER['GEOIP_COUNTRY_NAME'] . PHP_EOL;
	echo '$_SERVER[ "GEOIP_REGION" ]              :';
	echo $_SERVER['GEOIP_REGION'] . PHP_EOL;
	echo '$_SERVER[ "GEOIP_LATITUDE" ]            :';
	echo $_SERVER['GEOIP_LATITUDE'] . PHP_EOL;
	echo '$_SERVER[ "GEOIP_LONGITUDE" ]           :';
	echo $_SERVER['GEOIP_LONGITUDE'] . PHP_EOL;
}
?>
WPEngine: <?php echo isset( $opts['wpengine'] ) && $opts['wpengine'] ? 'Yes' : 'No'; ?><?php echo PHP_EOL; ?>
Maxmind Local database: <?php echo isset( $opts['maxmind'] ) && $opts['maxmind'] ? 'Yes' : 'No'; ?><?php echo PHP_EOL; ?>
Ip2location: <?php echo isset( $opts['ip2location'] ) && $opts['ip2location'] ? 'Yes' : 'No'; ?><?php echo PHP_EOL; ?>
Litespeed: <?php echo isset( $opts['litespeed'] ) && $opts['litespeed'] ? 'Yes' : 'No'; ?><?php echo PHP_EOL; ?>
Hosting database: <?php echo isset( $opts['hosting_db'] ) && $opts['hosting_db'] ? 'Yes' : 'No'; ?><?php echo PHP_EOL; ?>


##Settings page##

Cache mode:               <?php echo isset( $opts['cache_mode'] ) && $opts['cache_mode'] ? 'On' : 'Off'; ?><?php echo PHP_EOL; ?>
Ajax mode:                <?php echo isset( $opts['ajax_mode'] ) && $opts['ajax_mode'] == '1' ? 'On' : 'Off'; ?><?php echo PHP_EOL; ?>

##Addons##
Redirects:               <?php echo isset( $addons['geo-redirects'] ) && $addons['geo-redirects'] ? 'On' : 'Off'; ?><?php echo PHP_EOL; ?>
Links:                   <?php echo isset( $addons['geo-links'] ) && $addons['geo-links'] ? 'On' : 'Off'; ?><?php echo PHP_EOL; ?>
Blocker:                 <?php echo isset( $addons['geo-blocker'] ) && $addons['geo-blocker'] ? 'On' : 'Off'; ?><?php echo PHP_EOL; ?>
Flags:                   <?php echo isset( $addons['geo-flags'] ) && $addons['geo-flags'] ? 'On' : 'Off'; ?><?php echo PHP_EOL; ?>

##Addons Settings##
Disable menus: 	         <?php echo isset( $geowp_opts['disable_menu_integration'] ) && $geowp_opts['disable_menu_integration'] ? 'On' : 'Off'; ?><?php echo PHP_EOL; ?>
Disable widgets:         <?php echo isset( $geowp_opts['disable_widget_integration'] ) && $geowp_opts['disable_widget_integration'] ? 'On' : 'Off'; ?><?php echo PHP_EOL; ?>
Links Slug:              <?php echo isset( $geol_opts['goto_page'] ) ? $geol_opts['goto_page'] : 'Off'; ?><?php echo PHP_EOL; ?>
Links Stats:             <?php echo !empty( $geol_opts['opt_stats'] )  ? 'On' : 'Off'; ?><?php echo PHP_EOL; ?>


##Basic Info##

Site URL:                 <?php echo site_url() . PHP_EOL; ?>
Home URL:                 <?php echo home_url() . PHP_EOL; ?>
Multisite:                <?php echo ( is_multisite() ? 'Yes' : 'No' ) . PHP_EOL; ?>

##WordPress Configuration##

Version:                  <?php echo $wp_version . PHP_EOL; ?>
Language:                 <?php echo get_locale() . PHP_EOL; ?>
Permalink Structure:      <?php echo get_option( 'permalink_structure' ) . PHP_EOL; ?>
Active Theme:             <?php echo $theme . PHP_EOL; ?>
ABSPATH:                  <?php echo ABSPATH . PHP_EOL; ?>
Table Prefix:             Length: <?php echo strlen( $wpdb->prefix ); ?> | Status: <?php echo ( strlen( $wpdb->prefix ) > 16 ? 'ERROR: Too long' : 'Acceptable' ) . PHP_EOL ?>
WP_DEBUG:                 <?php echo ( defined( 'WP_DEBUG' ) ? WP_DEBUG ? 'Enabled' : 'Disabled' : 'Not set' ) . PHP_EOL; ?>
Memory Limit:             <?php echo WP_MEMORY_LIMIT . PHP_EOL; ?>
Registered Post Status:   <?php echo implode( ', ', get_post_stati() ) . PHP_EOL; ?>


##WordPress Uploads/Constants

WP_CONTENT_DIR:           <?php echo ( defined( 'WP_CONTENT_DIR' ) ? WP_CONTENT_DIR ? WP_CONTENT_DIR : 'Disabled' : 'Not set' ) . PHP_EOL; ?>
WP_CONTENT_URL:           <?php echo ( defined( 'WP_CONTENT_URL' ) ? WP_CONTENT_URL ? WP_CONTENT_URL : 'Disabled' : 'Not set' ) . PHP_EOL; ?>
UPLOADS:                  <?php echo ( defined( 'UPLOADS' ) ? UPLOADS ? UPLOADS : 'Disabled' : 'Not set' ) . PHP_EOL; ?>
wp_uploads_dir() path:    <?php echo $uploads_dir['path'] . PHP_EOL; ?>
wp_uploads_dir() url:     <?php echo $uploads_dir['url'] . PHP_EOL; ?>
wp_uploads_dir() basedir: <?php echo $uploads_dir['basedir'] . PHP_EOL; ?>
wp_uploads_dir() baseurl: <?php echo $uploads_dir['baseurl'] . PHP_EOL; ?>

<?php if ( count( $muplugins ) > 0 && ! empty( $muplugins ) ) : ?>
##Must-Use Plugins##
	<?php foreach ( $muplugins as $plugin => $plugin_data ) : ?>
		<?php echo $plugin_data['Name']; ?> : <?php echo $plugin_data['Version']; ?>
	<?php endforeach; ?>
<?php endif; ?>

##WordPress Active Plugins##

<?php
foreach ( $plugins as $plugin_path => $plugin ) {
	if ( ! in_array( $plugin_path, $active_plugins, true ) ) {
		continue;
	}

	$update = ( array_key_exists( $plugin_path, $updates ) ) ? ' (needs update - ' . $updates[ $plugin_path ]->update->new_version . ')' : '';
	echo $plugin['Name'] . ': ' . $plugin['Version'] . $update . PHP_EOL;
}
?>

##WordPress Inactive Plugins##

<?php
foreach ( $plugins as $plugin_path => $plugin ) {
	if ( in_array( $plugin_path, $active_plugins, true ) ) {
		continue;
	}

	$update = ( array_key_exists( $plugin_path, $updates ) ) ? ' (needs update - ' . $updates[ $plugin_path ]->update->new_version . ')' : '';
	echo $plugin['Name'] . ': ' . $plugin['Version'] . $update . PHP_EOL;
}
?><?php
	if ( is_multisite() ) {
		// WordPress Multisite active plugins.
		echo '##Network Active Plugins##' . PHP_EOL;

		$plugins        = wp_get_active_network_plugins();
		$active_plugins = get_site_option( 'active_sitewide_plugins', [] );

		foreach ( $plugins as $plugin_path ) {
			$plugin_base = plugin_basename( $plugin_path );
			if ( ! array_key_exists( $plugin_base, $active_plugins ) ) {
				continue;
			}

			$update = ( array_key_exists( $plugin_path, $updates ) ) ? ' (needs update - ' . $updates[ $plugin_path ]->update->new_version . ')' : '';
			$plugin = get_plugin_data( $plugin_path );
			echo $plugin['Name'] . ': ' . $plugin['Version'] . $update . PHP_EOL;
		}
	}
	?>

##Webserver Configuration##

PHP Version:              <?php echo PHP_VERSION . PHP_EOL; ?>
MySQL Version:            <?php echo $wpdb->db_version() . PHP_EOL; ?>
Webserver Info:           <?php echo $_SERVER['SERVER_SOFTWARE'] . PHP_EOL; ?>
Hostname:                 <?php $host = gethostname(); echo $host . PHP_EOL;?>
IP:                       <?php echo gethostbyname($host) . PHP_EOL;?>

## PHP Configuration##

Memory Limit:             <?php echo ini_get( 'memory_limit' ) . PHP_EOL; ?>
Upload Max Size:          <?php echo ini_get( 'upload_max_filesize' ) . PHP_EOL; ?>
Post Max Size:            <?php echo ini_get( 'post_max_size' ) . PHP_EOL; ?>
Upload Max Filesize:      <?php echo ini_get( 'upload_max_filesize' ) . PHP_EOL; ?>
Time Limit:               <?php echo ini_get( 'max_execution_time' ) . PHP_EOL; ?>
Max Input Vars:           <?php echo ini_get( 'max_input_vars' ) . PHP_EOL; ?>
Display Errors:           <?php echo ( ini_get( 'display_errors' ) ? 'On' : 'N/A' ) . PHP_EOL; ?>

## PHP Extensions##

cURL:                     <?php echo ( function_exists( 'curl_init' ) ? 'Supported' : 'Not Supported' ) . PHP_EOL; ?>
fsockopen:                <?php echo ( function_exists( 'fsockopen' ) ? 'Supported' : 'Not Supported' ) . PHP_EOL; ?>
SOAP Client:              <?php echo ( class_exists( 'SoapClient' ) ? 'Installed' : 'Not Installed' ) . PHP_EOL; ?>
Suhosin:                  <?php echo ( extension_loaded( 'suhosin' ) ? 'Installed' : 'Not Installed' ) . PHP_EOL; ?>

#Session Configuration

Session:                  <?php echo( isset( $_SESSION ) ? 'Enabled' : 'Disabled' ); ?>

<?php if ( isset( $_SESSION ) ) : ?>
Session Name:             <?php echo esc_html( ini_get( 'session.name' ) ) . PHP_EOL; ?>
Cookie Path:              <?php echo esc_html( ini_get( 'session.cookie_path' ) ) . PHP_EOL; ?>
Save Path:                <?php echo esc_html( ini_get( 'session.save_path' ) ) . PHP_EOL; ?>
Use Cookies:              <?php echo ( ini_get( 'session.use_cookies' ) ? 'On' : 'Off' ) . PHP_EOL; ?>
Use Only Cookies:         <?php echo ( ini_get( 'session.use_only_cookies' ) ? 'On' : 'Off' ) . PHP_EOL; ?>
	<?php endif; ?>

</textarea>

	<?php wp_nonce_field( 'field-debug-nonce', 'geot-debug-nonce' ); ?>
	<input type="submit" class="button-primary" name="geot-debug-button" value="Download debug data"/>
</form>