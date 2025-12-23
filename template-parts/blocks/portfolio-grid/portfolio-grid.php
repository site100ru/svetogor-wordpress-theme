<?php
/**
 * Шаблон блока "Сетка портфолио"
 */

// Получаем данные из полей ACF
$grid_title = get_field('grid_title') ?: 'Наши последние работы';
$grid_background = get_field('grid_background') ?: 'bg-grey';
$display_type = get_field('grid_display_type') ?: 'latest';
$posts_count = get_field('grid_posts_count') ?: 6;
$custom_posts = get_field('grid_custom_posts');
$show_button = get_field('grid_show_all_works_button');
$button_text = get_field('grid_button_text') ?: 'Все наши работы';
$portfolio_category_id = get_field('portfolio_category_id'); // ID категории портфолио (для товаров)

// Новые поля для выбора категории в блоке
$grid_portfolio_category = get_field('grid_portfolio_category');
$category_posts_count = get_field('grid_category_posts_count') ?: 6;

// Определяем ссылку для кнопки
$button_url = get_post_type_archive_link('portfolio'); // По умолчанию

// Если это блок с выбором категории
if ($display_type === 'category' && $grid_portfolio_category) {
  $category_link = get_term_link($grid_portfolio_category, 'portfolio_category');
  if (!is_wp_error($category_link)) {
    $button_url = $category_link;
  }
}
// Если это товар с переданной категорией
elseif ($portfolio_category_id) {
  $category_link = get_term_link($portfolio_category_id, 'portfolio_category');
  if (!is_wp_error($category_link)) {
    $button_url = $category_link;
  }
}

// Генерируем уникальный ID для блока
$grid_id = 'portfolio-grid-' . uniqid();

// Определяем классы для фона
$bg_class = ($grid_background === 'bg-grey') ? 'bg-grey' : '';

// Получаем работы для отображения
$portfolio_posts = array();

if ($display_type === 'custom' && $custom_posts) {
  $portfolio_posts = $custom_posts;
} elseif ($display_type === 'category' && $grid_portfolio_category) {
  // Получаем работы из выбранной категории
  $query_args = array(
    'post_type' => 'portfolio',
    'posts_per_page' => $category_posts_count,
    'post_status' => 'publish',
    'orderby' => 'date',
    'order' => 'DESC',
    'tax_query' => array(
      array(
        'taxonomy' => 'portfolio_category',
        'field' => 'term_id',
        'terms' => $grid_portfolio_category,
      ),
    ),
  );

  $portfolio_query = new WP_Query($query_args);
  if ($portfolio_query->have_posts()) {
    while ($portfolio_query->have_posts()) {
      $portfolio_query->the_post();
      $portfolio_posts[] = get_post();
    }
    wp_reset_postdata();
  }
} else {
  // Получаем последние работы
  $query_args = array(
    'post_type' => 'portfolio',
    'posts_per_page' => $posts_count,
    'post_status' => 'publish',
    'orderby' => 'date',
    'order' => 'DESC'
  );

  $portfolio_query = new WP_Query($query_args);
  if ($portfolio_query->have_posts()) {
    while ($portfolio_query->have_posts()) {
      $portfolio_query->the_post();
      $portfolio_posts[] = get_post();
    }
    wp_reset_postdata();
  }
}

// Если нет работ для отображения, не показываем блок
if (empty($portfolio_posts)) {
  return;
}

// Принудительно подключаем стили и скрипты для блока портфолио
wp_enqueue_script('portfolio-grid-js', get_template_directory_uri() . '/template-parts/blocks/portfolio-grid/portfolio-grid.js', array('jquery'), filemtime(get_template_directory() . '/template-parts/blocks/portfolio-grid/portfolio-grid.js'), true);

// ВАЖНО: Локализуем переменные для JavaScript
?>

<section class="section section-product box-shadow-main-img <?php echo esc_attr($bg_class); ?>">
  <div class="container">
    <div class="section-title text-center">
      <h3><?php echo esc_html($grid_title); ?></h3>
      <img src="<?php echo get_template_directory_uri(); ?>/assets/img/ico/points.svg" alt="Описание изображения"
        class="img-fluid" />
    </div>

    <!-- Карточки -->
    <div class="row g-4">
      <?php foreach ($portfolio_posts as $index => $post):
        $post_id = $post->ID;
        $post_title = $post->post_title;
        $featured_image = get_the_post_thumbnail_url($post_id, 'medium');
        $gallery_images = get_post_meta($post_id, 'portfolio_gallery', true);

        // Если нет главного изображения, пропускаем
        if (!$featured_image)
          continue;
        ?>
        <div class="col-12 col-md-6 col-lg-4">
          <div class="card-link portfolio-grid-item"
            onclick="openPortfolioGrid(<?php echo $index; ?>, <?php echo $post_id; ?>, '<?php echo esc_attr($grid_id); ?>');"
            data-post-id="<?php echo $post_id; ?>" style="cursor: pointer;">
            <div class="card">
              <div class="card-img-container">
                <img src="<?php echo esc_url($featured_image); ?>" alt="<?php echo esc_attr($post_title); ?>"
                  class="img-fluid" />
              </div>
              <div class="card-body text-center">
                <h5><?php echo esc_html($post_title); ?></h5>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <?php if ($show_button): ?>
    <div class="text-center mt-5">
      <a href="<?php echo esc_url($button_url); ?>" class="btn">
        <?php echo esc_html($button_text); ?>
      </a>
    </div>
  <?php endif; ?>
</section>

<!-- Модальное окно для галереи (будет создано динамически) -->
<div id="portfolioGridModal-<?php echo esc_attr($grid_id); ?>" class="portfolio-grid-modal" style="
        background: rgba(0, 0, 0, 0.85);
        display: none;
        position: fixed;
        top: 0;
        bottom: 0;
        left: 0;
        right: 0;
        z-index: 9999;
    ">
  <!-- Динамический слайдер будет загружаться здесь -->
  <div id="dynamic-carousel-container-grid-<?php echo esc_attr($grid_id); ?>"></div>

  <!-- Кнопка закрытия галереи -->
  <button type="button" onclick="closePortfolioGridModal('<?php echo esc_attr($grid_id); ?>');"
    class="btn-close btn-close-white" style="position: fixed; top: 25px; right: 25px; z-index: 99999"
    aria-label="Close"></button>
</div>