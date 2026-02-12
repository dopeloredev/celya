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
            <div class="bg-celya-light rounded-2xl p-6 space-y-4 mt-4">
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 rounded-full bg-celya-orange_light flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-celya-orange_dark" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-sm text-celya-dark mb-1">Livraison soignée</h4>
                        <p class="text-xs text-gray-600">Expédition sous 48h - Gratuite dès 49,90€</p>
                    </div>
                </div>
                
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 rounded-full bg-celya-orange_light flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-celya-orange_dark" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-sm text-celya-dark mb-1">Fabrication artisanale</h4>
                        <p class="text-xs text-gray-600">Confectionné à la main dans notre atelier</p>
                    </div>
                </div>
                
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 rounded-full bg-celya-orange_light flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-celya-orange_dark" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-sm text-celya-dark mb-1">Paiement sécurisé</h4>
                        <p class="text-xs text-gray-600">Transactions 100% protégées</p>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- ONGLETS & AVIS EN BAS -->
    <div class="product-tabs mt-16">
        <?php do_action( 'woocommerce_after_single_product_summary' ); ?>
    </div>

</div>

<?php do_action( 'woocommerce_after_single_product' ); ?>