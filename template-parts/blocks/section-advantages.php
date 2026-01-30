<?php
/**
 * Block Name: Наши преимущества
 * Description: Блок для отображения преимуществ компании в колонках с иконками
 */

// Создаем уникальный ID для блока
$id = 'advantages-' . $block['id'];

// Добавляем дополнительные классы, если они есть
$className = '';
if (!empty($block['className'])) {
  $className .= ' ' . $block['className'];
}

// Получаем данные полей из ACF
$section_title = get_field('section_title');
$advantages = get_field('advantages', 'option');
$background_color = get_field('background_color_section_advantages') ?: 'white';
$columns = get_field('columns') ?: '2';
$column_class = 'col-lg-' . (12 / intval($columns));


// Определяем классы на основе настроек
$section_class = 'section section-advantage';
$section_class .= $background_color === 'bg-grey' ? ' bg-grey' : '';
?>

<section id="<?php echo esc_attr($id); ?>"
  class="<?php echo esc_attr($section_class); ?> <?php echo esc_attr($className); ?>">
  <div class="container">
    <div class="row">
      <?php if (!empty($section_title)): ?>
        <div class="section-title text-center">
          <h2><?php echo esc_html($section_title); ?></h2>

          <img width="62" height="14" loading="lazy" src="<?php echo get_template_directory_uri(); ?>/assets/img/ico/points.svg" class="img-fluid" alt="Точки">
        </div>
      <?php endif; ?>

      <?php if (!empty($advantages) && is_array($advantages)): ?>
        <?php foreach ($advantages as $advantage): ?>
          <div class="<?php echo esc_attr($column_class); ?> mb-4">
            <div class="row">
              <div class="col-3 col-md-2">
                <?php if (!empty($advantage['icon'])): ?>
                  <img loading="lazy" src="<?php echo esc_url($advantage['icon']['url']); ?>"
                    alt="<?php echo esc_attr($advantage['icon']['alt'] ?: 'Преимущество'); ?>" class="img-fluid">
                <?php endif; ?>
              </div>
              <div class="col-9 col-md-10">
                <?php if (!empty($advantage['title'])): ?>
                  <h3 class="advantage-title text-start">
                    <?php echo esc_html($advantage['title']); ?>
                  </h3>
                <?php endif; ?>

                <?php if (!empty($advantage['description'])): ?>
                  <div class="advantage-text text-start">
                    <?php echo wp_kses_post($advantage['description']); ?>
                  </div>
                <?php endif; ?>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>
</section>