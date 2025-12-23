<?php
/**
 * Функции для системы новостей
 */

// Регистрация кастомного типа записей "News"
function create_news_post_type()
{
  $labels = array(
    'name' => 'Новости',
    'singular_name' => 'Новость',
    'menu_name' => 'Новости',
    'name_admin_bar' => 'Новость',
    'archives' => 'Архив новостей',
    'attributes' => 'Атрибуты новости',
    'parent_item_colon' => 'Родительская новость:',
    'all_items' => 'Все новости',
    'add_new_item' => 'Добавить новую новость',
    'add_new' => 'Добавить новую',
    'new_item' => 'Новая новость',
    'edit_item' => 'Редактировать новость',
    'update_item' => 'Обновить новость',
    'view_item' => 'Посмотреть новость',
    'view_items' => 'Посмотреть новости',
    'search_items' => 'Поиск новостей',
    'not_found' => 'Новости не найдены',
    'not_found_in_trash' => 'Новости не найдены в корзине',
    'featured_image' => 'Главное изображение',
    'set_featured_image' => 'Установить главное изображение',
    'remove_featured_image' => 'Удалить главное изображение',
    'use_featured_image' => 'Использовать как главное изображение',
    'insert_into_item' => 'Вставить в новость',
    'uploaded_to_this_item' => 'Загружено для этой новости',
    'items_list' => 'Список новостей',
    'items_list_navigation' => 'Навигация по новостям',
    'filter_items_list' => 'Фильтр новостей',
  );

  $args = array(
    'label' => 'Новость',
    'description' => 'Новости компании',
    'labels' => $labels,
    'supports' => array('title', 'editor', 'excerpt', 'thumbnail', 'custom-fields'),
    'hierarchical' => false,
    'public' => true,
    'show_ui' => true,
    'show_in_menu' => true,
    'menu_position' => 6,
    'menu_icon' => 'dashicons-admin-post',
    'show_in_admin_bar' => true,
    'show_in_nav_menus' => true,
    'can_export' => true,
    'has_archive' => 'news',
    'exclude_from_search' => false,
    'publicly_queryable' => true,
    'capability_type' => 'post',
    'show_in_rest' => true,
  );

  register_post_type('news', $args);
}
add_action('init', 'create_news_post_type', 0);

// Обновление rewrite rules при активации темы для новостей
function news_rewrite_flush()
{
  create_news_post_type();
  flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'news_rewrite_flush');

// Функция для подключения шаблонов новостей из папки news
function load_news_templates($template)
{
  // Для архивной страницы новостей
  if (is_post_type_archive('news')) {
    $news_archive = get_template_directory() . '/news/archive-news.php';
    if (file_exists($news_archive)) {
      return $news_archive;
    }
  }

  // Для отдельной страницы новости
  if (is_singular('news')) {
    $news_single = get_template_directory() . '/news/single-news.php';
    if (file_exists($news_single)) {
      return $news_single;
    }
  }

  return $template;
}
add_filter('template_include', 'load_news_templates');

// Добавление колонки с отрывком в админке
function add_news_excerpt_column($columns)
{
  $columns['news_excerpt'] = 'Отрывок';
  return $columns;
}
add_filter('manage_news_posts_columns', 'add_news_excerpt_column');

// Заполнение колонки с отрывком
function fill_news_excerpt_column($column, $post_id)
{
  if ($column === 'news_excerpt') {
    $excerpt = get_universal_excerpt($post_id);
    echo !empty($excerpt) ? esc_html(wp_trim_words($excerpt, 15)) : '<em>Не указано</em>';
  }
}
add_action('manage_news_posts_custom_column', 'fill_news_excerpt_column', 10, 2);
