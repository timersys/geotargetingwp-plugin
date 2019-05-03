(function( $ ) {
	'use strict';

    const urlParams = new URLSearchParams(window.location.search);
    const geot_debug = urlParams.get('geot_debug'),
     geot_debug_iso  = urlParams.get('geot_debug_iso'),
     geot_state  = urlParams.get('geot_state'),
     geot_state_code  = urlParams.get('geot_state_code'),
     geot_city  = urlParams.get('geot_city'),
     geot_zip  = urlParams.get('geot_zip');

        var data = {
                action : 'geo_blocks',
                pid : geobl.pid,
                referrer : document.referrer,
                query_string : document.location.search,
                is_category : geobl.is_category,
                is_archive : geobl.is_archive,
                is_front_page : geobl.is_front_page,
                is_search : geobl.is_search,
                geot_debug : geot_debug,
                geot_debug_iso  : geot_debug_iso,
                geot_state  : geot_state,
                geot_state_code  : geot_state_code,
                geot_city  : geot_city,
                geot_zip  : geot_zip
            }
            ,success_cb = function(response) {
                if( response && response.length ){
                    $('html').html(response);
                }
            },
            error_cb 	= function (data, error, errorThrown){
                console.log('Geo Redirects error: ' + error + ' - ' + errorThrown);
            }
        request(data, success_cb, error_cb);

    /**
     * Ajax requests
     * @param data
     * @param url
     * @param success_cb
     * @param error_cb
     * @param dataType
     */
    function request(data, success_cb, error_cb ){
        // Prepare variables.
        var ajax       = {
                url:      geobl.ajax_url,
                data:     data,
                cache:    false,
                type:     'POST',
                dataType: 'html',
                timeout:  30000
            },
            dataType   = dataType || false,
            success_cb = success_cb || false,
            error_cb   = error_cb   || false;


        // Set success callback if supplied.
        if ( success_cb ) {
            ajax.success = success_cb;
        }

        // Set error callback if supplied.
        if ( error_cb ) {
            ajax.error = error_cb;
        }

        // Make the ajax request.
        $.ajax(ajax);

    }
})( jQuery );
