<?php
/**
 * Функции для системы услуг
 */

// Регистрируем кастомный пост-тайп "Услуги"
function register_services_post_type()
{
  $labels = array(
    'name' => 'Услуги',
    'singular_name' => 'Услуга',
    'add_new' => 'Добавить новую',
    'add_new_item' => 'Добавить новую услугу',
    'edit_item' => 'Редактировать услугу',
    'new_item' => 'Новая услуга',
    'view_item' => 'Посмотреть услугу',
    'search_items' => 'Поиск услуг',
    'not_found' => 'Услуги не найдены',
    'not_found_in_trash' => 'Услуги не найдены в корзине',
    'all_items' => 'Все услуги',
    'menu_name' => 'Услуги',
    'name_admin_bar' => 'Услуга',
  );

  $args = array(
    'labels' => $labels,
    'public' => true,
    'publicly_queryable' => true,
    'show_ui' => true,
    'show_in_menu' => true,
    'query_var' => true,
    'rewrite' => array('slug' => 'services'),
    'capability_type' => 'post',
    'has_archive' => true,
    'hierarchical' => false,
    'menu_position' => 5,
    'menu_icon' => 'dashicons-admin-tools',
    'supports' => array('title', 'editor', 'thumbnail', 'excerpt'),
    'show_in_rest' => true,
  );

  register_post_type('services', $args);
}
add_action('init', 'register_services_post_type');

// Функция для подключения шаблонов услуг из папки services
function load_services_templates($template)
{
  // Для архивной страницы услуг
  if (is_post_type_archive('services')) {
    $services_archive = get_template_directory() . '/services/archive-services.php';
    if (file_exists($services_archive)) {
      return $services_archive;
    }
  }

  // Для отдельной страницы услуги
  if (is_singular('services')) {
    $services_single = get_template_directory() . '/services/single-service.php';
    if (file_exists($services_single)) {
      return $services_single;
    }
  }

  return $template;
}
add_filter('template_include', 'load_services_templates');
