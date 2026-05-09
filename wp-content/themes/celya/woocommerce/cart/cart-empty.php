<?php
/**
 * Empty cart page
 *
 * @package Celya
 * @version 7.0.1
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_cart_is_empty' );
?>

<div class="celya-cart-empty section-container">

    <div class="celya-cart-empty-icon">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
        </svg>
    </div>

    <h2><?php esc_html_e( 'Votre panier est vide', 'woocommerce' ); ?></h2>

    <p><?php esc_html_e( 'Découvrez nos produits artisanaux et ajoutez vos coups de cœur à votre panier.', 'woocommerce' ); ?></p>

    <?php if ( wc_get_page_id( 'shop' ) > 0 ) : ?>
        <a href="<?php echo esc_url( apply_filters( 'woocommerce_return_to_shop_redirect', wc_get_page_permalink( 'shop' ) ) ); ?>"
           class="return-to-shop">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
            </svg>
            <?php echo esc_html( apply_filters( 'woocommerce_return_to_shop_text', __( 'Voir nos produits', 'woocommerce' ) ) ); ?>
        </a>
    <?php endif; ?>

</div>
