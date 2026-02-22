<?php
/**
 * Champs personnalisés produits Celya - Avec tableaux éditables et support variations
 * 
 * @package Celya
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * =============================================================================
 * PARTIE 1 : CHAMPS POUR LES PRODUITS SIMPLES
 * =============================================================================
 */

/**
 * Ajouter les champs personnalisés dans l'admin produit
 */
function celya_add_product_custom_fields() {
    global $post;
    
    echo '<div class="options_group">';
    
    // ========== INGRÉDIENTS (Tableau éditable) ==========
    ?>
    <div class="form-field">
        <label><strong><?php _e( 'Ingrédients', 'celya' ); ?></strong></label>
        <div id="celya-ingredients-table" style="margin: 10px 0;">
            <?php celya_render_ingredients_table( $post->ID ); ?>
        </div>
        <button type="button" class="button celya-add-ingredient-row">
            <?php _e( '+ Ajouter un ingrédient', 'celya' ); ?>
        </button>
        <p class="description"><?php _e( 'Liste des ingrédients avec leur pourcentage', 'celya' ); ?></p>
    </div>
    
    <style>
        .celya-ingredient-row {
            display: flex;
            gap: 10px;
            margin-bottom: 8px;
            align-items: center;
        }
        .celya-ingredient-row input[type="text"] {
            flex: 1;
            min-width: 200px;
        }
        .celya-ingredient-row input[type="number"] {
            width: 80px;
        }
        .celya-ingredient-row .button {
            flex-shrink: 0;
        }
    </style>
    
    <script>
    jQuery(document).ready(function($) {
        // Ajouter une ligne d'ingrédient
        $('.celya-add-ingredient-row').on('click', function() {
            var html = '<div class="celya-ingredient-row">' +
                '<input type="text" name="_ingredients_name[]" placeholder="Nom de l\'ingrédient" />' +
                '<input type="number" name="_ingredients_percent[]" placeholder="%" step="0.01" min="0" max="100" />' +
                '<button type="button" class="button celya-remove-row">Supprimer</button>' +
                '</div>';
            $('#celya-ingredients-table').append(html);
        });
        
        // Supprimer une ligne
        $(document).on('click', '.celya-remove-row', function() {
            $(this).closest('.celya-ingredient-row').remove();
        });
    });
    </script>
    <?php
    
    // ========== ALLERGÈNES (Texte simple) ==========
    woocommerce_wp_textarea_input( array(
        'id'          => '_allergenes',
        'label'       => __( 'Allergènes', 'celya' ),
        'placeholder' => 'Allergènes présents...',
        'desc_tip'    => true,
        'description' => __( 'Liste des allergènes', 'celya' ),
    ));
    
    // ========== VALEURS NUTRITIONNELLES (Tableau éditable) ==========
    ?>
    <div class="form-field">
        <label><strong><?php _e( 'Valeurs nutritionnelles (pour 100g)', 'celya' ); ?></strong></label>
        <div id="celya-nutrition-table" style="margin: 10px 0;">
            <?php celya_render_nutrition_table( $post->ID ); ?>
        </div>
        <button type="button" class="button celya-add-nutrition-row">
            <?php _e( '+ Ajouter une valeur', 'celya' ); ?>
        </button>
        <p class="description"><?php _e( 'Valeurs nutritionnelles pour 100g', 'celya' ); ?></p>
    </div>
    
    <script>
    jQuery(document).ready(function($) {
        // Ajouter une ligne nutritionnelle
        $('.celya-add-nutrition-row').on('click', function() {
            var html = '<div class="celya-ingredient-row">' +
                '<input type="text" name="_nutrition_label[]" placeholder="Ex: Énergie, Protéines..." />' +
                '<input type="text" name="_nutrition_value[]" placeholder="Valeur" />' +
                '<input type="text" name="_nutrition_unit[]" placeholder="Unité (kcal, g, mg...)" style="width: 100px;" />' +
                '<button type="button" class="button celya-remove-row">Supprimer</button>' +
                '</div>';
            $('#celya-nutrition-table').append(html);
        });
    });
    </script>
    <?php
    
    // ========== CONSERVATION ==========
    woocommerce_wp_textarea_input( array(
        'id'          => '_conservation',
        'label'       => __( 'Conservation', 'celya' ),
        'placeholder' => 'À conserver dans un endroit sec...',
        'desc_tip'    => true,
        'description' => __( 'Instructions de conservation', 'celya' ),
    ));
    
    // ========== CONSEIL DÉGUSTATION ==========
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
 * Afficher le tableau des ingrédients existants
 */
function celya_render_ingredients_table( $product_id ) {
    $ingredients = get_post_meta( $product_id, '_ingredients_data', true );
    
    if ( empty( $ingredients ) || ! is_array( $ingredients ) ) {
        // Afficher une ligne vide par défaut
        echo '<div class="celya-ingredient-row">
            <input type="text" name="_ingredients_name[]" placeholder="Nom de l\'ingrédient" />
            <input type="number" name="_ingredients_percent[]" placeholder="%" step="0.01" min="0" max="100" />
            <button type="button" class="button celya-remove-row">Supprimer</button>
        </div>';
        return;
    }
    
    foreach ( $ingredients as $ingredient ) {
        $name    = isset( $ingredient['name'] ) ? esc_attr( $ingredient['name'] ) : '';
        $percent = isset( $ingredient['percent'] ) ? esc_attr( $ingredient['percent'] ) : '';
        
        echo '<div class="celya-ingredient-row">
            <input type="text" name="_ingredients_name[]" value="' . $name . '" placeholder="Nom de l\'ingrédient" />
            <input type="number" name="_ingredients_percent[]" value="' . $percent . '" placeholder="%" step="0.01" min="0" max="100" />
            <button type="button" class="button celya-remove-row">Supprimer</button>
        </div>';
    }
}

/**
 * Afficher le tableau des valeurs nutritionnelles existantes
 */
function celya_render_nutrition_table( $product_id ) {
    $nutrition = get_post_meta( $product_id, '_nutrition_data', true );
    
    if ( empty( $nutrition ) || ! is_array( $nutrition ) ) {
        // Lignes par défaut
        $defaults = array(
            array( 'label' => 'Énergie', 'value' => '', 'unit' => 'kcal' ),
            array( 'label' => 'Matières grasses', 'value' => '', 'unit' => 'g' ),
            array( 'label' => 'Glucides', 'value' => '', 'unit' => 'g' ),
            array( 'label' => 'Protéines', 'value' => '', 'unit' => 'g' ),
        );
        $nutrition = $defaults;
    }
    
    foreach ( $nutrition as $item ) {
        $label = isset( $item['label'] ) ? esc_attr( $item['label'] ) : '';
        $value = isset( $item['value'] ) ? esc_attr( $item['value'] ) : '';
        $unit  = isset( $item['unit'] ) ? esc_attr( $item['unit'] ) : '';
        
        echo '<div class="celya-ingredient-row">
            <input type="text" name="_nutrition_label[]" value="' . $label . '" placeholder="Ex: Énergie, Protéines..." />
            <input type="text" name="_nutrition_value[]" value="' . $value . '" placeholder="Valeur" />
            <input type="text" name="_nutrition_unit[]" value="' . $unit . '" placeholder="Unité (kcal, g, mg...)" style="width: 100px;" />
            <button type="button" class="button celya-remove-row">Supprimer</button>
        </div>';
    }
}

/**
 * Sauvegarder les champs personnalisés
 */
function celya_save_product_custom_fields( $post_id ) {
    
    // ========== SAUVEGARDER INGRÉDIENTS ==========
    if ( isset( $_POST['_ingredients_name'] ) && is_array( $_POST['_ingredients_name'] ) ) {
        $ingredients = array();
        
        foreach ( $_POST['_ingredients_name'] as $index => $name ) {
            if ( ! empty( $name ) ) {
                $ingredients[] = array(
                    'name'    => sanitize_text_field( $name ),
                    'percent' => isset( $_POST['_ingredients_percent'][ $index ] ) 
                                 ? floatval( $_POST['_ingredients_percent'][ $index ] ) 
                                 : 0,
                );
            }
        }
        
        update_post_meta( $post_id, '_ingredients_data', $ingredients );
    }
    
    // ========== SAUVEGARDER VALEURS NUTRITIONNELLES ==========
    if ( isset( $_POST['_nutrition_label'] ) && is_array( $_POST['_nutrition_label'] ) ) {
        $nutrition = array();
        
        foreach ( $_POST['_nutrition_label'] as $index => $label ) {
            if ( ! empty( $label ) ) {
                $nutrition[] = array(
                    'label' => sanitize_text_field( $label ),
                    'value' => isset( $_POST['_nutrition_value'][ $index ] ) 
                               ? sanitize_text_field( $_POST['_nutrition_value'][ $index ] ) 
                               : '',
                    'unit'  => isset( $_POST['_nutrition_unit'][ $index ] ) 
                               ? sanitize_text_field( $_POST['_nutrition_unit'][ $index ] ) 
                               : '',
                );
            }
        }
        
        update_post_meta( $post_id, '_nutrition_data', $nutrition );
    }
    
    // ========== SAUVEGARDER CHAMPS SIMPLES ==========
    $simple_fields = array(
        '_allergenes',
        '_conservation',
        '_conseil_degustation',
    );
    
    foreach ( $simple_fields as $field ) {
        if ( isset( $_POST[ $field ] ) ) {
            update_post_meta( $post_id, $field, sanitize_textarea_field( $_POST[ $field ] ) );
        }
    }
}
add_action( 'woocommerce_process_product_meta', 'celya_save_product_custom_fields' );

/**
 * =============================================================================
 * PARTIE 2 : CHAMPS POUR LES VARIATIONS
 * =============================================================================
 */

/**
 * Ajouter les champs aux variations
 */
function celya_add_variation_custom_fields( $loop, $variation_data, $variation ) {
    $variation_id = $variation->ID;
    
    echo '<div class="options_group form-row form-row-full">';
    
    // Ingrédients (simplifié pour les variations)
    woocommerce_wp_textarea_input( array(
        'id'          => "_variation_ingredients_{$loop}",
        'name'        => "_variation_ingredients[{$loop}]",
        'value'       => get_post_meta( $variation_id, '_variation_ingredients', true ),
        'label'       => __( 'Ingrédients (variation)', 'celya' ),
        'placeholder' => 'Ingrédients spécifiques à cette variation...',
        'desc_tip'    => true,
        'description' => __( 'Si vide, utilise les ingrédients du produit parent', 'celya' ),
    ));
    
    // Allergènes
    woocommerce_wp_textarea_input( array(
        'id'          => "_variation_allergenes_{$loop}",
        'name'        => "_variation_allergenes[{$loop}]",
        'value'       => get_post_meta( $variation_id, '_variation_allergenes', true ),
        'label'       => __( 'Allergènes (variation)', 'celya' ),
        'placeholder' => 'Allergènes spécifiques...',
        'desc_tip'    => true,
        'description' => __( 'Si vide, utilise les allergènes du produit parent', 'celya' ),
    ));
    
    // Valeurs nutritionnelles
    woocommerce_wp_textarea_input( array(
        'id'          => "_variation_nutrition_{$loop}",
        'name'        => "_variation_nutrition[{$loop}]",
        'value'       => get_post_meta( $variation_id, '_variation_nutrition', true ),
        'label'       => __( 'Valeurs nutritionnelles (variation)', 'celya' ),
        'placeholder' => 'Valeurs spécifiques pour 100g...',
        'desc_tip'    => true,
        'description' => __( 'Si vide, utilise les valeurs du produit parent', 'celya' ),
    ));
    
    echo '</div>';
}
add_action( 'woocommerce_product_after_variable_attributes', 'celya_add_variation_custom_fields', 10, 3 );

/**
 * Sauvegarder les champs des variations
 */
function celya_save_variation_custom_fields( $variation_id, $i ) {
    
    // Ingrédients
    if ( isset( $_POST['_variation_ingredients'][ $i ] ) ) {
        update_post_meta( 
            $variation_id, 
            '_variation_ingredients', 
            sanitize_textarea_field( $_POST['_variation_ingredients'][ $i ] ) 
        );
    }
    
    // Allergènes
    if ( isset( $_POST['_variation_allergenes'][ $i ] ) ) {
        update_post_meta( 
            $variation_id, 
            '_variation_allergenes', 
            sanitize_textarea_field( $_POST['_variation_allergenes'][ $i ] ) 
        );
    }
    
    // Valeurs nutritionnelles
    if ( isset( $_POST['_variation_nutrition'][ $i ] ) ) {
        update_post_meta( 
            $variation_id, 
            '_variation_nutrition', 
            sanitize_textarea_field( $_POST['_variation_nutrition'][ $i ] ) 
        );
    }
}
add_action( 'woocommerce_save_product_variation', 'celya_save_variation_custom_fields', 10, 2 );