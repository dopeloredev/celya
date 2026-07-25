<?php
/**
 * Rendu serveur du bloc celya/devis-form
 *
 * Variables disponibles : $attributes, $content, $block
 *
 * @package Celya
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$form_title = isset( $attributes['title'] ) && '' !== $attributes['title']
    ? $attributes['title']
    : __( 'Demander votre devis personnalisé', 'celya-tailwind' );

// ---------------------------------------------------------------------------
// État après soumission (Post-Redirect-Get) : succès / erreurs / re-remplissage.
// ---------------------------------------------------------------------------
$devis_state = isset( $_GET['devis'] ) ? sanitize_key( wp_unslash( $_GET['devis'] ) ) : '';
$old         = array();
$errors      = array();
$error_code  = '';

if ( 'error' === $devis_state && isset( $_GET['devis_token'] ) ) {
    $token = sanitize_text_field( wp_unslash( $_GET['devis_token'] ) );
    $flash = get_transient( 'celya_devis_flash_' . $token );
    if ( is_array( $flash ) ) {
        $old        = isset( $flash['data'] ) ? $flash['data'] : array();
        $errors     = isset( $flash['errors'] ) ? $flash['errors'] : array();
        $error_code = isset( $flash['code'] ) ? $flash['code'] : '';
        delete_transient( 'celya_devis_flash_' . $token );
    }
}

/**
 * Valeur ré-affichée après erreur.
 */
$val = static function ( $key ) use ( $old ) {
    return isset( $old[ $key ] ) ? esc_attr( $old[ $key ] ) : '';
};

/**
 * Message d'erreur d'un champ.
 */
$err = static function ( $key ) use ( $errors ) {
    return isset( $errors[ $key ] ) ? '<span class="celya-devis__error">' . esc_html( $errors[ $key ] ) . '</span>' : '';
};

// ---------------------------------------------------------------------------
// Scripts : reCAPTCHA v3 + comportement du formulaire.
// ---------------------------------------------------------------------------
$site_key = get_option( 'celya_devis_recaptcha_site_key' );
if ( $site_key ) {
    wp_enqueue_script(
        'celya-recaptcha-api',
        'https://www.google.com/recaptcha/api.js?render=' . rawurlencode( $site_key ),
        array(),
        null,
        true
    );
}
wp_enqueue_script( 'celya-devis-form-view' );
wp_localize_script(
    'celya-devis-form-view',
    'celyaDevis',
    array(
        'siteKey'         => $site_key ? $site_key : '',
        'recaptchaAction' => 'devis_submit',
        'ajaxUrl'         => admin_url( 'admin-ajax.php' ),
        'ajaxAction'      => 'celya_devis_ajax_submit',
    )
);

