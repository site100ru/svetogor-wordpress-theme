<?php

/**
 * Архив портфолио
 */
get_header();

// Подключаем JS для портфолио
wp_enqueue_script('portfolio-slider-js', get_template_directory_uri() . '/template-parts/blocks/portfolio-slider/portfolio-slider.js', array('jquery'), filemtime(get_template_directory() . '/template-parts/blocks/portfolio-slider/portfolio-slider.js'), true);

wp_localize_script('portfolio-slider-js', 'portfolio_ajax', array(
    'ajax_url' => admin_url('admin-ajax.php'),
    'nonce' => wp_create_nonce('portfolio_grid_nonce')
));

// Hero секция
$portfolio_bg = get_field('portfolio_hero_bg', 'option');
render_hero_section('Наши работы', null, 'portfolio_hero_bg');

// Хлебные крошки
render_breadcrumbs(array(
    array('title' => 'Наши работы')
));
?>

<!-- КОНТЕНТ -->
<section class="section section-portfolio box-shadow-main-img">
    <div class="container">
        <div class="section-content-cards">
            <!-- Заголовок -->
            <div class="section-title text-center">
                <h3>Все наши работы</h3>
                <img loading="lazy" src="<?php echo get_template_directory_uri(); ?>/assets/img/ico/points.svg" alt="Точки" class="img-fluid">
            </div>

            <!-- Карточки -->
            <div class="row">
                <?php
                $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

                $portfolio_query = new WP_Query(array(
                    'post_type' => 'portfolio',
                    'posts_per_page' => 15,
                    'paged' => $paged,
                    'post_status' => 'publish'
                ));

                if ($portfolio_query->have_posts()):
                    $index = 0;
                    while ($portfolio_query->have_posts()):
                        $portfolio_query->the_post();
                        $portfolio_id = get_the_ID();
                        $featured_image = get_the_post_thumbnail_url($portfolio_id, 'medium');
                        $portfolio_title = get_the_title();
                ?>

                        <div class="col-12 col-md-6 col-lg-4 mb-4">
                            <div onclick="openPortfolioGallery(<?php echo $index; ?>, <?php echo $portfolio_id; ?>);"
                                class="card bg-transparent h-100 m-0 cursor-pointer" data-post-id="<?php echo $portfolio_id; ?>">
                                <div class="card-img-container">
                                    <?php if ($featured_image): ?>
                                        <img loading="lazy" src="<?php echo $featured_image; ?>" alt="<?php echo esc_attr($portfolio_title); ?>" class="card-img-top">
                                    <?php endif; ?>
                                </div>
                                <div class="card-body text-center pb-0">
                                    <h4 class="h5 card-title mb-0"><?php echo $portfolio_title; ?></h4>
                                </div>
                            </div>
                        </div>

                    <?php
                        $index++;
                    endwhile;
                else:
                    ?>
                    <div class="col-12">
                        <p class="text-center">Работы не найдены.</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Пагинация -->
            <?php if ($portfolio_query->max_num_pages > 1): ?>
                <?php custom_pagination($portfolio_query); ?>
            <?php endif; ?>

            <?php wp_reset_postdata(); ?>
        </div>
    </div>
</section>

<?php
// Подключаем модальное окно галереи
$modal_id = 'productGalleryModal';
include get_template_directory() . '/template-parts/blocks/portfolio-slider/portfolio-gallery-modal.php';
?>

<?php get_footer(); ?>