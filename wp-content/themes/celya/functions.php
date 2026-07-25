<?php
/**
 * Celya Tailwind Theme Functions
 * 
 * @package Celya_Tailwind
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Charger les classes personnalisées
require_once get_template_directory() . '/inc/class-loader.php';


require_once get_template_directory() . '/inc/woocommerce-setup.php';
require_once get_template_directory() . '/inc/woocommerce-custom-fields.php';
require_once get_template_directory() . '/inc/woocommerce-setup-single-product-tabs.php';
require_once get_template_directory() . '/inc/woocommerce-setup-breadcrumb.php';
require_once get_template_directory() . '/inc/woocommerce-setup-single-product.php';
require_once get_template_directory() . '/inc/woocommerce-declinaisons.php';
require_once get_template_directory() . '/inc/celya-block-registration.php';
require_once get_template_directory() . '/inc/devis/devis-cpt.php';
require_once get_template_directory() . '/inc/devis/devis-admin.php';
require_once get_template_directory() . '/inc/devis/devis-form-handler.php';
require_once get_template_directory() . '/inc/devis/devis-smtp.php';
require_once get_template_directory() . '/inc/realisations.php';
require_once get_template_directory() . '/inc/variation-gallery.php';
/**
 * 1. SETUP DU THÈME
 */
function celya_theme_setup() {
    // Support des fonctionnalités WordPress
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
    ));
    add_theme_support( 'custom-logo' );
    add_theme_support( 'responsive-embeds' );
    
    // Tailles d'images personnalisées
    add_image_size( 'celya-realisation-sm',  200, 200, true );
    add_image_size( 'celya-realisation-md',  300, 300, true );
    add_image_size( 'celya-product-thumb',   400, 400, true );
    add_image_size( 'celya-product-large',   800, 800, true );
    add_image_size( 'celya-hero',           1920, 800, true );
    
    // Menus
    register_nav_menus( array(
        'primary'          => __( 'Menu Principal', 'celya-tailwind' ),
        'footer-produits'  => __( 'Footer - Produits', 'celya-tailwind' ),
        'footer-services'  => __( 'Footer - Services', 'celya-tailwind' ),
        'footer-apropos'  => __( 'Footer - A propos', 'celya-tailwind' ),
        'footer-informations'  => __( 'Footer - Informations', 'celya-tailwind' )
    ));

    // Support du theme.json / editor styles
    add_theme_support( 'align-wide' ); // Active les boutons Large + Pleine largeur dans Gutenberg
    add_theme_support( 'editor-styles' );
    add_theme_support( 'wp-block-styles' );
    add_theme_support( 'align-wide' );
    
    // Désactiver la palette de couleurs par défaut de WP
    add_theme_support( 'disable-custom-colors' );
    // Désactiver les tailles de police par défaut de WP
    add_theme_support( 'disable-custom-font-sizes' );
}
add_action( 'after_setup_theme', 'celya_theme_setup' );

/**
 * Intégrer les variables du thèmes dans gutenberg
 */
function celya_editor_styles() {
    add_editor_style( 'assets/css/output.css' );
}
add_action( 'after_setup_theme', 'celya_editor_styles' );

/**
 * Ajouter les options de contact dans le customizer
 */
