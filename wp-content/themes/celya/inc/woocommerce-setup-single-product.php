<?php
/**
 * Configuration WooCommerce pour la fiche produit - Celya
 * 
 * À ajouter dans functions.php ou woocommerce-category-setup.php
 * 
 * @package Celya
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * ========================================
 * CONFIGURATION FICHE PRODUIT
 * ========================================
 */

/**
 * Modifier le texte du bouton Ajouter au panier sur la fiche produit
 */
function celya_single_add_to_cart_text() {
    return 'Ajouter au panier';
}
add_filter( 'woocommerce_product_single_add_to_cart_text', 'celya_single_add_to_cart_text' );

/**
 * Nombre de produits "Vous aimerez aussi"
 */
function celya_related_products_args( $args ) {
    $args['posts_per_page'] = 3;
    $args['columns'] = 3;
    return $args;
}
add_filter( 'woocommerce_output_related_products_args', 'celya_related_products_args' );

/**
 * Personnaliser le titre "Vous aimerez aussi"
 */
function celya_related_products_heading() {
    return 'Vous aimerez aussi';
}
add_filter( 'woocommerce_product_related_products_heading', 'celya_related_products_heading' );

/**
 * Activer la galerie lightbox pour les images produit
 */
add_theme_support( 'wc-product-gallery-lightbox' );
add_theme_support( 'wc-product-gallery-slider' );

/**
 * Désactiver le schéma markup par défaut (optionnel)
 */
add_filter( 'woocommerce_structured_data_product', '__return_false' );

/**
 * Ajouter le poids dans les données de variation disponibles en JavaScript
 */
function celya_add_weight_to_variation_data( $data, $product, $variation ) {
    // Récupérer le poids de la variation
    $weight = $variation->get_weight();
    
    if ( $weight ) {
        // Formater le poids avec l'unité (g ou kg)
        if ( $weight < 1 ) {
            // Moins de 1kg = afficher en grammes
            $weight_g = $weight * 1000;
            $data['weight'] = round( $weight_g ) . 'g';
        } else {
            // 1kg ou plus = afficher en kg
            $data['weight'] = $weight . 'kg';
        }
        
        // Stocker aussi le poids brut (en kg) pour les calculs
        $data['weight_value'] = floatval( $weight );
    } else {
        $data['weight'] = '100g'; // Valeur par défaut
        $data['weight_value'] = 0.1;
    }
    
    return $data;
}
add_filter( 'woocommerce_available_variation', 'celya_add_weight_to_variation_data', 10, 3 );