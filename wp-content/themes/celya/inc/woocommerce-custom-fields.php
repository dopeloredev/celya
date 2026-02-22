<?php
/**
 * Champs personnalisés produits - Spécifications
 *
 * Gère l'onglet "Spécifications" dans le backoffice WooCommerce,
 * les tableaux d'ingrédients et valeurs nutritionnelles,
 * et le support des variations produit.
 *
 * @package Celya
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


// =============================================================================
// 1. MIGRATION - Compatibilité avec les anciennes meta (_celya_ingredients texte)
// =============================================================================

/**
 * Récupère une meta en gérant le fallback de l'ancien format texte vers le nouveau JSON.
 * Utilisé uniquement pour _celya_ingredients et _celya_allergens (anciens champs).
 */
function celya_get_legacy_ingredients( $product_id ) {
    $raw = get_post_meta( $product_id, '_celya_ingredients', true );
    if ( ! $raw ) {
        return array();
    }
    // Si c'est déjà du JSON valide, on le retourne directement
    $decoded = json_decode( $raw, true );
    if ( json_last_error() === JSON_ERROR_NONE && is_array( $decoded ) ) {
        return $decoded;
    }
    // Sinon, c'est l'ancien format texte : on le convertit en tableau d'une ligne
    return array(
        array( 'ingredient' => $raw, 'quantite' => '' ),
    );
}

// =============================================================================
// 2. ONGLET "SPÉCIFICATIONS" DANS LE BACKOFFICE
// =============================================================================

/**
 * Ajouter l'onglet "Spécifications" dans le backoffice produit
 */
function celya_add_specifications_tab( $tabs ) {
    $tabs['specifications'] = array(
        'label'  => __( 'Spécifications', 'celya' ),
        'target' => 'specifications_product_data',
        'class'  => array(),
        'priority' => 60,
    );
    return $tabs;
}
add_filter( 'woocommerce_product_data_tabs', 'celya_add_specifications_tab' );


// =============================================================================
// 3. CONTENU DE L'ONGLET (PRODUIT SIMPLE)
// =============================================================================

/**
 * Affiche le contenu du panneau "Spécifications".
 */
