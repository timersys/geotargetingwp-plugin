jQuery(document).ready(function ($) {

    //create TinyMCE plugin
    tinymce.create('tinymce.plugins.geo_link', {

        init: function (ed, url) {

            // Setup the command when the button is pressed
            ed.addCommand('geo_link_insert_shortcode', function () {

                jQuery('#geol_editor').dialog({
                    height: 500,
                    width: '600px',
                    buttons: {
                        "Insert Shortcode": function () {

                            var geol_slug = jQuery('#geol-posts').val();
                            var geol_nofo = jQuery('input[name="geol_nofollow"]').val();
                            var geol_nore = jQuery('input[name="geol_noreferrer"]').val();
                            var str = '';

                            str = '[geo-link slug="' + geol_slug + '"';

                            if (geol_nofo == 'yes')
                                str += ' nofollow="yes"';
                            else
                                str += ' nofollow="no"';

                            if (geol_nore == 'yes')
                                str += ' noreferrer="yes"';
                            else
                                str += ' noreferrer="no"';


                            var selected_text = ed.selection.getContent();
                            if (selected_text) {

                                str += "]" + selected_text + "[/geo-link]";

                            } else {

                                str += "]YOUR TEXT OR IMG HERE[/geo-link]";

                            }

                            var Editor = tinyMCE.get('content');
                            Editor.focus();
                            Editor.selection.setContent(str);

                            jQuery(this).dialog("close");
                        },
                        Cancel: function () {
                            jQuery(this).dialog("close");
                        }
                    }
                }).dialog('open');

            });

            //Add Button to Visual Editor Toolbar and launch the above command when it is clicked.
            ed.addButton('geo_link', {
                title: 'Geolinks shortcode',
                cmd: 'geo_link_insert_shortcode',
                image: geol_tinymce.icon
            });
        },
    });

    //Setup the TinyMCE plugin. The first parameter is the button ID and the second parameter must match the first parameter of the above "tinymce.create ()" function.
    tinymce.PluginManager.add('geo_link', tinymce.plugins.geo_link);
});