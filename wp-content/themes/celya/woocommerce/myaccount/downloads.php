<?php
/**
 * Downloads - My Account
 *
 * @package Celya
 * @version 7.8.0
 */

defined( 'ABSPATH' ) || exit;

$downloads     = WC()->customer->get_downloadable_products();
$has_downloads = (bool) $downloads;

do_action( 'woocommerce_before_account_downloads', $has_downloads );
?>

<div class="celya-account-section-header mb-6">
    <h2 class="font-serif text-2xl font-bold text-celya-primary mb-1">Mes téléchargements</h2>
    <p class="text-sm text-gray-500">Vos fichiers numériques disponibles au téléchargement</p>
</div>

<?php if ( $has_downloads ) : ?>

    <?php do_action( 'woocommerce_before_available_downloads' ); ?>
    <?php do_action( 'woocommerce_available_downloads', $downloads ); ?>
    <?php do_action( 'woocommerce_after_available_downloads' ); ?>

<?php else : ?>

    <div class="flex flex-col items-center justify-center py-16 text-center">
        <div class="w-20 h-20 rounded-full bg-celya-orange_light flex items-center justify-center mb-5">
            <svg class="w-9 h-9 text-celya-orange_dark" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
            </svg>
        </div>
        <h3 class="font-serif text-xl font-semibold text-celya-primary mb-2">Aucun téléchargement</h3>
        <p class="text-gray-500 text-sm mb-6">Vous n'avez pas encore de fichier disponible.</p>
        <a href="<?php echo esc_url( apply_filters( 'woocommerce_return_to_shop_redirect', wc_get_page_permalink( 'shop' ) ) ); ?>"
           class="btn-celya">
            Découvrir la boutique
        </a>
    </div>

<?php endif; ?>

<?php do_action( 'woocommerce_after_account_downloads', $has_downloads ); ?>
