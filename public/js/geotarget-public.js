(function ($) {
    'use strict';

    const GeotWP  = {
        uniqueID : null,
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
                    'geot_debug': geot_debug,
                    'geot_debug_iso': geot_debug_iso,
                    'geot_state': geot_state,
                    'geot_state_code': geot_state_code,
                    'geot_city': geot_city,
                    'geot_zip': geot_zip,
                };

            if( $('.geot-ajax').length > 0 && geot.is_builder != '1' ) {
                $('.geot-placeholder').show();

                $('.geot-ajax').each(function () {
                    let _this = $(this);
                    if (_this.hasClass('geot_menu_item'))
                        _this = $(this).find('a').first();

                    if( _this.data('action') && _this.data('action').length ) {
                        const uniqid = GeotWP.getUniqueName('geot');
                        _this.attr('id', uniqid);
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
            }

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
                        $('.geotr-ajax').show();
                        setTimeout(function () {
                            location.replace(redirect.url)
                        }, 2000);
                    }

                    if( blocker && blocker.length ) {
                        $('html').html(blocker);
                    }
                    console.log(response);
                    if ( results && results.length ) {
                        for ( i = 0; i < results.length; ++i ) {
                            if ( results[i].action == 'menu_filter' ) {
                                if (results[i].value != true) {
                                    $('#' + results[i].id).parent('.menu-item').removeClass('geot_menu_item');
                                } else {
                                    $('#' + results[i].id).parent('.menu-item').remove();
                                }
                            } else if ( results[i].action == 'widget_filter' ) {
                                const widget_id = $('#' + results[i].id).data('widget');
                                if ( results[i].value != true ) {
                                    $('#css-' + widget_id).remove();
                                } else {
                                    $('#' + widget_id).remove();
                                }
                                $('#' + results[i].id).remove();
                            } else if ( results[i].action.indexOf('filter' ) > -1) {
                                if ( results[i].value == true ) {
                                    var html = $('#' + results[i].id).html();
                                    $('#' + results[i].id).replaceWith(html);
                                }
                                $('#' + results[i].id).remove();
                            } else {
                                $('#' + results[i].id).replaceWith(results[i].value);
                            }
                        }
                    }
                    if (remove && remove.length) {
                        for (i = 0; i < remove.length; ++i) {
                            let id = remove[i];
                            $('#post-' + id + ', .post-' + id).remove();
                        }
                    }
                    if (hide && hide.length) {
                        for (i = 0; i < hide.length; ++i) {
                            let id = hide[i].id;
                            $('#post-' + id + ' .entry-content, .post-' + id + ' .entry-content').html('<p>' + hide[i].msg + '</p>');
                        }
                    }
                    if (debug && debug.length) {
                        $('#geot-debug-info').html(debug);
                        $('.geot-debug-data').html(debug.replace(/<!--|-->/gi, ''));
                    }
                    $(document).trigger('geotwp_ajax_success');
                }
            }

            const error_cb = function (data, error, errorThrown) {
                console.log('Geot Ajax error: ' + error + ' - ' + errorThrown);
            }
            if (geot && geot.ajax)
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
