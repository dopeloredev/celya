<?php
/**
 * Rendu serveur du block celya/bullet-list
 *
 * Variables disponibles : $attributes, $content, $block
 */

$items     = $attributes['items']    ?? [];
$icon_url  = $attributes['iconUrl']  ?? '';
$icon_alt  = $attributes['iconAlt']  ?? '';
$icon_size = max( 10, intval( $attributes['iconSize'] ?? 20 ) );
$gap       = max( 0, intval( $attributes['gap'] ?? 12 ) );

if ( empty( $items ) ) {
    return;
}

$icon_style = sprintf(
    'width:%1$dpx;height:%1$dpx;',
    $icon_size
);

$wrapper_attrs = get_block_wrapper_attributes( [
    'class' => 'celya-bullet-list',
    'style' => 'gap:' . $gap . 'px;',
] );
?>
<ul <?php echo $wrapper_attrs; ?>>
    <?php foreach ( $items as $item ) :
        $text = wp_kses_post( $item['text'] ?? '' );
        if ( '' === trim( wp_strip_all_tags( $text ) ) ) continue;
    ?>
    <li class="celya-bullet-list__item">
        <span class="celya-bullet-list__marker" aria-hidden="true">
            <?php if ( $icon_url ) : ?>
                <img
                    src="<?php echo esc_url( $icon_url ); ?>"
                    alt="<?php echo esc_attr( $icon_alt ); ?>"
                    class="celya-bullet-list__icon"
                    style="<?php echo esc_attr( $icon_style ); ?>"
                    loading="lazy"
                />
            <?php else : ?>
                <span class="celya-bullet-list__dot">•</span>
            <?php endif; ?>
        </span>
        <span class="celya-bullet-list__text"><?php echo $text; ?></span>
    </li>
    <?php endforeach; ?>
</ul>
