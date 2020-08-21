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

registerBlockType('geotargeting-pro/gutenberg-state', {
    title: __('Target States', 'geot'),
    description: __('You can place other blocks inside this container', 'geot'),
    icon: el('img', {width: 20, height: 20, src: gutgeot.icon_state}),
    category: 'geot-block',
    keywords: [__('inner-blocks'),],

    attributes: {
        in_states: {
            type: 'string',
            default: '',
        },
        in_regions: {
            type: 'array',
            default: [],
        },
        ex_states: {
            type: 'string',
            default: '',
        },
        ex_regions: {
            type: 'array',
            default: [],
        },
    },

    edit: function (props) {
        const {attributes, setAttributes, className, focus, setFocus} = props;
        const {in_states, in_regions, ex_states, ex_regions} = attributes;

        const ALLOWED_BLOCKS = [];

        getBlockTypes().forEach(function (blockType) {
            if (gutgeot.modules.indexOf(blockType.name) == -1)
                ALLOWED_BLOCKS.push(blockType.name);
        });

        var block_top_msg = __('You can modify the settings of the block in the sidebar.', 'geot');
        var block_sign_msg = [];

        function onChangeInStates(newContent) {
            setAttributes({in_states: newContent});
        }

        function onChangeExStates(newContent) {
            setAttributes({ex_states: newContent});
        }

        function onChangeInRegions(newContent) {
            setAttributes({in_regions: newContent});
        }

        function onChangeExRegions(newContent) {
            setAttributes({ex_regions: newContent});
        }

        if (in_states) {
            block_sign_msg.push(__('Include States', 'geot') + ' : ' + in_states);
        }

        if (ex_states) {
            block_sign_msg.push(__('Exclude States', 'geot') + ' : ' + ex_states);
        }

        if (in_regions.length) {
            block_sign_msg.push(__('Include Regions', 'geot') + ' : ' + in_regions.join(' , '));
        }

        if (ex_regions.length) {
            block_sign_msg.push(__('Exclude Regions', 'geot') + ' : ' + ex_regions.join(' , '));
        }


        if (block_sign_msg.length != 0)
            block_top_msg = block_sign_msg.join(' , ');


        return el(Fragment, {},
            el(InspectorControls, {},
                el(PanelBody, {title: __('Target States Settings', 'geot')},
                    el(PanelRow, {},
                        el(TextControl, {
                            label: __('Include States', 'geot'),
                            value: in_states,
                            onChange: onChangeInStates,
                            help: __('Type state names or ISO codes separated by comma.', 'geot')
                        }),
                    ),
                    el(PanelRow, {},
                        el(SelectControl, {
                                label: __('Include State Regions', 'geot'),
                                multiple: true,
                                options: gutgeot.regions_state,
                                onChange: onChangeInRegions,
                                value: in_regions,
                                help: __('Choose region name to show content to', 'geot'),
                            },
                        ),
                    ),
                    el(PanelRow, {},
                        el(TextControl, {
                            label: __('Exclude States', 'geot'),
                            value: ex_states,
                            onChange: onChangeExStates,
                            help: __('Type state names or ISO codes separated by comma.', 'geot'),
                        }),
                    ),
                    el(PanelRow, {},
                        el(SelectControl, {
                                label: __('Exclude State Regions', 'geot'),
                                multiple: true,
                                options: gutgeot.regions_state,
                                onChange: onChangeExRegions,
                                value: ex_regions,
                                help: __('Choose region name to exclude content', 'geot'),
                            },
                        ),
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