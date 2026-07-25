( function () {
    var el                = wp.element.createElement;
    var registerBlockType = wp.blocks.registerBlockType;
    var InspectorControls = wp.blockEditor.InspectorControls;
    var useBlockProps      = wp.blockEditor.useBlockProps;
    var PanelBody          = wp.components.PanelBody;
    var TextControl        = wp.components.TextControl;
    var ServerSideRender   = wp.serverSideRender;

    registerBlockType( 'celya/devis-form', {
        edit: function ( props ) {
            var attributes = props.attributes;
            var blockProps = useBlockProps();

            return el(
                'div',
                blockProps,
                el(
                    InspectorControls,
                    {},
                    el(
                        PanelBody,
                        { title: 'Réglages du formulaire', initialOpen: true },
                        el( TextControl, {
                            label: 'Titre du formulaire',
                            value: attributes.title,
                            onChange: function ( value ) {
                                props.setAttributes( { title: value } );
                            }
                        } )
                    )
                ),
                el( ServerSideRender, {
                    block: 'celya/devis-form',
                    attributes: attributes
                } )
            );
        },
        save: function () {
            // Rendu côté serveur (render.php).
            return null;
        }
    } );
} )();
