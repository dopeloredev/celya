<?php
/**
 * Configuration WooCommerce pour page catégorie - Celya
 * 
 * À ajouter dans functions.php :
 * require_once get_template_directory() . '/inc/woocommerce-setup.php';
 * 
 * @package Celya
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Charger les CSS WooCommerce page par page.
 *
 * Chaque feuille output-*.css est compilée depuis sa propre entrée
 * input-*.css (voir package.json) et n'est chargée que sur la page
 * concernée. Le CSS global (utilitaires Tailwind + composants partagés)
 * reste dans output.css, enqueué partout sous le handle « celya-tailwind ».
 */
function celya_enqueue_woocommerce_styles() {
    if ( ! class_exists( 'WooCommerce' ) ) {
        return;
    }

    // Carte : condition d'affichage => fichier output dédié.
    $page_styles = array(
        'celya-wc-product'  => array( is_product(),                                   'output-product.css'  ),
        'celya-wc-category' => array( is_shop() || is_product_taxonomy(),             'output-category.css' ),
        'celya-wc-cart'     => array( is_cart(),                                       'output-cart.css'     ),
        'celya-wc-checkout' => array( is_checkout(),                                   'output-checkout.css' ),
        'celya-wc-account'  => array( is_account_page(),                               'output-account.css'  ),
    );

    foreach ( $page_styles as $handle => $config ) {
        list( $condition, $filename ) = $config;

        if ( ! $condition ) {
            continue;
        }

        $path = get_template_directory() . '/assets/css/' . $filename;

        if ( ! file_exists( $path ) ) {
            continue;
        }

        wp_enqueue_style(
            $handle,
            get_template_directory_uri() . '/assets/css/' . $filename,
            array( 'celya-tailwind' ), // dépend du global → cascade correcte
            filemtime( $path )
        );
    }
}
add_action( 'wp_enqueue_scripts', 'celya_enqueue_woocommerce_styles', 20 );

/**
 * Définir 3 colonnes pour la grille de produits
 */
function celya_loop_columns() {
    return 3;
}
add_filter( 'loop_shop_columns', 'celya_loop_columns' );

/**
 * Nombre de produits par page
 */
function celya_products_per_page() {
    return 9; // 3x3 grille
}
add_filter( 'loop_shop_per_page', 'celya_products_per_page', 20 );

/**
 * Personnaliser le texte du bouton "Ajouter au panier"
 * Retourne une chaîne vide car on utilise uniquement l'icône SVG via CSS
 */
function celya_custom_add_to_cart_text() {
    return ''; // Vide - l'icône est affichée via CSS ::before
}
add_filter( 'woocommerce_product_add_to_cart_text', 'celya_custom_add_to_cart_text' );
add_filter( 'woocommerce_product_single_add_to_cart_text', '__return_empty_string' );

/**
 * Ajouter un attribut title au bouton pour l'accessibilité
 */
function celya_add_to_cart_button_attributes( $args, $product ) {
    $args['attributes']['title'] = 'Ajouter au panier';
    $args['attributes']['aria-label'] = 'Ajouter ' . $product->get_name() . ' au panier';
    return $args;
}
add_filter( 'woocommerce_loop_add_to_cart_args', 'celya_add_to_cart_button_attributes', 10, 2 );

/**
 * Désactiver le zoom sur les images produits (optionnel)
 */
remove_theme_support( 'wc-product-gallery-zoom' );

/**
 * Modifier l'ordre des éléments sur la page archive
 */
function celya_customize_shop_loop() {
    // Supprimer le badge "Sale" par défaut
    remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10 );
    
    // Supprimer les évaluations (étoiles) si non utilisées
    remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );
}
add_action( 'init', 'celya_customize_shop_loop' );

/**
 * Ajouter un champ personnalisé pour le suffixe de prix (ex: "/ Sachet de 100g")
 */
function celya_add_price_suffix_field() {
    woocommerce_wp_text_input( array(
        'id'          => '_price_suffix',
        'label'       => __( 'Suffixe de prix', 'celya' ),
        'placeholder' => '/ Sachet de 100g',
        'desc_tip'    => true,
        'description' => __( 'Texte affiché sous le prix (ex: "/ Sachet de 100g")', 'celya' ),
    ));
}
add_action( 'woocommerce_product_options_pricing', 'celya_add_price_suffix_field' );

/**
 * Sauvegarder le champ personnalisé
 */
