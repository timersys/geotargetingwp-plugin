/**
 * Register: Geotargenting Gutenberg Block.
 *
 * Registers a new block provided a unique name and an object defining its
 * behavior. Once registered, the block is made editor as an option to any
 * editor interface where blocks are implemented.
 *
 * @link https://wordpress.org/gutenberg/handbook/block-api/
 * @param  {string}   name     Block name.
 * @param  {Object}   settings Block settings.
 * @return {?WPBlock}          The block, if it has been successfully
 *                             registered; otherwise `undefined`.
 */

registerBlockType('geotargeting-pro/gutenberg-radius', {
    title: __('Radius', 'geot'),
    description: __('You can place other blocks inside this container', 'geot'),
    icon: el('img', {width: 20, height: 20, src: gutgeot.icon_radius}),
    category: 'geot-block',
    keywords: [__('inner-blocks'),],

    attributes: {
        radius_km: {
            type: 'string',
            default: '',
        },
        radius_lat: {
            type: 'string',
            default: '',
        },
        radius_lng: {
            type: 'string',
            default: '',
        }
    },

    edit: function (props) {
        const {attributes, setAttributes, className, focus, setFocus} = props;
        const {radius_km, radius_lat, radius_lng} = attributes;

        const ALLOWED_BLOCKS = [];

        getBlockTypes().forEach(function (blockType) {
            if (gutgeot.modules.indexOf(blockType.name) == -1)
                ALLOWED_BLOCKS.push(blockType.name);
        });

        var block_top_msg = __('You can modify the settings of the block in the sidebar.', 'geot');
        var block_sign_msg = [];

        function onChangeRadiusKm(newContent) {
            setAttributes({radius_km: newContent});
        }

        function onChangeRadiusLat(newContent) {
            setAttributes({radius_lat: newContent});
        }

        function onChangeRadiusLng(newContent) {
            setAttributes({radius_lng: newContent});
        }

        if (radius_km) {
            block_sign_msg.push(__('Radius (km)', 'geot') + ' : ' + radius_km);
        }

        if (radius_lat) {
            block_sign_msg.push(__('Latitude', 'geot') + ' : ' + radius_lat);
        }

        if (radius_lng) {
            block_sign_msg.push(__('Longitude', 'geot') + ' : ' + radius_lng);
        }

        if (block_sign_msg.length != 0)
            block_top_msg = block_sign_msg.join(' , ');


        return el(Fragment, {},
            el(InspectorControls, {},
                el(PanelBody, {title: __('Radius Settings', 'geot')},
                    el(PanelRow, {},
                        el(TextControl, {
                            label: __('Radius (km)', 'geot'),
                            value: radius_km,
                            onChange: onChangeRadiusKm,
                            help: __('Type the range.', 'geot')
                        }),
                    ),
                    el(PanelRow, {},
                        el(TextControl, {
                            label: __('Latitude', 'geot'),
                            value: radius_lat,
                            onChange: onChangeRadiusLat,
                            help: __('Type the latitude.', 'geot'),
                        }),
                    ),
                    el(PanelRow, {},
                        el(TextControl, {
                            label: __('Longitude', 'geot'),
                            value: radius_lng,
                            onChange: onChangeRadiusLng,
                            help: __('Type the Longitude.', 'geot'),
                        }),
                    ),
                ),
            ),
            el('div', {className: className},
                el('div', {}, block_top_msg),
                el(InnerBlocks, {allowedBlocks: ALLOWED_BLOCKS})
            )
        );
    },
    save: function () {
        return el('div', {}, el(InnerBlocks.Content));
    }
});