<?php

/**
 * Block Name: Контент с изображением
 * Description: Блок с текстом и изображением в колонках
 */

// Создаем уникальный ID для блока
$id = 'content-with-image-' . $block['id'];

// Добавляем дополнительные классы, если они есть
$className = '';
if (!empty($block['className'])) {
    $className .= ' ' . $block['className'];
}

// Получаем данные полей из ACF
$content = get_field('content');
$image = get_field('image');
$image_position = get_field('image_position') ?: 'right';
$background_color = get_field('background_color_content_with_image') ?: 'white';

// Размеры колонок
$text_col_md = get_field('text_col_md') ?: 6;
$text_col_xl = get_field('text_col_xl') ?: 5;
$image_col_md = get_field('image_col_md') ?: 6;
$image_col_xl = get_field('image_col_xl') ?: 6;
$gap_col_xl = get_field('gap_col_xl') ?: 1;

// Определяем классы на основе настроек
$section_class = 'section';
$section_class .= $background_color === 'bg-grey' ? ' bg-grey' : '';

// Определяем классы для колонок и порядок
if ($image_position === 'left') {
    // Изображение слева
    $image_order_mobile = 'order-3';
    $image_order_desktop = 'order-md-1';
    $text_order_mobile = 'order-1';
    $text_order_desktop = 'order-md-3';
    $gap_order = 'order-2 order-md-2';
} else {
    // Изображение справа (по умолчанию)
    $image_order_mobile = '';
    $image_order_desktop = '';
    $text_order_mobile = '';
    $text_order_desktop = '';
    $gap_order = '';
}
?>

<section id="<?php echo esc_attr($id); ?>" class="<?php echo esc_attr($section_class); ?> <?php echo esc_attr($className); ?>">
    <h2 class="d-none">Информативный раздел</h2>
    <div class="container">
        <div class="row align-items-start background_color_content_with_image">
            <?php if ($image_position === 'left'): ?>
                <!-- Изображение слева -->
                <div
                    class="col-12 col-md-<?php echo esc_attr($image_col_md); ?> col-xl-<?php echo esc_attr($image_col_xl); ?> <?php echo esc_attr($image_order_mobile . ' ' . $image_order_desktop); ?> section-image">
                    <?php if (!empty($image)): ?>
                        <img loading="lazy" src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($image['alt'] ?: 'Изображение'); ?>" class="img-fluid card-img-container">
                    <?php endif; ?>
                </div>

                <!-- Пустая колонка -->
                <?php if ($gap_col_xl > 0): ?>
                    <div class="d-none d-xl-block col-xl-<?php echo esc_attr($gap_col_xl); ?> <?php echo esc_attr($gap_order); ?>">
                    </div>
                <?php endif; ?>

                <!-- Текст справа -->
                <div
                    class="col-12 col-md-<?php echo esc_attr($text_col_md); ?> col-xl-<?php echo esc_attr($text_col_xl); ?> <?php echo esc_attr($text_order_mobile . ' ' . $text_order_desktop); ?> mb-4 mb-md-0">
                    <?php if (!empty($content)): ?>
                        <?php echo wp_kses_post($content); ?>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <!-- Текст слева -->
                <div
                    class="col-12 col-md-<?php echo esc_attr($text_col_md); ?> col-xl-<?php echo esc_attr($text_col_xl); ?> mb-3 mb-md-0">
                    <?php if (!empty($content)): ?>
                        <?php echo wp_kses_post($content); ?>
                    <?php endif; ?>
                </div>

                <!-- Пустая колонка -->
                <?php if ($gap_col_xl > 0): ?>
                    <div class="d-none d-xl-block col-xl-<?php echo esc_attr($gap_col_xl); ?>"></div>
                <?php endif; ?>

                <!-- Изображение справа -->
                <div
                    class="col-12 col-md-<?php echo esc_attr($image_col_md); ?> col-xl-<?php echo esc_attr($image_col_xl); ?> text-center section-image">
                    <?php if (!empty($image)): ?>
                        <img loading="lazy" src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($image['alt'] ?: 'Изображение'); ?>" class="img-fluid card-img-container">
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>