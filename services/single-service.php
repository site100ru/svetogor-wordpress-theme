<?php
/**
 * Шаблон одиночной услуги
 */
get_header();

while (have_posts()): the_post();
  
  // HERO секция с универсальной функцией
  render_hero_section(get_the_title(), get_hero_bg());

  // Хлебные крошки
  render_breadcrumbs([
    [
      'title' => 'Услуги',
      'url' => get_post_type_archive_link('services')
    ],
    [
      'title' => get_the_title()
    ]
  ]);

  // Основной контент
  $content = get_the_content();
  
  if (!empty($content)) {
    render_universal_content($content, 'services');
  }

  // Форма обратной связи
  get_template_part('template-parts/blocks/forms/form');

endwhile;

get_footer();