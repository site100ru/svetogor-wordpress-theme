<?php
/**
 * Block Name: FAQ
 * Description: Блок частых вопросов с аккордеоном
 */

// Получаем поля
$title = get_field('title') ?: 'Частые вопросы';
$background_color = get_query_var('faq_bg') ?: (get_field('background_color_field_faq') ?: 'bg-grey');
$container_width = get_field('container_width') ?: 10;
$questions = get_query_var('faq_questions') ?: get_field('questions');

// Генерируем уникальный ID для аккордеона
$accordion_id = 'accordion-' . uniqid();

// Определяем CSS классы для фона
$bg_class = ($background_color == 'bg-grey') ? 'bg-grey' : '';
?>

<!-- Частые вопросы -->
<section class="section section-faq <?php echo $bg_class; ?>">
  <div class="container">
    <div class="section-title text-center">
      <h2><?php echo esc_html($title); ?></h2>
      <img src="<?php echo get_template_directory_uri(); ?>/assets/img/ico/points.svg" alt="Точки" class="img-fluid" />
    </div>

    <div class="col-lg-<?php echo $container_width; ?> text-center mx-auto">
      <div class="accordion text-start" id="<?php echo $accordion_id; ?>">
        <?php if ($questions && is_array($questions)): ?>
          <?php foreach ($questions as $index => $item):
            $question = $item['question_answer']['question'];
            $answer = $item['question_answer']['answer'];
            $expanded = $item['expanded'];

            // Генерируем уникальные ID для каждого элемента
            $item_id = $accordion_id . '-item-' . $index;
            $heading_id = 'heading-' . $item_id;
            $collapse_id = 'collapse-' . $item_id;

            // Определяем классы для открытого/закрытого состояния
            $button_class = $expanded ? 'accordion-button' : 'accordion-button collapsed';
            $collapse_class = $expanded ? 'accordion-collapse collapse show' : 'accordion-collapse collapse';
            $aria_expanded = $expanded ? 'true' : 'false';
            ?>
            <div class="accordion-item mb-3">
              <h3 class="accordion-header" id="<?php echo $heading_id; ?>">
                <button class="<?php echo $button_class; ?>" type="button" data-bs-toggle="collapse"
                  data-bs-target="#<?php echo $collapse_id; ?>" aria-expanded="<?php echo $aria_expanded; ?>"
                  aria-controls="<?php echo $collapse_id; ?>">
                  <?php echo esc_html($question); ?>
                </button>
              </h3>
              <div id="<?php echo $collapse_id; ?>" class="<?php echo $collapse_class; ?>"
                aria-labelledby="<?php echo $heading_id; ?>" data-bs-parent="#<?php echo $accordion_id; ?>">
                <div class="accordion-body">
                  <?php echo wp_kses_post($answer); ?>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="text-center py-4">
            <p class="text-muted">Вопросы не добавлены</p>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>