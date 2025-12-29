<?php
/**
 * Генератор Schema.org разметки (JSON-LD)
 * 
 * @package svetogor
 */

if (!defined('ABSPATH')) {
    exit;
}

// ============================================================================
// Основная функция вывода Schema разметки
// ============================================================================

function output_schema_markup() {
    $schemas = array();

    // Organization Schema (только на главной и странице контактов)
    if (is_front_page() || is_page('contacts')) {
        $schemas[] = get_organization_schema();
    }

    // Breadcrumb Schema (на всех страницах, кроме главной)
    if (!is_front_page()) {
        $breadcrumb_schema = get_breadcrumb_schema();
        if ($breadcrumb_schema) {
            $schemas[] = $breadcrumb_schema;
        }
    }

    // Product Schema (на странице товара - уже есть в WooCommerce)
    // Не дублируем, так как WooCommerce уже добавляет product schema

    // Выводим все схемы
    if (!empty($schemas)) {
        foreach ($schemas as $schema) {
            echo '<script type="application/ld+json">' . "\n";
            echo wp_json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
            echo "\n" . '</script>' . "\n";
        }
    }
}
add_action('wp_head', 'output_schema_markup');

// ============================================================================
// Organization Schema
// ============================================================================

function get_organization_schema() {
    // Получаем данные из ACF Options
    $company_name = get_field('company_name', 'option') ?: get_bloginfo('name');
    $company_logo = get_field('company_logo', 'option');
    $company_email = get_field('company_email', 'option');
    $company_work_address = get_field('company_work_address', 'option');
    $company_legal_address = get_field('company_legal_address', 'option');
    $company_phones = get_field('company_phones', 'option');
    $company_inn = get_field('company_inn', 'option');

    $schema = array(
        '@context' => 'https://schema.org',
        '@type' => 'Organization',
        'name' => $company_name,
        'url' => home_url('/'),
    );

    // Логотип
    if ($company_logo && isset($company_logo['url'])) {
        $schema['logo'] = $company_logo['url'];
        $schema['image'] = $company_logo['url'];
    }

    // Email
    if ($company_email) {
        $schema['email'] = $company_email;
    }

    // ИНН
    if ($company_inn) {
        $schema['taxID'] = $company_inn;
    }

    // Адрес
    if ($company_work_address || $company_legal_address) {
        $address_text = $company_work_address ?: $company_legal_address;
        $schema['address'] = array(
            '@type' => 'PostalAddress',
            'streetAddress' => $address_text,
            'addressCountry' => 'RU',
        );
    }

    // Телефоны
    if ($company_phones && is_array($company_phones)) {
        $phone_numbers = array();
        foreach ($company_phones as $phone) {
            if (!empty($phone['phone_number'])) {
                $phone_numbers[] = $phone['phone_number'];
            }
        }
        if (!empty($phone_numbers)) {
            $schema['telephone'] = $phone_numbers[0]; // Первый телефон как основной
            if (count($phone_numbers) > 1) {
                $schema['contactPoint'] = array(
                    '@type' => 'ContactPoint',
                    'telephone' => $phone_numbers[0],
                    'contactType' => 'customer service',
                );
            }
        }
    }

    // Социальные сети (если они есть в ACF)
    $social_links = array();
    $social_fields = array('facebook_url', 'instagram_url', 'vk_url', 'youtube_url', 'telegram_url');
    
    foreach ($social_fields as $field) {
        $url = get_field($field, 'option');
        if ($url) {
            $social_links[] = $url;
        }
    }

    if (!empty($social_links)) {
        $schema['sameAs'] = $social_links;
    }

    return $schema;
}

// ============================================================================
// Breadcrumb Schema
// ============================================================================

