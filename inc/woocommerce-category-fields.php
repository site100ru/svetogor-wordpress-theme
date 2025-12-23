<?php
/**
 * Кастомные поля для категорий товаров WooCommerce
 */

// Переименование стандартной миниатюры
add_filter('woocommerce_taxonomy_args_product_cat', 'rename_category_thumbnail_label');

function rename_category_thumbnail_label($args) {
    // Изменяем лейблы для миниатюры
    add_action('product_cat_add_form_fields', 'rename_thumbnail_labels_add');
    add_action('product_cat_edit_form_fields', 'rename_thumbnail_labels_edit');
    
    return $args;
}

// Переименование лейблов при добавлении категории
function rename_thumbnail_labels_add() {
    ?>
    <script type="text/javascript">
    jQuery(document).ready(function($) {
        // Переименовываем лейбл миниатюры
        $('label[for="product_cat_thumbnail_id"]').text('Иконка для шапки');
        $('p:contains("Это изображение, используемое для представления этой категории")').text('Это изображение будет использоваться как иконка категории в шапке сайта.');
    });
    </script>
    <?php
}

// Переименование лейблов при редактировании категории
function rename_thumbnail_labels_edit() {
    ?>
    <script type="text/javascript">
    jQuery(document).ready(function($) {
        // Переименовываем лейбл миниатюры
        $('th:contains("Миниатюра")').text('Иконка для шапки');
        $('p:contains("Это изображение, используемое для представления этой категории")').text('Это изображение будет использоваться как иконка категории в шапке сайта.');
    });
    </script>
    <?php
}

// Добавление кастомных полей при создании категории
add_action('product_cat_add_form_fields', 'add_category_custom_fields');

function add_category_custom_fields() {
    // Получаем все категории для списка связанных
    $all_categories = get_terms(array(
        'taxonomy' => 'product_cat',
        'hide_empty' => false,
        'orderby' => 'name',
        'order' => 'ASC'
    ));
    ?>
    <!-- Фотография категории -->
    <div class="form-field term-category-photo-wrap">
        <label for="category_photo">Фотография категории</label>
        <div id="category_photo_container">
            <div id="category_photo_preview" style="margin-bottom: 15px;">
                <div style="width: 200px; height: 120px; background: #f0f0f0; border: 2px dashed #ccc; display: flex; align-items: center; justify-content: center; border-radius: 4px;">
                    <span style="color: #666;">Фото не выбрано</span>
                </div>
            </div>
            
            <button type="button" id="select-category-photo" class="button">Выбрать фотографию</button>
            <button type="button" id="remove-category-photo" class="button" style="display: none;">Удалить фото</button>
            <input type="hidden" id="category_photo_id" name="category_photo" value="">
        </div>
        <p class="description">Основное изображение категории. Рекомендуемый размер: 600x400px.</p>
    </div>

    <!-- Выводить в шапке -->
    <div class="form-field term-show-in-header-wrap">
        <label for="show_in_header">
            <input type="checkbox" id="show_in_header" name="show_in_header" value="1">
            Выводить в шапке
        </label>
        <p class="description">Отмеченные категории будут отображаться в навигации шапки сайта.</p>
    </div>

    <!-- Связанные категории -->
    <div class="form-field term-related-categories-wrap">
        <label for="related_categories">Связанные категории</label>
        <div id="related_categories_container">
            <p style="margin-bottom: 10px; font-weight: 500;">Выберите до 6 связанных категорий:</p>
            <div style="max-height: 200px; overflow-y: auto; border: 1px solid #ddd; padding: 10px; background: #fafafa;">
                <?php if (!empty($all_categories)): ?>
                    <?php foreach ($all_categories as $cat): ?>
                        <label style="display: block; margin-bottom: 5px;">
                            <input type="checkbox" name="related_categories[]" value="<?php echo $cat->term_id; ?>" class="related-category-checkbox">
                            <?php echo esc_html($cat->name); ?>
                        </label>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <p class="description">Выбранные категории будут отображаться в блоке "А еще Вам может пригодиться" на странице архива этой категории. Максимум 6 категорий.</p>
        </div>
    </div>

    <script type="text/javascript">
    jQuery(document).ready(function($) {
        var mediaUploader;
        
        // Медиа загрузчик для фотографии
        $('#select-category-photo').on('click', function(e) {
            e.preventDefault();
            
            if (mediaUploader) {
                mediaUploader.open();
                return;
            }
            
            mediaUploader = wp.media({
                title: 'Выберите фотографию категории',
                button: {
                    text: 'Использовать это изображение'
                },
                multiple: false
            });
            
            mediaUploader.on('select', function() {
                var attachment = mediaUploader.state().get('selection').first().toJSON();
                
                $('#category_photo_id').val(attachment.id);
                $('#category_photo_preview').html('<img src="' + attachment.sizes.medium.url + '" style="width: 200px; height: 120px; object-fit: cover; border-radius: 4px;">');
                $('#remove-category-photo').show();
            });
            
            mediaUploader.open();
        });
        
        $('#remove-category-photo').on('click', function() {
            $('#category_photo_id').val('');
            $('#category_photo_preview').html('<div style="width: 200px; height: 120px; background: #f0f0f0; border: 2px dashed #ccc; display: flex; align-items: center; justify-content: center; border-radius: 4px;"><span style="color: #666;">Фото не выбрано</span></div>');
            $(this).hide();
        });
        
        // Ограничение выбора связанных категорий
        $('.related-category-checkbox').on('change', function() {
            var checkedBoxes = $('.related-category-checkbox:checked').length;
            
            if (checkedBoxes >= 6) {
                $('.related-category-checkbox:not(:checked)').prop('disabled', true);
            } else {
                $('.related-category-checkbox').prop('disabled', false);
            }
        });
    });
    </script>
    <?php
}

