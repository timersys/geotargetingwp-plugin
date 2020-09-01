(function ($) {
    'use strict';

    $('document').ready(function () {

        $(".geot-chosen-select").selectize({});
        $(".geot-chosen-select-multiple").not('#widgets-left .geot-chosen-select-multiple').selectize({plugins: ['remove_button'],});

        $(".country_ajax").each(function () {
            var $select_city = $(this).next('.cities_container'),
                select_city = $select_city[0].selectize;

            $(this).selectize({
                onChange: function (value) {
                    if (!value.length) return;
                    select_city.disable();
                    select_city.clearOptions();
                    select_city.load(function (callback) {
                        jQuery.ajax({
                            url: geot.ajax_url,
                            type: 'POST',
                            dataType: 'json',
                            data: {
                                action: 'geot_cities_by_country',
                                country: value
                            },
                            error: function () {
                                callback();
                            },
                            success: function (res) {
                                select_city.settings.placeholder = 'Choose one o or more cities';
                                select_city.updatePlaceholder();
                                select_city.enable();
                                select_city.loaded = true;
                                callback(res);
                            }
                        });
                    });
                }
            });
        });

        if (typeof acf !== 'undefined' && typeof acf.add_action !== 'undefined') {
            acf.add_action('append', function ($el) {
                // $el will be equivalent to the new element being appended $('tr.row')
                var $select = $el.next('.acf-row').find('select');
                if ($select.length && typeof $select[0] !== 'undefined' && typeof $select[0].selectize !== 'undefined') {

                    var selectize = $select[0].selectize;
                    var options = [];

                    for (var key in selectize.options) {
                        options.push(selectize.options[key]);
                    }
                    $el.find('.selectize-control').remove()

                    var $field = $el.find('.geot-chosen-select-multiple');
                    $field.selectize({plugins: ['remove_button'], options: options});
                    var new_select = $field[0].selectize
                    new_select.clear(true);
                    new_select.enable();
                }

            });
        }


        $(".add-region").click(function (e) {
            e.preventDefault();
            var region = $(this).prev('.region-group');
            var new_region = region.clone();
            var new_id = parseInt(region.data('id')) + 1;

            new_region.find('input[type="text"]').attr('name', 'geot_settings[region][' + new_id + '][name]').val('');
            var $old_select = region.find('select');
            new_region.find('select').attr('name', 'geot_settings[region][' + new_id + '][countries][]').find("option:selected").removeAttr("selected");
            var $selectize = $old_select[0].selectize;
            new_region.find('.selectize-control').remove();
            new_region.insertAfter(region);
            new_region.find(".geot-chosen-select-multiple").selectize({
                options: geot_countries,
                plugins: ['remove_button'],
            });
        });

        $(".geot-settings").on('click', '.remove-region', function (e) {
            e.preventDefault();
            var region = $(this).parent('.region-group');
            region.remove();
        });

        $(".add-city-region").click(function (e) {
            e.preventDefault();
            var region = $(this).parent().find('.city-region-group').last();
            var new_region = region.clone();
            var cities = new_region.find(".cities_container");
            var chosen = new_region.find(".country_ajax");

            var new_id = parseInt(region.data('id')) + 1;
            new_region.attr('data-id', new_id);
            new_region.find('input[type="text"]').attr('name', 'geot_settings[city_region][' + new_id + '][name]').val('');
            chosen.attr('name', 'geot_settings[city_region][' + new_id + '][countries][]').find("option:selected").removeAttr("selected");
            cities.attr('name', 'geot_settings[city_region][' + new_id + '][cities][]').find("option:selected").removeAttr("selected");
            new_region.find('.selectize-control').remove();
            new_region.insertAfter(region);
            chosen.attr('data-counter', new_id);
            cities.attr('id', 'cities' + new_id);

            var $select_cities = cities.selectize({
                plugins: ['remove_button'],
                valueField: 'name',
                labelField: 'name',
                searchField: 'name',
                render: function (item, escape) {
                    return '<div>' + escape(item.name) + '</div>';
                },
            });
            var select_city = $select_cities[0].selectize;
            select_city.disable();
            select_city.clearOptions();
            var $select_countries = chosen.selectize({
                options: geot_countries,
                onChange: function (value) {
                    if (!value.length) return;
                    select_city.disable();
                    select_city.clearOptions();
                    select_city.load(function (callback) {
                        jQuery.ajax({
                            url: geot.ajax_url,
                            type: 'POST',
                            dataType: 'json',
                            data: {
                                action: 'geot_cities_by_country',
                                country: value
                            },
                            error: function () {
                                callback();
                            },
                            success: function (res) {
                                select_city.settings.placeholder = 'Choose one o or more cities';
                                select_city.updatePlaceholder();
                                select_city.enable();
                                callback(res);
                            }
                        });
                    });
                }
            });
            var select_countries = $select_countries[0].selectize;
            select_countries.clear(true);
        });

        $(".geot-settings").on('click', '.remove-city-region', function (e) {
            e.preventDefault();
            var region = $(this).parent('.city-region-group');
            region.remove();
        });

        // add state region
        $(".add-state-region").on('click', function (e) {
            e.preventDefault();

            const region = $(this).prev('.state-region-group'),
                new_region = region.clone(),
                new_id = parseInt(region.data('id'), 10) + 1;

            new_region.find('input.state-region-name').attr('name', 'geot_settings[state_region][' + new_id + '][name]').val('');
            new_region.find('input.state-region-list').attr('name', 'geot_settings[state_region][' + new_id + '][states]').val('');
            new_region.attr('data-id', new_id);

            new_region.insertAfter(region);
        });

        // Remove state region
        $(".geot-settings").on('click', '.remove-state-region', function (e) {
            e.preventDefault();
            const region = $(this).parent('.state-region-group');
            region.remove();
        });

        // add zip region
        $(".add-zip-region").on('click', function (e) {
            e.preventDefault();

            const region = $(this).prev('.zip-region-group'),
                new_region = region.clone(),
                new_id = parseInt(region.data('id'), 10) + 1;

            new_region.find('input.zip-region-name').attr('name', 'geot_settings[zip_region][' + new_id + '][name]').val('');
            new_region.find('input.zip-region-list').attr('name', 'geot_settings[zip_region][' + new_id + '][zips]').val('');
            new_region.attr('data-id', new_id);
            
            new_region.insertAfter(region);
        });

        // Remove zip region
        $(".geot-settings").on('click', '.remove-zip-region', function (e) {
            e.preventDefault();
            var region = $(this).parent('.zip-region-group');
            region.remove();
        });

        $(document).on('change', '.region-name', function () {

            $(this).val(slugify($(this).val()));
        });

        function slugify(str) {
            str = str.replace(/^\s+|\s+$/g, ''); // trim
            str = str.toLowerCase();

            // remove accents, swap ñ for n, etc
            var from = "àáäâèéëêìíïîòóöôùúüûñç·/_,:;";
            var to = "aaaaeeeeiiiioooouuuunc------";
            for (var i = 0, l = from.length; i < l; i++) {
                str = str.replace(new RegExp(from.charAt(i), 'g'), to.charAt(i));
            }

            str = str.replace(/[^a-z0-9 -]/g, '') // remove invalid chars
                .replace(/\s+/g, '-') // collapse whitespace and replace by -
                .replace(/-+/g, '-'); // collapse dashes

            return str;
        }

        function load_cities(o) {
            var counter = o.data('counter');
            var cities_select = $("#cities" + counter);
            var cities_choosen = cities_select.next('.chosen-container');
            cities_choosen.find('.default').val('loading....');
            $.post(
                geot.ajax_url,
                {action: 'geot_cities_by_country', country: o.val()},
                function (response) {
                    //cities_choosen.remove();
                    cities_select.html(response);
                    cities_select.trigger("chosen:updated");
                    cities_choosen.find('.default').val('Choose one');
                }
            );
        }

        $(document).on('widget-updated', function ( e, $widget ) {

            $(".geot-chosen-select-multiple", $widget).selectize({plugins: ['remove_button'],});

        });

        $(document).on('widget-added', function (ev, target) {
            $(target).find(".geot-chosen-select-multiple").selectize({plugins: ['remove_button'],});

        });

        $('.check-license').on('click', function (e) {
            e.preventDefault();
            var button = $(this),
                license = $('#license').val();
            button.prop('disabled', true).addClass('btn-spinner');
            $.ajax({
                'url': ajaxurl,
                'method': 'POST',
                'dataType': 'json',
                'data': {action: 'geot_check_license', license: license},
                'success': function (response) {
                    if (response.error) {
                        $('<p style="color:red">' + response.error + '</p>').insertAfter(button).hide().fadeIn();
                        $('#license').removeClass('geot_license_valid')
                    }
                    if (response.success) {
                        $('<p style="color:green">' + response.success + '</p>').insertAfter(button).hide().fadeIn();
                        $('#license').addClass('geot_license_valid');
                    }
                    button.prop('disabled', false).removeClass('btn-spinner');
                },
                'error': function (response) {
                    $('<p style="color:red">' + response.error + '</p>').insertAfter(button).hide().fadeIn();
                    $('#license').removeClass('geot_license_valid')
                    button.prop('disabled', false).removeClass('btn-spinner');
                }
            });
        });

        $('.check-addons-license').on('click', function (e) {
            e.preventDefault();
            var button = $(this),
                license = $('#license').val();
            button.prop('disabled', true).addClass('btn-spinner');
            $.ajax({
                'url': geot.ajax_url,
                'method': 'POST',
                'dataType': 'json',
                'data': {action: 'geot_check_license', license: license},
                'success': function (response) {
                    if (response.error) {
                        $('#response_error').html('<p style="color:red">' + response.error + '</p>').hide().fadeIn();
                        button.prop('disabled', false).removeClass('btn-spinner');
                    }
                    if (response.success) {
                        button.prop('disabled', false).removeClass('btn-spinner');
                        $('.check-addons-license').off('click').click();
                        button.prop('disabled', true);
                    }
                },
                'error': function (response) {
                    $('#response_error').html('<p style="color:red">' + response.error + '</p>').hide().fadeIn();
                    $('#license').removeClass('geot_license_valid')
                    button.prop('disabled', false).removeClass('btn-spinner');
                }
            });
        });


        $("#geolocation").on('change', function (e) {
            const geo = $(this).val();

            if( geo == 'by_html5' || geo == 'by_html5_mobile' )
                $('.force_geot_field').show();
            else
                $('.force_geot_field').hide();
        });
    });

})(jQuery);
