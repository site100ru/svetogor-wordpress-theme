<?php
/**
 * Управление метатегами robots для SEO
 */

// Защита от прямого доступа
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Добавление метатега robots
 */
function add_robots_meta_tag() {
    // Страницы для закрытия от индексации (по slug)
    $noindex_pages = array(
        'cart',           // Корзина
        'checkout',       // Оформление заказа
        'my-account',     // Личный кабинет
        'thank-you',      // Страница благодарности
        'sample-page',    // Пример страницы WordPress
    );
    
    $current_page_slug = get_post_field('post_name', get_queried_object_id());
    
    // Проверка на категорию Uncategorized
    $is_uncategorized = false;
    if (is_category()) {
        $category = get_queried_object();
        if ($category && $category->slug === 'uncategorized') {
            $is_uncategorized = true;
        }
    }
    
    // Условия для noindex
    $is_noindex = is_404() ||                    // Страница 404
                  is_search() ||                 // Страница поиска
                  is_author() ||                 // Страница автора
                  is_attachment() ||             // Страница вложения
                  is_date() ||                   // Архив по датам
                  is_tag() ||                    // Архив меток постов
                  is_tax('product_tag') ||       // Метки товаров WooCommerce
                  $is_uncategorized ||           // Категория Uncategorized
                  in_array($current_page_slug, $noindex_pages); // Страницы из списка
    
    if ($is_noindex) {
        echo '<meta name="robots" content="noindex, nofollow">' . "\n";
    } else {
        echo '<meta name="robots" content="index, follow">' . "\n";
    }
}
add_action('wp_head', 'add_robots_meta_tag', 1);