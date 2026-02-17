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
 * Ajouter des champs personnalisés dans l'admin produit
 */
/**
 * Ajouter les champs personnalisés dans l'admin produit
 */
function celya_add_product_custom_fields() {
    
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
        'placeholder' => 'Pour 100g : ...',
        'desc_tip'    => true,
        'description' => __( 'Tableau des valeurs nutritionnelles pour 100g', 'celya' ),
    ));
    
    // Conservation
    woocommerce_wp_textarea_input( array(
        'id'          => '_conservation',
        'label'       => __( 'Conservation', 'celya' ),
        'placeholder' => 'À conserver dans un endroit sec...',
        'desc_tip'    => true,
        'description' => __( 'Instructions de conservation', 'celya' ),
    ));
    
    // Conseil dégustation
    woocommerce_wp_textarea_input( array(
        'id'          => '_conseil_degustation',
        'label'       => __( 'Conseil dégustation', 'celya' ),
        'placeholder' => 'À déguster avec...',
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