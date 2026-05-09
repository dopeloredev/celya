<?php
/**
 * My Account page
 *
 * @package Celya
 * @version 3.5.0
 */

defined( 'ABSPATH' ) || exit;
?>

<div class="celya-myaccount section-container">
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8 items-start">

        <!-- Sidebar navigation -->
        <aside class="lg:col-span-1">
            <?php do_action( 'woocommerce_account_navigation' ); ?>
        </aside>

        <!-- Contenu principal -->
        <main class="lg:col-span-3">
            <div class="bg-white rounded-2xl shadow-celya-sm p-6 md:p-8 min-h-96">
                <?php do_action( 'woocommerce_account_content' ); ?>
            </div>
        </main>

    </div>
</div>
