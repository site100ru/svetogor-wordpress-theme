<?php

/**
 * Универсальные вспомогательные функции для шаблонов
 */

/**
 * Вывод хлебных крошек
 */
function render_breadcrumbs($items = array())
{
?>
    <section class="section-mini">
        <div class="container">
            <nav aria-label="breadcrumb" class="mb-0">
                <ol class="breadcrumb bg-transparent p-0 m-0">
                    <!-- Домашняя страница -->
                    <li class="breadcrumb-item">
                        <a href="<?php echo home_url(); ?>" class="text-decoration-none text-secondary">
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/img/ico/breadcrumbs.svg" loading="lazy" alt="Изображение домика" />
                        </a>
                    </li>

                    <?php foreach ($items as $index => $item): ?>
                        <?php if (isset($item['url'])): ?>
                            <li class="breadcrumb-item">
                                <a href="<?php echo esc_url($item['url']); ?>" class="text-decoration-none text-secondary">
                                    <?php echo esc_html($item['title']); ?>
                                </a>
                            </li>
                        <?php else: ?>
                            <li class="breadcrumb-item active" aria-current="page">
                                <?php echo esc_html($item['title']); ?>
                            </li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ol>
            </nav>
        </div>
    </section>
<?php
}

/**
 * Вывод Hero-секции с универсальной поддержкой фонов
 */
function render_hero_section($title, $bg = null, $bg_option = null, $extra_classes = [])
{
    $hero_bg_url = '';

    // Приоритет 1: Конкретное изображение передано вручную
    if ($bg && is_numeric($bg)) {
        // Передан ID изображения
        $hero_bg_data = wp_get_attachment_image_src($bg, 'full');
        if ($hero_bg_data) {
            $hero_bg_url = $hero_bg_data[0];
        }
    } elseif ($bg && is_string($bg)) {
        // Передан URL изображения напрямую
        $hero_bg_url = $bg;
    }

    // Приоритет 2: Автоопределение через универсальную систему
    if (empty($hero_bg_url)) {
        $auto_bg = get_hero_bg();
        if ($auto_bg) {
            $hero_bg_url = $auto_bg;
        }
    }

    // Приоритет 3: ACF опция (fallback для архивных страниц)
    if (empty($hero_bg_url) && $bg_option) {
        $bg_field = get_field($bg_option, 'option');
        if ($bg_field && isset($bg_field['url'])) {
            $hero_bg_url = $bg_field['url'];
        }
    }

    // Формируем CSS классы
    $classes = ['hero-section'];

    if (empty($hero_bg_url)) {
        $classes[] = 'hero-section--default';
    }

    if (!empty($extra_classes)) {
        $classes = array_merge($classes, (array) $extra_classes);
    }

    $class_string = implode(' ', array_filter($classes));
    $style = $hero_bg_url ? 'style="background-image: url(\'' . esc_url($hero_bg_url) . '\');"' : '';
?>
    <section class="<?php echo esc_attr($class_string); ?>" <?php echo $style; ?>>
        <div class="container position-relative">
            <div class="row">
                <div class="col hero-content">
                    <h1><?php echo esc_html($title); ?></h1>
                </div>
            </div>
        </div>
    </section>
<?php
}

/**
 * Автоматически получает фон через get_hero_bg()
 */
function render_simple_hero($title, $extra_classes = [])
{
    render_hero_section($title, null, null, $extra_classes);
}

/**
 * Универсальная функция для вывода карточки (статья, новость, услуга)
 */
function render_card($post_id, $post_type = 'post')
{
    $title = get_the_title($post_id);
    $link = get_permalink($post_id);
    $featured_image = get_the_post_thumbnail_url($post_id, 'medium');
    $date = get_the_date('d/m/Y', $post_id);

    // Получаем excerpt в зависимости от типа поста
    switch ($post_type) {
        case 'news':
            $excerpt = get_news_excerpt($post_id);
            break;
        case 'services':
            $excerpt = get_service_excerpt($post_id);
            break;
        default:
            $excerpt = get_article_excerpt($post_id);
    }
?>
    <div class="col-12 col-md-6 col-lg-4 mb-4">
        <a href="<?php echo esc_url($link); ?>" class="card h-100 m-0 bg-linear-gradient-wrapper text-decoration-none">
            <div class="card-img-container">
                <?php if ($featured_image): ?>
                    <img src="<?php echo esc_url($featured_image); ?>" alt="<?php echo esc_attr($title); ?>" class="card-img-top">
                <?php endif; ?>
            </div>
            <div class="card-body d-flex flex-column">
                <h5 class="card-title"><?php echo esc_html($title); ?></h5>
                <p class="card-text mb-0"><?php echo esc_html($excerpt); ?></p>
                <?php if ($post_type !== 'services'): ?>
                    <div class="mt-auto d-flex justify-content-start align-items-center pt-2">
                        <span class="text-muted small"><?php echo esc_html($date); ?></span>
                    </div>
                <?php endif; ?>
            </div>
        </a>
    </div>
<?php
}

