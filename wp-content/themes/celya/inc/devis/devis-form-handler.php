<?php
/**
 * Devis — Traitement de la soumission du formulaire
 *
 * Reçoit le POST du bloc « celya/devis-form », vérifie la sécurité (nonce,
 * honeypot, reCAPTCHA v3, rate-limit), valide/assainit les champs, crée la
 * demande (CPT) puis notifie l'admin. Fonctionne pour les visiteurs connectés
 * et non connectés (admin-post + admin-post_nopriv) avec un Post-Redirect-Get.
 *
 * @package Celya
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

const CELYA_DEVIS_ACTION       = 'celya_devis_submit';
const CELYA_DEVIS_AJAX_ACTION  = 'celya_devis_ajax_submit';
const CELYA_DEVIS_NONCE_ACTION = 'celya_devis_form';
const CELYA_DEVIS_NONCE_FIELD  = 'celya_devis_nonce';
const CELYA_DEVIS_ANCHOR       = 'celya-devis';
const CELYA_DEVIS_RL_MAX       = 5;   // Soumissions max…
const CELYA_DEVIS_RL_WINDOW    = 600; // …par fenêtre (secondes).

// Soumission AJAX (chemin principal, sans rechargement).
add_action( 'wp_ajax_' . CELYA_DEVIS_AJAX_ACTION, 'celya_devis_handle_ajax_submission' );
add_action( 'wp_ajax_nopriv_' . CELYA_DEVIS_AJAX_ACTION, 'celya_devis_handle_ajax_submission' );

// Soumission POST classique (repli si JavaScript désactivé).
add_action( 'admin_post_' . CELYA_DEVIS_ACTION, 'celya_devis_handle_post_submission' );
add_action( 'admin_post_nopriv_' . CELYA_DEVIS_ACTION, 'celya_devis_handle_post_submission' );

/**
 * Traite une soumission (cœur commun AJAX + POST classique).
 *
 * @return array{ok:bool,code:string,errors:array,data:array,post_id:int}
 */
function celya_devis_process_submission() {

    // 1. Nonce (anti-CSRF).
    if ( ! isset( $_POST[ CELYA_DEVIS_NONCE_FIELD ] )
        || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST[ CELYA_DEVIS_NONCE_FIELD ] ) ), CELYA_DEVIS_NONCE_ACTION ) ) {
        return celya_devis_result( false, 'securite' );
    }

    // 2. Honeypot : un champ caché qui doit rester vide. On simule un succès.
    if ( ! empty( $_POST['celya_devis_website'] ) ) {
        return celya_devis_result( true, 'success' );
    }

    // 3. Rate-limiting par IP.
    if ( celya_devis_is_rate_limited() ) {
        return celya_devis_result( false, 'trop_de_demandes' );
    }

    // 4. reCAPTCHA v3.
    if ( ! celya_devis_verify_recaptcha() ) {
        return celya_devis_result( false, 'recaptcha', array(), celya_devis_sanitize_input() );
    }

    // 5. Assainissement + validation.
    $data   = celya_devis_sanitize_input();
    $errors = celya_devis_validate( $data );

    if ( ! empty( $errors ) ) {
        return celya_devis_result( false, 'validation', $errors, $data );
    }

    // 6. Création de la demande.
    $post_id = celya_devis_create_post( $data );

    if ( is_wp_error( $post_id ) || ! $post_id ) {
        return celya_devis_result( false, 'enregistrement', array(), $data );
    }

    // 7. Notification admin (+ copie éventuelle).
    celya_devis_notify_admin( $post_id, $data );

    return celya_devis_result( true, 'success', array(), $data, $post_id );
}

/**
 * Construit un résultat de traitement normalisé.
 *
 * @param bool   $ok      Succès.
 * @param string $code    Code de résultat.
 * @param array  $errors  Erreurs par champ.
 * @param array  $data    Données saisies (re-remplissage).
 * @param int    $post_id ID créé.
 * @return array
 */
