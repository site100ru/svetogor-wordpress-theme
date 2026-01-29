<?php

/**
 * Block Name: Только текст
 * Description: Блок только с текстовым контентом
 */

// Создаем уникальный ID для блока
$id = 'text-only-' . $block['id'];

// Добавляем дополнительные классы, если они есть
$className = '';
if (!empty($block['className'])) {
    $className .= ' ' . $block['className'];
}

// Получаем данные полей из ACF
$content = get_field('content');
$background_color = get_field('background_color_field_text_only') ?: 'white';
$container_width = get_field('container_width') ?: 12;
$columns_count = get_field('columns_count') ?: '1';
$text_alignment = get_field('text_alignment') ?: 'left';
$second_column_content = get_field('second_column_content');

// Определяем классы на основе настроек
$section_class = 'section';
$section_class .= $background_color === 'grey' ? ' bg-grey' : '';
$section_class .= $background_color === 'bg-grey' ? ' bg-grey' : '';

// Определяем классы выравнивания для одной колонки
$alignment_class = '';
if ($columns_count === '1') {
    switch ($text_alignment) {
        case 'center':
            $alignment_class = 'text-center mx-auto';
            break;
        case 'right':
            $alignment_class = 'text-end ms-auto';
            break;
        case 'left':
        default:
            $alignment_class = 'text-start me-auto';
            break;
    }
}

// Определяем ширину колонок
if ($columns_count === '2') {
    $column_width = intval($container_width / 2);
    $column_width = max(1, min(6, $column_width)); // Ограничиваем от 1 до 6
} else {
    $column_width = $container_width;
}
?>

<div id="<?php echo esc_attr($id); ?>" class="<?php echo esc_attr($section_class); ?> <?php echo esc_attr($className); ?>">
    <div class="container">
        <div class="row align-items-start justify-content-center background-color-field-text-only">

            <?php if ($columns_count === '1'): ?>
                <!-- Одна колонка -->
                <div class="col-<?php echo esc_attr($column_width); ?> <?php echo esc_attr($alignment_class); ?>">
                    <?php if (!empty($content)): ?>
                        <div class="text-content">
                            <?php echo wp_kses_post($content); ?>
                        </div>
                    <?php endif; ?>
                </div>

            <?php else: ?>
                <!-- Две колонки -->
                <div class="col-<?php echo esc_attr($column_width); ?>">
                    <?php if (!empty($content)): ?>
                        <div class="text-content">
                            <?php echo wp_kses_post($content); ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="col-<?php echo esc_attr($column_width); ?>">
                    <?php if (!empty($second_column_content)): ?>
                        <div class="text-content">
                            <?php echo wp_kses_post($second_column_content); ?>
                        </div>
                    <?php endif; ?>
                </div>

            <?php endif; ?>

        </div>
    </div>
</div>