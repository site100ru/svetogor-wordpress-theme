<?php
/**
 * Блок "Последние новости/статьи"
 * Template: template-parts/blocks/news-articles-tabs.php
 */

// Получаем поля ACF с дефолтными значениями
$block_title = get_field('block_title') ?: 'Последние новости/статьи';
$posts_count = get_field('posts_count') ?: 3;
$background_color = get_field('background_color_half_section');
$show_news = get_field('show_news') !== false; // По умолчанию true
$show_articles = get_field('show_articles') !== false; // По умолчанию true

// Получаем классы блока
$className = '';
if (!empty($block['className'])) {
  $className = ' ' . $block['className'];
}

// Получаем anchor блока
$anchor = '';
if (!empty($block['anchor'])) {
  $anchor = ' id="' . esc_attr($block['anchor']) . '"';
}

// Генерируем уникальный ID для табов
$unique_id = 'tabs-' . uniqid();

// Определяем какой таб должен быть активным по умолчанию
$news_is_active = $show_news; // Новости активны по умолчанию, если они включены
$articles_is_active = !$show_news && $show_articles; // Статьи активны только если новости отключены

// Получаем последние новости
$news_query = null;
if ($show_news) {
  $news_query = new WP_Query(array(
    'post_type' => 'news',
    'posts_per_page' => $posts_count,
    'post_status' => 'publish',
    'orderby' => 'date',
    'order' => 'DESC'
  ));
}

// Получаем последние статьи
$articles_query = null;
if ($show_articles) {
  $articles_query = new WP_Query(array(
    'post_type' => 'post',
    'posts_per_page' => $posts_count,
    'post_status' => 'publish',
    'orderby' => 'date',
    'order' => 'DESC'
  ));
}
?>

