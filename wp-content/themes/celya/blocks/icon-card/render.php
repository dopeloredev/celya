<?php
/**
 * Rendu serveur du block celya/icon-card
 *
 * Variables disponibles : $attributes, $content, $block
 */

$media_url      = $attributes['mediaUrl']        ?? '';
$media_alt      = $attributes['mediaAlt']        ?? '';
$bg_slug        = $attributes['backgroundColor'] ?? 'celya-primary';
$radius_key     = $attributes['borderRadius']    ?? 'none';
$size           = max( 32, intval( $attributes['size'] ?? 80 ) );
$icon_alignment = in_array( $attributes['iconAlignment'] ?? 'left', [ 'left', 'center', 'right' ], true )
    ? $attributes['iconAlignment']
    : 'left';

$radius_map = [
    'none'   => '0px',
    'small'  => '8px',
    'medium' => '16px',
    'large'  => '24px',
    'xl'     => '32px',
    'full'   => '50%',
];
$radius = $radius_map[ $radius_key ] ?? '0px';

$bg_inline = sprintf(
    'width:%1$dpx;height:%1$dpx;border-radius:%2$s;',
    $size,
    esc_attr( $radius )
);

$wrapper_attrs = get_block_wrapper_attributes( [ 'class' => 'celya-icon-card is-align-' . $icon_alignment ] );
?>
<div <?php echo $wrapper_attrs; ?>>
    <div
        class="celya-icon-card__bg has-<?php echo esc_attr( $bg_slug ); ?>-background-color"
        style="<?php echo $bg_inline; ?>"
    >
        <?php if ( $media_url ) : ?>
            <img
                src="<?php echo esc_url( $media_url ); ?>"
                alt="<?php echo esc_attr( $media_alt ); ?>"
                class="celya-icon-card__icon"
                loading="lazy"
            />
        <?php endif; ?>
    </div>
</div>
