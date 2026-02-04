<?php get_header(); ?>

<div class="section-container">
    <div class="max-w-4xl mx-auto">
        <?php if ( have_posts() ) : ?>
            <div class="space-y-8">
                <?php while ( have_posts() ) : the_post(); ?>
                    <article <?php post_class( 'bg-white rounded-celya shadow-celya p-6' ); ?>>
                        <?php if ( has_post_thumbnail() ) : ?>
                            <div class="mb-4 rounded-lg overflow-hidden">
                                <?php the_post_thumbnail( 'large', array( 'class' => 'w-full h-auto' ) ); ?>
                            </div>
                        <?php endif; ?>
                        
                        <h2 class="text-3xl font-serif font-bold mb-4 text-celya-primary">
                            <a href="<?php the_permalink(); ?>" class="hover:text-celya-accent transition">
                                <?php the_title(); ?>
                            </a>
                        </h2>
                        
                        <div class="prose max-w-none text-gray-700">
                            <?php the_excerpt(); ?>
                        </div>
                        
                        <a href="<?php the_permalink(); ?>" 
                           class="inline-block mt-4 btn-celya-secondary">
                            Lire la suite
                        </a>
                    </article>
                <?php endwhile; ?>
            </div>
            
            <!-- Pagination -->
            <div class="mt-12">
                <?php the_posts_pagination( array(
                    'mid_size'  => 2,
                    'prev_text' => '← Précédent',
                    'next_text' => 'Suivant →',
                )); ?>
            </div>
        <?php else : ?>
            <p class="text-center text-gray-500">Aucun contenu trouvé.</p>
        <?php endif; ?>
    </div>
</div>

<?php get_footer(); ?>