$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => 'celya-devis' ) );
?>
<div <?php echo $wrapper_attributes; ?>>
    <div class="celya-devis__card" id="celya-devis">

        <h2 class="celya-devis__title"><?php echo esc_html( $form_title ); ?></h2>

        <?php // Zone de message gérée en AJAX (et utilisée aussi pour le repli no-JS). ?>
        <div class="celya-devis__notice-zone" aria-live="polite">
            <?php if ( 'success' === $devis_state ) : ?>
                <p class="celya-devis__notice celya-devis__notice--success">
                    <?php echo esc_html( celya_devis_message_for_code( 'success' ) ); ?>
                </p>
            <?php elseif ( 'error' === $devis_state ) : ?>
                <p class="celya-devis__notice celya-devis__notice--error">
                    <?php echo esc_html( celya_devis_message_for_code( $error_code ) ); ?>
                </p>
            <?php endif; ?>
        </div>

        <form class="celya-devis__form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" novalidate>

            <input type="hidden" name="action" value="celya_devis_submit" />
            <?php wp_nonce_field( 'celya_devis_form', 'celya_devis_nonce' ); ?>
            <input type="hidden" name="celya_devis_recaptcha_token" value="" />

            <?php // Honeypot : champ leurre masqué, ignoré des humains. ?>
            <div class="celya-devis__hp" aria-hidden="true">
                <label>Ne pas remplir<input type="text" name="celya_devis_website" tabindex="-1" autocomplete="off" /></label>
            </div>

            <?php $req = '<span class="celya-devis__req" aria-hidden="true">*</span>'; ?>

            <div class="celya-devis__grid">

                <p class="celya-devis__field">
                    <label for="celya-devis-nom"><?php esc_html_e( 'Nom et Prénom', 'celya-tailwind' ); ?> <?php echo $req; ?></label>
                    <input type="text" id="celya-devis-nom" name="celya_devis_nom" placeholder="<?php esc_attr_e( 'Nom Prénom', 'celya-tailwind' ); ?>" value="<?php echo $val( 'nom' ); ?>" required />
                    <?php echo $err( 'nom' ); ?>
                </p>

                <p class="celya-devis__field">
                    <label for="celya-devis-email"><?php esc_html_e( 'Adresse mail', 'celya-tailwind' ); ?> <?php echo $req; ?></label>
                    <input type="email" id="celya-devis-email" name="celya_devis_email" placeholder="mail@mail.com" value="<?php echo $val( 'email' ); ?>" required />
                    <?php echo $err( 'email' ); ?>
                </p>

                <p class="celya-devis__field">
                    <label for="celya-devis-telephone"><?php esc_html_e( 'Numéro de téléphone', 'celya-tailwind' ); ?> <?php echo $req; ?></label>
                    <input type="tel" id="celya-devis-telephone" name="celya_devis_telephone" placeholder="06 00 00 00 00" value="<?php echo $val( 'telephone' ); ?>" required />
                    <?php echo $err( 'telephone' ); ?>
                </p>

                <p class="celya-devis__field">
                    <label for="celya-devis-entreprise"><?php esc_html_e( 'Nom de votre entreprise', 'celya-tailwind' ); ?> <?php echo $req; ?></label>
                    <input type="text" id="celya-devis-entreprise" name="celya_devis_entreprise" placeholder="<?php esc_attr_e( 'Nom de votre entreprise', 'celya-tailwind' ); ?>" value="<?php echo $val( 'entreprise' ); ?>" required />
                    <?php echo $err( 'entreprise' ); ?>
                </p>

                <p class="celya-devis__field">
                    <label for="celya-devis-siret"><?php esc_html_e( 'N° Siret', 'celya-tailwind' ); ?> <?php echo $req; ?></label>
                    <input type="text" id="celya-devis-siret" name="celya_devis_siret" inputmode="numeric" placeholder="<?php esc_attr_e( 'Votre numéro Siret', 'celya-tailwind' ); ?>" value="<?php echo $val( 'siret' ); ?>" required />
                    <?php echo $err( 'siret' ); ?>
                </p>

                <p class="celya-devis__field">
                    <label for="celya-devis-secteur"><?php esc_html_e( 'Secteur d\'activité', 'celya-tailwind' ); ?> <?php echo $req; ?></label>
                    <?php echo celya_devis_render_select( 'celya_devis_secteur', 'celya-devis-secteur', 'devis_secteur', (int) ( $old['secteur'] ?? 0 ), __( 'Sélectionnez…', 'celya-tailwind' ), true ); ?>
                    <?php echo $err( 'secteur' ); ?>
                </p>

                <p class="celya-devis__field">
                    <label for="celya-devis-quantite"><?php esc_html_e( 'Quantité estimée', 'celya-tailwind' ); ?> <?php echo $req; ?></label>
                    <input type="text" id="celya-devis-quantite" name="celya_devis_quantite" placeholder="<?php esc_attr_e( '2 KG', 'celya-tailwind' ); ?>" value="<?php echo $val( 'quantite' ); ?>" required />
                    <?php echo $err( 'quantite' ); ?>
                </p>

                <p class="celya-devis__field">
                    <label for="celya-devis-conditionnement"><?php esc_html_e( 'Conditionnement souhaité', 'celya-tailwind' ); ?> <?php echo $req; ?></label>
                    <?php echo celya_devis_render_select( 'celya_devis_conditionnement', 'celya-devis-conditionnement', 'devis_conditionnement', (int) ( $old['conditionnement'] ?? 0 ), __( 'Sélectionnez…', 'celya-tailwind' ), true ); ?>
                    <?php echo $err( 'conditionnement' ); ?>
                </p>

            </div>

            <?php
            $types = get_terms(
                array(
                    'taxonomy'   => 'devis_type_projet',
                    'hide_empty' => false,
                )
            );
            if ( ! is_wp_error( $types ) && ! empty( $types ) ) :
                $selected_type = (int) ( $old['type_projet'] ?? 0 );
                ?>
                <fieldset class="celya-devis__projet">
                    <legend><?php esc_html_e( 'Type de projet', 'celya-tailwind' ); ?> <?php echo $req; ?></legend>
                    <div class="celya-devis__cards">
                        <?php foreach ( $types as $type ) : ?>
                            <?php
                            $icon_url       = celya_devis_get_icon_url( (int) get_term_meta( $type->term_id, 'celya_devis_icon', true ) );
                            $icon_white_url = celya_devis_get_icon_url( (int) get_term_meta( $type->term_id, 'celya_devis_icon_white', true ) );
                            // Repli : si une seule version est fournie, on l'utilise pour les deux états.
                            $icon_default = $icon_url ? $icon_url : $icon_white_url;
                            $icon_active  = $icon_white_url ? $icon_white_url : $icon_url;
                            ?>
                            <label class="celya-devis__card-option">
                                <input
                                    type="radio"
                                    name="celya_devis_type_projet"
                                    value="<?php echo esc_attr( $type->term_id ); ?>"
                                    <?php checked( $selected_type, (int) $type->term_id ); ?>
                                    required
                                />
                                <span class="celya-devis__card-inner">
                                    <?php if ( $icon_default ) : ?>
                                        <img class="celya-devis__card-icon celya-devis__card-icon--default" src="<?php echo esc_url( $icon_default ); ?>" alt="" />
                                        <img class="celya-devis__card-icon celya-devis__card-icon--active" src="<?php echo esc_url( $icon_active ); ?>" alt="" />
                                    <?php endif; ?>
                                    <span class="celya-devis__card-label"><?php echo esc_html( $type->name ); ?></span>
                                </span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                    <?php echo $err( 'type_projet' ); ?>
                </fieldset>
            <?php endif; ?>

            <p class="celya-devis__field celya-devis__field--full">
                <label for="celya-devis-message"><?php esc_html_e( 'Décrivez-nous votre projet', 'celya-tailwind' ); ?> <?php echo $req; ?></label>
                <textarea id="celya-devis-message" name="celya_devis_message" rows="6" placeholder="<?php esc_attr_e( 'Type de produit souhaité, quantités, date de livraison, personnalisation et toute demande spécifique…', 'celya-tailwind' ); ?>" required><?php echo esc_textarea( $old['message'] ?? '' ); ?></textarea>
                <?php echo $err( 'message' ); ?>
            </p>

            <p class="celya-devis__legend"><?php echo $req; ?> <?php esc_html_e( 'Champs obligatoires', 'celya-tailwind' ); ?></p>

            <button type="submit" class="celya-devis__submit"><?php esc_html_e( 'Envoyer ma demande', 'celya-tailwind' ); ?></button>

        </form>
    </div>
</div>
