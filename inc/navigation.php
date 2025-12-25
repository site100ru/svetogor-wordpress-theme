<?php

/**
 * ОПТИМИЗИРОВАННАЯ НАВИГАЦИЯ SVETOGOR
 * Убраны дублирования, улучшена структура
 */

if (!defined('ABSPATH')) exit;

// ============================================================================
// CORE: Получение данных
// ============================================================================

/**
 * Получение категорий ВТОРОГО уровня (дочерние от корневых)
 */
function svetogor_get_second_level_categories()
{
    static $cache = null;

    if ($cache !== null) return $cache;

    if (!class_exists('WooCommerce')) return [];

    $all_categories = get_terms([
        'taxonomy' => 'product_cat',
        'hide_empty' => false,
        'meta_query' => [
            ['key' => 'show_in_header', 'value' => '1', 'compare' => '=']
        ],
        'orderby' => 'menu_order',
        'order' => 'ASC'
    ]);

    if (!$all_categories || is_wp_error($all_categories)) return [];

    $second_level = [];
    foreach ($all_categories as $cat) {
        if ($cat->parent == 0) continue;

        $parent_term = get_term($cat->parent, 'product_cat');
        if ($parent_term && !is_wp_error($parent_term) && $parent_term->parent == 0) {
            $second_level[] = $cat;
        }
    }

    $cache = $second_level;
    return $second_level;
}

/**
 * Получение категорий ТРЕТЬЕГО уровня
 */
function svetogor_get_third_level_categories($parent_id)
{
    if (!class_exists('WooCommerce')) return [];

    $third_level = get_terms([
        'taxonomy' => 'product_cat',
        'hide_empty' => false,
        'parent' => $parent_id,
        'meta_query' => [
            ['key' => 'show_in_header', 'value' => '1', 'compare' => '=']
        ],
        'orderby' => 'menu_order',
        'order' => 'ASC',
        'number' => 20
    ]);

    return $third_level && !is_wp_error($third_level) ? $third_level : [];
}

/**
 * Получение товаров категории
 */
function svetogor_get_category_products($category_id, $limit = 5)
{
    if (!class_exists('WooCommerce')) return [];

    return get_posts([
        'post_type' => 'product',
        'posts_per_page' => $limit,
        'post_status' => 'publish',
        'tax_query' => [[
            'taxonomy' => 'product_cat',
            'field' => 'term_id',
            'terms' => $category_id,
        ]],
        'orderby' => 'menu_order',
        'order' => 'ASC'
    ]);
}

/**
 * Безопасное получение иконки
 */
function svetogor_get_category_icon($term_id)
{
    $thumbnail_id = get_term_meta($term_id, 'thumbnail_id', true);
    if ($thumbnail_id && $url = wp_get_attachment_image_url($thumbnail_id, 'thumbnail')) {
        return $url;
    }
    return get_template_directory_uri() . '/assets/img/ico/default-category.svg';
}

/**
 * Создание якорной ссылки
 */
function svetogor_create_anchor_link($parent_category, $current_category)
{
    $parent_link = get_term_link($parent_category);
    if (is_wp_error($parent_link)) {
        return get_term_link($current_category);
    }
    return $parent_link . '#' . $current_category->slug;
}

// ============================================================================
// RENDER: Вывод элементов меню
// ============================================================================

/**
 * Вывод категорий ВТОРОГО уровня (левая колонка)
 */
function svetogor_render_second_level()
{
    $categories = svetogor_get_second_level_categories();

    if (empty($categories)) {
        echo '<p>Категории не найдены</p>';
        return;
    }

    $first = true;
    foreach ($categories as $cat) {
        $icon = svetogor_get_category_icon($cat->term_id);
        $active = $first ? ' active' : '';
?>
        <a class="nav-link<?= $active ?>"
            href="<?= get_term_link($cat) ?>"
            data-target="<?= $cat->term_id ?>">
            <span class="category-icon">
                <img loading="lazy" src="<?= esc_url($icon) ?>" alt="<?= esc_attr($cat->name) ?>">
            </span>
            <span><?= esc_html($cat->name) ?></span>
            <span class="category-arrow"></span>
        </a>
    <?php
        $first = false;
    }
}

/**
 * Вывод товаров подкатегории
 */
