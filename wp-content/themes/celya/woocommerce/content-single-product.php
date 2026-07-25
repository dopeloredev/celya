<?php
/**
 * The template for displaying product content in the single-product.php template
 *
 * @package Celya
 * @version 8.6.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

do_action( 'woocommerce_before_single_product' );

if ( post_password_required() ) {
    echo get_the_password_form();
    return;
}
?>
<div id="product-<?php the_ID(); ?>" <?php wc_product_class( '', $product ); ?>>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
        
        <!-- COLONNE GAUCHE : Galerie d'images (7 colonnes) -->
        <div class="lg:col-span-7">
            <div class="flex gap-4">

                <?php
                /**
                 * Hook: woocommerce_before_single_product_summary.
                 *
                 * @hooked woocommerce_show_product_sale_flash - 10
                 * @hooked woocommerce_show_product_images - 20
                 */
                do_action( 'woocommerce_before_single_product_summary' );
                ?>
            </div>
        </div>

        <!-- COLONNE DROITE : Informations produit (5 colonnes) -->
        <div class="lg:col-span-5 flex flex-col">
            
            <!-- Formulaire d'ajout au panier -->
            <?php do_action( 'woocommerce_single_product_summary' ); ?>
            
            <!-- Informations de livraison / Trust badges -->
            <div class="flex flex-wrap items-center justify-between gap-2 mt-4 pt-4 border-t">
                
                <!-- Livraison -->
                <div class="flex items-center gap-2 bg-celya-accent-light py-0 px-2 rounded-celya-s">
                    <svg class="w-5 h-5 text-celya-dark flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zm10 0a2 2 0 11-4 0 2 2 0 014 0zM1 1h4l2.68 13.39a2 2 0 001.98 1.61h9.72a2 2 0 001.98-1.61L23 6H6"/>
                    </svg>
                    <span class="text-xs font-medium text-celya-dark">Livraison 3j à 7j</span>
                </div>

                <!-- Séparateur -->
                <div class="hidden sm:block w-px h-8 bg-celya-light"></div>

                <!-- Click & Collect -->
                <div class="flex items-center gap-2 bg-celya-accent-light py-0 px-2 rounded-celya-s">
                    <svg class="w-5 h-5 text-celya-dark flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <div class="flex flex-col">
                        <span class="text-xs font-medium text-celya-dark leading-tight">Click &amp; Collect</span>
                    </div>
                </div>

                <!-- Séparateur -->
                <div class="hidden sm:block w-px h-8 bg-celya-light"></div>

                <!-- Paiement sécurisé -->
                <div class="flex items-center gap-2 bg-celya-accent-light py-0 px-2 rounded-celya-s">
                    <svg class="w-5 h-5 text-celya-dark flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    <span class="text-xs font-medium text-celya-dark">Paiement sécurisé</span>
                </div>

            </div>

        </div>
    </div>
    
    <?php
        // besoin pour passer les related products après avis
        remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );
        remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );
    ?>

    <!-- TABS -->
    <div class="product-tabs mt-16">
        <?php do_action( 'woocommerce_after_single_product_summary' ); ?>
    </div>

    <!-- SECTION AVIS -->
    <?php get_template_part( 'template-parts/single-product-reviews' ); ?>

    <?php
        // AFFICHAGE DES PRODUITS ASSOCIES
        woocommerce_upsell_display();
        woocommerce_output_related_products();
    ?>

</div>

<?php do_action( 'woocommerce_after_single_product' ); ?>