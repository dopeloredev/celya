<?php
/**
 * The Template for displaying product archives, including the main shop page
 * 
 * This template can be overridden by copying it to yourtheme/woocommerce/archive-product.php.
 * 
 * @package Celya
 * @version 8.6.0
 */

defined( 'ABSPATH' ) || exit;

get_header( 'shop' );

/**
 * Hook: woocommerce_before_main_content.
 *
 * @hooked woocommerce_output_content_wrapper - 10 (removed)
 * @hooked woocommerce_breadcrumb - 20 (kept)
 */
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
do_action( 'woocommerce_before_main_content' );
?>

<div class="section-container pt-6 pb-1">
    <!-- Titre + Description -->
    <header class="woocommerce-products-header mb-8">
        <h1 class="woocommerce-products-header__title page-title font-serif text-3xl md:text-4xl font-bold text-celya-primary mb-4">
            NOS PRODUITS
        </h1>
        
        <p class="text-celya-orange_dark text-sm mb-6">
            Découvrez nos produits artisanaux confectionnés dans notre atelier au cœur de Chaudefonds-sur-Layon.
        </p>
    </header>
</div>

<!-- Container personnalisé Celya -->
<div class="section-container pt-1">
    <div class="grid grid-cols-1 lg:grid-cols-8 gap-8">
        
        <!-- Sidebar gauche - CATÉGORIES -->
        <aside class="lg:col-span-2">
            <div class="p-6 mb-6">
                <h3 class="font-serif text-lg font-bold text-celya-primary mb-4">CATÉGORIES</h3>
                
                <?php
                // Afficher les catégories de produits
                $product_categories = get_terms( array(
                    'taxonomy'   => 'product_cat',
                    'hide_empty' => true,
                    'parent'     => 0, // Seulement les catégories parentes
                ) );
                
                if ( ! empty( $product_categories ) && ! is_wp_error( $product_categories ) ) :
                    $current_cat = get_queried_object();
                    $shop_url = get_permalink( wc_get_page_id( 'shop' ) );
                    $is_shop = is_shop() && ! is_product_category();
                    // Lien "Tous les produits"
                    ?>
                    <ul class="space-y-3">
                        <li>
                            <a href="<?php echo esc_url( $shop_url ); ?>" class="flex items-center justify-between text-sm text-celya-dark">
                                <span><b>Tous les produits</b></span>
                                <span class="flex items-center justify-center text-xs"><b><?php echo wp_count_posts('product')->publish; ?></b></span>
                            </a>
                        </li>
                    
                    <?php
                    foreach ( $product_categories as $category ) :
                        $is_current = ( is_product_category() && $current_cat->term_id === $category->term_id );
                        
                        // Récupérer le tag principal de la catégorie pour définir la couleur
                        // On cherche les produits de cette catégorie et leur premier tag
                        $products_in_cat = get_posts( array(
                            'post_type' => 'product',
                            'posts_per_page' => 1,
                            'tax_query' => array(
                                array(
                                    'taxonomy' => 'product_cat',
                                    'field' => 'term_id',
                                    'terms' => $category->term_id,
                                ),
                            ),
                        ));
                        
                        // Couleur par défaut
                        $badge_color = 'bg-celya-orange_dark';
                        
                        // Si on trouve un produit, récupérer son premier tag
                        if ( ! empty( $products_in_cat ) ) {
                            $product_tags = get_the_terms( $products_in_cat[0]->ID, 'product_tag' );
                            
                            if ( $product_tags && ! is_wp_error( $product_tags ) ) {
                                $first_tag = $product_tags[0]->name;
                                
                                // Mapper les tags aux couleurs
                                if ( $first_tag === 'Salé' ) {
                                    $badge_color = 'bg-celya-blue_dark';
                                } elseif ( $first_tag === 'Sucré' ) {
                                    $badge_color = 'bg-celya-orange_dark';
                                } elseif ( $first_tag === 'Spécialité' ) {
                                    $badge_color = 'bg-celya-green_dark';
                                }
                            }
                        }
                        ?>
                        <li>
                            <a href="<?php echo esc_url( get_term_link( $category ) ); ?>" 
                            class="flex items-center gap-2 text-sm <?php echo $is_current ? 'text-celya-orange_dark font-semibold' : 'text-celya-dark hover:text-celya-orange_dark'; ?> transition-colors">
                                <!-- Pastille colorée avant le texte -->
                                <span class="w-2 h-2 flex-shrink-0 rounded-full <?php echo $badge_color; ?>"></span>
                                <!-- Nom de la catégorie -->
                                <span class="flex-1"><?php echo esc_html( $category->name ); ?></span>
                                <!-- Nombre de produits -->
                                <span class="text-xs"><?php echo esc_html( $category->count ); ?></span>
                            </a>
                        </li>
                    <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
            
            <!-- Section Produits à personnaliser -->
            <div class="bg-celya-orange_light rounded-celya-m p-6">
                <h3 class="font-serif text-lg font-bold text-celya-primary mb-3">Produits à personnaliser</h3>
                <p class="text-sm text-celya-dark mb-4">
                    Personnalisez biscuits et autres produits pour vos événements (mariage, anniversaire, baptême, baby shower...)
                </p>
                <a href="<?php echo home_url('/personnalisation'); ?>" class="btn-celya-orange-dark text-sm w-full text-center block">
                    En savoir plus
                </a>
            </div>
        </aside>
        
        <!-- Zone principale produits -->
        <div class="lg:col-span-6">
            
            <?php if ( woocommerce_product_loop() ) : ?>

                <!-- Barre de tri -->
                <div class="flex justify-end items-center mb-6">
                    <div class="flex items-center gap-3">
                        <span class="text-sm text-celya-dark">Trier par</span>
                        <?php
                        /**
                         * Hook: woocommerce_before_shop_loop.
                         *
                         * @hooked woocommerce_catalog_ordering - 30 (tri)
                         */
                        do_action( 'woocommerce_before_shop_loop' );
                        ?>
                    </div>
                </div>
                
                <?php
                /**
                 * Hook: woocommerce_shop_loop.
                 */
                do_action( 'woocommerce_shop_loop' );
                
                woocommerce_product_loop_start();
                
                if ( wc_get_loop_prop( 'total' ) ) {
                    while ( have_posts() ) {
                        the_post();
                        
                        /**
                         * Hook: woocommerce_shop_loop.
                         */
                        do_action( 'woocommerce_shop_loop' );
                        
                        wc_get_template_part( 'content', 'product' );
                    }
                }
                
                woocommerce_product_loop_end();
                
                /**
                 * Hook: woocommerce_after_shop_loop.
                 *
                 * @hooked woocommerce_pagination - 10
                 */
                do_action( 'woocommerce_after_shop_loop' );
                ?>
                
            <?php else : ?>
                
                <?php
                /**
                 * Hook: woocommerce_no_products_found.
                 *
                 * @hooked wc_no_products_found - 10
                 */
                do_action( 'woocommerce_no_products_found' );
                ?>
                
            <?php endif; ?>
            
        </div><!-- .lg:col-span-6 -->
        
    </div><!-- .grid -->
