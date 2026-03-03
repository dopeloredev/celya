<?php
/**
 * Configuration des onglets de la fiche produit - Celya
 *
 * Gère l'enregistrement, l'ordre et le contenu des onglets front-end.
 * Pour les produits variables, le contenu des onglets est rechargé via AJAX
 * à chaque changement de variation.
 *
 * @package Celya
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


// =============================================================================
// 1. HELPERS
// =============================================================================

/**
 * Vérifie si un tableau de specs est entièrement vide.
 *
 * @param  array $specs
 * @return bool
 */
function celya_specs_are_empty( $specs ) {
    $has_ingredients = ! empty(
        array_filter( $specs['ingredients'] ?? array(), fn( $r ) => ! empty( $r['ingredient'] ) )
    );
    $has_nutrition = ! empty(
        array_filter( $specs['nutrition'] ?? array(), fn( $r ) => ! empty( $r['nutriment'] ) )
    );

    return ! $has_ingredients
        && ! $has_nutrition
        && empty( $specs['allergenes'] )
        && empty( $specs['conservation'] )
        && empty( $specs['degustation'] );
}

/**
 * Récupère les specs à afficher par défaut au chargement de la page.
 *
 * - Produit simple   → specs du produit
 * - Produit variable → specs du parent ; si vides, première variation publiée
 *
 * @param  WC_Product $product
 * @return array
 */
function celya_get_specs_for_display( $product ) {
    $specs = celya_get_product_specs( $product );

    if ( $product->is_type( 'variable' ) && celya_specs_are_empty( $specs ) ) {
        foreach ( $product->get_children() as $variation_id ) {
            $variation = wc_get_product( $variation_id );
            if ( ! $variation || $variation->get_status() !== 'publish' ) {
                continue;
            }
            $variation_specs = celya_get_product_specs( $variation );
            if ( ! celya_specs_are_empty( $variation_specs ) ) {
                return $variation_specs;
            }
        }
    }

    return $specs;
}


// =============================================================================
// 2. ENREGISTREMENT ET ORDRE DES ONGLETS
// =============================================================================

/**
 * Personnalise les onglets de la fiche produit.
 *
 * Ordre : Description (10) | Caractéristiques (20) | Ingrédients & Allergènes (30)
 *       | Valeurs nutritionnelles (40) | Conservation (50)
 *
 * Pour les produits variables : les onglets s'affichent dès qu'au moins
 * une variation a des données (ils ne disparaissent pas au switch de variation).
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

    unset( $tabs['reviews'] );

    // ── Onglets Celya ───────────────────────────────────────────────────────

    if ( $product->is_type( 'variable' ) ) {
        // Vérifier qu'au moins une variation (ou le parent) a des données
        $has_any_specs = ! celya_specs_are_empty( celya_get_product_specs( $product ) );

        if ( ! $has_any_specs ) {
            foreach ( $product->get_children() as $variation_id ) {
                $variation = wc_get_product( $variation_id );
                if ( $variation && ! celya_specs_are_empty( celya_get_product_specs( $variation ) ) ) {
                    $has_any_specs = true;
                    break;
                }
            }
        }

        if ( $has_any_specs ) {
            $tabs['celya_ingredients'] = array(
                'title'    => __( 'Ingrédients & Allergènes', 'celya' ),
                'priority' => 30,
                'callback' => 'celya_tab_ingredients_allergens',
            );
            $tabs['celya_nutrition'] = array(
                'title'    => __( 'Valeurs nutritionnelles', 'celya' ),
                'priority' => 40,
                'callback' => 'celya_tab_nutrition',
            );
            $tabs['celya_conservation'] = array(
                'title'    => __( 'Conservation', 'celya' ),
                'priority' => 50,
                'callback' => 'celya_tab_conservation',
            );
            $tabs['celya_degustation'] = array(
                'title'    => __( 'Degustation', 'celya' ),
                'priority' => 60,
                'callback' => 'celya_tab_degustation',
            );
        }
    } else {
        // Produit simple : conditionnel selon les données
        $specs = celya_get_specs_for_display( $product );

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

        if ( ! empty( $specs['conservation'] )) {
            $tabs['celya_conservation'] = array(
                'title'    => __( 'Conservation', 'celya' ),
                'priority' => 50,
                'callback' => 'celya_tab_conservation',
            );
        }

        if (! empty( $specs['degustation'] )) {
            $tabs['celya_degustation'] = array(
                'title'    => __( 'Degustation', 'celya' ),
                'priority' => 60,
                'callback' => 'celya_tab_degustation',
            );
        }
    }

    return $tabs;
}
add_filter( 'woocommerce_product_tabs', 'celya_customize_product_tabs', 98 );


// =============================================================================
// 3. CALLBACKS DES ONGLETS (rendu initial côté serveur)
// =============================================================================

/**
 * Charge un template depuis woocommerce/single-product/tabs/{name}.php
 * Ajoute un data-tab sur le conteneur pour le ciblage JS.
 *
 * @param string $name
 * @param array  $specs
 */
