<?php

/**
 * Функции для системы статей
 */

// Переименовываем стандартные "Записи" в "Статьи" и настраиваем их
function rename_posts_to_articles()
{
    global $wp_post_types;

    // Изменяем лейблы
    $labels = &$wp_post_types['post']->labels;
    $labels->name = 'Статьи';
    $labels->singular_name = 'Статья';
    $labels->add_new = 'Добавить новую';
    $labels->add_new_item = 'Добавить новую статью';
    $labels->edit_item = 'Редактировать статью';
    $labels->new_item = 'Новая статья';
    $labels->view_item = 'Посмотреть статью';
    $labels->search_items = 'Поиск статей';
    $labels->not_found = 'Статьи не найдены';
    $labels->not_found_in_trash = 'Статьи не найдены в корзине';
    $labels->all_items = 'Все статьи';
    $labels->menu_name = 'Статьи';
    $labels->name_admin_bar = 'Статья';

    // Изменяем иконку
    $wp_post_types['post']->menu_icon = 'dashicons-edit-page';
}
add_action('init', 'rename_posts_to_articles');

// Убираем рубрики (categories) и метки (tags) для статей
function remove_categories_and_tags_from_posts()
{
    unregister_taxonomy_for_object_type('category', 'post');
    unregister_taxonomy_for_object_type('post_tag', 'post');
}
add_action('init', 'remove_categories_and_tags_from_posts');

// Убираем мета-боксы рубрик и меток из админки
function remove_categories_tags_meta_boxes()
{
    remove_meta_box('categorydiv', 'post', 'side');
    remove_meta_box('tagsdiv-post_tag', 'post', 'side');
}
add_action('admin_menu', 'remove_categories_tags_meta_boxes');

// Функция для подключения шаблонов статей из папки articles
function load_articles_templates($template)
{
    // Для архивной страницы статей (главной страницы блога)
    if (is_home() || is_category() || is_tag() || is_author() || is_date()) {
        $articles_archive = get_template_directory() . '/articles/archive-articles.php';
        if (file_exists($articles_archive)) {
            return $articles_archive;
        }
    }

    // Для отдельной страницы статьи
    if (is_singular('post')) {
        $articles_single = get_template_directory() . '/articles/single-article.php';
        if (file_exists($articles_single)) {
            return $articles_single;
        }
    }

    return $template;
}
add_filter('template_include', 'load_articles_templates');
