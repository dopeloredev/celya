(function () {
    var el                  = wp.element.createElement;
    var Fragment            = wp.element.Fragment;
    var registerBlockType   = wp.blocks.registerBlockType;
    var InspectorControls   = wp.blockEditor.InspectorControls;
    var useBlockProps       = wp.blockEditor.useBlockProps;
    var useInnerBlocksProps = wp.blockEditor.useInnerBlocksProps;
    var InnerBlocks         = wp.blockEditor.InnerBlocks;
    var PanelBody           = wp.components.PanelBody;
    var RangeControl        = wp.components.RangeControl;

    registerBlockType('celya/image-cards', {

        edit: function (props) {
            var attrs   = props.attributes;
            var setAttr = props.setAttributes;
            var columns = attrs.columns;
            var gap     = attrs.gap;

            var blockProps = useBlockProps({
                className: 'celya-image-cards',
                style: {
                    gridTemplateColumns: 'repeat(' + columns + ', 1fr)',
                    gap: gap + 'px',
                },
            });

            var innerBlocksProps = useInnerBlocksProps(blockProps, {
                allowedBlocks: ['celya/image-card'],
                template: [
                    ['celya/image-card', {}],
                    ['celya/image-card', {}],
                    ['celya/image-card', {}],
                ],
                orientation: 'horizontal',
                renderAppender: InnerBlocks.ButtonBlockAppender,
            });

            var inspector = el(InspectorControls, null,
                el(PanelBody, { title: 'Mise en page', initialOpen: true },
                    el(RangeControl, {
                        label:    'Nombre de colonnes',
                        value:    columns,
                        onChange: function (v) { setAttr({ columns: v }); },
                        min:      1,
                        max:      4,
                        step:     1,
                    }),
                    el(RangeControl, {
                        label:    'Espacement (px)',
                        value:    gap,
                        onChange: function (v) { setAttr({ gap: v }); },
                        min:      0,
                        max:      64,
                        step:     4,
                    })
                )
            );

            return el(Fragment, null,
                inspector,
                el('div', innerBlocksProps)
            );
        },

        save: function () {
            return el(wp.blockEditor.InnerBlocks.Content, null);
        },
    });
}());