function celya_load_tab_template( $name, $specs ) {
    $template = get_template_directory() . '/woocommerce/single-product/tabs/' . sanitize_file_name( $name ) . '.php';
    if ( file_exists( $template ) ) {
        echo '<div class="celya-tab-content" data-tab="' . esc_attr( $name ) . '">';
        include $template;
        echo '</div>';
    }
}

function celya_tab_ingredients_allergens() {
    global $product;
    $specs = celya_get_specs_for_display( $product );
    celya_load_tab_template( 'ingredients_allergens', $specs );
}

function celya_tab_nutrition() {
    global $product;
    $specs = celya_get_specs_for_display( $product );
    celya_load_tab_template( 'nutri', $specs );
}

function celya_tab_conservation() {
    global $product;
    $specs = celya_get_specs_for_display( $product );
    celya_load_tab_template( 'conservation', $specs );
}

function celya_tab_degustation() {
    global $product;
    $specs = celya_get_specs_for_display( $product );
    celya_load_tab_template( 'degustation', $specs );
}

// =============================================================================
// 4. ENDPOINT AJAX — Retourne le HTML des onglets pour une variation donnée
// =============================================================================

/**
 * Handler AJAX : reçoit un variation_id, retourne le HTML des 3 onglets en JSON.
 * Accessible pour utilisateurs connectés et non connectés.
 */
function celya_ajax_get_variation_tabs() {
    // Vérification nonce
    check_ajax_referer( 'celya_variation_tabs', 'nonce' );

    $variation_id = intval( $_POST['variation_id'] ?? 0 );
    $product_id   = intval( $_POST['product_id']   ?? 0 );

    if ( ! $variation_id || ! $product_id ) {
        wp_send_json_error( array( 'message' => 'Paramètres manquants.' ), 400 );
    }

    // Récupérer la variation
    $variation = wc_get_product( $variation_id );
    if ( ! $variation || ! $variation->is_type( 'variation' ) ) {
        wp_send_json_error( array( 'message' => 'Variation introuvable.' ), 404 );
    }

    // Vérifier que la variation appartient bien au produit parent
    if ( $variation->get_parent_id() !== $product_id ) {
        wp_send_json_error( array( 'message' => 'Variation invalide.' ), 403 );
    }

    // Récupérer les specs (avec fallback parent intégré dans celya_get_product_specs)
    $specs = celya_get_product_specs( $variation );

    // Générer le HTML de chaque onglet via les templates
    $tabs = array(
        'ingredients_allergens' => '',
        'nutri'                 => '',
        'conservation'          => '',
        'degustation'          => '',
    );

    foreach ( array_keys( $tabs ) as $tab_name ) {
        $template = get_template_directory() . '/woocommerce/single-product/tabs/' . $tab_name . '.php';
        if ( file_exists( $template ) ) {
            ob_start();
            include $template; // $specs est disponible dans la portée
            $tabs[ $tab_name ] = ob_get_clean();
        }
    }

    wp_send_json_success( array( 'tabs' => $tabs ) );
}
add_action( 'wp_ajax_celya_get_variation_tabs',        'celya_ajax_get_variation_tabs' );
add_action( 'wp_ajax_nopriv_celya_get_variation_tabs', 'celya_ajax_get_variation_tabs' );


