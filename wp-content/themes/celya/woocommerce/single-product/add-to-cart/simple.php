<?php
/**
 * Simple product add to cart
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

    <?php
    $price     = (float) $product->get_price();
    $weight_kg = (float) $product->get_weight();

    if ( $price > 0 ) :
        $price_formatted = number_format( $price, 2, ',', '' );
    ?>
    <!-- Bloc prix — même style que .single_variation des produits variables -->
    <div class="bg-celya-orange_light rounded-xl p-4 mb-6">
        <div class="flex items-baseline gap-3 mb-1">
            <span class="text-2xl font-extrabold text-celya-primary">
                <?php echo esc_html( $price_formatted ); ?>&nbsp;€
            </span>

            <?php if ( $weight_kg > 0 ) :
                $weight_g = round( $weight_kg * 1000 );
            ?>
                <div class="text-sm text-celya-orange_dark font-bold">
                    / Sachet de <?php echo esc_html( $weight_g ); ?>g
                </div>
            <?php endif; ?>
        </div>

        <?php if ( $weight_kg > 0 && $price > 0 ) :
            $price_per_kg         = $price / $weight_kg;
            $price_per_kg_formatted = number_format( $price_per_kg, 2, ',', '' );
        ?>
            <div class="text-xs text-gray-500">
                (<?php echo esc_html( $price_per_kg_formatted ); ?>€ le Kg)
            </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <form class="cart" action="<?php echo esc_url( apply_filters( 'woocommerce_add_to_cart_form_action', $product->get_permalink() ) ); ?>" method="post" enctype='multipart/form-data'>

        <?php do_action( 'woocommerce_before_add_to_cart_button' ); ?>

        <!-- Conteneur Quantité + Bouton — même structure que variation-add-to-cart-button.php -->
        <div class="flex flex-col sm:flex-row items-stretch gap-4 mt-6 pr-4 w-full">

            <!-- Sélecteur de quantité -->
            <div class="quantity-wrapper flex items-center bg-white border-2 border-celya-light rounded-xl overflow-hidden" style="flex: 0 0 30%;">

                <button type="button" class="quantity-minus w-10 h-14 flex items-center justify-center text-celya-dark hover:bg-celya-light transition-colors" aria-label="Diminuer la quantité">
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
                        'classes'     => array( 'h-14', 'qty', 'text-center', 'border-0', 'font-bold', 'text-celya-dark', 'text-lg' ),
                    )
                );
                ?>

                <button type="button" class="quantity-plus w-10 h-14 flex items-center justify-center text-celya-dark hover:bg-celya-light transition-colors" aria-label="Augmenter la quantité">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                </button>

            </div>

            <!-- Bouton Ajouter au panier -->
            <button type="submit" name="add-to-cart" value="<?php echo esc_attr( $product->get_id() ); ?>" class="single_add_to_cart_button<?php echo esc_attr( wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : '' ); ?>" style="flex: 0 0 70%;">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/pictograms/blanc/add_to_cart.svg" width="50" alt="Ajouter au panier" title="Ajouter au panier">
                <span>Ajouter au panier</span>
            </button>

        </div>

        <?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>

    </form>

    <script>
    jQuery(document).ready(function($) {
        $('.quantity-minus').on('click', function() {
            var $input = $(this).closest('.quantity-wrapper').find('input.qty');
            var currentVal = parseInt($input.val()) || 1;
            var minVal     = parseInt($input.attr('min')) || 1;

            if (currentVal > minVal) {
                $input.val(currentVal - 1).trigger('change');
            }
        });

        $('.quantity-plus').on('click', function() {
            var $input = $(this).closest('.quantity-wrapper').find('input.qty');
            var currentVal = parseInt($input.val()) || 1;
            var maxVal     = parseInt($input.attr('max')) || 9999;

            if (currentVal < maxVal) {
                $input.val(currentVal + 1).trigger('change');
            }
        });
    });
    </script>

    <?php do_action( 'woocommerce_after_add_to_cart_form' ); ?>

<?php endif; ?>
