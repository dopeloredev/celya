<?php
/**
 * Champs personnalisés produits - Backoffice
 *
 * Responsabilités :
 * - Onglet "Spécifications" dans le formulaire produit WooCommerce
 * - Tableaux dynamiques ingrédients + valeurs nutritionnelles
 * - Champs texte allergènes, conservation, conseil dégustation
 * - Support des variations
 * - celya_get_product_specs() utilisée par woocommerce-setup-single-product-tabs.php
 *
 * @package Celya
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


// =============================================================================
// 1. COMPATIBILITÉ ANCIEN FORMAT (migration texte → JSON)
// =============================================================================

/**
 * Récupère les ingrédients en gérant le fallback entre l'ancien format texte
 * et le nouveau format JSON.
 *
 * @param  int   $product_id
 * @return array
 */
function celya_get_legacy_ingredients( $product_id ) {
    $raw = get_post_meta( $product_id, '_celya_ingredients', true );

    if ( ! $raw ) {
        return array();
    }

    $decoded = json_decode( $raw, true );
    if ( json_last_error() === JSON_ERROR_NONE && is_array( $decoded ) ) {
        return $decoded;
    }

    // Ancien format texte brut → on encapsule dans une ligne
    return array(
        array( 'ingredient' => $raw, 'quantite' => '' ),
    );
}


// =============================================================================
// 2. RÉCUPÉRATION DES SPECS (utilisée aussi en front par le fichier tabs)
// =============================================================================

/**
 * Récupère toutes les spécifications d'un produit.
 * Pour une variation, remonte au parent si un champ est vide.
 *
 * @param  WC_Product $product
 * @return array {
 *     @type array  $ingredients  Tableau de ['ingredient', 'quantite']
 *     @type array  $nutrition    Tableau de ['nutriment', 'valeur', 'unite']
 *     @type string $allergenes
 *     @type string $conservation
 *     @type string $degustation
 * }
 */
function celya_get_product_specs( $product ) {
    $id        = $product->get_id();
    $parent_id = $product->is_type( 'variation' ) ? $product->get_parent_id() : null;

    // Récupère une meta, avec fallback parent pour les variations
    $get = function( $key ) use ( $id, $parent_id ) {
        $val = get_post_meta( $id, $key, true );
        if ( ( ! $val || $val === '[]' ) && $parent_id ) {
            $val = get_post_meta( $parent_id, $key, true );
        }
        return $val;
    };

    $ingredients_raw = $get( '_celya_ingredients' );
    $nutrition_raw   = $get( '_celya_nutrition_table' );

    // Décode les ingrédients (gère l'ancien format texte)
    if ( $ingredients_raw ) {
        $decoded = json_decode( $ingredients_raw, true );
        $ingredients = ( json_last_error() === JSON_ERROR_NONE && is_array( $decoded ) )
            ? $decoded
            : array( array( 'ingredient' => $ingredients_raw, 'quantite' => '' ) );
    } else {
        $ingredients = array();
    }

    return array(
        'ingredients'  => $ingredients,
        'nutrition'    => $nutrition_raw ? json_decode( $nutrition_raw, true ) : array(),
        // Fallback sur l'ancien champ _celya_allergens (sans accent)
        'allergenes'   => $get( '_celya_allergenes' ) ?: $get( '_celya_allergens' ),
        'conservation' => $get( '_celya_conservation' ),
        'degustation'  => $get( '_celya_conseil_degustation' ),
    );
}


// =============================================================================
// 3. ONGLET "SPÉCIFICATIONS" DANS LE BACKOFFICE
// =============================================================================

/**
 * Enregistre l'onglet "Spécifications" dans le formulaire produit.
 */
function celya_add_specifications_tab( $tabs ) {
    $tabs['celya_specifications'] = array(
        'label'    => __( 'Spécifications', 'celya' ),
        'target'   => 'celya_specifications_data',
        'class'    => array(),
        'priority' => 60,
    );
    return $tabs;
}
add_filter( 'woocommerce_product_data_tabs', 'celya_add_specifications_tab' );


// =============================================================================
// 4. CONTENU DU PANNEAU "SPÉCIFICATIONS"
// =============================================================================

/**
 * Affiche le contenu du panneau dans l'onglet Spécifications.
 */
