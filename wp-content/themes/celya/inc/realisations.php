<?php
/**
 * Réalisations — CPT, taxonomie et shortcode
 *
 * @package Celya
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Custom Post Type : realisation
 */
function celya_register_cpt_realisation() {
    $labels = array(
        'name'               => __( 'Réalisations', 'celya' ),
        'singular_name'      => __( 'Réalisation', 'celya' ),
        'add_new_item'       => __( 'Ajouter une réalisation', 'celya' ),
        'edit_item'          => __( 'Modifier la réalisation', 'celya' ),
        'search_items'       => __( 'Rechercher une réalisation', 'celya' ),
        'not_found'          => __( 'Aucune réalisation trouvée.', 'celya' ),
        'menu_name'          => __( 'Célya - Réalisations', 'celya' ),
    );

    $puzzle_svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="black"><path d="M20.5 11H19V7c0-1.1-.9-2-2-2h-4V3.5C13 2.12 11.88 1 10.5 1S8 2.12 8 3.5V5H4c-1.1 0-1.99.9-1.99 2v3.8H3.5c1.49 0 2.7 1.21 2.7 2.7s-1.21 2.7-2.7 2.7H2V20c0 1.1.9 2 2 2h3.8v-1.5c0-1.49 1.21-2.7 2.7-2.7 1.49 0 2.7 1.21 2.7 2.7V22H17c1.1 0 2-.9 2-2v-4h1.5c1.38 0 2.5-1.12 2.5-2.5S21.88 11 20.5 11z"/></svg>';

    register_post_type( 'realisation', array(
        'labels'              => $labels,
        'public'              => true,
        'has_archive'         => false,
        'show_in_rest'        => true,
        'supports'            => array( 'title', 'thumbnail' ),
        'rewrite'             => array( 'slug' => 'realisations' ),
        'menu_icon'           => 'data:image/svg+xml;base64,' . base64_encode( $puzzle_svg ),
        'menu_position'       => 3,
        'exclude_from_search' => false,
    ) );
}
add_action( 'init', 'celya_register_cpt_realisation' );

/**
 * Taxonomie : occasion (non-hiérarchique)
 */
function celya_register_taxonomy_occasion() {
    $labels = array(
        'name'              => __( 'Occasions', 'celya' ),
        'singular_name'     => __( 'Occasion', 'celya' ),
        'search_items'      => __( 'Rechercher une occasion', 'celya' ),
        'all_items'         => __( 'Toutes les occasions', 'celya' ),
        'edit_item'         => __( 'Modifier l\'occasion', 'celya' ),
        'update_item'       => __( 'Mettre à jour', 'celya' ),
        'add_new_item'      => __( 'Ajouter une occasion', 'celya' ),
        'new_item_name'     => __( 'Nouvelle occasion', 'celya' ),
        'menu_name'         => __( 'Occasions', 'celya' ),
        'not_found'         => __( 'Aucune occasion trouvée.', 'celya' ),
    );

    register_taxonomy( 'occasion', array( 'realisation' ), array(
        'labels'            => $labels,
        'hierarchical'      => false,
        'public'            => true,
        'show_in_rest'      => true,
        'show_admin_column' => true,
        'rewrite'           => array( 'slug' => 'occasion' ),
    ) );
}
add_action( 'init', 'celya_register_taxonomy_occasion' );

/**
 * Shortcode [realisations occasion="mariages" limit="8"]
 */
