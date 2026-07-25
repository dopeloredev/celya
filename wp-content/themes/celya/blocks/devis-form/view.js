/**
 * Formulaire de devis — soumission AJAX
 *
 * Envoie le formulaire sans recharger la page : récupère un jeton reCAPTCHA v3
 * (si configuré), poste en AJAX, puis affiche le message de succès ou les
 * erreurs par champ directement au niveau du formulaire. Si JavaScript est
 * indisponible, le formulaire retombe sur l'envoi POST classique (admin-post).
 */
( function () {
    'use strict';

    var config = window.celyaDevis || {};

    document.querySelectorAll( '.celya-devis__form' ).forEach( function ( form ) {
        form.addEventListener( 'submit', function ( e ) {
            // Sans fetch (très vieux navigateur), on laisse l'envoi POST natif.
            if ( ! window.fetch || ! config.ajaxUrl ) {
                return;
            }

            e.preventDefault();

            // Validation HTML5 native d'abord (champs requis, email…).
            if ( form.checkValidity && ! form.checkValidity() ) {
                form.reportValidity();
                return;
            }

            var button = form.querySelector( '.celya-devis__submit' );
            clearMessages( form );
            if ( button ) {
                button.disabled = true;
            }

            getToken().then( function ( token ) {
                var field = form.querySelector( 'input[name="celya_devis_recaptcha_token"]' );
                if ( field ) {
                    field.value = token;
                }
                sendForm( form, button );
            } );
        } );
    } );

    /**
     * Récupère un jeton reCAPTCHA v3, ou une chaîne vide si non configuré.
     */
    function getToken() {
        if ( ! config.siteKey || ! window.grecaptcha ) {
            return Promise.resolve( '' );
        }
        return new Promise( function ( resolve ) {
            window.grecaptcha.ready( function () {
                window.grecaptcha
                    .execute( config.siteKey, { action: config.recaptchaAction || 'devis_submit' } )
                    .then( resolve, function () { resolve( '' ); } );
            } );
        } );
    }

    /**
     * Poste le formulaire en AJAX et traite la réponse JSON.
     */
    function sendForm( form, button ) {
        var data = new FormData( form );
        // On cible l'action AJAX plutôt que l'action admin-post.
        data.set( 'action', config.ajaxAction );

        fetch( config.ajaxUrl, {
            method: 'POST',
            credentials: 'same-origin',
            body: data
        } )
            .then( function ( r ) { return r.json(); } )
            .then( function ( json ) { handleResponse( form, json ); } )
            .catch( function () {
                showNotice( form, 'error', 'Une erreur est survenue lors de l’envoi. Merci de réessayer.' );
            } )
            .finally( function () {
                if ( button ) {
                    button.disabled = false;
                }
            } );
    }

    /**
     * Affiche le succès ou les erreurs renvoyées par le serveur.
     */
    function handleResponse( form, json ) {
        var payload = json && json.data ? json.data : {};

        if ( json && json.success ) {
            showNotice( form, 'success', payload.message );
            form.reset();
            return;
        }

        showNotice( form, 'error', payload.message );
        renderFieldErrors( form, payload.errors || {} );
    }

    /**
     * Insère un message global et défile vers lui (ou la 1re erreur).
     */
    function showNotice( form, type, message ) {
        var zone = getNoticeZone( form );
        if ( zone && message ) {
            zone.innerHTML =
                '<p class="celya-devis__notice celya-devis__notice--' +
                ( type === 'success' ? 'success' : 'error' ) +
                '"></p>';
            zone.querySelector( 'p' ).textContent = message;
        }
        var target = ( type === 'success' ) ? zone : ( form.querySelector( '.celya-devis__error--js' ) || zone );
        if ( target && target.scrollIntoView ) {
            target.scrollIntoView( { behavior: 'smooth', block: 'center' } );
        }
    }

    /**
     * Affiche les messages d'erreur sous chaque champ concerné.
     */
    function renderFieldErrors( form, errors ) {
        Object.keys( errors ).forEach( function ( key ) {
            var container = fieldContainer( form, key );
            if ( ! container ) {
                return;
            }
            var span = document.createElement( 'span' );
            span.className = 'celya-devis__error celya-devis__error--js';
            span.textContent = errors[ key ];
            container.appendChild( span );
        } );
    }

    /**
     * Retrouve le conteneur d'un champ à partir de sa clé d'erreur.
     */
    function fieldContainer( form, key ) {
        if ( key === 'type_projet' ) {
            return form.querySelector( '.celya-devis__projet' );
        }
        var input = form.querySelector( '[name="celya_devis_' + key + '"]' );
        return input ? input.closest( '.celya-devis__field' ) : null;
    }

    /**
     * Vide les messages (global + erreurs de champ injectées).
     */
    function clearMessages( form ) {
        var zone = getNoticeZone( form );
        if ( zone ) {
            zone.innerHTML = '';
        }
        form.querySelectorAll( '.celya-devis__error--js' ).forEach( function ( el ) {
            el.parentNode.removeChild( el );
        } );
    }

    /**
     * Récupère la zone de notice associée à un formulaire.
     */
    function getNoticeZone( form ) {
        var card = form.closest( '.celya-devis__card' ) || form.parentNode;
        return card ? card.querySelector( '.celya-devis__notice-zone' ) : null;
    }
} )();
