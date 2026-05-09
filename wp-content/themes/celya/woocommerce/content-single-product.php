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

                <!-- Miniatures verticales à gauche -->
                <div class="flex flex-col gap-3 w-20 flex-shrink-0">
                    <?php
                    // Miniature image principale
                    $main_thumb_url  = get_the_post_thumbnail_url( get_the_ID(), 'woocommerce_single' );
                    $main_thumb_small = get_the_post_thumbnail_url( get_the_ID(), 'woocommerce_gallery_thumbnail' );
                    ?>
                    <div class="celya-thumb active-thumb aspect-square rounded-lg overflow-hidden cursor-pointer border-2 border-celya-orange_dark"
                        data-full="<?php echo esc_url( $main_thumb_url ); ?>">
                        <?php 
                        if ( has_post_thumbnail() ) {
                            echo '<div class="w-full h-full">';
                            the_post_thumbnail( 'woocommerce_single', array(
                                'class' => 'w-full h-full object-cover transition-opacity duration-200',
                            ));
                            echo '</div>';
                        } else {
                            echo '<div class="w-full h-full flex items-center justify-center bg-celya-light transition-opacity duration-200">';
                            echo wc_placeholder_img( 'woocommerce_single' );
                            echo '</div>';
                        }
                        ?>
                    </div>

                    <?php
                    $attachment_ids = $product->get_gallery_image_ids();
                    foreach ( $attachment_ids as $attachment_id ) :
                        $full_url  = wp_get_attachment_image_url( $attachment_id, 'woocommerce_single' );
                        $thumb_url = wp_get_attachment_image_url( $attachment_id, 'woocommerce_gallery_thumbnail' );
                    ?>
                    <div class="celya-thumb aspect-square rounded-lg overflow-hidden cursor-pointer border-2 border-transparent opacity-60 hover:opacity-100 hover:border-celya-orange_dark transition-all"
                        data-full="<?php echo esc_url( $full_url ); ?>">
                        <img src="<?php echo esc_url( $thumb_url ); ?>" class="w-full h-full object-cover" alt="">
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Image principale -->
                <div class="relative flex-1 aspect-square rounded-2xl overflow-hidden bg-white shadow-md border border-celya-light group">
                    <?php
                    // Image principale du produit
                    if ( has_post_thumbnail() ) {
                        echo '<div class="w-full h-full">';
                        the_post_thumbnail( 'woocommerce_single', array(
                            'class' => 'w-full h-full object-cover transition-opacity duration-200',
                        ));
                        echo '</div>';
                    } else {
                        echo '<div class="w-full h-full flex items-center justify-center bg-celya-light transition-opacity duration-200">';
                        echo wc_placeholder_img( 'woocommerce_single' );
                        echo '</div>';
                    }
                    ?>

                    <!-- Tags produit -->
                    <div class="absolute top-4 right-4 flex flex-col gap-2">
                        <?php
                        $product_tags = get_the_terms( $product->get_id(), 'product_tag' );
                        if ( $product_tags && ! is_wp_error( $product_tags ) ) :
                            $first_tag  = $product_tags[0]->name;
                            $tag_classe = '';
                            if ( $first_tag === 'Salé' )       $tag_classe = 'bg-celya-blue_dark text-white';
                            elseif ( $first_tag === 'Sucré' )   $tag_classe = 'bg-celya-orange_dark text-white';
                            elseif ( $first_tag === 'Spécialité' ) $tag_classe = 'bg-celya-green_dark text-white';
                            if ( $tag_classe ) :
                                echo '<span class="' . esc_attr( $tag_classe ) . ' text-xs font-bold px-3 py-1.5 rounded-full uppercase tracking-wider">' . esc_html( $first_tag ) . '</span>';
                            endif;
                        endif;

                        if ( $product->is_on_sale() ) :
                            echo '<span class="bg-red-500 text-white text-xs font-bold px-3 py-1.5 rounded-full uppercase tracking-wider">Promo</span>';
                        endif;
                        ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- COLONNE DROITE : Informations produit (5 colonnes) -->
        <div class="lg:col-span-5 flex flex-col">
            
            <!-- Formulaire d'ajout au panier -->
            <?php do_action( 'woocommerce_single_product_summary' ); ?>
            
            <!-- Informations de livraison / Trust badges -->
            <div class="flex flex-wrap items-center justify-between gap-2 mt-4 pt-4 border-t">
                
                <!-- Livraison -->
                <div class="flex items-center gap-2 bg-celya-orange_light py-0 px-2 rounded-celya-s">
                    <svg class="w-5 h-5 text-celya-dark flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zm10 0a2 2 0 11-4 0 2 2 0 014 0zM1 1h4l2.68 13.39a2 2 0 001.98 1.61h9.72a2 2 0 001.98-1.61L23 6H6"/>
                    </svg>
                    <span class="text-xs font-medium text-celya-dark">Livraison 3j à 7j</span>
                </div>

                <!-- Séparateur -->
                <div class="hidden sm:block w-px h-8 bg-celya-light"></div>

                <!-- Click & Collect -->
                <div class="flex items-center gap-2 bg-celya-orange_light py-0 px-2 rounded-celya-s">
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
                <div class="flex items-center gap-2 bg-celya-orange_light py-0 px-2 rounded-celya-s">
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