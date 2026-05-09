<?php
/**
 * Rendu serveur du bloc celya/image-card (enfant)
 * $content contient le rendu des InnerBlocks (titres, paragraphes, etc.)
 */

$image_id    = intval( $attributes['imageId']    ?? 0 );
$image_url   = $attributes['imageUrl']           ?? '';
$image_alt   = $attributes['imageAlt']           ?? '';
$image_ratio = $attributes['imageRatio']         ?? '4/3';
$card_bg     = $attributes['cardBgColor']        ?? 'celya-white';

$wrapper_attrs = get_block_wrapper_attributes( [
    'class' => 'celya-image-card has-' . esc_attr( $card_bg ) . '-background-color',
] );
?>
<div <?php echo $wrapper_attrs; ?>>

    <div class="celya-image-card__img-wrap" style="aspect-ratio:<?php echo esc_attr( $image_ratio ); ?>;">
        <?php if ( $image_url ) : ?>
            <?php if ( $image_id ) : ?>
                <?php echo wp_get_attachment_image( $image_id, 'large', false, [
                    'class'   => 'celya-image-card__img',
                    'alt'     => esc_attr( $image_alt ),
                    'loading' => 'lazy',
                ] ); ?>
            <?php else : ?>
                <img
                    src="<?php echo esc_url( $image_url ); ?>"
                    alt="<?php echo esc_attr( $image_alt ); ?>"
                    class="celya-image-card__img"
                    loading="lazy"
                />
            <?php endif; ?>
        <?php else : ?>
            <div class="celya-image-card__img-placeholder">
                <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                </svg>
            </div>
        <?php endif; ?>
    </div>

    <div class="celya-image-card__body">
        <?php echo $content; ?>
    </div>

</div>
