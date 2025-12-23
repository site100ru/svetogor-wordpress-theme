<?php
/**
 * Архив новостей
 */
get_header();

render_archive_template(array(
  'post_type' => 'news',
  'title' => 'Новости',
  'posts_per_page' => 15,
  'breadcrumbs' => array(
    array('title' => 'Новости')
  ),
  'card_type' => 'default',
  'no_posts_message' => 'Новости не найдены',
  'no_posts_text' => 'В данный момент новостей нет. Зайдите позже!'
));

get_footer();