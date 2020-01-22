(function ($) {
    'use strict';

    const GeotWP_Location  = {
        /**
         * Start function
         */
        init: function () {
            $(document).ready( GeotWP_Location.ready );
        },
        /**
         * When dom it's ready
         */
        ready: function () {

        	GeotWP_Location.maybe_overlay();

			if (navigator.geolocation) {
				navigator.geolocation.getCurrentPosition(
					GeotWP_Location.successPosition,
					GeotWP_Location.errorPosition
				);
			} else { 
				console.log(geotloc.msg_fail);
			}
        },
        successPosition: function() {
        	GeotWP_Location.saveInfo('geotLocation', 'yes');

        	$('div.geotloc_overlay').fadeOut('fast');
        },
        errorPosition: function(error) {
			/*switch(error.code) {
				case error.PERMISSION_DENIED:
					x.innerHTML = "User denied the request for Geolocation."
				break;
				case error.POSITION_UNAVAILABLE:
					x.innerHTML = "Location information is unavailable."
				break;
				case error.TIMEOUT:
					x.innerHTML = "The request to get user location timed out."
				break;
				case error.UNKNOWN_ERROR:
					x.innerHTML = "An unknown error occurred."
				break;
			}*/

			GeotWP_Location.saveInfo('geotLocation', 'no');
			$('div.geotloc_overlay').fadeOut('fast');
        },
        maybe_overlay: function() {

        	if( GeotWP_Location.getInfo('geotLocation') == null ) {
        		$('div.geotloc_overlay').fadeIn('slow');
        		//$('body').addClass('geotloc_overlay');
        		//$('body').css('background-color', '#000000');
        		//$('body').css('opacity', '0.5');
        		//$('body').css('background', 'url('+geotloc.img_src+')');
        	}
        },
        saveInfo: function(key = '', value = '') {

        	if( key.length == 0 )
				return false;

        	if ( typeof(Storage) !== 'undefined' ) {
        		localStorage.setItem(key, value);
        	}

        	return true;
        },
        getInfo: function(key = '') {
        	if( key.length == 0 )
				return false;

			let info;

        	if ( typeof(Storage) !== 'undefined' ) {
        		info = localStorage.getItem(key);
        	}

        	return info;
        }
    }

    GeotWP_Location.init();

    window.geotWP_location = GeotWP_Location;

})(jQuery);
