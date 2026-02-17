<?php
/**
 * Configuration des onglets de la fiche produit - Celya
 * 
 * À inclure dans functions.php ou dans inc/woocommerce-single-product-setup.php
 * 
 * @package Celya
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Personnaliser les onglets de la fiche produit
 * Ordre : Description | Caractéristiques | Ingrédients & Allergènes | Valeurs nutritionnelles | Conservation
 */
function celya_customize_product_tabs( $tabs ) {
    
    // ========== ONGLET 1 : DESCRIPTION ==========
    if ( isset( $tabs['description'] ) ) {
        $tabs['description']['title']    = 'Description';
        $tabs['description']['priority'] = 10;
    }
    
    // ========== ONGLET 2 : CARACTÉRISTIQUES ==========
    if ( isset( $tabs['additional_information'] ) ) {
        $tabs['additional_information']['title']    = 'Caractéristiques';
        $tabs['additional_information']['priority'] = 20;
    }
    
    // ========== ONGLET 3 : INGRÉDIENTS & ALLERGÈNES ==========
    $tabs['ingredients'] = array(
        'title'    => 'Ingrédients & Allergènes',
        'priority' => 30,
        'callback' => 'celya_ingredients_tab_content',
    );
    
    // ========== ONGLET 4 : VALEURS NUTRITIONNELLES ==========
    $tabs['nutrition'] = array(
        'title'    => 'Valeurs nutritionnelles',
        'priority' => 40,
        'callback' => 'celya_nutrition_tab_content',
    );
    
    // ========== ONGLET 5 : CONSERVATION ==========
    $tabs['conservation'] = array(
        'title'    => 'Conservation',
        'priority' => 50,
        'callback' => 'celya_conservation_tab_content',
    );
    
    // Retirer l'onglet Avis (affiché séparément)
    unset( $tabs['reviews'] );
    
    return $tabs;
}
add_filter( 'woocommerce_product_tabs', 'celya_customize_product_tabs', 98 );

/**
 * Contenu onglet Ingrédients & Allergènes
 */
function celya_ingredients_tab_content() {
    global $product;
    
    $ingredients = $product->get_meta( '_ingredients' );
    $allergenes  = $product->get_meta( '_allergenes' );
    
    if ( $ingredients ) {
        echo '<div class="ingredients-content prose max-w-none mb-6">';
        echo '<h3 class="font-serif text-lg font-bold text-celya-primary mb-3">Ingrédients</h3>';
        echo '<div class="text-celya-dark leading-relaxed">' . wp_kses_post( wpautop( $ingredients ) ) . '</div>';
        echo '</div>';
    }
    
    if ( $allergenes ) {
        echo '<div class="allergenes-content prose max-w-none">';
        echo '<h3 class="font-serif text-lg font-bold text-celya-primary mb-3">Allergènes</h3>';
        echo '<div class="text-celya-dark leading-relaxed bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded">' . wp_kses_post( wpautop( $allergenes ) ) . '</div>';
        echo '</div>';
    }
    
    if ( ! $ingredients && ! $allergenes ) {
        echo '<p class="text-celya-dark">Information non disponible.</p>';
    }
}

/**
 * Contenu onglet Valeurs nutritionnelles
 */
function celya_nutrition_tab_content() {
    global $product;
    
    $nutrition = $product->get_meta( '_nutrition_table' );
    
    if ( $nutrition ) {
        echo '<div class="nutrition-content">';
        echo '<div class="text-celya-dark leading-relaxed">' . wp_kses_post( wpautop( $nutrition ) ) . '</div>';
        echo '</div>';
    } else {
        echo '<p class="text-celya-dark">Information non disponible.</p>';
    }
}

/**
 * Contenu onglet Conservation
 */
function celya_conservation_tab_content() {
    global $product;
    
    $conservation        = $product->get_meta( '_conservation' );
    $conseil_degustation = $product->get_meta( '_conseil_degustation' );
    
    if ( $conservation ) {
        echo '<div class="conservation-content prose max-w-none mb-6">';
        echo '<div class="text-celya-dark leading-relaxed">' . wp_kses_post( wpautop( $conservation ) ) . '</div>';
        echo '</div>';
    }
    
    if ( $conseil_degustation ) {
        echo '<div class="conseil-content prose max-w-none">';
        echo '<h3 class="font-serif text-lg font-bold text-celya-primary mb-3">Conseil dégustation :</h3>';
        echo '<div class="text-celya-dark leading-relaxed bg-celya-orange_light p-4 rounded">' . wp_kses_post( wpautop( $conseil_degustation ) ) . '</div>';
        echo '</div>';
    }
    
    if ( ! $conservation && ! $conseil_degustation ) {
        echo '<p class="text-celya-dark">Information non disponible.</p>';
    }
}