<?php
/**
 * View Order - My Account
 *
 * @package Celya
 * @version 3.0.0
 */

defined( 'ABSPATH' ) || exit;

$notes = $order->get_customer_order_notes();
?>

<div class="mb-6">
    <a href="<?php echo esc_url( wc_get_endpoint_url( 'orders' ) ); ?>"
       class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-celya-primary transition-colors mb-4">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Retour aux commandes
    </a>

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h2 class="font-serif text-2xl font-bold text-celya-primary mb-1">
                Commande #<?php echo esc_html( $order->get_order_number() ); ?>
            </h2>
            <p class="text-sm text-gray-500">
                Passée le <?php echo esc_html( wc_format_datetime( $order->get_date_created() ) ); ?>
            </p>
        </div>
        <span class="celya-order-status celya-status-<?php echo esc_attr( $order->get_status() ); ?> self-start sm:self-auto text-sm px-4 py-2">
            <?php echo esc_html( wc_get_order_status_name( $order->get_status() ) ); ?>
        </span>
    </div>
</div>

<?php if ( $notes ) : ?>
<div class="bg-celya-blue_light rounded-xl p-5 mb-6">
    <h3 class="font-serif text-base font-semibold text-celya-primary mb-3 flex items-center gap-2">
        <svg class="w-4 h-4 text-celya-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        Mises à jour
    </h3>
    <ol class="space-y-3">
        <?php foreach ( $notes as $note ) : ?>
        <li class="bg-white rounded-lg p-3">
            <p class="text-xs text-gray-400 mb-1">
                <?php echo date_i18n( 'l j F Y à H\hi', strtotime( $note->comment_date ) ); // phpcs:ignore ?>
            </p>
            <div class="text-sm text-celya-dark">
                <?php echo wpautop( wptexturize( $note->comment_content ) ); // phpcs:ignore ?>
            </div>
        </li>
        <?php endforeach; ?>
    </ol>
</div>
<?php endif; ?>

<?php do_action( 'woocommerce_view_order', $order_id ); ?>
