<?php
/**
 * My Account navigation
 *
 * @package Celya
 * @version 9.3.0
 */

defined( 'ABSPATH' ) || exit;

$current_user = wp_get_current_user();
$initials     = strtoupper( substr( $current_user->first_name ?: $current_user->display_name, 0, 1 ) . substr( $current_user->last_name, 0, 1 ) );

// Icônes SVG par endpoint
$nav_icons = array(
    'dashboard'       => '<svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>',
    'orders'          => '<svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>',
    'downloads'       => '<svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>',
    'edit-address'    => '<svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>',
    'edit-account'    => '<svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>',
    'customer-logout' => '<svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>',
);

do_action( 'woocommerce_before_account_navigation' );
?>

<nav class="celya-account-nav bg-white rounded-2xl shadow-celya-sm overflow-hidden" aria-label="<?php esc_html_e( 'Account pages', 'woocommerce' ); ?>">

    <!-- Bloc utilisateur -->
    <div class="p-6 border-b border-celya-light bg-gradient-to-br from-celya-orange_light to-white">
        <div class="flex flex-col items-center text-center gap-3">
            <div class="w-16 h-16 rounded-full bg-celya-primary flex items-center justify-center shadow-celya-sm flex-shrink-0">
                <span class="text-white font-serif font-bold text-xl"><?php echo esc_html( $initials ); ?></span>
            </div>
            <div>
                <p class="font-serif font-semibold text-celya-primary text-base leading-tight">
                    <?php echo esc_html( $current_user->display_name ); ?>
                </p>
                <p class="text-xs text-gray-500 mt-0.5 break-all"><?php echo esc_html( $current_user->user_email ); ?></p>
            </div>
        </div>
    </div>

    <!-- Items de navigation -->
    <ul class="p-3 space-y-1">
        <?php foreach ( wc_get_account_menu_items() as $endpoint => $label ) :
            $classes    = wc_get_account_menu_item_classes( $endpoint );
            $is_active  = strpos( $classes, 'is-active' ) !== false;
            $is_logout  = 'customer-logout' === $endpoint;
            $icon       = $nav_icons[ $endpoint ] ?? $nav_icons['dashboard'];
        ?>
            <li class="<?php echo esc_attr( $classes ); ?>">
                <a href="<?php echo esc_url( wc_get_account_endpoint_url( $endpoint ) ); ?>"
                   <?php echo wc_is_current_account_menu_item( $endpoint ) ? 'aria-current="page"' : ''; ?>
                   class="celya-nav-item flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200
                          <?php if ( $is_logout ) : ?>
                              text-gray-400 hover:bg-red-50 hover:text-red-500
                          <?php elseif ( $is_active ) : ?>
                              bg-celya-orange_light text-celya-primary border-l-2 border-celya-orange_dark
                          <?php else : ?>
                              text-celya-dark hover:bg-celya-orange_light hover:text-celya-primary
                          <?php endif; ?>">
                    <?php echo $icon; // phpcs:ignore WordPress.Security.EscapeOutput ?>
                    <span><?php echo esc_html( $label ); ?></span>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>

</nav>

<?php do_action( 'woocommerce_after_account_navigation' ); ?>
