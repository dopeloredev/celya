(function ($) {
    'use strict';

    // Déplace chaque bloc galerie juste avant p.options (Enabled/Downloadable/Virtual)
    // Il n'existe pas de hook PHP à cet endroit précis — on repositionne en JS.
    function repositionGalleryFields() {
        $('.celya-var-gallery').each(function () {
            var $gallery = $(this);
            var $panel   = $gallery.closest('.woocommerce_variable_attributes');
            var $options = $panel.find('p.options').first();
            if ($options.length) {
                $options.before($gallery);
            }
        });
    }

    $(document).ready(function () {
        repositionGalleryFields();

        // Après chargement AJAX des variations (ex. "Load variations")
        $(document).on('woocommerce_variations_loaded woocommerce_variations_added', repositionGalleryFields);

        // Délégation sur document — les panneaux de variation sont chargés via AJAX par WooCommerce
        $(document).on('click', '.celya-var-gallery__add', function () {
            var $btn     = $(this);
            var $gallery = $btn.closest('.celya-var-gallery');
            var $thumbs  = $gallery.find('.celya-var-gallery__thumbs');
            var loop     = $gallery.data('loop');

            if (typeof wp === 'undefined' || !wp.media) {
                return;
            }

            var frame = wp.media({
                title   : 'Sélectionner les images de la galerie',
                button  : { text: 'Ajouter à la galerie' },
                multiple: true,
            });

            frame.on('select', function () {
                frame.state().get('selection').each(function (attachment) {
                    var a        = attachment.toJSON();
                    var thumbUrl = (a.sizes && a.sizes.thumbnail)
                        ? a.sizes.thumbnail.url
                        : a.url;

                    var $item = $('<div class="celya-var-gallery__item"></div>');
                    $item.append('<img src="' + thumbUrl + '" alt="">');
                    $item.append('<button type="button" class="celya-var-gallery__remove" title="Supprimer">&times;</button>');
                    $item.append('<input type="hidden" name="celya_var_gallery_ids[' + loop + '][]" value="' + a.id + '">');
                    $thumbs.append($item);
                });

                // Notifier WooCommerce qu'une modification a eu lieu → active "Save changes"
                $gallery.find('input[name^="celya_var_gallery_nonce"]').trigger('change');
            });

            frame.open();
        });

        $(document).on('click', '.celya-var-gallery__remove', function () {
            var $gallery = $(this).closest('.celya-var-gallery');
            $(this).closest('.celya-var-gallery__item').remove();
            // Notifier WooCommerce qu'une modification a eu lieu → active "Save changes"
            $gallery.find('input[name^="celya_var_gallery_nonce"]').trigger('change');
        });
    });
}(jQuery));
