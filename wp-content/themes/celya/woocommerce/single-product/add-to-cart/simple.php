<?php
/**
 * Simple product add to cart - Version 2 moderne
 *
 * @package Celya
 * @version 8.6.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

if ( ! $product->is_purchasable() ) {
    return;
}

echo wc_get_stock_html( $product );

if ( $product->is_in_stock() ) : ?>

    <?php do_action( 'woocommerce_before_add_to_cart_form' ); ?>

    <form class="cart" action="<?php echo esc_url( apply_filters( 'woocommerce_add_to_cart_form_action', $product->get_permalink() ) ); ?>" method="post" enctype='multipart/form-data'>
        
        <?php do_action( 'woocommerce_before_add_to_cart_button' ); ?>
        
        <!-- Conteneur Quantité + Bouton -->
        <div class="flex flex-col sm:flex-row items-stretch gap-4 mb-6">
            
            <!-- Sélecteur de quantité moderne -->
            <div class="flex items-center bg-white border-2 border-celya-light rounded-xl overflow-hidden">
                <button type="button" class="quantity-minus w-12 h-14 flex items-center justify-center text-celya-dark hover:bg-celya-light transition-colors" aria-label="Diminuer la quantité">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                    </svg>
                </button>
                
                <?php
                woocommerce_quantity_input(
                    array(
                        'min_value'   => apply_filters( 'woocommerce_quantity_input_min', $product->get_min_purchase_quantity(), $product ),
                        'max_value'   => apply_filters( 'woocommerce_quantity_input_max', $product->get_max_purchase_quantity(), $product ),
                        'input_value' => isset( $_POST['quantity'] ) ? wc_stock_amount( wp_unslash( $_POST['quantity'] ) ) : $product->get_min_purchase_quantity(),
                        'classes'     => array( 'w-16', 'h-14', 'text-center', 'border-0', 'font-bold', 'text-celya-dark', 'text-lg' ),
                    )
                );
                ?>
                
                <button type="button" class="quantity-plus w-12 h-14 flex items-center justify-center text-celya-dark hover:bg-celya-light transition-colors" aria-label="Augmenter la quantité">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                </button>
            </div>

            <!-- Bouton Ajouter au panier moderne -->
            <button type="submit" name="add-to-cart" value="<?php echo esc_attr( $product->get_id() ); ?>" class="single_add_to_cart_button button alt flex-1 bg-gradient-to-r from-celya-orange_dark to-celya-primary hover:from-celya-primary hover:to-celya-orange_dark text-white font-bold py-4 px-8 rounded-xl transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 flex items-center justify-center gap-3 text-base">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
                <span>Ajouter au panier</span>
            </button>

        </div>

        <?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>
        
    </form>

    <script>
    jQuery(document).ready(function($) {
        // Gestion des boutons quantité
        $('.quantity-minus').on('click', function() {
            var $input = $(this).siblings('input.qty');
            var currentVal = parseInt($input.val());
            var minVal = parseInt($input.attr('min')) || 1;
            
            if (currentVal > minVal) {
                $input.val(currentVal - 1).trigger('change');
            }
        });
        
        $('.quantity-plus').on('click', function() {
            var $input = $(this).siblings('input.qty');
            var currentVal = parseInt($input.val());
            var maxVal = parseInt($input.attr('max')) || 9999;
            
            if (currentVal < maxVal) {
                $input.val(currentVal + 1).trigger('change');
            }
        });
    });
    </script>

    <?php do_action( 'woocommerce_after_add_to_cart_form' ); ?>

<?php endif; ?>