// Добавление кастомных полей при редактировании категории
add_action('product_cat_edit_form_fields', 'edit_category_custom_fields');

function edit_category_custom_fields($term) {
    // Получаем существующие значения
    $category_photo = get_term_meta($term->term_id, 'category_photo', true);
    $show_in_header = get_term_meta($term->term_id, 'show_in_header', true);
    $related_categories = get_term_meta($term->term_id, 'related_categories', true);
    
    if (!is_array($related_categories)) {
        $related_categories = array();
    }
    
    $photo_url = '';
    if ($category_photo) {
        $photo_data = wp_get_attachment_image_src($category_photo, 'medium');
        if ($photo_data) {
            $photo_url = $photo_data[0];
        }
    }
    
    // Получаем все категории для списка связанных (исключая текущую)
    $all_categories = get_terms(array(
        'taxonomy' => 'product_cat',
        'hide_empty' => false,
        'orderby' => 'name',
        'order' => 'ASC',
        'exclude' => array($term->term_id) // Исключаем текущую категорию
    ));
    ?>
    
    <!-- Фотография категории -->
    <tr class="form-field term-category-photo-wrap">
        <th scope="row">
            <label for="category_photo">Фотография категории</label>
        </th>
        <td>
            <div id="category_photo_container">
                <div id="category_photo_preview" style="margin-bottom: 15px;">
                    <?php if ($photo_url): ?>
                        <img src="<?php echo $photo_url; ?>" style="width: 200px; height: 120px; object-fit: cover; border-radius: 4px;" alt="Изображение товара категории">
                    <?php else: ?>
                        <div style="width: 200px; height: 120px; background: #f0f0f0; border: 2px dashed #ccc; display: flex; align-items: center; justify-content: center; border-radius: 4px;">
                            <span style="color: #666;">Фото не выбрано</span>
                        </div>
                    <?php endif; ?>
                </div>
                
                <button type="button" id="select-category-photo" class="button">Выбрать фотографию</button>
                <button type="button" id="remove-category-photo" class="button" style="<?php echo $category_photo ? '' : 'display: none;'; ?>">Удалить фото</button>
                <input type="hidden" id="category_photo_id" name="category_photo" value="<?php echo $category_photo; ?>">
            </div>
            <p class="description">Основное изображение категории. Рекомендуемый размер: 600x400px.</p>
        </td>
    </tr>

    <!-- Выводить в шапке -->
    <tr class="form-field term-show-in-header-wrap">
        <th scope="row">
            <label for="show_in_header">Выводить в шапке</label>
        </th>
        <td>
            <label for="show_in_header">
                <input type="checkbox" id="show_in_header" name="show_in_header" value="1" <?php checked($show_in_header, '1'); ?>>
                Отображать категорию в навигации шапки сайта
            </label>
            <p class="description">Отмеченные категории будут отображаться в навигации шапки сайта.</p>
        </td>
    </tr>

    <!-- Связанные категории -->
    <tr class="form-field term-related-categories-wrap">
        <th scope="row">
            <label for="related_categories">Связанные категории</label>
        </th>
        <td>
            <div id="related_categories_container">
                <p style="margin-bottom: 10px; font-weight: 500;">Выберите до 6 связанных категорий:</p>
                <div style="max-height: 200px; overflow-y: auto; border: 1px solid #ddd; padding: 10px; background: #fafafa;">
                    <?php if (!empty($all_categories)): ?>
                        <?php foreach ($all_categories as $cat): ?>
                            <label style="display: block; margin-bottom: 5px;">
                                <input type="checkbox" 
                                       name="related_categories[]" 
                                       value="<?php echo $cat->term_id; ?>" 
                                       class="related-category-checkbox"
                                       <?php checked(in_array($cat->term_id, $related_categories)); ?>>
                                <?php echo esc_html($cat->name); ?>
                            </label>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                
                <?php if (!empty($related_categories)): ?>
                    <div style="margin-top: 15px;">
                        <strong>Выбранные категории (<?php echo count($related_categories); ?>/6):</strong>
                        <ul style="margin-top: 5px;">
                            <?php foreach ($related_categories as $rel_cat_id): ?>
                                <?php $rel_cat = get_term($rel_cat_id, 'product_cat'); ?>
                                <?php if ($rel_cat && !is_wp_error($rel_cat)): ?>
                                    <li><?php echo esc_html($rel_cat->name); ?></li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                
                <p class="description">Выбранные категории будут отображаться в блоке "А еще Вам может пригодиться" на странице архива этой категории. Максимум 6 категорий.</p>
            </div>
        </td>
    </tr>

    <script type="text/javascript">
    jQuery(document).ready(function($) {
        var mediaUploader;
        
        // Медиа загрузчик для фотографии
        $('#select-category-photo').on('click', function(e) {
            e.preventDefault();
            
            if (mediaUploader) {
                mediaUploader.open();
                return;
            }
            
            mediaUploader = wp.media({
                title: 'Выберите фотографию категории',
                button: {
                    text: 'Использовать это изображение'
                },
                multiple: false
            });
            
            mediaUploader.on('select', function() {
                var attachment = mediaUploader.state().get('selection').first().toJSON();
                
                $('#category_photo_id').val(attachment.id);
                $('#category_photo_preview').html('<img src="' + attachment.sizes.medium.url + '" style="width: 200px; height: 120px; object-fit: cover; border-radius: 4px;">');
                $('#remove-category-photo').show();
            });
            
            mediaUploader.open();
        });
        
        $('#remove-category-photo').on('click', function() {
            $('#category_photo_id').val('');
            $('#category_photo_preview').html('<div style="width: 200px; height: 120px; background: #f0f0f0; border: 2px dashed #ccc; display: flex; align-items: center; justify-content: center; border-radius: 4px;"><span style="color: #666;">Фото не выбрано</span></div>');
            $(this).hide();
        });
        
        // Ограничение выбора связанных категорий
        function updateRelatedCategoriesLimit() {
            var checkedBoxes = $('.related-category-checkbox:checked').length;
            
            if (checkedBoxes >= 6) {
                $('.related-category-checkbox:not(:checked)').prop('disabled', true);
            } else {
                $('.related-category-checkbox').prop('disabled', false);
            }
        }
        
        // Инициализируем ограничение при загрузке
        updateRelatedCategoriesLimit();
        
        // Обновляем ограничение при изменении
        $('.related-category-checkbox').on('change', function() {
            updateRelatedCategoriesLimit();
        });
    });
    </script>
    <?php
}

