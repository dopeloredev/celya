<?php
/**
 * Template Name: Réalisations
 *
 * @package Celya
 */

get_header();

$current_occasion = sanitize_text_field( $_GET['occasion'] ?? '' );
$current_page     = max( 1, intval( $_GET['pg'] ?? 1 ) );
$occasions        = get_terms( [ 'taxonomy' => 'occasion', 'hide_empty' => true ] );
?>

<main id="site-content">

    <!-- ── Contenu Gutenberg de la page ─────────────────────────────────── -->
    <?php while ( have_posts() ) : the_post(); ?>
        <?php if ( has_blocks() ) : ?>
            <div class="section-container">
                <?php the_content(); ?>
            </div>
        <?php endif; ?>
    <?php endwhile; ?>

    <!-- ── Filtres ───────────────────────────────────────────────────────── -->
    <?php if ( $occasions && ! is_wp_error( $occasions ) ) : ?>
    <div class="sticky top-20 z-30 pt-5">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-wrap gap-2 py-4" role="tablist" aria-label="<?php esc_attr_e( 'Filtrer par occasion', 'celya' ); ?>">

                <button
                    class="realisation-archive-filter rounded-full border px-4 py-1.5 text-sm font-medium transition-colors duration-200 cursor-pointer <?php echo empty( $current_occasion ) ? 'bg-celya-primary text-white border-celya-primary' : 'border-celya-primary text-celya-primary'; ?>"
                    data-occasion=""
                    aria-pressed="<?php echo empty( $current_occasion ) ? 'true' : 'false'; ?>"
                >
                    <?php esc_html_e( 'Toutes', 'celya' ); ?>
                </button>

                <?php foreach ( $occasions as $occ ) :
                    $is_active = $current_occasion === $occ->slug;
                ?>
                <button
                    class="realisation-archive-filter rounded-full border px-4 py-1.5 text-sm font-medium transition-colors duration-200 cursor-pointer <?php echo $is_active ? 'bg-celya-primary text-white border-celya-primary' : 'border-celya-primary text-celya-primary'; ?>"
                    data-occasion="<?php echo esc_attr( $occ->slug ); ?>"
                    aria-pressed="<?php echo $is_active ? 'true' : 'false'; ?>"
                >
                    <?php echo esc_html( $occ->name ); ?>
                </button>
                <?php endforeach; ?>

            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- ── Listing réalisations ──────────────────────────────────────────── -->
    <div class="section-container !pt-8">

        <div id="realisations-loading" class="fixed inset-0 bg-white/50 backdrop-blur-sm flex items-center justify-center z-50 opacity-0 pointer-events-none transition-opacity duration-200">
            <div class="w-10 h-10 rounded-full border-2 border-celya-primary border-t-transparent animate-spin"></div>
        </div>

        <div id="realisations-content">
            <?php echo celya_realisations_render( $current_occasion, $current_page ); ?>
        </div>

    </div>

</main>

<?php get_footer(); ?>
