<?php
/**
 * Variable product add to cart
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/add-to-cart/variable.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 9.6.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

$attribute_keys  = array_keys( $attributes );
$variations_json = wp_json_encode( $available_variations );
$variations_attr = function_exists( 'wc_esc_json' ) ? wc_esc_json( $variations_json ) : _wp_specialchars( $variations_json, ENT_QUOTES, 'UTF-8', true );

do_action( 'woocommerce_before_add_to_cart_form' ); ?>

<form class="variations_form cart" action="<?php echo esc_url( apply_filters( 'woocommerce_add_to_cart_form_action', $product->get_permalink() ) ); ?>" method="post" enctype='multipart/form-data' data-product_id="<?php echo absint( $product->get_id() ); ?>" data-product_variations="<?php echo $variations_attr; // WPCS: XSS ok. ?>">
	<?php do_action( 'woocommerce_before_variations_form' ); ?>

	<?php if ( empty( $available_variations ) && false !== $available_variations ) : ?>
		<p class="stock out-of-stock bg-red-50 border-l-4 border-red-500 p-4 rounded-lg text-red-700">
			<?php echo esc_html( apply_filters( 'woocommerce_out_of_stock_message', __( 'Ce produit est actuellement en rupture de stock et indisponible.', 'woocommerce' ) ) ); ?>
		</p>
	<?php else : ?>
		
		<!-- Sélecteurs de variations avec style moderne -->
		<div class="variations space-y-6 mb-6">
			<?php foreach ( $attributes as $attribute_name => $options ) : ?>
				<?php
				$attribute_label = wc_attribute_label( $attribute_name );
				$sanitized_name = sanitize_title( $attribute_name );
				?>
				
				<div class="variation-selector">
					<label for="<?php echo esc_attr( $sanitized_name ); ?>">
						<?php echo "Sélectionner votre ".esc_html( $attribute_label ); ?>
					</label>
					
					<?php if ( is_array( $options ) ) : ?>
						<!-- Boutons de sélection modernes -->
						<div class="variation-buttons grid grid-cols-2 sm:grid-cols-3 gap-3" data-attribute="<?php echo esc_attr( $sanitized_name ); ?>">
							<?php
							// Récupérer les termes de l'attribut
							if ( taxonomy_exists( $attribute_name ) ) {
								$terms = wc_get_product_terms( $product->get_id(), $attribute_name, array( 'fields' => 'all' ) );
								
								foreach ( $terms as $term ) {
									if ( in_array( $term->slug, $options, true ) ) {
										$option_label = apply_filters( 'woocommerce_variation_option_name', $term->name, $term, $attribute_name, $product );
										?>
										<button 
											type="button" 
											class="variation-option relative px-4 py-3 bg-white border-2 border-celya-light rounded-xl text-sm font-semibold text-celya-dark hover:border-celya-orange_dark hover:bg-celya-orange_light transition-all duration-200 shadow-sm hover:shadow-md" 
											data-value="<?php echo esc_attr( $term->slug ); ?>"
											aria-label="<?php echo esc_attr( sprintf( __( 'Sélectionner %s', 'celya' ), $option_label ) ); ?>">
											<span class="relative z-10"><?php echo esc_html( $option_label ); ?></span>
										</button>
										<?php
									}
								}
							} else {
								foreach ( $options as $option ) {
									$option_label = apply_filters( 'woocommerce_variation_option_name', $option, null, $attribute_name, $product );
									?>
									<button 
										type="button" 
										class="variation-option relative px-4 py-3 bg-white border-2 border-celya-light rounded-xl text-sm font-semibold text-celya-dark hover:border-celya-orange_dark hover:bg-celya-orange_light transition-all duration-200 shadow-sm hover:shadow-md" 
										data-value="<?php echo esc_attr( $option ); ?>"
										aria-label="<?php echo esc_attr( sprintf( __( 'Sélectionner %s', 'celya' ), $option_label ) ); ?>">
										<span class="relative z-10"><?php echo esc_html( $option_label ); ?></span>
									</button>
									<?php
								}
							}
							?>
						</div>
						
						<!-- Select caché pour la compatibilité WooCommerce -->
						<?php
						wc_dropdown_variation_attribute_options(
							array(
								'options'   => $options,
								'attribute' => $attribute_name,
								'product'   => $product,
								'class'     => 'hidden',
								'id'        => esc_attr( $sanitized_name ),
							)
						);
						?>
					<?php else : ?>
						<!-- Fallback avec select standard moderne -->
						<?php
						wc_dropdown_variation_attribute_options(
							array(
								'options'   => $options,
								'attribute' => $attribute_name,
								'product'   => $product,
								'class'     => 'w-full px-4 py-3 bg-white border-2 border-celya-light rounded-xl focus:outline-none focus:ring-2 focus:ring-celya-orange_dark focus:border-celya-orange_dark',
							)
						);
						?>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>
		
		<div class="reset_variations_alert screen-reader-text" role="alert" aria-live="polite" aria-relevant="all"></div>
		
		<?php do_action( 'woocommerce_after_variations_table' ); ?>

		<div class="single_variation_wrap">
			<?php
			/**
			 * Hook: woocommerce_before_single_variation.
			 */
			do_action( 'woocommerce_before_single_variation' );

			/**
			 * Hook: woocommerce_single_variation. Used to output the cart button and placeholder for variation data.
			 *
			 * @since 2.4.0
			 * @hooked woocommerce_single_variation - 10 Empty div for variation data.
			 * @hooked woocommerce_single_variation_add_to_cart_button - 20 Qty and cart button.
			 */
			do_action( 'woocommerce_single_variation' );

			/**
			 * Hook: woocommerce_after_single_variation.
			 */
			do_action( 'woocommerce_after_single_variation' );
			?>
		</div>
	<?php endif; ?>

	<?php do_action( 'woocommerce_after_variations_form' ); ?>
