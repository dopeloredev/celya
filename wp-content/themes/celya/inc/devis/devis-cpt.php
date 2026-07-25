<?php
/**
 * Devis — Custom Post Type & Taxonomies
 *
 * Enregistre le CPT « demande_devis » et les taxonomies qui servent de listes
 * déroulantes gérables depuis le back-office (secteur, conditionnement, type de
 * projet, statut de traitement).
 *
 * @package Celya
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Enregistre le CPT « demande_devis ».
 *
 * Non public : les demandes ne sont jamais affichées côté front, uniquement
 * gérées dans l'admin. La création se fait via le formulaire, pas via l'éditeur.
 */
function celya_devis_register_cpt() {

    $labels = array(
        'name'               => __( 'Devis', 'celya-tailwind' ),
        'singular_name'      => __( 'Devis', 'celya-tailwind' ),
        'menu_name'          => __( 'Devis', 'celya-tailwind' ),
        'all_items'          => __( 'Tous les devis', 'celya-tailwind' ),
        'view_item'          => __( 'Voir le devis', 'celya-tailwind' ),
        'search_items'       => __( 'Rechercher un devis', 'celya-tailwind' ),
        'not_found'          => __( 'Aucun devis', 'celya-tailwind' ),
        'not_found_in_trash' => __( 'Aucun devis dans la corbeille', 'celya-tailwind' ),
    );

    register_post_type(
        'demande_devis',
        array(
            'labels'             => $labels,
            'public'             => false,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'show_in_rest'       => false,
            'menu_icon'          => 'dashicons-media-document',
            'menu_position'      => 26,
            'capability_type'    => 'post',
            'map_meta_cap'       => true,
            'capabilities'       => array(
                // On interdit la création manuelle : les demandes viennent du formulaire.
                'create_posts' => 'do_not_allow',
            ),
            'supports'           => array( 'title' ),
            'has_archive'        => false,
            'rewrite'            => false,
            'query_var'          => false,
            'exclude_from_search'=> true,
        )
    );
}
add_action( 'init', 'celya_devis_register_cpt' );

/**
 * Retourne la définition des taxonomies « listes déroulantes » du devis.
 *
 * @return array<string,array>
 */
function celya_devis_get_taxonomies() {
    return array(
        'devis_secteur'        => array(
            'singular' => __( 'Secteur d\'activité', 'celya-tailwind' ),
            'plural'   => __( 'Secteurs d\'activité', 'celya-tailwind' ),
            'defaults' => array( 'Restauration', 'Hôtellerie', 'Épicerie fine', 'Événementiel', 'Entreprise', 'Autre' ),
            'icons'    => false,
        ),
        'devis_conditionnement' => array(
            'singular' => __( 'Conditionnement', 'celya-tailwind' ),
            'plural'   => __( 'Conditionnements', 'celya-tailwind' ),
            'defaults' => array( 'Vrac', 'Sachet individuel', 'Boîte', 'Coffret cadeau' ),
            'icons'    => false,
        ),
        'devis_type_projet'     => array(
            'singular' => __( 'Type de projet', 'celya-tailwind' ),
            'plural'   => __( 'Types de projet', 'celya-tailwind' ),
            'defaults' => array( 'Revente', 'Cadeaux', 'Événement', 'Autre' ),
            'icons'    => true,
        ),
    );
}

/**
 * Enregistre toutes les taxonomies du devis (rattachées au CPT).
 */
function celya_devis_register_taxonomies() {

    foreach ( celya_devis_get_taxonomies() as $taxonomy => $args ) {

        register_taxonomy(
            $taxonomy,
            'demande_devis',
            array(
                'labels'             => array(
                    'name'          => $args['plural'],
                    'singular_name' => $args['singular'],
                    'menu_name'     => $args['plural'],
                    'all_items'     => sprintf( __( 'Tous les %s', 'celya-tailwind' ), strtolower( $args['plural'] ) ),
                    'add_new_item'  => sprintf( __( 'Ajouter : %s', 'celya-tailwind' ), strtolower( $args['singular'] ) ),
                    'new_item_name' => sprintf( __( 'Nom du %s', 'celya-tailwind' ), strtolower( $args['singular'] ) ),
                ),
                'public'             => false,
                'show_ui'            => true,
                'show_in_menu'       => true,
                'show_admin_column'  => true,
                'show_in_rest'       => false,
                'hierarchical'       => true, // Affiche une table d'options claire (add/edit/delete).
                'query_var'          => false,
                'rewrite'            => false,
            )
        );
    }
}
add_action( 'init', 'celya_devis_register_taxonomies' );

/**
 * Insère les options par défaut une seule fois (au changement de thème).
 */
