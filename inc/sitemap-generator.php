<?php
/**
 * Генератор XML Sitemap для WordPress + WooCommerce
 * 
 * @package svetogor
 */

if (!defined('ABSPATH')) {
    exit;
}

// ============================================================================
// Отключение стандартного WordPress Sitemap
// ============================================================================

add_filter('wp_sitemaps_enabled', '__return_false');

// ============================================================================
// Регистрация правила rewrite для /sitemap.xml
// ============================================================================

function custom_sitemap_rewrite_rule() {
    add_rewrite_rule('^sitemap\.xml$', 'index.php?custom_sitemap=1', 'top');
}
add_action('init', 'custom_sitemap_rewrite_rule');

function custom_sitemap_query_vars($vars) {
    $vars[] = 'custom_sitemap';
    return $vars;
}
add_filter('query_vars', 'custom_sitemap_query_vars');

// ============================================================================
// Генерация sitemap.xml
// ============================================================================

function generate_custom_sitemap() {
    if (get_query_var('custom_sitemap')) {
        header('Content-Type: application/xml; charset=utf-8');
        echo '<?xml version="1.0" encoding="UTF-8"?>';
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        // Главная страница
        sitemap_add_url(home_url('/'), current_time('c'), 'daily', '1.0');

        // Страницы (pages)
        sitemap_add_post_type('page', 'monthly', '0.6');

        // Товары WooCommerce (products)
        sitemap_add_post_type('product', 'weekly', '0.8');

        // Категории товаров
        sitemap_add_taxonomy('product_cat', 'weekly', '0.7');

        // Портфолио
        sitemap_add_post_type('portfolio', 'monthly', '0.7');

        // Услуги
        sitemap_add_post_type('services', 'monthly', '0.8');

        // Новости
        sitemap_add_post_type('news', 'weekly', '0.6');

        // Статьи (post - переименованные в "Статьи")
        sitemap_add_post_type('post', 'weekly', '0.5');

        // Комплексное оформление (таксономия)
        sitemap_add_taxonomy('complex_design', 'monthly', '0.7');

        // Проверяем другие таксономии для кастомных типов
        sitemap_add_custom_taxonomies();

        echo '</urlset>';
        exit;
    }
}
add_action('template_redirect', 'generate_custom_sitemap');

// ============================================================================
// Вспомогательные функции
// ============================================================================

/**
 * Добавление URL в sitemap
 */
function sitemap_add_url($loc, $lastmod, $changefreq, $priority) {
    echo '<url>';
    echo '<loc>' . esc_url($loc) . '</loc>';
    echo '<lastmod>' . esc_html($lastmod) . '</lastmod>';
    echo '<changefreq>' . esc_html($changefreq) . '</changefreq>';
    echo '<priority>' . esc_html($priority) . '</priority>';
    echo '</url>';
}

/**
 * Добавление записей определенного типа
 */
function sitemap_add_post_type($post_type, $changefreq, $priority) {
    $args = array(
        'post_type' => $post_type,
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'orderby' => 'modified',
        'order' => 'DESC',
        'no_found_rows' => true,
        'update_post_meta_cache' => false,
        'update_post_term_cache' => false,
    );

    $posts = get_posts($args);

    foreach ($posts as $post) {
        $permalink = get_permalink($post->ID);
        $modified = get_post_modified_time('c', false, $post->ID);
        
        sitemap_add_url($permalink, $modified, $changefreq, $priority);
    }
}

/**
 * Добавление таксономии
 */
function sitemap_add_taxonomy($taxonomy, $changefreq, $priority) {
    $terms = get_terms(array(
        'taxonomy' => $taxonomy,
        'hide_empty' => true,
    ));

    if (!is_wp_error($terms) && !empty($terms)) {
        foreach ($terms as $term) {
            $term_link = get_term_link($term);
            
            if (!is_wp_error($term_link)) {
                // Получаем последнюю дату изменения записей в категории
                $posts = get_posts(array(
                    'post_type' => 'any',
                    'posts_per_page' => 1,
                    'orderby' => 'modified',
                    'order' => 'DESC',
                    'tax_query' => array(
                        array(
                            'taxonomy' => $taxonomy,
                            'field' => 'term_id',
                            'terms' => $term->term_id,
                        ),
                    ),
                ));

                $lastmod = !empty($posts) ? get_post_modified_time('c', false, $posts[0]->ID) : current_time('c');
                
                sitemap_add_url($term_link, $lastmod, $changefreq, $priority);
            }
        }
    }
}

/**
 * Автоматическое добавление других таксономий для кастомных типов постов
 */
function sitemap_add_custom_taxonomies() {
    // Получаем все таксономии для портфолио
    $portfolio_taxonomies = get_object_taxonomies('portfolio', 'objects');
    foreach ($portfolio_taxonomies as $taxonomy) {
        if ($taxonomy->public && $taxonomy->name !== 'post_tag' && $taxonomy->name !== 'category') {
            sitemap_add_taxonomy($taxonomy->name, 'monthly', '0.6');
        }
    }

    // Получаем все таксономии для услуг
    $services_taxonomies = get_object_taxonomies('services', 'objects');
    foreach ($services_taxonomies as $taxonomy) {
        if ($taxonomy->public && $taxonomy->name !== 'post_tag' && $taxonomy->name !== 'category') {
            sitemap_add_taxonomy($taxonomy->name, 'monthly', '0.6');
        }
    }

    // Получаем все таксономии для новостей
    $news_taxonomies = get_object_taxonomies('news', 'objects');
    foreach ($news_taxonomies as $taxonomy) {
        if ($taxonomy->public && $taxonomy->name !== 'post_tag' && $taxonomy->name !== 'category') {
            sitemap_add_taxonomy($taxonomy->name, 'weekly', '0.5');
        }
    }
}

// ============================================================================
// Активация правил rewrite при активации темы
// ============================================================================

function custom_sitemap_activate() {
    custom_sitemap_rewrite_rule();
    flush_rewrite_rules();
}
add_action('after_switch_theme', 'custom_sitemap_activate');