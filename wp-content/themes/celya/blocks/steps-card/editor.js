(function () {
    var el                  = wp.element.createElement;
    var Fragment            = wp.element.Fragment;
    var registerBlockType   = wp.blocks.registerBlockType;
    var InspectorControls   = wp.blockEditor.InspectorControls;
    var useBlockProps       = wp.blockEditor.useBlockProps;
    var useInnerBlocksProps = wp.blockEditor.useInnerBlocksProps;
    var MediaUpload         = wp.blockEditor.MediaUpload;
    var MediaUploadCheck    = wp.blockEditor.MediaUploadCheck;
    var PanelBody           = wp.components.PanelBody;
    var RangeControl        = wp.components.RangeControl;
    var SelectControl       = wp.components.SelectControl;
    var ToggleControl       = wp.components.ToggleControl;
    var Button              = wp.components.Button;
    var ColorPalette        = wp.components.ColorPalette;

    var THEME_COLORS = [
        { name: 'Blanc pur',         slug: 'celya-white',        color: '#ffffff' },
        { name: 'Blanc crème',       slug: 'celya-light',        color: '#FAF9F8' },
        { name: 'Gris clair',        slug: 'celya-grey-light',   color: '#F6F6F6' },
        { name: 'Orange clair',      slug: 'celya-orange-light', color: '#FDECE2' },
        { name: 'Beige biscuit',     slug: 'celya-secondary',    color: '#F2D0A7' },
        { name: 'Bleu clair',        slug: 'celya-blue-light',   color: '#F2F7FC' },
        { name: 'Vert clair',        slug: 'celya-green-light',  color: '#E9F6E8' },
        { name: 'Jaune clair',       slug: 'celya-yellow-light', color: '#FCF5DD' },
        { name: 'Rose clair',        slug: 'celya-pink-light',   color: '#F9E8EE' },
        { name: 'Orange foncé',      slug: 'celya-orange-dark',  color: '#F2B28D' },
        { name: 'Bleu foncé',        slug: 'celya-blue-dark',    color: '#BDD9F2' },
        { name: 'Vert foncé',        slug: 'celya-green-dark',   color: '#ABE0A4' },
        { name: 'Jaune foncé',       slug: 'celya-yellow-dark',  color: '#F2D479' },
        { name: 'Rose foncé',        slug: 'celya-pink-dark',    color: '#EDA2C1' },
        { name: 'Marron artisanal',  slug: 'celya-primary',      color: '#59332A' },
        { name: 'Texte sombre',      slug: 'celya-dark',         color: '#2C2C2C' },
    ];

    var ICON_SHAPE_OPTIONS = [
        { label: 'Cercle',             value: 'circle'  },
        { label: 'Rectangle arrondi',  value: 'rounded' },
    ];

    function colorBySlug(slug) {
        var match = THEME_COLORS.filter(function (c) { return c.slug === slug; });
        return match.length ? match[0].color : '#ffffff';
    }

    function slugByColor(color) {
        var match = THEME_COLORS.filter(function (c) { return c.color === color; });
        return match.length ? match[0].slug : null;
    }

    function placeholderSvg() {
        return el('svg', {
            xmlns:       'http://www.w3.org/2000/svg',
            viewBox:     '0 0 24 24',
            fill:        'none',
            stroke:      'currentColor',
            strokeWidth: '1.5',
            style:       { width: '45%', height: '45%', opacity: '0.4', color: '#59332A' },
        },
            el('path', {
                strokeLinecap:  'round',
                strokeLinejoin: 'round',
                d: 'M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z',
            })
        );
    }

    registerBlockType('celya/steps-card', {

        edit: function (props) {
            var attrs        = props.attributes;
            var setAttr      = props.setAttributes;
            var iconId       = attrs.iconId;
            var iconUrl      = attrs.iconUrl;
            var iconAlt      = attrs.iconAlt;
            var showIcon     = attrs.showIcon;
            var cardBgColor  = attrs.cardBgColor;
            var iconBgColor  = attrs.iconBgColor;
            var iconShape    = attrs.iconShape;
            var iconRadius   = attrs.iconRadius;
            var iconSize     = attrs.iconSize;
            var showBorder   = attrs.showBorder;
            var borderColor  = attrs.borderColor;

            var cardBgHex      = colorBySlug(cardBgColor);
            var iconBgHex      = colorBySlug(iconBgColor);
            var borderColorHex = colorBySlug(borderColor);
            var iconPx         = iconSize + 'px';
            var borderRadius   = iconShape === 'circle' ? '50%' : (iconRadius + 'px');

            /* ── Sidebar ── */
            var inspector = el(InspectorControls, null,

                el(PanelBody, { title: 'Bordure de la card', initialOpen: false },
                    el(ToggleControl, {
                        label:    'Afficher la bordure',
                        checked:  showBorder,
                        onChange: function (v) { setAttr({ showBorder: v }); },
                    }),
                    showBorder
                        ? el('div', { className: 'components-base-control', style: { marginTop: '12px' } },
                            el('label', {
                                className: 'components-base-control__label',
                                style:     { display: 'block', marginBottom: '8px', fontWeight: '500' },
                            }, 'Couleur de la bordure'),
                            el(ColorPalette, {
                                colors:              THEME_COLORS,
                                value:               borderColorHex,
                                onChange:            function (color) {
                                    if (!color) return;
                                    var slug = slugByColor(color);
                                    if (slug) setAttr({ borderColor: slug });
                                },
                                disableCustomColors: true,
                                clearable:           false,
                            })
                          )
                        : null
                ),

                el(PanelBody, { title: 'Fond de la card', initialOpen: true },
                    el('div', { className: 'components-base-control' },
                        el('label', {
                            className: 'components-base-control__label',
                            style:     { display: 'block', marginBottom: '8px', fontWeight: '500' },
                        }, 'Couleur de fond'),
                        el(ColorPalette, {
                            colors:              THEME_COLORS,
                            value:               cardBgHex,
                            onChange:            function (color) {
                                if (!color) return;
                                var slug = slugByColor(color);
                                if (slug) setAttr({ cardBgColor: slug });
                            },
                            disableCustomColors: true,
                            clearable:           false,
                        })
                    )
                ),

                el(PanelBody, { title: 'Icône', initialOpen: false },

                    /* ── Afficher / masquer l'icône ── */
                    el(ToggleControl, {
                        label:    "Afficher l'icône",
                        checked:  showIcon,
                        onChange: function (v) { setAttr({ showIcon: v }); },
                    }),

                    /* ── Options visibles uniquement si l'icône est affichée ── */
                    showIcon
                        ? el(Fragment, null,

                            /* Image */
                            el('div', { style: { marginTop: '12px', marginBottom: '16px' } },
                                el(MediaUploadCheck, null,
                                    el(MediaUpload, {
                                        onSelect: function (media) {
                                            setAttr({ iconId: media.id, iconUrl: media.url, iconAlt: media.alt || '' });
                                        },
                                        allowedTypes: ['image'],
                                        value: iconId,
                                        render: function (ref) {
                                            if (iconUrl) {
                                                return el('div', null,
                                                    el('img', {
                                                        src:   iconUrl,
                                                        alt:   iconAlt,
                                                        style: {
                                                            display:      'block',
                                                            maxWidth:     '100%',
                                                            maxHeight:    '64px',
                                                            objectFit:    'contain',
                                                            marginBottom: '10px',
                                                            borderRadius: '4px',
                                                            background:   '#f0f0f0',
                                                            padding:      '4px',
                                                        },
                                                    }),
                                                    el('div', { style: { display: 'flex', gap: '8px' } },
                                                        el(Button, { onClick: ref.open, variant: 'secondary', size: 'small' }, 'Changer'),
                                                        el(Button, {
                                                            onClick:       function () { setAttr({ iconId: 0, iconUrl: '', iconAlt: '' }); },
                                                            variant:       'link',
                                                            isDestructive: true,
                                                            size:          'small',
                                                        }, 'Supprimer')
                                                    )
                                                );
                                            }
                                            return el(Button, {
                                                onClick: ref.open,
                                                variant: 'primary',
                                                style:   { width: '100%', justifyContent: 'center' },
                                            }, 'Choisir une icône');
                                        },
                                    })
                                )
                            ),

                            /* Couleur de fond */
                            el('div', { className: 'components-base-control', style: { marginBottom: '16px' } },
                                el('label', {
                                    className: 'components-base-control__label',
                                    style:     { display: 'block', marginBottom: '8px', fontWeight: '500' },
                                }, 'Couleur de fond du bloc icône'),
                                el(ColorPalette, {
                                    colors:              THEME_COLORS,
                                    value:               iconBgHex,
                                    onChange:            function (color) {
                                        if (!color) return;
                                        var slug = slugByColor(color);
                                        if (slug) setAttr({ iconBgColor: slug });
                                    },
                                    disableCustomColors: true,
                                    clearable:           false,
                                })
                            ),

                            /* Forme */
                            el(SelectControl, {
                                label:    'Forme du fond',
                                value:    iconShape,
                                options:  ICON_SHAPE_OPTIONS,
                                onChange: function (v) { setAttr({ iconShape: v }); },
                            }),

                            /* Rayon d'arrondi — uniquement si rectangle arrondi */
                            iconShape === 'rounded'
                                ? el(RangeControl, {
                                    label:    "Arrondi des angles (px)",
                                    value:    iconRadius,
                                    onChange: function (v) { setAttr({ iconRadius: v }); },
                                    min:      0,
                                    max:      64,
                                    step:     2,
                                })
                                : null,

                            /* Taille */
                            el(RangeControl, {
                                label:    'Taille du bloc icône (px)',
                                value:    iconSize,
                                onChange: function (v) { setAttr({ iconSize: v }); },
                                min:      32,
                                max:      120,
                                step:     4,
                            })
                        )
                        : null
                )
            );

            /* ── Icône canvas (cliquable si showIcon = true) ── */
            var iconArea = showIcon
                ? el(MediaUploadCheck, null,
                    el(MediaUpload, {
                        onSelect: function (media) {
                            setAttr({ iconId: media.id, iconUrl: media.url, iconAlt: media.alt || '' });
                        },
                        allowedTypes: ['image'],
                        value: iconId,
                        render: function (ref) {
                            var iconContent = iconUrl
                                ? el('img', {
                                    src:   iconUrl,
                                    alt:   iconAlt,
                                    style: { width: '55%', height: '55%', objectFit: 'contain', display: 'block' },
                                  })
                                : placeholderSvg();

                            return el('div', {
                                onClick: ref.open,
                                title:   'Cliquer pour choisir une icône',
                                style: {
                                    width:           iconPx,
                                    height:          iconPx,
                                    borderRadius:    borderRadius,
                                    backgroundColor: iconBgHex,
                                    display:         'flex',
                                    alignItems:      'center',
                                    justifyContent:  'center',
                                    cursor:          'pointer',
                                    flexShrink:      '0',
                                    alignSelf:       'flex-start',
                                },
                            }, iconContent);
                        },
                    })
                  )
                : null;

            /* ── Bloc principal ── */
            var blockProps = useBlockProps({
                className: 'celya-steps-card',
                style: {
                    backgroundColor: cardBgHex,
                    border: showBorder ? ('2px solid ' + borderColorHex) : undefined,
                },
            });

            var innerBlocksProps = useInnerBlocksProps(
                { className: 'celya-steps-card__body' },
                {
                    template: [
                        ['core/heading',   { level: 3, placeholder: "Titre de l'étape" }],
                        ['core/paragraph', { placeholder: "Description de l'étape…" }],
                    ],
                    templateLock: false,
                }
            );

            return el(Fragment, null,
                inspector,
                el('div', blockProps,
                    iconArea,
                    el('div', innerBlocksProps)
                )
            );
        },

        save: function () {
            return el(wp.blockEditor.InnerBlocks.Content, null);
        },
    });
}());
