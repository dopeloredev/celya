<?php
/**
 * Template 404 - Celya
 * Page non trouvée
 */

get_header();
?>

<main id="site-content">

    <!-- =====================================================
         Héros 404
    ===================================================== -->
    <section class="min-h-[calc(100vh-5rem)] flex items-center py-4 md:py-4">
        <div class="section-container w-full">
            <div class="max-w-5xl mx-auto">

                <div class="grid lg:grid-cols-2 gap-12 lg:gap-16 items-center">

                    <!-- Colonne texte -->
                    <div class="text-center lg:text-left animate-fade-in">

                        <!-- Badge erreur -->
                        <div class="inline-flex items-center gap-2 bg-celya-orange_light text-celya-orange_dark text-xs font-semibold font-sans tracking-widest uppercase px-4 py-2 rounded-full mb-8">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                            Erreur 404
                        </div>

                        <!-- Titre -->
                        <h1 class="font-serif text-5xl lg:text-6xl font-bold text-celya-primary leading-tight mb-6">
                            Cette page<br>
                            s'est <em class="not-italic text-celya-orange_dark">émiettée&hellip;</em>
                        </h1>

                        <!-- Description -->
                        <p class="text-celya-dark text-lg leading-relaxed mb-10 max-w-md mx-auto lg:mx-0">
                            La page que vous cherchez n'est pas disponible.&nbsp;
                            Retournez à l'accueil ou explorez notre boutique.
                        </p>

                        <!-- Boutons -->
                        <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="btn-celya">
                                Retour à l'accueil
                            </a>
                            <?php if ( function_exists( 'wc_get_page_id' ) ) : ?>
                                <a href="<?php echo esc_url( get_permalink( wc_get_page_id( 'shop' ) ) ); ?>" class="btn-celya-outline">
                                    Voir la boutique
                                </a>
                            <?php else : ?>
                                <a href="<?php echo esc_url( home_url( '/shop' ) ); ?>" class="btn-celya-outline">
                                    Voir la boutique
                                </a>
                            <?php endif; ?>
                        </div>

                    </div>

                    <!-- Colonne illustration 404 -->
                    <div class="flex justify-center lg:justify-end animate-slide-in-right">
                        <div class="relative w-80 h-80">

                            <!-- Biscuit en fond -->
                            <svg class="absolute inset-0 w-full h-full opacity-30" viewBox="0 0 850 850" xmlns="http://www.w3.org/2000/svg">
                                <path fill="#F2D0A7" d="M351,819h0c-2.5-1.8-5.7-2.5-8.7-1.7h0c-53,13.3-108.5-9.7-136.5-56.6h0c-1.6-2.7-4.3-4.5-7.3-4.9h0c-54-8-96.5-50.4-104.5-104.5h0c-.5-3.1-2.3-5.8-4.9-7.3h0c-46.9-28.1-69.8-83.5-56.6-136.5h0c.8-3,.1-6.2-1.7-8.7h0c-32.6-43.9-32.6-103.9,0-147.8h0c1.8-2.5,2.5-5.7,1.7-8.7h0c-13.3-53,9.7-108.5,56.6-136.5h0c2.7-1.6,4.5-4.3,4.9-7.3h0c8-54.1,50.4-96.5,104.5-104.5h0c3.1-.5,5.8-2.3,7.3-4.9h0c28.1-46.9,83.5-69.8,136.5-56.6h0c3,.8,6.2.1,8.7-1.7h0c43.9-32.6,103.9-32.6,147.8,0h0c2.5,1.8,5.7,2.5,8.7,1.7h0c53-13.3,108.5,9.7,136.5,56.6h0c1.6,2.7,4.3,4.5,7.3,4.9h0c54.1,8,96.5,50.4,104.5,104.5h0c.5,3.1,2.3,5.8,4.9,7.3h0c46.9,28.1,69.8,83.5,56.6,136.5h0c-.8,3-.1,6.2,1.7,8.7h0c32.6,43.9,32.6,103.9,0,147.8h0c-1.8,2.5-2.5,5.7-1.7,8.7h0c13.3,53-9.7,108.5-56.6,136.5h0c-2.7,1.6-4.5,4.3-4.9,7.3h0c-8,54.1-50.4,96.5-104.5,104.5h0c-3.1.5-5.8,2.3-7.3,4.9h0c-28.1,46.9-83.5,69.8-136.5,56.6h0c-3-.8-6.2-.1-8.7,1.7h0c-43.9,32.6-103.9,32.6-147.8,0Z"></path>
                            </svg>

                            <!-- 404 graphique -->
                            <div class="absolute inset-0 flex flex-col items-center justify-center gap-1">

                                <span class="font-serif text-[7rem] font-bold text-celya-primary leading-none">
                                    4
                                </span>

                                <span class="font-serif text-[7rem] font-bold text-celya-orange_dark leading-none">
                                    0
                                </span>

                                <span class="font-serif text-[7rem] font-bold text-celya-primary leading-none">
                                    4
                                </span>

                            </div>

                        </div>
                    </div>

                </div>

            </div>
        </div>
    </section>

</main>

<?php get_footer(); ?>
