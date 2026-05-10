<?php
/**
 * Rendu serveur du bloc celya/realisations
 */

$occasions_filter    = $attributes['occasions']          ?? [];
$limit               = intval( $attributes['limit']      ?? 8 );
$show_filters        = (bool) ( $attributes['showFilters'] ?? true );
$columns             = max( 1, intval( $attributes['columns']   ?? 4 ) );
$image_size          = sanitize_text_field( $attributes['imageSize']  ?? 'celya-product-thumb' );
$image_ratio         = sanitize_text_field( $attributes['imageRatio'] ?? 'square' );

/* Couleurs des pills ---------------------------------------------------- */
$inactive_border = sanitize_hex_color( $attributes['pillInactiveBorder'] ?? '#59332A' ) ?: '#59332A';
$inactive_bg     = sanitize_hex_color( $attributes['pillInactiveBg']     ?? ''        ) ?: 'transparent';
$inactive_text   = sanitize_hex_color( $attributes['pillInactiveText']   ?? '#59332A' ) ?: '#59332A';
$active_border   = sanitize_hex_color( $attributes['pillActiveBorder']   ?? '#59332A' ) ?: '#59332A';
$active_bg       = sanitize_hex_color( $attributes['pillActiveBg']       ?? '#59332A' ) ?: '#59332A';
$active_text     = sanitize_hex_color( $attributes['pillActiveText']     ?? '#ffffff' ) ?: '#ffffff';

/* Ratio d'affichage des images ------------------------------------------ */
$ratio_map   = [
    'square'   => 'aspect-square',
    '4-3'      => 'aspect-[4/3]',
    '16-9'     => 'aspect-video',
    '3-4'      => 'aspect-[3/4]',
];
$ratio_class = $ratio_map[ $image_ratio ] ?? 'aspect-square';

/* Query ----------------------------------------------------------------- */
$query_args = [
    'post_type'      => 'realisation',
    'posts_per_page' => $limit > 0 ? $limit : -1,
    'orderby'        => 'date',
    'order'          => 'DESC',
];

if ( ! empty( $occasions_filter ) ) {
    $query_args['tax_query'] = [ [
        'taxonomy' => 'occasion',
        'field'    => 'slug',
        'terms'    => $occasions_filter,
    ] ];
}

$query = new WP_Query( $query_args );

if ( ! $query->have_posts() ) {
    echo '<p class="py-8 text-center text-celya-primary opacity-50">'
        . esc_html__( 'Aucune réalisation publiée.', 'celya' )
        . '</p>';
    return;
}

$posts_data     = [];
$seen_occasions = [];

while ( $query->have_posts() ) {
    $query->the_post();
    $post_id = get_the_ID();
    $terms   = get_the_terms( $post_id, 'occasion' );
    $slugs   = [];

    if ( $terms && ! is_wp_error( $terms ) ) {
        foreach ( $terms as $term ) {
            $slugs[]                       = $term->slug;
            $seen_occasions[ $term->slug ] = $term->name;
        }
    }

    $posts_data[] = [ 'id' => $post_id, 'slugs' => $slugs ];
}
wp_reset_postdata();

/* Wrapper --------------------------------------------------------------- */
$inline_vars  = '--grid-cols:'            . $columns         . ';';
$inline_vars .= '--pill-inactive-border:' . $inactive_border . ';';
$inline_vars .= '--pill-inactive-bg:'     . $inactive_bg     . ';';
$inline_vars .= '--pill-inactive-text:'   . $inactive_text   . ';';
$inline_vars .= '--pill-active-border:'   . $active_border   . ';';
$inline_vars .= '--pill-active-bg:'       . $active_bg       . ';';
$inline_vars .= '--pill-active-text:'     . $active_text     . ';';

$wrapper_attrs = get_block_wrapper_attributes( [ 'style' => $inline_vars ] );
?>
<div <?php echo $wrapper_attrs; ?>>

    <?php if ( $show_filters && count( $seen_occasions ) > 1 ) : ?>
    <div class="flex flex-wrap gap-2 mb-6">
        <button class="realisation-filter-pill" data-filter="all" aria-pressed="true">
            <?php esc_html_e( 'Toutes', 'celya' ); ?>
        </button>
        <?php foreach ( $seen_occasions as $slug => $name ) : ?>
        <button class="realisation-filter-pill" data-filter="<?php echo esc_attr( $slug ); ?>" aria-pressed="false">
            <?php echo esc_html( $name ); ?>
        </button>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <div class="realisation-grid">
        <?php foreach ( $posts_data as $data ) : ?>
        <div class="realisation-item" data-occasions="<?php echo esc_attr( implode( ' ', $data['slugs'] ) ); ?>">
            <div class="<?php echo esc_attr( $ratio_class ); ?> overflow-hidden rounded-celya-m bg-celya-grey_light">
                <?php if ( has_post_thumbnail( $data['id'] ) ) : ?>
                    <?php echo get_the_post_thumbnail( $data['id'], $image_size, [
                        'class'   => 'w-full h-full object-cover',
                        'loading' => 'lazy',
                    ] ); ?>
                <?php else : ?>
                    <div class="w-full h-full flex items-center justify-center text-celya-primary opacity-30">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <p class="hidden text-center text-celya-primary mt-6 realisation-empty">
        <?php esc_html_e( 'Aucune réalisation pour cette occasion.', 'celya' ); ?>
    </p>

</div>
