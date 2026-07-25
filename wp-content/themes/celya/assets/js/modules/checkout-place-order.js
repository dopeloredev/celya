/**
 * Checkout (bloc natif WooCommerce) — déplace le bouton « Passer la commande »
 * dans la sidebar, juste après le bloc récapitulatif/totaux.
 *
 * Le bloc Checkout est rendu en React : le bloc « Actions » (.wc-block-checkout__actions)
 * est verrouillé en bas de la colonne principale (.wc-block-checkout__main). On l'insère
 * comme frère immédiatement APRÈS le bloc récap (.wp-block-woocommerce-checkout-order-summary-block)
 * dans la sidebar (.wc-block-checkout__sidebar). Ainsi, tout contenu ajouté ensuite dans la
 * sidebar via le page builder se place SOUS le bouton.
 *
 * Le déplacement est ré-appliqué via MutationObserver, car React peut ré-insérer le bloc
 * dans la colonne principale (ou ré-ordonner la sidebar) lors d'un re-render.
 *
 * Le bouton reste à l'intérieur de .wc-block-checkout (même racine React) → ses événements
 * (clic, état désactivé, libellé) continuent de fonctionner normalement.
 */
( function () {
    'use strict';

    var ACTIONS_SEL = '.wc-block-checkout__actions';
    var MAIN_SEL = '.wc-block-checkout__main';
    var SIDEBAR_SEL = '.wc-block-checkout__sidebar';
    // Ancre = bloc récap/totaux dans la sidebar (sélecteurs par ordre de préférence).
    var SUMMARY_SEL = '.wp-block-woocommerce-checkout-order-summary-block, .wc-block-checkout__sidebar .wc-block-components-totals-wrapper';

    /**
     * Trouve l'élément récap au niveau direct de la sidebar (pour insérer le bouton comme frère).
     * @param {Element} sidebar
     * @returns {Element|null}
     */
    function findSummaryAnchor( sidebar ) {
        var match = sidebar.querySelector( SUMMARY_SEL );
        if ( ! match ) {
            return null;
        }
        // Remonter jusqu'à l'enfant direct de la sidebar pour insérer au bon niveau.
        while ( match && match.parentElement !== sidebar ) {
            match = match.parentElement;
        }
        return match;
    }

    /**
     * Insère le bloc Actions juste après le bloc récap, dans la sidebar.
     * @returns {boolean} true si le bloc est correctement positionné.
     */
    function relocate() {
        var actions = document.querySelector( ACTIONS_SEL );
        var sidebar = document.querySelector( SIDEBAR_SEL );

        if ( ! actions || ! sidebar ) {
            return false;
        }

        var anchor = findSummaryAnchor( sidebar );

        // Déjà bien placé ? (le bouton suit directement l'ancre) → no-op (évite toute boucle).
        if ( anchor && anchor.nextElementSibling === actions ) {
            return true;
        }
        if ( ! anchor && actions.parentElement === sidebar ) {
            return true; // fallback déjà en place
        }

        actions.classList.add( 'wc-block-checkout__actions--relocated' );
        if ( anchor ) {
            anchor.insertAdjacentElement( 'afterend', actions );
        } else {
            sidebar.appendChild( actions ); // fallback : bas de la sidebar
        }
        return true;
    }

    function init() {
        relocate();

        // Ré-applique le déplacement si React ré-insère/ré-ordonne le bloc.
        var observed = [ document.querySelector( MAIN_SEL ), document.querySelector( SIDEBAR_SEL ) ];
        observed.forEach( function ( node ) {
            if ( node ) {
                new MutationObserver( function () {
                    relocate();
                } ).observe( node, { childList: true } );
            }
        } );
    }

    function boot() {
        if ( document.querySelector( ACTIONS_SEL ) ) {
            init();
            return;
        }

        // Le bloc Checkout est monté de façon asynchrone : on attend son apparition.
        var bodyObs = new MutationObserver( function () {
            if ( document.querySelector( ACTIONS_SEL ) ) {
                bodyObs.disconnect();
                init();
            }
        } );
        bodyObs.observe( document.body, { childList: true, subtree: true } );
    }

    if ( document.readyState === 'loading' ) {
        document.addEventListener( 'DOMContentLoaded', boot );
    } else {
        boot();
    }
} )();
