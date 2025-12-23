<?php
/**
 * Дополнительные вспомогательные функции
 * Функции для работы с категориями, товарами и портфолио
 * 
 * @package svetogor
 */

if (!defined('ABSPATH')) {
    exit;
}

// ============================================================================
// Функции для работы с FAQ категорий
// ============================================================================

/**
 * Получает FAQ для категории по ID
 */
function get_category_faq($category_id) {
    if (!is_acf_available()) {
        return get_category_faq_manual($category_id);
    }
    
    $faq_data = get_field('category_faq_questions', 'product_cat_' . $category_id);
    
    if (!empty($faq_data) && is_array($faq_data)) {
        return $faq_data;
    }

    return get_category_faq_manual($category_id);
}

/**
 * Получает FAQ категории вручную (резервный метод)
 */
function get_category_faq_manual($category_id) {
	// Получаем количество элементов repeater
    $count = get_term_meta($category_id, 'category_faq_questions', true);

    if (empty($count) || !is_numeric($count)) {
        return false;
    }

    $faq_items = array();

    // Собираем каждый элемент
    for ($i = 0; $i < intval($count); $i++) {
        $question = get_term_meta($category_id, "category_faq_questions_{$i}_question", true);
        $answer = get_term_meta($category_id, "category_faq_questions_{$i}_answer", true);
        $expanded = get_term_meta($category_id, "category_faq_questions_{$i}_expanded", true);

        // Проверяем, что есть и вопрос и ответ
        if (!empty($question) && !empty($answer)) {
            $faq_items[] = array(
                'question' => $question,
                'answer' => $answer,
                'expanded' => ($expanded === '1' || $expanded === 1)
            );
        }
    }

    return !empty($faq_items) ? $faq_items : false;
}

/**
 * Проверяет наличие FAQ у категории
 */
function category_has_faq($category_id) {
    $faq = get_category_faq($category_id);
    return !empty($faq);
}

/**
 * Получает количество вопросов в FAQ категории
 */
function get_category_faq_count($category_id) {
    $faq = get_category_faq($category_id);
    return is_array($faq) ? count($faq) : 0;
}

// ============================================================================
// Функции для работы с портфолио категорий
// ============================================================================

/**
 * Получает настройки портфолио для категории товаров
 */
function get_category_portfolio_settings($category_id) {
    if (!is_acf_available()) {
        return false;
    }

    $portfolio_category = get_field('category_portfolio_category', 'product_cat_' . $category_id);
    $portfolio_count = get_field('category_portfolio_count', 'product_cat_' . $category_id) ?: 6;
    $portfolio_title = get_field('category_portfolio_title', 'product_cat_' . $category_id);

    if (!$portfolio_category) {
        return false;
    }

    return array(
        'portfolio_category' => $portfolio_category,
        'portfolio_count' => min($portfolio_count, 6), // Максимум 6
        'portfolio_title' => $portfolio_title ?: 'Наши работы'
    );
}

/**
 * Проверяет наличие настроек портфолио у категории товаров
 */
function category_has_portfolio($category_id) {
    $settings = get_category_portfolio_settings($category_id);
    return !empty($settings);
}

/**
 * Получает количество работ в категории портфолио
 */
function get_portfolio_category_count($portfolio_category_id) {

    $count = wp_count_posts('portfolio');

    $args = array(
        'post_type' => 'portfolio',
        'posts_per_page' => -1,
        'post_status' => 'publish',
        'fields' => 'ids',
        'tax_query' => array(
            array(
                'taxonomy' => 'portfolio_category',
                'field' => 'term_id',
                'terms' => $portfolio_category_id,
            ),
        ),
    );

    $query = new WP_Query($args);
    return $query->found_posts;
}

/**
 * Получает работы портфолио по категории
 */
function get_portfolio_posts_by_category($portfolio_category_id, $count = 6) {
    $args = array(
        'post_type' => 'portfolio',
        'posts_per_page' => min($count, 6),
        'post_status' => 'publish',
        'orderby' => 'date',
        'order' => 'DESC',
        'tax_query' => array(
            array(
                'taxonomy' => 'portfolio_category',
                'field' => 'term_id',
                'terms' => $portfolio_category_id,
            ),
        ),
    );

    $query = new WP_Query($args);
    $posts = array();

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $posts[] = get_post();
        }
        wp_reset_postdata();
    }

    return $posts;
}

/**
 * Получает все категории товаров с настройками портфолио
 */
