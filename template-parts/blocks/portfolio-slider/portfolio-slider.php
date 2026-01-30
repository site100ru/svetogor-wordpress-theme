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
wp_enqueue_script('portfolio-slider-js', get_template_directory_uri() . '/template-parts/blocks/portfolio-slider/portfolio-slider.js', array('jquery'), filemtime(get_template_directory() . '/template-parts/blocks/portfolio-slider/portfolio-slider.js'), true);

// Локализация переменных
?>

<section class="section section-works section-glide <?php echo esc_attr($bg_class); ?>">
    <div class="container">
        <div class="section-title text-center">
            <h3><?php echo esc_html($slider_title); ?></h3>
            <img width="62" height="14" loading="lazy" src="<?php echo get_template_directory_uri(); ?>/assets/img/ico/points.svg" alt="Описание изображения" class="img-fluid">
        </div>

        <div class="glide glide-auto glide--ltr glide--carousel glide--swipeable" data-glide-perview="3"
            data-glide-gap="30"
            data-glide-autoplay="4000"
            data-glide-perview-md="2"
            data-glide-gap-md="20"
            data-glide-perview-sm="1"
            data-glide-gap-sm="15"
            id="<?php echo esc_attr($slider_id); ?>">
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
                                    <img loading="lazy" src="<?php echo esc_url($featured_image); ?>" alt="<?php echo esc_attr($post_title); ?>" class="card-img-top">
                                </div>
                                <div class="card-body text-center">
                                    <h4 class="h5 card-title"><?php echo esc_html($post_title); ?></h4>
                                </div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="glide__arrows" data-glide-el="controls">
                <button class="glide__arrow glide__arrow--left btn-carousel-left" data-glide-dir="&lt;">
                    <img loading="lazy"
                        src="<?php echo esc_url(isset($prev_arrow['url']) ? $prev_arrow['url'] : get_template_directory_uri() . '/assets/img/ico/arrow-left.svg'); ?>"
                        alt="Назад">
                </button>
                <button class="glide__arrow glide__arrow--right btn-carousel-right" data-glide-dir="&gt;">
                    <img loading="lazy"
                        src="<?php echo esc_url(isset($next_arrow['url']) ? $next_arrow['url'] : get_template_directory_uri() . '/assets/img/ico/arrow-right.svg'); ?>"
                        alt="Вперед">
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