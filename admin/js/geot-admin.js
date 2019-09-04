(function ($) {
    'use strict';

    $('document').ready(function () {
        geot.rules.init();
    });

    var geot = {rules: null}

    /*
    *  Rules
    *
    *  Js for needed for rules
    *
    *  @since: 1.0.0
    *  Thanks to advanced custom fields plugin for part of this code
    */

    geot.rules = {
        $el: null,
        init: function () {

            // vars
            var _this = this;

            // $el
            _this.$el = $('#geot-rules');

            // add rule
            _this.$el.on('click', '.rules-add-rule', function () {

                _this.add_rule($(this).closest('tr'));
                return false;
            });


            // remove rule
            _this.$el.on('click', '.rules-remove-rule', function () {

                _this.remove_rule($(this).closest('tr'));
                return false;
            });


            // add rule
            _this.$el.on('click', '.rules-add-group', function () {

                _this.add_group();
                return false;
            });


            // change rule
            _this.$el.on('change', '.param select', function () {

                // vars
                var $tr = $(this).closest('tr'),
                    rule_id = $tr.attr('data-id'),
                    $group = $tr.closest('.rules-group'),
                    group_id = $group.attr('data-id'),
                    val_td = $tr.find('td.value'),
                    ajax_data = {
                        'action': "geot/field_group/render_rules",
                        'nonce': geot_js.nonce,
                        'rule_id': rule_id,
                        'group_id': group_id,
                        'value': '',
                        'param': $(this).val()
                    };


                // add loading gif
                var div = $('<div class="geot-loading"><img src="' + geot_js.admin_url + '/images/wpspin_light.gif"/> </div>');
                val_td.html(div);

                // load rules html
                $.ajax({
                    url: ajaxurl,
                    data: ajax_data,
                    type: 'post',
                    dataType: 'html',
                    success: function (html) {
                        val_td.html(html);

                    }
                });

                // Operators Rules
                var operator_td = $tr.find('td.operator'),
                    ajax_data = {
                        'action': "geot/field_group/render_operator",
                        'nonce': geot_js.nonce,
                        'rule_id': rule_id,
                        'group_id': group_id,
                        'value': '',
                        'param': $(this).val()
                    };

                operator_td.html(div);
                $.ajax({
                    url: ajaxurl,
                    data: ajax_data,
                    type: 'post',
                    dataType: 'html',
                    success: function (html) {

                        operator_td.html(html);
                    }
                });

            });
        },
        add_rule: function ($tr) {

            // vars
            var $tr2 = $tr.clone(),
                old_id = $tr.parent().find('tr').last().attr('data-id'),
                current_id = $tr2.attr('data-id'),
                new_id = 'rule_' + (parseInt(old_id.replace('rule_', ''), 10) + 1);

            // update names
            $tr2.find('[name]').each(function () {

                $(this).attr('name', $(this).attr('name').replace(current_id, new_id));
                $(this).attr('id', $(this).attr('id').replace(current_id, new_id));
            });

            // update data-i
            $tr2.attr('data-id', new_id);

            // add tr
            $tr.after($tr2);

            return false;
        },
        remove_rule: function ($tr) {

            // vars
            var siblings = $tr.siblings('tr').length;

            if (siblings == 0) {
                // remove group
                this.remove_group($tr.closest('.rules-group'));
            } else {
                // remove tr
                $tr.remove();
            }

        },
        add_group: function () {

            // vars
            var $group = this.$el.find('.rules-group:last'),
                $group2 = $group.clone(),
                old_id = $group2.attr('data-id'),
                new_id = 'group_' + (parseInt(old_id.replace('group_', ''), 10) + 1);

            // update names
            $group2.find('[name]').each(function () {

                $(this).attr('name', $(this).attr('name').replace(old_id, new_id));
                $(this).attr('id', $(this).attr('id').replace(old_id, new_id));
            });


            // update data-i
            $group2.attr('data-id', new_id);

            // update h4
            $group2.find('h4').html(geot_js.l10n.or).addClass('rules-or');

            // remove all tr's except the first one
            $group2.find('tr:not(:first)').remove();

            // add tr
            $group.after($group2);
        },
        remove_group: function ($group) {
            $group.remove();
        }
    };
})(jQuery);