function celya_specifications_tab_content() {
    global $post;
    $product_id = $post->ID;

    // Ingrédients
    $ingredients_rows = celya_get_legacy_ingredients( $product_id );
    if ( empty( $ingredients_rows ) ) {
        $ingredients_rows = array( array( 'ingredient' => '', 'quantite' => '' ) );
    }

    // Nutrition
    $nutrition_raw  = get_post_meta( $product_id, '_celya_nutrition_table', true );
    $nutrition_rows = ( $nutrition_raw ) ? json_decode( $nutrition_raw, true ) : array();
    if ( empty( $nutrition_rows ) ) {
        $nutrition_rows = array( array( 'nutriment' => '', 'valeur' => '', 'unite' => 'g' ) );
    }

    // Champs texte (fallback ancien champ allergens)
    $allergenes   = get_post_meta( $product_id, '_celya_allergenes', true )
                 ?: get_post_meta( $product_id, '_celya_allergens', true );
    $conservation = get_post_meta( $product_id, '_celya_conservation', true );
    $degustation  = get_post_meta( $product_id, '_celya_conseil_degustation', true );

    $units = array( 'g', 'mg', 'µg', 'kcal', 'kJ', 'ml', '%' );
    ?>

    <div id="celya_specifications_data" class="panel woocommerce_options_panel">

        <!-- ==================== INGRÉDIENTS ==================== -->
        <div class="options_group">
            <p class="form-field form-field-wide">
                <strong><?php esc_html_e( 'Ingrédients', 'celya' ); ?></strong>
            </p>
            <table class="celya-spec-table widefat" id="celya_ingredients_table">
                <thead>
                    <tr>
                        <th><?php esc_html_e( 'Ingrédient', 'celya' ); ?></th>
                        <th><?php esc_html_e( 'Quantité / précision (optionnel)', 'celya' ); ?></th>
                        <th width="44"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ( $ingredients_rows as $row ) : ?>
                    <tr>
                        <td><input type="text" name="_celya_ingredients_rows[][ingredient]" value="<?php echo esc_attr( $row['ingredient'] ?? '' ); ?>" class="short" /></td>
                        <td><input type="text" name="_celya_ingredients_rows[][quantite]"   value="<?php echo esc_attr( $row['quantite']   ?? '' ); ?>" class="short" /></td>
                        <td><button type="button" class="button celya-remove-row">✕</button></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <p>
                <button type="button" class="button celya-add-row" data-table="celya_ingredients_table" data-template="ingredient">
                    <?php esc_html_e( '+ Ajouter un ingrédient', 'celya' ); ?>
                </button>
            </p>
        </div>

        <!-- ==================== VALEURS NUTRITIONNELLES ==================== -->
        <div class="options_group">
            <p class="form-field form-field-wide">
                <strong><?php esc_html_e( 'Valeurs nutritionnelles (pour 100g)', 'celya' ); ?></strong>
            </p>
            <table class="celya-spec-table widefat" id="celya_nutrition_table">
                <thead>
                    <tr>
                        <th><?php esc_html_e( 'Nutriment', 'celya' ); ?></th>
                        <th><?php esc_html_e( 'Valeur', 'celya' ); ?></th>
                        <th><?php esc_html_e( 'Unité', 'celya' ); ?></th>
                        <th width="44"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ( $nutrition_rows as $row ) : ?>
                    <tr>
                        <td><input type="text"   name="_celya_nutrition_rows[][nutriment]" value="<?php echo esc_attr( $row['nutriment'] ?? '' ); ?>" class="short" /></td>
                        <td><input type="number" name="_celya_nutrition_rows[][valeur]"    value="<?php echo esc_attr( $row['valeur']    ?? '' ); ?>" class="short" step="0.01" min="0" /></td>
                        <td>
                            <select name="_celya_nutrition_rows[][unite]">
                                <?php foreach ( $units as $unit ) : ?>
                                    <option value="<?php echo esc_attr( $unit ); ?>" <?php selected( $row['unite'] ?? 'g', $unit ); ?>><?php echo esc_html( $unit ); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <td><button type="button" class="button celya-remove-row">✕</button></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <p>
                <button type="button" class="button celya-add-row" data-table="celya_nutrition_table" data-template="nutrition">
                    <?php esc_html_e( '+ Ajouter un nutriment', 'celya' ); ?>
                </button>
            </p>
        </div>

        <!-- ==================== CHAMPS TEXTE ==================== -->
        <div class="options_group">
            <?php
            woocommerce_wp_textarea_input( array(
                'id'          => '_celya_allergenes',
                'label'       => __( 'Allergènes', 'celya' ),
                'value'       => $allergenes,
                'placeholder' => 'Ex : Contient gluten, lait, fruits à coques...',
                'desc_tip'    => true,
                'description' => __( 'Liste des allergènes présents dans le produit', 'celya' ),
                'rows'        => 3,
            ));
            woocommerce_wp_textarea_input( array(
                'id'          => '_celya_conservation',
                'label'       => __( 'Conservation', 'celya' ),
                'value'       => $conservation,
                'placeholder' => 'À conserver dans un endroit sec et à l\'abri de la lumière...',
                'desc_tip'    => true,
                'description' => __( 'Instructions de conservation du produit', 'celya' ),
                'rows'        => 3,
            ));
            woocommerce_wp_textarea_input( array(
                'id'          => '_celya_conseil_degustation',
                'label'       => __( 'Conseil dégustation', 'celya' ),
                'value'       => $degustation,
                'placeholder' => 'À déguster avec un verre de vin blanc...',
                'desc_tip'    => true,
                'description' => __( 'Conseils pour déguster le produit', 'celya' ),
                'rows'        => 3,
            ));
            ?>
        </div>

    </div>
    <?php
}
add_action( 'woocommerce_product_data_panels', 'celya_specifications_tab_content' );