function celya_devis_result( $ok, $code, $errors = array(), $data = array(), $post_id = 0 ) {
    return array(
        'ok'      => (bool) $ok,
        'code'    => $code,
        'errors'  => $errors,
        'data'    => $data,
        'post_id' => (int) $post_id,
    );
}

/**
 * Message d'erreur lisible associé à un code.
 *
 * @param string $code Code de résultat.
 * @return string
 */
function celya_devis_message_for_code( $code ) {
    switch ( $code ) {
        case 'recaptcha':
            return __( 'La vérification anti-spam a échoué. Merci de réessayer.', 'celya-tailwind' );
        case 'trop_de_demandes':
            return __( 'Trop de demandes envoyées. Merci de patienter quelques minutes.', 'celya-tailwind' );
        case 'validation':
            return __( 'Merci de corriger les champs indiqués ci-dessous.', 'celya-tailwind' );
        case 'securite':
            return __( 'Votre session a expiré. Merci de recharger la page puis de réessayer.', 'celya-tailwind' );
        case 'success':
            return __( 'Merci ! Votre demande de devis a bien été envoyée. Nous revenons vers vous rapidement.', 'celya-tailwind' );
        default:
            return __( 'Une erreur est survenue lors de l’envoi. Merci de réessayer.', 'celya-tailwind' );
    }
}

/**
 * Point d'entrée AJAX (chemin principal).
 */
function celya_devis_handle_ajax_submission() {
    $result = celya_devis_process_submission();

    $payload = array(
        'code'    => $result['code'],
        'message' => celya_devis_message_for_code( $result['code'] ),
        'errors'  => $result['errors'],
    );

    if ( $result['ok'] ) {
        wp_send_json_success( $payload );
    }

    wp_send_json_error( $payload );
}

/**
 * Point d'entrée POST classique (repli sans JavaScript), avec Post-Redirect-Get.
 */
function celya_devis_handle_post_submission() {
    $redirect = wp_get_referer() ? wp_get_referer() : home_url( '/' );
    $result   = celya_devis_process_submission();

    if ( $result['ok'] ) {
        $url = add_query_arg( 'devis', 'success', remove_query_arg( array( 'devis', 'devis_token' ), $redirect ) );
        wp_safe_redirect( $url . '#' . CELYA_DEVIS_ANCHOR );
        exit;
    }

    celya_devis_redirect_error( $redirect, $result['code'], $result['data'], $result['errors'] );
}

/**
 * Construit un <select> alimenté par les termes d'une taxonomie (utilisé par
 * le rendu du bloc devis-form).
 *
 * @param string $name        Attribut name.
 * @param string $id          Attribut id.
 * @param string $taxonomy    Taxonomie source.
 * @param int    $selected    Terme présélectionné.
 * @param string $placeholder Libellé de l'option vide.
 * @param bool   $required    Ajoute l'attribut required.
 * @return string HTML échappé.
 */
function celya_devis_render_select( $name, $id, $taxonomy, $selected, $placeholder, $required = false ) {
    $terms = get_terms(
        array(
            'taxonomy'   => $taxonomy,
            'hide_empty' => false,
        )
    );

    $html  = sprintf(
        '<select id="%s" name="%s"%s>',
        esc_attr( $id ),
        esc_attr( $name ),
        $required ? ' required' : ''
    );
    $html .= '<option value="">' . esc_html( $placeholder ) . '</option>';

    if ( ! is_wp_error( $terms ) ) {
        foreach ( $terms as $term ) {
            $html .= sprintf(
                '<option value="%d" %s>%s</option>',
                (int) $term->term_id,
                selected( $selected, (int) $term->term_id, false ),
                esc_html( $term->name )
            );
        }
    }

    $html .= '</select>';
    return $html;
}

/**
 * Récupère l'IP cliente de façon prudente.
 *
 * @return string
 */
function celya_devis_get_ip() {
    $ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
    return $ip ? $ip : '0.0.0.0';
}

/**
 * Incrémente et vérifie le compteur de soumissions par IP.
 *
 * @return bool true si la limite est dépassée.
 */
