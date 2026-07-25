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
 * Thème couleur conditionnel de la fiche produit.
 *
 * Pose une classe body.theme-<slug> selon le tag du produit. Cette classe
 * réassigne les variables --celya-accent-* (définies dans woo-single-product.css),
 * ce qui recolore toute la fiche produit sans dupliquer de template.
 * Un produit sans tag connu conserve l'accent orange par défaut (:root).
 */
function celya_single_product_theme_body_class( $classes ) {
    if ( ! is_product() ) {
        return $classes;
    }

    // Source de vérité partagée avec la coloration des déclinaisons.
    $theme = celya_get_product_theme_slug( get_queried_object_id() );
    if ( $theme ) {
        $classes[] = 'theme-' . $theme;
    }

    return $classes;
}
add_filter( 'body_class', 'celya_single_product_theme_body_class' );

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
 * Galerie : on désactive le badge solde natif (géré dans product-image.php)
 * et les scripts de galerie non utilisés (slider, lightbox, zoom).
 */
add_action( 'init', function () {
    remove_theme_support( 'wc-product-gallery-zoom' );
    remove_theme_support( 'wc-product-gallery-lightbox' );
    remove_theme_support( 'wc-product-gallery-slider' );
} );
remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10 );

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