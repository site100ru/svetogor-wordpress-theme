<?php
/**
 * Оптимизированный шаблон архива портфолио
 * archive-portfolio.php
 */
get_header();

// Подключаем общий JS для портфолио
wp_enqueue_script('portfolio-slider-js', get_template_directory_uri() . '/template-parts/blocks/portfolio-slider/portfolio-slider.js', array('jquery'), filemtime(get_template_directory() . '/template-parts/blocks/portfolio-slider/portfolio-slider.js'), true);

// Локализация переменных  
wp_localize_script('portfolio-slider-js', 'portfolio_ajax', array(
  'ajax_url' => admin_url('admin-ajax.php'),
  'nonce' => wp_create_nonce('portfolio_grid_nonce') // Используем тот же nonce, что в functions.php
));
?>

<section class="hero-section hero-section" <?php
$portfolio_bg = get_field('portfolio_hero_bg', 'option');
if ($portfolio_bg): ?>style="background-image: url('<?php echo esc_url($portfolio_bg['url']); ?>');" <?php endif; ?>>
  <div class="container position-relative">
    <div class="row">
      <div class="col hero-content">
        <h1>Наши работы</h1>
      </div>
    </div>
  </div>
</section>

<!-- ХЛЕБНЫЕ КРОШКИ/ ЗАГОЛОВОК -->
<section class="section-mini">
  <div class="container">
    <!-- Хлебные крошки -->
    <nav aria-label="breadcrumb" class="mb-0">
      <ol class="breadcrumb bg-transparent p-0 m-0">
        <li class="breadcrumb-item">
          <a href="<?php echo home_url(); ?>" class="text-decoration-none text-secondary">
            <img src="<?php echo get_template_directory_uri(); ?>/assets/img/ico/breadcrumbs.svg" loading="lazy" />
          </a>
        </li>
        <li class="breadcrumb-item active" aria-current="page">Наши работы</li>
      </ol>
    </nav>
  </div>
</section>

<!-- КОНТЕНТ -->
<section class="section section-portfolio box-shadow-main-img">
  <div class="container">
    <div class="section-content-cards">
      <!-- Заголовок -->
      <div class="section-title text-center">
        <h3>Все наши работы</h3>
        <img src="<?php echo get_template_directory_uri(); ?>/assets/img/ico/points.svg" alt="Точки"
          class="img-fluid" />
      </div>

      <!-- Карточки -->
      <div class="row">
        <?php
        // Настройка пагинации - 15 работ на страницу
        $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

        $portfolio_query = new WP_Query(array(
          'post_type' => 'portfolio',
          'posts_per_page' => 15,
          'paged' => $paged,
          'post_status' => 'publish'
        ));

        if ($portfolio_query->have_posts()):
          $index = 0;
          while ($portfolio_query->have_posts()):
            $portfolio_query->the_post();
            $portfolio_id = get_the_ID();
            $featured_image = get_the_post_thumbnail_url($portfolio_id, 'medium');
            $portfolio_title = get_the_title();
            ?>

            <div class="col-12 col-md-6 col-lg-4 mb-4">
              <div onclick="openPortfolioGallery(<?php echo $index; ?>, <?php echo $portfolio_id; ?>);"
                class="card bg-transparent h-100 m-0 cursor-pointer"
                data-post-id="<?php echo $portfolio_id; ?>">
                <div class="card-img-container">
                  <?php if ($featured_image): ?>
                    <img src="<?php echo $featured_image; ?>" alt="<?php echo esc_attr($portfolio_title); ?>"
                      class="card-img-top" />
                  <?php endif; ?>
                </div>
                <div class="card-body text-center pb-0">
                  <h5 class="card-title mb-0"><?php echo $portfolio_title; ?></h5>
                </div>
              </div>
            </div>

            <?php
            $index++;
          endwhile;
        else:
          ?>
          <div class="col-12">
            <p class="text-center">Работы не найдены.</p>
          </div>
        <?php endif; ?>
      </div>

      <!-- Пагинация -->
      <?php if ($portfolio_query->max_num_pages > 1): ?>
        <nav class="mt-5">
          <ul class="pagination justify-content-center page-numbers flex-wrap">
            <?php
            $current_page = max(1, get_query_var('paged'));
            $total_pages = $portfolio_query->max_num_pages;

            // Предыдущая страница
            if ($current_page > 1): ?>
              <li class="page-item">
                <a class="page-link" href="<?php echo get_pagenum_link($current_page - 1); ?>" aria-label="Previous">
                  <span aria-hidden="true">←</span>
                </a>
              </li>
            <?php endif;

            // Первая страница
            if ($current_page > 3): ?>
              <li class="page-item <?php echo ($current_page == 1) ? 'active' : ''; ?>">
                <a class="page-link" href="<?php echo get_pagenum_link(1); ?>">1</a>
              </li>
              <?php if ($current_page > 4): ?>
                <li class="page-item">
                  <a class="page-link" style="cursor: default;">...</a>
                </li>
              <?php endif;
            endif;

            // Страницы вокруг текущей
            for ($i = max(1, $current_page - 2); $i <= min($total_pages, $current_page + 2); $i++): ?>
              <li class="page-item <?php echo ($current_page == $i) ? 'active' : ''; ?>">
                <a class="page-link" href="<?php echo get_pagenum_link($i); ?>"><?php echo $i; ?></a>
              </li>
            <?php endfor;

            // Последняя страница
            if ($current_page < $total_pages - 2): ?>
              <?php if ($current_page < $total_pages - 3): ?>
                <li class="page-item">
                  <a class="page-link" style="cursor: default;">...</a>
                </li>
              <?php endif; ?>
              <li class="page-item">
                <a class="page-link" href="<?php echo get_pagenum_link($total_pages); ?>"><?php echo $total_pages; ?></a>
              </li>
            <?php endif;

            // Следующая страница
            if ($current_page < $total_pages): ?>
              <li class="page-item">
                <a class="page-link" href="<?php echo get_pagenum_link($current_page + 1); ?>" aria-label="Next">
                  <span aria-hidden="true">→</span>
                </a>
              </li>
            <?php endif; ?>
          </ul>
        </nav>
      <?php endif; ?>

      <?php wp_reset_postdata(); ?>
    </div>
  </div>
</section>

<?php
// Подключаем общий шаблон модального окна (для архива используем стандартные ID)
$modal_id = 'productGalleryModal';
include get_template_directory() . '/template-parts/blocks/portfolio-slider/portfolio-gallery-modal.php';
?>

<?php get_footer(); ?>