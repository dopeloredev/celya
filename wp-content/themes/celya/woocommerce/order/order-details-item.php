<?php
/**
 * Order Item Details
 *
 * Override Celya — ligne d'article sous forme de <li> flex avec vignette produit.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package Celya
 * @version 5.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! apply_filters( 'woocommerce_order_item_visible', true, $item ) ) {
	return;
}

$is_visible        = $product && $product->is_visible();
$product_permalink = apply_filters( 'woocommerce_order_item_permalink', $is_visible ? $product->get_permalink( $item ) : '', $item, $order );
$thumbnail         = $product ? $product->get_image( 'woocommerce_gallery_thumbnail', array( 'class' => 'w-full h-full object-cover' ) ) : '';

$qty          = $item->get_quantity();
$refunded_qty = $order->get_qty_refunded_for_item( $item_id );

if ( $refunded_qty ) {
	$qty_display = '<del class="text-gray-400">' . esc_html( $qty ) . '</del> <ins class="no-underline">' . esc_html( $qty - ( $refunded_qty * -1 ) ) . '</ins>';
} else {
	$qty_display = esc_html( $qty );
}

$item_name = apply_filters( 'woocommerce_order_item_name', $item->get_name(), $item, $is_visible );
?>
<li class="celya-order-item <?php echo esc_attr( apply_filters( 'woocommerce_order_item_class', '', $item, $order ) ); ?> flex items-start gap-4 px-5 sm:px-6 py-4">

	<?php if ( $thumbnail ) : ?>
		<div class="w-14 h-14 sm:w-16 sm:h-16 rounded-xl overflow-hidden bg-celya-light flex-shrink-0 border border-celya-light">
			<?php echo wp_kses_post( $thumbnail ); ?>
		</div>
	<?php endif; ?>

	<div class="flex-1 min-w-0">
		<p class="font-semibold text-celya-dark text-sm leading-snug m-0">
			<?php if ( $product_permalink ) : ?>
				<a href="<?php echo esc_url( $product_permalink ); ?>" class="hover:text-celya-orange_dark transition-colors no-underline">
					<?php echo wp_kses_post( $item_name ); ?>
				</a>
			<?php else : ?>
				<?php echo wp_kses_post( $item_name ); ?>
			<?php endif; ?>
		</p>

		<p class="text-xs text-gray-400 mt-0.5 m-0">
			<?php esc_html_e( 'Quantité', 'celya' ); ?> : <span class="font-semibold text-gray-500"><?php echo wp_kses_post( $qty_display ); ?></span>
		</p>

		<div class="celya-item-meta text-xs text-gray-500 mt-1">
			<?php
			do_action( 'woocommerce_order_item_meta_start', $item_id, $item, $order, false );

			wc_display_item_meta( $item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

			do_action( 'woocommerce_order_item_meta_end', $item_id, $item, $order, false );
			?>
		</div>

		<?php if ( $show_purchase_note && $purchase_note ) : ?>
			<div class="mt-2 text-xs text-celya-dark bg-celya-blue_light rounded-lg px-3 py-2 leading-relaxed">
				<?php echo wpautop( do_shortcode( wp_kses_post( $purchase_note ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>
		<?php endif; ?>
	</div>

	<div class="text-sm font-semibold text-celya-primary whitespace-nowrap text-right pt-0.5">
		<?php echo $order->get_formatted_line_subtotal( $item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</div>

</li>
