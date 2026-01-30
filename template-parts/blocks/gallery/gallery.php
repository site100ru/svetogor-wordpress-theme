<?php

/**
 * Block Name: Gallery
 * Description: Блок галереи изображений
 */

// Получаем поля
$title = get_field('title') ?: 'Изображения';
$background_color = get_field('background_color_field_gallery') ?: 'bg-grey';
$alignment = get_field('alignment') ?: 'start';
$images = get_field('images');

// Определяем CSS классы для фона
$bg_class = ($background_color == 'bg-grey') ? 'bg-grey' : '';

// Определяем класс для расположения
$justify_class = '';
if (!empty($alignment)) {
    $justify_class = 'justify-content-' . $alignment;
}
?>

<!-- Изображения -->
<section class="section section-works section-glide <?php echo $bg_class; ?>">
    <div class="container">
        <div class="section-title text-center">
            <h3><?php echo esc_html($title); ?></h3>
            <img width="62" height="14" loading="lazy" src="<?php echo get_template_directory_uri(); ?>/assets/img/ico/points.svg" alt="Точки" class="img-fluid">
        </div>

        <div class="row <?php echo $justify_class; ?>">
            <?php if ($images && is_array($images)): ?>
                <?php foreach ($images as $index => $image):
                    $image_url = $image['sizes']['medium'] ?? $image['url'];
                    $image_alt = $image['alt'] ?: 'Изображение ' . ($index + 1);
                ?>
                    <div class="col-12 col-md-6 col-lg-4 mb-4">
                        <div class="card-img-container">
                            <img loading="lazy" src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($image_alt); ?>" class="card-img-top">
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="text-center py-4">
                        <p class="text-muted">Изображения не добавлены</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>