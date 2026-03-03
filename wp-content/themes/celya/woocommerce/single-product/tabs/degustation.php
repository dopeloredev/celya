<?php
/**
 * Onglet Conservation & Conseil dégustation
 * Variable disponible : $specs (array)
 *
 * @package Celya
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$degustation  = $specs['degustation']  ?? '';

$has_content = ! empty( $degustation );
?>

<div class="celya-tab celya-tab--degustation">

    <?php if ( ! $has_content ) : ?>
    <p class="text-sm text-gray-400 italic py-4">
        <?php esc_html_e( 'Informations non disponibles pour le moment.', 'celya' ); ?>
    </p>

    <?php else : ?>
        <?php if ( ! empty( $degustation ) ) : ?>
        <div class="celya-spec-block p-5 bg-celya-green_light rounded-celya-s">
            <h3 class="text-celya-primary font-serif font-bold text-lg mb-2">
                <?php esc_html_e( 'Conseil dégustation', 'celya' ); ?>
            </h3>
            <p class="text-sm text-gray-700"><?php echo nl2br( esc_html( $degustation ) ); ?></p>
        </div>
        <?php endif; ?>

    <?php endif; ?>

</div>