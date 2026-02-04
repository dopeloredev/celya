<?php
/**
 * Template Part : Carte produit pour page d'accueil
 * 
 * @package Celya
 */

global $product;
?>

<div class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 group">
    
    <!-- Image -->
    <div class="relative overflow-hidden bg-celya-light">
        <a href="<?php the_permalink(); ?>">
            <?php if ( has_post_thumbnail() ) : ?>
                <?php the_post_thumbnail( 'medium', array( 'class' => 'w-full h-64 object-cover group-hover:scale-110 transition-transform duration-500' ) ); ?>
            <?php else : ?>
                <div class="w-full h-64 flex items-center justify-center bg-gray-200">
                    <span class="text-6xl">🍪</span>
                </div>
            <?php endif; ?>
        </a>
        
        <!-- Tags produit -->
        <?php
        // Récupération des tags du produit
        $product_tags = get_the_terms( $product->get_id(), 'product_tag' );

        if ( $product_tags && ! is_wp_error( $product_tags ) ) :
            foreach ( $product_tags as $tag ) :
                $tag_name = $tag->name;
                $tag_classe = '';
                
                if ( $tag_name === 'Salé' ) {
                    $tag_classe = 'tag-product-sale';
                } 
                elseif ( $tag_name === 'Sucré' ) {
                    $tag_classe = 'tag-product-sucre';
                } 
                elseif ( $tag_name === 'Spécialité' ) {
                    $tag_classe = 'tag-product-specialite';
                } 
                else {
                    // Couleur par défaut pour les autres tags
                    $tag_classe = 'tag-product-sucre';
                }
            // Affichage du tag
        ?>
            <span class="<?php echo $tag_classe; ?>">
                <?php echo esc_html( $tag_name ); ?>
            </span>

        <?php 
            endforeach;
        endif;
        ?>
    </div>

    <!-- Contenu -->
    <div class="p-6">
        <!-- Titre -->
        <h3 class="text-xl font-serif font-bold text-celya-primary mb-2 line-clamp-2">
            <a href="<?php the_permalink(); ?>">
                <?php the_title(); ?>
            </a>
        </h3>
        
        <!-- Description -->
        <p class="text-gray-600 text-sm mb-4 line-clamp-2">
            <?php echo wp_trim_words( $product->get_short_description(), 15, '...' ); ?>
        </p>
        
        <!-- Prix et bouton -->
        <div class="flex items-center justify-between">
            <div class="text-2xl font-bold text-celya-orange_dark">
                <?php echo $product->get_price_html(); ?>
            </div>
            
            <?php if ( $product->is_purchasable() && $product->is_in_stock() ) : ?>
                <a href="<?php echo esc_url( $product->add_to_cart_url() ); ?>" 
                   data-quantity="1" 
                   data-product_id="<?php echo $product->get_id(); ?>" 
                   class="ajax_add_to_cart add_to_cart_button bg-celya-primary text-white px-6 py-2 rounded-full font-semibold hover:bg-celya-accent transition-colors duration-300">
                    Ajouter
                </a>
            <?php else : ?>
                <span class="text-gray-400 text-sm">Indisponible</span>
            <?php endif; ?>
        </div>
    </div>
</div>