function celya_devis_is_rate_limited() {
    $key   = 'celya_devis_rl_' . md5( celya_devis_get_ip() );
    $count = (int) get_transient( $key );

    if ( $count >= CELYA_DEVIS_RL_MAX ) {
        return true;
    }

    set_transient( $key, $count + 1, CELYA_DEVIS_RL_WINDOW );
    return false;
}

/**
 * Vérifie le jeton reCAPTCHA v3 côté serveur.
 *
 * Si aucune clé secrète n'est configurée, la vérification est ignorée (permet
 * de tester le formulaire avant de renseigner les clés).
 *
 * @return bool
 */
function celya_devis_verify_recaptcha() {

    $secret = get_option( 'celya_devis_recaptcha_secret_key' );
    if ( empty( $secret ) ) {
        return true; // reCAPTCHA non configuré → on ne bloque pas.
    }

    $token = isset( $_POST['celya_devis_recaptcha_token'] )
        ? sanitize_text_field( wp_unslash( $_POST['celya_devis_recaptcha_token'] ) )
        : '';

    if ( empty( $token ) ) {
        return false;
    }

    $response = wp_remote_post(
        'https://www.google.com/recaptcha/api/siteverify',
        array(
            'timeout' => 10,
            'body'    => array(
                'secret'   => $secret,
                'response' => $token,
                'remoteip' => celya_devis_get_ip(),
            ),
        )
    );

    if ( is_wp_error( $response ) ) {
        return false;
    }

    $body = json_decode( wp_remote_retrieve_body( $response ), true );
    if ( empty( $body['success'] ) ) {
        return false;
    }

    $threshold = (float) get_option( 'celya_devis_recaptcha_threshold', '0.5' );
    $score     = isset( $body['score'] ) ? (float) $body['score'] : 0;

    return $score >= $threshold;
}

/**
 * Assainit les champs soumis.
 *
 * @return array
 */
