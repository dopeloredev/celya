<?php
/**
 * My Account Dashboard
 *
 * @package Celya
 * @version 4.4.0
 */

defined( 'ABSPATH' ) || exit;

$current_user = wp_get_current_user();

// Récupérer les stats rapides
$customer_orders = wc_get_orders( array(
    'customer' => get_current_user_id(),
    'limit'    => -1,
    'return'   => 'ids',
) );
$total_orders    = count( $customer_orders );
$last_order      = ! empty( $customer_orders ) ? wc_get_order( $customer_orders[0] ) : null;

$allowed_html = array( 'a' => array( 'href' => array() ) );
?>

<!-- Bannière de bienvenue -->
<div class="bg-gradient-to-r from-celya-primary to-celya-dark rounded-2xl p-6 mb-8 text-white relative overflow-hidden">
    <div class="relative z-10">
        <p class="text-celya-orange_light text-xs font-semibold uppercase tracking-widest mb-1">Mon espace</p>
        <h1 class="font-serif text-2xl md:text-3xl font-bold text-white mb-2">
            Bonjour, <?php echo esc_html( $current_user->first_name ?: $current_user->display_name ); ?> !
        </h1>
    </div>
    <!-- Décoration -->
    <div class="absolute -right-8 -top-8 w-40 h-40 rounded-full bg-white/5"></div>
    <div class="absolute -right-4 -bottom-10 w-28 h-28 rounded-full bg-celya-orange_dark/20"></div>
</div>

<!-- Cartes de stats rapides -->
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">

    <!-- Total commandes -->
    <div class="bg-celya-orange_light rounded-xl p-5 flex items-center gap-4">
        <div class="w-11 h-11 rounded-full bg-celya-orange_dark/30 flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5 text-celya-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
            </svg>
        </div>
        <div>
            <p class="text-2xl font-extrabold text-celya-primary"><?php echo esc_html( $total_orders ); ?></p>
            <p class="text-xs text-celya-dark/70 font-medium">Commande<?php echo $total_orders > 1 ? 's' : ''; ?></p>
        </div>
    </div>

    <!-- Dernière commande -->
    <div class="bg-celya-blue_light rounded-xl p-5 flex items-center gap-4 sm:col-span-2">
        <div class="w-11 h-11 rounded-full bg-celya-blue_dark/40 flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5 text-celya-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
        </div>
        <?php if ( $last_order ) : ?>
        <div>
            <p class="text-xs text-celya-dark/70 font-medium mb-0.5">Dernière commande</p>
            <p class="font-bold text-celya-primary text-sm">
                #<?php echo esc_html( $last_order->get_order_number() ); ?>
                &nbsp;·&nbsp;
                <span class="celya-order-status celya-status-<?php echo esc_attr( $last_order->get_status() ); ?>">
                    <?php echo esc_html( wc_get_order_status_name( $last_order->get_status() ) ); ?>
                </span>
            </p>
            <p class="text-xs text-gray-500 mt-0.5"><?php echo esc_html( wc_format_datetime( $last_order->get_date_created() ) ); ?></p>
        </div>
        <?php else : ?>
        <div>
            <p class="text-sm text-celya-dark/70">Aucune commande pour le moment.</p>
        </div>
        <?php endif; ?>
    </div>

</div>

<!-- Accès rapides -->
<h2 class="font-serif text-lg font-semibold text-celya-primary mb-4">Accès rapide</h2>
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

    <a href="<?php echo esc_url( wc_get_endpoint_url( 'orders' ) ); ?>"
       class="group flex items-center gap-4 p-5 bg-white border border-celya-light rounded-xl hover:border-celya-orange_dark hover:shadow-celya-sm transition-all duration-200">
        <div class="w-10 h-10 rounded-lg bg-celya-orange_light flex items-center justify-center flex-shrink-0 group-hover:bg-celya-orange_dark/20 transition-colors">
            <svg class="w-5 h-5 text-celya-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
            </svg>
        </div>
        <div>
            <p class="font-semibold text-celya-dark text-sm">Mes commandes</p>
            <p class="text-xs text-gray-400 mt-0.5">Suivre & consulter</p>
        </div>
        <svg class="w-4 h-4 text-gray-300 ml-auto group-hover:text-celya-orange_dark transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
    </a>

    <a href="<?php echo esc_url( wc_get_endpoint_url( 'edit-address' ) ); ?>"
       class="group flex items-center gap-4 p-5 bg-white border border-celya-light rounded-xl hover:border-celya-orange_dark hover:shadow-celya-sm transition-all duration-200">
        <div class="w-10 h-10 rounded-lg bg-celya-orange_light flex items-center justify-center flex-shrink-0 group-hover:bg-celya-orange_dark/20 transition-colors">
            <svg class="w-5 h-5 text-celya-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
        </div>
        <div>
            <p class="font-semibold text-celya-dark text-sm">Mes adresses</p>
            <p class="text-xs text-gray-400 mt-0.5">Livraison & facturation</p>
        </div>
        <svg class="w-4 h-4 text-gray-300 ml-auto group-hover:text-celya-orange_dark transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
    </a>

    <a href="<?php echo esc_url( wc_get_endpoint_url( 'edit-account' ) ); ?>"
       class="group flex items-center gap-4 p-5 bg-white border border-celya-light rounded-xl hover:border-celya-orange_dark hover:shadow-celya-sm transition-all duration-200">
        <div class="w-10 h-10 rounded-lg bg-celya-orange_light flex items-center justify-center flex-shrink-0 group-hover:bg-celya-orange_dark/20 transition-colors">
            <svg class="w-5 h-5 text-celya-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
        </div>
        <div>
            <p class="font-semibold text-celya-dark text-sm">Mon compte</p>
            <p class="text-xs text-gray-400 mt-0.5">Infos & mot de passe</p>
        </div>
        <svg class="w-4 h-4 text-gray-300 ml-auto group-hover:text-celya-orange_dark transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
    </a>

    <a href="<?php echo esc_url( get_permalink( wc_get_page_id( 'shop' ) ) ); ?>"
       class="group flex items-center gap-4 p-5 bg-white border border-celya-light rounded-xl hover:border-celya-orange_dark hover:shadow-celya-sm transition-all duration-200">
        <div class="w-10 h-10 rounded-lg bg-celya-orange_light flex items-center justify-center flex-shrink-0 group-hover:bg-celya-orange_dark/20 transition-colors">
            <svg class="w-5 h-5 text-celya-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
        </div>
        <div>
            <p class="font-semibold text-celya-dark text-sm">La boutique</p>
            <p class="text-xs text-gray-400 mt-0.5">Découvrir nos produits</p>
        </div>
        <svg class="w-4 h-4 text-gray-300 ml-auto group-hover:text-celya-orange_dark transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
    </a>

</div>

<?php
do_action( 'woocommerce_account_dashboard' );
do_action( 'woocommerce_before_my_account' ); // deprecated
do_action( 'woocommerce_after_my_account' );  // deprecated
?>
