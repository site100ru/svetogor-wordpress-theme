<?php
/**
 * Svetogor functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package svetogor
 */

// ============================================================================
// Константы темы
// ============================================================================

if (!defined('_S_VERSION')) {
    define('_S_VERSION', '1.0.0');
}

if (!defined('ABSPATH')) {
    exit;
}

// ============================================================================
// Базовая настройка темы
// ============================================================================

require_once get_template_directory() . '/inc/theme-setup.php';

// ============================================================================
// Вспомогательные функции
// ============================================================================

require_once get_template_directory() . '/inc/helpers.php';
require_once get_template_directory() . '/inc/helper-functions.php';

// ============================================================================
// ACF блоки
// ============================================================================

require_once get_template_directory() . '/inc/acf-blocks.php';

// ============================================================================
// WooCommerce
// ============================================================================

require_once get_template_directory() . '/inc/woocommerce-setup.php';
require_once get_template_directory() . '/inc/woocommerce-templates.php';
require_once get_template_directory() . '/inc/woocommerce-faq.php';
require_once get_template_directory() . '/inc/woocommerce-portfolio.php';
require_once get_template_directory() . '/inc/woocommerce-expanding-text.php';

// Кастомные поля для категорий WooCommerce
require_once get_template_directory() . '/inc/woocommerce-category-fields.php';

// Универсальный визуальный редактор для таксономий
require_once get_template_directory() . '/inc/taxonomy-visual-description.php';

// ============================================================================
// Комплексное оформление
// ============================================================================

require_once get_template_directory() . '/inc/complex-design.php';

// ============================================================================
// Пользовательские типы записей
// ============================================================================

// Портфолио
if (file_exists(get_template_directory() . '/portfolio/functions.php')) {
    require_once get_template_directory() . '/portfolio/functions.php';
}

// Новости
if (file_exists(get_template_directory() . '/news/functions.php')) {
    require_once get_template_directory() . '/news/functions.php';
}

// Статьи
if (file_exists(get_template_directory() . '/articles/functions.php')) {
    require_once get_template_directory() . '/articles/functions.php';
}

// Услуги
if (file_exists(get_template_directory() . '/services/functions.php')) {
    require_once get_template_directory() . '/services/functions.php';
}

// ============================================================================
// Контактные данные
// ============================================================================

require_once get_template_directory() . '/inc/contact-functions.php';

// ============================================================================
// Функции меню и навигации
// ============================================================================

require_once get_template_directory() . '/inc/navigation.php';
require_once get_template_directory() . '/inc/menu-functions.php';

// ============================================================================
// Страницы
// ============================================================================

require_once get_template_directory() . '/inc/page-functions.php';

// ============================================================================
// AJAX обработчики
// ============================================================================

require_once get_template_directory() . '/inc/ajax-handlers.php';

// ============================================================================
// Дополнительные функции WordPress
// ============================================================================

require_once get_template_directory() . '/inc/common-functions.php';
require_once get_template_directory() . '/inc/transliteration.php';
require_once get_template_directory() . '/inc/custom-header.php';
require_once get_template_directory() . '/inc/template-tags.php';
require_once get_template_directory() . '/inc/template-functions.php';
require_once get_template_directory() . '/inc/customizer.php';

// ============================================================================
// SEO система
// ============================================================================
require_once get_template_directory() . '/inc/seo-meta-fields.php';

// ============================================================================
// XML Sitemap система
// ============================================================================
require_once get_template_directory() . '/inc/sitemap-generator.php';

// ============================================================================
// Schema.org разметка (JSON-LD)
// ============================================================================
require_once get_template_directory() . '/inc/schema-markup.php';

// ============================================================================
// Кастомные URL для товаров WooCommerce
// ============================================================================
require_once get_template_directory() . '/inc/custom-product-urls.php';

// ============================================================================
// Jetpack
// ============================================================================

if (defined('JETPACK__VERSION')) {
    require_once get_template_directory() . '/inc/jetpack.php';
}

require_once get_template_directory() . '/inc/content-renderer.php';
require_once get_template_directory() . '/inc/template-helpers.php';
require_once get_template_directory() . '/inc/admin-scripts.php';


// Подключение универсальной системы hero-фона
require_once get_template_directory() . '/inc/class-hero-background-meta-box.php';

/**
 * Подключение файла управления метатегами robots
 */
require_once get_template_directory() . '/inc/robots-meta-tag.php';

/**
 * Подключение файла управления каноническими URL
 */
require_once get_template_directory() . '/inc/canonical-urls.php';

// ============================================================================
// Глобальные переменные для совместимости
// ============================================================================

/**
 * Инициализация глобальных переменных для обратной совместимости
 */
function init_global_variables() {
    global $footer_socials, $wc_archive_social_networks;
    
    // Получаем социальные сети для футера
    $footer_socials = apply_filters('pre_get_footer_social_networks', false);
    
    if (!$footer_socials) {
        $footer_socials = get_footer_social_networks();
    }
    
    // Проверка для архивов WooCommerce
    if (empty($footer_socials) && !empty($wc_archive_social_networks)) {
        $footer_socials = $wc_archive_social_networks;
    }
}
add_action('wp', 'init_global_variables');

add_action('admin_head', function () {
    echo '<style>
        #edittag {
            max-width: 100%;
        }
    </style>';
});

// Очистка <head> от ненужного кода
add_action('after_setup_theme', function() {
    remove_action('wp_head', 'wp_generator');
    remove_action('wp_head', 'rsd_link');
});


add_action('template_redirect', function() {
    if (untrailingslashit($_SERVER['REQUEST_URI']) === '/uslugi.html') {
        include get_template_directory() . '/services/archive-services.php';
        exit;
    }
});


add_action('template_redirect', function () {
    if (is_post_type_archive('services')) {
        wp_safe_redirect(home_url('/uslugi.html/'), 301);
        exit;
    }
});

add_action('template_redirect', function () {
    if (is_post_type_archive('articles')) {
        wp_safe_redirect(home_url('/stati.html/'), 301);
        exit;
    }
});
