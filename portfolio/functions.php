<?php

/**
 * Функции для системы портфолио
 */

// Регистрируем таксономию "Категории портфолио"
function register_portfolio_categories_taxonomy()
{
    $labels = array(
        'name' => 'Категории портфолио',
        'singular_name' => 'Категория портфолио',
        'search_items' => 'Поиск категорий',
        'all_items' => 'Все категории',
        'parent_item' => 'Родительская категория',
        'parent_item_colon' => 'Родительская категория:',
        'edit_item' => 'Редактировать категорию',
        'update_item' => 'Обновить категорию',
        'add_new_item' => 'Добавить новую категорию',
        'new_item_name' => 'Название новой категории',
        'menu_name' => 'Категории портфолио',
    );

    $args = array(
        'hierarchical' => true,
        'labels' => $labels,
        'show_ui' => true,
        'show_admin_column' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'portfolio-category'),
        'show_in_rest' => true,
    );

    register_taxonomy('portfolio_category', array('portfolio'), $args);
}
add_action('init', 'register_portfolio_categories_taxonomy');

// Регистрация кастомного типа записей "Portfolio"
function create_portfolio_post_type()
{
    $labels = array(
        'name' => 'Портфолио',
        'singular_name' => 'Работа',
        'menu_name' => 'Портфолио',
        'name_admin_bar' => 'Работа',
        'archives' => 'Архив работ',
        'attributes' => 'Атрибуты работы',
        'parent_item_colon' => 'Родительская работа:',
        'all_items' => 'Все работы',
        'add_new_item' => 'Добавить новую работу',
        'add_new' => 'Добавить новую',
        'new_item' => 'Новая работа',
        'edit_item' => 'Редактировать работу',
        'update_item' => 'Обновить работу',
        'view_item' => 'Посмотреть работу',
        'view_items' => 'Посмотреть работы',
        'search_items' => 'Поиск работ',
        'not_found' => 'Работы не найдены',
        'not_found_in_trash' => 'Работы не найдены в корзине',
        'featured_image' => 'Главное изображение',
        'set_featured_image' => 'Установить главное изображение',
        'remove_featured_image' => 'Удалить главное изображение',
        'use_featured_image' => 'Использовать как главное изображение',
        'insert_into_item' => 'Вставить в работу',
        'uploaded_to_this_item' => 'Загружено для этой работы',
        'items_list' => 'Список работ',
        'items_list_navigation' => 'Навигация по работам',
        'filter_items_list' => 'Фильтр работ',
    );

    $args = array(
        'label' => 'Работа',
        'description' => 'Портфолио работ',
        'labels' => $labels,
        'supports' => array('title', 'editor', 'thumbnail'),
        'hierarchical' => false,
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'menu_position' => 5,
        'menu_icon' => 'dashicons-portfolio',
        'show_in_admin_bar' => true,
        'show_in_nav_menus' => true,
        'can_export' => true,
        'has_archive' => 'portfolio',
        'exclude_from_search' => false,
        'publicly_queryable' => true,
        'capability_type' => 'post',
        'show_in_rest' => false,
    );

    register_post_type('portfolio', $args);
}
add_action('init', 'create_portfolio_post_type', 0);

// Функция для подключения шаблонов портфолио из папки portfolio
function load_portfolio_templates($template)
{
    // Для архивной страницы портфолио
    if (is_post_type_archive('portfolio')) {
        $portfolio_archive = get_template_directory() . '/portfolio/archive-portfolio.php';
        if (file_exists($portfolio_archive)) {
            return $portfolio_archive;
        }
    }

    // Для отдельной страницы портфолио
    if (is_singular('portfolio')) {
        $portfolio_single = get_template_directory() . '/portfolio/single-portfolio.php';
        if (file_exists($portfolio_single)) {
            return $portfolio_single;
        }
    }

    // Для категории портфолио
    if (is_tax('portfolio_category')) {
        $portfolio_category = get_template_directory() . '/portfolio/taxonomy-portfolio_category.php';
        if (file_exists($portfolio_category)) {
            return $portfolio_category;
        }
    }

    return $template;
}
add_filter('template_include', 'load_portfolio_templates');

