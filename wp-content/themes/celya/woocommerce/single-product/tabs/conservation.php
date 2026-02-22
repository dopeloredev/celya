<?php
/**
 * Onglet Conservation & Conseil dégustation
 * Variable disponible : $specs (array)
 *
 * @package Celya
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$conservation = $specs['conservation'] ?? '';
$degustation  = $specs['degustation']  ?? '';
?>

<div class="celya-tab celya-tab--conservation">

    <?php if ( ! empty( $conservation ) ) : ?>
    <div class="celya-spec-block mb-6 p-5 bg-celya-light rounded-celya-s">
        <h3 class="text-celya-primary font-serif font-bold text-lg mb-2">
            🏠 <?php esc_html_e( 'Conservation', 'celya' ); ?>
        </h3>
        <p class="text-sm text-gray-700"><?php echo nl2br( esc_html( $conservation ) ); ?></p>
    </div>
    <?php endif; ?>

    <?php if ( ! empty( $degustation ) ) : ?>
    <div class="celya-spec-block p-5 bg-celya-green_light rounded-celya-s">
        <h3 class="text-celya-primary font-serif font-bold text-lg mb-2">
            🍷 <?php esc_html_e( 'Conseil dégustation', 'celya' ); ?>
        </h3>
        <p class="text-sm text-gray-700"><?php echo nl2br( esc_html( $degustation ) ); ?></p>
    </div>
    <?php endif; ?>

</div>
```

---

## Récapitulatif des changements
```
custom-fields.php        → Remplacer la section 7 complète
woocommerce/tabs/        → Créer le dossier + les 3 fichiers