// Сохранение кастомных полей при создании категории
add_action('created_product_cat', 'save_category_custom_fields');

// Сохранение кастомных полей при редактировании категории
add_action('edited_product_cat', 'save_category_custom_fields');

function save_category_custom_fields($term_id) {
    // Сохраняем фотографию категории
    if (isset($_POST['category_photo'])) {
        $category_photo = intval($_POST['category_photo']);
        if ($category_photo) {
            update_term_meta($term_id, 'category_photo', $category_photo);
        } else {
            delete_term_meta($term_id, 'category_photo');
        }
    }
    
    // Сохраняем настройку "выводить в шапке"
    if (isset($_POST['show_in_header']) && $_POST['show_in_header'] == '1') {
        update_term_meta($term_id, 'show_in_header', '1');
    } else {
        delete_term_meta($term_id, 'show_in_header');
    }
    
    // Сохраняем связанные категории
    if (isset($_POST['related_categories']) && is_array($_POST['related_categories'])) {
        $related_categories = array_map('intval', $_POST['related_categories']);
        // Ограничиваем до 6 категорий
        $related_categories = array_slice($related_categories, 0, 6);
        update_term_meta($term_id, 'related_categories', $related_categories);
    } else {
        delete_term_meta($term_id, 'related_categories');
    }
}

// Добавление колонок в список категорий
add_filter('manage_edit-product_cat_columns', 'add_category_custom_columns');

function add_category_custom_columns($columns) {
    // Добавляем колонку для фотографии категории
    $new_columns = array();
    foreach ($columns as $key => $column) {
        $new_columns[$key] = $column;
        if ($key === 'thumb') {
            $new_columns['category_photo'] = 'Фотография';
        }
    }
    
    // Добавляем колонки
    $new_columns['show_in_header'] = 'В шапке';
    $new_columns['related_categories_count'] = 'Связанные';
    
    return $new_columns;
}

// Заполнение кастомных колонок
add_filter('manage_product_cat_custom_column', 'fill_category_custom_columns', 10, 3);

function fill_category_custom_columns($content, $column_name, $term_id) {
    switch ($column_name) {
        case 'category_photo':
            $category_photo = get_term_meta($term_id, 'category_photo', true);
            if ($category_photo) {
                $image = wp_get_attachment_image($category_photo, array(50, 50), false, array('style' => 'width: 50px; height: 50px; object-fit: cover; border-radius: 4px;'));
                $content = $image ?: 'Нет фото';
            } else {
                $content = 'Нет фото';
            }
            break;
            
        case 'show_in_header':
            $show_in_header = get_term_meta($term_id, 'show_in_header', true);
            $content = $show_in_header ? '<span style="color: green;">✓ Да</span>' : '<span style="color: #ccc;">— Нет</span>';
            break;
            
        case 'related_categories_count':
            $related_categories = get_term_meta($term_id, 'related_categories', true);
            if (is_array($related_categories) && !empty($related_categories)) {
                $count = count($related_categories);
                $content = '<span style="color: #0073aa;">' . $count . '/6</span>';
            } else {
                $content = '<span style="color: #ccc;">0/6</span>';
            }
            break;
    }
    
    return $content;
}

// Функции для получения данных категории
function get_category_photo($term_id, $size = 'medium') {
    $photo_id = get_term_meta($term_id, 'category_photo', true);
    if ($photo_id) {
        return wp_get_attachment_image_src($photo_id, $size);
    }
    return false;
}

function get_category_photo_url($term_id, $size = 'medium') {
    $photo = get_category_photo($term_id, $size);
    return $photo ? $photo[0] : '';
}

function is_category_in_header($term_id) {
    return get_term_meta($term_id, 'show_in_header', true) === '1';
}

function get_related_categories($term_id) {
    $related_categories = get_term_meta($term_id, 'related_categories', true);
    
    if (!is_array($related_categories) || empty($related_categories)) {
        return array();
    }
    
    $categories = array();
    foreach ($related_categories as $cat_id) {
        $category = get_term($cat_id, 'product_cat');
        if ($category && !is_wp_error($category)) {
            $categories[] = $category;
        }
    }
    
    return $categories;
}

// Функция для получения категорий для шапки
function get_header_categories() {
    $args = array(
        'taxonomy' => 'product_cat',
        'hide_empty' => false,
        'meta_query' => array(
            array(
                'key' => 'show_in_header',
                'value' => '1',
                'compare' => '='
            )
        )
    );
    
    return get_terms($args);
}