// =============================================================================
// 5. ENQUEUE DU SCRIPT JS (uniquement sur les fiches produit variables)
// =============================================================================

/**
 * Injecte le script AJAX et le nonce uniquement sur les fiches produit variables.
 */
function celya_enqueue_variation_tabs_script() {
    if ( ! is_product() ) {
        return;
    }

    // Utiliser get_the_ID() + wc_get_product() plutôt que global $product
    // car $product peut être une string (post_type) au moment de wp_enqueue_scripts
    $product = wc_get_product( get_the_ID() );
    if ( ! $product || ! $product->is_type( 'variable' ) ) {
        return;
    }

    // Localiser les données nécessaires au JS
    wp_localize_script( 'celya-app', 'celyaVariationTabs', array(
        'ajaxUrl'    => admin_url( 'admin-ajax.php' ),
        'nonce'      => wp_create_nonce( 'celya_variation_tabs' ),
        'productId'  => $product->get_id(),
        'action'     => 'celya_get_variation_tabs',
        'loadingMsg' => __( 'Chargement…', 'celya' ),
    ) );

    // Script inline attaché après celya-app (déjà enqueued dans functions.php)
    wp_add_inline_script( 'celya-app', celya_get_variation_tabs_js() );
}
add_action( 'wp_enqueue_scripts', 'celya_enqueue_variation_tabs_script', 20 );

/**
 * Retourne le JS de mise à jour AJAX des onglets.
 *
 * @return string
 */
function celya_get_variation_tabs_js() {
    return <<<'JS'
jQuery(function ($) {

    // Vérifie que les données sont bien localisées
    if (typeof celyaVariationTabs === 'undefined') return;

    var cfg = celyaVariationTabs;

    // Indicateur de chargement en cours (évite les requêtes multiples)
    var isLoading = false;

    /**
     * Met à jour le contenu d'un onglet avec un spinner pendant le chargement.
     */
    function setLoading() {
        $('[data-tab]').each(function () {
            $(this).html(
                '<p class="text-sm text-gray-400 italic py-4 flex items-center gap-2">' +
                '<span class="celya-spinner inline-block w-4 h-4 border-2 border-celya-primary border-t-transparent rounded-full animate-spin"></span>' +
                cfg.loadingMsg +
                '</p>'
            );
        });
    }

    /**
     * Charge les specs de la variation via AJAX et met à jour les onglets.
     *
     * @param {number} variationId
     */
    function loadVariationTabs(variationId) {
        if (isLoading) return;
        isLoading = true;

        setLoading();

        $.ajax({
            url:    cfg.ajaxUrl,
            method: 'POST',
            data: {
                action:       cfg.action,
                nonce:        cfg.nonce,
                variation_id: variationId,
                product_id:   cfg.productId,
            },
            success: function (response) {
                if (!response.success || !response.data || !response.data.tabs) {
                    return;
                }

                var tabs = response.data.tabs;

                // Mettre à jour chaque onglet s'il existe dans le DOM
                Object.keys(tabs).forEach(function (tabName) {
                    var $container = $('[data-tab="' + tabName + '"]');
                    if ($container.length) {
                        $container.html(tabs[tabName]);
                    }
                });
            },
            error: function () {
                $('[data-tab]').each(function () {
                    $(this).html(
                        '<p class="text-sm text-red-400 italic py-4">Erreur lors du chargement.</p>'
                    );
                });
            },
            complete: function () {
                isLoading = false;
            },
        });
    }

    // ── Écoute les events WooCommerce ──────────────────────────────────────

    // Déclenché quand une variation valide est sélectionnée
    $(document).on('found_variation', function (event, variation) {
        if (variation && variation.variation_id) {
            loadVariationTabs(variation.variation_id);
        }
    });

    // Déclenché quand la sélection est réinitialisée ("Effacer")
    $(document).on('reset_data', function () {
        // On recharge la page pour revenir au rendu serveur initial
        // (plus propre que de deviner la variation par défaut côté JS)
        window.location.reload();
    });

});
JS;
}