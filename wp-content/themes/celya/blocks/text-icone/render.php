<?php
/**
 * Rendu serveur du bloc celya/text-icone
 * $content contient le rendu des InnerBlocks (titres, paragraphes, etc.)
 */

$media_id      = intval( $attributes['mediaId']       ?? 0 );
$media_url     = $attributes['mediaUrl']              ?? '';
$media_alt     = $attributes['mediaAlt']              ?? '';
$bg_slug       = $attributes['backgroundColor']       ?? 'celya-secondary';
$radius_key    = $attributes['borderRadius']           ?? 'medium';
$icon_size     = max( 40, intval( $attributes['iconSize'] ?? 72 ) );

$allowed_align = [ 'flex-start', 'center', 'flex-end' ];
$vertical_align = in_array( $attributes['verticalAlign'] ?? '', $allowed_align, true )
    ? $attributes['verticalAlign']
    : 'flex-start';

$radius_map = [
    'none'   => '0px',
    'small'  => '8px',
    'medium' => '16px',
    'large'  => '24px',
    'xl'     => '32px',
    'full'   => '50%',
];
$radius   = $radius_map[ $radius_key ] ?? '16px';
$img_size = round( $icon_size * 0.6 );

$square_style = sprintf(
    'width:%1$dpx;height:%1$dpx;min-width:%1$dpx;border-radius:%2$s;',
    $icon_size,
    esc_attr( $radius )
);

$wrapper_attrs = get_block_wrapper_attributes( [
    'class' => 'celya-text-icone',
    'style' => 'align-items:' . esc_attr( $vertical_align ) . ';',
] );
?>
<div <?php echo $wrapper_attrs; ?>>

    <div
        class="celya-text-icone__icon-wrap has-<?php echo esc_attr( $bg_slug ); ?>-background-color"
        style="<?php echo $square_style; ?>"
    >
        <?php if ( $media_url ) : ?>
            <?php if ( $media_id ) : ?>
                <?php echo wp_get_attachment_image( $media_id, 'thumbnail', false, [
                    'class'   => 'celya-text-icone__icon',
                    'alt'     => esc_attr( $media_alt ),
                    'loading' => 'lazy',
                    'style'   => 'width:' . $img_size . 'px;height:' . $img_size . 'px;',
                ] ); ?>
            <?php else : ?>
                <img
                    src="<?php echo esc_url( $media_url ); ?>"
                    alt="<?php echo esc_attr( $media_alt ); ?>"
                    class="celya-text-icone__icon"
                    loading="lazy"
                    style="width:<?php echo $img_size; ?>px;height:<?php echo $img_size; ?>px;"
                />
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <div class="celya-text-icone__content">
        <?php echo $content; ?>
    </div>

</div>
