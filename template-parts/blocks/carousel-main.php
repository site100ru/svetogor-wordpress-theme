<?php
/**
 * Главная карусель блок
 */

// Получаем поля ACF
$slides = get_field('carousel_slides');

// Получаем стрелки из вкладки "Иконки" страницы настроек
$prev_arrow = get_field('carousel_prev_arrow', 'option');
$next_arrow = get_field('carousel_next_arrow', 'option');

// Если нет слайдов, не выводим блок
if (!$slides) {
  return;
}

// Автоматически генерируем уникальный ID для каждого блока
$block_id = 'carousel_' . $block['id'];
?>

<div id="<?php echo esc_attr($block_id); ?>" class="carousel slide pointer-event carouselMain" data-bs-ride="carousel">
  <div class="carousel-inner">
    <?php foreach ($slides as $index => $slide): ?>
      <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
        <?php if ($slide['background_image']): ?>
          <div class="carousel-background"
            style="background-image: url('<?php echo esc_url($slide['background_image']['url']); ?>')"></div>
        <?php endif; ?>

        <div class="carousel-content">
          <div class="container">
            <div class="row">
              <?php if ($slide['title']): ?>
                <?php
                // Первый слайд - h1, остальные - h2
                $title_tag = $index === 0 ? 'h1' : 'h2';
                ?>
                <<?php echo esc_attr($title_tag); ?> class="carousel-title">
                  <?php echo wp_kses_post($slide['title']); ?>
                </<?php echo esc_attr($title_tag); ?>>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

  <?php if (count($slides) > 1): ?>
    <!-- Стрелки навигации -->
    <button class="carousel-control-prev" type="button" data-bs-target="#<?php echo esc_attr($block_id); ?>"
      data-bs-slide="prev">
      <?php if ($prev_arrow): ?>
        <img loading="lazy" src="<?php echo esc_url($prev_arrow['url']); ?>" alt="<?php echo esc_attr($prev_arrow['alt'] ?: 'Назад'); ?>" class="carousel-control-icon">
      <?php else: ?>
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
      <?php endif; ?>
      <span class="visually-hidden">Назад</span>
    </button>

    <button class="carousel-control-next" type="button" data-bs-target="#<?php echo esc_attr($block_id); ?>"
      data-bs-slide="next">
      <?php if ($next_arrow): ?>
        <img loading="lazy" src="<?php echo esc_url($next_arrow['url']); ?>" alt="<?php echo esc_attr($next_arrow['alt'] ?: 'Далее'); ?>" class="carousel-control-icon">
      <?php else: ?>
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
      <?php endif; ?>
      <span class="visually-hidden">Далее</span>
    </button>
  <?php endif; ?>
</div>