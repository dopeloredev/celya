<?php
    /**
     * Template Page d'accueil - Celya
     */

    get_header();
?>

<main id="site-content">
  <?php
  while ( have_posts() ) :
    the_post();
    the_content();
  endwhile;
  ?>
</main>

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

<?php get_footer(); ?>