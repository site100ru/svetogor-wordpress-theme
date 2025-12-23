<?php
get_header();

render_archive_template(array(
    'post_type' => 'post',
    'title' => 'Статьи',
    'posts_per_page' => 15,
    'breadcrumbs' => array(array('title' => 'Статьи')),
    'no_posts_message' => 'Статьи не найдены',
    'no_posts_text' => 'В данный момент статей нет. Зайдите позже!'
));

get_footer();