// =============================================================================
// 5. SAUVEGARDE DES CHAMPS (PRODUIT SIMPLE)
// =============================================================================

/**
 * Sauvegarde les champs de l'onglet Spécifications.
 *
 * @param int $post_id
 */
function celya_save_specifications_fields( $post_id ) {

    // Ingrédients → JSON
    if ( isset( $_POST['_celya_ingredients_rows'] ) && is_array( $_POST['_celya_ingredients_rows'] ) ) {
        $rows = array_values( array_filter(
            array_map( function( $r ) {
                return array(
                    'ingredient' => sanitize_text_field( $r['ingredient'] ?? '' ),
                    'quantite'   => sanitize_text_field( $r['quantite']   ?? '' ),
                );
            }, $_POST['_celya_ingredients_rows'] ),
            fn( $r ) => ! empty( trim( $r['ingredient'] ) )
        ));
        update_post_meta( $post_id, '_celya_ingredients', wp_json_encode( $rows ) );
    }

    // Nutrition → JSON
    if ( isset( $_POST['_celya_nutrition_rows'] ) && is_array( $_POST['_celya_nutrition_rows'] ) ) {
        $rows = array_values( array_filter(
            array_map( function( $r ) {
                return array(
                    'nutriment' => sanitize_text_field( $r['nutriment'] ?? '' ),
                    'valeur'    => floatval( $r['valeur'] ?? 0 ),
                    'unite'     => sanitize_text_field( $r['unite']    ?? 'g' ),
                );
            }, $_POST['_celya_nutrition_rows'] ),
            fn( $r ) => ! empty( trim( $r['nutriment'] ) )
        ));
        update_post_meta( $post_id, '_celya_nutrition_table', wp_json_encode( $rows ) );
    }

    // Champs texte
    foreach ( array( '_celya_allergenes', '_celya_conservation', '_celya_conseil_degustation' ) as $field ) {
        if ( isset( $_POST[ $field ] ) ) {
            update_post_meta( $post_id, $field, sanitize_textarea_field( wp_unslash( $_POST[ $field ] ) ) );
        }
    }
}
add_action( 'woocommerce_process_product_meta', 'celya_save_specifications_fields' );


// =============================================================================
// 6. CHAMPS DANS LES VARIATIONS
// =============================================================================

/**
 * Affiche les champs Spécifications dans chaque variation.
 *
 * @param int     $loop
 * @param array   $variation_data
 * @param WP_Post $variation
 */