function celya_devis_seed_default_terms() {

    foreach ( celya_devis_get_taxonomies() as $taxonomy => $args ) {

        if ( ! taxonomy_exists( $taxonomy ) ) {
            // S'assure que la taxonomie est connue avant d'insérer des termes.
            celya_devis_register_taxonomies();
        }

        foreach ( $args['defaults'] as $term_name ) {
            if ( ! term_exists( $term_name, $taxonomy ) ) {
                wp_insert_term( $term_name, $taxonomy );
            }
        }
    }
}
add_action( 'after_switch_theme', 'celya_devis_seed_default_terms' );

/**
 * Garde-fou : seed une seule fois même si le thème est déjà actif.
 */
function celya_devis_maybe_seed_default_terms() {
    if ( get_option( 'celya_devis_seeded' ) ) {
        return;
    }
    celya_devis_seed_default_terms();
    update_option( 'celya_devis_seeded', 1 );
}
add_action( 'admin_init', 'celya_devis_maybe_seed_default_terms' );

/*
 * -------------------------------------------------------------------------
 *  Champ « icône » pour les termes du type de projet (term meta)
 * -------------------------------------------------------------------------
 */

/**
 * Les deux versions d'icône gérées par type de projet.
 *
 * @return array<string,array> clé meta => [ label, description ].
 */
function celya_devis_icon_fields() {
    return array(
        'celya_devis_icon'       => array(
            'label'       => __( 'Icône — couleur', 'celya-tailwind' ),
            'description' => __( 'Version affichée par défaut (orange). PNG ou SVG.', 'celya-tailwind' ),
        ),
        'celya_devis_icon_white' => array(
            'label'       => __( 'Icône — blanc', 'celya-tailwind' ),
            'description' => __( 'Version affichée quand le type de projet est sélectionné. PNG ou SVG.', 'celya-tailwind' ),
        ),
    );
}

/**
 * Rend un contrôle d'upload d'icône (réutilisé add/edit).
 *
 * @param string $field   Clé meta.
 * @param array  $config  [ label, description ].
 * @param int    $value   ID de la pièce jointe.
 * @param string $context 'add' (div.form-field) ou 'edit' (tr).
 */
function celya_devis_render_icon_control( $field, $config, $value, $context ) {
    $value = (int) $value;
    $url   = celya_devis_get_icon_url( $value );

    ob_start();
    ?>
    <input type="hidden" class="celya-devis-icon-input" name="<?php echo esc_attr( $field ); ?>" value="<?php echo esc_attr( $value ); ?>" />
    <p class="celya-devis-icon-preview">
        <?php if ( $url ) : ?>
            <img src="<?php echo esc_url( $url ); ?>" alt="" style="max-width:64px;height:auto;" />
        <?php endif; ?>
    </p>
    <button type="button" class="button celya-devis-icon-upload"><?php esc_html_e( 'Choisir une icône', 'celya-tailwind' ); ?></button>
    <button type="button" class="button celya-devis-icon-remove" <?php echo $value ? '' : 'style="display:none;"'; ?>><?php esc_html_e( 'Retirer', 'celya-tailwind' ); ?></button>
    <p class="description"><?php echo esc_html( $config['description'] ); ?></p>
    <?php
    $inner = ob_get_clean();

    if ( 'add' === $context ) {
        printf(
            '<div class="form-field celya-devis-icon-field"><label>%s</label>%s</div>',
            esc_html( $config['label'] ),
            $inner
        );
    } else {
        printf(
            '<tr class="form-field celya-devis-icon-field"><th scope="row"><label>%s</label></th><td>%s</td></tr>',
            esc_html( $config['label'] ),
            $inner
        );
    }
}

/**
 * Champs icônes sur l'écran « Ajouter un type de projet ».
 */
function celya_devis_type_projet_add_icon_field() {
    wp_enqueue_media();
    foreach ( celya_devis_icon_fields() as $field => $config ) {
        celya_devis_render_icon_control( $field, $config, 0, 'add' );
    }
    celya_devis_print_icon_uploader_js();
}
add_action( 'devis_type_projet_add_form_fields', 'celya_devis_type_projet_add_icon_field' );

/**
 * Champs icônes sur l'écran « Modifier un type de projet ».
 *
 * @param WP_Term $term Terme en cours d'édition.
 */
function celya_devis_type_projet_edit_icon_field( $term ) {
    wp_enqueue_media();
    foreach ( celya_devis_icon_fields() as $field => $config ) {
        $value = (int) get_term_meta( $term->term_id, $field, true );
        celya_devis_render_icon_control( $field, $config, $value, 'edit' );
    }
    celya_devis_print_icon_uploader_js();
}
add_action( 'devis_type_projet_edit_form_fields', 'celya_devis_type_projet_edit_icon_field' );

