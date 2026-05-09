<?php
/**
 * Rendu serveur du block celya/steps-card (enfant)
 * $content contient le rendu des InnerBlocks (titres, paragraphes, etc.)
 */

$icon_url     = $attributes['iconUrl']     ?? '';
$icon_alt     = $attributes['iconAlt']     ?? '';
$show_icon    = $attributes['showIcon']    ?? true;
$card_bg      = $attributes['cardBgColor'] ?? 'celya-white';
$icon_bg      = $attributes['iconBgColor'] ?? 'celya-orange-light';
$icon_shape   = $attributes['iconShape']   ?? 'circle';
$icon_radius  = max( 0, intval( $attributes['iconRadius'] ?? 12 ) );
$icon_size    = max( 32, intval( $attributes['iconSize']  ?? 64 ) );
$show_border  = $attributes['showBorder']  ?? false;
$border_color = $attributes['borderColor'] ?? 'celya-primary';

$celya_colors = [
    'celya-white'        => '#ffffff',
    'celya-light'        => '#FAF9F8',
    'celya-grey-light'   => '#F6F6F6',
    'celya-orange-light' => '#FDECE2',
    'celya-secondary'    => '#F2D0A7',
    'celya-blue-light'   => '#F2F7FC',
    'celya-green-light'  => '#E9F6E8',
    'celya-yellow-light' => '#FCF5DD',
    'celya-pink-light'   => '#F9E8EE',
    'celya-orange-dark'  => '#F2B28D',
    'celya-blue-dark'    => '#BDD9F2',
    'celya-green-dark'   => '#ABE0A4',
    'celya-yellow-dark'  => '#F2D479',
    'celya-pink-dark'    => '#EDA2C1',
    'celya-primary'      => '#59332A',
    'celya-dark'         => '#2C2C2C',
];

$border_hex     = $celya_colors[ $border_color ] ?? '#59332A';
$icon_px        = $icon_size . 'px';
$border_radius  = ( $icon_shape === 'circle' ) ? '50%' : ( $icon_radius . 'px' );
$border_style   = $show_border ? 'border:2px solid ' . esc_attr( $border_hex ) . ';' : '';

$wrapper_attrs = get_block_wrapper_attributes( [
    'class' => 'celya-steps-card has-' . esc_attr( $card_bg ) . '-background-color',
    'style' => $border_style,
] );
?>
<div <?php echo $wrapper_attrs; ?>>

    <?php if ( $show_icon ) : ?>
    <div
        class="celya-steps-card__icon-wrap has-<?php echo esc_attr( $icon_bg ); ?>-background-color"
        style="width:<?php echo esc_attr( $icon_px ); ?>;height:<?php echo esc_attr( $icon_px ); ?>;border-radius:<?php echo esc_attr( $border_radius ); ?>;"
    >
        <?php if ( $icon_url ) : ?>
            <img
                src="<?php echo esc_url( $icon_url ); ?>"
                alt="<?php echo esc_attr( $icon_alt ); ?>"
                class="celya-steps-card__icon"
                loading="lazy"
            />
        <?php else : ?>
            <svg class="celya-steps-card__icon-placeholder" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
            </svg>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <div class="celya-steps-card__body">
        <?php echo $content; ?>
    </div>

</div>