function celya_add_variation_specifications( $loop, $variation_data, $variation ) {
    $variation_id = $variation->ID;

    $ingredients_rows = celya_get_legacy_ingredients( $variation_id );
    if ( empty( $ingredients_rows ) ) {
        $ingredients_rows = array( array( 'ingredient' => '', 'quantite' => '' ) );
    }

    $nutrition_raw  = get_post_meta( $variation_id, '_celya_nutrition_table', true );
    $nutrition_rows = $nutrition_raw ? json_decode( $nutrition_raw, true ) : array();
    if ( empty( $nutrition_rows ) ) {
        $nutrition_rows = array( array( 'nutriment' => '', 'valeur' => '', 'unite' => 'g' ) );
    }

    $allergenes   = get_post_meta( $variation_id, '_celya_allergenes', true );
    $conservation = get_post_meta( $variation_id, '_celya_conservation', true );
    $degustation  = get_post_meta( $variation_id, '_celya_conseil_degustation', true );

    $units        = array( 'g', 'mg', 'µg', 'kcal', 'kJ', 'ml', '%' );
    $ing_table_id = 'celya_var_ing_' . $loop;
    $nut_table_id = 'celya_var_nut_' . $loop;
    ?>

    <div class="celya-variation-specs" style="padding:12px 12px 0; border-top:1px solid #eee; width:100%; box-sizing:border-box;">

        <p style="margin:0 0 4px; font-weight:600; color:#23282d; font-size:13px;">
            📋 <?php esc_html_e( 'Spécifications de cette variation', 'celya' ); ?>
        </p>
        <p style="margin:0 0 12px; font-size:11px; color:#777;">
            <?php esc_html_e( 'Laissez vide pour hériter du produit parent.', 'celya' ); ?>
        </p>

        <!-- Ingrédients -->
        <p style="font-weight:600; margin:0 0 6px; font-size:12px;"><?php esc_html_e( 'Ingrédients', 'celya' ); ?></p>
        <table class="celya-spec-table widefat" id="<?php echo esc_attr( $ing_table_id ); ?>">
            <thead><tr>
                <th><?php esc_html_e( 'Ingrédient', 'celya' ); ?></th>
                <th><?php esc_html_e( 'Quantité', 'celya' ); ?></th>
                <th width="40"></th>
            </tr></thead>
            <tbody>
                <?php foreach ( $ingredients_rows as $row ) : ?>
                <tr>
                    <td><input type="text" name="celya_var_ing[<?php echo (int) $loop; ?>][][ingredient]" value="<?php echo esc_attr( $row['ingredient'] ?? '' ); ?>" /></td>
                    <td><input type="text" name="celya_var_ing[<?php echo (int) $loop; ?>][][quantite]"   value="<?php echo esc_attr( $row['quantite']   ?? '' ); ?>" /></td>
                    <td><button type="button" class="button celya-remove-row">✕</button></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <p>
            <button type="button" class="button celya-add-row" data-table="<?php echo esc_attr( $ing_table_id ); ?>" data-template="ingredient" data-loop="<?php echo (int) $loop; ?>">
                <?php esc_html_e( '+ Ingrédient', 'celya' ); ?>
            </button>
        </p>

        <!-- Nutrition -->
        <p style="font-weight:600; margin:12px 0 6px; font-size:12px;"><?php esc_html_e( 'Valeurs nutritionnelles', 'celya' ); ?></p>
        <table class="celya-spec-table widefat" id="<?php echo esc_attr( $nut_table_id ); ?>">
            <thead><tr>
                <th><?php esc_html_e( 'Nutriment', 'celya' ); ?></th>
                <th><?php esc_html_e( 'Valeur', 'celya' ); ?></th>
                <th><?php esc_html_e( 'Unité', 'celya' ); ?></th>
                <th width="40"></th>
            </tr></thead>
            <tbody>
                <?php foreach ( $nutrition_rows as $row ) : ?>
                <tr>
                    <td><input type="text"   name="celya_var_nut[<?php echo (int) $loop; ?>][][nutriment]" value="<?php echo esc_attr( $row['nutriment'] ?? '' ); ?>" /></td>
                    <td><input type="number" name="celya_var_nut[<?php echo (int) $loop; ?>][][valeur]"    value="<?php echo esc_attr( $row['valeur']    ?? '' ); ?>" step="0.01" min="0" /></td>
                    <td>
                        <select name="celya_var_nut[<?php echo (int) $loop; ?>][][unite]">
                            <?php foreach ( $units as $unit ) : ?>
                                <option value="<?php echo esc_attr( $unit ); ?>" <?php selected( $row['unite'] ?? 'g', $unit ); ?>><?php echo esc_html( $unit ); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <td><button type="button" class="button celya-remove-row">✕</button></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <p>
            <button type="button" class="button celya-add-row" data-table="<?php echo esc_attr( $nut_table_id ); ?>" data-template="nutrition" data-loop="<?php echo (int) $loop; ?>">
                <?php esc_html_e( '+ Nutriment', 'celya' ); ?>
            </button>
        </p>

        <!-- Champs texte -->
        <p style="margin-top:12px;">
            <label style="font-weight:600; display:block; margin-bottom:4px; font-size:12px;"><?php esc_html_e( 'Allergènes', 'celya' ); ?></label>
            <textarea name="celya_var_allergenes[<?php echo (int) $loop; ?>]" rows="2" style="width:100%;"><?php echo esc_textarea( $allergenes ); ?></textarea>
        </p>
        <p>
            <label style="font-weight:600; display:block; margin-bottom:4px; font-size:12px;"><?php esc_html_e( 'Conservation', 'celya' ); ?></label>
            <textarea name="celya_var_conservation[<?php echo (int) $loop; ?>]" rows="2" style="width:100%;"><?php echo esc_textarea( $conservation ); ?></textarea>
        </p>
        <p>
            <label style="font-weight:600; display:block; margin-bottom:4px; font-size:12px;"><?php esc_html_e( 'Conseil dégustation', 'celya' ); ?></label>
            <textarea name="celya_var_degustation[<?php echo (int) $loop; ?>]" rows="2" style="width:100%;"><?php echo esc_textarea( $degustation ); ?></textarea>
        </p>

    </div>
    <?php
}
add_action( 'woocommerce_product_after_variable_attributes', 'celya_add_variation_specifications', 10, 3 );


