(function () {
    var el                  = wp.element.createElement;
    var Fragment            = wp.element.Fragment;
    var registerBlockType   = wp.blocks.registerBlockType;
    var InspectorControls   = wp.blockEditor.InspectorControls;
    var useBlockProps       = wp.blockEditor.useBlockProps;
    var useInnerBlocksProps = wp.blockEditor.useInnerBlocksProps;
    var InnerBlocks         = wp.blockEditor.InnerBlocks;
    var PanelBody           = wp.components.PanelBody;
    var ToggleControl       = wp.components.ToggleControl;
    var ColorPalette        = wp.components.ColorPalette;

    var THEME_COLORS = [
        { name: 'Marron artisanal',  slug: 'celya-primary',      color: '#59332A' },
        { name: 'Beige biscuit',     slug: 'celya-secondary',    color: '#F2D0A7' },
        { name: 'Blanc crème',       slug: 'celya-light',        color: '#FAF9F8' },
        { name: 'Blanc pur',         slug: 'celya-white',        color: '#ffffff' },
        { name: 'Texte sombre',      slug: 'celya-dark',         color: '#2C2C2C' },
        { name: 'Orange foncé',      slug: 'celya-orange-dark',  color: '#F2B28D' },
        { name: 'Orange clair',      slug: 'celya-orange-light', color: '#FDECE2' },
        { name: 'Bleu foncé',        slug: 'celya-blue-dark',    color: '#BDD9F2' },
        { name: 'Bleu clair',        slug: 'celya-blue-light',   color: '#F2F7FC' },
        { name: 'Vert foncé',        slug: 'celya-green-dark',   color: '#ABE0A4' },
        { name: 'Vert clair',        slug: 'celya-green-light',  color: '#E9F6E8' },
        { name: 'Jaune foncé',       slug: 'celya-yellow-dark',  color: '#F2D479' },
        { name: 'Jaune clair',       slug: 'celya-yellow-light', color: '#FCF5DD' },
        { name: 'Rose foncé',        slug: 'celya-pink-dark',    color: '#EDA2C1' },
        { name: 'Rose clair',        slug: 'celya-pink-light',   color: '#F9E8EE' },
        { name: 'Gris clair',        slug: 'celya-grey-light',   color: '#F6F6F6' },
    ];

    function colorBySlug(slug) {
        var match = THEME_COLORS.filter(function (c) { return c.slug === slug; });
        return match.length ? match[0].color : '#F2B28D';
    }

    function slugByColor(color) {
        var match = THEME_COLORS.filter(function (c) { return c.color === color; });
        return match.length ? match[0].slug : null;
    }

    registerBlockType('celya/steps-cards', {

        edit: function (props) {
            var attrs          = props.attributes;
            var setAttr        = props.setAttributes;
            var showNumbers    = attrs.showNumbers;
            var showConnector  = attrs.showConnector;
            var connectorColor = attrs.connectorColor;

            var connectorHex = colorBySlug(connectorColor);

            var wrapperClass = 'celya-steps-cards'
                + (showConnector ? ' celya-steps-cards--has-connector' : ' celya-steps-cards--no-connector')
                + (showNumbers   ? ' celya-steps-cards--numbered'      : '');

            var blockProps = useBlockProps({
                className: wrapperClass,
                style: {
                    '--celya-connector-color': connectorHex,
                },
            });

            var innerBlocksProps = useInnerBlocksProps(blockProps, {
                allowedBlocks: ['celya/steps-card'],
                template: [
                    ['celya/steps-card', {}],
                    ['celya/steps-card', {}],
                    ['celya/steps-card', {}],
                ],
                orientation: 'horizontal',
                renderAppender: InnerBlocks.ButtonBlockAppender,
            });

            var inspector = el(InspectorControls, null,

                el(PanelBody, { title: 'Numérotation', initialOpen: true },
                    el(ToggleControl, {
                        label:    'Afficher les numéros de steps',
                        checked:  showNumbers,
                        onChange: function (v) { setAttr({ showNumbers: v }); },
                    })
                ),

                el(PanelBody, { title: 'Connecteur', initialOpen: true },
                    el(ToggleControl, {
                        label:    'Afficher la ligne de connexion',
                        checked:  showConnector,
                        onChange: function (v) { setAttr({ showConnector: v }); },
                    }),
                    showConnector
                        ? el('div', { className: 'components-base-control', style: { marginTop: '12px' } },
                            el('label', {
                                className: 'components-base-control__label',
                                style:     { display: 'block', marginBottom: '8px', fontWeight: '500' },
                            }, 'Couleur du connecteur'),
                            el(ColorPalette, {
                                colors:              THEME_COLORS,
                                value:               connectorHex,
                                onChange:            function (color) {
                                    if (!color) return;
                                    var slug = slugByColor(color);
                                    if (slug) setAttr({ connectorColor: slug });
                                },
                                disableCustomColors: true,
                                clearable:           false,
                            })
                        )
                        : null
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