function svetogor_render_products($category_id)
{
    $products = svetogor_get_category_products($category_id);

    if (empty($products)) return;

    echo '<ul class="subcategory-list">';
    foreach ($products as $product) {
    ?>
        <li>
            <a href="<?= get_permalink($product->ID) ?>">
                <?= esc_html($product->post_title) ?>
            </a>
        </li>
    <?php
    }
    echo '</ul>';
}

/**
 * Вывод категорий ТРЕТЬЕГО уровня (правая колонка)
 */
function svetogor_render_third_level()
{
    $second_level = svetogor_get_second_level_categories();

    if (empty($second_level)) return;

    $first = true;
    foreach ($second_level as $second_cat) {
        $active = $first ? ' active' : '';
        $third_level = svetogor_get_third_level_categories($second_cat->term_id);
    ?>
        <div class="subcategory-content<?= $active ?>" id="<?= $second_cat->term_id ?>-content">
            <div class="row">
                <?php if (!empty($third_level)): ?>
                    <?php foreach ($third_level as $third_cat): ?>
                        <div class="col-md-3">
                            <a href="<?= esc_url(svetogor_create_anchor_link($second_cat, $third_cat)) ?>"
                                class="subcategory-title h5">
                                <?= esc_html($third_cat->name) ?>
                            </a>
                            <?php svetogor_render_products($third_cat->term_id); ?>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-md-12">
                        <p>Подкатегории будут добавлены позже</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php
        $first = false;
    }
}

/**
 * Вывод мега-меню "Продукция"
 */
function svetogor_render_products_megamenu($title)
{
    ?>
    <li class="nav-item nav-item-hero dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="productsDropdown"
            role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <?= esc_html($title) ?>
        </a>

        <div class="dropdown-menu mega-menu" role="region" aria-labelledby="productsDropdown">
            <div class="container">
                <div class="row">
                    <!-- Левая колонка -->
                    <div class="col-lg-3">
                        <div class="category-menu">
                            <nav class="nav flex-column">
                                <?php svetogor_render_second_level(); ?>
                            </nav>
                        </div>
                    </div>

                    <!-- Правая колонка -->
                    <div class="col-lg-9">
                        <?php svetogor_render_third_level(); ?>
                    </div>
                </div>
            </div>
        </div>
    </li>
<?php
}

/**
 * Вывод обычного пункта меню
 */
function svetogor_render_menu_item($item)
{
    // Проверка на "Продукцию"
    if ($item->ID == 1226 || trim($item->title) === 'Продукция') {
        svetogor_render_products_megamenu($item->title);
        return;
    }
?>
    <li class="nav-item nav-item-hero">
        <a class="nav-link" href="<?= esc_url($item->url) ?>">
            <?= esc_html($item->title) ?>
        </a>
    </li>
    <?php
}

/**
 * Вывод всего меню с разделителями
 */
function svetogor_render_main_menu()
{
    $menu_locations = get_nav_menu_locations();

    if (!isset($menu_locations['header_menu'])) {
        echo '<li><a href="' . home_url() . '">Главная</a></li>';
        return;
    }

    $menu_items = wp_get_nav_menu_items($menu_locations['header_menu']);
    $parent_items = array_filter($menu_items ?: [], fn($item) => $item->menu_item_parent == 0);

    // Находим позицию "Продукции"
    $products_position = -1;
    foreach ($parent_items as $i => $item) {
        if ($item->ID == 1226 || trim($item->title) === 'Продукция') {
            $products_position = $i;
            break;
        }
    }

    $separator_svg = get_template_directory_uri() . '/assets/img/ico/menu-decoration-point.svg';

    foreach ($parent_items as $i => $item) {
        // Разделитель (кроме первого и после "Продукции")
        if ($i > 0 && ($i - 1) !== $products_position) {
    ?>
            <li class="nav-item d-none d-lg-inline align-content-center">
                <img loading="lazy" class="nav-link" src="<?= $separator_svg ?>" alt="Разделитель">
            </li>
    <?php
        }

        svetogor_render_menu_item($item);
    }
}

// ============================================================================
// MOBILE: Мобильное меню
// ============================================================================

function svetogor_render_mobile_menu()
{
    ?>
    <div class="offcanvas offcanvas-end" tabindex="-1" id="mobileMenu">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title">Меню</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body position-relative">
            <?php
            svetogor_render_mobile_level1();
            svetogor_render_mobile_level2();
            svetogor_render_mobile_level3();
            ?>
        </div>
    </div>
<?php
}