// Добавляем поля раскрывающегося текста при редактировании категории
add_action('product_cat_edit_form_fields', 'edit_category_expanding_text_fields', 20);

function edit_category_expanding_text_fields($term) {
    // Получаем существующие значения
    $section_title = get_term_meta($term->term_id, 'expanding_section_title', true);
    $background_color = get_term_meta($term->term_id, 'expanding_background_color', true) ?: 'white';
    $main_content = get_term_meta($term->term_id, 'expanding_main_content', true);
    $additional_content = get_term_meta($term->term_id, 'expanding_additional_content', true);
    $button_text = get_term_meta($term->term_id, 'expanding_button_text', true) ?: 'Читать далее';
    $button_text_collapse = get_term_meta($term->term_id, 'expanding_button_text_collapse', true) ?: 'Свернуть';
    ?>
    
    <!-- Раскрывающий текст -->
    <tr>
        <th colspan="2">
            <h3 style="margin: 30px 0 20px 0; padding: 10px 0; border-bottom: 1px solid #ddd;">Раскрывающий текст</h3>
        </th>
    </tr>

    <!-- Заголовок секции -->
    <tr class="form-field">
        <th scope="row">
            <label for="expanding_section_title">Заголовок секции</label>
        </th>
        <td>
            <input type="text" id="expanding_section_title" name="expanding_section_title" value="<?php echo esc_attr($section_title); ?>">
            <p class="description">Основной заголовок блока раскрывающегося текста.</p>
        </td>
    </tr>

    <!-- Фон секции -->
    <tr class="form-field">
        <th scope="row">
            <label for="expanding_background_color">Фон секции</label>
        </th>
        <td>
            <select id="expanding_background_color" name="expanding_background_color">
                <option value="white" <?php selected($background_color, 'white'); ?>>Белый</option>
                <option value="grey" <?php selected($background_color, 'grey'); ?>>Серый</option>
            </select>
            <p class="description">Выберите цвет фона для секции.</p>
        </td>
    </tr>

    <!-- Основной контент -->
    <tr class="form-field">
        <th scope="row">
            <label for="expanding_main_content">Основной контент</label>
        </th>
        <td>
            <?php
            wp_editor($main_content, 'expanding_main_content', array(
                'textarea_name' => 'expanding_main_content',
                'media_buttons' => true,
                'textarea_rows' => 10,
                'teeny' => false,
                'quicktags' => true
            ));
            ?>
            <p class="description">Основной текст, который всегда виден.</p>

            <!-- HTML подсказки -->
            <div style="margin-top: 10px; padding: 10px; background: #f9f9f9; border-left: 4px solid #0073aa;">
                <strong>HTML теги которые можно использовать:</strong><br>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-top: 8px;">
                    <div>
                        <code>&lt;p&gt;Абзац&lt;/p&gt;</code><br>
                        <code>&lt;strong&gt;Жирный&lt;/strong&gt;</code><br>
                        <code>&lt;em&gt;Курсив&lt;/em&gt;</code><br>
                        <code>&lt;br&gt;</code> - перенос строки
                    </div>
                    <div>
                        <code>&lt;a href="URL"&gt;Ссылка&lt;/a&gt;</code><br>
                        <code>&lt;ul&gt;&lt;li&gt;Список&lt;/li&gt;&lt;/ul&gt;</code><br>
                        <code>&lt;h2&gt;Заголовок&lt;/h2&gt;</code><br>
                        <code>&lt;h3&gt;Подзаголовок&lt;/h3&gt;</code>
                    </div>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr; margin-top: 8px;">
                    <div>
                        <strong>Пример добавления изображения:</strong><br>
                        <!-- Базовый пример -->
                        <div style="margin: 10px 0; padding: 8px; background: #fff3cd; border-radius: 4px;">
                            <strong>Простое изображение:</strong><br>
                            <code>&lt;img src="URL_изображения" alt="Описание" width="300" height="200" /&gt;</code>
                        </div>

                        <!-- Выравнивание -->
                        <div style="margin: 10px 0; padding: 8px; background: #d1ecf1; border-radius: 4px;">
                            <strong>Выравнивание изображений:</strong><br>

                            <div style="margin: 5px 0;">
                                <strong>По центру:</strong><br>
                                <code>&lt;img src="URL" alt="Описание" width="300" height="200" class="aligncenter" /&gt;</code>
                            </div>
                            
                            <div style="margin: 5px 0;">
                                <strong>По правому краю:</strong><br>
                                <code>&lt;img src="URL" alt="Описание" width="300" height="200" class="alignright" /&gt;</code>
                            </div>
                        </div>
                    </div>      
                </div>
            </div>
        </td>
        
    </tr>

    <!-- Дополнительный контент -->
    <tr class="form-field">
        <th scope="row">
            <label for="expanding_additional_content">Дополнительный контент</label>
        </th>
        <td>
            <?php
            wp_editor($additional_content, 'expanding_additional_content', array(
                'textarea_name' => 'expanding_additional_content',
                'media_buttons' => true,
                'textarea_rows' => 10,
                'teeny' => false,
                'quicktags' => true
            ));
            ?>
            <p class="description">Текст, который будет скрыт под кнопкой "Читать далее".</p>
            <!-- Пример -->
            <div style="margin-top: 10px; padding: 10px; background: #f0f8ff; border-left: 4px solid #008000;">
                <strong>Пример текста:</strong><br>
                <code>&lt;p&gt;Наша компания &lt;strong&gt;более 10 лет&lt;/strong&gt; работает на рынке.&lt;/p&gt;<br>
                &lt;p&gt;Мы предлагаем:&lt;/p&gt;<br>
                &lt;ul&gt;<br>
                &nbsp;&nbsp;&lt;li&gt;Качественную продукцию&lt;/li&gt;<br>
                &nbsp;&nbsp;&lt;li&gt;Быструю доставку&lt;/li&gt;<br>
                &lt;/ul&gt;</code>
            </div>
        </td>
    </tr>
    <?php
}

