<?php
/**
 * Система "Комплексное оформление" для WooCommerce
 * 
 * @package svetogor
 */

if (!defined('ABSPATH')) {
    exit;
}

// ============================================================================
// Регистрация таксономии
// ============================================================================

/**
 * Регистрация таксономии "Комплексное оформление"
 */
function register_complex_design_taxonomy() {
    $labels = array(
        'name' => 'Комплексное оформление',
        'singular_name' => 'Комплексное оформление',
        'search_items' => 'Поиск оформлений',
        'all_items' => 'Все оформления',
        'edit_item' => 'Редактировать оформление',
        'update_item' => 'Обновить оформление',
        'add_new_item' => 'Добавить новое оформление',
        'new_item_name' => 'Название нового оформления',
        'menu_name' => 'Комплексное оформление',
    );

    $args = array(
        'hierarchical' => false,
        'labels' => $labels,
        'show_ui' => true,
        'show_admin_column' => false,
        'query_var' => true,
        'rewrite' => array('slug' => 'complex-design'),
        'public' => true,
        'show_in_menu' => true,
        'show_tagcloud' => false,
        'show_in_rest' => true,
    );

    register_taxonomy('complex_design', array('product'), $args);
}
add_action('init', 'register_complex_design_taxonomy');

// ============================================================================
// Поля таксономии - Миниатюра
// ============================================================================

/**
 * Поле миниатюры для формы создания
 */
function add_complex_design_thumbnail_field($tag) {
    ?>
    <div class="form-field">
        <label for="complex_design_thumbnail">Миниатюра</label>
        <input type="hidden" id="complex_design_thumbnail" name="complex_design_thumbnail" value="" />
        <div id="complex_design_thumbnail_preview"></div>
        <button type="button" class="button complex-design-thumbnail-upload">Выбрать изображение</button>
        <button type="button" class="button complex-design-thumbnail-remove" style="display:none;">Удалить изображение</button>
        <p>Выберите изображение для миниатюры оформления.</p>
    </div>

    <script>
        jQuery(document).ready(function ($) {
            var mediaUploader;

            $('.complex-design-thumbnail-upload').click(function (e) {
                e.preventDefault();

                if (mediaUploader) {
                    mediaUploader.open();
                    return;
                }

                mediaUploader = wp.media({
                    title: 'Выберите миниатюру',
                    button: { text: 'Использовать это изображение' },
                    multiple: false
                });

                mediaUploader.on('select', function () {
                    var attachment = mediaUploader.state().get('selection').first().toJSON();
                    $('#complex_design_thumbnail').val(attachment.id);
                    $('#complex_design_thumbnail_preview').html('<img src="' + attachment.sizes.thumbnail.url + '" style="max-width: 150px;" />');
                    $('.complex-design-thumbnail-remove').show();
                });

                mediaUploader.open();
            });

            $('.complex-design-thumbnail-remove').click(function (e) {
                e.preventDefault();
                $('#complex_design_thumbnail').val('');
                $('#complex_design_thumbnail_preview').html('');
                $(this).hide();
            });
        });
    </script>
    <?php
}
add_action('complex_design_add_form_fields', 'add_complex_design_thumbnail_field', 20);

/**
 * Поле миниатюры для формы редактирования
 */
function edit_complex_design_thumbnail_field($tag) {
    $thumbnail_id = get_term_meta($tag->term_id, 'thumbnail_id', true);
    $thumbnail_url = '';
    if ($thumbnail_id) {
        $thumbnail_url = wp_get_attachment_image_url($thumbnail_id, 'thumbnail');
    }
    ?>
    <tr class="form-field">
        <th scope="row" valign="top"><label for="complex_design_thumbnail">Миниатюра</label></th>
        <td>
            <input type="hidden" id="complex_design_thumbnail" name="complex_design_thumbnail" value="<?php echo esc_attr($thumbnail_id); ?>" />
            <div id="complex_design_thumbnail_preview">
                <?php if ($thumbnail_url): ?>
                    <img src="<?php echo esc_url($thumbnail_url); ?>" style="max-width: 150px;" alt="Превью изображения" />
                <?php endif; ?>
            </div>
            <button type="button" class="button complex-design-thumbnail-upload">Выбрать изображение</button>
            <button type="button" class="button complex-design-thumbnail-remove" style="<?php echo $thumbnail_url ? '' : 'display:none;'; ?>">Удалить изображение</button>
            <br />
            <span class="description">Миниатюра оформления.</span>
        </td>
    </tr>

    <script>
        jQuery(document).ready(function ($) {
            var mediaUploader;

            $('.complex-design-thumbnail-upload').click(function (e) {
                e.preventDefault();

                if (mediaUploader) {
                    mediaUploader.open();
                    return;
                }

                mediaUploader = wp.media({
                    title: 'Выберите миниатюру',
                    button: { text: 'Использовать это изображение' },
                    multiple: false
                });

                mediaUploader.on('select', function () {
                    var attachment = mediaUploader.state().get('selection').first().toJSON();
                    $('#complex_design_thumbnail').val(attachment.id);
                    $('#complex_design_thumbnail_preview').html('<img src="' + attachment.sizes.thumbnail.url + '" style="max-width: 150px;" />');
                    $('.complex-design-thumbnail-remove').show();
                });

                mediaUploader.open();
            });

            $('.complex-design-thumbnail-remove').click(function (e) {
                e.preventDefault();
                $('#complex_design_thumbnail').val('');
                $('#complex_design_thumbnail_preview').html('');
                $(this).hide();
            });
        });
    </script>
    <?php
}
add_action('complex_design_edit_form_fields', 'edit_complex_design_thumbnail_field', 20);

