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
 * Personnaliser les onglets de la fiche produit
 */
function celya_customize_product_tabs( $tabs ) {
    
    // Renommer l'onglet Description
    if ( isset( $tabs['description'] ) ) {
        $tabs['description']['title'] = 'Description';
        $tabs['description']['priority'] = 10;
    }
    
    // Renommer l'onglet Informations complémentaires
    if ( isset( $tabs['additional_information'] ) ) {
        $tabs['additional_information']['title'] = 'Caractéristiques';
        $tabs['additional_information']['priority'] = 20;
    }
    
    // Ajouter un onglet "Ingrédients & Allergènes"
    $tabs['ingredients'] = array(
        'title'    => 'Ingrédients & Allergènes',
        'priority' => 30,
        'callback' => 'celya_ingredients_tab_content',
    );
    
    // Ajouter un onglet "Valeurs nutritionnelles"
    $tabs['nutrition'] = array(
        'title'    => 'Valeurs nutritionnelles',
        'priority' => 40,
        'callback' => 'celya_nutrition_tab_content',
    );
    
    // Ajouter un onglet "Conservation"
    $tabs['conservation'] = array(
        'title'    => 'Conservation',
        'priority' => 50,
        'callback' => 'celya_conservation_tab_content',
    );
    
    // Renommer et repositionner l'onglet Avis
    if ( isset( $tabs['reviews'] ) ) {
        $tabs['reviews']['title'] = 'Avis';
        $tabs['reviews']['priority'] = 60;
    }
    
    return $tabs;
}
add_filter( 'woocommerce_product_tabs', 'celya_customize_product_tabs', 98 );

/**
 * Contenu onglet Ingrédients & Allergènes
 */
function celya_ingredients_tab_content() {
    global $product;
    
    $ingredients = $product->get_meta( '_ingredients' );
    $allergenes = $product->get_meta( '_allergenes' );
    
    if ( $ingredients ) {
        echo '<div class="ingredients-content prose max-w-none">';
        echo '<h3 class="font-serif text-lg font-bold text-celya-primary mb-3">Ingrédients</h3>';
        echo '<div class="text-celya-dark leading-relaxed">' . wp_kses_post( wpautop( $ingredients ) ) . '</div>';
        echo '</div>';
    }
    
    if ( $allergenes ) {
        echo '<div class="allergenes-content prose max-w-none mt-6">';
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
    
    $conservation = $product->get_meta( '_conservation' );
    $conseil_degustation = $product->get_meta( '_conseil_degustation' );
    
    if ( $conservation ) {
        echo '<div class="conservation-content prose max-w-none">';
        echo '<div class="text-celya-dark leading-relaxed">' . wp_kses_post( wpautop( $conservation ) ) . '</div>';
        echo '</div>';
    }
    
    if ( $conseil_degustation ) {
        echo '<div class="conseil-content prose max-w-none mt-6">';
        echo '<h3 class="font-serif text-lg font-bold text-celya-primary mb-3">Conseil dégustation :</h3>';
        echo '<div class="text-celya-dark leading-relaxed bg-celya-orange_light p-4 rounded">' . wp_kses_post( wpautop( $conseil_degustation ) ) . '</div>';
        echo '</div>';
    }
    
    if ( ! $conservation && ! $conseil_degustation ) {
        echo '<p class="text-celya-dark">Information non disponible.</p>';
    }
}

/**
 * Ajouter des champs personnalisés dans l'admin produit
 */
function celya_add_product_custom_fields() {
    global $post;
    
    echo '<div class="options_group">';
    
    // Prix au kilo
    woocommerce_wp_text_input( array(
        'id'          => '_price_per_kg',
        'label'       => __( 'Prix au kilo', 'celya' ),
        'placeholder' => '89€',
        'desc_tip'    => true,
        'description' => __( 'Prix au kilogramme (ex: "89€")', 'celya' ),
    ));
    
    // Ingrédients
    woocommerce_wp_textarea_input( array(
        'id'          => '_ingredients',
        'label'       => __( 'Ingrédients', 'celya' ),
        'placeholder' => 'Liste des ingrédients...',
        'desc_tip'    => true,
        'description' => __( 'Liste complète des ingrédients', 'celya' ),
    ));
    
    // Allergènes
    woocommerce_wp_textarea_input( array(
        'id'          => '_allergenes',
        'label'       => __( 'Allergènes', 'celya' ),
        'placeholder' => 'Allergènes présents...',
        'desc_tip'    => true,
        'description' => __( 'Liste des allergènes', 'celya' ),
    ));
    
    // Valeurs nutritionnelles
    woocommerce_wp_textarea_input( array(
        'id'          => '_nutrition_table',
        'label'       => __( 'Valeurs nutritionnelles', 'celya' ),
        'placeholder' => 'Tableau nutritionnel...',
        'desc_tip'    => true,
        'description' => __( 'Tableau des valeurs nutritionnelles pour 100g', 'celya' ),
    ));
    
    // Conservation
    woocommerce_wp_textarea_input( array(
        'id'          => '_conservation',
        'label'       => __( 'Conservation', 'celya' ),
        'placeholder' => 'Conditions de conservation...',
        'desc_tip'    => true,
        'description' => __( 'Instructions de conservation', 'celya' ),
    ));
    
    // Conseil dégustation
    woocommerce_wp_textarea_input( array(
        'id'          => '_conseil_degustation',
        'label'       => __( 'Conseil dégustation', 'celya' ),
        'placeholder' => 'Conseils de dégustation...',
        'desc_tip'    => true,
        'description' => __( 'Conseils pour déguster le produit', 'celya' ),
    ));
    
    echo '</div>';
}
add_action( 'woocommerce_product_options_general_product_data', 'celya_add_product_custom_fields' );

/**
 * Sauvegarder les champs personnalisés
 */
function celya_save_product_custom_fields( $post_id ) {
    $fields = array(
        '_price_per_kg',
        '_ingredients',
        '_allergenes',
        '_nutrition_table',
        '_conservation',
        '_conseil_degustation',
    );
    
    foreach ( $fields as $field ) {
        if ( isset( $_POST[ $field ] ) ) {
            update_post_meta( $post_id, $field, sanitize_textarea_field( $_POST[ $field ] ) );
        }
    }
}
add_action( 'woocommerce_process_product_meta', 'celya_save_product_custom_fields' );

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