/**
 * Script du media uploader pour les champs icône (imprimé une fois par écran).
 */
function celya_devis_print_icon_uploader_js() {
    static $printed = false;
    if ( $printed ) {
        return;
    }
    $printed = true;
    ?>
    <script>
    ( function ( $ ) {
        $( document ).on( 'click', '.celya-devis-icon-upload', function ( e ) {
            e.preventDefault();
            var $wrap = $( this ).closest( '.celya-devis-icon-field' );
            var frame = wp.media( { title: 'Choisir une icône', multiple: false, library: { type: 'image' } } );
            frame.on( 'select', function () {
                var att = frame.state().get( 'selection' ).first().toJSON();
                $wrap.find( '.celya-devis-icon-input' ).val( att.id );
                var url = ( att.sizes && att.sizes.thumbnail ) ? att.sizes.thumbnail.url : att.url;
                $wrap.find( '.celya-devis-icon-preview' ).html( '<img src="' + url + '" alt="" style="max-width:64px;height:auto;" />' );
                $wrap.find( '.celya-devis-icon-remove' ).show();
            } );
            frame.open();
        } );
        $( document ).on( 'click', '.celya-devis-icon-remove', function ( e ) {
            e.preventDefault();
            var $wrap = $( this ).closest( '.celya-devis-icon-field' );
            $wrap.find( '.celya-devis-icon-input' ).val( '' );
            $wrap.find( '.celya-devis-icon-preview' ).empty();
            $( this ).hide();
        } );
    } )( jQuery );
    </script>
    <?php
}

/**
 * Sauvegarde les deux versions d'icône du terme « type de projet ».
 *
 * @param int $term_id ID du terme.
 */
function celya_devis_save_type_projet_icon( $term_id ) {
    // Le formulaire d'édition de terme est déjà protégé par le nonce natif de WordPress.
    foreach ( array_keys( celya_devis_icon_fields() ) as $field ) {
        if ( ! isset( $_POST[ $field ] ) ) {
            continue;
        }
        $icon_id = (int) $_POST[ $field ];
        if ( $icon_id > 0 ) {
            update_term_meta( $term_id, $field, $icon_id );
        } else {
            delete_term_meta( $term_id, $field );
        }
    }
}
add_action( 'created_devis_type_projet', 'celya_devis_save_type_projet_icon' );
add_action( 'edited_devis_type_projet', 'celya_devis_save_type_projet_icon' );

/*
 * -------------------------------------------------------------------------
 *  URL d'icône robuste (SVG ou PNG)
 * -------------------------------------------------------------------------
 */

/**
 * Retourne l'URL d'une icône de terme.
 *
 * Les SVG n'ont pas de miniature générée par WordPress : on retombe alors sur
 * l'URL du fichier original.
 *
 * @param int $icon_id ID de la pièce jointe.
 * @return string URL ou chaîne vide.
 */
function celya_devis_get_icon_url( $icon_id ) {
    $icon_id = (int) $icon_id;
    if ( ! $icon_id ) {
        return '';
    }
    $url = wp_get_attachment_image_url( $icon_id, 'thumbnail' );
    if ( ! $url ) {
        $url = wp_get_attachment_url( $icon_id );
    }
    return $url ? $url : '';
}

/*
 * -------------------------------------------------------------------------
 *  Support de l'upload SVG (réservé aux administrateurs, fichier assaini)
 * -------------------------------------------------------------------------
 */

/**
 * Indique si l'utilisateur courant est autorisé à téléverser des SVG.
 *
 * @return bool
 */
function celya_devis_user_can_upload_svg() {
    return current_user_can( 'manage_options' );
}

/**
 * Autorise le type MIME SVG dans la médiathèque (admins uniquement).
 *
 * @param array $mimes Types autorisés.
 * @return array
 */
function celya_devis_allow_svg_mime( $mimes ) {
    if ( celya_devis_user_can_upload_svg() ) {
        $mimes['svg'] = 'image/svg+xml';
    }
    return $mimes;
}
add_filter( 'upload_mimes', 'celya_devis_allow_svg_mime' );

/**
 * Corrige la détection de type de fichier de WordPress pour les SVG.
 *
 * @param array      $data     Données ext/type/proper_filename.
 * @param string     $file     Chemin du fichier.
 * @param string     $filename Nom du fichier.
 * @param array|null $mimes    Types autorisés.
 * @return array
 */
