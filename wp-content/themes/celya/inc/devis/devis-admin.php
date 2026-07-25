<?php
/**
 * Devis — Back-office
 *
 * Colonnes de la liste des demandes, metaboxes de consultation (lecture seule)
 * et page de réglages (clés reCAPTCHA + email de notification).
 *
 * @package Celya
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Liste des meta-données stockées pour une demande de devis.
 *
 * @return array<string,string> clé meta => libellé.
 */
function celya_devis_get_fields() {
    return array(
        '_celya_devis_nom'        => __( 'Nom et prénom', 'celya-tailwind' ),
        '_celya_devis_email'      => __( 'Adresse mail', 'celya-tailwind' ),
        '_celya_devis_telephone'  => __( 'Téléphone', 'celya-tailwind' ),
        '_celya_devis_entreprise' => __( 'Entreprise', 'celya-tailwind' ),
        '_celya_devis_siret'      => __( 'N° SIRET', 'celya-tailwind' ),
        '_celya_devis_quantite'   => __( 'Quantité estimée', 'celya-tailwind' ),
        '_celya_devis_message'    => __( 'Description du projet', 'celya-tailwind' ),
    );
}

/*
 * -------------------------------------------------------------------------
 *  Colonnes de la liste des demandes
 * -------------------------------------------------------------------------
 */

/**
 * Définit les colonnes de la liste des demandes.
 *
 * @param array $columns Colonnes existantes.
 * @return array
 */
function celya_devis_admin_columns( $columns ) {
    return array(
        'cb'                     => $columns['cb'],
        'title'                  => __( 'Demandeur', 'celya-tailwind' ),
        'celya_devis_email'      => __( 'Email', 'celya-tailwind' ),
        'celya_devis_telephone'  => __( 'Téléphone', 'celya-tailwind' ),
        'taxonomy-devis_type_projet' => __( 'Type de projet', 'celya-tailwind' ),
        'date'                   => __( 'Reçu le', 'celya-tailwind' ),
    );
}
add_filter( 'manage_demande_devis_posts_columns', 'celya_devis_admin_columns' );

/**
 * Remplit les colonnes personnalisées.
 *
 * @param string $column  Identifiant de colonne.
 * @param int    $post_id ID de la demande.
 */
function celya_devis_admin_column_content( $column, $post_id ) {
    if ( 'celya_devis_email' === $column ) {
        $email = get_post_meta( $post_id, '_celya_devis_email', true );
        if ( $email ) {
            printf( '<a href="%s">%s</a>', esc_url( 'mailto:' . $email ), esc_html( $email ) );
        }
    } elseif ( 'celya_devis_telephone' === $column ) {
        echo esc_html( get_post_meta( $post_id, '_celya_devis_telephone', true ) );
    }
}
add_action( 'manage_demande_devis_posts_custom_column', 'celya_devis_admin_column_content', 10, 2 );

/*
 * -------------------------------------------------------------------------
 *  Metaboxes de consultation (lecture seule)
 * -------------------------------------------------------------------------
 */

/**
 * Enregistre la metabox de détail de la demande.
 */
function celya_devis_register_metaboxes() {
    add_meta_box(
        'celya_devis_details',
        __( 'Détail de la demande', 'celya-tailwind' ),
        'celya_devis_render_details_metabox',
        'demande_devis',
        'normal',
        'high'
    );
}
add_action( 'add_meta_boxes', 'celya_devis_register_metaboxes' );

/**
 * Affiche les informations de la demande en lecture seule.
 *
 * @param WP_Post $post Demande courante.
 */
function celya_devis_render_details_metabox( $post ) {
    echo '<table class="widefat striped" style="margin-top:8px;">';
    foreach ( celya_devis_get_fields() as $meta_key => $label ) {
        $value = get_post_meta( $post->ID, $meta_key, true );
        echo '<tr>';
        printf( '<th style="width:200px;text-align:left;">%s</th>', esc_html( $label ) );
        echo '<td>';
        if ( '_celya_devis_email' === $meta_key && $value ) {
            printf( '<a href="%s">%s</a>', esc_url( 'mailto:' . $value ), esc_html( $value ) );
        } elseif ( '_celya_devis_message' === $meta_key ) {
            echo nl2br( esc_html( $value ) );
        } else {
            echo esc_html( $value );
        }
        echo '</td></tr>';
    }
    echo '</table>';
}

