<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <?php wp_head(); ?>
</head>

<body <?php body_class( 'bg-celya-light' ); ?>>
<?php wp_body_open(); ?>

<header class="sticky top-0 z-50 bg-white shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-20">
            
            <!-- Logo -->
            <div class="flex-shrink-0">
                <a href="<?php echo esc_url(home_url('/')); ?>" class="flex items-center">

                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/logo/logo_celya_header_desktop.svg" 
                             alt="<?php bloginfo('name'); ?>" 
                             class="h-16 w-auto" style="height: 4rem;">

                    <?php //if (has_custom_logo()) : ?>
                        <?php //the_custom_logo(); ?>
                    <?php //else : ?>
                        <!--
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/logo/logo_marron_fond_blanc.svg" 
                             alt="<?php bloginfo('name'); ?>" 
                             class="h-12 w-auto">
                    -->
                    <?php //endif; ?>
                </a>
            </div>

            <!-- Navigation principale (Desktop) -->
            <nav class="hidden lg:flex items-center space-x-8">
                <?php
                wp_nav_menu(array(
                    'theme_location' => 'primary',
                    'container'      => false,
                    'menu_class'     => 'flex items-center space-x-8',
                    'fallback_cb'    => false,
                    'walker'         => new Celya_Walker_Nav_Menu(),
                ));
                ?>
            </nav>

            <!-- Icônes Compte + Panier + Mobile Menu (Desktop) -->
            <div class="hidden lg:flex items-center space-x-6">
                
                <!-- Icône Mon Compte -->
                <a href="<?php echo esc_url(wc_get_page_permalink('myaccount')); ?>" 
                   class="flex items-center gap-2 text-celya-dark hover:text-celya-orange_dark transition-colors group"
                   title="Mon compte">
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/pictograms/marron/mon_compte.svg" 
                         alt="Mon compte" 
                         class="w-14 h-14 transition-transform group-hover:scale-110">
                </a>

                <!-- Icône Panier avec compteur -->
                <?php if (class_exists('WooCommerce')) : ?>
                    <a href="<?php echo esc_url(wc_get_cart_url()); ?>" 
                       class="relative flex items-center gap-2 text-celya-dark hover:text-celya-orange_dark transition-colors group"
                       title="Mon panier">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/pictograms/marron/panier.svg" 
                             alt="Panier" 
                             class="w-14 h-14 transition-transform group-hover:scale-110">
                        
                        <!-- Compteur panier -->
                        <?php 
                        $cart_count = WC()->cart->get_cart_contents_count();
                        if ($cart_count > 0) : 
                        ?>
                            <span class="absolute -top-1 -right-1 bg-celya-primary text-white text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center shadow-md">
                                <?php echo $cart_count; ?>
                            </span>
                        <?php endif; ?>
                    </a>
                <?php endif; ?>
            </div>

            <!-- Burger Menu (Mobile) -->
            <div class="lg:hidden flex items-center space-x-4">
                
                <!-- Icône Panier Mobile -->
                <?php if (class_exists('WooCommerce')) : ?>
                    <a href="<?php echo esc_url(wc_get_cart_url()); ?>" 
                       class="relative text-celya-dark"
                       title="Mon panier">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/pictograms/marron/panier.svg" 
                             alt="Panier" 
                             class="w-7 h-7">
                        
                        <?php 
                        $cart_count = WC()->cart->get_cart_contents_count();
                        if ($cart_count > 0) : 
                        ?>
                            <span class="absolute -top-1 -right-1 bg-celya-primary text-white text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center shadow-md">
                                <?php echo $cart_count; ?>
                            </span>
                        <?php endif; ?>
                    </a>
                <?php endif; ?>

                <!-- Bouton Burger -->
                <button type="button" 
                        class="text-celya-dark hover:text-celya-primary focus:outline-none"
                        aria-label="Menu mobile"
                        id="mobile-menu-button">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>

        </div>
    </div>

    <!-- Menu Mobile (caché par défaut) -->
    <div id="mobile-menu" class="hidden lg:hidden bg-white border-t border-gray-200">
        <div class="px-4 pt-4 pb-6 space-y-4">
            
            <!-- Navigation mobile -->
            <?php
            wp_nav_menu(array(
                'theme_location' => 'primary',
                'container'      => false,
                'menu_class'     => 'space-y-3',
                'fallback_cb'    => false,
                'items_wrap'     => '<ul class="%2$s">%3$s</ul>',
                'link_before'    => '<span class="block py-2 font-sans text-sm font-medium uppercase tracking-wide text-celya-dark hover:text-celya-primary transition-colors">',
                'link_after'     => '</span>',
            ));
            ?>

            <!-- Lien Mon compte (mobile) -->
            <div class="pt-4 border-t border-gray-200">
                <a href="<?php echo esc_url(wc_get_page_permalink('myaccount')); ?>" 
                   class="flex items-center gap-3 py-2 font-sans text-sm font-medium uppercase tracking-wide text-celya-dark hover:text-celya-primary transition-colors">
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/pictograms/marron/mon_compte.svg" 
                         alt="Mon compte" 
                         class="w-5 h-5">
                    <span>Mon compte</span>
                </a>
            </div>

        </div>
    </div>
</header>

<!-- JavaScript pour le menu mobile -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const mobileMenuButton = document.getElementById('mobile-menu-button');
    const mobileMenu = document.getElementById('mobile-menu');
    
    if (mobileMenuButton && mobileMenu) {
        mobileMenuButton.addEventListener('click', function() {
            mobileMenu.classList.toggle('hidden');
            
            // Animation du burger en X
            const svg = this.querySelector('svg');
            if (mobileMenu.classList.contains('hidden')) {
                svg.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>';
            } else {
                svg.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>';
            }
        });
    }
});
</script>

<?php
/**
 * Hook après l'ouverture du body
 * Permet d'ajouter du contenu après le header (breadcrumb, etc.)
 */
do_action('celya_after_header');
?>
