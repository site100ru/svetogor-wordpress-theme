<?php
/**
 * AJAX обработчики для темы
 * 
 * @package svetogor
 */

if (!defined('ABSPATH')) {
    exit;
}

// ============================================================================
// AJAX для предпросмотра связанных категорий
// ============================================================================

/**
 * Предварительный просмотр связанных категорий в админке
 */
function ajax_preview_related_categories() {
    // Проверяем права доступа
    if (!current_user_can('manage_product_terms')) {
        wp_die('Недостаточно прав доступа');
    }

    $category_ids = isset($_POST['category_ids']) ? array_map('intval', $_POST['category_ids']) : array();

    if (empty($category_ids)) {
        echo '<p>Выберите категории для предварительного просмотра</p>';
        wp_die();
    }

    // Ограничиваем до 6 категорий
    $category_ids = array_slice($category_ids, 0, 6);

    echo '<div class="related-categories-preview" style="display: flex; flex-wrap: wrap; gap: 10px; margin-top: 15px;">';

    foreach ($category_ids as $cat_id) {
        $category = get_term($cat_id, 'product_cat');
        if ($category && !is_wp_error($category)) {
            $photo_url = get_category_photo_url($cat_id, 'thumbnail');
            if (!$photo_url) {
                $photo_url = get_template_directory_uri() . '/assets/img/default-category.jpg';
            }

            echo '<div style="width: 80px; text-align: center; padding: 10px; border: 1px solid #ddd; border-radius: 4px; background: #f9f9f9;">';
            echo '<img src="' . esc_url($photo_url) . '" alt="' . esc_attr($category->name) . '" style="width: 60px; height: 40px; object-fit: cover; border-radius: 2px;">';
            echo '<div style="font-size: 11px; margin-top: 5px; line-height: 1.2;">' . esc_html($category->name) . '</div>';
            echo '</div>';
        }
    }

    echo '</div>';

    wp_die();
}
add_action('wp_ajax_preview_related_categories', 'ajax_preview_related_categories');

// ============================================================================
// AJAX для предпросмотра раскрывающегося текста категории
// ============================================================================

/**
 * Получение данных категории для предпросмотра раскрывающегося текста
 */
function handle_category_expanding_text_preview() {
    // Проверяем nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'category_preview_nonce')) {
        wp_send_json_error('Ошибка безопасности');
    }

    $category_id = intval($_POST['category_id']);

    if (!$category_id) {
        wp_send_json_error('Неверный ID категории');
    }

    // Получаем данные категории
    $category_data = get_category_expanding_text_data($category_id);

    // Очищаем HTML теги для предпросмотра
    if (isset($category_data['main_content'])) {
        $category_data['main_content'] = wp_strip_all_tags($category_data['main_content']);
    }

    if (isset($category_data['additional_content'])) {
        $category_data['additional_content'] = wp_strip_all_tags($category_data['additional_content']);
    }

    wp_send_json_success($category_data);
}
add_action('wp_ajax_get_category_expanding_text_preview', 'handle_category_expanding_text_preview');

// ============================================================================
// JavaScript для предпросмотра связанных категорий в админке
// ============================================================================

/**
 * Добавление JavaScript для предпросмотра связанных категорий
 */
function related_categories_preview_script() {
    global $taxnow;

    if ($taxnow !== 'product_cat') {
        return;
    }
    ?>
    <script type="text/javascript">
        jQuery(document).ready(function ($) {
            if ($('#related_categories_container').length) {
                var previewButton = '<button type="button" id="preview-related-categories" class="button" style="margin-top: 10px;">Предварительный просмотр</button><div id="preview-container"></div>';
                $('#related_categories_container').append(previewButton);
                
                $('#preview-related-categories').on('click', function () {
                    var selectedCategories = [];
                    $('.related-category-checkbox:checked').each(function () {
                        selectedCategories.push($(this).val());
                    });
                    
                    if (selectedCategories.length === 0) {
                        $('#preview-container').html('<p style="color: #666; font-style: italic;">Выберите категории для предварительного просмотра</p>');
                        return;
                    }
                    
                    $.post(ajaxurl, {
                        action: 'preview_related_categories',
                        category_ids: selectedCategories
                    }, function (response) {
                        $('#preview-container').html(response);
                    });
                });
            }
        });
    </script>
    <?php
}
add_action('admin_footer-edit-tags.php', 'related_categories_preview_script');
add_action('admin_footer-term.php', 'related_categories_preview_script');

// ============================================================================
// JavaScript для предпросмотра раскрывающегося текста в админке
// ============================================================================

/**
 * Добавление JavaScript для динамического обновления предпросмотра раскрывающегося текста
 */