// Обновляем функцию сохранения, добавляя новые поля
add_action('created_product_cat', 'save_category_expanding_text_fields');
add_action('edited_product_cat', 'save_category_expanding_text_fields');

function save_category_expanding_text_fields($term_id) {
    // Сохраняем заголовок секции
    if (isset($_POST['expanding_section_title'])) {
        $section_title = sanitize_text_field($_POST['expanding_section_title']);
        if (!empty($section_title)) {
            update_term_meta($term_id, 'expanding_section_title', $section_title);
        } else {
            delete_term_meta($term_id, 'expanding_section_title');
        }
    }

    // Сохраняем фон секции
    if (isset($_POST['expanding_background_color'])) {
        $background_color = sanitize_text_field($_POST['expanding_background_color']);
        update_term_meta($term_id, 'expanding_background_color', $background_color);
    }

    // Сохраняем основной контент
    if (isset($_POST['expanding_main_content'])) {
        $main_content = wp_kses_post($_POST['expanding_main_content']);
        if (!empty($main_content)) {
            update_term_meta($term_id, 'expanding_main_content', $main_content);
        } else {
            delete_term_meta($term_id, 'expanding_main_content');
        }
    }

    // Сохраняем дополнительный контент
    if (isset($_POST['expanding_additional_content'])) {
        $additional_content = wp_kses_post($_POST['expanding_additional_content']);
        if (!empty($additional_content)) {
            update_term_meta($term_id, 'expanding_additional_content', $additional_content);
        } else {
            delete_term_meta($term_id, 'expanding_additional_content');
        }
    }

    // Сохраняем текст кнопки "Показать"
    if (isset($_POST['expanding_button_text'])) {
        $button_text = sanitize_text_field($_POST['expanding_button_text']);
        update_term_meta($term_id, 'expanding_button_text', $button_text);
    }

    // Сохраняем текст кнопки "Скрыть"
    if (isset($_POST['expanding_button_text_collapse'])) {
        $button_text_collapse = sanitize_text_field($_POST['expanding_button_text_collapse']);
        update_term_meta($term_id, 'expanding_button_text_collapse', $button_text_collapse);
    }
}

// Добавляем колонку в список категорий для раскрывающегося текста
add_filter('manage_edit-product_cat_columns', 'add_expanding_text_column');

function add_expanding_text_column($columns) {
    $columns['expanding_text'] = 'Раскр. текст';
    return $columns;
}

// Заполняем колонку раскрывающегося текста
add_filter('manage_product_cat_custom_column', 'fill_expanding_text_column', 10, 3);

function fill_expanding_text_column($content, $column_name, $term_id) {
    if ($column_name === 'expanding_text') {
        $section_title = get_term_meta($term_id, 'expanding_section_title', true);
        $main_content = get_term_meta($term_id, 'expanding_main_content', true);
        $additional_content = get_term_meta($term_id, 'expanding_additional_content', true);
        
        if (!empty($section_title) || !empty($main_content) || !empty($additional_content)) {
            $content = '<span style="color: green;">✓ Настроен</span>';
        } else {
            $content = '<span style="color: #ccc;">— Не настроен</span>';
        }
    }
    
    return $content;
}

// Функции для получения данных раскрывающегося текста категории
function get_category_expanding_text_data($term_id) {
    return array(
        'section_title' => get_term_meta($term_id, 'expanding_section_title', true),
        'background_color' => get_term_meta($term_id, 'expanding_background_color', true) ?: 'white',
        'main_content' => get_term_meta($term_id, 'expanding_main_content', true),
        'additional_content' => get_term_meta($term_id, 'expanding_additional_content', true),
    );
}

function has_category_expanding_text($term_id) {
    $data = get_category_expanding_text_data($term_id);
    return !empty($data['section_title']) || !empty($data['main_content']) || !empty($data['additional_content']);
}


/**
 * Добавление текстового блока для категорий товаров WooCommerce
 * Добавить этот код в файл paste-2.txt после существующих полей
 */

// Добавляем поля текстового блока при редактировании категории
add_action('product_cat_edit_form_fields', 'edit_category_text_block_fields', 25);

