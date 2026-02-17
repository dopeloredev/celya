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
        <div class="lg:col-span-7 space-y-4">
            
            <!-- Image principale -->
            <div class="relative group aspect-square rounded-2xl overflow-hidden bg-white shadow-md border border-celya-light">
                <?php
                // Image principale du produit
                if ( has_post_thumbnail() ) {
                    echo '<div class="w-full h-full">';
                    the_post_thumbnail( 'woocommerce_single', array(
                        'class' => 'w-full h-full object-cover',
                    ));
                    echo '</div>';
                } else {
                    echo '<div class="w-full h-full flex items-center justify-center bg-celya-light">';
                    echo wc_placeholder_img( 'woocommerce_single' );
                    echo '</div>';
                }
                ?>
                
                <!-- Bouton zoom -->
                <div class="absolute bottom-4 right-4 bg-white/90 backdrop-blur p-2 rounded-lg shadow-sm cursor-pointer hover:bg-white transition-colors">
                    <svg class="w-6 h-6 text-celya-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m3-3H7"/>
                    </svg>
                </div>
                
                <!-- Tags produit -->
                <div class="absolute top-4 left-4 flex flex-col gap-2">
                    <?php
                    $product_tags = get_the_terms( $product->get_id(), 'product_tag' );
                    if ( $product_tags && ! is_wp_error( $product_tags ) ) :
                        $first_tag = $product_tags[0]->name;
                        $tag_classe = '';
                        
                        if ( $first_tag === 'Salé' ) {
                            $tag_classe = 'bg-celya-blue_dark text-white';
                        } elseif ( $first_tag === 'Sucré' ) {
                            $tag_classe = 'bg-celya-orange_dark text-white';
                        } elseif ( $first_tag === 'Spécialité' ) {
                            $tag_classe = 'bg-celya-green_dark text-white';
                        }
                        
                        if ( $tag_classe ) :
                            echo '<span class="' . esc_attr( $tag_classe ) . ' text-xs font-bold px-3 py-1.5 rounded-full uppercase tracking-wider">' . esc_html( $first_tag ) . '</span>';
                        endif;
                    endif;
                    ?>
                </div>
            </div>
            
            <!-- Miniatures de la galerie -->
            <?php
            $attachment_ids = $product->get_gallery_image_ids();
            if ( $attachment_ids ) :
                ?>
                <div class="grid grid-cols-4 gap-4">
                    <!-- Miniature de l'image principale -->
                    <div class="aspect-square rounded-lg border-2 border-celya-orange_dark overflow-hidden cursor-pointer">
                        <?php 
                        if ( has_post_thumbnail() ) {
                            the_post_thumbnail( 'woocommerce_gallery_thumbnail', array(
                                'class' => 'w-full h-full object-cover',
                            ));
                        }
                        ?>
                    </div>
                    
                    <!-- Autres miniatures -->
                    <?php 
                    $counter = 0;
                    foreach ( $attachment_ids as $attachment_id ) :
                        if ( $counter >= 3 ) break; // Limiter à 4 images au total
                        ?>
                        <div class="aspect-square rounded-lg border border-transparent overflow-hidden cursor-pointer opacity-70 hover:opacity-100 hover:border-celya-orange_dark transition-all">
                            <?php echo wp_get_attachment_image( $attachment_id, 'woocommerce_gallery_thumbnail', false, array( 'class' => 'w-full h-full object-cover' ) ); ?>
                        </div>
                    <?php 
                        $counter++;
                    endforeach; 
                    ?>
                </div>
            <?php endif; ?>
            
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
        // affichage des related products
        woocommerce_upsell_display();
        woocommerce_output_related_products();
    ?>

</div>

<?php do_action( 'woocommerce_after_single_product' ); ?>