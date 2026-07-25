<?php
/**
 * Déclinaisons produit — Celya
 *
 * Simule la notion de « variation » pour les produits SIMPLES sans utiliser
 * le système natif WooCommerce (produit variable) qui ne convient pas au projet.
 *
 * Principe :
 *  - Chaque produit simple porte UNE saveur (attribut global `pa_saveur`).
 *  - Un champ « Déclinaison » (onglet Produits liés) permet d'associer les autres
 *    produits simples qui représentent la même recette dans une autre saveur.
 *  - Sur la fiche produit, on affiche un sélecteur de saveurs réutilisant le style
 *    des variations : la saveur du produit courant est présélectionnée et non
 *    cliquable, les autres sont des liens vers les fiches des déclinaisons.
 *
 * NB : la relation est explicite et doit être renseignée sur chaque produit du
 * groupe (le produit vanille liste fraise, le produit fraise liste vanille).
 *
 * @package Celya
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

const CELYA_DECLINAISON_META = '_celya_declinaisons';
const CELYA_SAVEUR_TAXONOMY  = 'pa_saveur';

// =============================================================================
// 1. CHAMP ADMIN — onglet « Produits liés »
// =============================================================================

/**
 * Ajoute le sélecteur de déclinaisons (recherche produit select2 native WC)
 * sous les ventes croisées/incitatives de l'onglet « Produits liés ».
 */
function celya_declinaisons_product_field() {
    global $post;

    $ids = (array) get_post_meta( $post->ID, CELYA_DECLINAISON_META, true );
    ?>
    <div class="options_group">
        <p class="form-field">
            <label for="celya_declinaisons"><?php esc_html_e( 'Déclinaison', 'celya' ); ?></label>
            <select
                class="wc-product-search"
                multiple="multiple"
                style="width: 50%;"
                id="celya_declinaisons"
                name="<?php echo esc_attr( CELYA_DECLINAISON_META ); ?>[]"
                data-placeholder="<?php esc_attr_e( 'Rechercher un produit&hellip;', 'celya' ); ?>"
                data-action="woocommerce_json_search_products"
                data-exclude="<?php echo esc_attr( $post->ID ); ?>">
                <?php
                foreach ( $ids as $declinaison_id ) {
                    $declinaison = wc_get_product( $declinaison_id );
                    if ( $declinaison instanceof WC_Product ) {
                        printf(
                            '<option value="%s" selected="selected">%s</option>',
                            esc_attr( $declinaison_id ),
                            esc_html( wp_strip_all_tags( $declinaison->get_formatted_name() ) )
                        );
                    }
                }
                ?>
            </select>
            <?php
            echo wc_help_tip(
                __( 'Autres produits représentant la même recette dans une autre saveur. Affiche un sélecteur de saveurs sur la fiche. La saveur provient de l\'attribut « Saveur » de chaque produit.', 'celya' )
            );
            ?>
        </p>
    </div>
    <?php
}
add_action( 'woocommerce_product_options_related', 'celya_declinaisons_product_field' );

/**
 * Sauvegarde des déclinaisons.
 *
 * @param int $post_id
 */
function celya_declinaisons_save( $post_id ) {
    $ids = isset( $_POST[ CELYA_DECLINAISON_META ] )
        ? array_filter( array_map( 'intval', (array) $_POST[ CELYA_DECLINAISON_META ] ) )
        : array();

    // On ne stocke jamais le produit courant dans sa propre liste.
    $ids = array_values( array_diff( array_unique( $ids ), array( (int) $post_id ) ) );

    update_post_meta( $post_id, CELYA_DECLINAISON_META, $ids );
}
add_action( 'woocommerce_process_product_meta', 'celya_declinaisons_save' );


// =============================================================================
// 2. HELPERS
// =============================================================================

/**
 * Récupère le terme de saveur (pa_saveur) d'un produit.
 * Un produit simple ne porte qu'une seule saveur : on renvoie la première.
 *
 * @param WC_Product $product
 * @return WP_Term|null
 */
function celya_get_product_saveur( $product ) {
    if ( ! $product instanceof WC_Product ) {
        return null;
    }

    $terms = wc_get_product_terms( $product->get_id(), CELYA_SAVEUR_TAXONOMY, array( 'fields' => 'all' ) );

    return ! empty( $terms ) ? $terms[0] : null;
}

/**
 * Renvoie le slug de thème couleur (sucre|sale|specialite) d'un produit selon
 * son product_tag, ou '' si aucun tag connu.
 *
 * Source de vérité unique partagée par le thème de la fiche produit
 * (classe body.theme-* dans woocommerce-setup-single-product.php) et par la
 * coloration par déclinaison (classe .declinaison-theme-*).
 *
 * @param WC_Product|int $product Produit ou ID.
 * @return string
 */
