<?php
/**
 * Single variation display
 *
 * This is a javascript-based template for single variations (see https://codex.wordpress.org/Javascript_Reference/wp.template).
 * The values will be dynamically replaced after selecting attributes.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 9.3.0
 */

defined( 'ABSPATH' ) || exit;

?>
<script type="text/template" id="tmpl-variation-template">
	<div class="woocommerce-variation-price">
		<div class="flex items-baseline gap-3 mb-2">
			<span class="text-2xl font-extrabold text-celya-primary">
				{{{ data.variation.price_html }}}
			</span>

			<# if ( data.variation.weight ) { #>
				<# // Convertir le poids en grammes
				var weightKg = parseFloat(data.variation.weight); // Poids en kg
				var weightG = Math.round(weightKg * 1000); // Convertir en grammes
				#>
			
				<div class="text-sm text-celya-orange_dark font-bold mt-2">
					/ Sachet de {{{ weightG }}}g
				</div>
			<# } #>
		</div>
		
		<# if ( data.variation.weight ) { #>
			<# 
			// Calculer le prix au kilo
			var price = parseFloat(data.variation.display_price);
			
			if (weightKg > 0 && price > 0) {
				var pricePerKg = (price / weightKg); // Prix au kilo
				var formattedPricePerKg = pricePerKg.toFixed(2).replace('.', ',');
			#>
				<div class="text-xs text-gray-500 mt-1">
					({{{ formattedPricePerKg }}}€ le Kg)
				</div>
			<# } #>
		<# } #>
	</div>
</script>

<script type="text/template" id="tmpl-unavailable-variation-template">
	<p role="alert" class="text-red-600 font-semibold">
		<?php esc_html_e( 'Ce produit n\'est pas disponible pour le moment. Veuillez sélectionner une autre saveur.', 'woocommerce' ); ?>
	</p>
</script>