<?php
/**
 * Configuration WooCommerce pour le file d'ariane - Celya
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Personnaliser l'affichage du breadcrumb WooCommerce
 */
function celya_custom_breadcrumb() {
    if ( function_exists( 'woocommerce_breadcrumb' ) ) {
        echo '<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8">';
        woocommerce_breadcrumb( array(
            'delimiter'   => '&nbsp;/&nbsp;',
            'wrap_before' => '<nav class="woocommerce-breadcrumb text-sm text-celya-dark mb-0" aria-label="Breadcrumb">',
            'wrap_after'  => '</nav>',
            'before'      => '',
            'after'       => '',
            'home'        => 'Accueil',
        ) );
        echo '</div>';
    }
}

// Supprimer le breadcrumb par défaut et ajouter le custom
remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );
add_action( 'woocommerce_before_main_content', 'celya_custom_breadcrumb', 20 );