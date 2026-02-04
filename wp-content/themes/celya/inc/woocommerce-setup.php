<?php
/**
 * WooCommerce Setup - Celya Biscuiterie
 * 
 * @package Celya
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Personnalisations WooCommerce pour Celya
 */

// Nombre de produits par page
function celya_products_per_page() {
    return 12;
}
add_filter( 'loop_shop_per_page', 'celya_products_per_page', 20 );

// Nombre de colonnes
function celya_loop_columns() {
    return 3;
}
add_filter( 'loop_shop_columns', 'celya_loop_columns' );

// Texte bouton panier
function celya_add_to_cart_text() {
    return 'Ajouter au panier';
}
add_filter( 'woocommerce_product_single_add_to_cart_text', 'celya_add_to_cart_text' );
add_filter( 'woocommerce_product_add_to_cart_text', 'celya_add_to_cart_text' );

// Badge artisanal sur produits
function celya_add_artisanal_badge() {
    echo '<span class="absolute top-3 left-3 bg-celya-primary text-white text-xs px-3 py-1 rounded-full font-semibold uppercase z-10">🌾 Artisanal</span>';
}
add_action( 'woocommerce_before_shop_loop_item_title', 'celya_add_artisanal_badge', 5 );

// Message panier vide
function celya_empty_cart_message() {
    return '<p class="text-center text-gray-600 py-8">Votre panier est vide. Découvrez nos délicieux biscuits artisanaux ! 🍪</p>';
}
add_filter( 'wc_empty_cart_message', 'celya_empty_cart_message' );

// Livraison gratuite à partir de 50€
function celya_free_shipping_notice() {
    if ( ! WC()->cart ) {
        return;
    }
    
    $cart_total = WC()->cart->get_subtotal();
    $free_shipping_threshold = 50;
    
    if ( $cart_total < $free_shipping_threshold ) {
        $remaining = $free_shipping_threshold - $cart_total;
        wc_print_notice( 
            sprintf( 
                'Plus que %s pour bénéficier de la livraison gratuite ! 🎁', 
                wc_price( $remaining ) 
            ), 
            'notice' 
        );
    } else {
        wc_print_notice( 'Vous bénéficiez de la livraison gratuite ! 🎉', 'success' );
    }
}
add_action( 'woocommerce_before_cart', 'celya_free_shipping_notice' );
