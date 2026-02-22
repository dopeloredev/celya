<?php
/**
 * Single Product tabs
 *
 * @package Celya
 * @version 8.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Filter tabs and allow third parties to add their own.
 * On retire l'onglet "reviews" pour l'afficher séparément en dessous.
 */
$product_tabs = apply_filters( 'woocommerce_product_tabs', array() );

// Retirer l'onglet avis des onglets — il sera affiché séparément
unset( $product_tabs['reviews'] );

if ( ! empty( $product_tabs ) ) : ?>

    <div class="woocommerce-tabs">
        
        <!-- Navigation des onglets -->
        <ul class="tabs wc-tabs overflow-x-auto" role="tablist">
            <?php 
            $tab_index = 0;
            foreach ( $product_tabs as $key => $product_tab ) : 
                $is_active = $tab_index === 0 ? 'active' : '';
                ?>
                <li class="<?php echo esc_attr( $key ); ?>_tab <?php echo esc_attr( $is_active ); ?> flex-shrink-0" 
                    id="tab-title-<?php echo esc_attr( $key ); ?>" 
                    role="tab" 
                    aria-controls="tab-<?php echo esc_attr( $key ); ?>">
                    
                    <a href="#tab-<?php echo esc_attr( $key ); ?>" class="text-sm whitespace-nowrap">
                        <?php echo wp_kses_post( apply_filters( 'woocommerce_product_' . $key . '_tab_title', $product_tab['title'], $key ) ); ?>
                    </a>
                </li>
            <?php 
                $tab_index++;
            endforeach; 
            ?>
        </ul>
        
        <!-- Contenu des onglets -->
        <div class="wc-tab-content pt-8 pb-12 px-1">
            <?php 
            $tab_index = 0;
            foreach ( $product_tabs as $key => $product_tab ) : 
                $is_active = $tab_index === 0 ? 'active' : '';
                ?>
                <div class="woocommerce-Tabs-panel woocommerce-Tabs-panel--<?php echo esc_attr( $key ); ?> panel entry-content wc-tab <?php echo esc_attr( $is_active ); ?>" id="tab-<?php echo esc_attr( $key ); ?>" role="tabpanel" aria-labelledby="tab-title-<?php echo esc_attr( $key ); ?>" <?php echo $tab_index === 0 ? '' : 'style="display:none;"'; ?>>
                    <?php
                    if ( isset( $product_tab['callback'] ) ) {
                        call_user_func( $product_tab['callback'], $key, $product_tab );
                    }
                    ?>
                </div>
            <?php 
                $tab_index++;
            endforeach; 
            ?>
        </div>

        <?php do_action( 'woocommerce_product_after_tabs' ); ?>
        
    </div>

    <script>
    jQuery(document).ready(function($) {
        // Gestion des onglets
        $('.wc-tabs li a').on('click', function(e) {
            e.preventDefault();
            
            var target = $(this).attr('href');
            
            // Désactiver tous les onglets
            $('.wc-tabs li').removeClass('active');
            $('.wc-tabs li a').removeClass('text-celya-orange_dark').addClass('text-celya-dark');
            $('.wc-tab').hide().removeClass('active');
            
            // Activer l'onglet cliqué
            $(this).closest('li').addClass('active');
            $(this).removeClass('text-celya-dark').addClass('text-celya-orange_dark');
            $(target).show().addClass('active');
        });
    });
    </script>

<?php endif; ?>