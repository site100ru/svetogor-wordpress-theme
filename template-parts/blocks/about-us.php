<?php
/**
 * Block Name: О нас
 * Description: Блок с информацией о компании и изображением
 */

// Создаем уникальный ID для блока
$id = 'about-us-' . $block['id'];

// Добавляем дополнительные классы, если они есть
$className = '';
if (!empty($block['className'])) {
  $className .= ' ' . $block['className'];
}

// Получаем настройку фона из самого блока (локальная настройка)
$background_color = get_field('background_color_about_us_page') ?: 'bg-grey';

// Получаем глобальные данные из страницы настроек
$section_title = get_field('about_us_section_title', 'option');
$description = get_field('about_us_description', 'option');
$background_image = get_field('about_us_background_image', 'option');
$button_text = get_field('about_us_button_text', 'option');
$button_link = get_field('about_us_button_link', 'option');

// Определяем классы на основе настроек фона
$section_class = 'section section-half about-section';
$section_class .= $background_color === 'bg-grey' ? ' bg-grey' : '';
?>

<section id="<?php echo esc_attr($id); ?>"
  class="<?php echo esc_attr($section_class); ?><?php echo esc_attr($className); ?>">
  <div class="d-flex flex-wrap half-bg">
    <!-- Левая часть с картинкой (скрывается на мобилках) -->
    <div class="right-part d-none d-md-block">
      <?php if (!empty($background_image)): ?>
        <img loading="lazy" src="<?php echo esc_url($background_image['url']); ?>" alt="<?php echo esc_attr($background_image['alt'] ?: 'О нас'); ?>" class="img-cover">
      <?php endif; ?>
    </div>
    <!-- Правая часть с фоном -->
    <div class="left-part flex-grow-1"></div>
  </div>

  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-6 col-lg-7"></div>
      <div class="col-md-6 col-lg-5">
        <?php if (!empty($section_title)): ?>
          <h2 class="mb-1"><?php echo esc_html($section_title); ?></h2>

          <img loading="lazy" src="<?php echo get_template_directory_uri(); ?>/assets/img/ico/points.svg" alt="Точки" class="img-fluid">
          <br>
        <?php endif; ?>

        <?php if (!empty($description)): ?>
          <div class="order-description about-description">
            <?php echo wp_kses_post($description); ?>
          </div>
        <?php endif; ?>

        <?php if (!empty($button_text) && !empty($button_link)): ?>
          <a href="<?php echo esc_url($button_link); ?>" class="btn mb-0 mb-md-4 btn-big">
            <?php echo esc_html($button_text); ?>
          </a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>