function edit_category_text_block_fields($term) {
    // Получаем существующие значения
    $text_block_content = get_term_meta($term->term_id, 'text_block_content', true);
    $text_block_background_color = get_term_meta($term->term_id, 'text_block_background_color', true) ?: 'white';
    $text_block_container_width = get_term_meta($term->term_id, 'text_block_container_width', true) ?: '12';
    $text_block_columns_count = get_term_meta($term->term_id, 'text_block_columns_count', true) ?: '1';
    $text_block_text_alignment = get_term_meta($term->term_id, 'text_block_text_alignment', true) ?: 'left';
    $text_block_second_column_content = get_term_meta($term->term_id, 'text_block_second_column_content', true);
    ?>
    
    <!-- Текстовый блок -->
    <tr>
        <th colspan="2">
            <h3 style="margin: 30px 0 20px 0; padding: 10px 0; border-bottom: 1px solid #ddd;">Текстовый блок</h3>
        </th>
    </tr>

    <!-- Фон секции -->
    <tr class="form-field">
        <th scope="row">
            <label for="text_block_background_color">Фон текстового блока</label>
        </th>
        <td>
            <select id="text_block_background_color" name="text_block_background_color">
                <option value="white" <?php selected($text_block_background_color, 'white'); ?>>Белый</option>
                <option value="grey" <?php selected($text_block_background_color, 'grey'); ?>>Серый</option>
            </select>
            <p class="description">Выберите цвет фона для текстового блока.</p>
        </td>
    </tr>

    <!-- Ширина контейнера -->
    <tr class="form-field">
        <th scope="row">
            <label for="text_block_container_width">Ширина контейнера</label>
        </th>
        <td>
            <select id="text_block_container_width" name="text_block_container_width">
                <option value="6" <?php selected($text_block_container_width, '6'); ?>>6/12 (50%)</option>
                <option value="8" <?php selected($text_block_container_width, '8'); ?>>8/12 (67%)</option>
                <option value="10" <?php selected($text_block_container_width, '10'); ?>>10/12 (83%)</option>
                <option value="12" <?php selected($text_block_container_width, '12'); ?>>12/12 (100%)</option>
            </select>
            <p class="description">Ширина контейнера контента относительно общей ширины.</p>
        </td>
    </tr>

    <!-- Количество колонок -->
    <tr class="form-field">
        <th scope="row">
            <label for="text_block_columns_count">Количество колонок</label>
        </th>
        <td>
            <select id="text_block_columns_count" name="text_block_columns_count" onchange="toggleSecondColumn(this.value)">
                <option value="1" <?php selected($text_block_columns_count, '1'); ?>>Одна колонка</option>
                <option value="2" <?php selected($text_block_columns_count, '2'); ?>>Две колонки</option>
            </select>
            <p class="description">Выберите количество колонок для контента.</p>
        </td>
    </tr>

    <!-- Выравнивание текста (только для одной колонки) -->
    <tr class="form-field text-alignment-row" style="<?php echo $text_block_columns_count === '2' ? 'display: none;' : ''; ?>">
        <th scope="row">
            <label for="text_block_text_alignment">Выравнивание текста</label>
        </th>
        <td>
            <select id="text_block_text_alignment" name="text_block_text_alignment">
                <option value="left" <?php selected($text_block_text_alignment, 'left'); ?>>По левому краю</option>
                <option value="center" <?php selected($text_block_text_alignment, 'center'); ?>>По центру</option>
                <option value="right" <?php selected($text_block_text_alignment, 'right'); ?>>По правому краю</option>
            </select>
            <p class="description">Выравнивание текста в одной колонке.</p>
        </td>
    </tr>

    <!-- Основной контент (первая колонка) -->
    <tr class="form-field">
        <th scope="row">
            <label for="text_block_content">Контент <?php echo $text_block_columns_count === '2' ? '(первая колонка)' : ''; ?></label>
        </th>
        <td>
            <?php
            wp_editor($text_block_content, 'text_block_content', array(
                'textarea_name' => 'text_block_content',
                'media_buttons' => true,
                'textarea_rows' => 10,
                'teeny' => false,
                'quicktags' => true
            ));
            ?>
            <p class="description">Основной текстовый контент блока.</p>

            <!-- HTML подсказки -->
            <div style="margin-top: 10px; padding: 10px; background: #f9f9f9; border-left: 4px solid #0073aa;">
                <strong>HTML теги которые можно использовать:</strong><br>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-top: 8px;">
                    <div>
                        <code>&lt;p&gt;Абзац&lt;/p&gt;</code><br>
                        <code>&lt;strong&gt;Жирный&lt;/strong&gt;</code><br>
                        <code>&lt;em&gt;Курсив&lt;/em&gt;</code><br>
                        <code>&lt;br&gt;</code> - перенос строки
                    </div>
                    <div>
                        <code>&lt;a href="URL"&gt;Ссылка&lt;/a&gt;</code><br>
                        <code>&lt;ul&gt;&lt;li&gt;Список&lt;/li&gt;&lt;/ul&gt;</code><br>
                        <code>&lt;h2&gt;Заголовок&lt;/h2&gt;</code><br>
                        <code>&lt;h3&gt;Подзаголовок&lt;/h3&gt;</code>
                    </div>
                </div>
                
                <div style="margin-top: 8px;">
                    <strong>Пример добавления изображения:</strong><br>
                    <code>&lt;img src="URL_изображения" alt="Описание" width="300" height="200" class="aligncenter" /&gt;</code>
                </div>
            </div>
        </td>
    </tr>

    <!-- Вторая колонка (показывается только при выборе "Две колонки") -->
    <tr class="form-field second-column-row" style="<?php echo $text_block_columns_count === '1' ? 'display: none;' : ''; ?>">
        <th scope="row">
            <label for="text_block_second_column_content">Контент второй колонки</label>
        </th>
        <td>
            <?php
            wp_editor($text_block_second_column_content, 'text_block_second_column_content', array(
                'textarea_name' => 'text_block_second_column_content',
                'media_buttons' => true,
                'textarea_rows' => 10,
                'teeny' => false,
                'quicktags' => true
            ));
            ?>
            <p class="description">Контент для второй колонки (отображается только при выборе "Две колонки").</p>
        </td>
    </tr>

    <script type="text/javascript">
    function toggleSecondColumn(value) {
        var secondColumnRow = document.querySelector('.second-column-row');
        var textAlignmentRow = document.querySelector('.text-alignment-row');
        var contentLabel = document.querySelector('label[for="text_block_content"]');
        
        if (value === '2') {
            secondColumnRow.style.display = '';
            textAlignmentRow.style.display = 'none';
            contentLabel.innerHTML = 'Контент (первая колонка)';
        } else {
            secondColumnRow.style.display = 'none';
            textAlignmentRow.style.display = '';
            contentLabel.innerHTML = 'Контент';
        }
    }
    </script>
    <?php
}

