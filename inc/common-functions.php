<?php

/**
 * Общие функции для новостей и статей
 */

// Регистрация блока "Последние новости/статьи"
function register_news_articles_tabs_block()
{
    if (function_exists('acf_register_block_type')) {
        acf_register_block_type(array(
            'name' => 'news-articles-tabs',
            'title' => 'Последние новости/статьи',
            'description' => 'Блок с табами для отображения последних новостей и статей',
            'render_template' => get_template_directory() . '/template-parts/blocks/news-articles-tabs.php',
            'category' => 'custom-blocks',
            'icon' => 'tagcloud',
            'keywords' => array('news', 'articles', 'tabs', 'новости', 'статьи', 'табы'),
            'supports' => array(
                'align' => array('wide', 'full'),
                'anchor' => true,
                'customClassName' => true,
            ),
        ));
    }
}
add_action('acf/init', 'register_news_articles_tabs_block');

// Кастомная функция для пагинации с правильной Bootstrap структурой
function custom_pagination($query_obj, $echo = true)
{
    global $wp_query;

    // Используем переданный объект запроса или глобальный
    $query = $query_obj ? $query_obj : $wp_query;

    $total_pages = $query->max_num_pages;
    $current_page = max(1, get_query_var('paged'));

    if ($total_pages <= 1) {
        return '';
    }

    $pagination = '<nav class="mt-5">';
    $pagination .= '<ul class="pagination justify-content-center page-numbers flex-wrap">';

    // Кнопка "Предыдущая"
    if ($current_page > 1) {
        $prev_link = get_pagenum_link($current_page - 1);
        $pagination .= '<li class="page-item">';
        $pagination .= '<a class="page-link" href="' . esc_url($prev_link) . '" aria-label="Previous">';
        $pagination .= '<span aria-hidden="true">←</span>';
        $pagination .= '</a>';
        $pagination .= '</li>';
    }

    // Логика отображения страниц
    $start_page = max(1, $current_page - 2);
    $end_page = min($total_pages, $current_page + 2);

    // Первая страница
    if ($start_page > 1) {
        $pagination .= '<li class="page-item">';
        $pagination .= '<a class="page-link" href="' . esc_url(get_pagenum_link(1)) . '">1</a>';
        $pagination .= '</li>';

        if ($start_page > 2) {
            $pagination .= '<li class="page-item">';
            $pagination .= '<a class="page-link">...</a>';
            $pagination .= '</li>';
        }
    }

    // Основные страницы
    for ($i = $start_page; $i <= $end_page; $i++) {
        $active_class = ($i == $current_page) ? ' active' : '';
        $pagination .= '<li class="page-item' . $active_class . '">';
        $pagination .= '<a class="page-link" href="' . esc_url(get_pagenum_link($i)) . '">' . $i . '</a>';
        $pagination .= '</li>';
    }

    // Последняя страница
    if ($end_page < $total_pages) {
        if ($end_page < $total_pages - 1) {
            $pagination .= '<li class="page-item">';
            $pagination .= '<a class="page-link">...</a>';
            $pagination .= '</li>';
        }

        $pagination .= '<li class="page-item">';
        $pagination .= '<a class="page-link" href="' . esc_url(get_pagenum_link($total_pages)) . '">' . $total_pages . '</a>';
        $pagination .= '</li>';
    }

    // Кнопка "Следующая"
    if ($current_page < $total_pages) {
        $next_link = get_pagenum_link($current_page + 1);
        $pagination .= '<li class="page-item">';
        $pagination .= '<a class="page-link" href="' . esc_url($next_link) . '" aria-label="Next">';
        $pagination .= '<span aria-hidden="true">→</span>';
        $pagination .= '</a>';
        $pagination .= '</li>';
    }

    $pagination .= '</ul>';
    $pagination .= '</nav>';

    if ($echo) {
        echo $pagination;
    } else {
        return $pagination;
    }
}