function celya_shortcode_realisations( $atts ) {
    $atts = shortcode_atts( array(
        'occasion' => '',
        'limit'    => 8,
    ), $atts, 'realisations' );

    $query_args = array(
        'post_type'      => 'realisation',
        'posts_per_page' => intval( $atts['limit'] ),
        'orderby'        => 'date',
        'order'          => 'DESC',
    );

    if ( ! empty( $atts['occasion'] ) ) {
        $query_args['tax_query'] = array(
            array(
                'taxonomy' => 'occasion',
                'field'    => 'slug',
                'terms'    => array_map( 'trim', explode( ',', $atts['occasion'] ) ),
            ),
        );
    }

    $query = new WP_Query( $query_args );

    if ( ! $query->have_posts() ) {
        return '';
    }

    ob_start();
    ?>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <?php while ( $query->have_posts() ) : $query->the_post(); ?>
            <?php echo celya_realisation_card( get_the_ID() ); ?>
        <?php endwhile; wp_reset_postdata(); ?>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode( 'realisations', 'celya_shortcode_realisations' );

/**
 * Plage de numéros de pages avec ellipsis pour la pagination.
 * Retourne un tableau mixte int|string ('…').
 */
function celya_realisations_pagination_range( $current, $total ) {
    $delta  = 2;
    $result = [];

    $start = max( 1, $current - $delta );
    $end   = min( $total, $current + $delta );

    if ( $start > 1 ) {
        $result[] = 1;
        if ( $start > 2 ) $result[] = '…';
    }

    for ( $i = $start; $i <= $end; $i++ ) {
        $result[] = $i;
    }

    if ( $end < $total ) {
        if ( $end < $total - 1 ) $result[] = '…';
        $result[] = $total;
    }

    return $result;
}

/**
 * Rendu HTML de la grille + pagination pour la page archive.
 * Utilisé à la fois par le template PHP et le handler AJAX.
 */
function celya_realisations_render( $occasion = '', $paged = 1, $per_page = 12 ) {
    $paged = max( 1, intval( $paged ) );

    $query_args = [
        'post_type'      => 'realisation',
        'posts_per_page' => $per_page,
        'paged'          => $paged,
        'orderby'        => 'date',
        'order'          => 'DESC',
    ];

    if ( ! empty( $occasion ) ) {
        $query_args['tax_query'] = [ [
            'taxonomy' => 'occasion',
            'field'    => 'slug',
            'terms'    => sanitize_text_field( $occasion ),
        ] ];
    }

    $query = new WP_Query( $query_args );

    ob_start();

    if ( ! $query->have_posts() ) : ?>

        <div class="py-24 text-center">
            <p class="text-celya-primary opacity-40 text-lg"><?php esc_html_e( 'Aucune réalisation trouvée.', 'celya' ); ?></p>
        </div>

    <?php else :
        $total_pages = $query->max_num_pages;
        $found       = $query->found_posts;
    ?>

        <p class="text-sm text-celya-primary/60 mb-6 font-sans">
            <?php echo esc_html( $found === 1 ? '1 réalisation' : $found . ' réalisations' ); ?>
            <?php if ( $paged > 1 ) : ?>
                <span class="opacity-60">— page <?php echo $paged; ?>/<?php echo $total_pages; ?></span>
            <?php endif; ?>
        </p>

        <div class="realisation-archive-grid grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6">
            <?php while ( $query->have_posts() ) : $query->the_post();
                $terms = get_the_terms( get_the_ID(), 'occasion' );
            ?>
            <article class="group relative aspect-square overflow-hidden rounded-celya-m bg-celya-grey_light cursor-default select-none">

                <?php if ( has_post_thumbnail() ) : ?>
                    <?php the_post_thumbnail( 'celya-product-large', [
                        'class'   => 'w-full h-full object-cover transition-transform duration-500 group-hover:scale-105',
                        'loading' => 'lazy',
                    ] ); ?>
                <?php else : ?>
                    <div class="w-full h-full flex items-center justify-center text-celya-primary opacity-20">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                <?php endif; ?>

                <div class="absolute inset-0 bg-gradient-to-t from-celya-primary/85 via-celya-primary/15 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col justify-end p-3 md:p-4">
                    <p class="font-serif text-white font-semibold text-sm md:text-base leading-snug"><?php the_title(); ?></p>
                    <?php if ( $terms && ! is_wp_error( $terms ) ) : ?>
                        <div class="flex flex-wrap gap-1 mt-1.5">
                            <?php foreach ( $terms as $term ) : ?>
                                <span class="text-xs text-white/80 border border-white/40 rounded-full px-2 py-0.5 leading-none">
                                    <?php echo esc_html( $term->name ); ?>
                                </span>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

            </article>
            <?php endwhile; wp_reset_postdata(); ?>
        </div>

        <?php if ( $total_pages > 1 ) :
            $range = celya_realisations_pagination_range( $paged, $total_pages );
        ?>
        <nav class="realisation-archive-pagination flex justify-center items-center gap-2 flex-wrap mt-12" aria-label="<?php esc_attr_e( 'Pagination', 'celya' ); ?>">

            <?php if ( $paged > 1 ) : ?>
            <button class="pagination-btn w-10 h-10 rounded-full border border-celya-primary text-celya-primary flex items-center justify-center hover:bg-celya-orange_light transition-colors duration-200" data-page="<?php echo $paged - 1; ?>" aria-label="<?php esc_attr_e( 'Page précédente', 'celya' ); ?>">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            </button>
            <?php endif; ?>

            <?php foreach ( $range as $p ) : ?>
                <?php if ( $p === '…' ) : ?>
                    <span class="text-celya-primary/40 px-1 select-none">…</span>
                <?php else : ?>
                    <button
                        class="pagination-btn w-10 h-10 rounded-full border text-sm font-medium transition-colors duration-200 <?php echo $p === $paged ? 'bg-celya-primary text-white border-celya-primary' : 'border-celya-primary text-celya-primary hover:bg-celya-orange_light'; ?>"
                        data-page="<?php echo intval( $p ); ?>"
                        <?php echo $p === $paged ? 'aria-current="page"' : ''; ?>
                    ><?php echo intval( $p ); ?></button>
                <?php endif; ?>
            <?php endforeach; ?>

            <?php if ( $paged < $total_pages ) : ?>
            <button class="pagination-btn w-10 h-10 rounded-full border border-celya-primary text-celya-primary flex items-center justify-center hover:bg-celya-orange_light transition-colors duration-200" data-page="<?php echo $paged + 1; ?>" aria-label="<?php esc_attr_e( 'Page suivante', 'celya' ); ?>">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            </button>
            <?php endif; ?>

        </nav>
        <?php endif; ?>

    <?php endif;

    return ob_get_clean();
}

/**
 * Handler AJAX pour le filtrage + pagination de la page archive.
 */
function celya_ajax_realisations() {
    check_ajax_referer( 'celya_nonce', 'nonce' );

    $occasion = sanitize_text_field( $_POST['occasion'] ?? '' );
    $paged    = max( 1, intval( $_POST['pg'] ?? 1 ) );

    wp_send_json_success( [
        'html' => celya_realisations_render( $occasion, $paged ),
    ] );
}
add_action( 'wp_ajax_celya_realisations',        'celya_ajax_realisations' );
add_action( 'wp_ajax_nopriv_celya_realisations', 'celya_ajax_realisations' );

/**
 * Rendu d'une carte réalisation.
 */
function celya_realisation_card( $post_id ) {
    $terms = get_the_terms( $post_id, 'occasion' );

    ob_start();
    ?>
    <div class="flex flex-col gap-2">
        <div class="aspect-square overflow-hidden rounded-celya-m bg-celya-grey_light">
            <?php if ( has_post_thumbnail( $post_id ) ) : ?>
                <?php echo get_the_post_thumbnail( $post_id, 'celya-product-thumb', array( 'class' => 'w-full h-full object-cover', 'loading' => 'lazy' ) ); ?>
            <?php else : ?>
                <div class="w-full h-full flex items-center justify-center text-celya-primary opacity-30">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
            <?php endif; ?>
        </div>

        <p class="font-serif text-sm font-semibold text-celya-primary leading-snug"><?php echo esc_html( get_the_title( $post_id ) ); ?></p>

        <?php if ( $terms && ! is_wp_error( $terms ) ) : ?>
            <div class="flex flex-wrap gap-1">
                <?php foreach ( $terms as $term ) : ?>
                    <span class="text-xs rounded-full border border-celya-primary text-celya-primary px-2 py-0.5 leading-none">
                        <?php echo esc_html( $term->name ); ?>
                    </span>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    <?php
    return ob_get_clean();
}