<!-- Последние новости/статьи -->
<section
  class="section section-glide section-tabs box-shadow-main no-border <?php echo esc_attr($className); ?> <?php echo esc_attr($background_color); ?>"
  <?php echo $anchor; ?>>
  <div class="container">
    <div class="section-title text-center">
      <h2><?php echo esc_html($block_title); ?></h2>
      <img loading="lazy" src="<?php echo get_template_directory_uri(); ?>/assets/img/ico/points.svg" alt="Точки" class="img-fluid">
    </div>

    <!-- Табуляция -->
    <?php if ($show_news && $show_articles): ?>
      <ul class="nav nav-tabs justify-content-center align-items-center mb-4" id="<?php echo $unique_id; ?>"
        role="tablist">
        <?php if ($show_news): ?>
          <li class="nav-item" role="presentation">
            <button class="nav-link<?php echo $news_is_active ? ' active' : ''; ?>" id="news-tab-<?php echo $unique_id; ?>"
              data-bs-toggle="tab" data-bs-target="#news-<?php echo $unique_id; ?>" type="button" role="tab"
              aria-controls="news-<?php echo $unique_id; ?>"
              aria-selected="<?php echo $news_is_active ? 'true' : 'false'; ?>">
              Новости
            </button>
          </li>
        <?php endif; ?>

        <?php if ($show_news && $show_articles): ?>
          <li class="nav-item">
            <img loading="lazy" src="<?php echo get_template_directory_uri(); ?>/assets/img/ico/menu-decoration-point.svg" alt="Декоративная точка - разделитель" class="img-fluid py-3">
          </li>
        <?php endif; ?>

        <?php if ($show_articles): ?>
          <li class="nav-item" role="presentation">
            <button class="nav-link<?php echo $articles_is_active ? ' active' : ''; ?>"
              id="articles-tab-<?php echo $unique_id; ?>" data-bs-toggle="tab"
              data-bs-target="#articles-<?php echo $unique_id; ?>" type="button" role="tab"
              aria-controls="articles-<?php echo $unique_id; ?>"
              aria-selected="<?php echo $articles_is_active ? 'true' : 'false'; ?>">
              Статьи
            </button>
          </li>
        <?php endif; ?>
      </ul>
    <?php endif; ?>

    <!-- Контент табов -->
    <div class="tab-content" id="<?php echo $unique_id; ?>Content">

      <!-- Новости -->
      <?php if ($show_news): ?>
        <div class="tab-pane fade<?php echo $news_is_active ? ' show active' : ''; ?>" id="news-<?php echo $unique_id; ?>"
          role="tabpanel" aria-labelledby="news-tab-<?php echo $unique_id; ?>">
          <div class="row g-4 justify-content-center">
            <?php if ($news_query && $news_query->have_posts()): ?>
              <?php while ($news_query->have_posts()):
                $news_query->the_post(); ?>
                <div class="col-12 col-md-6 col-lg-4">
                  <a href="<?php the_permalink(); ?>"
                    class="card tabs-card h-100 bg-linear-gradient-wrapper text-decoration-none">
                    <div class="card-img-container">
                      <?php if (has_post_thumbnail()): ?>
                        <?php the_post_thumbnail('medium', array('class' => 'card-img-top', 'alt' => get_the_title())); ?>
                      <?php else: ?>
                        <img loading="lazy" src="<?php echo wc_placeholder_img_src(); ?>" alt="<?php the_title(); ?>" class="card-img-top">
                      <?php endif; ?>
                    </div>
                    <div class="card-body d-flex flex-column">
                      <h2 class="card-title"><?php the_title(); ?></h2>
                      <p class="card-text mb-0">
                        <?php echo get_news_excerpt(get_the_ID()); ?>
                      </p>
                      <div class="mt-auto d-flex justify-content-start align-items-center">
                        <span class="text-muted small"><?php echo get_the_date('d/m/Y'); ?></span>
                      </div>
                    </div>
                  </a>
                </div>
              <?php endwhile; ?>
              <?php wp_reset_postdata(); ?>
            <?php else: ?>
              <div class="col-12">
                <p class="text-center text-muted">Новости не найдены</p>
              </div>
            <?php endif; ?>
          </div>

          <!-- Кнопка Все новости -->
          <?php if ($news_query && $news_query->have_posts()): ?>
            <div class="mt-5 text-center">
              <a href="<?php echo get_post_type_archive_link('news'); ?>" class="btn btn-big">Все новости</a>
            </div>
          <?php endif; ?>
        </div>
      <?php endif; ?>

      <!-- Статьи -->
      <?php if ($show_articles): ?>
        <div class="tab-pane fade<?php echo $articles_is_active ? ' show active' : ''; ?>"
          id="articles-<?php echo $unique_id; ?>" role="tabpanel"
          aria-labelledby="articles-tab-<?php echo $unique_id; ?>">
          <div class="row g-4 justify-content-center">
            <?php if ($articles_query && $articles_query->have_posts()): ?>
              <?php while ($articles_query->have_posts()):
                $articles_query->the_post(); ?>
                <div class="col-12 col-md-6 col-lg-4">
                  <a href="<?php the_permalink(); ?>"
                    class="card tabs-card h-100 bg-linear-gradient-wrapper text-decoration-none">
                    <div class="card-img-container">
                      <?php if (has_post_thumbnail()): ?>
                        <?php the_post_thumbnail('medium', array('class' => 'card-img-top', 'alt' => get_the_title())); ?>
                      <?php else: ?>
                        <img loading="lazy" src="<?php echo wc_placeholder_img_src(); ?>" alt="<?php the_title(); ?>" class="card-img-top">
                      <?php endif; ?>
                    </div>
                    <div class="card-body d-flex flex-column">
                      <h5 class="card-title"><?php the_title(); ?></h5>
                      <p class="card-text mb-0">
                        <?php echo get_article_excerpt(get_the_ID()); ?>
                      </p>
                      <div class="mt-auto d-flex justify-content-start align-items-center">
                        <span class="text-muted small"><?php echo get_the_date('d/m/Y'); ?></span>
                      </div>
                    </div>
                  </a>
                </div>
              <?php endwhile; ?>
              <?php wp_reset_postdata(); ?>
            <?php else: ?>
              <div class="col-12">
                <p class="text-center text-muted">Статьи не найдены</p>
              </div>
            <?php endif; ?>
          </div>

          <!-- Кнопка Все статьи -->
          <?php if ($articles_query && $articles_query->have_posts()): ?>
            <div class="mt-5 text-center">
              <a href="<?php echo get_permalink(get_option('page_for_posts')) ?: home_url('/articles/'); ?>" class="btn">Все
                статьи</a>
            </div>
          <?php endif; ?>
        </div>
      <?php endif; ?>

    </div>
  </div>
</section>