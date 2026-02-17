<?php
    /**
     * Template Page d'accueil - Celya
     */

    get_header();
?>


<!-- Section Hero avec Hero Banner -->
<section class="relative w-full h-[500px] md:h-[600px] bg-cover bg-center" style="background-image: url('<?php echo get_template_directory_uri(); ?>/assets/images/banners/accueil-banniere.png');">
    <div class="absolute inset-0 bg-black bg-opacity-40"></div>
    <div class="relative z-10 flex flex-col items-center justify-center h-full text-center text-white px-4">
        <h1 class="font-serif text-4xl md:text-6xl font-bold mb-6 text-white">
            Lorem ipsum dolor sit amet
        </h1>
        <p class="text-lg md:text-xl mb-8 max-w-2xl text-white">
            Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.
        </p>
        <a href="<?php echo home_url('/shop'); ?>" class="btn-celya-orange-dark">
            Voir tous nos produits
        </a>
    </div>
</section>

<!-- Produits mis en avant -->
<?php if ( class_exists( 'WooCommerce' ) ) : ?>
    <!-- Section Sélection du moment -->
    <section class="section-container bg-celya-light">
        <!-- Conteneur flex pour aligner titre et bouton -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <p class="text-celya-orange_dark uppercase tracking-wider text-sm mb-2">Sélection du moment</p>
                <h2 class="font-serif text-3xl md:text-4xl font-bold text-celya-primary">Nos incontournables</h2>
            </div>
            <a href="<?php echo home_url('/shop'); ?>" class="text-celya-primary hover:text-celya-accent transition-colors flex items-center gap-2">
                Voir tout
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                </svg>
            </a>
        </div>

        <!-- Product list -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php
            $args = array(
                'post_type'      => 'product',
                'posts_per_page' => 3,
                'orderby'        => 'popularity',
            );

            $products = new WP_Query( $args );

            if ( $products->have_posts() ) :
                while ( $products->have_posts() ) : $products->the_post();
                    // Chargement du bon template part
                    get_template_part( 'template-parts/product-card-home' );
                endwhile;
                wp_reset_postdata();
            endif;
            ?>
        </div>
    </section>       
<?php endif; ?>


<!-- Section Particuliers / Professionnels -->
 <div class="w-full bg-celya-grey_light">
    <section class="section-container">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Carte Particuliers -->
            <div class="bg-celya-orange_light rounded-celya-m p-8 md:p-10">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-12 h-12 bg-celya-orange_dark rounded-lg flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-celya-dark" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                    </div>
                    <h2 class="font-serif text-2xl md:text-3xl font-bold text-celya-primary">Particuliers</h2>
                </div>
                
                <p class="text-celya-dark mb-4">
                    Achat direct en ligne. Biscuits personnalisés, coffrets cadeaux et gourmandises quotidiennes.
                </p>
                
                <ul class="space-y-3 mb-6">
                    <li class="flex items-start gap-2 text-celya-dark text-sm">
                        <svg class="w-5 h-5 text-celya-accent flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        <span>Lorem ipsum dolor sit amet, consectetur adipiscing elit</span>
                    </li>
                    <li class="flex items-start gap-2 text-celya-dark text-sm">
                        <svg class="w-5 h-5 text-celya-accent flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        <span>Lorem ipsum dolor sit amet, consectetur adipiscing elit</span>
                    </li>
                    <li class="flex items-start gap-2 text-celya-dark text-sm">
                        <svg class="w-5 h-5 text-celya-accent flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        <span>Lorem ipsum dolor sit amet, consectetur adipiscing elit</span>
                    </li>
                </ul>
                
                <a href="<?php echo home_url('/produits'); ?>" class="btn-celya inline-block">
                    Accéder à la boutique
                </a>
            </div>
            
            <!-- Carte Professionnels -->
            <div class="bg-celya-primary text-white rounded-celya-m p-8 md:p-10">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-12 h-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h2 class="font-serif text-2xl md:text-3xl font-bold text-white">Professionnels</h2>
                </div>
                
                <p class="mb-4 text-white">
                    Événements, cadeaux d'affaires ou épiceries fines. Découvrez nos solutions sur-mesure pour votre entreprise.
                </p>
                
                <ul class="space-y-3 mb-6">
                    <li class="flex items-start gap-2 text-white text-sm">
                        <svg class="w-5 h-5 text-celya-secondary flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        <span>Lorem ipsum dolor sit amet, consectetur adipiscing elit</span>
                    </li>
                    <li class="flex items-start gap-2 text-white text-sm">
                        <svg class="w-5 h-5 text-celya-secondary flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        <span>Lorem ipsum dolor sit amet, consectetur adipiscing elit</span>
                    </li>
                    <li class="flex items-start gap-2 text-white text-sm">
                        <svg class="w-5 h-5 text-celya-secondary flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        <span>Lorem ipsum dolor sit amet, consectetur adipiscing elit</span>
                    </li>
                </ul>
                
                <a href="<?php echo home_url('/professionnel'); ?>" class="btn-celya-orange-dark text-celya-primary inline-block">
                    Demandez votre devis
                </a>
            </div>
        </div>
    </section>
