<?php
/**
 * Template Page d'accueil - Celya
 */

get_header();
?>

<div class="bg-gradient-to-b from-celya-light to-white">
    
    <!-- Hero Section -->
    <section class="py-20 text-center">
        <div class="max-w-4xl mx-auto px-4">
            <h1 class="text-5xl md:text-6xl font-serif font-bold text-celya-primary mb-6">
                Celya Biscuiterie
            </h1>
            <p class="text-xl md:text-2xl text-gray-700 mb-8">
                Biscuits artisanaux élaborés avec des farines alternatives et des ingrédients de qualité
            </p>
            <a href="<?php echo get_permalink( wc_get_page_id( 'shop' ) ); ?>" 
               class="inline-block bg-celya-primary text-white px-8 py-4 rounded-full text-lg font-semibold 
                      hover:bg-celya-accent transition transform hover:scale-105">
                Découvrir nos produits 🍪
            </a>
        </div>
    </section>
    
    <!-- Produits mis en avant -->
    <?php if ( class_exists( 'WooCommerce' ) ) : ?>
        <section class="py-16 bg-white">
            <div class="max-w-7xl mx-auto px-4">
                <h2 class="text-4xl font-serif font-bold text-center text-celya-primary mb-12">
                    Nos Produits
                </h2>
                
                <?php
                echo do_shortcode('[products limit="4" columns="4" orderby="popularity"]');
                ?>
            </div>
        </section>
    <?php endif; ?>
    
    <!-- Valeurs -->
    <section class="py-16 bg-celya-light">
        <div class="max-w-7xl mx-auto px-4">
            <h2 class="text-4xl font-serif font-bold text-center text-celya-primary mb-12">
                Nos Valeurs
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center p-6 bg-white rounded-lg shadow-md">
                    <div class="text-5xl mb-4">🌾</div>
                    <h3 class="text-2xl font-serif font-bold text-celya-primary mb-3">Artisanal</h3>
                    <p class="text-gray-700">Fabrication artisanale dans notre atelier de 80m²</p>
                </div>
                
                <div class="text-center p-6 bg-white rounded-lg shadow-md">
                    <div class="text-5xl mb-4">💚</div>
                    <h3 class="text-2xl font-serif font-bold text-celya-primary mb-3">Qualité</h3>
                    <p class="text-gray-700">Farines alternatives et ingrédients de qualité</p>
                </div>
                
                <div class="text-center p-6 bg-white rounded-lg shadow-md">
                    <div class="text-5xl mb-4">🎨</div>
                    <h3 class="text-2xl font-serif font-bold text-celya-primary mb-3">Créativité</h3>
                    <p class="text-gray-700">Personnalisation et créations sur-mesure</p>
                </div>
            </div>
        </div>
    </section>
    
</div>

<?php get_footer(); ?>