function celya_save_price_suffix_field( $post_id ) {
    $price_suffix = isset( $_POST['_price_suffix'] ) ? sanitize_text_field( $_POST['_price_suffix'] ) : '';
    update_post_meta( $post_id, '_price_suffix', $price_suffix );
}
add_action( 'woocommerce_process_product_meta', 'celya_save_price_suffix_field' );

/**
 * Personnaliser le message "Aucun produit trouvé"
 */
function celya_no_products_found() {
    echo '<div class="woocommerce-info text-center py-12">';
    echo '<p class="text-lg mb-4">🍪 Aucun produit ne correspond à votre recherche.</p>';
    echo '<a href="' . esc_url( get_permalink( wc_get_page_id( 'shop' ) ) ) . '" class="btn-celya inline-block">';
    echo 'Voir tous les produits';
    echo '</a>';
    echo '</div>';
}
add_action( 'woocommerce_no_products_found', 'celya_no_products_found' );

/**
 * Ajouter les classes Tailwind au formulaire d'ajout au panier
 */
function celya_quantity_input_classes( $classes ) {
    $classes[] = 'w-16';
    $classes[] = 'text-center';
    return $classes;
}
add_filter( 'woocommerce_quantity_input_classes', 'celya_quantity_input_classes' );

/**
 * Support des images de haute qualité
 */
function celya_woocommerce_image_dimensions() {
    // Image principale produit
    update_option( 'woocommerce_single_image_width', 800 );
    
    // Vignette produit
    update_option( 'woocommerce_thumbnail_image_width', 400 );
    
    // Ratio d'image 1:1 (carré)
    update_option( 'woocommerce_thumbnail_cropping', '1:1' );
}
add_action( 'after_switch_theme', 'celya_woocommerce_image_dimensions' );

/**
 * Personnaliser les options de tri
 */
function celya_custom_catalog_orderby( $sortby ) {
    $sortby['menu_order'] = 'Tri par défaut';  // "Default sorting"
    $sortby['popularity'] = 'Plus populaires';
    $sortby['date'] = 'Plus récents';
    $sortby['price'] = 'Prix : croissant';
    $sortby['price-desc'] = 'Prix : décroissant';
    
    // Supprimer "Note moyenne" si non utilisé
    unset( $sortby['rating'] );
    
    return $sortby;
}
add_filter( 'woocommerce_catalog_orderby', 'celya_custom_catalog_orderby' );
add_filter( 'woocommerce_default_catalog_orderby_options', 'celya_custom_catalog_orderby' );

/**
 * Afficher le nombre de produits dans les catégories (sidebar)
 */
function celya_product_categories_widget_args( $list_args ) {
    $list_args['show_count'] = true;
    return $list_args;
}
add_filter( 'woocommerce_product_categories_widget_args', 'celya_product_categories_widget_args' );


// Badge artisanal sur produits
function celya_add_artisanal_badge() {
    echo '<span class="absolute top-3 left-3 bg-celya-primary text-white text-xs px-3 py-1 rounded-full font-semibold uppercase z-10">🌾 Artisanal</span>';
}
add_action( 'woocommerce_before_shop_loop_item_title', 'celya_add_artisanal_badge', 5 );

// Message panier vide
function celya_empty_cart_message() {
    return '<p class="text-center text-gray-600 py-8">Votre panier est vide. Découvrez nos délicieux biscuits artisanaux ! 🍪</p>';
}
add_filter( 'wc_empty_cart_message', 'celya_empty_cart_message' );

// Livraison gratuite à partir de 50€
function celya_free_shipping_notice() {
    if ( ! WC()->cart ) {
        return;
    }
    
    $cart_total = WC()->cart->get_subtotal();
    $free_shipping_threshold = 50;
    
    if ( $cart_total < $free_shipping_threshold ) {
        $remaining = $free_shipping_threshold - $cart_total;
        wc_print_notice( 
            sprintf( 
                'Plus que %s pour bénéficier de la livraison gratuite ! 🎁', 
                wc_price( $remaining ) 
            ), 
            'notice' 
        );
    } else {
        wc_print_notice( 'Vous bénéficiez de la livraison gratuite ! 🎉', 'success' );
    }
}
add_action( 'woocommerce_before_cart', 'celya_free_shipping_notice' );


// Masque l'onglet "Downloads" (téléchargements) dans la navigation de l'espace client.
// La surcharge de template woocommerce/myaccount/downloads.php a été retirée en conséquence.
function celya_remove_downloads_account_menu( $items ) {
    unset( $items['downloads'] );
    return $items;
}
add_filter( 'woocommerce_account_menu_items', 'celya_remove_downloads_account_menu' );