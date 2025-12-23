<?php
/**
 * Шаблон одиночной новости
 */
get_header();

while (have_posts()): the_post();
  
  // HERO секция с универсальной функцией
  render_hero_section(get_the_title(), get_hero_bg());

  // Хлебные крошки
  render_breadcrumbs([
    [
      'title' => 'Новости',
      'url' => get_post_type_archive_link('news')
    ],
    [
      'title' => wp_trim_words(get_the_title(), 6)
    ]
  ]);

  // Основной контент
  $content = get_the_content();
  
  if (!empty($content)) {
    render_universal_content($content, 'news');
  } else {
    ?>
    <section class="section single-news-content">
      <div class="container">
        <div class="row justify-content-center">
          <div class="col-12 col-lg-8">
            <div class="news-content">
              <p class="text-muted">Содержимое новости не добавлено.</p>
            </div>
          </div>
        </div>
      </div>
    </section>
    <?php
  }

  // Другие новости
  render_related_posts(
    get_the_ID(),
    'news',
    'Другие новости',
    get_post_type_archive_link('news'),
    'Все новости'
  );

endwhile;

get_footer();