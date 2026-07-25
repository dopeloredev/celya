<?php
/**
 * Order Customer Details
 *
 * Override Celya — adresses sous forme de cartes cohérentes avec l'espace client.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package Celya
 * @version 8.7.0
 */

defined( 'ABSPATH' ) || exit;

$show_shipping = ! wc_ship_to_billing_address_only() && $order->needs_shipping_address();
?>
<section class="woocommerce-customer-details celya-customer-details">

	<div class="grid grid-cols-1 <?php echo $show_shipping ? 'lg:grid-cols-2' : ''; ?> gap-5">

		<!-- Adresse de facturation -->
		<div class="woocommerce-column--billing-address bg-white border border-celya-light rounded-2xl shadow-celya-sm p-5 sm:p-6">
			<h2 class="woocommerce-column__title font-serif text-lg font-bold text-celya-primary flex items-center gap-2.5 m-0 mb-4">
				<span class="w-9 h-9 rounded-full bg-celya-orange_light flex items-center justify-center flex-shrink-0">
					<svg class="w-5 h-5 text-celya-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
					</svg>
				</span>
				<?php esc_html_e( 'Adresse de facturation', 'celya' ); ?>
			</h2>

			<address class="celya-address not-italic text-sm text-celya-dark leading-relaxed m-0">
				<?php echo wp_kses_post( $order->get_formatted_billing_address( esc_html__( 'N/A', 'woocommerce' ) ) ); ?>

				<?php if ( $order->get_billing_phone() ) : ?>
					<span class="woocommerce-customer-details--phone flex items-center gap-2 mt-3 text-gray-600">
						<svg class="w-4 h-4 text-celya-orange_dark flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
						</svg>
						<?php echo esc_html( $order->get_billing_phone() ); ?>
					</span>
				<?php endif; ?>

				<?php if ( $order->get_billing_email() ) : ?>
					<span class="woocommerce-customer-details--email flex items-center gap-2 mt-2 text-gray-600 break-all">
						<svg class="w-4 h-4 text-celya-orange_dark flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
						</svg>
						<?php echo esc_html( $order->get_billing_email() ); ?>
					</span>
				<?php endif; ?>

				<?php
					/**
					 * Action hook fired after an address in the order customer details.
					 *
					 * @since 8.7.0
					 */
					do_action( 'woocommerce_order_details_after_customer_address', 'billing', $order );
				?>
			</address>
		</div>

		<?php if ( $show_shipping ) : ?>
		<!-- Adresse de livraison -->
		<div class="woocommerce-column--shipping-address bg-white border border-celya-light rounded-2xl shadow-celya-sm p-5 sm:p-6">
			<h2 class="woocommerce-column__title font-serif text-lg font-bold text-celya-primary flex items-center gap-2.5 m-0 mb-4">
				<span class="w-9 h-9 rounded-full bg-celya-blue_light flex items-center justify-center flex-shrink-0">
					<svg class="w-5 h-5 text-celya-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
					</svg>
				</span>
				<?php esc_html_e( 'Adresse de livraison', 'celya' ); ?>
			</h2>

			<address class="celya-address not-italic text-sm text-celya-dark leading-relaxed m-0">
				<?php echo wp_kses_post( $order->get_formatted_shipping_address( esc_html__( 'N/A', 'woocommerce' ) ) ); ?>

				<?php if ( $order->get_shipping_phone() ) : ?>
					<span class="woocommerce-customer-details--phone flex items-center gap-2 mt-3 text-gray-600">
						<svg class="w-4 h-4 text-celya-orange_dark flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
						</svg>
						<?php echo esc_html( $order->get_shipping_phone() ); ?>
					</span>
				<?php endif; ?>

				<?php
					do_action( 'woocommerce_order_details_after_customer_address', 'shipping', $order );
				?>
			</address>
		</div>
		<?php endif; ?>

	</div>

	<?php do_action( 'woocommerce_order_details_after_customer_details', $order ); ?>

</section>
