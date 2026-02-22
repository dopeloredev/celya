<?php
/**
 * Onglet Valeurs nutritionnelles
 * Variable disponible : $specs (array)
 *
 * @package Celya
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$nutrition = array_filter( $specs['nutrition'] ?? array(), fn($r) => ! empty( $r['nutriment'] ) );
?>

<div class="celya-tab celya-tab--nutrition">

    <?php if ( ! empty( $nutrition ) ) : ?>
    <div class="celya-spec-block p-5 bg-celya-light rounded-celya-s">
        <h3 class="text-celya-primary font-serif font-bold text-lg mb-3">
            📊 <?php esc_html_e( 'Valeurs nutritionnelles', 'celya' ); ?>
            <small class="font-normal text-sm text-gray-500 font-sans">
                <?php esc_html_e( 'pour 100g', 'celya' ); ?>
            </small>
        </h3>
        <table class="w-full text-sm text-gray-700">
            <thead>
                <tr class="border-b-2 border-gray-200">
                    <th class="py-2 text-left font-semibold"><?php esc_html_e( 'Nutriment', 'celya' ); ?></th>
                    <th class="py-2 text-right font-semibold"><?php esc_html_e( 'Quantité', 'celya' ); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ( $nutrition as $row ) : ?>
                <tr class="border-b border-gray-100 last:border-0">
                    <td class="py-2"><?php echo esc_html( $row['nutriment'] ); ?></td>
                    <td class="py-2 text-right font-medium">
                        <?php echo esc_html( $row['valeur'] . ' ' . $row['unite'] ); ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>

</div>