</div>
    
<!-- Section Notre Savoir-Faire -->
<section class="section-container pb-8">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center p-8 md:p-10 bg-celya-orange_light radius-default">
        <!-- Image avec logo -->
        <div class="flex justify-center">
            <div class="relative w-full max-w-md">
                <div class="flex items-center justify-center p-12">
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/logo/logo_orange_fond_orange.svg" alt="Les biscuits de Célya" class="w-full h-full object-contain">
                </div>
            </div>
        </div>
        
        <!-- Contenu texte -->
        <div class="">
            <h2 class="font-serif text-3xl md:text-4xl font-bold text-celya-primary mb-6">
                Notre savoir faire
            </h2>
            <p class="text-celya-dark mb-4">
                Lorem ipsum dolor sit amet, consectetur adipiscing elit Lorem ipsum dolor sit amet, consectetur adipiscing elit
            </p>
            <p class="text-celya-dark mb-6">
                Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
            </p>
            <a href="<?php echo home_url('/a-propos'); ?>" class="btn-celya inline-block">
                En savoir plus
            </a>
        </div>
    </div>
</section>

<!-- Section Où nous trouver ? -->
<section class="section-container pt-8">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-start">
        <div class="flex flex-col justify-center">
            <!-- Titre -->
            <div class="text-left mb-6">
                <p class="text-celya-orange_dark uppercase tracking-wider text-sm mb-2 font-semibold">Où nous trouver ?</p>
                <h2 class="text-3xl md:text-4xl font-bold text-celya-primary">Click & Collect / Vente atelier</h2>
            </div>
        
            <!-- Click & Collect -->
            <div class="p-2">
                <div class="flex items-start gap-3 mb-4">
                    <div class="w-12 h-12 bg-celya-orange_dark rounded-lg flex items-center justify-center">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/pictograms/blanc/picto_click_and_collect.svg" 
                            alt="Click & Collect" title="Click & Collect" class="w-full h-full object-contain">
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-celya-primary">Click & Collect</h3>
                        <p class="text-sm">
                            Commandez en ligne et venez récupérer votre commande sur place
                            <br><span class="text-celya-orange_dark font-bold">Uniquement Vendredi (14h-18h) & Samedi (9h-12h)</span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Fait maison -->
            <div class="p-2">
                <div class="flex items-start gap-3 mb-4">
                    <div class="w-12 h-12 bg-celya-orange_dark rounded-lg flex items-center justify-center">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/pictograms/blanc/picto_fait_maison.svg" 
                            alt="Fait maison" title="Fait maison" class="w-full h-full object-contain">
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-celya-primary">Vente spéciale dans notre atelier</h3>
                        <p class="text-sm">
                            Assortiments variables (box apéritives, brioche, pain burger, pains spéciaux)
                            <br><span class="text-celya-orange_dark font-bold">Uniquement Vendredi (14h-18h) & Samedi (9h-12h)</span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Informations adresse -->
            <div class="p-2">
                <div class="flex items-start gap-3 mb-4">
                    <div class="w-12 h-12 bg-celya-primary rounded-lg flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-celya-primary">Notre atelier</h3>
                        <p class="text-sm">
                            <span class="font-semibold">La Noue Ronde</span>, Chaudefonds-sur-Layon, 49290
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Col 2 Carte -->
        <div class="relative h-96 bg-gray-200 rounded-celya-m overflow-hidden radius-default">
            <iframe 
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d6096.878042149183!2d-0.7167287166064013!3d47.310387100768175!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x48062dbe6a81b6d1%3A0xee0b3c5c6b5c0c7b!2sLa%20Noue%20Ronde%2C%2049290%20Chaudefonds-sur-Layon!5e0!3m2!1sfr!2sfr!4v1769882513353!5m2!1sfr!2sfr" 
                width="100%" 
                height="100%" 
                style="border:0;" 
                allowfullscreen="" 
                loading="lazy" 
                referrerpolicy="no-referrer-when-downgrade">
            </iframe>
        </div>
    </div>
