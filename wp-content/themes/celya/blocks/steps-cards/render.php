<?php
/**
 * Rendu serveur du block celya/steps-cards (conteneur)
 * $content contient le rendu cumulé de chaque celya/steps-card enfant.
 */

$show_numbers   = $attributes['showNumbers']    ?? true;
$show_connector = $attributes['showConnector']  ?? true;
$connector_slug = $attributes['connectorColor'] ?? 'celya-orange-dark';

$classes = 'celya-steps-cards';
if ( $show_connector )  $classes .= ' celya-steps-cards--has-connector';
else                    $classes .= ' celya-steps-cards--no-connector';
if ( $show_numbers )    $classes .= ' celya-steps-cards--numbered';

$wrapper_attrs = get_block_wrapper_attributes( [
    'class' => $classes,
    'style' => '--celya-connector-color:var(--wp--preset--color--' . esc_attr( $connector_slug ) . ',#F2B28D);',
] );
?>
<div <?php echo $wrapper_attrs; ?>>
    <?php echo $content; ?>
</div>
