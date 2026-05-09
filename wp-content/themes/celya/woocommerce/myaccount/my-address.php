<?php
/**
 * My Addresses - My Account
 *
 * @package Celya
 * @version 9.3.0
 */

defined( 'ABSPATH' ) || exit;

$customer_id = get_current_user_id();

if ( ! wc_ship_to_billing_address_only() && wc_shipping_enabled() ) {
    $get_addresses = apply_filters(
        'woocommerce_my_account_get_addresses',
        array(
            'billing'  => __( 'Adresse de facturation', 'woocommerce' ),
            'shipping' => __( 'Adresse de livraison', 'woocommerce' ),
        ),
        $customer_id
    );
} else {
    $get_addresses = apply_filters(
        'woocommerce_my_account_get_addresses',
        array(
            'billing' => __( 'Adresse de facturation', 'woocommerce' ),
        ),
        $customer_id
    );
}
?>

<div class="celya-account-section-header mb-6">
    <h2 class="font-serif text-2xl font-bold text-celya-primary mb-1">Mes adresses</h2>
    <p class="text-sm text-gray-500">
        <?php echo apply_filters( 'woocommerce_my_account_my_address_description', esc_html__( 'Ces adresses seront utilisées par défaut lors du paiement.', 'woocommerce' ) ); // phpcs:ignore ?>
    </p>
</div>

<div class="grid grid-cols-1 <?php echo count( $get_addresses ) > 1 ? 'md:grid-cols-2' : ''; ?> gap-6">
    <?php foreach ( $get_addresses as $name => $address_title ) :
        $address = wc_get_account_formatted_address( $name );
        $icon    = 'billing' === $name
            ? '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>'
            : '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>';
    ?>
    <div class="bg-white border border-celya-light rounded-2xl p-6 shadow-celya-sm flex flex-col">

        <!-- En-tête -->
        <div class="flex items-center justify-between mb-4 pb-4 border-b border-celya-light">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-celya-orange_light flex items-center justify-center text-celya-primary">
                    <?php echo $icon; // phpcs:ignore ?>
                </div>
                <h3 class="font-serif text-base font-semibold text-celya-primary"><?php echo esc_html( $address_title ); ?></h3>
            </div>
            <a href="<?php echo esc_url( wc_get_endpoint_url( 'edit-address', $name ) ); ?>"
               class="flex items-center gap-1.5 text-xs font-semibold text-celya-primary hover:text-celya-orange_dark transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                <?php echo $address ? esc_html__( 'Modifier', 'celya' ) : esc_html__( 'Ajouter', 'celya' ); ?>
            </a>
        </div>

        <!-- Contenu adresse -->
        <div class="flex-1">
            <?php if ( $address ) : ?>
                <address class="not-italic text-sm text-celya-dark leading-relaxed">
                    <?php echo wp_kses_post( $address ); ?>
                </address>
                <?php do_action( 'woocommerce_my_account_after_my_address', $name ); ?>
            <?php else : ?>
                <div class="flex flex-col items-center justify-center py-6 text-center">
                    <p class="text-sm text-gray-400 mb-3">Aucune adresse renseignée.</p>
                    <a href="<?php echo esc_url( wc_get_endpoint_url( 'edit-address', $name ) ); ?>"
                       class="text-xs font-semibold text-celya-primary hover:text-celya-orange_dark transition-colors underline underline-offset-2">
                        Ajouter cette adresse
                    </a>
                </div>
            <?php endif; ?>
        </div>

    </div>
    <?php endforeach; ?>
</div>
