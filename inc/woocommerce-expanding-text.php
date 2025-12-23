<?php
/**
 * Раскрывающийся текст для товаров и категорий WooCommerce
 * 
 * @package svetogor
 */

if (!defined('ABSPATH')) {
    exit;
}

// ============================================================================
// Вывод раскрывающегося текста
// ============================================================================

/**
 * Выводит раскрывающий текст для товара из выбранной категории
 * Фон всегда серый (bg-grey)
 */
function render_product_expanding_text($product_id) {
    // Получаем настройки товара - из какой категории брать текст
    $expanding_text_settings = get_field('expanding_text_settings', $product_id);

    if (!$expanding_text_settings || !isset($expanding_text_settings['category_source']) || empty($expanding_text_settings['category_source'])) {
        return;
    }

    $category_id = $expanding_text_settings['category_source'];

    // Проверяем, что категория действительно связана с товаром
    $product_categories = wp_get_post_terms($product_id, 'product_cat', array('fields' => 'ids'));

    if (!in_array($category_id, $product_categories)) {
        return;
    }

    // Получаем данные раскрывающегося текста из категории
    $category_expanding_data = get_category_expanding_text_data($category_id);

    // Проверяем, есть ли данные для вывода
    if (!has_category_expanding_text($category_id)) {
        return;
    }

    // ПРИНУДИТЕЛЬНО устанавливаем серый фон для товара
    $category_expanding_data['background_color'] = 'grey';

    // Проверяем, есть ли переопределение заголовка в настройках товара
    if (!empty($expanding_text_settings['custom_section_title'])) {
        $category_expanding_data['section_title'] = $expanding_text_settings['custom_section_title'];
    }

    // Добавляем кнопку по умолчанию если её нет
    if (empty($category_expanding_data['button_text'])) {
        $category_expanding_data['button_text'] = 'Читать далее';
    }

    // Временно устанавливаем данные для ACF фильтра
    global $temp_expanding_text_data;
    $temp_expanding_text_data = $category_expanding_data;

    // Создаем блок как в Gutenberg
    global $block;
    $block = array(
        'id' => uniqid('expanding-text-'),
        'className' => ''
    );

    // Добавляем фильтр для подмены ACF данных
    add_filter('acf/load_value', 'temp_expanding_text_acf_filter', 10, 3);

    // Подключаем шаблон раскрывающегося текста
    $template_path = get_template_directory() . '/template-parts/blocks/general-info/general-info.php';
    if (file_exists($template_path)) {
        include $template_path;
    }

    // Очищаем временные данные
    $temp_expanding_text_data = null;
    $block = null;
    remove_filter('acf/load_value', 'temp_expanding_text_acf_filter', 10);
}

/**
 * Фильтр для подмены ACF данных раскрывающегося текста товара
 */
function temp_expanding_text_acf_filter($value, $post_id, $field) {
    global $temp_expanding_text_data;

    if ($temp_expanding_text_data && isset($field['name'])) {
        switch ($field['name']) {
            case 'section_title':
            case 'section_title_general_info':
                return $temp_expanding_text_data['section_title'];
            case 'background_color':
            case 'background_color_general_info':
                return 'grey';
            case 'main_content':
                return $temp_expanding_text_data['main_content'];
            case 'additional_content':
                return $temp_expanding_text_data['additional_content'];
            case 'button_text':
                return $temp_expanding_text_data['button_text'];
        }
    }

    return $value;
}