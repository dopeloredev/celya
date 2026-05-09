<?php
/**
 * Rendu serveur du bloc celya/image-cards (conteneur)
 * $content contient le rendu cumulé de chaque celya/image-card enfant.
 */

$columns = max( 1, intval( $attributes['columns'] ?? 3 ) );
$gap     = max( 0, intval( $attributes['gap']     ?? 24 ) );

$wrapper_attrs = get_block_wrapper_attributes( [
    'class' => 'celya-image-cards',
    'style' => 'grid-template-columns:repeat(' . $columns . ',1fr);gap:' . $gap . 'px;',
] );
?>
<div <?php echo $wrapper_attrs; ?>>
    <?php echo $content; ?>
</div>