function add_expanding_text_preview_script() {
    global $post;

    if (!$post || $post->post_type !== 'product') {
        return;
    }
    ?>
    <script type="text/javascript">
        jQuery(document).ready(function ($) {
            function updateExpandingTextPreview() {
                var categoryId = $('[data-name="category_source"] select').val();
                var previewField = $('[data-name="show_preview"] .acf-input');

                if (!categoryId) {
                    previewField.html('<div class="acf-message-wrapper"><p class="acf-message">Выберите категорию для просмотра настроек</p></div>');
                    return;
                }

                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'get_category_expanding_text_preview',
                        category_id: categoryId,
                        nonce: '<?php echo wp_create_nonce("category_preview_nonce"); ?>'
                    },
                    success: function (response) {
                        if (response.success) {
                            var data = response.data;
                            var previewHtml = '<div style="background: #f9f9f9; padding: 15px; border-radius: 4px; border-left: 4px solid #0073aa;">';

                            if (data.section_title) {
                                previewHtml += '<h4 style="margin: 0 0 10px 0; color: #0073aa;">📝 ' + data.section_title + '</h4>';
                            }

                            previewHtml += '<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">';

                            // Левая колонка
                            previewHtml += '<div>';
                            previewHtml += '<p style="margin: 0 0 8px 0;"><strong>Фон:</strong> ' + (data.background_color === 'grey' ? 'Серый' : 'Белый') + '</p>';

                            if (data.main_content) {
                                var mainContentPreview = data.main_content.length > 100 ?
                                    data.main_content.substring(0, 100) + '...' :
                                    data.main_content;
                                previewHtml += '<p style="margin: 0 0 8px 0;"><strong>Основной текст:</strong><br><em>' + mainContentPreview + '</em></p>';
                            }
                            previewHtml += '</div>';

                            // Правая колонка
                            previewHtml += '<div>';
                            if (data.additional_content) {
                                var additionalContentPreview = data.additional_content.length > 100 ?
                                    data.additional_content.substring(0, 100) + '...' :
                                    data.additional_content;
                                previewHtml += '<p style="margin: 0 0 8px 0;"><strong>Дополнительный текст:</strong><br><em>' + additionalContentPreview + '</em></p>';
                            }

                            previewHtml += '<p style="margin: 0;"><strong>Статус:</strong> <span style="color: green;">✓ Настроен</span></p>';
                            previewHtml += '</div>';

                            previewHtml += '</div>';
                            previewHtml += '</div>';

                            previewField.html(previewHtml);
                        } else {
                            previewField.html('<div class="acf-message-wrapper"><p class="acf-message acf-message-error">Ошибка загрузки данных категории</p></div>');
                        }
                    },
                    error: function () {
                        previewField.html('<div class="acf-message-wrapper"><p class="acf-message acf-message-error">Ошибка AJAX запроса</p></div>');
                    }
                });
            }

            $(document).on('change', '[data-name="category_source"] select', function () {
                updateExpandingTextPreview();
            });

            setTimeout(updateExpandingTextPreview, 500);
        });
    </script>
    <?php
}
add_action('acf/input/admin_head', 'add_expanding_text_preview_script');

// ============================================================================
// ACF фильтры для поля предпросмотра
// ============================================================================

/**
 * Обновляем поле предпросмотра при загрузке
 */
function load_expanding_text_preview_field($field) {
    $field['message'] = '<div id="expanding-text-preview">
        <div style="text-align: center; padding: 20px; color: #666;">
            <p>Выберите категорию выше, чтобы увидеть предпросмотр настроек</p>
        </div>
    </div>';

    return $field;
}
add_filter('acf/load_field/name=show_preview', 'load_expanding_text_preview_field');

/**
 * Хук для обновления выборов в ACF поле при загрузке
 */
function load_category_choices_for_expanding_text($field) {
    global $post;

    if ($post && $post->post_type === 'product') {
        $choices = get_product_categories_for_acf_choices($post->ID);

        if (!empty($choices)) {
            $field['choices'] = $choices;
        } else {
            $field['choices'] = array('' => 'Нет категорий с настроенным текстом');
        }
    }

    return $field;
}
add_filter('acf/load_field/name=category_source', 'load_category_choices_for_expanding_text');

/**
 * Получает все категории товара для выбора в ACF поле
 */
function get_product_categories_for_acf_choices($product_id = null) {
    if (!$product_id) {
        global $post;
        $product_id = $post ? $post->ID : null;
    }

    if (!$product_id) {
        return array();
    }

    $categories = wp_get_post_terms($product_id, 'product_cat');
    $choices = array();

    if (!empty($categories)) {
        foreach ($categories as $category) {
            if (has_category_expanding_text($category->term_id)) {
                $choices[$category->term_id] = $category->name . ' ✓';
            } else {
                $choices[$category->term_id] = $category->name . ' (не настроен)';
            }
        }
    }

    return $choices;
}