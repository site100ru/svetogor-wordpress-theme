<?php
/*
Template Name: Страница с хлебными крошками
*/

get_header();

// Получаем hero-фон через универсальную функцию
$hero_bg_url = get_hero_bg();
?>

<!-- HERO СЕКЦИЯ -->
<section class="hero-section<?php echo $hero_bg_url ? '' : ' hero-section--default'; ?>"
    <?php if ($hero_bg_url): ?>
    style="background-image: url('<?php echo esc_url($hero_bg_url); ?>');"
    <?php endif; ?>>
    <div class="container position-relative">
        <div class="row">
            <div class="col hero-content">
                <h1><?php the_title(); ?></h1>
            </div>
        </div>
    </div>
</section>

<!-- ХЛЕБНЫЕ КРОШКИ -->
<section class="section-mini">
    <h2 class=d-none>Секция навигации по сайту</h2>
    <div class="container">
        <?php render_page_breadcrumbs(get_the_title()); ?>
    </div>
</section>

<!-- ОСНОВНОЙ КОНТЕНТ -->
<?php
while (have_posts()): the_post();

    // Получаем контент
    $content = get_the_content();

    if (!empty($content)) {
        render_page_content($content);
    } else {
        // Заглушка для пустого контента
?>
        <section class="section single-page-content">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-12 col-lg-8">
                        <div class="page-content">
                            <p class="text-muted">Содержимое страницы не добавлено.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
<?php
    }

endwhile;
?>

<?php get_footer(); ?>