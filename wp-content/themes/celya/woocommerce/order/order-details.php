<?php
/**
 * Order details
 *
 * Override Celya — design cohérent avec l'espace client.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package Celya
 * @version 10.1.0
 *
 * @var bool $show_downloads Controls whether the downloads table should be rendered.
 */

// phpcs:disable WooCommerce.Commenting.CommentHooks.MissingHookComment

defined( 'ABSPATH' ) || exit;

$order = wc_get_order( $order_id ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited

if ( ! $order ) {
	return;
}

$order_items        = $order->get_items( apply_filters( 'woocommerce_purchase_order_item_types', 'line_item' ) );
$show_purchase_note = $order->has_status( apply_filters( 'woocommerce_purchase_note_order_statuses', array( 'completed', 'processing' ) ) );
$downloads          = $order->get_downloadable_items();
$actions            = array_filter(
	wc_get_account_orders_actions( $order ),
	function ( $key ) {
		return 'view' !== $key;
	},
	ARRAY_FILTER_USE_KEY
);

// We make sure the order belongs to the user. This will also be true if the user is a guest, and the order belongs to a guest (userID === 0).
$show_customer_details = $order->get_user_id() === get_current_user_id();

if ( $show_downloads ) {
	wc_get_template(
		'order/order-downloads.php',
		array(
			'downloads'  => $downloads,
			'show_title' => true,
		)
	);
}

$order_totals = $order->get_order_item_totals();
?>
<section class="woocommerce-order-details celya-order-details mb-6">
	<?php do_action( 'woocommerce_order_details_before_order_table', $order ); ?>

	<div class="bg-white border border-celya-light rounded-2xl shadow-celya-sm overflow-hidden">

		<!-- En-tête de la carte -->
		<div class="flex items-center gap-2.5 px-5 sm:px-6 py-4 border-b border-celya-light bg-celya-light/40">
			<span class="w-9 h-9 rounded-full bg-celya-orange_light flex items-center justify-center flex-shrink-0">
				<svg class="w-5 h-5 text-celya-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
				</svg>
			</span>
			<h2 class="woocommerce-order-details__title font-serif text-lg font-bold text-celya-primary m-0">
				<?php esc_html_e( 'Détail de la commande', 'celya' ); ?>
			</h2>
		</div>

		<!-- Lignes d'articles -->
		<ul class="celya-order-items divide-y divide-celya-light list-none m-0 p-0">
			<?php
			do_action( 'woocommerce_order_details_before_order_table_items', $order );

			foreach ( $order_items as $item_id => $item ) {
				$product = $item->get_product();

				wc_get_template(
					'order/order-details-item.php',
					array(
						'order'              => $order,
						'item_id'            => $item_id,
						'item'               => $item,
						'show_purchase_note' => $show_purchase_note,
						'purchase_note'      => $product ? $product->get_purchase_note() : '',
						'product'            => $product,
					)
				);
			}

			do_action( 'woocommerce_order_details_after_order_table_items', $order );
			?>
		</ul>

		<!-- Totaux -->
		<?php if ( ! empty( $order_totals ) ) : ?>
		<div class="celya-order-totals px-5 sm:px-6 py-4 border-t border-celya-light bg-celya-light/30 space-y-2.5">
			<?php foreach ( $order_totals as $key => $total ) :
				$is_total = 'order_total' === $key;
			?>
				<div class="flex items-baseline justify-between gap-4 <?php echo $is_total ? 'pt-2.5 mt-0.5 border-t border-celya-light/80' : ''; ?>">
					<span class="<?php echo $is_total ? 'font-serif text-base font-bold text-celya-primary' : 'text-sm text-gray-500'; ?>">
						<?php echo esc_html( $total['label'] ); ?>
					</span>
					<span class="<?php echo $is_total ? 'font-serif text-lg font-bold text-celya-primary' : 'text-sm font-semibold text-celya-dark'; ?> whitespace-nowrap">
						<?php echo wp_kses_post( $total['value'] ); ?>
					</span>
				</div>
			<?php endforeach; ?>

			<?php if ( $order->get_customer_note() ) : ?>
				<div class="pt-3 mt-1 border-t border-celya-light/80">
					<p class="text-xs font-semibold uppercase tracking-wider text-gray-400 mb-1"><?php esc_html_e( 'Note', 'celya' ); ?></p>
					<p class="text-sm text-celya-dark leading-relaxed m-0">
						<?php
						$customer_note = wc_wptexturize_order_note( $order->get_customer_note() );
						echo wp_kses( nl2br( $customer_note ), array( 'br' => array() ) );
						?>
					</p>
				</div>
			<?php endif; ?>
		</div>
		<?php endif; ?>

		<!-- Actions -->
		<?php if ( ! empty( $actions ) ) : ?>
		<div class="celya-order-actions flex flex-wrap gap-2.5 px-5 sm:px-6 py-4 border-t border-celya-light">
			<?php
			foreach ( $actions as $key => $action ) { // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
				if ( empty( $action['aria-label'] ) ) {
					/* translators: %1$s Action name, %2$s Order number. */
					$action_aria_label = sprintf( __( '%1$s order number %2$s', 'woocommerce' ), $action['name'], $order->get_order_number() );
				} else {
					$action_aria_label = $action['aria-label'];
				}
				echo '<a href="' . esc_url( $action['url'] ) . '" class="inline-flex items-center gap-1.5 text-xs font-semibold px-4 py-2 rounded-full border border-celya-primary text-celya-primary hover:bg-celya-primary hover:text-white transition-all duration-200 ' . sanitize_html_class( $key ) . '" aria-label="' . esc_attr( $action_aria_label ) . '">' . esc_html( $action['name'] ) . '</a>';
				unset( $action_aria_label );
			}
			?>
		</div>
		<?php endif; ?>

	</div>

	<?php do_action( 'woocommerce_order_details_after_order_table', $order ); ?>
</section>

<?php
/**
 * Action hook fired after the order details.
 *
 * @since 4.4.0
 * @param WC_Order $order Order data.
 */
do_action( 'woocommerce_after_order_details', $order );

if ( $show_customer_details ) {
	wc_get_template( 'order/order-details-customer.php', array( 'order' => $order ) );
}
