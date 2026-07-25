<?php
/**
 * Galerie d'images par variation
 *
 * @package Celya
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* =========================================================================
 * ADMIN — Affichage du champ galerie dans chaque ligne de variation
 * ======================================================================= */

add_action( 'woocommerce_product_after_variable_attributes', 'celya_variation_gallery_field', 10, 3 );
function celya_variation_gallery_field( $loop, $variation_data, $variation ) {
	$variation_id = $variation->ID;
	$gallery_ids  = get_post_meta( $variation_id, '_celya_variation_gallery_ids', true );
	$gallery_ids  = ( ! empty( $gallery_ids ) && is_array( $gallery_ids ) ) ? $gallery_ids : array();
	$nonce        = wp_create_nonce( 'celya_var_gallery_' . $variation_id );
	?>
	<div class="celya-var-gallery form-row form-row-full" data-loop="<?php echo esc_attr( $loop ); ?>">
		<label><?php esc_html_e( 'Galerie d\'images supplémentaires', 'celya' ); ?></label>
		<input type="hidden"
			name="celya_var_gallery_nonce[<?php echo esc_attr( $loop ); ?>]"
			value="<?php echo esc_attr( $nonce ); ?>">
		<div class="celya-var-gallery__thumbs">
			<?php foreach ( $gallery_ids as $img_id ) :
				$thumb_url = wp_get_attachment_image_url( $img_id, 'thumbnail' );
				if ( ! $thumb_url ) continue;
			?>
			<div class="celya-var-gallery__item">
				<img src="<?php echo esc_url( $thumb_url ); ?>" alt="">
				<button type="button" class="celya-var-gallery__remove" title="Supprimer">&times;</button>
				<input type="hidden"
					name="celya_var_gallery_ids[<?php echo esc_attr( $loop ); ?>][]"
					value="<?php echo esc_attr( $img_id ); ?>">
			</div>
			<?php endforeach; ?>
		</div>
		<button type="button" class="button celya-var-gallery__add" data-loop="<?php echo esc_attr( $loop ); ?>">
			<?php esc_html_e( 'Ajouter des images', 'celya' ); ?>
		</button>
	</div>
	<?php
}

/* =========================================================================
 * ADMIN — Sauvegarde des IDs de la galerie par variation
 * ======================================================================= */

add_action( 'woocommerce_save_product_variation', 'celya_variation_gallery_save', 10, 2 );
function celya_variation_gallery_save( $variation_id, $loop ) {
	if ( ! current_user_can( 'edit_products' ) ) {
		return;
	}

	$nonce = isset( $_POST['celya_var_gallery_nonce'][ $loop ] )
		? sanitize_text_field( wp_unslash( $_POST['celya_var_gallery_nonce'][ $loop ] ) )
		: '';

	if ( ! wp_verify_nonce( $nonce, 'celya_var_gallery_' . $variation_id ) ) {
		return;
	}

	if ( isset( $_POST['celya_var_gallery_ids'][ $loop ] ) ) {
		$ids = array_values( array_filter( array_map( 'absint', (array) $_POST['celya_var_gallery_ids'][ $loop ] ) ) );
		update_post_meta( $variation_id, '_celya_variation_gallery_ids', $ids );
	} else {
		delete_post_meta( $variation_id, '_celya_variation_gallery_ids' );
	}
}

/* =========================================================================
 * FRONTEND — Enqueue + données localisées pour le JS
 * ======================================================================= */

add_action( 'wp_enqueue_scripts', 'celya_variation_gallery_enqueue', 25 );
function celya_variation_gallery_enqueue() {
	if ( ! is_product() ) {
		return;
	}

	global $product;
	if ( ! $product instanceof WC_Product ) {
		$product = wc_get_product( get_the_ID() );
	}
	if ( ! $product || ! $product->is_type( 'variable' ) ) {
		return;
	}

	$js_file = get_template_directory() . '/assets/js/variation-gallery.js';
	if ( ! file_exists( $js_file ) ) {
		return;
	}

	wp_enqueue_script(
		'celya-variation-gallery',
		get_template_directory_uri() . '/assets/js/variation-gallery.js',
		array( 'jquery', 'celya-product-page' ),
		filemtime( $js_file ),
		true
	);

	// Construire le tableau image par variation (toutes celles avec au moins une image propre)
	$gallery_data  = array();
	$variation_ids = $product->get_children();

	foreach ( $variation_ids as $variation_id ) {
		$variation = wc_get_product( $variation_id );
		if ( ! $variation ) {
			continue;
		}

		$extra_ids = get_post_meta( $variation_id, '_celya_variation_gallery_ids', true );
		$extra_ids = ( ! empty( $extra_ids ) && is_array( $extra_ids ) ) ? $extra_ids : array();

		$images    = array();

		// Image native de la variation en premier
		$native_id = $variation->get_image_id();
		if ( $native_id ) {
			$images[] = array(
				'thumb' => wp_get_attachment_image_url( $native_id, 'woocommerce_gallery_thumbnail' ) ?: '',
				'full'  => wp_get_attachment_image_url( $native_id, 'woocommerce_single' ) ?: '',
				'alt'   => get_post_meta( $native_id, '_wp_attachment_image_alt', true ) ?: '',
			);
		}

		// Images supplémentaires du meta
		foreach ( $extra_ids as $img_id ) {
			$full_url = wp_get_attachment_image_url( $img_id, 'woocommerce_single' );
			if ( ! $full_url ) {
				continue;
			}
			$images[] = array(
				'thumb' => wp_get_attachment_image_url( $img_id, 'woocommerce_gallery_thumbnail' ) ?: '',
				'full'  => $full_url,
				'alt'   => get_post_meta( $img_id, '_wp_attachment_image_alt', true ) ?: '',
			);
		}

		if ( ! empty( $images ) ) {
			$gallery_data[ $variation_id ] = $images;
		}
	}

	wp_localize_script( 'celya-variation-gallery', 'celyaVarGallery', $gallery_data );
}

/* =========================================================================
 * ADMIN — Enqueue scripts et styles pour l'interface d'édition produit
 * ======================================================================= */

add_action( 'admin_enqueue_scripts', 'celya_variation_gallery_admin_enqueue' );
function celya_variation_gallery_admin_enqueue() {
	$screen = get_current_screen();
	if ( ! $screen || $screen->id !== 'product' ) {
		return;
	}

	$css_file = get_template_directory() . '/assets/css/variation-gallery-admin.css';
	if ( file_exists( $css_file ) ) {
		wp_enqueue_style(
			'celya-variation-gallery-admin',
			get_template_directory_uri() . '/assets/css/variation-gallery-admin.css',
			array(),
			filemtime( $css_file )
		);
	}

	$js_file = get_template_directory() . '/assets/js/variation-gallery-admin.js';
	if ( file_exists( $js_file ) ) {
		wp_enqueue_media();
		wp_enqueue_script(
			'celya-variation-gallery-admin',
			get_template_directory_uri() . '/assets/js/variation-gallery-admin.js',
			array( 'jquery' ),
			filemtime( $js_file ),
			true
		);
	}
}