// Добавляем поля при создании категории
add_action('product_cat_add_form_fields', 'add_category_text_block_fields');

function add_category_text_block_fields() {
    ?>
    <!-- Текстовый блок -->
    <div class="form-field">
        <h3 style="margin: 30px 0 20px 0; padding: 10px 0; border-bottom: 1px solid #ddd;">Текстовый блок</h3>
    </div>

    <!-- Фон секции -->
    <div class="form-field">
        <label for="text_block_background_color">Фон текстового блока</label>
        <select id="text_block_background_color" name="text_block_background_color">
            <option value="white">Белый</option>
            <option value="grey">Серый</option>
        </select>
        <p class="description">Выберите цвет фона для текстового блока.</p>
    </div>

    <!-- Ширина контейнера -->
    <div class="form-field">
        <label for="text_block_container_width">Ширина контейнера</label>
        <select id="text_block_container_width" name="text_block_container_width">
            <option value="6">6/12 (50%)</option>
            <option value="8">8/12 (67%)</option>
            <option value="10">10/12 (83%)</option>
            <option value="12" selected>12/12 (100%)</option>
        </select>
        <p class="description">Ширина контейнера контента относительно общей ширины.</p>
    </div>

    <!-- Количество колонок -->
    <div class="form-field">
        <label for="text_block_columns_count">Количество колонок</label>
        <select id="text_block_columns_count" name="text_block_columns_count" onchange="toggleSecondColumnAdd(this.value)">
            <option value="1" selected>Одна колонка</option>
            <option value="2">Две колонки</option>
        </select>
        <p class="description">Выберите количество колонок для контента.</p>
    </div>

    <!-- Выравнивание текста -->
    <div class="form-field text-alignment-add-row">
        <label for="text_block_text_alignment">Выравнивание текста</label>
        <select id="text_block_text_alignment" name="text_block_text_alignment">
            <option value="left" selected>По левому краю</option>
            <option value="center">По центру</option>
            <option value="right">По правому краю</option>
        </select>
        <p class="description">Выравнивание текста в одной колонке.</p>
    </div>

    <!-- Основной контент -->
    <div class="form-field">
        <label for="text_block_content">Контент</label>
        <textarea id="text_block_content" name="text_block_content" rows="8" style="width: 100%;"></textarea>
        <p class="description">Основной текстовый контент блока. Можно использовать HTML.</p>
    </div>

    <!-- Вторая колонка -->
    <div class="form-field second-column-add-row" style="display: none;">
        <label for="text_block_second_column_content">Контент второй колонки</label>
        <textarea id="text_block_second_column_content" name="text_block_second_column_content" rows="8" style="width: 100%;"></textarea>
        <p class="description">Контент для второй колонки.</p>
    </div>

    <script type="text/javascript">
    function toggleSecondColumnAdd(value) {
        var secondColumnRow = document.querySelector('.second-column-add-row');
        var textAlignmentRow = document.querySelector('.text-alignment-add-row');
        var contentLabel = document.querySelector('label[for="text_block_content"]');
        
        if (value === '2') {
            secondColumnRow.style.display = 'block';
            textAlignmentRow.style.display = 'none';
            contentLabel.innerHTML = 'Контент (первая колонка)';
        } else {
            secondColumnRow.style.display = 'none';
            textAlignmentRow.style.display = 'block';
            contentLabel.innerHTML = 'Контент';
        }
    }
    </script>
    <?php
}

// Обновляем функцию сохранения, добавляя новые поля
add_action('created_product_cat', 'save_category_text_block_fields');
add_action('edited_product_cat', 'save_category_text_block_fields');

function save_category_text_block_fields($term_id) {
    // Сохраняем фон блока
    if (isset($_POST['text_block_background_color'])) {
        $background_color = sanitize_text_field($_POST['text_block_background_color']);
        update_term_meta($term_id, 'text_block_background_color', $background_color);
    }

    // Сохраняем ширину контейнера
    if (isset($_POST['text_block_container_width'])) {
        $container_width = sanitize_text_field($_POST['text_block_container_width']);
        update_term_meta($term_id, 'text_block_container_width', $container_width);
    }

    // Сохраняем количество колонок
    if (isset($_POST['text_block_columns_count'])) {
        $columns_count = sanitize_text_field($_POST['text_block_columns_count']);
        update_term_meta($term_id, 'text_block_columns_count', $columns_count);
    }

    // Сохраняем выравнивание текста
    if (isset($_POST['text_block_text_alignment'])) {
        $text_alignment = sanitize_text_field($_POST['text_block_text_alignment']);
        update_term_meta($term_id, 'text_block_text_alignment', $text_alignment);
    }

    // Сохраняем основной контент
    if (isset($_POST['text_block_content'])) {
        $content = wp_kses_post($_POST['text_block_content']);
        if (!empty($content)) {
            update_term_meta($term_id, 'text_block_content', $content);
        } else {
            delete_term_meta($term_id, 'text_block_content');
        }
    }

    // Сохраняем контент второй колонки
    if (isset($_POST['text_block_second_column_content'])) {
        $second_column_content = wp_kses_post($_POST['text_block_second_column_content']);
        if (!empty($second_column_content)) {
            update_term_meta($term_id, 'text_block_second_column_content', $second_column_content);
        } else {
            delete_term_meta($term_id, 'text_block_second_column_content');
        }
    }
}

