<?php

/**
 * Block Name: Breadcrumbs Header
 * Description: Блок с хлебными крошками и заголовком страницы
 */

// Получаем поля блока
$page_title = get_field('breadcrumbs_block_page_title_unique_2024');
$parent_link = get_field('breadcrumbs_block_parent_link_unique');
$background_color = get_field('breadcrumbs_block_bg_color_unique_2024') ?: 'section-mini';
$show_title = get_field('breadcrumbs_block_show_title_unique');

// Если заголовок не указан, показываем сообщение
if (!$page_title) {
    echo '<p>Пожалуйста, укажите заголовок страницы в настройках блока</p>';
    return;
}
?>

<!-- ХЛЕБНЫЕ КРОШКИ/ ЗАГОЛОВОК -->
<section class="<?php echo esc_attr($background_color); ?>">
    <div class="container">
        <!-- Хлебные крошки -->
        <nav aria-label="breadcrumb" class="mb-0">
            <ol class="breadcrumb bg-transparent p-0 m-0">
                <!-- Иконка главной -->
                <li class="breadcrumb-item">
                    <a href="<?php echo home_url('/'); ?>" class="text-decoration-none text-secondary">
                        <img loading="lazy" src="<?php echo get_template_directory_uri(); ?>/assets/img/ico/breadcrumbs.svg" alt="Изображение домика">
                    </a>
                </li>

                <?php if ($parent_link && !empty($parent_link['url'])): ?>
                    <!-- Родительская страница -->
                    <li class="breadcrumb-item">
                        <a href="<?php echo esc_url($parent_link['url']); ?>" class="text-decoration-none text-secondary">
                            <?php echo esc_html($parent_link['title']); ?>
                        </a>
                    </li>
                <?php endif; ?>

                <!-- Текущая страница -->
                <li class="breadcrumb-item active" aria-current="page">
                    <?php echo esc_html($page_title); ?>
                </li>
            </ol>
        </nav>

        <?php if ($show_title): ?>
            <h1 class="text-center mb-0 section-mini-title">
                <?php echo esc_html($page_title); ?>
            </h1>
        <?php endif; ?>
    </div>
</section>