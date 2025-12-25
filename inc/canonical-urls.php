<?php
/**
 * Управление каноническими URL
 */

// Защита от прямого доступа
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Добавление канонических URL на все страницы
 */
function add_canonical_url() {
    $canonical_url = '';
    
    if (is_front_page()) {
        // Для главной страницы - со слэшем
        $canonical_url = trailingslashit(home_url());
        
    } elseif (is_singular()) {
        // Для отдельных страниц, постов, товаров, портфолио
        $canonical_url = trailingslashit(get_permalink());
        
    } elseif (is_category()) {
        // Для категорий
        $canonical_url = trailingslashit(get_category_link(get_queried_object_id()));
        
    } elseif (is_tag()) {
        // Для меток
        $canonical_url = trailingslashit(get_tag_link(get_queried_object_id()));
        
    } elseif (is_tax()) {
        // Для таксономий
        $canonical_url = trailingslashit(get_term_link(get_queried_object()));
        
    } elseif (is_post_type_archive()) {
        // Для архивов
        $canonical_url = trailingslashit(get_post_type_archive_link(get_post_type()));
        
    } elseif (is_author()) {
        // Для страниц автора
        $canonical_url = trailingslashit(get_author_posts_url(get_queried_object_id()));
        
    } elseif (is_search()) {
        // Для страницы поиска
        $canonical_url = trailingslashit(get_search_link());
        
    } elseif (is_404()) {
        // Для 404 canonical не нужен
        return;
        
    } else {
        // Для всех остальных случаев
        global $wp;
        $canonical_url = trailingslashit(home_url($wp->request));
    }
    
    // Убираем параметры из URL
    $canonical_url = strtok($canonical_url, '?');
    
    // Выводим canonical
    if (!empty($canonical_url)) {
        echo '<link rel="canonical" href="' . esc_url($canonical_url) . '" />' . "\n";
    }
}
add_action('wp_head', 'add_canonical_url', 1);

// Удаляем стандартный canonical от WordPress
remove_action('wp_head', 'rel_canonical');