function get_breadcrumb_schema() {
    global $post;

    $breadcrumbs = array();
    $position = 1;

    // Главная страница всегда первая
    $breadcrumbs[] = array(
        '@type' => 'ListItem',
        'position' => $position++,
        'name' => 'Главная',
        'item' => home_url('/'),
    );

    // Определяем тип страницы и строим цепочку
    if (is_singular('product')) {
        // Товар WooCommerce
        breadcrumb_add_product_chain($breadcrumbs, $position);
        
    } elseif (is_singular('portfolio')) {
        // Портфолио
        breadcrumb_add_post_type_chain($breadcrumbs, $position, 'portfolio', 'Портфолио');
        
    } elseif (is_singular('services')) {
        // Услуги
        breadcrumb_add_post_type_chain($breadcrumbs, $position, 'services', 'Услуги');
        
    } elseif (is_singular('news')) {
        // Новости
        breadcrumb_add_post_type_chain($breadcrumbs, $position, 'news', 'Новости');
        
    } elseif (is_singular('post')) {
        // Статьи (переименованные записи)
        breadcrumb_add_post_type_chain($breadcrumbs, $position, 'post', 'Статьи');
        
    } elseif (is_page()) {
        // Обычная страница
        breadcrumb_add_page_chain($breadcrumbs, $position);
        
    } elseif (is_post_type_archive('portfolio')) {
        // Архив портфолио
        $breadcrumbs[] = array(
            '@type' => 'ListItem',
            'position' => $position++,
            'name' => 'Портфолио',
            'item' => get_post_type_archive_link('portfolio'),
        );
        
    } elseif (is_post_type_archive('services')) {
        // Архив услуг
        $breadcrumbs[] = array(
            '@type' => 'ListItem',
            'position' => $position++,
            'name' => 'Услуги',
            'item' => get_post_type_archive_link('services'),
        );
        
    } elseif (is_post_type_archive('news')) {
        // Архив новостей
        $breadcrumbs[] = array(
            '@type' => 'ListItem',
            'position' => $position++,
            'name' => 'Новости',
            'item' => get_post_type_archive_link('news'),
        );
        
    } elseif (is_home() || is_singular('post')) {
        // Блог (архив статей)
        $blog_page_id = get_option('page_for_posts');
        if ($blog_page_id) {
            $breadcrumbs[] = array(
                '@type' => 'ListItem',
                'position' => $position++,
                'name' => get_the_title($blog_page_id),
                'item' => get_permalink($blog_page_id),
            );
        } else {
            $breadcrumbs[] = array(
                '@type' => 'ListItem',
                'position' => $position++,
                'name' => 'Статьи',
                'item' => home_url('/blog/'),
            );
        }
        
    } elseif (is_tax('product_cat')) {
        // Категория товаров
        breadcrumb_add_term_chain($breadcrumbs, $position, 'product_cat');
        
    } elseif (is_tax('complex_design')) {
        // Комплексное оформление
        breadcrumb_add_term_chain($breadcrumbs, $position, 'complex_design');
        
    } elseif (is_tax()) {
        // Другие таксономии
        $term = get_queried_object();
        breadcrumb_add_term_chain($breadcrumbs, $position, $term->taxonomy);
        
    } elseif (is_search()) {
        // Поиск
        $breadcrumbs[] = array(
            '@type' => 'ListItem',
            'position' => $position++,
            'name' => 'Результаты поиска',
            'item' => get_search_link(),
        );
        
    } elseif (is_404()) {
        // 404
        $breadcrumbs[] = array(
            '@type' => 'ListItem',
            'position' => $position++,
            'name' => 'Страница не найдена',
        );
    }

    // Если только главная страница в цепочке - не выводим breadcrumb
    if (count($breadcrumbs) <= 1) {
        return null;
    }

    return array(
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => $breadcrumbs,
    );
}

// ============================================================================
// Вспомогательные функции для Breadcrumb
// ============================================================================

/**
 * Добавление цепочки для товара WooCommerce
 */
function breadcrumb_add_product_chain(&$breadcrumbs, &$position) {
    global $post;

    // Получаем родительскую категорию товара
    $terms = get_the_terms($post->ID, 'product_cat');
    
    if ($terms && !is_wp_error($terms)) {
        // Находим родительскую категорию
        $parent_term = null;
        foreach ($terms as $term) {
            if ($term->parent == 0) {
                $parent_term = $term;
                break;
            }
        }
        
        // Если не нашли родительскую, берем первую
        if (!$parent_term) {
            $parent_term = $terms[0];
        }

        // Добавляем категорию
        $breadcrumbs[] = array(
            '@type' => 'ListItem',
            'position' => $position++,
            'name' => $parent_term->name,
            'item' => get_term_link($parent_term),
        );
    }

    // Добавляем сам товар
    $breadcrumbs[] = array(
        '@type' => 'ListItem',
        'position' => $position++,
        'name' => get_the_title(),
        'item' => get_permalink(),
    );
}

/**
 * Добавление цепочки для кастомного типа записи
 */
function breadcrumb_add_post_type_chain(&$breadcrumbs, &$position, $post_type, $archive_name) {
    global $post;

    // Добавляем архивную страницу
    $archive_link = get_post_type_archive_link($post_type);
    if ($archive_link) {
        $breadcrumbs[] = array(
            '@type' => 'ListItem',
            'position' => $position++,
            'name' => $archive_name,
            'item' => $archive_link,
        );
    }

    // Проверяем, есть ли таксономии
    $taxonomies = get_object_taxonomies($post_type, 'objects');
    foreach ($taxonomies as $taxonomy) {
        if ($taxonomy->public) {
            $terms = get_the_terms($post->ID, $taxonomy->name);
            if ($terms && !is_wp_error($terms)) {
                // Берем первый термин (или родительский)
                $term = $terms[0];
                $breadcrumbs[] = array(
                    '@type' => 'ListItem',
                    'position' => $position++,
                    'name' => $term->name,
                    'item' => get_term_link($term),
                );
                break; // Берем только первую таксономию
            }
        }
    }

    // Добавляем текущую запись
    $breadcrumbs[] = array(
        '@type' => 'ListItem',
        'position' => $position++,
        'name' => get_the_title(),
        'item' => get_permalink(),
    );
}

/**
 * Добавление цепочки для страницы
 */
function breadcrumb_add_page_chain(&$breadcrumbs, &$position) {
    global $post;

    // Добавляем текущую страницу
    $breadcrumbs[] = array(
        '@type' => 'ListItem',
        'position' => $position++,
        'name' => get_the_title(),
        'item' => get_permalink(),
    );
}

/**
 * Добавление цепочки для таксономии (категории)
 */
function breadcrumb_add_term_chain(&$breadcrumbs, &$position, $taxonomy, $term = null) {
    // Если термин не передан, получаем текущий
    if (!$term) {
        $term = get_queried_object();
    }

    // Если есть родительские категории, добавляем их СНАЧАЛА
    if ($term->parent) {
        $parent = get_term($term->parent, $taxonomy);
        if ($parent && !is_wp_error($parent)) {
            // Передаем родительский термин в рекурсию
            breadcrumb_add_term_chain($breadcrumbs, $position, $taxonomy, $parent);
        }
    }

    // Добавляем текущую категорию
    $breadcrumbs[] = array(
        '@type' => 'ListItem',
        'position' => $position++,
        'name' => $term->name,
        'item' => get_term_link($term),
    );
}