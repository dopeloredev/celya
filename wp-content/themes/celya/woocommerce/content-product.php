<?php
/**
 * The template for displaying product content within loops
 *
 * @package Celya
 * @version 8.6.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

// Ensure visibility.
if ( empty( $product ) || ! $product->is_visible() ) {
    return;
}
?>
<li <?php wc_product_class( 'group', $product ); ?>>
    <div class="bg-white rounded-celya-m shadow-md overflow-hidden hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 h-full flex flex-col">
        
        <!-- Image du produit -->
        <div class="relative overflow-hidden bg-celya-light">
            <a href="<?php the_permalink(); ?>" class="block aspect-square">
                <?php
                /**
                 * Hook: woocommerce_before_shop_loop_item_title.
                 *
                 * @hooked woocommerce_show_product_loop_sale_flash - 10
                 * @hooked woocommerce_template_loop_product_thumbnail - 10
                 */
                remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10 );
                
                echo '<div class="w-full h-full overflow-hidden group-hover:scale-110 transition-transform duration-500">';
                woocommerce_template_loop_product_thumbnail();
                echo '</div>';
                ?>
            </a>
            
            <!-- Tags produits (en haut à droite) -->
            <div class="absolute top-3 right-3 z-10 flex flex-col gap-2">
                <?php
                // Tag "Épuisé" si rupture de stock
                if ( ! $product->is_in_stock() ) {
                    echo '<span class="bg-gray-400 text-white text-xs px-3 py-1 rounded-full font-sans font-semibold">Épuisé</span>';
                }
                
                // Récupération des tags du produit
                $product_tags = get_the_terms( $product->get_id(), 'product_tag' );
                
                if ( $product_tags && ! is_wp_error( $product_tags ) ) :
                    // Limiter à 2 tags max pour ne pas surcharger
                    $tags_to_display = array_slice( $product_tags, 0, 2 );
                    
                    foreach ( $tags_to_display as $tag ) :
                        $tag_name = $tag->name;
                        $tag_classe = '';
                        
                        // Mapper les tags aux couleurs
                        if ( $tag_name === 'Salé' ) {
                            $tag_classe = 'bg-celya-blue_dark text-celya-dark';
                        } 
                        elseif ( $tag_name === 'Sucré' ) {
                            $tag_classe = 'bg-celya-orange_dark text-white';
                        } 
                        elseif ( $tag_name === 'Spécialité' ) {
                            $tag_classe = 'bg-celya-green_dark text-celya-dark';
                        } 
                        else {
                            // Couleur par défaut
                            $tag_classe = 'bg-celya-orange_light text-celya-dark';
                        }
                        ?>
                        <span class="<?php echo esc_attr( $tag_classe ); ?> text-xs px-3 py-1 rounded-full font-sans font-semibold">
                            <?php echo esc_html( $tag_name ); ?>
                        </span>
                    <?php endforeach;
                endif;
                ?>
            </div>
        </div>
        
        <!-- Contenu de la carte - flex-1 pour prendre l'espace restant -->
        <div class="p-6 flex-1 flex flex-col">
            
            <!-- Titre du produit - hauteur fixe -->
            <h2 class="woocommerce-loop-product__title text-lg font-serif font-bold text-celya-primary mb-2 line-clamp-2 h-14">
                <a href="<?php the_permalink(); ?>" class="hover:text-celya-orange_dark transition-colors">
                    <?php the_title(); ?>
                </a>
            </h2>
            
            <!-- Description courte - hauteur fixe -->
            <div class="h-12 mb-4">
                <?php if ( $product->get_short_description() ) : ?>
                    <p class="text-gray-600 text-sm line-clamp-2">
                        <?php echo wp_trim_words( $product->get_short_description(), 12, '...' ); ?>
                    </p>
                <?php endif; ?>
            </div>
            
            <!-- Prix et bouton - poussé en bas avec mt-auto -->
            <div class="flex items-center justify-between mt-auto">
                
                <!-- Prix -->
                <div class="text-celya-primary font-sans">
                    <?php
                    /**
                     * Hook: woocommerce_after_shop_loop_item_title.
                     *
                     * @hooked woocommerce_template_loop_rating - 5
                     * @hooked woocommerce_template_loop_price - 10
                     */
                    echo '<div class="text-xl font-bold">';
                    woocommerce_template_loop_price();
                    echo '</div>';
                    
                    // Afficher l'unité si présente
                    $price_suffix = $product->get_meta('_price_suffix');
                    if ( $price_suffix ) {
                        echo '<div class="text-xs text-gray-500 mt-0.5">' . esc_html( $price_suffix ) . '</div>';
                    } else {
                        // Valeur par défaut depuis la maquette
                        echo '<div class="text-xs text-gray-500 mt-0.5">/ Sachet de 100g</div>';
                    }
                    ?>
                </div>
                
                <!-- Bouton Ajouter au panier -->
                <?php
                /**
                 * Hook: woocommerce_after_shop_loop_item.
                 *
                 * @hooked woocommerce_template_loop_product_link_close - 5
                 * @hooked woocommerce_template_loop_add_to_cart - 10
                 */
                if ( $product->is_purchasable() && $product->is_in_stock() ) {
                    echo '<div class="celya-add-to-cart-wrapper">';
                    woocommerce_template_loop_add_to_cart();
                    echo '</div>';
                } else {
                    echo '<span class="text-gray-400 text-sm">Indisponible</span>';
                }
                ?>
            </div>
            
        </div><!-- .p-6 -->
    </div>
</li>