(function ($) {
    'use strict';

    const GeotWP  = {
        uniqueID : null,
        lat : null,
        lng : null,
        img_geoloc : null,
        img_consent : null,
        /**
         * Start function
         */
        init: function () {
            $(document).ready( GeotWP.ready );
        },
        /**
         * When dom it's ready
         */
        ready: function () {
            GeotWP.initSelectize();
            GeotWP.initBrowser();

            /* Geolocation */
            if( geot.geoloc_enable && geot.geoloc_enable == 'by_html5' ) {
                GeotWP.executeGps();

            } else if( geot.geoloc_enable && geot.geoloc_enable == 'by_html5_mobile' && GeotWP.isMobile() ) {
                GeotWP.executeGps();

            } else {
                GeotWP.executeAjax();

                if( geot.elementor_popup )
                    $( document ).on( 'elementor/popup/show', GeotWP.executeAjax );
            }
        },
        executeGps: function() {
            const cords = JSON.parse( sessionStorage.getItem('geot_cords') );
            if( cords ) {
                GeotWP.lat = cords.lat;
                GeotWP.lng = cords.lng;
                GeotWP.executeAjax();
            } else {
                if (navigator.geolocation) {
                    GeotWP.maybe_overlay();

                    // Set Geolocation
                    navigator.geolocation.getCurrentPosition(
                        GeotWP.successPosition,
                        GeotWP.errorPosition,
                        {maximumAge:10000, timeout:5000, enableHighAccuracy: true}
                    );

                } else {
                    console.log(geot.geoloc_fail);
                }
            }
        },
        executeAjax: function() {
            if( ( ! $('.geot-ajax').length &&
                ( ! geot.has_geo_posts || geot.has_geo_posts.length == 0 ) &&
                ! $('.geotr-ajax').length &&
                ! $('.geobl-ajax').length )||
                geot.is_builder == '1' ) {
                return;
            }
            const geot_debug = GeotWP.getUrlParameter('geot_debug'),
                geot_debug_iso = GeotWP.getUrlParameter('geot_debug_iso'),
                geot_state = GeotWP.getUrlParameter('geot_state'),
                geot_state_code = GeotWP.getUrlParameter('geot_state_code'),
                geot_city = GeotWP.getUrlParameter('geot_city'),
                geot_zip = GeotWP.getUrlParameter('geot_zip');
            let data = {
                    'action': 'geot_ajax',
                    'geots': {},
                    'vars': geot,
                    'pid': geot.pid,
                    'referrer': document.referrer,
                    'url': window.location.href,
                    'query_string': document.location.search,
                    'is_category': geot.is_category,
                    'is_archive': geot.is_archive,
                    'is_front_page': geot.is_front_page,
                    'is_search': geot.is_search,
                    'browser_language': navigator.language || navigator.userLanguage,
                    'geot_debug': geot_debug,
                    'geot_debug_iso': geot_debug_iso,
                    'geot_state': geot_state,
                    'geot_state_code': geot_state_code,
                    'geot_city': geot_city,
                    'geot_zip': geot_zip,
                    'geot_lat': GeotWP.lat,
                    'geot_lng': GeotWP.lng,
                };


                $('.geot-placeholder').show();

                $('.geot-ajax').each(function () {
                    let _this = $(this);
                    if (_this.hasClass('geot_menu_item'))
                        _this = $(this).find('a').first();

                    if( _this.data('action') && _this.data('action').length ) {
                        const uniqid = GeotWP.getUniqueName('geot');
                        _this.addClass(uniqid);
                        data.geots[uniqid] = {
                            'action': _this.data('action') || '',
                            'filter': _this.data('filter') || '',
                            'region': _this.data('region') || '',
                            'ex_filter': _this.data('ex_filter') || '',
                            'ex_region': _this.data('ex_region') || '',
                            'default': _this.data('default') || '',
                            'locale': _this.data('locale') || 'en',
                        }
                    }
                });


            if( $('.geotr-ajax').length )
                data.geot_redirects = 1;

            if( $('.geobl-ajax').length )
                data.geot_blockers = 1;


            const onSuccess = function (response) {
                if (response.success) {

                    $('.geot-placeholder').remove();

                    let results = response.data,
                        i,
                        redirect = response.redirect,
                        blocker = response.blocker,
                        remove = response.posts.remove,
                        hide = response.posts.hide,
                        debug = response.debug;

                    if( redirect && redirect.url ) {
                        let do_redirect = true;
                        if( parseInt( redirect.one_time_redirect ) == 1) {
                            if( localStorage.getItem('geo_redirect_' + redirect.id ) ){
                                do_redirect = false;
                            }
                            localStorage.setItem( 'geo_redirect_' + redirect.id, true );
                        } if(  parseInt( redirect.one_time_redirect ) == 2) {
                            if( sessionStorage.getItem('geo_redirect_' + redirect.id ) ){
                                do_redirect = false;
                            }
                            sessionStorage.setItem( 'geo_redirect_' + redirect.id, true )
                        }
                        if( do_redirect ) {
                            $('.geotr-ajax').show();
                            setTimeout(function () {
                                location.replace(redirect.url)
                            }, 2000);
                        }
                    }

                    if( blocker && blocker.length ) {
                        $('html').html(blocker);
                    }
                    console.log(response);
                    if ( results && results.length ) {
                        for ( i = 0; i < results.length; ++i ) {
                            if ( results[i].action == 'menu_filter' ) {
                                if (results[i].value != true) {
                                    $('.' + results[i].id).parent('.geot_menu_item').removeClass('geot_menu_item');
                                } else {
                                    $('.' + results[i].id).parent('.geot_menu_item').remove();
                                }
                            } else if ( results[i].action == 'widget_filter' ) {
                                const widget_id = $('.' + results[i].id).data('widget');
                                if ( results[i].value != true ) {
                                    $('#css-' + widget_id).remove();
                                } else {
                                    $('#' + widget_id).remove();
                                }
                                $('.' + results[i].id).remove();
                            } else if ( results[i].action.indexOf('filter' ) > -1) {
                                if ( results[i].value == true ) {
                                    var html = $('.' + results[i].id).html();
                                    $('.' + results[i].id).replaceWith(html);
                                }
                                $('.' + results[i].id).remove();
                            } else {
                                $('.' + results[i].id).replaceWith(results[i].value);
                            }
                        }
                    }
                    if (remove && remove.length) {

                        for (i = 0; i < remove.length; ++i) {
                            let id = remove[i];
                            let remove_class = '#post-' + id + ', .post-' + id;
                            if( geot.remove_class.length ) {
                                remove_class = remove_class + ',' + geot.remove_class.replace('%id', id);
                            }
                            $(remove_class).remove();
                        }
                    }
                    if (hide && hide.length) {

                        for (i = 0; i < hide.length; ++i) {
                            let id = hide[i].id;
                            let hide_class = '#post-' + id + ' .entry-content,' +
                                '#post-' + id + ' .woocommerce-product-details__short-description,' +
                                '.post-' + id + ' .entry-content,' +
                                '.post-' + id + ' .woocommerce-product-details__short-description';
                            if( geot.hide_class.length ) {
                                hide_class = hide_class + ',' + geot.hide_class.replace('%id', id);;
                            }
                            $( hide_class ).html('<p>' + hide[i].msg + '</p>');
                        }
                    }
                    if (debug && debug.length) {
                        $('#geot-debug-info').html(debug);
                        $('.geot-debug-data').html(debug.replace(/<!--|-->/gi, ''));
                    }
                    $(document).trigger('geotwp_ajax_success', response);
                }
            }

            const error_cb = function (data, error, errorThrown) {
                console.log('Geot Ajax error: ' + error + ' - ' + errorThrown);
            }
            if ( geot && geot.ajax )
                GeotWP.request(data, onSuccess, error_cb);
        },
        /**
         * Start the geot dropdown widget
         */
        initSelectize: function() {
            if (geot && (/iP(od|hone)/i.test(window.navigator.userAgent) || /IEMobile/i.test(window.navigator.userAgent) || /Windows Phone/i.test(window.navigator.userAgent) || /BlackBerry/i.test(window.navigator.userAgent) || /BB10/i.test(window.navigator.userAgent) || /Android.*Mobile/i.test(window.navigator.userAgent))) {
                geot.dropdown_search = true;
            }
            let geot_options = {
                onChange: function (country_code) {
                    if (!country_code.length)
                        return;
                    GeotWP.createCookie('geot_country', country_code, 999);
                    if (geot.dropdown_redirect && geot.dropdown_redirect.length) {
                        window.location.replace(geot.dropdown_redirect);
                    } else {
                        window.location.reload();
                    }
                }
            };
            if ($('.geot_dropdown').data('flags')) {
                geot_options.render = {
                    option: function (data, escape) {
                        return '<div class="option">' +
                            '<span class="geot-flag flag-' + escape(data.value.toLowerCase()) + '"></span>' +
                            '<span class="url">' + escape(data.text) + '</span>' +
                            '</div>';
                    },
                    item: function (data, escape) {
                        return '<div class="item"><span class="geot-flag flag-' + escape(data.value.toLowerCase()) + '"></span>' + escape(data.text) + '</div>';
                    }
                };
            }
            if ($('.geot_dropdown').length) {
                let $geot_select = $('.geot_dropdown').selectize(geot_options);
                if (GeotWP.readCookie('geot_country')) {
                    let selectize = $geot_select[0].selectize;
                    selectize.addItem(GeotWP.readCookie('geot_country'), true);
                }
            }
        },
        isMobile : function() {
            let check = false;
            (function(a){if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i.test(a)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0,4))) check = true;})(navigator.userAgent||navigator.vendor||window.opera);
            return check;
        },
        /**
         * Detected Browser
         * Source : https://jsfiddle.net/6spj1059/
         * @returns
         */
        initBrowser: function() {
            // Opera 8.0+
            const isOpera = (!!window.opr && !!opr.addons) || !!window.opera || navigator.userAgent.indexOf(' OPR/') >= 0;

            // Firefox 1.0+
            const isFirefox = typeof InstallTrigger !== 'undefined';

            // Safari 3.0+ "[object HTMLElementConstructor]" 
            const isSafari = /constructor/i.test(window.HTMLElement) || (function (p) { return p.toString() === "[object SafariRemoteNotification]"; })(!window['safari'] || (typeof safari !== 'undefined' && safari.pushNotification));

            // Internet Explorer 6-11
            const isIE = /*@cc_on!@*/false || !!document.documentMode;

            // Edge 20+
            const isEdge = !isIE && !!window.StyleMedia;

            // Chrome 1 - 79
            const isChrome = !!window.chrome && (!!window.chrome.webstore || !!window.chrome.runtime);


            if( isOpera ) {
                GeotWP.img_geoloc = geot.geoloc_img_opera;
                GeotWP.img_consent = geot.geoloc_consent_opera;
            } else if( isFirefox ) {
                GeotWP.img_geoloc = geot.geoloc_img_firefox;
                GeotWP.img_consent = geot.geoloc_consent_firefox;
            } else if( isSafari ) {
                GeotWP.img_geoloc = geot.geoloc_img_safari;
                GeotWP.img_consent = geot.geoloc_consent_safari;
            } else if( isEdge || isIE ) {
                GeotWP.img_geoloc = geot.geoloc_img_edge;
                GeotWP.img_consent = geot.geoloc_consent_edge;
            } else {
                GeotWP.img_geoloc = geot.geoloc_img_chrome;
                GeotWP.img_consent = geot.geoloc_consent_chrome;
            }  
        },
        /**
         * Generate unique id
         * @param prefix
         * @returns {*}
         */
        getUniqueName: function (prefix) {
            if (! GeotWP.uniqueID) {
                GeotWP.uniqueID = (new Date()).getTime();
            }
            return prefix + (GeotWP.uniqueID++);
        },
        /**
         * When Geolocation get the coordinates successfully
         * @return {[type]} [description]
         */
        successPosition: function(position) {
            GeotWP.createCookie('geot-gps', 'yes', 999);

            const $overlay = $('div.geotloc_overlay_box');
            $overlay.fadeOut('fast');

            // If first time, refresh
            if( GeotWP.readCookie('geotRefresh') == null ) {
                GeotWP.createCookie('geotRefresh', 'yes');
                window.location.reload();
            }

            GeotWP.lat = position.coords.latitude;
            GeotWP.lng = position.coords.longitude;
            sessionStorage.setItem('geot_cords', JSON.stringify({ lat: GeotWP.lat, lng: GeotWP.lng } ) );

            GeotWP.executeAjax();
        },
        /**
         * When Geolocation not get the coordinates
         * @param  OBJ error
         * @return mixed
         */
        errorPosition: function(error) {
            GeotWP.createCookie('geot-gps', 'no');

            const $overlay = $('div.geotloc_overlay_box');

            if( geot.geoloc_force ) {
                $overlay.find('.geotloc_overlay_content').append(GeotWP.img_consent);

                if( ! $overlay.is(':visible') )
                    GeotWP.show_overlay();
            } else {
                $overlay.fadeOut('fast');
            }

            // If first time, refresh
            if(  GeotWP.readCookie('geotRefresh') == null && ! geot.geoloc_force ) {
                GeotWP.createCookie('geotRefresh', 'yes');
                window.location.reload();
            }
        },
        /**
         * Put Shadow Overlay
         * @return mixed
         */
        maybe_overlay: function() {

            const $overlay = $('div.geotloc_overlay_box');

            if( GeotWP.readCookie('geot-gps') == null ) {

                $overlay.find('div.geotloc_overlay_content').append(GeotWP.img_geoloc);
                GeotWP.show_overlay();
            }

            $overlay.find('.geotloc_overlay_remove').on('click', function() {
                $overlay.fadeOut('fast');
            });
        },
        /**
         * Show Overlay
         * @return mixed
         */
        show_overlay: function() {
            const $overlay = $('div.geotloc_overlay_box');

            if( geot.geoloc_force )
                $overlay.find('div.geotloc_overlay_remove').hide();
            else
                $overlay.find('div.geotloc_overlay_remove').show();
            
            $overlay.fadeIn('slow');
        },
        /**
         * Create Cookies
         * @param name
         * @param value
         * @param days
         */
        createCookie: function(name, value, days) {
            let expires = "";
            if (days) {
                let date = new Date();
                date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
                expires = "; expires=" + date.toGMTString();
            }
            document.cookie = name + "=" + value + expires + "; path=/";
        },
        /**
         * Delete Cookies
         * @param name
         * @returns {string|null}
         */
        deleteCookie: function(name) {
            document.cookie = name + '=;expires=Thu, 01 Jan 1970 00:00:01 GMT;';
        },
        /**
         * Read Cookies
         * @param name
         * @returns {string|null}
         */
        readCookie: function(name) {
            let nameEQ = name + "=";
            let ca = document.cookie.split(';');
            for (let i = 0; i < ca.length; i++) {
                let c = ca[i];
                while (c.charAt(0) == ' ') c = c.substring(1, c.length);
                if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
            }
            return null;
        },
        /**
         * Perform Ajax requests
         * @param data
         * @param success_cb
         * @param error_cb
         * @param dataType
         */
        request:  function (data, success_cb, error_cb, dataType) {
            // Prepare variables.
            let ajax = {
                    url: geot.ajax_url,
                    data: data,
                    cache: false,
                    type: 'POST',
                    dataType: 'json',
                    timeout: 30000
                },
                data_type = dataType || false,
                success = success_cb || false,
                error = error_cb || false;

            // Set success callback if supplied.
            if (success) {
                ajax.success = success;
            }

            // Set error callback if supplied.
            if (error) {
                ajax.error = error;
            }

            // Change dataType if supplied.
            if (data_type) {
                ajax.dataType = data_type;
            }
            // Make the ajax request.
            $.ajax(ajax);

        },
        /**
         * Get parameter from url
         * @param name
         * @returns {string}
         */
        getUrlParameter: function(name) {
            name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
            let regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
            let results = regex.exec(window.location.search);
            return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
        }
    }

    GeotWP.init();

    window.geotWP = GeotWP;

})(jQuery);