function get_product_categories_with_portfolio($product_id) {
    $terms = wp_get_post_terms($product_id, 'product_cat');
    $categories_with_portfolio = array();

    if (!empty($terms) && !is_wp_error($terms)) {
        foreach ($terms as $term) {
            if (category_has_portfolio($term->term_id)) {
                $settings = get_category_portfolio_settings($term->term_id);
                $portfolio_count = get_portfolio_category_count($settings['portfolio_category']);

                $categories_with_portfolio[] = array(
                    'category' => $term,
                    'portfolio_settings' => $settings,
                    'portfolio_count' => $portfolio_count,
                    'level' => get_category_level($term->term_id)
                );
            }
        }

        // Сортируем по уровню (сначала более глубокие)
        usort($categories_with_portfolio, function ($a, $b) {
            return $b['level'] - $a['level'];
        });
    }

    return $categories_with_portfolio;
}

// ============================================================================
// Функции для работы с комплексным оформлением
// ============================================================================

/**
 * Получает миниатюру комплексного оформления
 */
function get_complex_design_thumbnail($term_id, $size = 'thumbnail') {
    $thumbnail_id = get_term_meta($term_id, 'thumbnail_id', true);
    
    if ($thumbnail_id) {
        return wp_get_attachment_image_url($thumbnail_id, $size);
    }
    
    return false;
}

/**
 * Получает комплексные оформления по категории
 * 
 */
function get_complex_design_by_category($category_id) {
    $designs = get_terms(array(
        'taxonomy' => 'complex_design',
        'hide_empty' => false,
    ));

    $matched_designs = array();
    
    foreach ($designs as $design) {
        $linked_categories = get_term_meta($design->term_id, 'linked_categories', true);
        
        if (is_array($linked_categories) && in_array($category_id, $linked_categories)) {
            $matched_designs[] = $design;
        }
    }

    return $matched_designs;
}

// ============================================================================
// Функции для работы со страницами
// ============================================================================

/**
 * Функция для генерации хлебных крошек для страниц
 */
function render_page_breadcrumbs($page_title = '') {
    global $post;

    if (empty($page_title)) {
        $page_title = get_the_title();
    }

    echo '<nav aria-label="breadcrumb" class="mb-0">';
    echo '<ol class="breadcrumb bg-transparent p-0 m-0">';

    // Главная страница
    echo '<li class="breadcrumb-item">';
    echo '<a href="' . home_url() . '" class="text-decoration-none text-secondary">';
    echo '<img src="' . get_template_directory_uri() . '/assets/img/ico/breadcrumbs.svg" loading="lazy" />';
    echo '</a>';
    echo '</li>';

    // Родительские страницы
    if ($post->post_parent) {
        $parent_ids = array_reverse(get_post_ancestors($post->ID));
        
        foreach ($parent_ids as $parent_id) {
            echo '<li class="breadcrumb-item">';
            echo '<a href="' . get_permalink($parent_id) . '" class="text-decoration-none text-secondary">';
            echo get_the_title($parent_id);
            echo '</a>';
            echo '</li>';
        }
    }

    // Текущая страница
    echo '<li class="breadcrumb-item active" aria-current="page">';
    echo wp_trim_words($page_title, 6);
    echo '</li>';

    echo '</ol>';
    echo '</nav>';
}

/**
 * Функция для вывода контента страниц с группировкой стандартных блоков
 */
function render_page_content($content) {

    // Применяем все фильтры WordPress включая наш
    $processed_content = apply_filters('the_content', $content);

    // Простая замена: группируем блоки с классом standard-block-wrapper-page
    $pattern = '/(<div class="standard-block-wrapper-page">.*?<\/div>)/s';
    $parts = preg_split($pattern, $processed_content, -1, PREG_SPLIT_DELIM_CAPTURE);

    $current_standard_group = '';

    foreach ($parts as $part) {
        if (empty(trim($part))) continue;

        if (strpos($part, 'standard-block-wrapper-page') !== false) {
            // Накапливаем стандартные блоки
            $clean_content = preg_replace('/<div class="standard-block-wrapper-page">(.*?)<\/div>/s', '$1', $part);
            $current_standard_group .= $clean_content;
        } else {
            // Если накопились стандартные блоки, выводим их в контейнере
            if (!empty(trim($current_standard_group))) {
                ?>
                <section class="section single-page-content">
                    <div class="container">
                        <div class="row justify-content-center">
                            <div class="col-12 col-lg-8">
                                <div class="page-content">
                                    <?php echo $current_standard_group; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <?php
                $current_standard_group = '';
            }

            // Выводим кастомный блок как есть
            echo $part;
        }
    }

    if (!empty(trim($current_standard_group))) {
        ?>
        <section class="section single-page-content">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-12 col-lg-8">
                        <div class="page-content">
                            <?php echo $current_standard_group; ?>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <?php
    }
}

// ============================================================================
// Служебные функции
// ============================================================================

/**
 * Фильтр для временной подмены ACF данных
 */
function temp_acf_load_value($value, $post_id, $field) {
    global $temp_acf_data;

    if ($temp_acf_data && isset($temp_acf_data[$field['name']])) {
        return $temp_acf_data[$field['name']];
    }

    return $value;
}