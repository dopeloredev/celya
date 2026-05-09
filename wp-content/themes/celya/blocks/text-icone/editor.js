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
    var Button              = wp.components.Button;
    var ColorPalette        = wp.components.ColorPalette;

    var THEME_COLORS = [
        { name: 'Beige biscuit',    slug: 'celya-secondary',    color: '#F2D0A7' },
        { name: 'Blanc crème',      slug: 'celya-light',        color: '#FAF9F8' },
        { name: 'Gris clair',       slug: 'celya-grey-light',   color: '#F6F6F6' },
        { name: 'Orange clair',     slug: 'celya-orange-light', color: '#FDECE2' },
        { name: 'Orange foncé',     slug: 'celya-orange-dark',  color: '#F2B28D' },
        { name: 'Bleu clair',       slug: 'celya-blue-light',   color: '#F2F7FC' },
        { name: 'Bleu foncé',       slug: 'celya-blue-dark',    color: '#BDD9F2' },
        { name: 'Vert clair',       slug: 'celya-green-light',  color: '#E9F6E8' },
        { name: 'Vert foncé',       slug: 'celya-green-dark',   color: '#ABE0A4' },
        { name: 'Jaune clair',      slug: 'celya-yellow-light', color: '#FCF5DD' },
        { name: 'Jaune foncé',      slug: 'celya-yellow-dark',  color: '#F2D479' },
        { name: 'Rose clair',       slug: 'celya-pink-light',   color: '#F9E8EE' },
        { name: 'Rose foncé',       slug: 'celya-pink-dark',    color: '#EDA2C1' },
        { name: 'Marron artisanal', slug: 'celya-primary',      color: '#59332A' },
        { name: 'Texte sombre',     slug: 'celya-dark',         color: '#2C2C2C' },
    ];

    var VERTICAL_ALIGN_OPTIONS = [
        { label: 'Haut',   value: 'flex-start' },
        { label: 'Centre', value: 'center'     },
        { label: 'Bas',    value: 'flex-end'   },
    ];

    var BORDER_RADIUS_OPTIONS = [
        { label: 'Aucun',             value: 'none'   },
        { label: 'Petit — 8px',       value: 'small'  },
        { label: 'Moyen — 16px',      value: 'medium' },
        { label: 'Grand — 24px',      value: 'large'  },
        { label: 'Très grand — 32px', value: 'xl'     },
        { label: 'Circulaire — 50%',  value: 'full'   },
    ];

    var BORDER_RADIUS_CSS = {
        none:   '0px',
        small:  '8px',
        medium: '16px',
        large:  '24px',
        xl:     '32px',
        full:   '50%',
    };

    function colorBySlug(slug) {
        var match = THEME_COLORS.filter(function (c) { return c.slug === slug; });
        return match.length ? match[0].color : '#F2D0A7';
    }

    function slugByColor(color) {
        var match = THEME_COLORS.filter(function (c) { return c.color === color; });
        return match.length ? match[0].slug : null;
    }

    function placeholderSvg(size) {
        var svgSize = Math.round(size * 0.45);
        return el('svg', {
            xmlns:       'http://www.w3.org/2000/svg',
            viewBox:     '0 0 24 24',
            fill:        'none',
            stroke:      'currentColor',
            strokeWidth: '1.5',
            style:       { width: svgSize + 'px', height: svgSize + 'px', opacity: 0.45, color: '#59332A' },
        },
            el('path', {
                strokeLinecap:  'round',
                strokeLinejoin: 'round',
                d: 'M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z',
            })
        );
    }

    registerBlockType('celya/text-icone', {

        edit: function (props) {
            var attrs         = props.attributes;
            var setAttr       = props.setAttributes;
            var mediaId       = attrs.mediaId;
            var mediaUrl      = attrs.mediaUrl;
            var mediaAlt      = attrs.mediaAlt;
            var bgSlug        = attrs.backgroundColor;
            var radius        = attrs.borderRadius;
            var iconSize      = attrs.iconSize;
            var verticalAlign = attrs.verticalAlign;

            var bgColor    = colorBySlug(bgSlug);
            var radiusCss  = BORDER_RADIUS_CSS[radius] || '0px';
            var imgSize    = Math.round(iconSize * 0.6);

            var squareStyle = {
                width:           iconSize + 'px',
                height:          iconSize + 'px',
                minWidth:        iconSize + 'px',
                backgroundColor: bgColor,
                borderRadius:    radiusCss,
                display:         'flex',
                alignItems:      'center',
                justifyContent:  'center',
                overflow:        'hidden',
                flexShrink:      '0',
                cursor:          'pointer',
            };

            /* ── Sidebar ── */
            var inspector = el(InspectorControls, null,

                el(PanelBody, { title: 'Mise en page', initialOpen: true },
                    el(SelectControl, {
                        label:    'Alignement vertical',
                        value:    verticalAlign,
                        options:  VERTICAL_ALIGN_OPTIONS,
                        onChange: function (v) { setAttr({ verticalAlign: v }); },
                    })
                ),

                el(PanelBody, { title: 'Icône', initialOpen: false },
                    el(MediaUploadCheck, null,
                        el(MediaUpload, {
                            onSelect:     function (media) {
                                setAttr({ mediaId: media.id, mediaUrl: media.url, mediaAlt: media.alt || '' });
                            },
                            allowedTypes: ['image'],
                            value:        mediaId,
                            render:       function (ref) {
                                if (mediaUrl) {
                                    return el('div', null,
                                        el('img', {
                                            src:   mediaUrl,
                                            alt:   mediaAlt,
                                            style: {
                                                display:      'block',
                                                maxWidth:     '100%',
                                                maxHeight:    '80px',
                                                objectFit:    'contain',
                                                marginBottom: '10px',
                                                borderRadius: '4px',
                                                background:   '#f0f0f0',
                                            },
                                        }),
                                        el('div', { style: { display: 'flex', gap: '8px' } },
                                            el(Button, { onClick: ref.open, variant: 'secondary', size: 'small' }, 'Changer'),
                                            el(Button, {
                                                onClick:       function () { setAttr({ mediaId: 0, mediaUrl: '', mediaAlt: '' }); },
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

                el(PanelBody, { title: 'Fond de l\'icône', initialOpen: false },
                    el('div', { className: 'components-base-control', style: { marginBottom: '16px' } },
                        el('label', {
                            className: 'components-base-control__label',
                            style:     { display: 'block', marginBottom: '8px', fontWeight: '500' },
                        }, 'Couleur de fond'),
                        el(ColorPalette, {
                            colors:              THEME_COLORS,
                            value:               bgColor,
                            onChange:            function (color) {
                                if (!color) return;
                                var slug = slugByColor(color);
                                if (slug) setAttr({ backgroundColor: slug });
                            },
                            disableCustomColors: true,
                            clearable:           false,
                        })
                    ),
                    el(SelectControl, {
                        label:    'Arrondi des angles',
                        value:    radius,
                        options:  BORDER_RADIUS_OPTIONS,
                        onChange: function (v) { setAttr({ borderRadius: v }); },
                    }),
                    el(RangeControl, {
                        label:    'Taille du carré (px)',
                        value:    iconSize,
                        onChange: function (v) { setAttr({ iconSize: v }); },
                        min:      40,
                        max:      200,
                        step:     4,
                    })
                )
            );

            /* ── Zone icône cliquable ── */
            var iconArea = el(MediaUploadCheck, null,
                el(MediaUpload, {
                    onSelect:     function (media) {
                        setAttr({ mediaId: media.id, mediaUrl: media.url, mediaAlt: media.alt || '' });
                    },
                    allowedTypes: ['image'],
                    value:        mediaId,
                    render:       function (ref) {
                        return el('div', {
                            onClick: ref.open,
                            title:   mediaUrl ? "Cliquer pour changer l'icône" : 'Cliquer pour ajouter une icône',
                            style:   squareStyle,
                        },
                            mediaUrl
                                ? el('img', {
                                    src:   mediaUrl,
                                    alt:   mediaAlt,
                                    style: { width: imgSize + 'px', height: imgSize + 'px', objectFit: 'contain', display: 'block' },
                                  })
                                : placeholderSvg(iconSize)
                        );
                    },
                })
            );

            /* ── Bloc principal ── */
            var blockProps = useBlockProps({
                className: 'celya-text-icone',
                style: { alignItems: verticalAlign },
            });

            var innerBlocksProps = useInnerBlocksProps(
                { className: 'celya-text-icone__content' },
                {
                    template: [
                        ['core/heading',   { level: 3, placeholder: 'Titre…' }],
                        ['core/paragraph', { placeholder: 'Description…' }],
                    ],
                    templateLock: false,
                }
            );

            return el(Fragment, null,
                inspector,
                el('div', blockProps,
                    el('div', { className: 'celya-text-icone__icon-wrap' }, iconArea),
                    el('div', innerBlocksProps)
                )
            );
        },

        save: function () {
            return el(wp.blockEditor.InnerBlocks.Content, null);
        },
    });
}());
