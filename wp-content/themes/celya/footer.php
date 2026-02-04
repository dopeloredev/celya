</main>

<!-- Footer -->
    <footer class="bg-white border-t border-gray-200">
        
        <!-- Section Contact + Informations footer -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-8 gap-8">
                
                <!-- Colonne 1 : Contact -->
                <div class="lg:col-span-2">
                    <h3 class="font-serif text-lg font-bold text-celya-orange_dark mb-4">Contact</h3>
                    <ul class="space-y-3 text-sm text-celya-dark">
                        <li class="flex items-start gap-2">
                            <svg class="w-5 h-5 text-celya-orange_dark flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            <a href="mailto:<?php echo get_option('celya_email', 'contact@celya.fr'); ?>" 
                               class="hover:text-celya-orange_dark transition-colors">
                                <?php echo get_option('celya_email', 'contact@celya.fr'); ?>
                            </a>
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="w-5 h-5 text-celya-orange_dark flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            <a href="tel:<?php echo str_replace(' ', '', get_option('celya_phone', '0241000000')); ?>" 
                               class="hover:text-celya-orange_dark transition-colors">
                                <?php echo get_option('celya_phone', '02 41 00 00 00'); ?>
                            </a>
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="w-5 h-5 text-celya-orange_dark flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <address class="not-italic">
                                <?php echo get_option('celya_address', 'La Noue Ronde,<br>1712 route de l\'Orchère,<br>49290 CHAUDEFONDS SUR LAYON'); ?>
                            </address>
                        </li>
                    </ul>
                </div>

                <!-- Colonne 2 : Produits -->
                <div>
                    <h3 class="font-serif text-lg font-bold text-celya-orange_dark mb-4">Produits</h3>
                    <?php
                    wp_nav_menu(array(
                        'theme_location' => 'footer-produits',
                        'container'      => false,
                        'menu_class'     => 'space-y-2 text-sm',
                        'fallback_cb'    => false,
                        'link_before'    => '<span class="text-celya-dark hover:text-celya-orange_dark transition-colors">',
                        'link_after'     => '</span>',
                    ));
                    ?>
                </div>

                <!-- Colonne 3 : Services -->
                <div>
                    <h3 class="font-serif text-lg font-bold text-celya-orange_dark mb-4">Services</h3>
                    <?php
                    wp_nav_menu(array(
                        'theme_location' => 'footer-services',
                        'container'      => false,
                        'menu_class'     => 'space-y-2 text-sm',
                        'fallback_cb'    => false,
                        'link_before'    => '<span class="text-celya-dark hover:text-celya-orange_dark transition-colors">',
                        'link_after'     => '</span>',
                    ));
                    ?>
                </div>

                <!-- Colonne 4 : À propos -->
                <div>
                    <h3 class="font-serif text-lg font-bold text-celya-orange_dark mb-4">À propos</h3>
                    <?php
                    wp_nav_menu(array(
                        'theme_location' => 'footer-apropos',
                        'container'      => false,
                        'menu_class'     => 'space-y-2 text-sm',
                        'fallback_cb'    => false,
                        'link_before'    => '<span class="text-celya-dark hover:text-celya-orange_dark transition-colors">',
                        'link_after'     => '</span>',
                    ));
                    ?>
                </div>

                <!-- Colonne 5 : Informations -->
                <div>
                    <h3 class="font-serif text-lg font-bold text-celya-orange_dark mb-4">Informations</h3>
                    <?php
                    wp_nav_menu(array(
                        'theme_location' => 'footer-informations',
                        'container'      => false,
                        'menu_class'     => 'space-y-2 text-sm',
                        'fallback_cb'    => false,
                        'link_before'    => '<span class="text-celya-dark hover:text-celya-orange_dark transition-colors">',
                        'link_after'     => '</span>',
                    ));
                    ?>
                </div>

                <!-- Colonne 6 : Suivez-nous -->
                <div class="lg:col-span-2">
                    <h3 class="font-serif text-lg font-bold text-celya-orange_dark mb-4">Suivez-nous</h3>
                    <div class="flex items-center gap-4">

                        <!-- Instagram -->
                        <?php if (get_option('celya_instagram')) : ?>
                            <a href="<?php echo esc_url(get_option('celya_instagram')); ?>" 
                               target="_blank" 
                               rel="noopener noreferrer"
                               class="w-10 h-10 flex items-center justify-center rounded-full bg-celya-orange_light text-celya-primary hover:bg-celya-orange_dark hover:text-white transition-colors"
                               title="Instagram">

                                <div class="flex justify-center">
                                    <div class="relative w-full max-w-md">
                                        <div class="flex items-center justify-center p-12">
                                            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/pictograms/blanc/socials_instagram.svg" 
                                                alt="Instagram Célya" 
                                                title="Instagram Célya" 
                                                class="w-full h-full object-contain">
                                        </div>
                                    </div>
                                </div>       
                            </a>
                        <?php endif; ?>

                        <!-- Facebook -->
                        <?php if (get_option('celya_facebook')) : ?>
                            <a href="<?php echo esc_url(get_option('celya_facebook')); ?>" 
                               target="_blank" 
                               rel="noopener noreferrer"
                               class="w-10 h-10 flex items-center justify-center rounded-full bg-celya-orange_light text-celya-primary hover:bg-celya-orange_dark hover:text-white transition-colors"
                               title="Facebook">

                                <div class="flex justify-center">
                                    <div class="relative w-full max-w-md">
                                        <div class="flex items-center justify-center p-12">
                                            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/pictograms/blanc/socials_facebook.svg" 
                                                alt="Facebook Célya" 
                                                title="Facebook Célya" 
                                                class="w-full h-full object-contain">
                                        </div>
                                    </div>
                                </div>                                   
                               
                            </a>
                        <?php endif; ?>

                        <!-- LinkedIn -->
                        <?php if (get_option('celya_linkedin')) : ?>
                            <a href="<?php echo esc_url(get_option('celya_linkedin')); ?>" 
                               target="_blank" 
                               rel="noopener noreferrer"
                               class="w-10 h-10 flex items-center justify-center rounded-full bg-celya-orange_light text-celya-primary hover:bg-celya-orange_dark hover:text-white transition-colors"
                               title="LinkedIn">
                                <div class="flex justify-center">
                                    <div class="relative w-full max-w-md">
                                        <div class="flex items-center justify-center p-12">
                                            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/pictograms/blanc/socials_linkedin.svg" 
                                                alt="LinkedIn Célya" 
                                                title="LinkedIn Célya" 
                                                class="w-full h-full object-contain">
                                        </div>
                                    </div>
                                </div>      
                            </a>
                        <?php endif; ?>
                    </div>

                    <!-- Avis Google (optionnel) -->
                    <div class="mt-6 mb-2">
                        <div class="flex items-center gap-1">
                            <p class="text-sm text-celya-dark pr-2">Avis</p>
                            <?php
                            // Afficher 5 étoiles (à personnaliser avec vraies données)
                            for ($i = 0; $i < 5; $i++) :
                            ?>
                                <svg class="w-5 h-5 text-celya-orange_dark" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            <?php endfor; ?>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Section Copyright -->
        <div class="border-t border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex flex-col md:flex-row justify-between items-center gap-4 text-sm text-celya-dark">
                    
                    <!-- Copyright -->
                    <p class="font-sans text-xs">
                        Les biscuits de Célya © <?php echo date('Y'); ?> - Tous droits réservés.
                    </p>

                    <!-- Liens légaux -->
                    <nav class="flex items-center gap-6">
                        <a href="<?php echo get_privacy_policy_url(); ?>" 
                           class="hover:text-celya-orange_dark transition-colors text-xs">
                            Politique de confidentialité
                        </a>
                        <a href="<?php echo home_url('/mentions-legales'); ?>" 
                           class="hover:text-celya-orange_dark transition-colors text-xs">
                            Mentions légales
                        </a>
                        <a href="<?php echo home_url('/cgv'); ?>" 
                           class="hover:text-celya-orange_dark transition-colors text-xs">
                            CGV
                        </a>
                        <?php if (function_exists('wc_get_page_permalink')) : ?>
                            <a href="<?php echo esc_url(wc_get_page_permalink('terms')); ?>" 
                               class="hover:text-celya-orange_dark transition-colors text-xs">
                                CGU
                            </a>
                        <?php endif; ?>
                    </nav>

                </div>
            </div>
        </div>

    </footer>

    <?php wp_footer(); ?>
</body>
</html>