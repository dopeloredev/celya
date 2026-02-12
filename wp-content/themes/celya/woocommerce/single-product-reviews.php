<?php
/**
 * Display single product reviews (comments)
 *
 * @package Celya
 * @version 8.6.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

if ( ! comments_open() ) {
    return;
}

$rating_count = $product->get_rating_count();
$review_count = $product->get_review_count();
$average      = $product->get_average_rating();

?>
<div id="reviews" class="woocommerce-Reviews">
    
    <div id="comments" class="mb-12">
        
        <?php if ( have_comments() ) : ?>
            
            <!-- Résumé des avis -->
            <div class="bg-celya-orange_light rounded-celya-m p-8 mb-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    
                    <!-- Note moyenne -->
                    <div class="text-center">
                        <div class="text-6xl font-bold text-celya-primary mb-2">
                            <?php echo number_format( $average, 1, ',', '' ); ?>/5
                        </div>
                        <div class="flex justify-center mb-2">
                            <?php echo wc_get_rating_html( $average, $rating_count ); ?>
                        </div>
                        <p class="text-sm text-celya-dark">
                            Résumé des avis :
                        </p>
                    </div>
                    
                    <!-- Répartition des notes -->
                    <div class="space-y-2">
                        <?php
                        // Calculer la distribution des notes (5 à 1 étoile)
                        $comments = get_comments( array(
                            'post_id' => $product->get_id(),
                            'status' => 'approve',
                            'type' => 'review',
                        ));
                        
                        $distribution = array(
                            5 => 0,
                            4 => 0,
                            3 => 0,
                            2 => 0,
                            1 => 0,
                        );
                        
                        foreach ( $comments as $comment ) {
                            $rating = intval( get_comment_meta( $comment->comment_ID, 'rating', true ) );
                            if ( $rating >= 1 && $rating <= 5 ) {
                                $distribution[$rating]++;
                            }
                        }
                        
                        for ( $i = 5; $i >= 1; $i-- ) :
                            $count = $distribution[$i];
                            $percentage = $review_count > 0 ? round( ( $count / $review_count ) * 100 ) : 0;
                        ?>
                            <div class="flex items-center gap-3">
                                <div class="flex items-center gap-1">
                                    <?php for ( $star = 1; $star <= 5; $star++ ) : ?>
                                        <svg class="w-4 h-4 <?php echo $star <= $i ? 'text-yellow-400' : 'text-gray-300'; ?>" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    <?php endfor; ?>
                                </div>
                                <div class="flex-1 bg-gray-200 rounded-full h-2">
                                    <div class="bg-celya-orange_dark h-2 rounded-full" style="width: <?php echo $percentage; ?>%"></div>
                                </div>
                                <span class="text-sm text-celya-dark w-12"><?php echo $percentage; ?>%</span>
                            </div>
                        <?php endfor; ?>
                    </div>
                    
                </div>
            </div>
            
            <!-- Titre et bouton donner avis -->
            <div class="flex items-center justify-between mb-6">
                <h2 class="font-serif text-2xl font-bold text-celya-primary">
                    Les derniers avis
                </h2>
                <a href="#review_form_wrapper" class="text-sm text-celya-orange_dark hover:text-celya-primary font-semibold flex items-center gap-2">
                    Voir tous les avis
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
            
            <!-- Liste des avis -->
            <ol class="commentlist grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php
                $comments_to_show = array_slice( $comments, 0, 6 ); // Afficher les 6 derniers
                wp_list_comments( 
                    apply_filters( 'woocommerce_product_review_list_args', array( 
                        'callback' => 'woocommerce_comments',
                        'style' => 'ol',
                    )),
                    $comments_to_show
                );
                ?>
            </ol>

        <?php else : ?>
            
            <p class="woocommerce-noreviews text-center text-celya-dark py-8">
                <?php esc_html_e( 'Il n\'y a pas encore d\'avis.', 'woocommerce' ); ?>
            </p>

        <?php endif; ?>
        
    </div>

    <?php if ( get_option( 'woocommerce_review_rating_verification_required' ) === 'no' || wc_customer_bought_product( '', get_current_user_id(), $product->get_id() ) ) : ?>
        
        <!-- Formulaire d'avis -->
        <div id="review_form_wrapper" class="mt-12">
            <div id="review_form" class="bg-celya-light rounded-celya-m p-8">
                <?php
                $commenter    = wp_get_current_commenter();
                $comment_form = array(
                    'title_reply'         => have_comments() ? esc_html__( 'Donner votre avis', 'woocommerce' ) : sprintf( esc_html__( 'Soyez le premier à laisser votre avis sur "%s"', 'woocommerce' ), get_the_title() ),
                    'title_reply_to'      => esc_html__( 'Répondre à %s', 'woocommerce' ),
                    'title_reply_before'  => '<h3 id="reply-title" class="comment-reply-title font-serif text-2xl font-bold text-celya-primary mb-6">',
                    'title_reply_after'   => '</h3>',
                    'comment_notes_after' => '',
                    'label_submit'        => esc_html__( 'Envoyer', 'woocommerce' ),
                    'logged_in_as'        => '',
                    'comment_field'       => '',
                    'class_submit'        => 'btn-celya-orange-dark',
                    'submit_button'       => '<button type="submit" class="bg-celya-orange_dark hover:bg-celya-primary text-white font-semibold py-3 px-8 rounded-lg transition-colors duration-300">%4$s</button>',
                );

                $name_email_required = (bool) get_option( 'require_name_email', 1 );
                $fields              = array(
                    'author' => array(
                        'label'    => __( 'Nom', 'woocommerce' ),
                        'type'     => 'text',
                        'value'    => $commenter['comment_author'],
                        'required' => $name_email_required,
                        'class'    => 'w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-celya-orange_dark',
                    ),
                    'email'  => array(
                        'label'    => __( 'E-mail', 'woocommerce' ),
                        'type'     => 'email',
                        'value'    => $commenter['comment_author_email'],
                        'required' => $name_email_required,
                        'class'    => 'w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-celya-orange_dark',
                    ),
                );

                $comment_form['fields'] = array();

                foreach ( $fields as $key => $field ) {
                    $field_html  = '<p class="comment-form-' . esc_attr( $key ) . ' mb-4">';
                    $field_html .= '<label for="' . esc_attr( $key ) . '" class="block text-sm font-semibold text-celya-dark mb-2">' . esc_html( $field['label'] );

                    if ( $field['required'] ) {
                        $field_html .= '&nbsp;<span class="required text-red-500">*</span>';
                    }

                    $field_html .= '</label><input id="' . esc_attr( $key ) . '" name="' . esc_attr( $key ) . '" type="' . esc_attr( $field['type'] ) . '" value="' . esc_attr( $field['value'] ) . '" class="' . esc_attr( $field['class'] ) . '" ' . ( $field['required'] ? 'required' : '' ) . ' /></p>';

                    $comment_form['fields'][ $key ] = $field_html;
                }

                $account_page_url = wc_get_page_permalink( 'myaccount' );
                if ( $account_page_url ) {
                    $comment_form['must_log_in'] = '<p class="must-log-in bg-celya-orange_light border-l-4 border-celya-orange_dark p-4 rounded mb-6">' . sprintf( __( 'Vous devez être <a href="%s">connecté</a> pour publier un avis.', 'woocommerce' ), esc_url( $account_page_url ) ) . '</p>';
                }

                if ( wc_review_ratings_enabled() ) {
                    $comment_form['comment_field'] = '<div class="comment-form-rating mb-4"><label for="rating" class="block text-sm font-semibold text-celya-dark mb-2">' . esc_html__( 'Votre note', 'woocommerce' ) . ( wc_review_ratings_required() ? '&nbsp;<span class="required text-red-500">*</span>' : '' ) . '</label><select name="rating" id="rating" required class="w-auto px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-celya-orange_dark">
                        <option value="">' . esc_html__( 'Choisir une note', 'woocommerce' ) . '</option>
                        <option value="5">' . esc_html__( 'Excellent', 'woocommerce' ) . '</option>
                        <option value="4">' . esc_html__( 'Bien', 'woocommerce' ) . '</option>
                        <option value="3">' . esc_html__( 'Moyen', 'woocommerce' ) . '</option>
                        <option value="2">' . esc_html__( 'Pas terrible', 'woocommerce' ) . '</option>
                        <option value="1">' . esc_html__( 'Très mauvais', 'woocommerce' ) . '</option>
                    </select></div>';
                }

                $comment_form['comment_field'] .= '<p class="comment-form-comment mb-4"><label for="comment" class="block text-sm font-semibold text-celya-dark mb-2">' . esc_html__( 'Votre avis', 'woocommerce' ) . '&nbsp;<span class="required text-red-500">*</span></label><textarea id="comment" name="comment" cols="45" rows="6" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-celya-orange_dark"></textarea></p>';

                comment_form( apply_filters( 'woocommerce_product_review_comment_form_args', $comment_form ) );
                ?>
            </div>
        </div>

    <?php else : ?>
        
        <p class="woocommerce-verification-required bg-celya-orange_light border-l-4 border-celya-orange_dark p-4 rounded mt-8">
            <?php esc_html_e( 'Seuls les clients connectés ayant acheté ce produit ont la possibilité de laisser un avis.', 'woocommerce' ); ?>
        </p>

    <?php endif; ?>

    <div class="clear"></div>
</div>