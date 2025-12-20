<?php
/**
 * Block Name: Как заказать
 * Description: Блок с процессом заказа в 4 шага
 */

$id = 'how-to-order-' . $block['id'];
$className = !empty($block['className']) ? ' ' . $block['className'] : '';

//  ОПРЕДЕЛЕНИЯ ФОНА
$background_color = get_field('background_color_how_to_order');

// Проверяем глобальную переменную для фона
global $temp_how_to_order_data;
if ($temp_how_to_order_data && isset($temp_how_to_order_data['background_color_how_to_order'])) {
  $background_color = $temp_how_to_order_data['background_color_how_to_order'];
}

// Если значение пустое или не задано - белый по умолчанию
if (empty($background_color)) {
  $background_color = 'white';
}

$columns = get_field('columns') ?: '4';

// Контент из настроек (страница опций)
$section_title = get_field('section_title') ?: 'Как заказать';
$steps = get_field('steps', 'option');

// ЛОГИКА КЛАССОВ
$section_class = 'section section-how';
// Добавляем bg-grey ТОЛЬКО если background_color === 'grey'
if ($background_color === 'grey') {
  $section_class .= ' bg-grey';
}
// Для 'white' или любого другого значения - остается белым фоном

$column_class = 'col-md-6 col-lg-' . (12 / intval($columns));

?>

<section id="<?php echo esc_attr($id); ?>"
  class="<?php echo esc_attr($section_class); ?> <?php echo esc_attr($className); ?>">
  <div class="container">
    <div class="row">
      <?php if (!empty($section_title)): ?>
        <div class="section-title text-center">
          <h3><?php echo esc_html($section_title); ?></h3>
          <img src="<?php echo get_template_directory_uri(); ?>/assets/img/ico/points.svg" class="img-fluid"
            alt="Точки" />
        </div>
      <?php endif; ?>

      <?php if (!empty($steps) && is_array($steps)): ?>
        <div class="row justify-content-around">
          <?php foreach ($steps as $index => $step): ?>
            <div
              class="<?php echo esc_attr($column_class); ?> <?php echo $index === count($steps) - 1 ? 'mb-0' : 'mb-5 mb-lg-0'; ?>">
              <div class="row align-items-center">
                <div class="col-4 text-center">
                  <?php if (!empty($step['number_icon'])): ?>
                    <img src="<?php echo esc_url($step['number_icon']['url']); ?>"
                      alt="<?php echo esc_attr($step['number_icon']['alt'] ?: 'Шаг'); ?>" class="img-fluid">
                  <?php endif; ?>
                </div>
                <div class="col-4">
                  <?php if (!empty($step['process_icon'])): ?>
                    <img src="<?php echo esc_url($step['process_icon']['url']); ?>"
                      alt="<?php echo esc_attr($step['process_icon']['alt'] ?: 'Процесс'); ?>" class="img-fluid">
                  <?php endif; ?>
                </div>
              </div>
              <div class="row pt-3">
                <div class="col text-start">
                  <?php if (!empty($step['description'])): ?>
                    <p class="mb-0"><?php echo wp_kses_post($step['description']); ?></p>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <?php if (current_user_can('administrator')): ?>
          <div style="background: #ffeb3b; padding: 10px; margin: 10px 0;">
            <strong>БЛОК 'КАК ЗАКАЗАТЬ':</strong> Шаги не настроены в опциях сайта
          </div>
        <?php endif; ?>
      <?php endif; ?>
    </div>
  </div>
</section>