</div><!-- .section-container -->

<!-- Footer avec pictogrammes -->
<div class="w-full bg-celya-light py-8 mt-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
            
            <!-- Livraison -->
            <div class="flex flex-col items-center">
                <div class="w-12 h-12 mb-3">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-full h-full text-celya-orange_dark">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                    </svg>
                </div>
                <h4 class="font-sans font-semibold text-sm text-celya-dark mb-1">Livraison sous 72h</h4>
                <p class="text-xs text-gray-600">(Gratuite à partir 49,90€ de commande)</p>
            </div>
            
            <!-- Click & Collect -->
            <div class="flex flex-col items-center">
                <div class="w-12 h-12 mb-3">
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/pictograms/marron/picto_click_and_collect.svg" 
                         alt="Click & Collect" class="w-full h-full">
                </div>
                <h4 class="font-sans font-semibold text-sm text-celya-dark mb-1">Click & Collect</h4>
                <p class="text-xs text-gray-600">Vendredi (14h-18h) & Samedi (9h-12h)</p>
            </div>
            
            <!-- Paiement sécurisé -->
            <div class="flex flex-col items-center">
                <div class="w-12 h-12 mb-3">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-full h-full text-celya-orange_dark">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <h4 class="font-sans font-semibold text-sm text-celya-dark mb-1">Paiement sécurisé</h4>
                <p class="text-xs text-gray-600">(visa, mastercard, etc.)</p>
            </div>
            
            <!-- Besoin d'aide -->
            <div class="flex flex-col items-center">
                <div class="w-12 h-12 mb-3">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-full h-full text-celya-orange_dark">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                    </svg>
                </div>
                <h4 class="font-sans font-semibold text-sm text-celya-dark mb-1">Besoin d'aide ?</h4>
                <p class="text-xs text-gray-600"><?php echo get_option('celya_phone', '02 41 00 00 00'); ?></p>
            </div>
            
        </div>
    </div>
</div>

<?php
/**
 * Hook: woocommerce_after_main_content.
 *
 * @hooked woocommerce_output_content_wrapper_end - 10 (removed)
 */
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );
do_action( 'woocommerce_after_main_content' );

get_footer( 'shop' );
