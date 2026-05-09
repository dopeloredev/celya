<?php
/**
 * Edit account form - My Account
 *
 * @package Celya
 * @version 9.7.0
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_edit_account_form' );
?>

<div class="mb-6">
    <h2 class="font-serif text-2xl font-bold text-celya-primary mb-1">Informations du compte</h2>
    <p class="text-sm text-gray-500">Gérez vos informations personnelles et votre mot de passe</p>
</div>

<form class="woocommerce-EditAccountForm edit-account celya-account-form" action="" method="post" <?php do_action( 'woocommerce_edit_account_form_tag' ); ?>>

    <?php do_action( 'woocommerce_edit_account_form_start' ); ?>

    <!-- Informations personnelles -->
    <div class="mb-8">
        <h3 class="font-serif text-base font-semibold text-celya-primary mb-4 flex items-center gap-2">
            <svg class="w-4 h-4 text-celya-orange_dark" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            Informations personnelles
        </h3>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <p class="woocommerce-form-row woocommerce-form-row--first form-row form-row-first !m-0">
                <label for="account_first_name" class="form-label">
                    <?php esc_html_e( 'Prénom', 'woocommerce' ); ?>
                    <span class="text-red-500 ml-0.5">*</span>
                </label>
                <input type="text"
                       class="woocommerce-Input woocommerce-Input--text input-text form-input"
                       name="account_first_name"
                       id="account_first_name"
                       autocomplete="given-name"
                       value="<?php echo esc_attr( $user->first_name ); ?>"
                       aria-required="true" />
            </p>

            <p class="woocommerce-form-row woocommerce-form-row--last form-row form-row-last !m-0">
                <label for="account_last_name" class="form-label">
                    <?php esc_html_e( 'Nom', 'woocommerce' ); ?>
                    <span class="text-red-500 ml-0.5">*</span>
                </label>
                <input type="text"
                       class="woocommerce-Input woocommerce-Input--text input-text form-input"
                       name="account_last_name"
                       id="account_last_name"
                       autocomplete="family-name"
                       value="<?php echo esc_attr( $user->last_name ); ?>"
                       aria-required="true" />
            </p>
        </div>

        <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide !m-0 mt-5">
            <label for="account_display_name" class="form-label">
                <?php esc_html_e( 'Nom affiché', 'woocommerce' ); ?>
                <span class="text-red-500 ml-0.5">*</span>
            </label>
            <input type="text"
                   class="woocommerce-Input woocommerce-Input--text input-text form-input"
                   name="account_display_name"
                   id="account_display_name"
                   aria-describedby="account_display_name_description"
                   value="<?php echo esc_attr( $user->display_name ); ?>"
                   aria-required="true" />
            <span id="account_display_name_description" class="text-xs text-gray-400 mt-1 block">
                <?php esc_html_e( 'Ce nom sera visible dans votre espace client et dans les avis.', 'woocommerce' ); ?>
            </span>
        </p>

        <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide !m-0 mt-5">
            <label for="account_email" class="form-label">
                <?php esc_html_e( 'Adresse e-mail', 'woocommerce' ); ?>
                <span class="text-red-500 ml-0.5">*</span>
            </label>
            <input type="email"
                   class="woocommerce-Input woocommerce-Input--email input-text form-input"
                   name="account_email"
                   id="account_email"
                   autocomplete="email"
                   value="<?php echo esc_attr( $user->user_email ); ?>"
                   aria-required="true" />
        </p>
    </div>

    <?php do_action( 'woocommerce_edit_account_form_fields' ); ?>

    <!-- Changement de mot de passe -->
    <fieldset class="border border-celya-light rounded-xl p-6">
        <legend class="font-serif font-semibold text-celya-primary px-2 flex items-center gap-2 text-base">
            <svg class="w-4 h-4 text-celya-orange_dark" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
            <?php esc_html_e( 'Changer le mot de passe', 'woocommerce' ); ?>
        </legend>

        <div class="space-y-5">
            <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide !m-0">
                <label for="password_current" class="form-label">
                    <?php esc_html_e( 'Mot de passe actuel', 'woocommerce' ); ?>
                    <span class="text-gray-400 font-normal text-xs ml-1">(laisser vide pour ne pas changer)</span>
                </label>
                <input type="password"
                       class="woocommerce-Input woocommerce-Input--password input-text form-input"
                       name="password_current"
                       id="password_current"
                       autocomplete="off" />
            </p>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide !m-0">
                    <label for="password_1" class="form-label">
                        <?php esc_html_e( 'Nouveau mot de passe', 'woocommerce' ); ?>
                    </label>
                    <input type="password"
                           class="woocommerce-Input woocommerce-Input--password input-text form-input"
                           name="password_1"
                           id="password_1"
                           autocomplete="off" />
                </p>

                <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide !m-0">
                    <label for="password_2" class="form-label">
                        <?php esc_html_e( 'Confirmer le nouveau mot de passe', 'woocommerce' ); ?>
                    </label>
                    <input type="password"
                           class="woocommerce-Input woocommerce-Input--password input-text form-input"
                           name="password_2"
                           id="password_2"
                           autocomplete="off" />
                </p>
            </div>
        </div>
    </fieldset>

    <?php do_action( 'woocommerce_edit_account_form' ); ?>

    <div class="mt-6 pt-6 border-t border-celya-light flex items-center gap-4">
        <?php wp_nonce_field( 'save_account_details', 'save-account-details-nonce' ); ?>
        <button type="submit"
                class="btn-celya"
                name="save_account_details"
                value="<?php esc_attr_e( 'Save changes', 'woocommerce' ); ?>">
            <?php esc_html_e( 'Enregistrer les modifications', 'woocommerce' ); ?>
        </button>
        <input type="hidden" name="action" value="save_account_details" />
    </div>

    <?php do_action( 'woocommerce_edit_account_form_end' ); ?>

</form>

<?php do_action( 'woocommerce_after_edit_account_form' ); ?>
