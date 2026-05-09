(function () {
    var el                = wp.element.createElement;
    var Fragment          = wp.element.Fragment;
    var registerBlockType = wp.blocks.registerBlockType;
    var InspectorControls = wp.blockEditor.InspectorControls;
    var useBlockProps     = wp.blockEditor.useBlockProps;
    var RichText          = wp.blockEditor.RichText;
    var MediaUpload       = wp.blockEditor.MediaUpload;
    var MediaUploadCheck  = wp.blockEditor.MediaUploadCheck;
    var PanelBody         = wp.components.PanelBody;
    var RangeControl      = wp.components.RangeControl;
    var Button            = wp.components.Button;

    function nextId(items) {
        return items.reduce(function (max, item) {
            return Math.max(max, item.id || 0);
        }, 0) + 1;
    }

    function BulletMarker(iconUrl, iconAlt, iconSize) {
        if (iconUrl) {
            return el('img', {
                src:   iconUrl,
                alt:   iconAlt,
                style: {
                    width:      iconSize + 'px',
                    height:     iconSize + 'px',
                    objectFit:  'contain',
                    flexShrink: '0',
                    display:    'block',
                },
            });
        }
        return el('span', {
            className: 'celya-bullet-list__dot',
            'aria-hidden': 'true',
        }, '•');
    }

    registerBlockType('celya/bullet-list', {

        edit: function (props) {
            var attrs    = props.attributes;
            var setAttr  = props.setAttributes;
            var items    = attrs.items;
            var iconUrl  = attrs.iconUrl;
            var iconId   = attrs.iconId;
            var iconAlt  = attrs.iconAlt;
            var iconSize = attrs.iconSize;
            var gap      = attrs.gap;

            function updateItem(index, text) {
                var next = items.map(function (item, i) {
                    return i === index ? { id: item.id, text: text } : item;
                });
                setAttr({ items: next });
            }

            function addItemAfter(index) {
                var next = items.slice();
                next.splice(index + 1, 0, { id: nextId(next), text: '' });
                setAttr({ items: next });
            }

            function removeItem(index) {
                if (items.length <= 1) return;
                setAttr({ items: items.filter(function (_, i) { return i !== index; }) });
            }

            /* ── Sidebar ── */
            var inspectorControls = el(InspectorControls, null,

                el(PanelBody, { title: 'Icône de puce', initialOpen: true },
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
                                                maxHeight:    '60px',
                                                objectFit:    'contain',
                                                marginBottom: '10px',
                                                borderRadius: '4px',
                                                background:   '#f0f0f0',
                                                padding:      '6px',
                                            },
                                        }),
                                        el('div', { style: { display: 'flex', gap: '8px' } },
                                            el(Button, {
                                                onClick:  ref.open,
                                                variant:  'secondary',
                                                size:     'small',
                                            }, 'Changer'),
                                            el(Button, {
                                                onClick: function () {
                                                    setAttr({ iconId: 0, iconUrl: '', iconAlt: '' });
                                                },
                                                variant:       'link',
                                                isDestructive: true,
                                                size:          'small',
                                            }, 'Supprimer')
                                        )
                                    );
                                }
                                return el('div', null,
                                    el('p', {
                                        style: { fontSize: '12px', color: '#757575', marginBottom: '8px' },
                                    }, 'Sans icône, un point classique (•) sera affiché.'),
                                    el(Button, {
                                        onClick: ref.open,
                                        variant: 'primary',
                                        style:   { width: '100%', justifyContent: 'center' },
                                    }, 'Choisir une icône')
                                );
                            },
                        })
                    ),
                    iconUrl
                        ? el(RangeControl, {
                            label:    "Taille de l'icône (px)",
                            value:    iconSize,
                            onChange: function (v) { setAttr({ iconSize: v }); },
                            min:      10,
                            max:      64,
                            step:     2,
                        })
                        : null
                ),

                el(PanelBody, { title: 'Espacement', initialOpen: true },
                    el(RangeControl, {
                        label:    'Espace entre les puces (px)',
                        value:    gap,
                        onChange: function (v) { setAttr({ gap: v }); },
                        min:      0,
                        max:      48,
                        step:     2,
                    })
                )
            );

            /* ── Canvas ── */
            var listItems = items.map(function (item, index) {
                return el('li', {
                    key:   item.id,
                    className: 'celya-bullet-list__item celya-bullet-list__item--editor',
                },
                    el('span', { className: 'celya-bullet-list__marker' },
                        BulletMarker(iconUrl, iconAlt, iconSize)
                    ),
                    el(RichText, {
                        tagName:        'span',
                        className:      'celya-bullet-list__text',
                        value:          item.text,
                        onChange:       function (val) { updateItem(index, val); },
                        placeholder:    'Saisir le texte…',
                        allowedFormats: ['core/bold', 'core/italic', 'core/link'],
                        onSplit:        function (before, after) {
                            var beforeItem = { id: item.id, text: before };
                            var afterItem  = { id: nextId(items), text: after };
                            var next = items.slice();
                            next.splice(index, 1, beforeItem, afterItem);
                            setAttr({ items: next });
                        },
                        onMerge: function (forward) {
                            if (forward) {
                                if (index >= items.length - 1) return;
                                var merged = { id: item.id, text: item.text + items[index + 1].text };
                                var next   = items.filter(function (_, i) { return i !== index + 1; });
                                next[index] = merged;
                                setAttr({ items: next });
                            } else {
                                if (index === 0) return;
                                var merged = { id: items[index - 1].id, text: items[index - 1].text + item.text };
                                var next   = items.filter(function (_, i) { return i !== index; });
                                next[index - 1] = merged;
                                setAttr({ items: next });
                            }
                        },
                        onRemove: function () { removeItem(index); },
                    }),
                    items.length > 1
                        ? el(Button, {
                            onClick:       function () { removeItem(index); },
                            variant:       'tertiary',
                            isDestructive: true,
                            size:          'small',
                            label:         'Supprimer cet élément',
                            className:     'celya-bullet-list__delete-btn',
                        }, '×')
                        : null
                );
            });

            var blockProps = useBlockProps({
                className: 'celya-bullet-list celya-bullet-list--editor',
                style:     { gap: gap + 'px' },
            });

            var addRow = el('li', {
                key:       '__add__',
                className: 'celya-bullet-list__item celya-bullet-list__add-item',
            },
                el('span', {
                    className:     'celya-bullet-list__marker',
                    'aria-hidden': 'true',
                },
                    BulletMarker(iconUrl, iconAlt, iconSize)
                ),
                el('button', {
                    onClick:   function () { addItemAfter(items.length - 1); },
                    className: 'celya-bullet-list__add-btn',
                    type:      'button',
                }, '+ Ajouter un élément')
            );

            var preview = el('ul', blockProps, listItems.concat([addRow]));

            return el(Fragment, null, inspectorControls, preview);
        },

        save: function () {
            return null;
        },
    });
}());