/**
 * Sauvegarde les champs Spécifications de chaque variation.
 *
 * @param int $variation_id
 * @param int $loop
 */
function celya_save_variation_specifications( $variation_id, $loop ) {

    // Ingrédients
    if ( isset( $_POST['celya_var_ing'][ $loop ] ) && is_array( $_POST['celya_var_ing'][ $loop ] ) ) {
        $rows = array_values( array_filter(
            array_map( function( $r ) {
                return array(
                    'ingredient' => sanitize_text_field( $r['ingredient'] ?? '' ),
                    'quantite'   => sanitize_text_field( $r['quantite']   ?? '' ),
                );
            }, $_POST['celya_var_ing'][ $loop ] ),
            fn( $r ) => ! empty( trim( $r['ingredient'] ) )
        ));
        update_post_meta( $variation_id, '_celya_ingredients', wp_json_encode( $rows ) );
    }

    // Nutrition
    if ( isset( $_POST['celya_var_nut'][ $loop ] ) && is_array( $_POST['celya_var_nut'][ $loop ] ) ) {
        $rows = array_values( array_filter(
            array_map( function( $r ) {
                return array(
                    'nutriment' => sanitize_text_field( $r['nutriment'] ?? '' ),
                    'valeur'    => floatval( $r['valeur'] ?? 0 ),
                    'unite'     => sanitize_text_field( $r['unite']    ?? 'g' ),
                );
            }, $_POST['celya_var_nut'][ $loop ] ),
            fn( $r ) => ! empty( trim( $r['nutriment'] ) )
        ));
        update_post_meta( $variation_id, '_celya_nutrition_table', wp_json_encode( $rows ) );
    }

    // Champs texte
    $map = array(
        'celya_var_allergenes'   => '_celya_allergenes',
        'celya_var_conservation' => '_celya_conservation',
        'celya_var_degustation'  => '_celya_conseil_degustation',
    );
    foreach ( $map as $post_key => $meta_key ) {
        if ( isset( $_POST[ $post_key ][ $loop ] ) ) {
            update_post_meta( $variation_id, $meta_key, sanitize_textarea_field( wp_unslash( $_POST[ $post_key ][ $loop ] ) ) );
        }
    }
}
add_action( 'woocommerce_save_product_variation', 'celya_save_variation_specifications', 10, 2 );


