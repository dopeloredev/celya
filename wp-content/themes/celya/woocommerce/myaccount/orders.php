<?php
/**
 * Orders - My Account
 *
 * @package Celya
 * @version 9.5.0
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_account_orders', $has_orders );
?>

<div class="celya-account-section-header flex items-center justify-between mb-6">
    <div>
        <h2 class="font-serif text-2xl font-bold text-celya-primary mb-1">Mes commandes</h2>
        <p class="text-sm text-gray-500">Retrouvez l'historique de toutes vos commandes</p>
    </div>
    <a href="<?php echo esc_url( get_permalink( wc_get_page_id( 'shop' ) ) ); ?>"
       class="hidden sm:flex items-center gap-2 text-sm font-semibold text-celya-primary hover:text-celya-orange_dark transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
        </svg>
        Boutique
    </a>
</div>

<?php if ( $has_orders ) : ?>

    <!-- Table desktop -->
    <div class="hidden md:block overflow-hidden rounded-xl border border-celya-light">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-celya-light">
                    <?php foreach ( wc_get_account_orders_columns() as $column_id => $column_name ) : ?>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-celya-dark uppercase tracking-wider">
                            <?php echo esc_html( $column_name ); ?>
                        </th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody class="divide-y divide-celya-light bg-white">
                <?php foreach ( $customer_orders->orders as $customer_order ) :
                    $order      = wc_get_order( $customer_order );
                    $item_count = $order->get_item_count() - $order->get_item_count_refunded();
                ?>
                <tr class="hover:bg-celya-light/50 transition-colors woocommerce-orders-table__row--status-<?php echo esc_attr( $order->get_status() ); ?>">
                    <?php foreach ( wc_get_account_orders_columns() as $column_id => $column_name ) :
                        $is_order_number = 'order-number' === $column_id;
                    ?>
                        <?php if ( $is_order_number ) : ?>
                            <th class="px-4 py-4 font-semibold text-celya-primary whitespace-nowrap" data-title="<?php echo esc_attr( $column_name ); ?>" scope="row">
                        <?php else : ?>
                            <td class="px-4 py-4 text-celya-dark" data-title="<?php echo esc_attr( $column_name ); ?>">
                        <?php endif; ?>

                            <?php if ( has_action( 'woocommerce_my_account_my_orders_column_' . $column_id ) ) : ?>
                                <?php do_action( 'woocommerce_my_account_my_orders_column_' . $column_id, $order ); ?>

                            <?php elseif ( $is_order_number ) : ?>
                                <a href="<?php echo esc_url( $order->get_view_order_url() ); ?>"
                                   aria-label="<?php echo esc_attr( sprintf( __( 'View order number %s', 'woocommerce' ), $order->get_order_number() ) ); ?>"
                                   class="hover:text-celya-orange_dark transition-colors">
                                    #<?php echo esc_html( $order->get_order_number() ); ?>
                                </a>

                            <?php elseif ( 'order-date' === $column_id ) : ?>
                                <time datetime="<?php echo esc_attr( $order->get_date_created()->date( 'c' ) ); ?>" class="text-gray-500">
                                    <?php echo esc_html( wc_format_datetime( $order->get_date_created() ) ); ?>
                                </time>

                            <?php elseif ( 'order-status' === $column_id ) : ?>
                                <span class="celya-order-status celya-status-<?php echo esc_attr( $order->get_status() ); ?>">
                                    <?php echo esc_html( wc_get_order_status_name( $order->get_status() ) ); ?>
                                </span>

                            <?php elseif ( 'order-total' === $column_id ) : ?>
                                <span class="font-semibold text-celya-primary">
                                    <?php echo wp_kses_post( sprintf( _n( '%1$s · %2$s article', '%1$s · %2$s articles', $item_count, 'woocommerce' ), $order->get_formatted_order_total(), $item_count ) ); ?>
                                </span>

                            <?php elseif ( 'order-actions' === $column_id ) : ?>
                                <?php
                                $actions = wc_get_account_orders_actions( $order );
                                if ( ! empty( $actions ) ) :
                                    foreach ( $actions as $key => $action ) :
                                        $aria = $action['aria-label'] ?? sprintf( __( '%1$s order number %2$s', 'woocommerce' ), $action['name'], $order->get_order_number() );
                                ?>
                                    <a href="<?php echo esc_url( $action['url'] ); ?>"
                                       class="inline-flex items-center gap-1 text-xs font-semibold px-3 py-1.5 rounded-full border border-celya-primary text-celya-primary hover:bg-celya-primary hover:text-white transition-all duration-200 mr-1"
                                       aria-label="<?php echo esc_attr( $aria ); ?>">
                                        <?php echo esc_html( $action['name'] ); ?>
                                    </a>
                                <?php
                                    endforeach;
                                endif;
                                ?>
                            <?php endif; ?>

                        <?php if ( $is_order_number ) : ?></th>
                        <?php else : ?></td>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Cartes mobile -->
    <div class="md:hidden space-y-4">
        <?php foreach ( $customer_orders->orders as $customer_order ) :
            $order      = wc_get_order( $customer_order );
            $item_count = $order->get_item_count() - $order->get_item_count_refunded();
            $actions    = wc_get_account_orders_actions( $order );
        ?>
        <div class="bg-white border border-celya-light rounded-xl p-4 shadow-celya-sm">
            <div class="flex items-center justify-between mb-3">
                <a href="<?php echo esc_url( $order->get_view_order_url() ); ?>"
                   class="font-serif font-bold text-celya-primary text-base hover:text-celya-orange_dark transition-colors">
                    Commande #<?php echo esc_html( $order->get_order_number() ); ?>
                </a>
                <span class="celya-order-status celya-status-<?php echo esc_attr( $order->get_status() ); ?>">
                    <?php echo esc_html( wc_get_order_status_name( $order->get_status() ) ); ?>
                </span>
            </div>
            <div class="flex items-center justify-between text-sm text-gray-500 mb-4">
                <time datetime="<?php echo esc_attr( $order->get_date_created()->date( 'c' ) ); ?>">
                    <?php echo esc_html( wc_format_datetime( $order->get_date_created() ) ); ?>
                </time>
                <span class="font-semibold text-celya-primary">
                    <?php echo wp_kses_post( sprintf( _n( '%1$s · %2$s article', '%1$s · %2$s articles', $item_count, 'woocommerce' ), $order->get_formatted_order_total(), $item_count ) ); ?>
                </span>
            </div>
            <?php if ( ! empty( $actions ) ) : ?>
            <div class="flex gap-2 flex-wrap">
                <?php foreach ( $actions as $key => $action ) :
                    $aria = $action['aria-label'] ?? sprintf( __( '%1$s order number %2$s', 'woocommerce' ), $action['name'], $order->get_order_number() );
                ?>
                    <a href="<?php echo esc_url( $action['url'] ); ?>"
                       class="flex-1 text-center text-xs font-semibold px-3 py-2 rounded-full border border-celya-primary text-celya-primary hover:bg-celya-primary hover:text-white transition-all duration-200"
                       aria-label="<?php echo esc_attr( $aria ); ?>">
                        <?php echo esc_html( $action['name'] ); ?>
                    </a>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>

    <?php do_action( 'woocommerce_before_account_orders_pagination' ); ?>

    <?php if ( 1 < $customer_orders->max_num_pages ) : ?>
    <div class="flex justify-center gap-3 mt-6">
        <?php if ( 1 !== $current_page ) : ?>
            <a class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full border border-celya-primary text-celya-primary text-sm font-semibold hover:bg-celya-primary hover:text-white transition-all duration-200"
               href="<?php echo esc_url( wc_get_endpoint_url( 'orders', $current_page - 1 ) ); ?>">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                <?php esc_html_e( 'Précédent', 'woocommerce' ); ?>
            </a>
        <?php endif; ?>
        <?php if ( intval( $customer_orders->max_num_pages ) !== $current_page ) : ?>
            <a class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full border border-celya-primary text-celya-primary text-sm font-semibold hover:bg-celya-primary hover:text-white transition-all duration-200"
               href="<?php echo esc_url( wc_get_endpoint_url( 'orders', $current_page + 1 ) ); ?>">
                <?php esc_html_e( 'Suivant', 'woocommerce' ); ?>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        <?php endif; ?>
    </div>
    <?php endif; ?>

<?php else : ?>

    <div class="flex flex-col items-center justify-center py-16 text-center">
        <div class="w-20 h-20 rounded-full bg-celya-orange_light flex items-center justify-center mb-5">
            <svg class="w-9 h-9 text-celya-orange_dark" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
            </svg>
        </div>
        <h3 class="font-serif text-xl font-semibold text-celya-primary mb-2">Aucune commande</h3>
        <p class="text-gray-500 text-sm mb-6">Vous n'avez pas encore passé de commande.</p>
        <a href="<?php echo esc_url( apply_filters( 'woocommerce_return_to_shop_redirect', wc_get_page_permalink( 'shop' ) ) ); ?>"
           class="btn-celya">
            Découvrir la boutique
        </a>
    </div>

<?php endif; ?>

<?php do_action( 'woocommerce_after_account_orders', $has_orders ); ?>
