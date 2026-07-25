<?php
/**
 * BLOCKS GUTENBERG PERSONNALISÉS
 */

function celya_register_blocks() {
    wp_register_script(
        'celya-icon-card-editor',
        get_template_directory_uri() . '/blocks/icon-card/editor.js',
        [ 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-i18n' ],
        filemtime( get_template_directory() . '/blocks/icon-card/editor.js' ),
        true
    );

    wp_register_style(
        'celya-icon-card-style',
        get_template_directory_uri() . '/blocks/icon-card/style.css',
        [],
        filemtime( get_template_directory() . '/blocks/icon-card/style.css' )
    );

    register_block_type( get_template_directory() . '/blocks/icon-card' );

    wp_register_script(
        'celya-bullet-list-editor',
        get_template_directory_uri() . '/blocks/bullet-list/editor.js',
        [ 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-i18n' ],
        filemtime( get_template_directory() . '/blocks/bullet-list/editor.js' ),
        true
    );

    wp_register_style(
        'celya-bullet-list-style',
        get_template_directory_uri() . '/blocks/bullet-list/style.css',
        [],
        filemtime( get_template_directory() . '/blocks/bullet-list/style.css' )
    );

    register_block_type( get_template_directory() . '/blocks/bullet-list' );

    wp_register_script(
        'celya-steps-cards-editor',
        get_template_directory_uri() . '/blocks/steps-cards/editor.js',
        [ 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-i18n' ],
        filemtime( get_template_directory() . '/blocks/steps-cards/editor.js' ),
        true
    );

    wp_register_script(
        'celya-steps-cards-view',
        get_template_directory_uri() . '/blocks/steps-cards/view.js',
        [],
        filemtime( get_template_directory() . '/blocks/steps-cards/view.js' ),
        true
    );

    wp_register_style(
        'celya-steps-cards-style',
        get_template_directory_uri() . '/blocks/steps-cards/style.css',
        [],
        filemtime( get_template_directory() . '/blocks/steps-cards/style.css' )
    );

    register_block_type( get_template_directory() . '/blocks/steps-cards' );

    /* Le style est partagé avec steps-cards (même handle celya-steps-cards-style) */
    wp_register_script(
        'celya-steps-card-editor',
        get_template_directory_uri() . '/blocks/steps-card/editor.js',
        [ 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-i18n' ],
        filemtime( get_template_directory() . '/blocks/steps-card/editor.js' ),
        true
    );

    register_block_type( get_template_directory() . '/blocks/steps-card' );

    wp_register_script(
        'celya-image-cards-editor',
        get_template_directory_uri() . '/blocks/image-cards/editor.js',
        [ 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-i18n' ],
        filemtime( get_template_directory() . '/blocks/image-cards/editor.js' ),
        true
    );

    wp_register_style(
        'celya-image-cards-style',
        get_template_directory_uri() . '/blocks/image-cards/style.css',
        [],
        filemtime( get_template_directory() . '/blocks/image-cards/style.css' )
    );

    register_block_type( get_template_directory() . '/blocks/image-cards' );

    /* Le style est partagé avec image-cards (même handle celya-image-cards-style) */
    wp_register_script(
        'celya-image-card-editor',
        get_template_directory_uri() . '/blocks/image-card/editor.js',
        [ 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-i18n' ],
        filemtime( get_template_directory() . '/blocks/image-card/editor.js' ),
        true
    );

    register_block_type( get_template_directory() . '/blocks/image-card' );

    wp_register_script(
        'celya-text-icone-editor',
        get_template_directory_uri() . '/blocks/text-icone/editor.js',
        [ 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-i18n' ],
        filemtime( get_template_directory() . '/blocks/text-icone/editor.js' ),
        true
    );

    wp_register_style(
        'celya-text-icone-style',
        get_template_directory_uri() . '/blocks/text-icone/style.css',
        [],
        filemtime( get_template_directory() . '/blocks/text-icone/style.css' )
    );

    register_block_type( get_template_directory() . '/blocks/text-icone' );

    wp_register_script(
        'celya-realisations-editor',
        get_template_directory_uri() . '/blocks/realisations/editor.js',
        [ 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-data', 'wp-server-side-render' ],
        filemtime( get_template_directory() . '/blocks/realisations/editor.js' ),
        true
    );

    wp_register_script(
        'celya-realisations-view',
        get_template_directory_uri() . '/blocks/realisations/view.js',
        [],
        filemtime( get_template_directory() . '/blocks/realisations/view.js' ),
        true
    );

    wp_register_style(
        'celya-realisations-style',
        get_template_directory_uri() . '/blocks/realisations/style.css',
        [],
        filemtime( get_template_directory() . '/blocks/realisations/style.css' )
    );

    register_block_type( get_template_directory() . '/blocks/realisations' );

    wp_register_script(
        'celya-devis-form-editor',
        get_template_directory_uri() . '/blocks/devis-form/editor.js',
        [ 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-i18n', 'wp-server-side-render' ],
        filemtime( get_template_directory() . '/blocks/devis-form/editor.js' ),
        true
    );

    wp_register_script(
        'celya-devis-form-view',
        get_template_directory_uri() . '/blocks/devis-form/view.js',
        [],
        filemtime( get_template_directory() . '/blocks/devis-form/view.js' ),
        true
    );

    wp_register_style(
        'celya-devis-form-style',
        get_template_directory_uri() . '/blocks/devis-form/style.css',
        [],
        filemtime( get_template_directory() . '/blocks/devis-form/style.css' )
    );

    register_block_type( get_template_directory() . '/blocks/devis-form' );
}
add_action( 'init', 'celya_register_blocks' );