// ============================================================================
// Поля таксономии - Товары
// ============================================================================

/**
 * Поле выбора товаров для формы создания
 */
function add_complex_design_products_field($tag) {
    ?>
    <div class="form-field">
        <label for="complex_design_products">Связанные товары</label>
        <input type="text" id="product_search" placeholder="Поиск товара..." style="width: 100%; margin-bottom: 10px;" />
        <?php
        $products = get_posts(array(
            'post_type' => 'product',
            'posts_per_page' => -1,
            'orderby' => 'title',
            'order' => 'ASC',
        ));

        if (!empty($products)) {
            echo '<div style="max-height: 200px; overflow-y: auto; border: 1px solid #ddd; padding: 10px;">';
            foreach ($products as $product) {
                echo '<label class="product-item" style="display: block; margin-bottom: 5px;">';
                echo '<input type="checkbox" name="complex_design_products[]" value="' . $product->ID . '" /> ';
                echo esc_html($product->post_title);
                echo '</label>';
            }
            echo '</div>';
        }
        ?>
        <p>Выберите товары, которые относятся к этому оформлению.</p>
    </div>

    <script>
        jQuery(document).ready(function($) {
            $('#product_search').on('keyup', function() {
                var searchText = $(this).val().toLowerCase();
                $('.product-item').each(function() {
                    var productName = $(this).text().toLowerCase();
                    if (productName.indexOf(searchText) > -1) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            });
        });
    </script>
    <?php
}
add_action('complex_design_add_form_fields', 'add_complex_design_products_field', 30);

/**
 * Поле выбора товаров для формы редактирования
 */
function edit_complex_design_products_field($tag) {
    $selected_products = get_term_meta($tag->term_id, 'linked_products', true);
    $selected_products = is_array($selected_products) ? $selected_products : array();
    ?>
    <tr class="form-field">
        <th scope="row" valign="top"><label for="complex_design_products">Связанные товары</label></th>
        <td>
            <input type="text" id="product_search" placeholder="Поиск товара..." style="width: 100%; margin-bottom: 10px;" />
            <?php
            $products = get_posts(array(
                'post_type' => 'product',
                'posts_per_page' => -1,
                'orderby' => 'title',
                'order' => 'ASC',
            ));

            if (!empty($products)) {
                echo '<div style="max-height: 200px; overflow-y: auto; border: 1px solid #ddd; padding: 10px;">';
                foreach ($products as $product) {
                    $checked = in_array($product->ID, $selected_products) ? 'checked="checked"' : '';
                    echo '<label class="product-item" style="display: block; margin-bottom: 5px;">';
                    echo '<input type="checkbox" name="complex_design_products[]" value="' . $product->ID . '" ' . $checked . ' /> ';
                    echo esc_html($product->post_title);
                    echo '</label>';
                }
                echo '</div>';
            }
            ?>
            <br />
            <span class="description">Товары, связанные с этим оформлением.</span>
        </td>
    </tr>

    <script>
        jQuery(document).ready(function($) {
            $('#product_search').on('keyup', function() {
                var searchText = $(this).val().toLowerCase();
                $('.product-item').each(function() {
                    var productName = $(this).text().toLowerCase();
                    if (productName.indexOf(searchText) > -1) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            });
        });
    </script>
    <?php
}
add_action('complex_design_edit_form_fields', 'edit_complex_design_products_field', 30);

// ============================================================================
// Поля таксономии - Категории
// ============================================================================

/**
 * Поле связанных категорий для формы создания
 */
function add_complex_design_categories_field($tag) {
    ?>
    <div class="form-field">
        <label for="complex_design_categories">Связанные категории</label>
        <input type="text" id="category_search" placeholder="Поиск категории..." style="width: 100%; margin-bottom: 10px;" />
        <?php
        $categories = get_terms(array(
            'taxonomy' => 'product_cat',
            'hide_empty' => false,
        ));

        if (!empty($categories) && !is_wp_error($categories)) {
            echo '<div style="max-height: 200px; overflow-y: auto; border: 1px solid #ddd; padding: 10px;">';
            foreach ($categories as $category) {
                echo '<label class="category-item" style="display: block; margin-bottom: 5px;">';
                echo '<input type="checkbox" name="complex_design_categories[]" value="' . $category->term_id . '" /> ';
                echo esc_html($category->name);
                echo '</label>';
            }
            echo '</div>';
        }
        ?>
        <p>Выберите категории товаров, которые относятся к этому оформлению.</p>
    </div>

    <script>
        jQuery(document).ready(function($) {
            $('#category_search').on('keyup', function() {
                var searchText = $(this).val().toLowerCase();
                $('.category-item').each(function() {
                    var categoryName = $(this).text().toLowerCase();
                    if (categoryName.indexOf(searchText) > -1) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            });
        });
    </script>
    <?php
}
add_action('complex_design_add_form_fields', 'add_complex_design_categories_field', 40);

/**
 * Поле связанных категорий для формы редактирования
 */
function edit_complex_design_categories_field($tag) {
    $selected_categories = get_term_meta($tag->term_id, 'linked_categories', true);
    $selected_categories = is_array($selected_categories) ? $selected_categories : array();
    ?>
    <tr class="form-field">
        <th scope="row" valign="top"><label for="complex_design_categories">Связанные категории</label></th>
        <td>
            <input type="text" id="category_search" placeholder="Поиск категории..." style="width: 100%; margin-bottom: 10px;" />
            <?php
            $categories = get_terms(array(
                'taxonomy' => 'product_cat',
                'hide_empty' => false,
            ));

            if (!empty($categories) && !is_wp_error($categories)) {
                echo '<div style="max-height: 200px; overflow-y: auto; border: 1px solid #ddd; padding: 10px;">';
                foreach ($categories as $category) {
                    $checked = in_array($category->term_id, $selected_categories) ? 'checked="checked"' : '';
                    echo '<label class="category-item" style="display: block; margin-bottom: 5px;">';
                    echo '<input type="checkbox" name="complex_design_categories[]" value="' . $category->term_id . '" ' . $checked . ' /> ';
                    echo esc_html($category->name);
                    echo '</label>';
                }
                echo '</div>';
            }
            ?>
            <br />
            <span class="description">Категории, связанные с этим оформлением.</span>
        </td>
    </tr>

    <script>
        jQuery(document).ready(function($) {
            $('#category_search').on('keyup', function() {
                var searchText = $(this).val().toLowerCase();
                $('.category-item').each(function() {
                    var categoryName = $(this).text().toLowerCase();
                    if (categoryName.indexOf(searchText) > -1) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            });
        });
    </script>
    <?php
}
add_action('complex_design_edit_form_fields', 'edit_complex_design_categories_field', 40);

// ============================================================================
// Сохранение полей
// ============================================================================

/**
 * Сохранение полей таксономии
 */
function save_complex_design_fields($term_id) {
    // Сохраняем миниатюру
    if (isset($_POST['complex_design_thumbnail'])) {
        update_term_meta($term_id, 'thumbnail_id', sanitize_text_field($_POST['complex_design_thumbnail']));
    }

    // Сохраняем связанные категории
    if (isset($_POST['complex_design_categories'])) {
        $categories = array_map('intval', $_POST['complex_design_categories']);
        update_term_meta($term_id, 'linked_categories', $categories);
    } else {
        delete_term_meta($term_id, 'linked_categories');
    }

    // Сохраняем связанные товары
    if (isset($_POST['complex_design_products'])) {
        $products = array_map('intval', $_POST['complex_design_products']);
        update_term_meta($term_id, 'linked_products', $products);
    } else {
        delete_term_meta($term_id, 'linked_products');
    }
}
add_action('edited_complex_design', 'save_complex_design_fields');
add_action('create_complex_design', 'save_complex_design_fields');

// ============================================================================
// Настройка описания
// ============================================================================

/**
 * Удаляем стандартное поле описания
 */
function remove_complex_design_description_field() {
    remove_action('complex_design_edit_form_fields', 'taxonomy_metadata_wpdbfix_edit', 9);
}
add_action('admin_init', 'remove_complex_design_description_field');

/**
 * Скрываем стандартное описание через CSS
 */
function hide_default_description_css() {
    global $current_screen;
    if ($current_screen && $current_screen->taxonomy == 'complex_design') {
        echo '<style>
            .term-description-wrap { display: none !important; }
            .term-description-wrap.custom-description-wrap { display: table-row !important; }
        </style>';
    }
}
add_action('admin_head-term.php', 'hide_default_description_css');
add_action('admin_head-edit-tags.php', 'hide_default_description_css');

/**
 * Добавляем визуальный редактор для редактирования описания
 */
function add_complex_design_description_editor($term) {
    ?>
    <tr class="form-field term-description-wrap custom-description-wrap">
        <th scope="row"><label for="custom_description">Описание</label></th>
        <td>
            <?php
            $content = htmlspecialchars_decode($term->description, ENT_QUOTES);
            
            $settings = array(
                'textarea_name' => 'description',
                'textarea_rows' => 10,
                'wpautop' => true,
                'media_buttons' => false,
                'tinymce' => array(
                    'toolbar1' => 'formatselect,bold,italic,underline,bullist,numlist,blockquote,alignleft,aligncenter,alignright,link,unlink,removeformat',
                ),
                'quicktags' => true,
            );
            
            wp_editor($content, 'custom_description', $settings);
            ?>
            <p class="description">Описание оформления с поддержкой форматирования.</p>
        </td>
    </tr>
    <script>
        jQuery(document).ready(function($) {
            $('#edittag').on('submit', function() {
                var editor_content = '';
                if (typeof tinyMCE !== 'undefined' && tinyMCE.get('custom_description')) {
                    editor_content = tinyMCE.get('custom_description').getContent();
                } else {
                    editor_content = $('#custom_description').val();
                }
                
                $('<input>').attr({
                    type: 'hidden',
                    name: 'description',
                    value: editor_content
                }).appendTo('#edittag');
            });
        });
    </script>
    <?php
}
add_action('complex_design_edit_form_fields', 'add_complex_design_description_editor');

/**
 * Редактор для формы добавления нового термина
 */
function add_complex_design_description_editor_add() {
    ?>
    <div class="form-field term-description-wrap custom-description-wrap">
        <label for="custom_description">Описание</label>
        <?php
        $settings = array(
            'textarea_name' => 'description',
            'textarea_rows' => 10,
            'wpautop' => true,
            'media_buttons' => false,
            'tinymce' => array(
                'toolbar1' => 'formatselect,bold,italic,underline,bullist,numlist,blockquote,alignleft,aligncenter,alignright,link,unlink,removeformat',
            ),
            'quicktags' => true,
        );
        
        wp_editor('', 'custom_description', $settings);
        ?>
        <p class="description">Описание оформления с поддержкой форматирования.</p>
    </div>
    <script>
        jQuery(document).ready(function($) {
            $('#addtag').on('submit', function() {
                var editor_content = '';
                if (typeof tinyMCE !== 'undefined' && tinyMCE.get('custom_description')) {
                    editor_content = tinyMCE.get('custom_description').getContent();
                } else {
                    editor_content = $('#custom_description').val();
                }
                
                $('<input>').attr({
                    type: 'hidden',
                    name: 'description',
                    value: editor_content
                }).appendTo('#addtag');
            });
        });
    </script>
    <style>
        .term-description-wrap:not(.custom-description-wrap) { 
            display: none !important; 
        }
    </style>
    <?php
}
add_action('complex_design_add_form_fields', 'add_complex_design_description_editor_add');

// ============================================================================
// Подключение медиабиблиотеки
// ============================================================================

/**
 * Подключаем медиабиблиотеку в админке
 */
function enqueue_complex_design_admin_scripts($hook) {
    if ($hook == 'edit-tags.php' || $hook == 'term.php') {
        global $taxonomy;
        if ($taxonomy == 'complex_design') {
            wp_enqueue_media();
        }
    }
}
add_action('admin_enqueue_scripts', 'enqueue_complex_design_admin_scripts');