<?php
/**
 * Configuration des onglets de la fiche produit - Celya
 *
 * Gère l'enregistrement, l'ordre et le contenu des onglets front-end
 * sur les fiches produit WooCommerce.
 *
 * Les données sont récupérées via celya_get_product_specs() définie dans
 * inc/custom-fields.php (chargé avant ce fichier dans functions.php).
 *
 * @package Celya
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Récupère toutes les spécifications d'un produit.
 * Pour une variation, remonte au produit parent si un champ est vide.
 *
 * @param  WC_Product $product
 * @return array
 */
function celya_get_product_specs( $product ) {
    $id        = $product->get_id();
    $parent_id = $product->is_type( 'variation' ) ? $product->get_parent_id() : null;

    $get = function( $key ) use ( $id, $parent_id ) {
        $val = get_post_meta( $id, $key, true );
        // Si vide et c'est une variation, on remonte au parent
        if ( ( ! $val || $val === '[]' ) && $parent_id ) {
            $val = get_post_meta( $parent_id, $key, true );
        }
        return $val;
    };

    $ingredients_raw = $get( '_celya_ingredients' );
    $nutrition_raw   = $get( '_celya_nutrition_table' );

    return array(
        'ingredients'  => $ingredients_raw ? json_decode( $ingredients_raw, true ) : array(),
        'nutrition'    => $nutrition_raw   ? json_decode( $nutrition_raw, true )   : array(),
        'allergenes'   => $get( '_celya_allergenes' ) ?: $get( '_celya_allergens' ),
        'conservation' => $get( '_celya_conservation' ),
        'degustation'  => $get( '_celya_conseil_degustation' ),
    );
}




// =============================================================================
// 1. ENREGISTREMENT ET ORDRE DES ONGLETS
// =============================================================================

/**
 * Personnalise les onglets de la fiche produit.
 *
 * Ordre final :
 * Description (10) | Caractéristiques (20) | Ingrédients & Allergènes (30)
 * | Valeurs nutritionnelles (40) | Conservation (50)
 *
 * Les onglets Ingrédients, Nutrition et Conservation n'apparaissent
 * que si des données sont renseignées pour le produit.
 */
function celya_customize_product_tabs( $tabs ) {
    global $product;

    if ( ! $product ) {
        return $tabs;
    }

    // ── Onglets natifs WooCommerce ──────────────────────────────────────────

    if ( isset( $tabs['description'] ) ) {
        $tabs['description']['title']    = __( 'Description', 'celya' );
        $tabs['description']['priority'] = 10;
    }

    if ( isset( $tabs['additional_information'] ) ) {
        $tabs['additional_information']['title']    = __( 'Caractéristiques', 'celya' );
        $tabs['additional_information']['priority'] = 20;
    }

    // Retirer l'onglet Avis (affiché séparément via un template dédié)
    unset( $tabs['reviews'] );

    // ── Onglets personnalisés (conditionnels) ───────────────────────────────

    $specs = celya_get_product_specs( $product );

    // Ingrédients & Allergènes
    $has_ingredients = ! empty(
        array_filter( $specs['ingredients'] ?? array(), fn( $r ) => ! empty( $r['ingredient'] ) )
    );
    if ( $has_ingredients || ! empty( $specs['allergenes'] ) ) {
        $tabs['celya_ingredients'] = array(
            'title'    => __( 'Ingrédients & Allergènes', 'celya' ),
            'priority' => 30,
            'callback' => 'celya_tab_ingredients_allergens',
        );
    }

    // Valeurs nutritionnelles
    $has_nutrition = ! empty(
        array_filter( $specs['nutrition'] ?? array(), fn( $r ) => ! empty( $r['nutriment'] ) )
    );
    if ( $has_nutrition ) {
        $tabs['celya_nutrition'] = array(
            'title'    => __( 'Valeurs nutritionnelles', 'celya' ),
            'priority' => 40,
            'callback' => 'celya_tab_nutrition',
        );
    }

    // Conservation
    if ( ! empty( $specs['conservation'] ) || ! empty( $specs['degustation'] ) ) {
        $tabs['celya_conservation'] = array(
            'title'    => __( 'Conservation', 'celya' ),
            'priority' => 50,
            'callback' => 'celya_tab_conservation',
        );
    }

    return $tabs;
}
add_filter( 'woocommerce_product_tabs', 'celya_customize_product_tabs', 98 );


// =============================================================================
// 2. CALLBACKS DES ONGLETS → chargement des templates PHP
// =============================================================================

/**
 * Charge un fichier template depuis woocommerce/tabs/{$name}.php
 * La variable $specs est injectée et disponible dans le template.
 *
 * @param string $name  Nom du fichier sans extension.
 * @param array  $specs Données issues de celya_get_product_specs().
 */
function celya_load_tab_template( $name, $specs ) {
    $template = get_template_directory() . '/woocommerce/tabs/' . sanitize_file_name( $name ) . '.php';
    if ( file_exists( $template ) ) {
        include $template;
    }
}

/** Callback → woocommerce/tabs/ingredients_allergens.php */
function celya_tab_ingredients_allergens() {
    global $product;
    $specs = celya_get_product_specs( $product );
    celya_load_tab_template( 'ingredients_allergens', $specs );
}

/** Callback → woocommerce/tabs/nutri.php */
function celya_tab_nutrition() {
    global $product;
    $specs = celya_get_product_specs( $product );
    celya_load_tab_template( 'nutri', $specs );
}

/** Callback → woocommerce/tabs/conservation.php */
function celya_tab_conservation() {
    global $product;
    $specs = celya_get_product_specs( $product );
    celya_load_tab_template( 'conservation', $specs );
}