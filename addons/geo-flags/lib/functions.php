<?php
/**
* Grab geof settings
* @return mixed|void
*/
function geof_settings(){
	return apply_filters('geot_pro/settings_page/opts', get_option( 'geot_pro_settings' ) );
}