</form>

<script>
jQuery(document).ready(function($) {
	
	// ========== GESTION DES BOUTONS DE VARIATION ==========
	
	$('.variation-option').on('click', function(e) {
        e.preventDefault();
        
        var $button = $(this);
        var $container = $button.closest('.variation-buttons');
        var value = $button.data('value');
        var $select = $container.siblings('select');
        
        // Désélectionner tous les boutons de ce groupe
        $container.find('.variation-option')
            .removeClass('border-celya-orange_dark bg-celya-orange_light selected');
        
        // Sélectionner le bouton cliqué
        $button.addClass('border-celya-orange_dark bg-celya-orange_light selected');
        
        // Mettre à jour le select caché
        $select.val(value).trigger('change');
    });
	
	// ========== SYNCHRONISER LES BOUTONS AVEC LE SELECT ==========
	
	/**
	 * Fonction pour synchroniser l'affichage des boutons avec la valeur du select
	 */
    function syncButtonsWithSelect() {
        $('.variation-selector').each(function() {
            var $selector = $(this);
            var $select = $selector.find('select[name^="attribute_"]');
            var $buttons = $selector.find('.variation-buttons');
            var selectedValue = $select.val();
            
            if (selectedValue && selectedValue !== '') {
                // Désactiver tous les boutons
                $buttons.find('.variation-option')
                    .removeClass('border-celya-orange_dark bg-celya-orange_light selected');
                
                // Activer le bouton correspondant à la valeur sélectionnée
                var $activeButton = $buttons.find('.variation-option[data-value="' + selectedValue + '"]');
                $activeButton.addClass('border-celya-orange_dark bg-celya-orange_light selected');
            }
        });
    }
	
	// ========== GESTION DES VARIATIONS WOOCOMMERCE ==========
	
	var $form = $('.variations_form');
	
	// Quand une variation est trouvée
	$form.on('found_variation', function(event, variation) {
		// Synchroniser les boutons avec la variation trouvée
		syncButtonsWithSelect();
	});
	
	// Quand on réinitialise
	$form.on('reset_data', function() {
        // Réinitialiser les boutons visuellement
        $('.variation-option')
            .removeClass('border-celya-orange_dark bg-celya-orange_light selected');
    });
	
	// Quand le select change (même programmatiquement)
	$('select[name^="attribute_"]').on('change', function() {
		syncButtonsWithSelect();
	});
	
	// ========== INITIALISATION AU CHARGEMENT ==========
	
	// Attendre que WooCommerce ait initialisé le formulaire
	setTimeout(function() {
		// Vérifier s'il y a déjà des valeurs pré-sélectionnées (par WooCommerce ou par défaut)
		var hasPreselection = false;
		
		$form.find('select[name^="attribute_"]').each(function() {
			var $select = $(this);
			var currentValue = $select.val();
			
			if (currentValue && currentValue !== '') {
				hasPreselection = true;
			}
		});
		
		if (hasPreselection) {
			// Si des valeurs sont déjà sélectionnées, synchroniser les boutons
			syncButtonsWithSelect();
			$form.trigger('check_variations');
		} else {
			// Sinon, sélectionner automatiquement la première variation
			$form.find('select[name^="attribute_"]').each(function() {
				var $select = $(this);
				
				// Si aucune valeur n'est sélectionnée
				if ($select.val() === '' || $select.val() === null) {
					// Sélectionner la première option disponible
					var $firstOption = $select.find('option').not('[value=""]').first();
					
					if ($firstOption.length) {
						var firstValue = $firstOption.val();
						
						// Mettre à jour le select
						$select.val(firstValue).trigger('change');
						
						// Activer visuellement le bouton correspondant
						var $button = $('.variation-option[data-value="' + firstValue + '"]');
						$button.removeClass('border-celya-light')
							   .addClass('border-celya-orange_dark bg-celya-orange_light');
					}
				}
			});
			
			// Déclencher la recherche de variation
			$form.trigger('check_variations');
		}
	}, 300);
	
});
</script>