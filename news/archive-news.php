<?php
/**
 * Архив новостей
 */
get_header();

// Получаем SEO заголовок или используем стандартный
$seo_title = get_option('archive_seo_title_news');
$h1_title = !empty($seo_title) ? $seo_title : 'Новости - Компания Светогор';
?>

<h1 class="d-none"><?php echo esc_html($h1_title); ?></h1>

<?php
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