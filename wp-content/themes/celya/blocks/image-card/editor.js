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
    var SelectControl       = wp.components.SelectControl;
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

    var RATIO_OPTIONS = [
        { label: '4:3',        value: '4/3'  },
        { label: '16:9',       value: '16/9' },
        { label: '3:2',        value: '3/2'  },
        { label: '1:1 (Carré)', value: '1/1' },
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
            style:       { width: '40px', height: '40px', opacity: 0.35, color: '#59332A' },
        },
            el('path', {
                strokeLinecap:  'round',
                strokeLinejoin: 'round',
                d: 'M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z',
            })
        );
    }

    registerBlockType('celya/image-card', {

        edit: function (props) {
            var attrs       = props.attributes;
            var setAttr     = props.setAttributes;
            var imageId     = attrs.imageId;
            var imageUrl    = attrs.imageUrl;
            var imageAlt    = attrs.imageAlt;
            var imageRatio  = attrs.imageRatio;
            var cardBgColor = attrs.cardBgColor;

            var cardBgHex = colorBySlug(cardBgColor);

            /* ── Sidebar ── */
            var inspector = el(InspectorControls, null,

                el(PanelBody, { title: 'Image', initialOpen: true },
                    el('div', { style: { marginBottom: '16px' } },
                        el(MediaUploadCheck, null,
                            el(MediaUpload, {
                                onSelect:     function (media) {
                                    setAttr({ imageId: media.id, imageUrl: media.url, imageAlt: media.alt || '' });
                                },
                                allowedTypes: ['image'],
                                value:        imageId,
                                render:       function (ref) {
                                    if (imageUrl) {
                                        return el('div', null,
                                            el('img', {
                                                src:   imageUrl,
                                                alt:   imageAlt,
                                                style: {
                                                    display:      'block',
                                                    width:        '100%',
                                                    aspectRatio:  imageRatio,
                                                    objectFit:    'cover',
                                                    marginBottom: '10px',
                                                    borderRadius: '6px',
                                                },
                                            }),
                                            el('div', { style: { display: 'flex', gap: '8px' } },
                                                el(Button, { onClick: ref.open, variant: 'secondary', size: 'small' }, 'Changer'),
                                                el(Button, {
                                                    onClick:       function () { setAttr({ imageId: 0, imageUrl: '', imageAlt: '' }); },
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
                                    }, 'Choisir une image');
                                },
                            })
                        )
                    ),
                    el(SelectControl, {
                        label:    "Format de l'image",
                        value:    imageRatio,
                        options:  RATIO_OPTIONS,
                        onChange: function (v) { setAttr({ imageRatio: v }); },
                    })
                ),

                el(PanelBody, { title: 'Fond de la card', initialOpen: false },
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
                )
            );

            /* ── Zone image cliquable ── */
            var imageArea = el(MediaUploadCheck, null,
                el(MediaUpload, {
                    onSelect:     function (media) {
                        setAttr({ imageId: media.id, imageUrl: media.url, imageAlt: media.alt || '' });
                    },
                    allowedTypes: ['image'],
                    value:        imageId,
                    render:       function (ref) {
                        if (imageUrl) {
                            return el('div', {
                                onClick: ref.open,
                                title:   "Cliquer pour changer l'image",
                                style:   { cursor: 'pointer', lineHeight: 0 },
                            },
                                el('img', {
                                    src:   imageUrl,
                                    alt:   imageAlt,
                                    style: {
                                        width:       '100%',
                                        aspectRatio: imageRatio,
                                        objectFit:   'cover',
                                        display:     'block',
                                    },
                                })
                            );
                        }
                        return el('div', {
                            onClick: ref.open,
                            title:   'Cliquer pour ajouter une image',
                            style:   {
                                width:           '100%',
                                aspectRatio:     imageRatio,
                                backgroundColor: '#F6F6F6',
                                display:         'flex',
                                flexDirection:   'column',
                                alignItems:      'center',
                                justifyContent:  'center',
                                cursor:          'pointer',
                                gap:             '10px',
                            },
                        },
                            placeholderSvg(),
                            el('span', {
                                style: {
                                    fontSize:   '0.75rem',
                                    color:      '#59332A',
                                    opacity:    0.5,
                                    fontFamily: 'sans-serif',
                                },
                            }, 'Cliquer pour ajouter une image')
                        );
                    },
                })
            );

            /* ── Bloc principal ── */
            var blockProps = useBlockProps({
                className: 'celya-image-card has-' + cardBgColor + '-background-color',
                style:     { backgroundColor: cardBgHex },
            });

            var innerBlocksProps = useInnerBlocksProps(
                { className: 'celya-image-card__body' },
                {
                    template: [
                        ['core/heading',   { level: 3, placeholder: 'Titre de la card' }],
                        ['core/paragraph', { placeholder: 'Description…' }],
                    ],
                    templateLock: false,
                }
            );

            return el(Fragment, null,
                inspector,
                el('div', blockProps,
                    el('div', { className: 'celya-image-card__img-wrap' }, imageArea),
                    el('div', innerBlocksProps)
                )
            );
        },

        save: function () {
            return el(wp.blockEditor.InnerBlocks.Content, null);
        },
    });
}());
