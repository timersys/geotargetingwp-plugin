<?php
function geotr_settings() {
	return get_option('geotr_settings', GeotWP_R_Settings::default_message() );
}