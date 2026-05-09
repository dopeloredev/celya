<?php
/**
 * Edit address form - My Account
 *
 * @package Celya
 * @version 9.3.0
 */

defined( 'ABSPATH' ) || exit;

$page_title = ( 'billing' === $load_address )
    ? esc_html__( 'Adresse de facturation', 'woocommerce' )
    : esc_html__( 'Adresse de livraison', 'woocommerce' );

do_action( 'woocommerce_before_edit_account_address_form' );
?>

<?php if ( ! $load_address ) : ?>
    <?php wc_get_template( 'myaccount/my-address.php' ); ?>
<?php else : ?>

    <div class="mb-6">
        <a href="<?php echo esc_url( wc_get_endpoint_url( 'edit-address' ) ); ?>"
           class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-celya-primary transition-colors mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Retour aux adresses
        </a>
        <h2 class="font-serif text-2xl font-bold text-celya-primary mb-1">
            <?php echo apply_filters( 'woocommerce_my_account_edit_address_title', $page_title, $load_address ); // phpcs:ignore ?>
        </h2>
    </div>

    <form method="post" novalidate class="celya-account-form">

        <div class="woocommerce-address-fields">
            <?php do_action( "woocommerce_before_edit_address_form_{$load_address}" ); ?>

            <div class="woocommerce-address-fields__field-wrapper grid grid-cols-1 sm:grid-cols-2 gap-x-5">
                <?php
                foreach ( $address as $key => $field ) {
                    woocommerce_form_field( $key, $field, wc_get_post_data_by_key( $key, $field['value'] ) );
                }
                ?>
            </div>

            <?php do_action( "woocommerce_after_edit_address_form_{$load_address}" ); ?>

            <div class="mt-6 pt-6 border-t border-celya-light flex items-center gap-4">
                <button type="submit"
                        name="save_address"
                        value="<?php esc_attr_e( 'Save address', 'woocommerce' ); ?>"
                        class="btn-celya">
                    <?php esc_html_e( 'Enregistrer l\'adresse', 'woocommerce' ); ?>
                </button>
                <a href="<?php echo esc_url( wc_get_endpoint_url( 'edit-address' ) ); ?>"
                   class="text-sm text-gray-400 hover:text-celya-primary transition-colors">
                    Annuler
                </a>
                <?php wp_nonce_field( 'woocommerce-edit_address', 'woocommerce-edit-address-nonce' ); ?>
                <input type="hidden" name="action" value="edit_address" />
            </div>
        </div>

    </form>

<?php endif; ?>

<?php do_action( 'woocommerce_after_edit_account_address_form' ); ?>
