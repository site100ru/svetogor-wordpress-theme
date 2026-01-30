<?php
/**
 * Блок "Слайдер комплексного оформления"
 */

// Получаем данные из полей ACF
$slider_title = get_field('slider_title') ?: 'Комплексное оформление Вашего бизнеса';
$slider_subtitle = get_field('slider_subtitle') ?: 'Найди свой и посмотри, в чем еще мы можем быть тебе полезным.';
$background_color = get_field('background_color_field_slider') ?: 'white';
$display_type = get_field('display_type') ?: 'all';
$selected_designs = get_field('selected_designs');

$prev_arrow = get_field('carousel_prev_arrow', 'option');
$next_arrow = get_field('carousel_next_arrow', 'option');

// Генерируем уникальный ID для слайдера
$slider_id = 'complex-design-slider-' . uniqid();

// Определяем классы для фона
$section_class = 'section section-glide section-comprehensive no-border box-shadow-main';
$section_class .= $background_color === 'bg-grey' ? ' bg-grey' : '';

// Получаем термины для отображения
$design_terms = array();

if ($display_type === 'selected' && $selected_designs) {
  // Получаем выбранные термины в заданном порядке
  foreach ($selected_designs as $design_item) {
    $term_id = $design_item['design_term'];
    $term = get_term($term_id, 'complex_design');
    if ($term && !is_wp_error($term)) {
      $design_terms[] = $term;
    }
  }
} else {
  // Получаем все термины
  $design_terms = get_terms(array(
    'taxonomy' => 'complex_design',
    'hide_empty' => false,
    'number' => 12, // Ограничиваем количество
  ));
}

// Если нет терминов для отображения, не показываем блок
if (empty($design_terms)) {
  return;
}

wp_enqueue_script('portfolio-slider-js', get_template_directory_uri() . '/template-parts/blocks/portfolio-slider/portfolio-slider.js', array('jquery'), filemtime(get_template_directory() . '/template-parts/blocks/portfolio-slider/portfolio-slider.js'), true);

?>

<section class="<?php echo esc_attr($section_class); ?>">
  <div class="container">
    <div class="section-title text-center">
      <h3><?php echo esc_html($slider_title); ?></h3>
      <?php if ($slider_subtitle): ?>
        <p class="color-32">
          <?php echo esc_html($slider_subtitle); ?>
        </p>
      <?php endif; ?>
      <img width="62" height="14" loading="lazy" src="<?php echo get_template_directory_uri(); ?>/assets/img/ico/points.svg" alt="Точки" class="img-fluid">
    </div>

    <div class="glide glide-auto glide--ltr glide--carousel glide--swipeable glide-comprehensive"
           data-glide-perview="4"
          data-glide-gap="12"
          data-glide-perview-md="3"
          data-glide-perview-sm="1"
     id="<?php echo esc_attr($slider_id); ?>">
      <div class="glide__track" data-glide-el="track">
        <ul class="glide__slides">
          <?php foreach ($design_terms as $term): ?>
            <?php
            // Получаем миниатюру термина
            $thumbnail_id = get_term_meta($term->term_id, 'thumbnail_id', true);
            $thumbnail_url = '';

            if ($thumbnail_id) {
              $thumbnail_url = wp_get_attachment_image_url($thumbnail_id, 'medium');
            }

            // Fallback изображение
            if (!$thumbnail_url) {
              $thumbnail_url = wc_placeholder_img_src();
            }

            // Ссылка на страницу термина
            $term_link = get_term_link($term);
            ?>
            <li class="glide__slide" style="margin-bottom: 10px; ">
              <a href="<?php echo esc_url($term_link); ?>" class="card-link w-100"
                style="text-decoration: none; color: inherit; display: block">
                <div class="card">
                  <div class="card-img-container">
                    <img loading="lazy" src="<?php echo esc_url($thumbnail_url); ?>" alt="<?php echo esc_attr($term->name); ?>" class="card-img-top">
                  </div>
                  <div class="card-body text-center">
                    <h4 class="card-title"><?php echo esc_html($term->name); ?></h4>
                    <span class="btn btn-invert">Подробнее</span>
                  </div>
                </div>
              </a>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>

      <?php if (count($design_terms) > 1): ?>
        <div class="glide__arrows" data-glide-el="controls">
          <button class="glide__arrow glide__arrow--left btn-carousel-left" data-glide-dir="&lt;"
            data-glide-el="controls">
            <img loading="lazy"
              src="<?php echo esc_url(isset($prev_arrow['url']) ? $prev_arrow['url'] : get_template_directory_uri() . '/assets/img/ico/arrow-left.svg'); ?>"
              alt="Назад">
          </button>

          <button class="glide__arrow glide__arrow--right btn-carousel-right" data-glide-dir="&gt;"
            data-glide-el="controls">
            <img loading="lazy"
              src="<?php echo esc_url(isset($next_arrow['url']) ? $next_arrow['url'] : get_template_directory_uri() . '/assets/img/ico/arrow-right.svg'); ?>"
              alt="Вперед">
          </button>
        </div>
      <?php endif; ?>
    </div>
  </div>
</section>
