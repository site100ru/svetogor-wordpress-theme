<?php
/**
 * Система подключения админских скриптов
 * Заменяет inline JS в мета-боксах на кешируемые внешние файлы
 * 
 * @version 1.0.0
 */

/**
 * Подключение скриптов для админки
 */
function enqueue_admin_metabox_scripts($hook) {
  // Подключаем только на страницах создания/редактирования постов
  if (!in_array($hook, array('post.php', 'post-new.php'))) {
    return;
  }

  global $post_type;

  // Скрипт для hero-background (статьи, новости, услуги)
  if (in_array($post_type, array('post', 'news', 'services'))) {
    wp_enqueue_media(); // Подключаем медиа-библиотеку WordPress
    
    wp_enqueue_script(
      'hero-background-uploader',
      get_template_directory_uri() . '/assets/js/admin/hero-background-uploader.js',
      array('jquery'),
      filemtime(get_template_directory() . '/assets/js/admin/hero-background-uploader.js'),
      true
    );
  }

  // Скрипт для галереи портфолио
  if ($post_type === 'portfolio') {
    wp_enqueue_media();
    
    wp_enqueue_script(
      'portfolio-gallery',
      get_template_directory_uri() . '/assets/js/admin/portfolio-gallery.js',
      array('jquery'),
      filemtime(get_template_directory() . '/assets/js/admin/portfolio-gallery.js'),
      true
    );
  }
}
add_action('admin_enqueue_scripts', 'enqueue_admin_metabox_scripts');

/**
 * Подключение CSS для мета-боксов 
 */
function enqueue_admin_metabox_styles($hook) {
  if (!in_array($hook, array('post.php', 'post-new.php'))) {
    return;
  }

  global $post_type;

  // CSS для галереи портфолио
  if ($post_type === 'portfolio') {
    wp_enqueue_style(
      'portfolio-gallery-metabox',
      get_template_directory_uri() . '/assets/css/admin/portfolio-gallery.css',
      array(),
      filemtime(get_template_directory() . '/assets/css/admin/portfolio-gallery.css')
    );
  }
}
add_action('admin_enqueue_scripts', 'enqueue_admin_metabox_styles');