// Добавляем колонку в список категорий для текстового блока
add_filter('manage_edit-product_cat_columns', 'add_text_block_column');

function add_text_block_column($columns) {
    $columns['text_block'] = 'Текст. блок';
    return $columns;
}

// Заполняем колонку текстового блока
add_filter('manage_product_cat_custom_column', 'fill_text_block_column', 10, 3);

function fill_text_block_column($content, $column_name, $term_id) {
    if ($column_name === 'text_block') {
        $text_content = get_term_meta($term_id, 'text_block_content', true);
        $second_column_content = get_term_meta($term_id, 'text_block_second_column_content', true);
        
        if (!empty($text_content) || !empty($second_column_content)) {
            $content = '<span style="color: green;">✓ Настроен</span>';
        } else {
            $content = '<span style="color: #ccc;">— Не настроен</span>';
        }
    }
    
    return $content;
}

// Функции для получения данных текстового блока категории
function get_category_text_block_data($term_id) {
    return array(
        'content' => get_term_meta($term_id, 'text_block_content', true),
        'background_color' => get_term_meta($term_id, 'text_block_background_color', true) ?: 'white',
        'container_width' => get_term_meta($term_id, 'text_block_container_width', true) ?: '12',
        'columns_count' => get_term_meta($term_id, 'text_block_columns_count', true) ?: '1',
        'text_alignment' => get_term_meta($term_id, 'text_block_text_alignment', true) ?: 'left',
        'second_column_content' => get_term_meta($term_id, 'text_block_second_column_content', true),
    );
}

function has_category_text_block($term_id) {
    $data = get_category_text_block_data($term_id);
    return !empty($data['content']) || !empty($data['second_column_content']);
}

/**
 * Выводит текстовый блок для архива категории используя шаблон text-only
 */
function render_archive_text_block($category_id = null) {
    if (!$category_id) {
        $current_category = get_queried_object();
        if ($current_category && isset($current_category->term_id)) {
            $category_id = $current_category->term_id;
        } else {
            return;
        }
    }

    // Проверяем, есть ли данные для вывода
    if (!has_category_text_block($category_id)) {
        return;
    }

    // Получаем данные текстового блока из категории
    $category_text_data = get_category_text_block_data($category_id);

    // Временно устанавливаем данные для ACF фильтра
    global $temp_text_block_data;
    $temp_text_block_data = $category_text_data;

    // Создаем блок как в Gutenberg
    global $block;
    $block = array(
        'id' => uniqid('text-block-'),
        'className' => ''
    );

    // Добавляем фильтр для подмены ACF данных
    add_filter('acf/load_value', 'temp_text_block_acf_filter', 10, 3);

    // Подключаем шаблон текстового блока
    $template_path = get_template_directory() . '/template-parts/blocks/text-only.php';
    if (file_exists($template_path)) {
        include $template_path;
    }

    // Очищаем временные данные
    $temp_text_block_data = null;
    $block = null;
    remove_filter('acf/load_value', 'temp_text_block_acf_filter', 10);
}

/**
 * Фильтр для подмены ACF данных текстового блока
 */
function temp_text_block_acf_filter($value, $post_id, $field) {
    global $temp_text_block_data;

    if ($temp_text_block_data && isset($field['name'])) {
        switch ($field['name']) {
            case 'content':
                return $temp_text_block_data['content'];
            case 'background_color':
                return $temp_text_block_data['background_color'];
            case 'container_width':
                return $temp_text_block_data['container_width'];
            case 'columns_count':
                return $temp_text_block_data['columns_count'];
            case 'text_alignment':
                return $temp_text_block_data['text_alignment'];
            case 'second_column_content':
                return $temp_text_block_data['second_column_content'];
        }
    }

    return $value;
}

/**
 * Выводит текстовый блок для товара из выбранной категории
 */
function render_product_text_block($product_id) {
    // Получаем настройки товара - из какой категории брать текстовый блок
    $text_block_settings = get_field('text_block_settings', $product_id);

    if (!$text_block_settings || !isset($text_block_settings['category_source']) || empty($text_block_settings['category_source'])) {
        return;
    }

    $category_id = $text_block_settings['category_source'];

    // Проверяем, что категория действительно связана с товаром
    $product_categories = wp_get_post_terms($product_id, 'product_cat', array('fields' => 'ids'));

    if (!in_array($category_id, $product_categories)) {
        return; // Категория не связана с товаром
    }

    // Проверяем, есть ли данные для вывода
    if (!has_category_text_block($category_id)) {
        return;
    }

    // Получаем данные текстового блока из категории
    $category_text_data = get_category_text_block_data($category_id);

    // Временно устанавливаем данные для ACF фильтра
    global $temp_text_block_data;
    $temp_text_block_data = $category_text_data;

    // Создаем блок как в Gutenberg
    global $block;
    $block = array(
        'id' => uniqid('text-block-'),
        'className' => ''
    );

    // Добавляем фильтр для подмены ACF данных
    add_filter('acf/load_value', 'temp_text_block_acf_filter', 10, 3);

    // Подключаем шаблон текстового блока
    $template_path = get_template_directory() . '/template-parts/blocks/text-only.php';
    if (file_exists($template_path)) {
        include $template_path;
    }

    // Очищаем временные данные
    $temp_text_block_data = null;
    $block = null;
    remove_filter('acf/load_value', 'temp_text_block_acf_filter', 10);
}


?>