/**
 * Универсальная карточка для услуги (горизонтальная)
 */
function render_service_card($post_id)
{
    $title = get_the_title($post_id);
    $link = get_permalink($post_id);
    $featured_image = get_the_post_thumbnail_url($post_id, 'medium');
    $excerpt = get_service_excerpt($post_id);
?>
    <div class="col-12 col-md-6 mb-4">
        <a href="<?php echo esc_url($link); ?>" class="card card-services-arhive">
            <div class="row g-0 align-items-center h-100">
                <div class="col-12 col-lg-4 text-center card-img-container">
                    <?php if ($featured_image): ?>
                        <img src="<?php echo esc_url($featured_image); ?>" alt="<?php echo esc_attr($title); ?>" class="img-fluid" />
                    <?php else: ?>
                        <img src="<?php echo wc_placeholder_img_src(); ?>" alt="<?php echo esc_attr($title); ?>" class="img-fluid" />
                    <?php endif; ?>
                </div>
                <div class="col-12 col-lg-8">
                    <div class="card-body">
                        <h5 class="card-title mb-3"><?php echo esc_html($title); ?></h5>
                        <p class="card-text"><?php echo esc_html($excerpt); ?></p>
                        <span class="btn btn-invert">Подробнее</span>
                    </div>
                </div>
            </div>
        </a>
    </div>
<?php
}

/**
 * Универсальный архивный шаблон
 */
function render_archive_template($args = array())
{
    $defaults = array(
        'post_type' => 'post',
        'title' => 'Архив',
        'posts_per_page' => 15,
        'breadcrumbs' => array(),
        'card_type' => 'default',
        'no_posts_message' => 'Записи не найдены',
        'no_posts_text' => 'В данный момент записей нет. Зайдите позже!',
        'show_pagination' => true,
    );

    $args = wp_parse_args($args, $defaults);
    $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

    $query = new WP_Query(array(
        'post_type' => $args['post_type'],
        'posts_per_page' => $args['posts_per_page'],
        'paged' => $paged,
        'post_status' => 'publish',
        'orderby' => 'date',
        'order' => 'DESC'
    ));
?>

    <!-- ХЛЕБНЫЕ КРОШКИ -->
    <?php if (!empty($args['breadcrumbs'])): ?>
        <?php render_breadcrumbs($args['breadcrumbs']); ?>
    <?php endif; ?>

    <!-- КОНТЕНТ -->
    <section class="section section-page-comprehensive box-shadow-main">
        <div class="container">
            <div class="section-content-cards">
                <!-- Заголовок -->
                <div class="section-title text-center">
                    <h3><?php echo esc_html($args['title']); ?></h3>
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/img/ico/points.svg" alt="Точки" class="img-fluid">
                </div>

                <!-- Карточки -->
                <div class="row <?php echo $args['card_type'] === 'service' ? 'row-cards' : ''; ?>">
                    <?php if ($query->have_posts()): ?>
                        <?php while ($query->have_posts()):
                            $query->the_post(); ?>
                            <?php
                            if ($args['card_type'] === 'service') {
                                render_service_card(get_the_ID());
                            } else {
                                render_card(get_the_ID(), $args['post_type']);
                            }
                            ?>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="col-12">
                            <div class="text-center py-5">
                                <h4><?php echo esc_html($args['no_posts_message']); ?></h4>
                                <p class="text-muted"><?php echo esc_html($args['no_posts_text']); ?></p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Пагинация -->
                <?php if ($args['show_pagination'] && $query->max_num_pages > 1): ?>
                    <?php custom_pagination($query); ?>
                <?php endif; ?>

                <?php wp_reset_postdata(); ?>
            </div>
        </div>
    </section>
<?php
}

/**
 * Универсальный вывод "Другие записи" (новости/статьи)
 */
function render_related_posts($current_id, $post_type, $title, $archive_link, $button_text = 'Все записи')
{
    $related_query = new WP_Query(array(
        'post_type' => $post_type,
        'posts_per_page' => 3,
        'post__not_in' => array($current_id),
        'post_status' => 'publish',
        'orderby' => 'date',
        'order' => 'DESC'
    ));

    if (!$related_query->have_posts()) {
        return;
    }
?>
    <section class="section section-glide box-shadow-main no-border bg-grey">
        <div class="container">
            <div class="section-title text-center">
                <h2><?php echo esc_html($title); ?></h2>
                <img src="<?php echo get_template_directory_uri(); ?>/assets/img/ico/points.svg" alt="Точки" class="img-fluid">
            </div>

            <div class="tab-pane fade show active">
                <div class="row g-4 justify-content-center">
                    <?php while ($related_query->have_posts()):
                        $related_query->the_post(); ?>
                        <?php render_card(get_the_ID(), $post_type); ?>
                    <?php endwhile; ?>
                </div>

                <div class="mt-5 text-center">
                    <a href="<?php echo esc_url($archive_link); ?>" class="btn"><?php echo esc_html($button_text); ?></a>
                </div>
            </div>
        </div>
    </section>
<?php
    wp_reset_postdata();
}
