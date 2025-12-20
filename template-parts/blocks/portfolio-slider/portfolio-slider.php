<?php
/**
 * Оптимизированный шаблон блока "Слайдер портфолио"
 * template-parts/blocks/portfolio-slider/portfolio-slider.php
 */

// Получаем данные из полей ACF
$slider_title = get_field('slider_title') ?: 'Наши последние работы';
$slider_background = get_field('slider_background') ?: 'bg-grey';
$display_type = get_field('display_type') ?: 'latest';
$posts_count = get_field('posts_count') ?: 10;
$custom_posts = get_field('custom_posts');
$show_button = get_field('show_all_works_button');
$show_button = isset($portfolio_data['show_button']) 
    ? $portfolio_data['show_button'] 
    : get_field('show_all_works_button');

$button_text = get_field('button_text') ?: 'Все наши работы';
$prev_arrow = get_field('carousel_prev_arrow', 'option');
$next_arrow = get_field('carousel_next_arrow', 'option');

// Генерируем уникальный ID для слайдера
$slider_id = 'portfolio-slider-' . uniqid();
$modal_suffix = $slider_id;

// Определяем классы для фона
$bg_class = ($slider_background === 'bg-grey') ? 'bg-grey' : '';

// Получаем работы для отображения
$portfolio_posts = array();

if ($display_type === 'custom' && $custom_posts) {
  $portfolio_posts = $custom_posts;
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

// Подключаем стили и скрипты
wp_enqueue_style('glide-css', 'https://cdn.jsdelivr.net/npm/@glidejs/glide@3.6.0/dist/css/glide.core.min.css', array(), '3.6.0');
wp_enqueue_script('glide-js', 'https://cdn.jsdelivr.net/npm/@glidejs/glide@3.6.0/dist/glide.min.js', array(), '3.6.0', true);
wp_enqueue_script('portfolio-slider-js', get_template_directory_uri() . '/template-parts/blocks/portfolio-slider/portfolio-slider.js', array('jquery'), filemtime(get_template_directory() . '/template-parts/blocks/portfolio-slider/portfolio-slider.js'), true);

// Локализация переменных
wp_localize_script('portfolio-slider-js', 'portfolio_ajax', array(
  'ajax_url' => admin_url('admin-ajax.php'),
  'nonce' => wp_create_nonce('portfolio_grid_nonce')
));
?>

<section class="section section-works section-glide <?php echo esc_attr($bg_class); ?>">
  <div class="container">
    <div class="section-title text-center">
      <h3><?php echo esc_html($slider_title); ?></h3>
      <img src="<?php echo get_template_directory_uri(); ?>/assets/img/ico/points.svg" alt="Описание изображения"
        class="img-fluid" />
    </div>

    <div class="glide glide--ltr glide--carousel glide--swipeable" id="<?php echo esc_attr($slider_id); ?>">
      <div class="glide__track" data-glide-el="track">
        <ul class="glide__slides">
          <?php foreach ($portfolio_posts as $index => $post):
            $post_id = $post->ID;
            $post_title = $post->post_title;
            $featured_image = get_the_post_thumbnail_url($post_id, 'medium');

            // Если нет главного изображения, пропускаем
            if (!$featured_image)
              continue;
            ?>
            <li class="glide__slide">
              <div
                onclick="openPortfolioGallery(<?php echo $index; ?>, <?php echo $post_id; ?>, '<?php echo esc_attr($modal_suffix); ?>');"
                class="card bg-transparent cursor-pointer w-100"
                data-post-id="<?php echo $post_id; ?>">
                <div class="card-img-container">
                  <img src="<?php echo esc_url($featured_image); ?>" alt="<?php echo esc_attr($post_title); ?>"
                    class="card-img-top" />
                </div>
                <div class="card-body text-center">
                  <h5 class="card-title"><?php echo esc_html($post_title); ?></h5>
                </div>
              </div>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>

      <div class="glide__arrows" data-glide-el="controls">
        <button class="glide__arrow glide__arrow--left btn-carousel-left" data-glide-dir="&lt;">
          <img
            src="<?php echo esc_url(isset($prev_arrow['url']) ? $prev_arrow['url'] : get_template_directory_uri() . '/assets/img/ico/arrow-left.svg'); ?>"
            alt="Назад" loading="lazy" />
        </button>
        <button class="glide__arrow glide__arrow--right btn-carousel-right" data-glide-dir="&gt;">
          <img
            src="<?php echo esc_url(isset($next_arrow['url']) ? $next_arrow['url'] : get_template_directory_uri() . '/assets/img/ico/arrow-right.svg'); ?>"
            alt="Вперед" loading="lazy" />
        </button>
      </div>
    </div>
  </div>

  <?php if ($show_button): ?>
    <div class="text-center mt-5">
      <a href="<?php echo get_post_type_archive_link('portfolio'); ?>" class="btn">
        <?php echo esc_html($button_text); ?>
      </a>
    </div>
  <?php endif; ?>
</section>

<?php
// Подключаем общий шаблон модального окна
$modal_id = 'portfolioModal-' . $modal_suffix;
include get_template_directory() . '/template-parts/blocks/portfolio-slider/portfolio-gallery-modal.php';
?>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    // Проверяем наличие переменной portfolio_ajax
    if (typeof portfolio_ajax === 'undefined') {
      window.portfolio_ajax = {
        ajax_url: '<?php echo admin_url('admin-ajax.php'); ?>',
        nonce: '<?php echo wp_create_nonce('portfolio_gallery_nonce'); ?>'
      };
    }

    // Инициализируем Glide слайдер для этого блока
    if (typeof Glide !== 'undefined') {
      const glideSlider = new Glide('#<?php echo esc_js($slider_id); ?>', {
        type: 'carousel',
        startAt: 0,
        perView: 3,
        gap: 30,
        autoplay: 4000,
        hoverpause: true,
        breakpoints: {
          1024: {
            perView: 2,
            gap: 20
          },
          768: {
            perView: 1,
            gap: 15
          }
        }
      });

      glideSlider.mount();
    } else {
      console.error('Portfolio Slider Error: Glide is not available!');
    }
  });
</script>