// =============================================================================
// 7. ASSETS ADMIN (CSS + JS — uniquement sur les pages produit)
// =============================================================================

/**
 * Injecte les styles et scripts des tableaux dynamiques.
 *
 * @param string $hook
 */
function celya_admin_specs_assets( $hook ) {
    global $post;

    if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
        return;
    }
    if ( ! $post || $post->post_type !== 'product' ) {
        return;
    }

    // ── CSS ──────────────────────────────────────────────────────────────────
    // On cible wp-admin qui est toujours chargé, sans dépendre d'un handle WC
    wp_add_inline_style( 'wp-admin', '
        .celya-spec-table { border-collapse: collapse; margin-bottom: 10px; width: 100%; }
        .celya-spec-table th,
        .celya-spec-table td { padding: 6px 8px; border: 1px solid #ddd; vertical-align: middle; }
        .celya-spec-table thead th { background: #f9f9f9; font-weight: 600; font-size: 12px; }
        .celya-spec-table input[type="text"],
        .celya-spec-table input[type="number"],
        .celya-spec-table select { width: 100%; margin: 0; box-sizing: border-box; }
        .celya-spec-table .button { padding: 2px 8px; font-size: 12px; line-height: 1.8; }
        #celya_specifications_data .options_group { padding: 10px 20px; }
        .celya-variation-specs .celya-spec-table th,
        .celya-variation-specs .celya-spec-table td { padding: 4px 6px; }
    ' );

    // ── JS ───────────────────────────────────────────────────────────────────
    // On s'accroche à jquery qui est toujours présent dans l'admin WP
    wp_add_inline_script( 'jquery', '
    jQuery(function($) {

        var units = ["g","mg","µg","kcal","kJ","ml","%"];

        function makeOptions() {
            return units.map(function(u) {
                return "<option value=\'" + u + "\'>" + u + "<\/option>";
            }).join("");
        }

        function rowIngredient(base) {
            return "<tr>" +
                "<td><input type=\'text\' name=\'" + base + "[][ingredient]\' \/><\/td>" +
                "<td><input type=\'text\' name=\'" + base + "[][quantite]\' \/><\/td>" +
                "<td><button type=\'button\' class=\'button celya-remove-row\'>✕<\/button><\/td>" +
            "<\/tr>";
        }

        function rowNutrition(base) {
            return "<tr>" +
                "<td><input type=\'text\'   name=\'" + base + "[][nutriment]\' \/><\/td>" +
                "<td><input type=\'number\' name=\'" + base + "[][valeur]\' step=\'0.01\' min=\'0\' \/><\/td>" +
                "<td><select name=\'" + base + "[][unite]\'>" + makeOptions() + "<\/select><\/td>" +
                "<td><button type=\'button\' class=\'button celya-remove-row\'>✕<\/button><\/td>" +
            "<\/tr>";
        }

        function getBaseName($table, tmpl, loop) {
            var first = $table.find("tbody input:first");
            if (first.length) {
                return first.attr("name")
                    .replace(/\[\]\[(ingredient|quantite|nutriment|valeur|unite)\]$/, "")
                    .replace(/\[\]$/, "");
            }
            // Fallback construction manuelle
            if (loop !== "" && loop !== undefined) {
                return tmpl === "ingredient"
                    ? "celya_var_ing[" + loop + "]"
                    : "celya_var_nut[" + loop + "]";
            }
            return tmpl === "ingredient"
                ? "_celya_ingredients_rows"
                : "_celya_nutrition_rows";
        }

        $(document).on("click", ".celya-add-row", function() {
            var $btn   = $(this);
            var tmpl   = $btn.data("template");
            var loop   = $btn.data("loop");
            var $table = $("#" + $btn.data("table"));
            var base   = getBaseName($table, tmpl, loop);

            $table.find("tbody").append(
                tmpl === "ingredient" ? rowIngredient(base) : rowNutrition(base)
            );
        });

        $(document).on("click", ".celya-remove-row", function() {
            var $tbody = $(this).closest("tbody");
            if ($tbody.find("tr").length > 1) {
                $(this).closest("tr").remove();
            } else {
                $(this).closest("tr").find("input, select").val("");
            }
        });

    });
    ' );
}
add_action( 'admin_enqueue_scripts', 'celya_admin_specs_assets' );
