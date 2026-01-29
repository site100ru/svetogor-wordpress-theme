<?php
get_header();

// Получаем SEO заголовок или используем стандартный
$seo_title = get_option('archive_seo_title_articles');
$h1_title = !empty($seo_title) ? $seo_title : 'Статьи - Компания Светогор';
?>

<h1 class="d-none"><?php echo esc_html($h1_title); ?></h1>

<?php
render_archive_template(array(
    'post_type' => 'post',
    'title' => 'Статьи',
    'posts_per_page' => 15,
    'breadcrumbs' => array(array('title' => 'Статьи')),
    'no_posts_message' => 'Статьи не найдены',
    'no_posts_text' => 'В данный момент статей нет. Зайдите позже!'
));

get_footer();
