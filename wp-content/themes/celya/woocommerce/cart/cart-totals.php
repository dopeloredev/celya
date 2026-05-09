<?php
/**
 * Cart totals
 *
 * @package Celya
 * @version 2.3.6
 */

defined( 'ABSPATH' ) || exit;
?>

<div class="cart_totals <?php echo ( WC()->customer->has_calculated_shipping() ) ? 'calculated_shipping' : ''; ?>">

    <?php do_action( 'woocommerce_before_cart_totals' ); ?>

    <!-- En-tête -->
    <div class="celya-totals-header">
        <h2><?php esc_html_e( 'Récapitulatif', 'woocommerce' ); ?></h2>
    </div>

    <!-- Corps -->
    <div class="celya-totals-body">
        <table cellspacing="0" class="shop_table shop_table_responsive">

            <tr class="cart-subtotal">
                <th><?php esc_html_e( 'Sous-total', 'woocommerce' ); ?></th>
                <td data-title="<?php esc_attr_e( 'Sous-total', 'woocommerce' ); ?>"><?php wc_cart_totals_subtotal_html(); ?></td>
            </tr>

            <?php foreach ( WC()->cart->get_coupons() as $code => $coupon ) : ?>
                <tr class="cart-discount coupon-<?php echo esc_attr( sanitize_title( $code ) ); ?>">
                    <th>
                        <span class="flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                            </svg>
                            <?php wc_cart_totals_coupon_label( $coupon ); ?>
                        </span>
                    </th>
                    <td data-title="<?php echo esc_attr( wc_cart_totals_coupon_label( $coupon, false ) ); ?>"><?php wc_cart_totals_coupon_html( $coupon ); ?></td>
                </tr>
            <?php endforeach; ?>

            <?php if ( WC()->cart->needs_shipping() && WC()->cart->show_shipping() ) : ?>
                <?php do_action( 'woocommerce_cart_totals_before_shipping' ); ?>
                <?php wc_cart_totals_shipping_html(); ?>
                <?php do_action( 'woocommerce_cart_totals_after_shipping' ); ?>
            <?php elseif ( WC()->cart->needs_shipping() && 'yes' === get_option( 'woocommerce_enable_shipping_calc' ) ) : ?>
                <tr class="shipping">
                    <th><?php esc_html_e( 'Livraison', 'woocommerce' ); ?></th>
                    <td data-title="<?php esc_attr_e( 'Livraison', 'woocommerce' ); ?>"><?php woocommerce_shipping_calculator(); ?></td>
                </tr>
            <?php endif; ?>

            <?php foreach ( WC()->cart->get_fees() as $fee ) : ?>
                <tr class="fee">
                    <th><?php echo esc_html( $fee->name ); ?></th>
                    <td data-title="<?php echo esc_attr( $fee->name ); ?>"><?php wc_cart_totals_fee_html( $fee ); ?></td>
                </tr>
            <?php endforeach; ?>

            <?php
            if ( wc_tax_enabled() && ! WC()->cart->display_prices_including_tax() ) {
                $taxable_address = WC()->customer->get_taxable_address();
                $estimated_text  = '';

                if ( WC()->customer->is_customer_outside_base() && ! WC()->customer->has_calculated_shipping() ) {
                    /* translators: %s location. */
                    $estimated_text = sprintf( ' <small>' . esc_html__( '(estimated for %s)', 'woocommerce' ) . '</small>', WC()->countries->estimated_for_prefix( $taxable_address[0] ) . WC()->countries->countries[ $taxable_address[0] ] );
                }

                if ( 'itemized' === get_option( 'woocommerce_tax_total_display' ) ) {
                    foreach ( WC()->cart->get_tax_totals() as $code => $tax ) {
                        ?>
                        <tr class="tax-rate tax-rate-<?php echo esc_attr( sanitize_title( $code ) ); ?>">
                            <th><?php echo esc_html( $tax->label ) . $estimated_text; // phpcs:ignore ?></th>
                            <td data-title="<?php echo esc_attr( $tax->label ); ?>"><?php echo wp_kses_post( $tax->formatted_amount ); ?></td>
                        </tr>
                        <?php
                    }
                } else {
                    ?>
                    <tr class="tax-total">
                        <th><?php echo esc_html( WC()->countries->tax_or_vat() ) . $estimated_text; // phpcs:ignore ?></th>
                        <td data-title="<?php echo esc_attr( WC()->countries->tax_or_vat() ); ?>"><?php wc_cart_totals_taxes_total_html(); ?></td>
                    </tr>
                    <?php
                }
            }
            ?>

            <?php do_action( 'woocommerce_cart_totals_before_order_total' ); ?>

            <tr class="order-total">
                <th><?php esc_html_e( 'Total', 'woocommerce' ); ?></th>
                <td data-title="<?php esc_attr_e( 'Total', 'woocommerce' ); ?>"><?php wc_cart_totals_order_total_html(); ?></td>
            </tr>

            <?php do_action( 'woocommerce_cart_totals_after_order_total' ); ?>

        </table>
    </div><!-- /.celya-totals-body -->

    <!-- Bouton Commander -->
    <div class="wc-proceed-to-checkout">
        <?php do_action( 'woocommerce_proceed_to_checkout' ); ?>
    </div>

    <!-- Trust badges -->
    <div class="celya-trust-badges">
        <div class="celya-trust-badges-grid">

            <div class="celya-trust-badge">
                <div class="celya-trust-badge-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <span>Paiement sécurisé</span>
            </div>

            <div class="celya-trust-badge">
                <div class="celya-trust-badge-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zm10 0a2 2 0 11-4 0 2 2 0 014 0zM1 1h4l2.68 13.39a2 2 0 001.98 1.61h9.72a2 2 0 001.98-1.61L23 6H6"/>
                    </svg>
                </div>
                <span>Livraison 3–7j</span>
            </div>

            <div class="celya-trust-badge">
                <div class="celya-trust-badge-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <span>Click &amp; Collect</span>
            </div>

        </div>
    </div>

    <?php do_action( 'woocommerce_after_cart_totals' ); ?>

</div>
