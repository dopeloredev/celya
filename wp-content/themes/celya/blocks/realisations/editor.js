(function () {
    var el                 = wp.element.createElement;
    var Fragment           = wp.element.Fragment;
    var registerBlockType  = wp.blocks.registerBlockType;
    var InspectorControls  = wp.blockEditor.InspectorControls;
    var PanelColorSettings = wp.blockEditor.PanelColorSettings;
    var useBlockProps      = wp.blockEditor.useBlockProps;
    var PanelBody          = wp.components.PanelBody;
    var RangeControl       = wp.components.RangeControl;
    var ToggleControl      = wp.components.ToggleControl;
    var CheckboxControl    = wp.components.CheckboxControl;
    var SelectControl      = wp.components.SelectControl;
    var Spinner            = wp.components.Spinner;
    var ServerSideRender   = wp.serverSideRender;
    var useSelect          = wp.data.useSelect;

    registerBlockType('celya/realisations', {

        edit: function (props) {
            var attrs   = props.attributes;
            var setAttr = props.setAttributes;

            var occasions = useSelect(function (select) {
                return select('core').getEntityRecords('taxonomy', 'occasion', {
                    per_page: -1,
                    hide_empty: false,
                });
            }, []);

            var blockProps = useBlockProps();

            var toggleOccasion = function (slug, checked) {
                var current = attrs.occasions.slice();
                if (checked) {
                    if (current.indexOf(slug) === -1) current.push(slug);
                } else {
                    current = current.filter(function (s) { return s !== slug; });
                }
                setAttr({ occasions: current });
            };

            /* Occasions ------------------------------------------------- */
            var occasionControls;
            if (occasions === null) {
                occasionControls = el(Spinner, null);
            } else if (!occasions || occasions.length === 0) {
                occasionControls = el('p', { className: 'components-base-control__help' }, 'Aucune occasion créée.');
            } else {
                occasionControls = el(Fragment, null,
                    el('p', { className: 'components-base-control__help', style: { marginBottom: '8px' } },
                        'Aucune case cochée = toutes les occasions.'
                    ),
                    occasions.map(function (occ) {
                        return el(CheckboxControl, {
                            key:      occ.slug,
                            label:    occ.name,
                            checked:  attrs.occasions.indexOf(occ.slug) !== -1,
                            onChange: function (v) { toggleOccasion(occ.slug, v); },
                        });
                    })
                );
            }

            var inspector = el(InspectorControls, null,

                /* Filtres ------------------------------------------------ */
                el(PanelBody, { title: 'Filtres', initialOpen: true },
                    el(ToggleControl, {
                        label:    'Afficher les boutons de filtre',
                        checked:  attrs.showFilters,
                        onChange: function (v) { setAttr({ showFilters: v }); },
                    })
                ),

                /* Couleurs — filtres inactifs ---------------------------- */
                el(PanelColorSettings, {
                    title:        'Filtres inactifs',
                    initialOpen:  false,
                    colorSettings: [
                        {
                            value:    attrs.pillInactiveBorder,
                            onChange: function (v) { setAttr({ pillInactiveBorder: v || '' }); },
                            label:    'Contour',
                        },
                        {
                            value:    attrs.pillInactiveBg,
                            onChange: function (v) { setAttr({ pillInactiveBg: v || '' }); },
                            label:    'Fond',
                        },
                        {
                            value:    attrs.pillInactiveText,
                            onChange: function (v) { setAttr({ pillInactiveText: v || '' }); },
                            label:    'Texte',
                        },
                    ],
                }),

                /* Couleurs — filtres actifs ------------------------------ */
                el(PanelColorSettings, {
                    title:        'Filtres actifs',
                    initialOpen:  false,
                    colorSettings: [
                        {
                            value:    attrs.pillActiveBorder,
                            onChange: function (v) { setAttr({ pillActiveBorder: v || '' }); },
                            label:    'Contour',
                        },
                        {
                            value:    attrs.pillActiveBg,
                            onChange: function (v) { setAttr({ pillActiveBg: v || '' }); },
                            label:    'Fond',
                        },
                        {
                            value:    attrs.pillActiveText,
                            onChange: function (v) { setAttr({ pillActiveText: v || '' }); },
                            label:    'Texte',
                        },
                    ],
                }),

                /* Occasions à afficher ----------------------------------- */
                el(PanelBody, { title: 'Occasions à afficher', initialOpen: true },
                    occasionControls
                ),

                /* Format des images -------------------------------------- */
                el(PanelBody, { title: 'Format des images', initialOpen: false },
                    el(SelectControl, {
                        label:    'Ratio d\'affichage',
                        value:    attrs.imageRatio,
                        options:  [
                            { label: 'Carré (1:1)',    value: 'square' },
                            { label: 'Paysage (4:3)',  value: '4-3'    },
                            { label: 'Cinéma (16:9)',  value: '16-9'   },
                            { label: 'Portrait (3:4)', value: '3-4'    },
                        ],
                        onChange: function (v) { setAttr({ imageRatio: v }); },
                    }),
                    el(SelectControl, {
                        label:    'Taille du fichier image',
                        value:    attrs.imageSize,
                        options:  [
                            { label: 'Petit carré — 200×200',      value: 'celya-realisation-sm' },
                            { label: 'Moyen carré — 300×300',      value: 'celya-realisation-md' },
                            { label: 'Vignette carrée — 400×400',  value: 'celya-product-thumb'  },
                            { label: 'Grand carré — 800×800',      value: 'celya-product-large'  },
                            { label: 'Pleine taille',               value: 'full'                 },
                        ],
                        onChange: function (v) { setAttr({ imageSize: v }); },
                    })
                ),

                /* Affichage --------------------------------------------- */
                el(PanelBody, { title: 'Affichage', initialOpen: false },
                    el(RangeControl, {
                        label:    'Colonnes (desktop)',
                        value:    attrs.columns,
                        onChange: function (v) { setAttr({ columns: v }); },
                        min:      1,
                        max:      6,
                        step:     1,
                    }),
                    el(RangeControl, {
                        label:    'Nombre de réalisations (0 = toutes)',
                        value:    attrs.limit,
                        onChange: function (v) { setAttr({ limit: v }); },
                        min:      0,
                        max:      32,
                        step:     1,
                    })
                )
            );

            return el(Fragment, null,
                inspector,
                el('div', blockProps,
                    el(ServerSideRender, {
                        block:      'celya/realisations',
                        attributes: attrs,
                    })
                )
            );
        },

        save: function () {
            return null;
        },
    });
}());