function celya_get_product_theme_slug( $product ) {
    $product_id = $product instanceof WC_Product ? $product->get_id() : (int) $product;
    if ( ! $product_id ) {
        return '';
    }

    foreach ( array( 'sale', 'specialite', 'sucre' ) as $slug ) {
        if ( has_term( $slug, 'product_tag', $product_id ) ) {
            return $slug;
        }
    }

    return '';
}

/**
 * Construit le groupe de déclinaisons à afficher : produit courant + déclinaisons
 * associées, chacune décrite par sa saveur et son URL.
 *
 * Le groupe est trié par nom de saveur pour garder le même ordre d'une fiche à
 * l'autre. Renvoie un tableau vide s'il n'y a aucune déclinaison à afficher.
 *
 * @param WC_Product $product
 * @return array<int, array{product:WC_Product, label:string, url:string, is_current:bool}>
 */
function celya_get_declinaison_group( $product ) {
    if ( ! $product instanceof WC_Product ) {
        return array();
    }

    $current_id = $product->get_id();
    $ids        = (array) get_post_meta( $current_id, CELYA_DECLINAISON_META, true );
    $ids        = array_filter( array_map( 'intval', $ids ) );

    if ( empty( $ids ) ) {
        return array();
    }

    // Produits du groupe : courant + déclinaisons visibles/publiées.
    $products = array( $current_id => $product );
    foreach ( $ids as $id ) {
        if ( $id === $current_id || isset( $products[ $id ] ) ) {
            continue;
        }
        $declinaison = wc_get_product( $id );
        if ( $declinaison instanceof WC_Product && 'publish' === $declinaison->get_status() ) {
            $products[ $id ] = $declinaison;
        }
    }

    // Il faut au moins une vraie déclinaison en plus du produit courant.
    if ( count( $products ) < 2 ) {
        return array();
    }

    $group = array();
    foreach ( $products as $id => $item ) {
        $saveur = celya_get_product_saveur( $item );
        $group[] = array(
            'product'    => $item,
            'label'      => $saveur ? $saveur->name : $item->get_name(),
            'url'        => $item->get_permalink(),
            'is_current' => ( $id === $current_id ),
        );
    }

    // Ordre : la saveur du produit courant en premier, puis les autres triées
    // par nom de saveur (ordre stable d'une fiche à l'autre).
    usort(
        $group,
        static function ( $a, $b ) {
            if ( $a['is_current'] !== $b['is_current'] ) {
                return $a['is_current'] ? -1 : 1;
            }
            return strcasecmp( $a['label'], $b['label'] );
        }
    );

    return $group;
}


// =============================================================================
// 3. RENDU FRONTEND
// =============================================================================

/**
 * Affiche le sélecteur de saveurs (déclinaisons) sur la fiche produit simple.
 * Réutilise les classes de style des variations (.variation-selector, etc.).
 *
 * @param WC_Product|null $product
 */
function celya_render_declinaisons( $product = null ) {
    if ( ! $product instanceof WC_Product ) {
        global $product;
    }

    $group = celya_get_declinaison_group( $product );
    if ( empty( $group ) ) {
        return;
    }
    ?>
    <div class="variation-selector declinaison-selector mb-6">
        <label><?php esc_html_e( 'Saveur', 'celya' ); ?></label>
        <div class="variation-buttons">
            <?php
            foreach ( $group as $item ) :
                // Chaque déclinaison porte la couleur de SON tag (et non l'accent
                // de la page) : la classe ré-scope les variables d'accent en CSS.
                $theme      = celya_get_product_theme_slug( $item['product'] );
                $theme_cls  = $theme ? ' declinaison-theme-' . $theme : '';
                ?>
                <?php if ( $item['is_current'] ) : ?>
                    <span class="variation-option selected declinaison-current<?php echo esc_attr( $theme_cls ); ?>" aria-current="true">
                        <?php echo esc_html( $item['label'] ); ?>
                    </span>
                <?php else : ?>
                    <a href="<?php echo esc_url( $item['url'] ); ?>"
                       class="variation-option declinaison-link<?php echo esc_attr( $theme_cls ); ?>"
                       aria-label="<?php echo esc_attr( sprintf( __( 'Voir la saveur %s', 'celya' ), $item['label'] ) ); ?>">
                        <?php echo esc_html( $item['label'] ); ?>
                    </a>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
    <?php
}