/**
 * Retire l'éditeur de contenu inutile pour ce CPT.
 */
function celya_devis_remove_editor() {
    remove_post_type_support( 'demande_devis', 'editor' );
}
add_action( 'init', 'celya_devis_remove_editor', 20 );

/*
 * -------------------------------------------------------------------------
 *  Page de réglages (clés reCAPTCHA + notification)
 * -------------------------------------------------------------------------
 */

/**
 * Ajoute la page de réglages sous le menu des demandes de devis.
 */
function celya_devis_register_settings_page() {
    add_submenu_page(
        'edit.php?post_type=demande_devis',
        __( 'Réglages des devis', 'celya-tailwind' ),
        __( 'Réglages', 'celya-tailwind' ),
        'manage_options',
        'celya-devis-settings',
        'celya_devis_render_settings_page'
    );
}
add_action( 'admin_menu', 'celya_devis_register_settings_page' );

/**
 * Enregistre les options via la Settings API.
 */
function celya_devis_register_settings() {
    register_setting( 'celya_devis_settings', 'celya_devis_recaptcha_site_key', 'sanitize_text_field' );
    register_setting( 'celya_devis_settings', 'celya_devis_recaptcha_secret_key', 'sanitize_text_field' );
    register_setting(
        'celya_devis_settings',
        'celya_devis_recaptcha_threshold',
        array(
            'sanitize_callback' => 'celya_devis_sanitize_threshold',
            'default'           => '0.5',
        )
    );
    register_setting( 'celya_devis_settings', 'celya_devis_notification_email', 'sanitize_email' );
    register_setting( 'celya_devis_settings', 'celya_devis_copy_email', 'celya_devis_sanitize_email_list' );

    // SMTP Brevo.
    register_setting( 'celya_devis_settings', 'celya_devis_smtp_enabled', 'celya_devis_sanitize_checkbox' );
    register_setting( 'celya_devis_settings', 'celya_devis_smtp_host', array( 'sanitize_callback' => 'sanitize_text_field', 'default' => 'smtp-relay.brevo.com' ) );
    register_setting( 'celya_devis_settings', 'celya_devis_smtp_port', array( 'sanitize_callback' => 'absint', 'default' => 587 ) );
    register_setting( 'celya_devis_settings', 'celya_devis_smtp_user', 'sanitize_text_field' );
    register_setting( 'celya_devis_settings', 'celya_devis_smtp_pass', 'celya_devis_sanitize_smtp_pass' );
    register_setting( 'celya_devis_settings', 'celya_devis_smtp_from_email', 'sanitize_email' );
    register_setting( 'celya_devis_settings', 'celya_devis_smtp_from_name', 'sanitize_text_field' );
}
add_action( 'admin_init', 'celya_devis_register_settings' );

/**
 * Assainit une liste d'emails séparés par des virgules.
 *
 * @param string $value Valeur saisie.
 * @return string
 */
function celya_devis_sanitize_email_list( $value ) {
    $emails = array_filter( array_map( 'trim', explode( ',', (string) $value ) ) );
    $valid  = array();
    foreach ( $emails as $email ) {
        $clean = sanitize_email( $email );
        if ( $clean && is_email( $clean ) ) {
            $valid[] = $clean;
        }
    }
    return implode( ', ', $valid );
}

/**
 * Assainit une case à cocher.
 *
 * @param mixed $value Valeur.
 * @return string '1' ou ''.
 */
function celya_devis_sanitize_checkbox( $value ) {
    return $value ? '1' : '';
}