</section>

<!-- Section Avis Clients -->
<div class="w-full bg-celya-grey_light">
    <section class="section-container">
        <div class="flex justify-between items-center mb-12">
            <h2 class="font-serif text-3xl md:text-4xl font-bold text-celya-primary">
                Vos derniers avis
            </h2>
            <a href="<?php echo home_url('/avis'); ?>" class="text-celya-primary hover:text-celya-accent transition-colors flex items-center gap-2">
                Donner votre avis
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                </svg>
            </a>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <?php
            // Récupération des avis (à adapter selon votre système d'avis)
            $reviews = array(
                array(
                    'name' => 'Lorem ipsum dolor sitamet amet ipsum',
                    'type' => 'Pro',
                    'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
                    'rating' => 5
                ),
                array(
                    'name' => 'Lorem ipsum dolor sitamet amet ipsum',
                    'type' => 'Pro',
                    'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
                    'rating' => 5
                ),
                array(
                    'name' => 'Lorem ipsum dolor sitamet amet ipsum',
                    'type' => '',
                    'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
                    'rating' => 5
                )
            );
            
            foreach ($reviews as $review) :
            ?>
            <div class="bg-white rounded-celya-m p-6 shadow-celya">
                <!-- En-tête avis -->
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h4 class="font-semibold text-lg text-celya-orange_dark mb-1"><?php echo esc_html($review['name']); ?></h4>
                    </div>
                    <div>
                        <?php if (!empty($review['type'])) : ?>
                            <span class="inline-block bg-celya-orange_dark text-celya-dark text-xs px-2 py-1 rounded-full font-semibold">
                                <?php echo esc_html($review['type']); ?>
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Étoiles -->
                <div class="flex gap-1 mb-4">
                    <?php for ($i = 1; $i <= 5; $i++) : ?>
                        <svg class="w-5 h-5 <?php echo $i <= $review['rating'] ? 'text-yellow-400' : 'text-gray-300'; ?>" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                        </svg>
                    <?php endfor; ?>
                </div>
                
                <!-- Contenu avis -->
                <p class="text-celya-dark text-sm">
                    <?php echo esc_html($review['content']); ?>
                </p>
            </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Pagination avis (points) -->
        <div class="flex justify-center gap-2 mt-8">
            <button class="w-3 h-3 rounded-full bg-celya-primary"></button>
            <button class="w-3 h-3 rounded-full bg-gray-300 hover:bg-celya-primary transition-colors"></button>
            <button class="w-3 h-3 rounded-full bg-gray-300 hover:bg-celya-primary transition-colors"></button>
        </div>
    </section>
</div>

<!-- Section Call to Action -->
<div class="w-full bg-celya-primary">
    <section class="section-container text-white">
        <div class="relative z-10 max-w-4xl mx-auto text-center px-4">
            <h2 class="text-white text-3xl md:text-5xl font-bold mb-6">
                Prêt à succomber ?
            </h2>
            <p class="text-lg md:text-l mb-8 max-w-4xl mx-auto">
                Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation consequat.
            </p>
            <a href="<?php echo home_url('/mon-compte'); ?>" class="btn-celya-orange-dark inline-block">
                Créer votre compte
            </a>
        </div>
    </section>
</div>

<?php get_footer(); ?>