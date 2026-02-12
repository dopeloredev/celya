<?php
/**
 * Single variation cart button
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 7.0.1
 */

defined( 'ABSPATH' ) || exit;

global $product;
?>
<div class="woocommerce-variation-add-to-cart variations_button">
	<?php do_action( 'woocommerce_before_add_to_cart_button' ); ?>

	<!-- Conteneur Quantité + Bouton -->
	<div class="flex flex-col sm:flex-row items-stretch gap-4 mt-6">
		
		<!-- Sélecteur de quantité -->
		<div class="flex sm:flex-1 items-center bg-white border-2 border-celya-light rounded-xl overflow-hidden">
			<button type="button" class="quantity-minus w-12 h-14 flex items-center justify-center text-celya-dark hover:bg-celya-light transition-colors" aria-label="Diminuer la quantité">
				<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
				</svg>
			</button>
			
			<?php
			do_action( 'woocommerce_before_add_to_cart_quantity' );

			woocommerce_quantity_input(
				array(
					'min_value'   => apply_filters( 'woocommerce_quantity_input_min', $product->get_min_purchase_quantity(), $product ),
					'max_value'   => apply_filters( 'woocommerce_quantity_input_max', $product->get_max_purchase_quantity(), $product ),
					'input_value' => isset( $_POST['quantity'] ) ? wc_stock_amount( wp_unslash( $_POST['quantity'] ) ) : $product->get_min_purchase_quantity(),
					'classes'     => array( 'w-16', 'h-14', 'text-center', 'border-0', 'font-bold', 'text-celya-dark', 'text-lg' ),
				)
			);

			do_action( 'woocommerce_after_add_to_cart_quantity' );
			?>
			
			<button type="button" class="quantity-plus w-12 h-14 flex items-center justify-center text-celya-dark hover:bg-celya-light transition-colors" aria-label="Augmenter la quantité">
				<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
				</svg>
			</button>
		</div>

		<!-- Bouton Ajouter au panier -->
		<button type="submit" class="single_add_to_cart_button sm:flex-3 <?php echo esc_attr( wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : '' ); ?>">
			<img src="<?php echo get_template_directory_uri(); ?>/assets/images/pictograms/blanc/add_to_cart.svg" width="50" alt="Ajouter au panier" title="Ajouter au panier">
			<span>Ajouter au panier</span>
		</button>

	</div>

	<?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>

	<input type="hidden" name="add-to-cart" value="<?php echo absint( $product->get_id() ); ?>" />
	<input type="hidden" name="product_id" value="<?php echo absint( $product->get_id() ); ?>" />
	<input type="hidden" name="variation_id" class="variation_id" value="0" />
</div>

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