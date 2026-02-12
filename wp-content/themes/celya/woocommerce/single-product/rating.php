<?php
/**
 * Single Product Rating
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/rating.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

global $product;

if ( ! wc_review_ratings_enabled() ) {
	return;
}

$rating_count = $product->get_rating_count();
$review_count = $product->get_review_count();
$average      = $product->get_average_rating();

if ( $rating_count > 0 ) : 
	// Formater la note : enlever les décimales si elles sont à .00
	$average_formatted = ( floor($average) == $average ) ? number_format($average, 0) : number_format($average, 1, ',', '');
?>

	<div class="flex items-center gap-2">
		<div class="flex items-center">
			<?php echo wc_get_rating_html( $average_formatted, $rating_count ); ?>
		</div>
		<span class="text-sm text-celya-dark">
			(<?php echo esc_html( $average_formatted )."/5 - "; ?> <?php echo esc_html( $review_count ); ?> <?php echo $review_count > 1 ? 'avis' : 'avis'; ?>)
		</span>
	</div>

<?php endif; ?>
