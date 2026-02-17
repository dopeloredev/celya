<?php
/**
 * Template Part : Section avis produit
 * Affichée séparément, en dehors des onglets.
 *
 * Usage : get_template_part( 'template-parts/product-reviews' );
 *
 * @package Celya
 */

defined( 'ABSPATH' ) || exit;

global $product;

if ( ! $product || ! comments_open() ) {
    return;
}

$rating_count = $product->get_rating_count();
$review_count = $product->get_review_count();
$average      = $product->get_average_rating();

$all_comments = get_comments( array(
    'post_id' => $product->get_id(),
    'status'  => 'approve',
    'type'    => 'review',
));
?>

<div id="reviews" class="woocommerce-Reviews mt-16">

    <?php if ( $rating_count > 0 ) :

        // Répartition des notes
        $distribution = array( 5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0 );
        foreach ( $all_comments as $comment ) {
            $rating = intval( get_comment_meta( $comment->comment_ID, 'rating', true ) );
            if ( $rating >= 1 && $rating <= 5 ) {
                $distribution[ $rating ]++;
            }
        }
        $average_formatted = ( floor( $average ) == $average )
            ? number_format( $average, 0 )
            : number_format( $average, 1, ',', '' );
    ?>

    <!-- Bloc résumé des avis -->
    <div class="bg-celya-orange_light rounded-celya-m p-10 mb-12">
        <div class="grid grid-cols-1 md:grid-cols-6 gap-8 items-center">

            
            <div class="text-center md:col-span-2 border-r-celya-orange_dark border-r-2 pr-12">
                <!-- Moyenne avis -->
                <div class="text-3xl font-bold font-serif text-celya-primary mb-2">
                    <?php echo esc_html( $average_formatted ); ?>/5
                </div>
                <div class="flex justify-center mb-3">
                    <?php echo wc_get_rating_html( $average, $rating_count ); ?>
                </div>

                <!-- Détails avis -->
                <?php for ( $i = 5; $i >= 1; $i-- ) :
                    $count      = $distribution[ $i ];
                    $percentage = $review_count > 0 ? round( ( $count / $review_count ) * 100 ) : 0;
                ?>
                    <div class="flex items-center gap-3">
                        <div class="flex items-center gap-0.5 flex-shrink-0">
                            <?php for ( $star = 1; $star <= 5; $star++ ) : ?>
                                <svg class="w-3.5 h-3.5 <?php echo $star <= $i ? 'text-celya-orange_dark' : 'text-gray-300'; ?>" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            <?php endfor; ?>
                        </div>
                        <div class="flex-1 bg-white rounded-full h-2">
                            <div class="bg-celya-orange_dark h-2 rounded-full transition-all duration-500" style="width: <?php echo esc_attr( $percentage ); ?>%"></div>
                        </div>
                        <span class="text-xs text-celya-dark w-8 text-right"><?php echo esc_html( $percentage ); ?>%</span>
                    </div>
                <?php endfor; ?>
            </div>

            <!-- Barres de distribution -->
            <div class="space-y-3 md:col-span-4">
                <?php if ( ! empty( $all_comments ) ) :
                    $excerpt = wp_trim_words( $all_comments[0]->comment_content, 30, '...' );
                    ?>
                    <p class="text-xl text-celya-dark mb-1">Résumé des avis :</p>
                    <p class="text-sm text-celya-dark italic mt-2">"<?php echo esc_html( $excerpt ); ?>"</p>
                <?php endif; ?>

                <a href="#review_form_wrapper" class="inline-block btn-celya-orange-dark text-sm">
                    Donner votre avis
                </a>
            </div>

        </div>
    </div>

    <!-- En-tête liste des avis -->
    <div class="flex items-center justify-between mb-8">
        <h2 class="text-3xl text-celya-primary">Les derniers avis</h2>
        <a href="#review_form_wrapper" class="flex items-center gap-2 text-sm font-semibold text-celya-dark hover:text-celya-orange_dark transition-colors">
            Voir tous les avis
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </a>
    </div>

    <!-- Grille des 6 derniers avis -->
    <?php if ( ! empty( $all_comments ) ) :
        $comments_to_show = array_slice( $all_comments, 0, 9 );
    ?>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
        <?php foreach ( $comments_to_show as $comment ) :
            $rating      = intval( get_comment_meta( $comment->comment_ID, 'rating', true ) );
            $author_name = $comment->comment_author;
            $content     = $comment->comment_content;
            $date        = date_i18n( get_option( 'date_format' ), strtotime( $comment->comment_date ) );
            $verified    = wc_review_is_from_verified_owner( $comment->comment_ID );
        ?>
        <div class="bg-white rounded-celya-m border border-celya-light p-6 shadow-sm hover:shadow-md hover:border-celya-orange_light transition-all">
            <div class="flex items-start justify-between mb-3">
                <div>
                    <p class="font-semibold text-sm text-celya-orange_dark leading-tight"><?php echo esc_html( $author_name ); ?></p>
                    <p class="text-xs text-gray-400 mt-0.5"><?php echo esc_html( $date ); ?></p>
                </div>
                <?php if ( $verified ) : ?>
                    <span class="inline-block bg-celya-orange_dark text-white text-xs px-2 py-0.5 rounded-full font-semibold flex-shrink-0">Pro</span>
                <?php endif; ?>
            </div>
            <?php if ( $rating ) : ?>
            <div class="flex items-center gap-0.5 mb-3">
                <?php for ( $s = 1; $s <= 5; $s++ ) : ?>
                    <svg class="w-4 h-4 <?php echo $s <= $rating ? 'text-celya-orange_dark' : 'text-gray-300'; ?>" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                <?php endfor; ?>
            </div>
            <?php endif; ?>
            <p class="text-sm text-celya-dark leading-relaxed line-clamp-4"><?php echo esc_html( $content ); ?></p>
        </div>
        <?php endforeach; ?>
    </div>
    <?php else : ?>
        <p class="text-center text-celya-dark py-8 text-sm">Il n'y a pas encore d'avis. Soyez le premier !</p>
    <?php endif; ?>

    <?php endif; // fin if $rating_count > 0 ?>

    <!-- Formulaire de dépôt d'avis -->
    <?php if ( get_option( 'woocommerce_review_rating_verification_required' ) === 'no' || wc_customer_bought_product( '', get_current_user_id(), $product->get_id() ) ) : ?>
    <div id="review_form_wrapper" class="mt-4">
        <div class="bg-celya-light rounded-celya-m p-8">
            <?php
            $commenter = wp_get_current_commenter();

            $comment_form = array(
                'title_reply'        => have_comments()
                    ? esc_html__( 'Donner votre avis', 'woocommerce' )
                    : sprintf( esc_html__( 'Soyez le premier à laisser votre avis sur "%s"', 'woocommerce' ), get_the_title() ),
                'title_reply_before' => '<h3 id="reply-title" class="font-serif text-2xl font-bold text-celya-primary mb-6">',
                'title_reply_after'  => '</h3>',
                'comment_notes_after'=> '',
                'label_submit'       => esc_html__( 'Envoyer', 'woocommerce' ),
                'logged_in_as'       => '',
                'comment_field'      => '',
                'submit_button'      => '<button type="submit" class="bg-celya-orange_dark hover:bg-celya-primary text-white font-semibold py-3 px-8 rounded-lg transition-colors duration-300">%4$s</button>',
            );

            $name_email_required = (bool) get_option( 'require_name_email', 1 );
            $fields = array(
                'author' => array( 'label' => __( 'Nom', 'woocommerce' ),    'type' => 'text',  'value' => $commenter['comment_author'],       'required' => $name_email_required ),
                'email'  => array( 'label' => __( 'E-mail', 'woocommerce' ), 'type' => 'email', 'value' => $commenter['comment_author_email'], 'required' => $name_email_required ),
            );

            $comment_form['fields'] = array();
            foreach ( $fields as $key => $field ) {
                $html  = '<p class="comment-form-' . esc_attr( $key ) . ' mb-4">';
                $html .= '<label for="' . esc_attr( $key ) . '" class="block text-sm font-semibold text-celya-dark mb-2">' . esc_html( $field['label'] );
                if ( $field['required'] ) {
                    $html .= '&nbsp;<span class="required text-red-500">*</span>';
                }
                $html .= '</label>';
                $html .= '<input id="' . esc_attr( $key ) . '" name="' . esc_attr( $key ) . '" type="' . esc_attr( $field['type'] ) . '" value="' . esc_attr( $field['value'] ) . '" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-celya-orange_dark" ' . ( $field['required'] ? 'required' : '' ) . ' /></p>';
                $comment_form['fields'][ $key ] = $html;
            }

            $account_page_url = wc_get_page_permalink( 'myaccount' );
            if ( $account_page_url ) {
                $comment_form['must_log_in'] = '<p class="bg-celya-orange_light border-l-4 border-celya-orange_dark p-4 rounded mb-6 text-sm">' . sprintf( __( 'Vous devez être <a href="%s" class="underline">connecté</a> pour publier un avis.', 'woocommerce' ), esc_url( $account_page_url ) ) . '</p>';
            }

            if ( wc_review_ratings_enabled() ) {
                $comment_form['comment_field']  = '<div class="comment-form-rating mb-4">';
                $comment_form['comment_field'] .= '<label for="rating" class="block text-sm font-semibold text-celya-dark mb-2">' . esc_html__( 'Votre note', 'woocommerce' );
                if ( wc_review_ratings_required() ) {
                    $comment_form['comment_field'] .= '&nbsp;<span class="required text-red-500">*</span>';
                }
                $comment_form['comment_field'] .= '</label>';
                $comment_form['comment_field'] .= '<select name="rating" id="rating" required class="w-auto px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-celya-orange_dark">';
                $comment_form['comment_field'] .= '<option value="">' . esc_html__( 'Choisir une note', 'woocommerce' ) . '</option>';
                $comment_form['comment_field'] .= '<option value="5">' . esc_html__( 'Excellent (5/5)', 'woocommerce' ) . '</option>';
                $comment_form['comment_field'] .= '<option value="4">' . esc_html__( 'Bien (4/5)', 'woocommerce' ) . '</option>';
                $comment_form['comment_field'] .= '<option value="3">' . esc_html__( 'Moyen (3/5)', 'woocommerce' ) . '</option>';
                $comment_form['comment_field'] .= '<option value="2">' . esc_html__( 'Pas terrible (2/5)', 'woocommerce' ) . '</option>';
                $comment_form['comment_field'] .= '<option value="1">' . esc_html__( 'Très mauvais (1/5)', 'woocommerce' ) . '</option>';
                $comment_form['comment_field'] .= '</select></div>';
            }

            $comment_form['comment_field'] .= '<p class="comment-form-comment mb-4"><label for="comment" class="block text-sm font-semibold text-celya-dark mb-2">' . esc_html__( 'Votre avis', 'woocommerce' ) . '&nbsp;<span class="required text-red-500">*</span></label><textarea id="comment" name="comment" cols="45" rows="6" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-celya-orange_dark"></textarea></p>';

            comment_form( apply_filters( 'woocommerce_product_review_comment_form_args', $comment_form ) );
            ?>
        </div>
    </div>
    <?php else : ?>
    <p class="bg-celya-orange_light border-l-4 border-celya-orange_dark p-4 rounded mt-8 text-sm">
        <?php esc_html_e( 'Seuls les clients connectés ayant acheté ce produit ont la possibilité de laisser un avis.', 'woocommerce' ); ?>
    </p>
    <?php endif; ?>

</div><!-- #reviews -->