function celya_devis_sanitize_input() {
    return array(
        'nom'              => isset( $_POST['celya_devis_nom'] ) ? sanitize_text_field( wp_unslash( $_POST['celya_devis_nom'] ) ) : '',
        'email'            => isset( $_POST['celya_devis_email'] ) ? sanitize_email( wp_unslash( $_POST['celya_devis_email'] ) ) : '',
        'telephone'        => isset( $_POST['celya_devis_telephone'] ) ? sanitize_text_field( wp_unslash( $_POST['celya_devis_telephone'] ) ) : '',
        'entreprise'       => isset( $_POST['celya_devis_entreprise'] ) ? sanitize_text_field( wp_unslash( $_POST['celya_devis_entreprise'] ) ) : '',
        'siret'            => isset( $_POST['celya_devis_siret'] ) ? preg_replace( '/\s+/', '', sanitize_text_field( wp_unslash( $_POST['celya_devis_siret'] ) ) ) : '',
        'quantite'         => isset( $_POST['celya_devis_quantite'] ) ? sanitize_text_field( wp_unslash( $_POST['celya_devis_quantite'] ) ) : '',
        'message'          => isset( $_POST['celya_devis_message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['celya_devis_message'] ) ) : '',
        'secteur'          => isset( $_POST['celya_devis_secteur'] ) ? (int) $_POST['celya_devis_secteur'] : 0,
        'conditionnement'  => isset( $_POST['celya_devis_conditionnement'] ) ? (int) $_POST['celya_devis_conditionnement'] : 0,
        'type_projet'      => isset( $_POST['celya_devis_type_projet'] ) ? (int) $_POST['celya_devis_type_projet'] : 0,
    );
}

/**
 * Valide les champs assainis.
 *
 * @param array $data Données assainies.
 * @return array<string,string> Erreurs par champ.
 */
function celya_devis_validate( $data ) {
    $errors = array();

    // Champs texte obligatoires.
    $required_text = array(
        'nom'        => __( 'Le nom est obligatoire.', 'celya-tailwind' ),
        'telephone'  => __( 'Le téléphone est obligatoire.', 'celya-tailwind' ),
        'entreprise' => __( 'Le nom de l’entreprise est obligatoire.', 'celya-tailwind' ),
        'quantite'   => __( 'La quantité estimée est obligatoire.', 'celya-tailwind' ),
        'message'    => __( 'Merci de décrire votre projet.', 'celya-tailwind' ),
    );
    foreach ( $required_text as $field => $message ) {
        if ( '' === $data[ $field ] ) {
            $errors[ $field ] = $message;
        }
    }

    if ( '' === $data['email'] || ! is_email( $data['email'] ) ) {
        $errors['email'] = __( 'Une adresse email valide est obligatoire.', 'celya-tailwind' );
    }

    if ( '' === $data['siret'] ) {
        $errors['siret'] = __( 'Le numéro SIRET est obligatoire.', 'celya-tailwind' );
    } elseif ( ! preg_match( '/^\d{14}$/', $data['siret'] ) ) {
        $errors['siret'] = __( 'Le numéro SIRET doit comporter 14 chiffres.', 'celya-tailwind' );
    }

    // Listes déroulantes / cartes obligatoires : sélection + terme valide.
    foreach ( array(
        'secteur'         => 'devis_secteur',
        'conditionnement' => 'devis_conditionnement',
        'type_projet'     => 'devis_type_projet',
    ) as $field => $taxonomy ) {
        if ( ! $data[ $field ] ) {
            $errors[ $field ] = __( 'Ce choix est obligatoire.', 'celya-tailwind' );
        } elseif ( ! term_exists( $data[ $field ], $taxonomy ) ) {
            $errors[ $field ] = __( 'Option invalide.', 'celya-tailwind' );
        }
    }

    return $errors;
}

/**
 * Crée la demande de devis (CPT + meta + termes).
 *
 * @param array $data Données validées.
 * @return int|WP_Error
 */
function celya_devis_create_post( $data ) {

    $title = sprintf(
        /* translators: 1: nom du demandeur, 2: date */
        __( 'Devis — %1$s — %2$s', 'celya-tailwind' ),
        $data['nom'] ? $data['nom'] : __( 'Anonyme', 'celya-tailwind' ),
        date_i18n( 'd/m/Y H:i' )
    );

    $post_id = wp_insert_post(
        array(
            'post_type'   => 'demande_devis',
            'post_status' => 'publish',
            'post_title'  => $title,
        ),
        true
    );

    if ( is_wp_error( $post_id ) ) {
        return $post_id;
    }

    update_post_meta( $post_id, '_celya_devis_nom', $data['nom'] );
    update_post_meta( $post_id, '_celya_devis_email', $data['email'] );
    update_post_meta( $post_id, '_celya_devis_telephone', $data['telephone'] );
    update_post_meta( $post_id, '_celya_devis_entreprise', $data['entreprise'] );
    update_post_meta( $post_id, '_celya_devis_siret', $data['siret'] );
    update_post_meta( $post_id, '_celya_devis_quantite', $data['quantite'] );
    update_post_meta( $post_id, '_celya_devis_message', $data['message'] );

    // Affectation des termes (taxonomies = listes déroulantes).
    if ( $data['secteur'] ) {
        wp_set_object_terms( $post_id, array( $data['secteur'] ), 'devis_secteur' );
    }
    if ( $data['conditionnement'] ) {
        wp_set_object_terms( $post_id, array( $data['conditionnement'] ), 'devis_conditionnement' );
    }
    if ( $data['type_projet'] ) {
        wp_set_object_terms( $post_id, array( $data['type_projet'] ), 'devis_type_projet' );
    }

    return $post_id;
}

/**
 * Envoie la notification email à l'administrateur.
 *
 * @param int   $post_id ID de la demande.
 * @param array $data    Données validées.
 */
function celya_devis_notify_admin( $post_id, $data ) {

    $to = get_option( 'celya_devis_notification_email', get_option( 'admin_email' ) );
    if ( ! is_email( $to ) ) {
        $to = get_option( 'admin_email' );
    }

    $secteur = celya_devis_term_name( $data['secteur'] );
    $cond    = celya_devis_term_name( $data['conditionnement'] );
    $type    = celya_devis_term_name( $data['type_projet'] );

    $subject = sprintf(
        /* translators: %s: nom du demandeur */
        __( '[Devis] Nouvelle demande de %s', 'celya-tailwind' ),
        $data['nom']
    );

    $lines = array(
        __( 'Nouvelle demande de devis :', 'celya-tailwind' ),
        '',
        sprintf( '%s : %s', __( 'Nom et prénom', 'celya-tailwind' ), $data['nom'] ),
        sprintf( '%s : %s', __( 'Email', 'celya-tailwind' ), $data['email'] ),
        sprintf( '%s : %s', __( 'Téléphone', 'celya-tailwind' ), $data['telephone'] ),
        sprintf( '%s : %s', __( 'Entreprise', 'celya-tailwind' ), $data['entreprise'] ),
        sprintf( '%s : %s', __( 'SIRET', 'celya-tailwind' ), $data['siret'] ),
        sprintf( '%s : %s', __( 'Secteur', 'celya-tailwind' ), $secteur ),
        sprintf( '%s : %s', __( 'Quantité', 'celya-tailwind' ), $data['quantite'] ),
        sprintf( '%s : %s', __( 'Conditionnement', 'celya-tailwind' ), $cond ),
        sprintf( '%s : %s', __( 'Type de projet', 'celya-tailwind' ), $type ),
        '',
        __( 'Description :', 'celya-tailwind' ),
        $data['message'],
        '',
        sprintf( '%s : %s', __( 'Voir la demande', 'celya-tailwind' ), get_edit_post_link( $post_id, 'raw' ) ),
    );

    $headers = array();
    if ( is_email( $data['email'] ) ) {
        $headers[] = 'Reply-To: ' . $data['nom'] . ' <' . $data['email'] . '>';
    }

    // Copie(s) de la demande : adresses séparées par des virgules dans les réglages.
    $copies = array_filter( array_map( 'trim', explode( ',', (string) get_option( 'celya_devis_copy_email', '' ) ) ) );
    foreach ( $copies as $copy ) {
        if ( is_email( $copy ) ) {
            $headers[] = 'Cc: ' . $copy;
        }
    }

    /**
     * Permet de filtrer les destinataires/contenu avant envoi.
     */
    $headers = apply_filters( 'celya_devis_mail_headers', $headers, $data, $post_id );

    wp_mail( $to, $subject, implode( "\n", $lines ), $headers );
}

/**
 * Retourne le nom d'un terme à partir de son ID.
 *
 * @param int $term_id ID du terme.
 * @return string
 */
function celya_devis_term_name( $term_id ) {
    if ( ! $term_id ) {
        return '';
    }
    $term = get_term( (int) $term_id );
    return ( $term && ! is_wp_error( $term ) ) ? $term->name : '';
}

/**
 * Redirige avec un message d'erreur et conserve les valeurs saisies via un
 * transient à durée de vie courte (Post-Redirect-Get sans perdre la saisie).
 *
 * @param string $redirect URL de redirection.
 * @param string $code     Code d'erreur.
 * @param array  $data     Valeurs saisies à conserver.
 * @param array  $errors   Erreurs par champ.
 */
function celya_devis_redirect_error( $redirect, $code, $data = array(), $errors = array() ) {
    $token = wp_generate_password( 12, false );
    set_transient(
        'celya_devis_flash_' . $token,
        array(
            'code'   => $code,
            'data'   => $data,
            'errors' => $errors,
        ),
        120
    );

    $url = add_query_arg(
        array(
            'devis'       => 'error',
            'devis_token' => $token,
        ),
        remove_query_arg( array( 'devis', 'devis_token' ), $redirect )
    );

    wp_safe_redirect( $url . '#' . CELYA_DEVIS_ANCHOR );
    exit;
}
