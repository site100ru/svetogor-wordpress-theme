<?php
/**
 * Универсальные вспомогательные функции
 * 
 * @package svetogor
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Универсальная проверка ACF
 */
function is_acf_available() {
    static $available = null;
    
    if ($available === null) {
        $available = function_exists('acf_register_block_type');
    }
    
    return $available;
}

/**
 * Форматирование телефона для href
 */
function format_phone_for_href($phone) {
    if (empty($phone)) {
        return '';
    }
    
    // Убираем все символы кроме цифр и +
    return preg_replace('/[^0-9+]/', '', $phone);
}

/**
 * Универсальный получатель данных категории
 */
function get_category_custom_data($category_id, $field_name, $taxonomy = 'product_cat') {
    if (!is_acf_available()) {
        return false;
    }
    
    $data = get_field($field_name, "{$taxonomy}_{$category_id}");
    
    if (empty($data) && is_array($data)) {
        return false;
    }
    
    return $data;
}

/**
 * Универсальный получатель иконок с fallback
 */
function get_icon_url($icon_field, $default_filename = '', $context = 'option') {
    $icon = get_field($icon_field, $context);
    
    if ($icon && isset($icon['url'])) {
        return $icon['url'];
    }
    
    if ($default_filename) {
        return get_template_directory_uri() . '/assets/img/ico/' . $default_filename;
    }
    
    return '';
}

/**
 * Универсальная функция для получения данных с fallback
 */
function get_option_field($field_name, $default = '') {
    if (!is_acf_available()) {
        return $default;
    }
    
    $value = get_field($field_name, 'option');
    return $value ?: $default;
}

/**
 * Получает URL иконки контакта с fallback
 */
function get_contact_icon_url($icon_field, $default_filename) {
    return get_icon_url($icon_field, $default_filename, 'option');
}

/**
 * Получает уровень вложенности категории
 */
function get_category_level($category_id) {
    $level = 0;
    $current_id = $category_id;

    while ($current_id) {
        $parent = wp_get_term_taxonomy_parent_id($current_id, 'product_cat');
        if ($parent) {
            $level++;
            $current_id = $parent;
        } else {
            break;
        }
    }

    return $level;
}

/**
 * Получает фото категории
 */
function get_category_photo_url($category_id, $size = 'medium') {
    $photo_id = get_category_custom_data($category_id, 'category_photo');
    
    if ($photo_id) {
        $photo_url = wp_get_attachment_image_url($photo_id, $size);
        if ($photo_url) {
            return $photo_url;
        }
    }
    
    return false;
}

/**
 * Получает связанные категории
 */
function get_related_categories($category_id) {
    $related_category_ids = get_category_custom_data($category_id, 'related_categories');
    
    if (!$related_category_ids || !is_array($related_category_ids)) {
        return array();
    }
    
    // Ограничиваем до 6 категорий
    $related_category_ids = array_slice($related_category_ids, 0, 6);
    
    $related_categories = array();
    
    foreach ($related_category_ids as $related_id) {
        $category = get_term($related_id, 'product_cat');
        if ($category && !is_wp_error($category)) {
            $related_categories[] = $category;
        }
    }
    
    return $related_categories;
}

/**
 * Проверяет наличие связанных категорий
 */
function has_related_categories($category_id) {
    $related_categories = get_related_categories($category_id);
    return !empty($related_categories);
}

/**
 * Получает количество связанных категорий
 */
function get_related_categories_count($category_id) {
    $related_categories = get_related_categories($category_id);
    return count($related_categories);
}

/**
 * Безопасная проверка наличия блока
 */
function safe_has_block($block_name) {
    if (!function_exists('has_block')) {
        return false;
    }
    
    return has_block($block_name);
}

/**
 * Получает данные раскрывающегося текста категории
 */
function get_category_expanding_text_data($category_id) {
    if (!is_acf_available()) {
        return false;
    }
    
    return array(
        'section_title' => get_category_custom_data($category_id, 'category_section_title') ?: '',
        'background_color' => get_category_custom_data($category_id, 'category_background_color') ?: 'white',
        'main_content' => get_category_custom_data($category_id, 'category_main_content') ?: '',
        'additional_content' => get_category_custom_data($category_id, 'category_additional_content') ?: '',
        'button_text' => get_category_custom_data($category_id, 'category_button_text') ?: 'Читать далее',
    );
}

/**
 * Проверяет наличие раскрывающегося текста в категории
 */
function has_category_expanding_text($category_id) {
    $data = get_category_expanding_text_data($category_id);
    
    if (!$data) {
        return false;
    }
    
    // Проверяем наличие основного или дополнительного контента
    return !empty($data['main_content']) || !empty($data['additional_content']);
}

/**
 * Проверяет, является ли текущая страница шаблоном с хлебными крошками
 */
function is_breadcrumbs_page_template() {
    global $post;
    
    if (!is_page() || !$post) {
        return false;
    }

    $page_template = get_page_template_slug($post->ID);
    return $page_template === 'page_with_bread_crumbs.php';
}

/**
 * Проверяет принадлежность товара к категории "shop"
 */
function is_product_in_shop_category($product_id = null) {
    if (!$product_id) {
        $product_id = get_the_ID();
    }

    $terms = wp_get_post_terms($product_id, 'product_cat');

    if (!empty($terms) && !is_wp_error($terms)) {
        foreach ($terms as $term) {
            if ($term->slug == 'shop') {
                return true;
            }
        }
    }

    return false;
}

/**
 * Проверяет принадлежность товара к категории "product"
 */
function is_product_in_product_category($product_id = null) {
    if (!$product_id) {
        $product_id = get_the_ID();
    }

    $terms = wp_get_post_terms($product_id, 'product_cat');

    if (!empty($terms) && !is_wp_error($terms)) {
        foreach ($terms as $term) {
            if ($term->slug == 'product') {
                return true;
            }
        }
    }

    return false;
}

/**
 * Проверяет, находимся ли в категории "shop"
 */
function is_shop_category() {
    if (is_product_category()) {
        $term = get_queried_object();
        return ($term->slug == 'shop');
    }

    return false;
}

/**
 * Проверяет, находимся ли в категории "product"
 */
function is_product_category_page() {
    if (is_product_category()) {
        $term = get_queried_object();
        return ($term->slug == 'product');
    }

    return false;
}

/**
 * Получает тип категории товара
 */
function get_product_category_type($product_id = null) {
    if (!$product_id) {
        $product_id = get_the_ID();
    }

    if (is_product_in_shop_category($product_id)) {
        return 'shop';
    } elseif (is_product_in_product_category($product_id)) {
        return 'product';
    } else {
        return 'other';
    }
}