function celya_customize_register($wp_customize) {
    
    // Section Contact
    $wp_customize->add_section('celya_contact', array(
        'title'    => __('Informations de contact', 'celya'),
        'priority' => 30,
    ));
    
    // Email
    $wp_customize->add_setting('celya_email', array(
        'default'           => 'contact@celya.fr',
        'sanitize_callback' => 'sanitize_email',
    ));
    $wp_customize->add_control('celya_email', array(
        'label'   => __('Email', 'celya'),
        'section' => 'celya_contact',
        'type'    => 'email',
    ));
    
    // Téléphone
    $wp_customize->add_setting('celya_phone', array(
        'default'           => '02 41 00 00 00',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('celya_phone', array(
        'label'   => __('Téléphone', 'celya'),
        'section' => 'celya_contact',
        'type'    => 'text',
    ));
    
    // Adresse
    $wp_customize->add_setting('celya_address', array(
        'default'           => 'La Noue Ronde,<br>1712 route de l\'Orchère,<br>49290 CHAUDEFONDS SUR LAYON',
        'sanitize_callback' => 'wp_kses_post',
    ));
    $wp_customize->add_control('celya_address', array(
        'label'   => __('Adresse', 'celya'),
        'section' => 'celya_contact',
        'type'    => 'textarea',
    ));
    
    // Facebook
    $wp_customize->add_setting('celya_facebook', array(
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ));
    $wp_customize->add_control('celya_facebook', array(
        'label'   => __('URL Facebook', 'celya'),
        'section' => 'celya_contact',
        'type'    => 'url',
    ));
    
    // Instagram
    $wp_customize->add_setting('celya_instagram', array(
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ));
    $wp_customize->add_control('celya_instagram', array(
        'label'   => __('URL Instagram', 'celya'),
        'section' => 'celya_contact',
        'type'    => 'url',
    ));
    
    // LinkedIn
    $wp_customize->add_setting('celya_linkedin', array(
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ));
    $wp_customize->add_control('celya_linkedin', array(
        'label'   => __('URL LinkedIn', 'celya'),
        'section' => 'celya_contact',
        'type'    => 'url',
    ));
}
add_action('customize_register', 'celya_customize_register');

/**
 * 2. SUPPORT WOOCOMMERCE
 */
function celya_woocommerce_setup() {
    add_theme_support( 'woocommerce' );
    add_theme_support( 'wc-product-gallery-zoom' );
    add_theme_support( 'wc-product-gallery-lightbox' );
    add_theme_support( 'wc-product-gallery-slider' );
}
add_action( 'after_setup_theme', 'celya_woocommerce_setup' );

//Message personnalisé quand produit ajouté au panier
add_filter( 'wc_add_to_cart_message_html', 'celya_custom_add_to_cart_message', 10, 2 );
function celya_custom_add_to_cart_message( $message, $products ) {
    $message = sprintf(
        '<div class="woocommerce-message bg-green-50 border-l-4 border-green-500 p-4 flex items-center">
            <span class="text-2xl mr-3">✅</span>
            <span class="text-green-800">%s a été ajouté à votre panier !</span>
            <a href="%s" class="ml-auto bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition">Voir mon panier</a>
        </div>',
        esc_html( reset( $products ) ),
        esc_url( wc_get_cart_url() )
    );
    return $message;
}

/**
 * 3. ENQUEUE SCRIPTS & STYLES
 */
function celya_enqueue_assets() {
    // CSS compilé de Tailwind
    wp_enqueue_style(
        'celya-tailwind',
        get_template_directory_uri() . '/assets/css/output.css',
        array(),
        filemtime( get_template_directory() . '/assets/css/output.css' )
    );
    
    // JavaScript
    wp_enqueue_script(
        'celya-app',
        get_template_directory_uri() . '/assets/js/app.js',
        array( 'jquery' ),
        filemtime( get_template_directory() . '/assets/js/app.js' ),
        true
    );
    if ( is_page_template( 'template-realisations.php' ) ) {
        wp_enqueue_script(
            'celya-realisations-archive',
            get_template_directory_uri() . '/assets/js/modules/realisations-archive.js',
            [ 'celya-app' ],
            filemtime( get_template_directory() . '/assets/js/modules/realisations-archive.js' ),
            true
        );
    }

    if ( is_product() ) { // Charger les modules JS uniquement sur les pages produit
        wp_enqueue_script(
            'celya-product-page',
            get_template_directory_uri() . '/assets/js/modules/product-page.js',
            array( 'celya-app' ), // dépend de app.js → chargé après
            filemtime( get_template_directory() . '/assets/js/modules/product-page.js' ),
            true // footer
        );
    }

    if ( function_exists( 'is_checkout' ) && is_checkout() ) { // Déplacement du bouton de commande (bloc Checkout)
        wp_enqueue_script(
            'celya-checkout-place-order',
            get_template_directory_uri() . '/assets/js/modules/checkout-place-order.js',
            array(),
            filemtime( get_template_directory() . '/assets/js/modules/checkout-place-order.js' ),
            true // footer
        );
    }

    // Localiser le script pour AJAX
    wp_localize_script( 'celya-app', 'celyaData', array(
        'ajaxUrl' => admin_url( 'admin-ajax.php' ),
        'nonce' => wp_create_nonce( 'celya_nonce' ),
    ));
}
add_action( 'wp_enqueue_scripts', 'celya_enqueue_assets' );


/**
 * 5. WIDGETS
 */
function celya_widgets_init() {
    register_sidebar( array(
        'name'          => __( 'Sidebar Boutique', 'celya-tailwind' ),
        'id'            => 'shop-sidebar',
        'before_widget' => '<div class="widget mb-8 p-6 bg-white rounded-celya shadow">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="text-xl font-serif font-bold mb-4 text-celya-primary">',
        'after_title'   => '</h3>',
    ));
    
    register_sidebar( array(
        'name'          => __( 'Footer Colonne 1', 'celya-tailwind' ),
        'id'            => 'footer-1',
        'before_widget' => '<div class="widget mb-6">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4 class="text-lg font-semibold mb-3 text-white">',
        'after_title'   => '</h4>',
    ));
}
add_action( 'widgets_init', 'celya_widgets_init' );


function celya_block_category( $categories ) {
    return array_merge(
        [ [ 'slug' => 'celya', 'title' => 'Celya', 'icon' => 'admin-appearance' ] ],
        $categories
    );
}
add_filter( 'block_categories_all', 'celya_block_category' );


/**
 * 6. OPTIMISATIONS
 */

// Désactiver emojis
function celya_disable_emojis() {
    remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
    remove_action( 'wp_print_styles', 'print_emoji_styles' );
}
add_action( 'init', 'celya_disable_emojis' );

// Lazy loading natif
add_filter( 'wp_lazy_loading_enabled', '__return_true' );

// Permet d'autoriser l'import de fichiers SVG
function celya_allow_svg( $mimes ) {
    $mimes['svg'] = 'image/svg+xml';
    return $mimes;
}
add_filter( 'upload_mimes', 'celya_allow_svg' );