function svetogor_render_mobile_level1()
{
    $menu_locations = get_nav_menu_locations();
    $menu_items = isset($menu_locations['header_menu'])
        ? wp_get_nav_menu_items($menu_locations['header_menu'])
        : [];
    $parent_items = array_filter($menu_items ?: [], fn($item) => $item->menu_item_parent == 0);
?>
    <div class="mobile-view level-1 active" id="main-menu-view">
        <ul class="navbar-nav">
            <?php foreach ($parent_items as $item):
                $is_products = ($item->ID == 1226 || trim($item->title) === 'Продукция');
            ?>
                <li class="nav-item">
                    <?php if ($is_products): ?>
                        <div class="mobile-menu-item" data-view="products-menu-view">
                            <div class="d-flex align-items-center">
                                <span><?= esc_html($item->title) ?></span>
                            </div>
                            <span class="arrow"></span>
                        </div>
                    <?php else: ?>
                        <a class="nav-link" href="<?= esc_url($item->url) ?>">
                            <?= esc_html($item->title) ?>
                        </a>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>

            <?php svetogor_render_mobile_footer_info(); ?>
        </ul>
    </div>
<?php
}

function svetogor_render_mobile_level2()
{
    $categories = svetogor_get_second_level_categories();
?>
    <div class="mobile-view level-2" id="products-menu-view">
        <h5 class="mobile-view-title">Продукция</h5>
        <?php foreach ($categories as $cat):
            $icon = svetogor_get_category_icon($cat->term_id);
        ?>
            <div class="mobile-menu-item" data-view="<?= $cat->term_id ?>-menu-view">
                <div class="d-flex align-items-center">
                    <img loading="lazy" src="<?= esc_url($icon) ?>"
                        alt="<?= esc_attr($cat->name) ?>"
                        style="width:20px;height:20px;margin-right:10px">
                    <span><?= esc_html($cat->name) ?></span>
                </div>
                <span class="arrow"></span>
            </div>
        <?php endforeach; ?>
        <button class="back-button" data-view="main-menu-view">Назад в меню</button>
    </div>
    <?php
}

function svetogor_render_mobile_level3()
{
    $second_level = svetogor_get_second_level_categories();

    foreach ($second_level as $second_cat) {
        $third_level = svetogor_get_third_level_categories($second_cat->term_id);
    ?>
        <div class="mobile-view level-3" id="<?= $second_cat->term_id ?>-menu-view">
            <a href="<?= get_term_link($second_cat) ?>" class="mobile-view-title h5">
                <?= esc_html($second_cat->name) ?>
            </a>

            <?php foreach ($third_level as $third_cat):
                $products = svetogor_get_category_products($third_cat->term_id);
            ?>
                <div class="mb-4">
                    <a href="<?= get_term_link($third_cat) ?>">
                        <?= esc_html($third_cat->name) ?>
                    </a>

                    <?php if (!empty($products)): ?>
                        <ul class="list-unstyled ps-3 mt-2">
                            <?php foreach ($products as $product): ?>
                                <li class="mb-2">
                                    <a href="<?= get_permalink($product->ID) ?>" class="text-decoration-none">
                                        <?= esc_html($product->post_title) ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>

            <button class="back-button" data-view="products-menu-view">Назад к продукции</button>
        </div>
    <?php
    }
}