/**
 * Conserve l'ancien mot de passe SMTP si le champ est laissé vide.
 *
 * @param string $value Valeur saisie.
 * @return string
 */
function celya_devis_sanitize_smtp_pass( $value ) {
    $value = (string) $value;
    if ( '' === trim( $value ) ) {
        return (string) get_option( 'celya_devis_smtp_pass', '' );
    }
    return $value;
}

/**
 * Borne le score reCAPTCHA entre 0 et 1.
 *
 * @param string $value Valeur saisie.
 * @return string
 */
function celya_devis_sanitize_threshold( $value ) {
    $value = (float) str_replace( ',', '.', (string) $value );
    if ( $value < 0 ) {
        $value = 0;
    } elseif ( $value > 1 ) {
        $value = 1;
    }
    return (string) $value;
}

/**
 * Rend la page de réglages.
 */
function celya_devis_render_settings_page() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }
    $notification_email = get_option( 'celya_devis_notification_email', get_option( 'admin_email' ) );
    ?>
    <div class="wrap">
        <h1><?php esc_html_e( 'Réglages des demandes de devis', 'celya-tailwind' ); ?></h1>
        <form method="post" action="options.php">
            <?php settings_fields( 'celya_devis_settings' ); ?>
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row"><label for="celya_devis_recaptcha_site_key"><?php esc_html_e( 'Clé du site reCAPTCHA v3', 'celya-tailwind' ); ?></label></th>
                    <td><input type="text" class="regular-text" id="celya_devis_recaptcha_site_key" name="celya_devis_recaptcha_site_key" value="<?php echo esc_attr( get_option( 'celya_devis_recaptcha_site_key' ) ); ?>" /></td>
                </tr>
                <tr>
                    <th scope="row"><label for="celya_devis_recaptcha_secret_key"><?php esc_html_e( 'Clé secrète reCAPTCHA v3', 'celya-tailwind' ); ?></label></th>
                    <td><input type="password" class="regular-text" id="celya_devis_recaptcha_secret_key" name="celya_devis_recaptcha_secret_key" value="<?php echo esc_attr( get_option( 'celya_devis_recaptcha_secret_key' ) ); ?>" autocomplete="off" /></td>
                </tr>
                <tr>
                    <th scope="row"><label for="celya_devis_recaptcha_threshold"><?php esc_html_e( 'Score minimum (0 à 1)', 'celya-tailwind' ); ?></label></th>
                    <td>
                        <input type="number" step="0.1" min="0" max="1" id="celya_devis_recaptcha_threshold" name="celya_devis_recaptcha_threshold" value="<?php echo esc_attr( get_option( 'celya_devis_recaptcha_threshold', '0.5' ) ); ?>" />
                        <p class="description"><?php esc_html_e( 'En dessous de ce score, la soumission est considérée comme un robot (0.5 recommandé).', 'celya-tailwind' ); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="celya_devis_notification_email"><?php esc_html_e( 'Email de notification', 'celya-tailwind' ); ?></label></th>
                    <td>
                        <input type="email" class="regular-text" id="celya_devis_notification_email" name="celya_devis_notification_email" value="<?php echo esc_attr( $notification_email ); ?>" />
                        <p class="description"><?php esc_html_e( 'Destinataire principal de la notification à chaque nouvelle demande.', 'celya-tailwind' ); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="celya_devis_copy_email"><?php esc_html_e( 'Email(s) en copie', 'celya-tailwind' ); ?></label></th>
                    <td>
                        <input type="text" class="regular-text" id="celya_devis_copy_email" name="celya_devis_copy_email" value="<?php echo esc_attr( get_option( 'celya_devis_copy_email', '' ) ); ?>" />
                        <p class="description"><?php esc_html_e( 'Copie (Cc) de la demande. Séparez plusieurs adresses par des virgules.', 'celya-tailwind' ); ?></p>
                    </td>
                </tr>
            </table>

            <h2 class="title"><?php esc_html_e( 'Envoi des emails (SMTP Brevo)', 'celya-tailwind' ); ?></h2>
            <p class="description"><?php esc_html_e( 'Améliore la délivrabilité des notifications en passant par le serveur SMTP de Brevo.', 'celya-tailwind' ); ?></p>
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row"><?php esc_html_e( 'Activer le SMTP Brevo', 'celya-tailwind' ); ?></th>
                    <td>
                        <label>
                            <input type="checkbox" id="celya_devis_smtp_enabled" name="celya_devis_smtp_enabled" value="1" <?php checked( get_option( 'celya_devis_smtp_enabled' ), '1' ); ?> />
                            <?php esc_html_e( 'Envoyer les emails du site via Brevo', 'celya-tailwind' ); ?>
                        </label>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="celya_devis_smtp_host"><?php esc_html_e( 'Serveur SMTP', 'celya-tailwind' ); ?></label></th>
                    <td><input type="text" class="regular-text" id="celya_devis_smtp_host" name="celya_devis_smtp_host" value="<?php echo esc_attr( get_option( 'celya_devis_smtp_host', 'smtp-relay.brevo.com' ) ); ?>" /></td>
                </tr>
                <tr>
                    <th scope="row"><label for="celya_devis_smtp_port"><?php esc_html_e( 'Port', 'celya-tailwind' ); ?></label></th>
                    <td>
                        <input type="number" id="celya_devis_smtp_port" name="celya_devis_smtp_port" value="<?php echo esc_attr( get_option( 'celya_devis_smtp_port', 587 ) ); ?>" />
                        <p class="description"><?php esc_html_e( '587 (TLS) recommandé pour Brevo.', 'celya-tailwind' ); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="celya_devis_smtp_user"><?php esc_html_e( 'Identifiant SMTP', 'celya-tailwind' ); ?></label></th>
                    <td>
                        <input type="text" class="regular-text" id="celya_devis_smtp_user" name="celya_devis_smtp_user" value="<?php echo esc_attr( get_option( 'celya_devis_smtp_user', '' ) ); ?>" autocomplete="off" />
                        <p class="description"><?php esc_html_e( 'Le login SMTP fourni par Brevo (souvent votre email de compte).', 'celya-tailwind' ); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="celya_devis_smtp_pass"><?php esc_html_e( 'Clé SMTP (mot de passe)', 'celya-tailwind' ); ?></label></th>
                    <td>
                        <input type="password" class="regular-text" id="celya_devis_smtp_pass" name="celya_devis_smtp_pass" value="" autocomplete="new-password" placeholder="<?php echo get_option( 'celya_devis_smtp_pass' ) ? '••••••••' : ''; ?>" />
                        <p class="description"><?php esc_html_e( 'Master Password / clé SMTP Brevo. Laissez vide pour conserver la valeur actuelle.', 'celya-tailwind' ); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="celya_devis_smtp_from_email"><?php esc_html_e( 'Email expéditeur', 'celya-tailwind' ); ?></label></th>
                    <td>
                        <input type="email" class="regular-text" id="celya_devis_smtp_from_email" name="celya_devis_smtp_from_email" value="<?php echo esc_attr( get_option( 'celya_devis_smtp_from_email', '' ) ); ?>" />
                        <p class="description"><?php esc_html_e( 'Adresse vérifiée comme expéditeur dans Brevo.', 'celya-tailwind' ); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="celya_devis_smtp_from_name"><?php esc_html_e( 'Nom expéditeur', 'celya-tailwind' ); ?></label></th>
                    <td><input type="text" class="regular-text" id="celya_devis_smtp_from_name" name="celya_devis_smtp_from_name" value="<?php echo esc_attr( get_option( 'celya_devis_smtp_from_name', get_bloginfo( 'name' ) ) ); ?>" /></td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
        <p>
            <a href="https://www.google.com/recaptcha/admin" target="_blank" rel="noopener noreferrer">
                <?php esc_html_e( 'Obtenir des clés reCAPTCHA v3 →', 'celya-tailwind' ); ?>
            </a>
        </p>
    </div>
    <?php
}
