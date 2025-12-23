<?php
/**
 * Портфолио для товаров и категорий WooCommerce
 * 
 * @package svetogor
 */

if (!defined('ABSPATH')) {
    exit;
}

// ============================================================================
// Получение настроек портфолио
// ============================================================================

/**
 * Получает настройки портфолио для товара с учетом приоритета
 */
function get_product_portfolio_settings($product_id) {
    if (!is_acf_available()) {
        return false;
    }

    // Проверяем, включено ли отображение портфолио для товара
    $show_portfolio = get_field('product_show_portfolio', $product_id);
    if ($show_portfolio === false || $show_portfolio === '0') {
        return false;
    }

    // Получаем настройки товара
    $product_portfolio_category = get_field('product_portfolio_category', $product_id);
    $product_portfolio_count = get_field('product_portfolio_count', $product_id) ?: 6;
    $product_portfolio_title = get_field('product_portfolio_title', $product_id);

    // Если в товаре выбрана категория портфолио - используем её
    if ($product_portfolio_category) {
        return array(
            'portfolio_category' => $product_portfolio_category,
            'portfolio_count' => min($product_portfolio_count, 6),
            'portfolio_title' => $product_portfolio_title ?: 'Наши работы',
            'source' => 'product'
        );
    }

    // Иначе ищем в категориях товара
    $terms = wp_get_post_terms($product_id, 'product_cat');

    if (empty($terms) || is_wp_error($terms)) {
        return false;
    }

    // Сортируем категории по уровню вложенности (сначала более глубокие)
    $categories_by_level = array();

    foreach ($terms as $term) {
        $level = get_category_level($term->term_id);
        if (!isset($categories_by_level[$level])) {
            $categories_by_level[$level] = array();
        }
        $categories_by_level[$level][] = $term;
    }

    krsort($categories_by_level);

    // Ищем портфолио в категориях, начиная с более глубоких
    foreach ($categories_by_level as $level => $categories) {
        foreach ($categories as $category) {
            $category_settings = get_category_portfolio_settings($category->term_id);
            if ($category_settings) {
                // Переопределяем заголовок из товара если есть
                if ($product_portfolio_title) {
                    $category_settings['portfolio_title'] = $product_portfolio_title;
                }
                // Переопределяем количество из товара если есть
                $category_settings['portfolio_count'] = min($product_portfolio_count, 6);
                $category_settings['source'] = 'category';
                $category_settings['source_category'] = $category;

                return $category_settings;
            }
        }
    }

    return false;
}

/**
 * Получает настройки портфолио для архива категории
 */
function get_archive_portfolio_settings($category_id = null) {
    if (!$category_id) {
        $current_category = get_queried_object();
        if ($current_category && isset($current_category->term_id)) {
            $category_id = $current_category->term_id;
        } else {
            return false;
        }
    }

    $settings = get_category_portfolio_settings($category_id);
    if ($settings) {
        $settings['source'] = 'archive';
        return $settings;
    }

    return false;
}

// ============================================================================
// Вывод портфолио блоков
// ============================================================================

/**
 * Выводит блок портфолио для товара используя существующий шаблон
 */
function render_product_portfolio($product_id, $background = 'bg-grey') {
    $settings = get_product_portfolio_settings($product_id);

    if (!$settings) {
        return;
    }

    // Получаем работы портфолио
    $portfolio_posts = get_portfolio_posts_by_category(
        $settings['portfolio_category'],
        $settings['portfolio_count']
    );

    if (empty($portfolio_posts)) {
        return;
    }

    // Симулируем ACF поля для блока портфолио
    $simulated_acf_data = array(
        'grid_title' => $settings['portfolio_title'],
        'grid_background' => $background,
        'grid_display_type' => 'custom',
        'grid_posts_count' => $settings['portfolio_count'],
        'grid_custom_posts' => $portfolio_posts,
        'grid_show_all_works_button' => true,
        'grid_button_text' => 'Все наши работы',
        'portfolio_category_id' => $settings['portfolio_category']
    );

    // Временно устанавливаем данные для ACF
    global $temp_acf_data;
    $temp_acf_data = $simulated_acf_data;
    add_filter('acf/load_value', 'temp_acf_load_value', 10, 3);

    // Подключаем шаблон блока портфолио
    $template_path = get_template_directory() . '/template-parts/blocks/portfolio-grid/portfolio-grid.php';
    if (file_exists($template_path)) {
        include $template_path;
    }

    // Очищаем временные данные
    $temp_acf_data = null;
    remove_filter('acf/load_value', 'temp_acf_load_value', 10);
}

/**
 * Выводит блок портфолио для архива категории
 */
function render_archive_portfolio($category_id = null, $background = 'bg-grey') {
    $settings = get_archive_portfolio_settings($category_id);

    if (!$settings) {
        return;
    }

    // Получаем работы портфолио
    $portfolio_posts = get_portfolio_posts_by_category(
        $settings['portfolio_category'],
        $settings['portfolio_count']
    );

    if (empty($portfolio_posts)) {
        return;
    }

    // Симулируем ACF поля для блока портфолио
    $simulated_acf_data = array(
        'grid_title' => $settings['portfolio_title'],
        'grid_background' => $background,
        'grid_display_type' => 'custom',
        'grid_posts_count' => $settings['portfolio_count'],
        'grid_custom_posts' => $portfolio_posts,
        'grid_show_all_works_button' => true,
        'grid_button_text' => 'Все наши работы',
        'portfolio_category_id' => $settings['portfolio_category']
    );

    // Временно устанавливаем данные для ACF
    global $temp_acf_data;
    $temp_acf_data = $simulated_acf_data;
    add_filter('acf/load_value', 'temp_acf_load_value', 10, 3);

    // Подключаем шаблон блока портфолио
    $template_path = get_template_directory() . '/template-parts/blocks/portfolio-grid/portfolio-grid.php';
    if (file_exists($template_path)) {
        include $template_path;
    }

    // Очищаем временные данные
    $temp_acf_data = null;
    remove_filter('acf/load_value', 'temp_acf_load_value', 10);
}