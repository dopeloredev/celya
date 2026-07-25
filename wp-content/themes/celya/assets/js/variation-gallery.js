(function ($) {
    'use strict';

    $(document).ready(function () {
        var $form = $('form.variations_form');
        if (!$form.length) return;

        if (typeof celyaVarGallery === 'undefined') return;

        var $mainImage      = $('#celya-main-image');
        var $thumbsContainer = $('.celya-thumb').first().parent();

        // Snapshot de la galerie d'origine pour restauration
        var originalThumbsHtml = $thumbsContainer.length ? $thumbsContainer.html() : null;
        var originalMainSrc    = $mainImage.length ? ($mainImage.attr('src') || '') : '';
        var originalSrcset     = $mainImage.length ? ($mainImage.attr('srcset') || '') : '';

        function buildThumbHtml(img, isFirst) {
            var cls = isFirst
                ? 'celya-thumb aspect-square rounded-lg overflow-hidden cursor-pointer border-2 active-thumb border-celya-orange_dark opacity-100'
                : 'celya-thumb aspect-square rounded-lg overflow-hidden cursor-pointer border-2 border-transparent opacity-60 hover:opacity-100 hover:border-celya-orange_dark transition-all';
            var alt = img.alt ? img.alt.replace(/"/g, '&quot;') : '';
            return '<div class="' + cls + '" data-full="' + img.full + '">'
                + '<img src="' + img.thumb + '" class="w-full h-full object-cover" alt="' + alt + '">'
                + '</div>';
        }

        function bindThumbClicks() {
            $thumbsContainer.find('.celya-thumb').off('click.var-gallery').on('click.var-gallery', function () {
                var fullUrl = $(this).data('full');
                if (!fullUrl) return;

                $mainImage.css('opacity', 0);
                setTimeout(function () {
                    $mainImage.attr('src', fullUrl).removeAttr('srcset');
                    $mainImage.css('opacity', 1);
                }, 150);

                $thumbsContainer.find('.celya-thumb')
                    .removeClass('active-thumb border-celya-orange_dark opacity-100')
                    .addClass('border-transparent opacity-60');
                $(this)
                    .addClass('active-thumb border-celya-orange_dark opacity-100')
                    .removeClass('border-transparent opacity-60');
            });
        }

        $form.on('found_variation', function (_e, variation) {
            var varId = variation.variation_id;

            if (!celyaVarGallery[varId]) {
                // Pas de galerie custom — restaurer les vignettes d'origine
                if ($thumbsContainer.length && originalThumbsHtml !== null) {
                    $thumbsContainer.html(originalThumbsHtml);
                    bindThumbClicks();
                }
                return;
            }

            var images = celyaVarGallery[varId];
            if (!images || !images.length) return;

            // Reconstruire les vignettes avec les images de la galerie custom
            var html = '';
            images.forEach(function (img, i) {
                html += buildThumbHtml(img, i === 0);
            });
            $thumbsContainer.html(html);
            bindThumbClicks();

            // Mettre à jour l'image principale (160ms > les 150ms de product-page.js pour s'exécuter en dernier)
            $mainImage.css('opacity', 0);
            setTimeout(function () {
                $mainImage.attr('src', images[0].full).removeAttr('srcset');
                $mainImage.css('opacity', 1);
            }, 160);
        });

        $form.on('reset_data', function () {
            if ($thumbsContainer.length && originalThumbsHtml !== null) {
                $thumbsContainer.html(originalThumbsHtml);
                bindThumbClicks();
            }

            if ($mainImage.length && originalMainSrc) {
                $mainImage.css('opacity', 0);
                setTimeout(function () {
                    $mainImage.attr('src', originalMainSrc);
                    if (originalSrcset) {
                        $mainImage.attr('srcset', originalSrcset);
                    }
                    $mainImage.css('opacity', 1);
                }, 150);
            }
        });
    });
}(jQuery));
