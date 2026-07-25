<?php
/**
 * Single Product Image
 *
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 10.5.0
 */

use Automattic\WooCommerce\Enums\ProductType;

defined( 'ABSPATH' ) || exit;

// Note: `wc_get_gallery_image_html` was added in WC 3.3.2 and did not exist prior. This check protects against theme overrides being used on older versions of WC.
if ( ! function_exists( 'wc_get_gallery_image_html' ) ) {
    return;
}

global $product;

$columns           = apply_filters( 'woocommerce_product_thumbnails_columns', 4 );
$post_thumbnail_id = $product->get_image_id();
$wrapper_classes   = apply_filters(
    'woocommerce_single_product_image_gallery_classes',
    array(
        'woocommerce-product-gallery',
        'woocommerce-product-gallery--' . ( $post_thumbnail_id ? 'with-images' : 'without-images' ),
        'woocommerce-product-gallery--columns-' . absint( $columns ),
        'images',
    )
);
?>

<!-- Miniatures verticales à gauche -->
<div class="flex flex-col gap-3 w-20 flex-shrink-0">
    <?php
    // Miniature image principale
    $main_thumb_url  = get_the_post_thumbnail_url( get_the_ID(), 'woocommerce_single' );
    $main_thumb_small = get_the_post_thumbnail_url( get_the_ID(), 'woocommerce_gallery_thumbnail' );
    ?>
    <div class="celya-thumb active-thumb aspect-square rounded-lg overflow-hidden cursor-pointer border-2 border-celya-accent-dark"
        data-full="<?php echo esc_url( $main_thumb_url ); ?>">
        <?php 
        if ( has_post_thumbnail() ) {
            echo '<div class="w-full h-full">';
            the_post_thumbnail( 'woocommerce_single', array(
                'class' => 'w-full h-full object-cover transition-opacity duration-200',
            ));
            echo '</div>';
        } else {
            echo '<div class="w-full h-full flex items-center justify-center bg-celya-light transition-opacity duration-200">';
            echo wc_placeholder_img( 'woocommerce_single' );
            echo '</div>';
        }
        ?>
    </div>

    <?php
    $attachment_ids = $product->get_gallery_image_ids();
    foreach ( $attachment_ids as $attachment_id ) :
        $full_url  = wp_get_attachment_image_url( $attachment_id, 'woocommerce_single' );
        $thumb_url = wp_get_attachment_image_url( $attachment_id, 'woocommerce_gallery_thumbnail' );
    ?>
    <div class="celya-thumb aspect-square rounded-lg overflow-hidden cursor-pointer border-2 border-transparent opacity-60 hover:opacity-100 hover:border-celya-accent-dark transition-all"
        data-full="<?php echo esc_url( $full_url ); ?>">
        <img src="<?php echo esc_url( $thumb_url ); ?>" class="w-full h-full object-cover" alt="">
    </div>
    <?php endforeach; ?>
</div>

<!-- Image principale -->
<div class="relative w-full h-[575px] overflow-hidden rounded-2xl bg-white shadow-md border border-celya-light group">
    <?php
    // Image principale — toujours rendu avec #celya-main-image pour que le JS puisse la cibler,
    // que le produit ait une image ou non.
    $main_src = get_the_post_thumbnail_url( get_the_ID(), 'woocommerce_single' )
        ?: wc_placeholder_img_src( 'woocommerce_single' );
    $main_alt = get_post_meta( get_post_thumbnail_id( get_the_ID() ), '_wp_attachment_image_alt', true )
        ?: get_the_title();
    ?>
    <img id="celya-main-image"
         src="<?php echo esc_url( $main_src ); ?>"
         class="absolute inset-0 w-full h-full object-cover transition-opacity duration-200"
         alt="<?php echo esc_attr( $main_alt ); ?>">

    <!-- Tags produit -->
    <div class="absolute top-4 right-4 flex flex-col gap-2">
        <?php
        // Le badge reprend l'accent de la page (piloté par body.theme-* selon
        // le tag) : plus besoin de mapper une couleur par tag ici.
        $product_tags = get_the_terms( $product->get_id(), 'product_tag' );
        if ( $product_tags && ! is_wp_error( $product_tags ) ) :
            $first_tag = $product_tags[0]->name;
            if ( in_array( $first_tag, array( 'Salé', 'Sucré', 'Spécialité' ), true ) ) :
                echo '<span class="bg-celya-accent-dark text-xs text-celya-primary font-bold px-3 py-1.5 rounded-full uppercase tracking-wider">' . esc_html( $first_tag ) . '</span>';
            endif;
        endif;

        if ( $product->is_on_sale() ) :
            echo '<span class="bg-red-500 text-white text-xs font-bold px-3 py-1.5 rounded-full uppercase tracking-wider">Promo</span>';
        endif;
        ?>
    </div>
</div>