function celya_devis_fix_svg_filetype( $data, $file, $filename, $mimes = null ) {
    if ( ! empty( $data['ext'] ) && ! empty( $data['type'] ) ) {
        return $data;
    }
    $ext = strtolower( pathinfo( $filename, PATHINFO_EXTENSION ) );
    if ( 'svg' === $ext && celya_devis_user_can_upload_svg() ) {
        $data['ext']  = 'svg';
        $data['type'] = 'image/svg+xml';
    }
    return $data;
}
add_filter( 'wp_check_filetype_and_ext', 'celya_devis_fix_svg_filetype', 10, 4 );

/**
 * Assainit un SVG à l'upload : refuse les utilisateurs non autorisés et retire
 * les éléments/attributs dangereux (scripts, gestionnaires d'événements, etc.).
 *
 * @param array $file Données de l'upload ($_FILES).
 * @return array
 */
function celya_devis_sanitize_svg_upload( $file ) {
    if ( empty( $file['type'] ) || 'image/svg+xml' !== $file['type'] ) {
        return $file;
    }

    if ( ! celya_devis_user_can_upload_svg() ) {
        $file['error'] = __( 'Vous n’êtes pas autorisé à téléverser des fichiers SVG.', 'celya-tailwind' );
        return $file;
    }

    $contents = file_get_contents( $file['tmp_name'] );
    $clean    = celya_devis_sanitize_svg_markup( (string) $contents );

    if ( false === $clean ) {
        $file['error'] = __( 'Ce fichier SVG est invalide ou contient du code non autorisé.', 'celya-tailwind' );
        return $file;
    }

    file_put_contents( $file['tmp_name'], $clean );
    return $file;
}
add_filter( 'wp_handle_upload_prefilter', 'celya_devis_sanitize_svg_upload' );

/**
 * Nettoie le balisage d'un SVG (liste noire d'éléments + attributs).
 *
 * @param string $svg Contenu brut du SVG.
 * @return string|false SVG nettoyé, ou false si invalide.
 */
function celya_devis_sanitize_svg_markup( $svg ) {
    $svg = trim( $svg );
    if ( '' === $svg ) {
        return false;
    }

    // Retire un éventuel BOM.
    $svg = preg_replace( '/^\xEF\xBB\xBF/', '', $svg );

    $previous = libxml_use_internal_errors( true );
    $dom      = new DOMDocument();
    // LIBXML_NONET empêche tout accès réseau (protection XXE).
    $loaded   = $dom->loadXML( $svg, LIBXML_NONET );
    libxml_clear_errors();
    libxml_use_internal_errors( $previous );

    if ( ! $loaded || ! $dom->documentElement || 'svg' !== strtolower( $dom->documentElement->nodeName ) ) {
        return false;
    }

    $disallowed_tags = array(
        'script', 'foreignobject', 'iframe', 'embed', 'object',
        'audio', 'video', 'animate', 'animatemotion', 'animatetransform',
        'set', 'handler', 'use',
    );

    // Supprime les éléments interdits.
    $nodes_to_remove = array();
    foreach ( $dom->getElementsByTagName( '*' ) as $node ) {
        if ( in_array( strtolower( $node->nodeName ), $disallowed_tags, true ) ) {
            $nodes_to_remove[] = $node;
        }
    }
    foreach ( $nodes_to_remove as $node ) {
        if ( $node->parentNode ) {
            $node->parentNode->removeChild( $node );
        }
    }

    // Supprime les attributs dangereux (on*, href/xlink:href en javascript:).
    $xpath = new DOMXPath( $dom );
    foreach ( iterator_to_array( $xpath->query( '//@*' ) ) as $attr ) {
        $name  = strtolower( $attr->nodeName );
        $value = preg_replace( '/\s+/', '', strtolower( (string) $attr->nodeValue ) );

        $is_href    = ( 'href' === $name || 'xlink:href' === $name );
        $is_event   = ( 0 === strpos( $name, 'on' ) );
        $is_js_href = $is_href && 0 === strpos( $value, 'javascript:' );

        if ( ( $is_event || $is_js_href ) && $attr->ownerElement ) {
            $attr->ownerElement->removeAttributeNode( $attr );
        }
    }

    return $dom->saveXML( $dom->documentElement );
}

/**
 * Affiche correctement les miniatures SVG dans la médiathèque (admin).
 */
function celya_devis_svg_admin_thumb_css() {
    if ( ! celya_devis_user_can_upload_svg() ) {
        return;
    }
    echo '<style>.media-icon img[src$=".svg"],.attachment-preview img[src$=".svg"]{width:100%;height:auto;}</style>';
}
add_action( 'admin_head', 'celya_devis_svg_admin_thumb_css' );
