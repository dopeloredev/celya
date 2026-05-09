<?php
/**
 * Cart Page
 *
 * @package Celya
 * @version 10.0.0
 */

defined( 'ABSPATH' ) || exit;

// Cross-sells déplacés sous le layout principal
remove_action( 'woocommerce_cart_collaterals', 'woocommerce_cross_sell_display' );

do_action( 'woocommerce_before_cart' );
?>

<div class="celya-cart-wrapper section-container">

    <!-- En-tête page panier -->
    <div class="celya-cart-header flex flex-wrap items-center justify-between gap-4 mb-8">
        <div>
            <p class="section-subtitle"><?php esc_html_e( 'Votre sélection', 'woocommerce' ); ?></p>
            <h1 class="font-serif text-3xl md:text-4xl font-bold text-celya-primary leading-tight">
                <?php esc_html_e( 'Mon Panier', 'woocommerce' ); ?>
                <?php
                $cart_count = WC()->cart->get_cart_contents_count();
                if ( $cart_count > 0 ) :
                ?>
                <span class="celya-cart-count text-xl font-sans font-normal text-gray-400 ml-2">
                    (<?php echo $cart_count; ?> <?php echo $cart_count > 1 ? 'articles' : 'article'; ?>)
                </span>
                <?php endif; ?>
            </h1>
        </div>
        <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>"
           class="flex items-center gap-2 text-sm font-medium text-celya-primary hover:text-celya-orange_dark transition-colors group">
            <svg class="w-4 h-4 transition-transform group-hover:-translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            <?php esc_html_e( 'Continuer mes achats', 'woocommerce' ); ?>
        </a>
    </div>

    <!-- Grille 2 colonnes : Articles + Récapitulatif -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

        <!-- ===================== COLONNE GAUCHE : Articles ===================== -->
        <div class="lg:col-span-8">

            <form class="woocommerce-cart-form" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">
                <?php do_action( 'woocommerce_before_cart_table' ); ?>

                <!-- En-têtes colonnes (desktop uniquement) -->
                <div class="celya-cart-cols-header hidden md:grid grid-cols-12 gap-4 px-5 pb-3 mb-1">
                    <div class="col-span-6 text-xs font-semibold uppercase tracking-wider text-gray-400">
                        <?php esc_html_e( 'Produit', 'woocommerce' ); ?>
                    </div>
                    <div class="col-span-2 text-xs font-semibold uppercase tracking-wider text-gray-400 text-center">
                        <?php esc_html_e( 'Prix', 'woocommerce' ); ?>
                    </div>
                    <div class="col-span-2 text-xs font-semibold uppercase tracking-wider text-gray-400 text-center">
                        <?php esc_html_e( 'Qté', 'woocommerce' ); ?>
                    </div>
                    <div class="col-span-2 text-xs font-semibold uppercase tracking-wider text-gray-400 text-right">
                        <?php esc_html_e( 'Total', 'woocommerce' ); ?>
                    </div>
                </div>

                <!-- Table native WooCommerce (invisible, pour compat JS) -->
                <table class="shop_table shop_table_responsive cart woocommerce-cart-form__contents celya-cart-table" cellspacing="0">
                    <thead class="celya-thead-hidden">
                        <tr>
                            <th class="product-remove"><span class="screen-reader-text"><?php esc_html_e( 'Remove item', 'woocommerce' ); ?></span></th>
                            <th class="product-thumbnail"><span class="screen-reader-text"><?php esc_html_e( 'Thumbnail image', 'woocommerce' ); ?></span></th>
                            <th scope="col" class="product-name"><?php esc_html_e( 'Product', 'woocommerce' ); ?></th>
                            <th scope="col" class="product-price"><?php esc_html_e( 'Price', 'woocommerce' ); ?></th>
                            <th scope="col" class="product-quantity"><?php esc_html_e( 'Quantity', 'woocommerce' ); ?></th>
                            <th scope="col" class="product-subtotal"><?php esc_html_e( 'Subtotal', 'woocommerce' ); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php do_action( 'woocommerce_before_cart_contents' ); ?>

                        <?php
                        foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
                            $_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
                            $product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );
                            $product_name = apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key );

                            if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
                                $product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
                        ?>
                        <tr class="woocommerce-cart-form__cart-item <?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>">

                            <!-- Supprimer (caché, remplacé par bouton dans subtotal) -->
                            <td class="product-remove celya-td-remove">
                                <?php
                                echo apply_filters(
                                    'woocommerce_cart_item_remove_link',
                                    sprintf(
                                        '<a role="button" href="%s" class="remove" aria-label="%s" data-product_id="%s" data-product_sku="%s">&times;</a>',
                                        esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
                                        esc_attr( sprintf( __( 'Remove %s from cart', 'woocommerce' ), wp_strip_all_tags( $product_name ) ) ),
                                        esc_attr( $product_id ),
                                        esc_attr( $_product->get_sku() )
                                    ),
                                    $cart_item_key
                                );
                                ?>
                            </td>

                            <!-- Image -->
                            <td class="product-thumbnail celya-td-thumb">
                                <div class="celya-cart-thumb">
                                    <?php
                                    $thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image( 'woocommerce_thumbnail' ), $cart_item, $cart_item_key );
                                    if ( $product_permalink ) {
                                        echo '<a href="' . esc_url( $product_permalink ) . '" class="block w-full h-full">' . $thumbnail . '</a>';
                                    } else {
                                        echo $thumbnail;
                                    }
                                    ?>
                                </div>
                            </td>

                            <!-- Nom + variantes -->
                            <th scope="row" class="product-name celya-td-name" data-title="<?php esc_attr_e( 'Produit', 'woocommerce' ); ?>">
                                <?php
                                if ( ! $product_permalink ) {
                                    echo wp_kses_post( $product_name );
                                } else {
                                    echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', sprintf(
                                        '<a href="%s" class="celya-cart-product-link">%s</a>',
                                        esc_url( $product_permalink ),
                                        $_product->get_name()
                                    ), $cart_item, $cart_item_key ) );
                                }
                                ?>
                                <?php do_action( 'woocommerce_after_cart_item_name', $cart_item, $cart_item_key ); ?>
                                <?php echo wc_get_formatted_cart_item_data( $cart_item ); ?>
                                <?php if ( $_product->backorders_require_notification() && $_product->is_on_backorder( $cart_item['quantity'] ) ) : ?>
                                    <p class="backorder_notification text-xs text-amber-600 mt-1 font-normal">
                                        <?php esc_html_e( 'Available on backorder', 'woocommerce' ); ?>
                                    </p>
                                <?php endif; ?>
                            </th>

                            <!-- Prix unitaire -->
                            <td class="product-price celya-td-price" data-title="<?php esc_attr_e( 'Prix', 'woocommerce' ); ?>">
                                <?php echo apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key ); ?>
                            </td>

                            <!-- Quantité -->
                            <td class="product-quantity celya-td-qty" data-title="<?php esc_attr_e( 'Quantité', 'woocommerce' ); ?>">
                                <?php
                                if ( $_product->is_sold_individually() ) {
                                    $min_quantity = 1;
                                    $max_quantity = 1;
                                } else {
                                    $min_quantity = 0;
                                    $max_quantity = $_product->get_max_purchase_quantity();
                                }
                                $product_quantity = woocommerce_quantity_input(
                                    array(
                                        'input_name'   => "cart[{$cart_item_key}][qty]",
                                        'input_value'  => $cart_item['quantity'],
                                        'max_value'    => $max_quantity,
                                        'min_value'    => $min_quantity,
                                        'product_name' => $product_name,
                                    ),
                                    $_product,
                                    false
                                );
                                echo apply_filters( 'woocommerce_cart_item_quantity', $product_quantity, $cart_item_key, $cart_item );
                                ?>
                            </td>

                            <!-- Sous-total -->
                            <td class="product-subtotal celya-td-subtotal" data-title="<?php esc_attr_e( 'Total', 'woocommerce' ); ?>">
                                <div class="celya-subtotal-cell">
                                    <span class="celya-line-total">
                                        <?php echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key ); ?>
                                    </span>
                                    <!-- Bouton supprimer (visible) -->
                                    <a role="button"
                                       href="<?php echo esc_url( wc_get_cart_remove_url( $cart_item_key ) ); ?>"
                                       class="celya-remove-btn"
                                       aria-label="<?php echo esc_attr( sprintf( __( 'Remove %s from cart', 'woocommerce' ), wp_strip_all_tags( $product_name ) ) ); ?>"
                                       data-product_id="<?php echo esc_attr( $product_id ); ?>"
                                       data-product_sku="<?php echo esc_attr( $_product->get_sku() ); ?>">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </a>
                                </div>
                            </td>

                        </tr>
                        <?php
                            }
                        }
                        ?>

                        <?php do_action( 'woocommerce_cart_contents' ); ?>

                        <!-- Ligne actions : Coupon + Mettre à jour -->
                        <tr>
                            <td colspan="6" class="actions celya-td-actions">
                                <div class="celya-cart-actions-bar">

                                    <?php if ( wc_coupons_enabled() ) : ?>
                                    <div class="coupon celya-coupon-wrap">
                                        <label for="coupon_code" class="sr-only"><?php esc_html_e( 'Coupon:', 'woocommerce' ); ?></label>
                                        <div class="celya-coupon-inner">
                                            <svg class="celya-coupon-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                            </svg>
                                            <input type="text"
                                                   name="coupon_code"
                                                   class="input-text celya-coupon-input"
                                                   id="coupon_code"
                                                   value=""
                                                   placeholder="<?php esc_attr_e( 'Code promo', 'woocommerce' ); ?>" />
                                            <button type="submit"
                                                    class="celya-coupon-btn"
                                                    name="apply_coupon"
                                                    value="<?php esc_attr_e( 'Apply coupon', 'woocommerce' ); ?>">
                                                <?php esc_html_e( 'Appliquer', 'woocommerce' ); ?>
                                            </button>
                                        </div>
                                        <?php do_action( 'woocommerce_cart_coupon' ); ?>
                                    </div>
                                    <?php endif; ?>

                                    <button type="submit"
                                            class="celya-update-btn"
                                            name="update_cart"
                                            value="<?php esc_attr_e( 'Update cart', 'woocommerce' ); ?>">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                        </svg>
                                        <?php esc_html_e( 'Mettre à jour', 'woocommerce' ); ?>
                                    </button>

                                    <?php do_action( 'woocommerce_cart_actions' ); ?>
                                    <?php wp_nonce_field( 'woocommerce-cart', 'woocommerce-cart-nonce' ); ?>

                                </div>
                            </td>
                        </tr>

                        <?php do_action( 'woocommerce_after_cart_contents' ); ?>
                    </tbody>
                </table>

                <?php do_action( 'woocommerce_after_cart_table' ); ?>
            </form>

        </div><!-- /.lg:col-span-8 -->

        <!-- ===================== COLONNE DROITE : Récapitulatif ===================== -->
        <div class="lg:col-span-4 lg:sticky lg:top-28">
            <?php do_action( 'woocommerce_before_cart_collaterals' ); ?>
            <div class="cart-collaterals">
                <?php do_action( 'woocommerce_cart_collaterals' ); ?>
            </div>
        </div>

    </div><!-- /.grid -->

</div><!-- /.celya-cart-wrapper -->

<?php do_action( 'woocommerce_after_cart' ); ?>