// Добавление мета-бокса для загрузки галереи изображений
function add_portfolio_gallery_meta_box()
{
    add_meta_box(
        'portfolio_gallery',
        'Галерея изображений портфолио',
        'portfolio_gallery_meta_box_callback',
        'portfolio',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'add_portfolio_gallery_meta_box');

// Callback функция для мета-бокса галереи
function portfolio_gallery_meta_box_callback($post)
{
    wp_nonce_field('portfolio_gallery_meta_box', 'portfolio_gallery_meta_box_nonce');

    $gallery_images = get_post_meta($post->ID, 'portfolio_gallery', true);
    $gallery_images = $gallery_images ? $gallery_images : array();
?>

    <div id="portfolio-gallery-container">
        <div id="portfolio-gallery-images">
            <?php foreach ($gallery_images as $image_id):
                // Получаем URL изображения (миниатюра)
                $image_url = wp_get_attachment_image_src($image_id, 'thumbnail');
                $image_url = $image_url ? $image_url[0] : ''; // Если изображение найдено
                // Получаем текст для alt
                $alt_text = get_post_meta($image_id, '_wp_attachment_image_alt', true) ?: get_the_title($post->ID);
            ?>
                <div class="gallery-image-item" data-id="<?php echo $image_id; ?>">
                    <!-- Если URL существует, выводим изображение, иначе пустое изображение -->
                    <img loading="lazy" src="<?php echo esc_url($image_url); ?>" style="width: 100px; height: 100px; object-fit: cover;" alt="<?php echo esc_attr($alt_text); ?>">
                    <button type="button" class="remove-gallery-image" data-id="<?php echo $image_id; ?>">×</button>
                </div>
            <?php endforeach; ?>
        </div>

        <button type="button" id="add-portfolio-gallery-images" class="button">Добавить изображения</button>
        <input type="hidden" id="portfolio-gallery-ids" name="portfolio_gallery"
            value="<?php echo implode(',', $gallery_images); ?>">
    </div>
<?php
}

// Сохранение данных мета-бокса
function save_portfolio_gallery_meta_box($post_id)
{
    if (!isset($_POST['portfolio_gallery_meta_box_nonce'])) {
        return;
    }

    if (!wp_verify_nonce($_POST['portfolio_gallery_meta_box_nonce'], 'portfolio_gallery_meta_box')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (isset($_POST['post_type']) && 'portfolio' == $_POST['post_type']) {
        if (!current_user_can('edit_page', $post_id)) {
            return;
        }
    } else {
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
    }

    if (isset($_POST['portfolio_gallery'])) {
        $gallery_ids = array_filter(explode(',', $_POST['portfolio_gallery']));
        update_post_meta($post_id, 'portfolio_gallery', $gallery_ids);
    } else {
        delete_post_meta($post_id, 'portfolio_gallery');
    }
}
add_action('save_post', 'save_portfolio_gallery_meta_box');

// AJAX endpoint для получения изображений галереи
function get_portfolio_gallery_images()
{
    // Проверяем nonce
    if (isset($_GET['nonce'])) {
        $nonce_valid = wp_verify_nonce($_GET['nonce'], 'portfolio_grid_nonce') ||
            wp_verify_nonce($_GET['nonce'], 'portfolio_gallery_nonce');

        if (!$nonce_valid) {
            wp_send_json_error('Неверный nonce');
            return;
        }
    }

    // Проверяем наличие post_id
    if (!isset($_GET['post_id'])) {
        wp_send_json_error('Не указан ID поста');
        return;
    }

    $post_id = intval($_GET['post_id']);

    // Проверяем, что пост существует и это портфолио
    $post = get_post($post_id);
    if (!$post || $post->post_type !== 'portfolio') {
        wp_send_json_error('Пост не найден или неверный тип');
        return;
    }

    $gallery_images = get_post_meta($post_id, 'portfolio_gallery', true);

    if (!$gallery_images || !is_array($gallery_images)) {
        wp_send_json_error('Галерея не найдена');
        return;
    }

    $images = array();
    foreach ($gallery_images as $image_id) {
        $image_url = wp_get_attachment_image_src($image_id, 'large');
        if ($image_url) {
            $images[] = array(
                'id' => $image_id,
                'url' => $image_url[0],
                'alt' => get_post_meta($image_id, '_wp_attachment_image_alt', true) ?: get_the_title($post_id)
            );
        }
    }

    wp_send_json_success($images);
}
add_action('wp_ajax_get_portfolio_gallery', 'get_portfolio_gallery_images');
add_action('wp_ajax_nopriv_get_portfolio_gallery', 'get_portfolio_gallery_images');

// Обновление rewrite rules при активации темы
function portfolio_rewrite_flush()
{
    create_portfolio_post_type();
    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'portfolio_rewrite_flush');

/**
 * Подключение скриптов для одиночной страницы портфолио
 */
function enqueue_portfolio_single_scripts()
{
    if (is_singular('portfolio')) {
        wp_enqueue_script(
            'portfolio-single',
            get_template_directory_uri() . '/assets/js/portfolio-single.js',
            ['jquery'],
            '1.0.0',
            true
        );
    }
}
add_action('wp_enqueue_scripts', 'enqueue_portfolio_single_scripts');
