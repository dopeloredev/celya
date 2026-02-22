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
 * Contenu onglet Ingrédients & Allergènes - VERSION AVEC TABLEAUX
 */
function celya_ingredients_tab_content() {
    global $product;
    
    // Vérifier si c'est une variation
    $is_variation = $product->is_type( 'variation' );
    $product_id   = $is_variation ? $product->get_parent_id() : $product->get_id();
    $variation_id = $is_variation ? $product->get_id() : 0;
    
    // ========== INGRÉDIENTS ==========
    $ingredients_data = get_post_meta( $product_id, '_ingredients_data', true );
    
    // Pour les variations, vérifier si elles ont leurs propres ingrédients
    if ( $variation_id ) {
        $variation_ingredients = get_post_meta( $variation_id, '_variation_ingredients', true );
        if ( ! empty( $variation_ingredients ) ) {
            // Si la variation a ses propres ingrédients (texte), l'afficher
            echo '<div class="ingredients-content prose max-w-none mb-6">';
            echo '<h3 class="font-serif text-lg font-bold text-celya-primary mb-3">Ingrédients</h3>';
            echo '<div class="text-celya-dark leading-relaxed">' . wp_kses_post( wpautop( $variation_ingredients ) ) . '</div>';
            echo '</div>';
            $ingredients_data = null; // Ne pas afficher le tableau parent
        }
    }
    
    if ( ! empty( $ingredients_data ) && is_array( $ingredients_data ) ) {
        echo '<div class="ingredients-content mb-6">';
        echo '<h3 class="font-serif text-lg font-bold text-celya-primary mb-3">Ingrédients</h3>';
        echo '<div class="bg-white rounded-lg border border-gray-200 overflow-hidden">';
        echo '<table class="w-full">';
        echo '<thead class="bg-celya-beige">';
        echo '<tr>';
        echo '<th class="px-4 py-3 text-left text-sm font-semibold text-celya-dark">Ingrédient</th>';
        echo '<th class="px-4 py-3 text-right text-sm font-semibold text-celya-dark">Pourcentage</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody class="divide-y divide-gray-200">';
        
        foreach ( $ingredients_data as $ingredient ) {
            $name    = isset( $ingredient['name'] ) ? esc_html( $ingredient['name'] ) : '';
            $percent = isset( $ingredient['percent'] ) ? floatval( $ingredient['percent'] ) : 0;
            
            echo '<tr class="hover:bg-gray-50 transition-colors">';
            echo '<td class="px-4 py-3 text-sm text-celya-dark">' . $name . '</td>';
            echo '<td class="px-4 py-3 text-sm text-celya-dark text-right font-medium">' . number_format( $percent, 2, ',', ' ' ) . '%</td>';
            echo '</tr>';
        }
        
        echo '</tbody>';
        echo '</table>';
        echo '</div>';
        echo '</div>';
    }
    
    // ========== ALLERGÈNES ==========
    $allergenes = get_post_meta( $product_id, '_allergenes', true );
    
    // Pour les variations
    if ( $variation_id ) {
        $variation_allergenes = get_post_meta( $variation_id, '_variation_allergenes', true );
        if ( ! empty( $variation_allergenes ) ) {
            $allergenes = $variation_allergenes;
        }
    }
    
    if ( $allergenes ) {
        echo '<div class="allergenes-content prose max-w-none">';
        echo '<h3 class="font-serif text-lg font-bold text-celya-primary mb-3">Allergènes</h3>';
        echo '<div class="text-celya-dark leading-relaxed bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded">';
        echo '<p class="text-sm font-semibold mb-2">⚠️ Contient ou peut contenir :</p>';
        echo wp_kses_post( wpautop( $allergenes ) );
        echo '</div>';
        echo '</div>';
    }
    
    if ( empty( $ingredients_data ) && empty( $allergenes ) ) {
        echo '<p class="text-celya-dark text-center py-8 text-sm italic">Informations non disponibles.</p>';
    }
}

/**
 * Contenu onglet Valeurs nutritionnelles - VERSION AVEC TABLEAUX
 */
function celya_nutrition_tab_content() {
    global $product;
    
    // Vérifier si c'est une variation
    $is_variation = $product->is_type( 'variation' );
    $product_id   = $is_variation ? $product->get_parent_id() : $product->get_id();
    $variation_id = $is_variation ? $product->get_id() : 0;
    
    $nutrition_data = get_post_meta( $product_id, '_nutrition_data', true );
    
    // Pour les variations, vérifier si elles ont leurs propres valeurs
    if ( $variation_id ) {
        $variation_nutrition = get_post_meta( $variation_id, '_variation_nutrition', true );
        if ( ! empty( $variation_nutrition ) ) {
            // Si la variation a ses propres valeurs (texte), l'afficher
            echo '<div class="nutrition-content">';
            echo '<div class="text-celya-dark leading-relaxed">' . wp_kses_post( wpautop( $variation_nutrition ) ) . '</div>';
            echo '</div>';
            return;
        }
    }
    
    if ( ! empty( $nutrition_data ) && is_array( $nutrition_data ) ) {
        echo '<div class="nutrition-content">';
        echo '<h3 class="font-serif text-lg font-bold text-celya-primary mb-4">Pour 100g</h3>';
        echo '<div class="bg-white rounded-lg border border-gray-200 overflow-hidden">';
        echo '<table class="w-full">';
        echo '<thead class="bg-celya-beige">';
        echo '<tr>';
        echo '<th class="px-4 py-3 text-left text-sm font-semibold text-celya-dark">Nutriment</th>';
        echo '<th class="px-4 py-3 text-right text-sm font-semibold text-celya-dark">Valeur</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody class="divide-y divide-gray-200">';
        
        foreach ( $nutrition_data as $item ) {
            $label = isset( $item['label'] ) ? esc_html( $item['label'] ) : '';
            $value = isset( $item['value'] ) ? esc_html( $item['value'] ) : '';
            $unit  = isset( $item['unit'] ) ? esc_html( $item['unit'] ) : '';
            
            echo '<tr class="hover:bg-gray-50 transition-colors">';
            echo '<td class="px-4 py-3 text-sm text-celya-dark">' . $label . '</td>';
            echo '<td class="px-4 py-3 text-sm text-celya-dark text-right font-medium">' . $value . ' ' . $unit . '</td>';
            echo '</tr>';
        }
        
        echo '</tbody>';
        echo '</table>';
        echo '</div>';
        echo '</div>';
    } else {
        echo '<p class="text-celya-dark text-center py-8 text-sm italic">Informations non disponibles.</p>';
    }
}

/**
 * Contenu onglet Conservation - INCHANGÉ
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
        echo '<p class="text-celya-dark text-center py-8 text-sm italic">Information non disponible.</p>';
    }
}