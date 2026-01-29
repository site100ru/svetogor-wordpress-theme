<?php

/**
 * Архив услуг
 */
get_header();

// Получаем SEO заголовок или используем стандартный
$seo_title = get_option('archive_seo_title_services');
$h1_title = !empty($seo_title) ? $seo_title : 'Услуги - Компания Светогор';
?>

<h1 class="d-none"><?php echo esc_html($h1_title); ?></h1>

<?php

// Выводим архив услуг
render_archive_template([
    'post_type' => 'services',
    'title' => 'Услуги',
    'posts_per_page' => -1, // Все услуги
    'breadcrumbs' => [
        ['title' => 'Услуги']
    ],
    'card_type' => 'service', // Горизонтальные карточки
    'section_classes' => [], // Дополнительные классы не нужны, функция сама добавит нужные
    'no_posts_message' => 'Услуги не найдены',
    'no_posts_text' => 'В данный момент услуг нет. Зайдите позже!',
    'show_pagination' => false // Отключаем пагинацию для услуг
]);

// Дополнительный контент из админки
$services_page = get_page_by_path('services');
if ($services_page && !empty($services_page->post_content)) {
    render_universal_content($services_page->post_content, 'services');
}

// Форма обратной связи
get_template_part('template-parts/blocks/forms/form');

get_footer();