function svetogor_render_mobile_footer_info()
{
    // Получаем данные через существующие функции
    $company_address = get_company_address();
    $company_email = get_company_email();
    $main_phone_data = get_main_phone_data();
    $header_socials = get_header_social_networks();

    ?>
    <li class="nav-item d-lg-none py-2">
        <!-- Адрес -->
        <?php if ($company_address): ?>
            <div class="d-flex align-items-center gap-2">
                <img loading="lazy"
                    src="<?= esc_url(get_contact_icon_url('location_icon', 'location-ico.svg')) ?>"
                    alt="Адрес"
                    style="max-height:14px">
                <span style="font-size:14px"><?= esc_html($company_address) ?></span>
            </div>
        <?php endif; ?>

        <!-- Телефон -->
        <?php if ($main_phone_data && isset($main_phone_data['phone_number'])): ?>
            <a class="top-menu-tel nav-link price-text"
                style="font-size:18px"
                href="tel:<?= esc_attr(format_phone_for_href($main_phone_data['phone_number'])) ?>">
                <?= esc_html($main_phone_data['phone_number']) ?>
            </a>
        <?php endif; ?>

        <!-- Email -->
        <?php if ($company_email): ?>
            <a href="mailto:<?= esc_attr($company_email) ?>"
                class="d-flex align-items-center gap-2">
                <img loading="lazy"
                    src="<?= esc_url(get_contact_icon_url('email_icon', 'email-ico.svg')) ?>"
                    alt="Email"
                    style="max-height:16px">
                <span style="font-size:14px"><?= esc_html($company_email) ?></span>
            </a>
        <?php endif; ?>
    </li>

    <!-- Социальные сети -->
    <?php if ($header_socials && is_array($header_socials) && !empty($header_socials)): ?>
        <li class="nav-item">
            <?php
            $total = count($header_socials);
            foreach ($header_socials as $index => $social):
                // ИСПОЛЬЗУЕМ КОРОТКИЕ КЛЮЧИ: icon, url, name
                if (!isset($social['icon']) || !isset($social['url'])) continue;

                // Последний элемент без отступа справа
                $is_last = ($index === $total - 1);
                $padding_class = $is_last ? 'pe-0' : 'pe-2';
            ?>
                <a class="ico-button <?= $padding_class ?>"
                    href="<?= esc_url($social['url']) ?>"
                    target="_blank"
                    rel="noopener noreferrer">
                    <img loading="lazy"
                        src="<?= esc_url($social['icon']['url']) ?>"
                        alt="<?= esc_attr($social['name'] ?? 'Social Network') ?>">
                </a>
            <?php endforeach; ?>
        </li>
    <?php endif; ?>
<?php
}

// ============================================================================
// MAIN: Главная функция навигации
// ============================================================================

function svetogor_safe_navigation_v5()
{
?>
    <div class="navbar-wrapper">
        <nav class="navbar navbar-expand-lg navbar-light bg-white" id="navbar">
            <div class="container flex-wrap">
                <!-- Логотип -->
                <a class="navbar-brand mx-lg-auto ms-xxl-0" href="<?= home_url() ?>">
                    <img loading="lazy" src="<?= get_template_directory_uri() ?>/assets/img/logo.svg" alt="Логотип" width="170px" height="50px">
                </a>

                <!-- Кнопка мобильного меню -->
                <button class="navbar-toggler" type="button"
                    data-bs-toggle="offcanvas" data-bs-target="#mobileMenu">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <!-- Десктопное меню -->
                <div class="collapse navbar-collapse" id="navbarContent">
                    <ul class="navbar-nav mx-md-auto me-xxl-0">
                        <?php svetogor_render_main_menu(); ?>
                    </ul>
                </div>
            </div>
        </nav>
    </div>

    <?php svetogor_render_mobile_menu(); ?>
<?php
}

// ============================================================================
// HOOKS
// ============================================================================

function svetogor_setup_navigation()
{
    add_theme_support('menus');
    register_nav_menus([
        'header_menu' => 'Основное меню',
        'footer_menu' => 'Подвальное меню',
    ]);
}
add_action('after_setup_theme', 'svetogor_setup_navigation');

function svetogor_enqueue_navigation_assets()
{
    wp_enqueue_script(
        'svetogor-navigation',
        get_template_directory_uri() . '/assets/js/navigation.js',
        ['jquery'],
        '1.0',
        true
    );

    if (!wp_script_is('bootstrap', 'enqueued')) {
        wp_enqueue_script(
            'bootstrap',
            get_template_directory_uri() . '/assets/js/bootstrap-custom.min.js',
            [],
            '1.0',
            true
        );
    }

    if (!wp_style_is('bootstrap', 'enqueued')) {
        wp_enqueue_style(
            'bootstrap',
            get_template_directory_uri() . '/assets/css/bootstrap-custom.min.css',
            [],
            '1.0'
        );
    }
}
add_action('wp_enqueue_scripts', 'svetogor_enqueue_navigation_assets');

// ============================================================================
// COMPATIBILITY: Совместимость со старым кодом
// ============================================================================

if (!function_exists('get_header_woocommerce_categories')) {
    function get_header_woocommerce_categories()
    {
        return svetogor_get_second_level_categories();
    }
}

if (!function_exists('get_header_subcategories')) {
    function get_header_subcategories($parent_id)
    {
        return svetogor_get_third_level_categories($parent_id);
    }
}

if (!function_exists('get_category_products')) {
    function get_category_products($category_id, $limit = 10)
    {
        return svetogor_get_category_products($category_id, $limit);
    }
}
