<?php
/**
 * Template archive — Réalisations
 *
 * @package Celya
 */

get_header();

$current_occasion = sanitize_text_field( $_GET['occasion'] ?? '' );
$current_page     = max( 1, intval( $_GET['pg'] ?? 1 ) );
$occasions        = get_terms( [ 'taxonomy' => 'occasion', 'hide_empty' => true ] );
?>

<main id="site-content">

    <!-- ── Hero ─────────────────────────────────────────────────────────── -->
    <div class="section-wrapper bg-celya-orange_light">
        <div class="section-container !py-10 md:!py-14">
            <p class="section-subtitle"><?php esc_html_e( 'Galerie', 'celya' ); ?></p>
            <h1 class="font-serif text-4xl md:text-5xl font-bold text-celya-primary mt-1">
                <?php esc_html_e( 'Nos réalisations', 'celya' ); ?>
            </h1>
        </div>
    </div>

    <!-- ── Filtres ───────────────────────────────────────────────────────── -->
    <?php if ( $occasions && ! is_wp_error( $occasions ) ) : ?>
    <div class="border-b border-celya-secondary bg-white sticky top-20 z-30">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-wrap gap-2 py-4" id="realisations-filters" role="tablist" aria-label="<?php esc_attr_e( 'Filtrer par occasion', 'celya' ); ?>">

                <button
                    class="realisation-archive-filter rounded-full border px-4 py-1.5 text-sm font-medium transition-colors duration-200 cursor-pointer <?php echo empty( $current_occasion ) ? 'bg-celya-primary text-white border-celya-primary' : 'border-celya-primary text-celya-primary hover:bg-celya-orange_light'; ?>"
                    data-occasion=""
                    aria-pressed="<?php echo empty( $current_occasion ) ? 'true' : 'false'; ?>"
                >
                    <?php esc_html_e( 'Toutes', 'celya' ); ?>
                </button>

                <?php foreach ( $occasions as $occ ) :
                    $is_active = $current_occasion === $occ->slug;
                ?>
                <button
                    class="realisation-archive-filter rounded-full border px-4 py-1.5 text-sm font-medium transition-colors duration-200 cursor-pointer <?php echo $is_active ? 'bg-celya-primary text-white border-celya-primary' : 'border-celya-primary text-celya-primary hover:bg-celya-orange_light'; ?>"
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

    <!-- ── Contenu (grille + pagination) ────────────────────────────────── -->
    <div class="section-container !pt-8">

        <!-- Overlay de chargement -->
        <div id="realisations-loading" class="fixed inset-0 bg-white/50 backdrop-blur-sm flex items-center justify-center z-50 opacity-0 pointer-events-none transition-opacity duration-200">
            <div class="w-10 h-10 rounded-full border-2 border-celya-primary border-t-transparent animate-spin"></div>
        </div>

        <!-- Zone remplacée par AJAX -->
        <div id="realisations-content">
            <?php echo celya_realisations_render( $current_occasion, $current_page ); ?>
        </div>

    </div>

</main>

<?php get_footer(); ?>
