<?php
/**
 * Onglet Ingrédients & Allergènes
 * Variable disponible : $specs (array)
 *
 * @package Celya
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$ingredients = array_filter( $specs['ingredients'] ?? array(), fn($r) => ! empty( $r['ingredient'] ) );
$allergenes  = $specs['allergenes'] ?? '';
?>

<div class="celya-tab celya-tab--ingredients">

    <?php if ( ! empty( $ingredients ) ) : ?>
    <div class="celya-spec-block mb-6 p-5 bg-celya-light rounded-celya-s">
        <h3 class="text-celya-primary font-serif font-bold text-lg mb-3">
            🌾 <?php esc_html_e( 'Ingrédients', 'celya' ); ?>
        </h3>
        <table class="w-full text-sm text-gray-700">
            <tbody>
                <?php foreach ( $ingredients as $row ) : ?>
                <tr class="border-b border-gray-100 last:border-0">
                    <td class="py-2 pr-6 font-medium"><?php echo esc_html( $row['ingredient'] ); ?></td>
                    <td class="py-2 text-gray-500 text-right"><?php echo esc_html( $row['quantite'] ?? '' ); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>

    <?php if ( ! empty( $allergenes ) ) : ?>
    <div class="celya-spec-block p-5 bg-celya-orange_light rounded-celya-s">
        <h3 class="text-celya-primary font-serif font-bold text-lg mb-2">
            ⚠️ <?php esc_html_e( 'Allergènes', 'celya' ); ?>
        </h3>
        <p class="text-sm text-gray-700"><?php echo nl2br( esc_html( $allergenes ) ); ?></p>
    </div>
    <?php endif; ?>

</div>