<?php
/**
 * Champs personnalisés produits
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Ajouter les champs dans l'admin
function celya_add_custom_product_fields() {
    echo '<div class="options_group">';
    
    woocommerce_wp_textarea_input( array(
        'id'          => '_celya_ingredients',
        'label'       => 'Ingrédients',
        'placeholder' => 'Farine de sarrasin, noisettes...',
        'desc_tip'    => true,
        'description' => 'Liste des ingrédients du produit',
    ));
    
    woocommerce_wp_text_input( array(
        'id'          => '_celya_allergens',
        'label'       => 'Allergènes',
        'placeholder' => 'Fruits à coques, gluten...',
        'desc_tip'    => true,
    ));
    
    echo '</div>';
}
add_action( 'woocommerce_product_options_general_product_data', 'celya_add_custom_product_fields' );

// Sauvegarder les champs
function celya_save_custom_product_fields( $post_id ) {
    $ingredients = isset( $_POST['_celya_ingredients'] ) ? sanitize_textarea_field( $_POST['_celya_ingredients'] ) : '';
    $allergens = isset( $_POST['_celya_allergens'] ) ? sanitize_text_field( $_POST['_celya_allergens'] ) : '';
    
    update_post_meta( $post_id, '_celya_ingredients', $ingredients );
    update_post_meta( $post_id, '_celya_allergens', $allergens );
}
add_action( 'woocommerce_process_product_meta', 'celya_save_custom_product_fields' );

// Afficher sur la page produit
function celya_display_product_custom_fields() {
    global $product;
    
    $ingredients = get_post_meta( $product->get_id(), '_celya_ingredients', true );
    $allergens = get_post_meta( $product->get_id(), '_celya_allergens', true );
    
    if ( $ingredients || $allergens ) {
        echo '<div class="celya-product-info mt-6 p-4 bg-celya-light rounded-lg">';
        
        if ( $ingredients ) {
            echo '<div class="mb-3">';
            echo '<strong class="text-celya-primary">🌾 Ingrédients :</strong> ';
            echo '<span class="text-gray-700">' . esc_html( $ingredients ) . '</span>';
            echo '</div>';
        }
        
        if ( $allergens ) {
            echo '<div>';
            echo '<strong class="text-celya-primary">⚠️ Allergènes :</strong> ';
            echo '<span class="text-gray-700">' . esc_html( $allergens ) . '</span>';
            echo '</div>';
        }
        
        echo '</div>';
    }
}
add_action( 'woocommerce_single_product_summary', 'celya_display_product_custom_fields', 25 );