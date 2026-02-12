<?php
/**
 * The Template for displaying all single products
 *
 * @package Celya
 * @version 8.6.0
 */

defined( 'ABSPATH' ) || exit;

get_header( 'shop' );

/**
 * Hook: woocommerce_before_main_content.
 */
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );
do_action( 'woocommerce_before_main_content' );

?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-16">
    
    <?php while ( have_posts() ) : ?>
        <?php the_post();  // Charge les données du produit actuel ?> 
        <?php wc_get_template_part( 'content', 'single-product' ); // Charge content-single-product.php ?>
    <?php endwhile; ?>

</div>

<?php
/**
 * Hook: woocommerce_after_main_content.
 */
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );
do_action( 'woocommerce_after_main_content' );

get_footer( 'shop' );