function celya_specifications_tab_content() {
    global $post;
    $product_id = $post->ID;

    // Récupération des données sauvegardées
    $ingredients_rows = celya_get_legacy_ingredients( $product_id );
    if ( empty( $ingredients_rows ) ) {
        $ingredients_rows = array( array( 'ingredient' => '', 'quantite' => '' ) );
    }

    $nutrition_raw  = get_post_meta( $product_id, '_celya_nutrition_table', true );
    $nutrition_rows = $nutrition_raw ? json_decode( $nutrition_raw, true ) : array();
    if ( empty( $nutrition_rows ) ) {
        $nutrition_rows = array( array( 'nutriment' => '', 'valeur' => '', 'unite' => 'g' ) );
    }

    $allergenes   = get_post_meta( $product_id, '_celya_allergenes', true );
    // Fallback ancien champ _celya_allergens (sans accent, sans 's')
    if ( ! $allergenes ) {
        $allergenes = get_post_meta( $product_id, '_celya_allergens', true );
    }
    $conservation = get_post_meta( $product_id, '_celya_conservation', true );
    $degustation  = get_post_meta( $product_id, '_celya_conseil_degustation', true );

    $units = array( 'g', 'mg', 'kcal', 'ml', '%' );

    ?>
    <div id="celya_specifications_data" class="panel woocommerce_options_panel">

        <!-- ======================== INGRÉDIENTS ======================== -->
        <div class="options_group">
            <p class="form-field form-field-wide">
                <strong><?php esc_html_e( 'Ingrédients', 'celya' ); ?></strong>
            </p>

            <table class="celya-spec-table widefat" id="celya_ingredients_table">
                <thead>
                    <tr>
                        <th><?php esc_html_e( 'Ingrédient', 'celya' ); ?></th>
                        <th><?php esc_html_e( 'Quantité / précision (optionnel)', 'celya' ); ?></th>
                        <th width="40"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ( $ingredients_rows as $row ) : ?>
                    <tr>
                        <td>
                            <input type="text"
                                   name="_celya_ingredients_rows[][ingredient]"
                                   value="<?php echo esc_attr( $row['ingredient'] ?? '' ); ?>"
                                   class="short" />
                        </td>
                        <td>
                            <input type="text"
                                   name="_celya_ingredients_rows[][quantite]"
                                   value="<?php echo esc_attr( $row['quantite'] ?? '' ); ?>"
                                   class="short" />
                        </td>
                        <td>
                            <button type="button" class="button celya-remove-row" title="Supprimer">✕</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <p>
                <button type="button"
                        class="button celya-add-row"
                        data-table="celya_ingredients_table"
                        data-template="ingredient">
                    <?php esc_html_e( '+ Ajouter un ingrédient', 'celya' ); ?>
                </button>
            </p>
        </div>

        <!-- ===================== VALEURS NUTRITIONNELLES ===================== -->
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
                        <th width="40"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ( $nutrition_rows as $row ) : ?>
                    <tr>
                        <td>
                            <input type="text"
                                   name="_celya_nutrition_rows[][nutriment]"
                                   value="<?php echo esc_attr( $row['nutriment'] ?? '' ); ?>"
                                   class="short" />
                        </td>
                        <td>
                            <input type="number"
                                   name="_celya_nutrition_rows[][valeur]"
                                   value="<?php echo esc_attr( $row['valeur'] ?? '' ); ?>"
                                   class="short"
                                   step="0.01"
                                   min="0" />
                        </td>
                        <td>
                            <select name="_celya_nutrition_rows[][unite]">
                                <?php foreach ( $units as $unit ) : ?>
                                    <option value="<?php echo esc_attr( $unit ); ?>"
                                        <?php selected( $row['unite'] ?? 'g', $unit ); ?>>
                                        <?php echo esc_html( $unit ); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <td>
                            <button type="button" class="button celya-remove-row" title="Supprimer">✕</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <p>
                <button type="button"
                        class="button celya-add-row"
                        data-table="celya_nutrition_table"
                        data-template="nutrition">
                    <?php esc_html_e( '+ Ajouter un nutriment', 'celya' ); ?>
                </button>
            </p>
        </div>

        <!-- ===================== CHAMPS TEXTE ===================== -->
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
// 4. SAUVEGARDE DES CHAMPS (PRODUIT SIMPLE)
// =============================================================================

/**
 * Sauvegarde les champs de l'onglet Spécifications lors de l'enregistrement du produit.
 */
function celya_save_specifications_fields( $post_id ) {

    // Ingrédients → JSON
    if ( isset( $_POST['_celya_ingredients_rows'] ) && is_array( $_POST['_celya_ingredients_rows'] ) ) {
        $rows = array_filter(
            $_POST['_celya_ingredients_rows'],
            fn( $r ) => ! empty( trim( $r['ingredient'] ?? '' ) )
        );
        $rows = array_map( function( $r ) {
            return array(
                'ingredient' => sanitize_text_field( $r['ingredient'] ),
                'quantite'   => sanitize_text_field( $r['quantite'] ?? '' ),
            );
        }, $rows );
        update_post_meta( $post_id, '_celya_ingredients', wp_json_encode( array_values( $rows ) ) );
    }

    // Nutrition → JSON
    if ( isset( $_POST['_celya_nutrition_rows'] ) && is_array( $_POST['_celya_nutrition_rows'] ) ) {
        $rows = array_filter(
            $_POST['_celya_nutrition_rows'],
            fn( $r ) => ! empty( trim( $r['nutriment'] ?? '' ) )
        );
        $rows = array_map( function( $r ) {
            return array(
                'nutriment' => sanitize_text_field( $r['nutriment'] ),
                'valeur'    => floatval( $r['valeur'] ?? 0 ),
                'unite'     => sanitize_text_field( $r['unite'] ?? 'g' ),
            );
        }, $rows );
        update_post_meta( $post_id, '_celya_nutrition_table', wp_json_encode( array_values( $rows ) ) );
    }

    // Champs texte simples
    $simple_fields = array(
        '_celya_allergenes',
        '_celya_conservation',
        '_celya_conseil_degustation',
    );
    foreach ( $simple_fields as $field ) {
        if ( isset( $_POST[ $field ] ) ) {
            update_post_meta( $post_id, $field, sanitize_textarea_field( wp_unslash( $_POST[ $field ] ) ) );
        }
    }
}
add_action( 'woocommerce_process_product_meta', 'celya_save_specifications_fields' );


// =============================================================================
// 5. CHAMPS DANS LES VARIATIONS
// =============================================================================

/**
 * Affiche les champs Spécifications dans chaque variation de produit.
 *
 * @param int     $loop           Index de la variation dans la boucle.
 * @param array   $variation_data Données de la variation.
 * @param WP_Post $variation      Objet WP_Post de la variation.
 */
function celya_add_variation_specifications( $loop, $variation_data, $variation ) {
    $variation_id = $variation->ID;

    // Ingrédients (avec fallback parent)
    $ingredients_rows = celya_get_legacy_ingredients( $variation_id );
    if ( empty( $ingredients_rows ) ) {
        $ingredients_rows = array( array( 'ingredient' => '', 'quantite' => '' ) );
    }

    // Nutrition (avec fallback parent)
    $nutrition_raw  = get_post_meta( $variation_id, '_celya_nutrition_table', true );
    $nutrition_rows = $nutrition_raw ? json_decode( $nutrition_raw, true ) : array();
    if ( empty( $nutrition_rows ) ) {
        $nutrition_rows = array( array( 'nutriment' => '', 'valeur' => '', 'unite' => 'g' ) );
    }

    // Champs texte
    $allergenes   = get_post_meta( $variation_id, '_celya_allergenes', true );
    $conservation = get_post_meta( $variation_id, '_celya_conservation', true );
    $degustation  = get_post_meta( $variation_id, '_celya_conseil_degustation', true );

    $units = array( 'g', 'mg', 'µg', 'kcal', 'kJ', 'ml', '%' );
    $table_ing_id  = 'celya_var_ingredients_' . $loop;
    $table_nut_id  = 'celya_var_nutrition_'   . $loop;

    ?>
    <div class="celya-variation-specs" style="padding:12px 0; border-top:1px solid #eee; width:100%;">

        <p style="margin:0 0 10px; font-weight:600; color:#23282d;">
            📋 <?php esc_html_e( 'Spécifications de cette variation', 'celya' ); ?>
        </p>
        <p style="margin:0 0 10px; font-size:12px; color:#666;">
            <?php esc_html_e( 'Laissez vide pour hériter des spécifications du produit parent.', 'celya' ); ?>
        </p>

        <!-- INGRÉDIENTS -->
        <p style="font-weight:600; margin-bottom:5px;"><?php esc_html_e( 'Ingrédients', 'celya' ); ?></p>
        <table class="celya-spec-table widefat" id="<?php echo esc_attr( $table_ing_id ); ?>">
            <thead>
                <tr>
                    <th><?php esc_html_e( 'Ingrédient', 'celya' ); ?></th>
                    <th><?php esc_html_e( 'Quantité', 'celya' ); ?></th>
                    <th width="40"></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ( $ingredients_rows as $row ) : ?>
                <tr>
                    <td>
                        <input type="text"
                               name="celya_variation_ingredients[<?php echo (int) $loop; ?>][][ingredient]"
                               value="<?php echo esc_attr( $row['ingredient'] ?? '' ); ?>" />
                    </td>
                    <td>
                        <input type="text"
                               name="celya_variation_ingredients[<?php echo (int) $loop; ?>][][quantite]"
                               value="<?php echo esc_attr( $row['quantite'] ?? '' ); ?>" />
                    </td>
                    <td>
                        <button type="button" class="button celya-remove-row">✕</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <p>
            <button type="button"
                    class="button celya-add-row"
                    data-table="<?php echo esc_attr( $table_ing_id ); ?>"
                    data-template="ingredient"
                    data-loop="<?php echo (int) $loop; ?>">
                <?php esc_html_e( '+ Ingrédient', 'celya' ); ?>
            </button>
        </p>

        <!-- NUTRITION -->
        <p style="font-weight:600; margin:12px 0 5px;"><?php esc_html_e( 'Valeurs nutritionnelles', 'celya' ); ?></p>
        <table class="celya-spec-table widefat" id="<?php echo esc_attr( $table_nut_id ); ?>">
            <thead>
                <tr>
                    <th><?php esc_html_e( 'Nutriment', 'celya' ); ?></th>
                    <th><?php esc_html_e( 'Valeur', 'celya' ); ?></th>
                    <th><?php esc_html_e( 'Unité', 'celya' ); ?></th>
                    <th width="40"></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ( $nutrition_rows as $row ) : ?>
                <tr>
                    <td>
                        <input type="text"
                               name="celya_variation_nutrition[<?php echo (int) $loop; ?>][][nutriment]"
                               value="<?php echo esc_attr( $row['nutriment'] ?? '' ); ?>" />
                    </td>
                    <td>
                        <input type="number"
                               name="celya_variation_nutrition[<?php echo (int) $loop; ?>][][valeur]"
                               value="<?php echo esc_attr( $row['valeur'] ?? '' ); ?>"
                               step="0.01" min="0" />
                    </td>
                    <td>
                        <select name="celya_variation_nutrition[<?php echo (int) $loop; ?>][][unite]">
                            <?php foreach ( $units as $unit ) : ?>
                                <option value="<?php echo esc_attr( $unit ); ?>"
                                    <?php selected( $row['unite'] ?? 'g', $unit ); ?>>
                                    <?php echo esc_html( $unit ); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <td>
                        <button type="button" class="button celya-remove-row">✕</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <p>
            <button type="button"
                    class="button celya-add-row"
                    data-table="<?php echo esc_attr( $table_nut_id ); ?>"
                    data-template="nutrition"
                    data-loop="<?php echo (int) $loop; ?>">
                <?php esc_html_e( '+ Nutriment', 'celya' ); ?>
            </button>
        </p>

        <!-- CHAMPS TEXTE -->
        <p style="margin-top:12px;">
            <label style="font-weight:600; display:block; margin-bottom:4px;">
                <?php esc_html_e( 'Allergènes', 'celya' ); ?>
            </label>
            <textarea name="celya_variation_allergenes[<?php echo (int) $loop; ?>]"
                      rows="2"
                      style="width:100%;"
                      placeholder="Ex : Contient gluten, lait..."><?php echo esc_textarea( $allergenes ); ?></textarea>
        </p>
        <p>
            <label style="font-weight:600; display:block; margin-bottom:4px;">
                <?php esc_html_e( 'Conservation', 'celya' ); ?>
            </label>
            <textarea name="celya_variation_conservation[<?php echo (int) $loop; ?>]"
                      rows="2"
                      style="width:100%;"
                      placeholder="À conserver dans un endroit sec..."><?php echo esc_textarea( $conservation ); ?></textarea>
        </p>
        <p>
            <label style="font-weight:600; display:block; margin-bottom:4px;">
                <?php esc_html_e( 'Conseil dégustation', 'celya' ); ?>
            </label>
            <textarea name="celya_variation_degustation[<?php echo (int) $loop; ?>]"
                      rows="2"
                      style="width:100%;"
                      placeholder="À déguster avec..."><?php echo esc_textarea( $degustation ); ?></textarea>
        </p>

    </div>
    <?php
}
add_action( 'woocommerce_product_after_variable_attributes', 'celya_add_variation_specifications', 10, 3 );


/**
 * Sauvegarde les champs Spécifications de chaque variation.
 *
 * @param int $variation_id ID de la variation.
 * @param int $loop         Index de la variation dans la boucle.
 */
function celya_save_variation_specifications( $variation_id, $loop ) {

    // Ingrédients
    if ( isset( $_POST['celya_variation_ingredients'][ $loop ] ) && is_array( $_POST['celya_variation_ingredients'][ $loop ] ) ) {
        $rows = array_filter(
            $_POST['celya_variation_ingredients'][ $loop ],
            fn( $r ) => ! empty( trim( $r['ingredient'] ?? '' ) )
        );
        $rows = array_map( function( $r ) {
            return array(
                'ingredient' => sanitize_text_field( $r['ingredient'] ),
                'quantite'   => sanitize_text_field( $r['quantite'] ?? '' ),
            );
        }, $rows );
        update_post_meta( $variation_id, '_celya_ingredients', wp_json_encode( array_values( $rows ) ) );
    }

    // Nutrition
    if ( isset( $_POST['celya_variation_nutrition'][ $loop ] ) && is_array( $_POST['celya_variation_nutrition'][ $loop ] ) ) {
        $rows = array_filter(
            $_POST['celya_variation_nutrition'][ $loop ],
            fn( $r ) => ! empty( trim( $r['nutriment'] ?? '' ) )
        );
        $rows = array_map( function( $r ) {
            return array(
                'nutriment' => sanitize_text_field( $r['nutriment'] ),
                'valeur'    => floatval( $r['valeur'] ?? 0 ),
                'unite'     => sanitize_text_field( $r['unite'] ?? 'g' ),
            );
        }, $rows );
        update_post_meta( $variation_id, '_celya_nutrition_table', wp_json_encode( array_values( $rows ) ) );
    }

    // Champs texte
    $text_fields = array(
        'celya_variation_allergenes'  => '_celya_allergenes',
        'celya_variation_conservation' => '_celya_conservation',
        'celya_variation_degustation' => '_celya_conseil_degustation',
    );
    foreach ( $text_fields as $post_key => $meta_key ) {
        if ( isset( $_POST[ $post_key ][ $loop ] ) ) {
            update_post_meta( $variation_id, $meta_key, sanitize_textarea_field( wp_unslash( $_POST[ $post_key ][ $loop ] ) ) );
        }
    }
}
add_action( 'woocommerce_save_product_variation', 'celya_save_variation_specifications', 10, 2 );


// =============================================================================
// 6. ASSETS ADMIN (CSS + JS pour les tableaux dynamiques)
// =============================================================================

/**
 * Injecte les styles et scripts nécessaires aux tableaux dynamiques,
 * uniquement sur les pages d'édition de produits WooCommerce.
 */
function celya_admin_specs_assets( $hook ) {
    global $post;

    if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
        return;
    }
    if ( ! $post || $post->post_type !== 'product' ) {
        return;
    }

    // CSS
    wp_add_inline_style( 'wp-admin', '
        /* Tableaux Spécifications */
        .celya-spec-table {
            border-collapse: collapse;
            margin-bottom: 10px;
            width: 100%;
        }
        .celya-spec-table th,
        .celya-spec-table td {
            padding: 6px 8px;
            border: 1px solid #ddd;
            vertical-align: middle;
        }
        .celya-spec-table thead th {
            background: #f9f9f9;
            font-weight: 600;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .celya-spec-table input[type="text"],
        .celya-spec-table input[type="number"],
        .celya-spec-table select {
            width: 100%;
            margin: 0;
            box-sizing: border-box;
        }
        .celya-spec-table .button {
            padding: 2px 8px;
            font-size: 12px;
            line-height: 1.8;
        }
        #celya_specifications_data .options_group {
            padding: 10px 20px;
        }
        .celya-variation-specs .celya-spec-table th,
        .celya-variation-specs .celya-spec-table td {
            padding: 4px 6px;
        }
    ' );

    // JS
    wp_add_inline_script( 'jquery', '
    jQuery(function($) {

        // Génère une ligne ingrédient
        function celya_row_ingredient(baseName) {
            return "<tr>" +
                "<td><input type=\'text\' name=\'" + baseName + "[][ingredient]\' /></td>" +
                "<td><input type=\'text\' name=\'" + baseName + "[][quantite]\' /></td>" +
                "<td><button type=\'button\' class=\'button celya-remove-row\'>✕</button></td>" +
            "</tr>";
        }

        // Génère une ligne nutrition
        function celya_row_nutrition(baseName) {
            var units = ["g","mg","µg","kcal","kJ","ml","%"];
            var options = units.map(function(u) {
                return "<option value=\'" + u + "\'>" + u + "</option>";
            }).join("");
            return "<tr>" +
                "<td><input type=\'text\'   name=\'" + baseName + "[][nutriment]\' /></td>" +
                "<td><input type=\'number\' name=\'" + baseName + "[][valeur]\' step=\'0.01\' min=\'0\' /></td>" +
                "<td><select name=\'" + baseName + "[][unite]\'>" + options + "</select></td>" +
                "<td><button type=\'button\' class=\'button celya-remove-row\'>✕</button></td>" +
            "</tr>";
        }

        // Déduit le baseName depuis le name du premier input de la table
        function celya_get_base_name($table, type, loop) {
            var first = $table.find("tbody input:first");
            if (first.length) {
                return first.attr("name")
                    .replace(/\[\]\[(ingredient|quantite|nutriment|valeur|unite)\]$/, "")
                    .replace(/\[\]$/, "");
            }
            // Fallback : reconstruction manuelle
            if (loop !== undefined && loop !== "") {
                // Variation
                return type === "ingredient"
                    ? "celya_variation_ingredients[" + loop + "]"
                    : "celya_variation_nutrition["   + loop + "]";
            }
            // Produit simple
            return type === "ingredient"
                ? "_celya_ingredients_rows"
                : "_celya_nutrition_rows";
        }

        // Clic sur "Ajouter une ligne"
        $(document).on("click", ".celya-add-row", function() {
            var $btn     = $(this);
            var tableId  = $btn.data("table");
            var tmpl     = $btn.data("template");
            var loop     = $btn.data("loop");
            var $table   = $("#" + tableId);
            var $tbody   = $table.find("tbody");
            var baseName = celya_get_base_name($table, tmpl, loop);

            if (tmpl === "ingredient") {
                $tbody.append(celya_row_ingredient(baseName));
            } else if (tmpl === "nutrition") {
                $tbody.append(celya_row_nutrition(baseName));
            }
        });

        // Clic sur "Supprimer une ligne"
        $(document).on("click", ".celya-remove-row", function() {
            var $tbody = $(this).closest("tbody");
            if ($tbody.find("tr").length > 1) {
                $(this).closest("tr").remove();
            } else {
                // Garder au moins une ligne vide plutôt que de tout supprimer
                $(this).closest("tr").find("input, select").val("");
            }
        });

    });
    ' );
}
add_action( 'admin_enqueue_scripts', 'celya_admin_specs_assets' );
