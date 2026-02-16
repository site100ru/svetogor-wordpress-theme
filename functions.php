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

add_action('template_redirect', function() {
    $request_path = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
    
    if ($request_path === 'stati.html') {
        global $wp_query;
        
        $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
        
        $args = array(
            'post_type' => 'post',
            'posts_per_page' => get_option('posts_per_page'),
            'paged' => $paged,
            'post_status' => 'publish',
        );
        
        $wp_query = new WP_Query($args);
        
        include get_template_directory() . '/articles/archive-articles.php';
        exit;
    }
});


add_action('template_redirect', 'custom_redirects_301');

function custom_redirects_301() {
    // Получаем текущий URL
    $current_url = $_SERVER['REQUEST_URI'];
    
    // Массив переадресаций: старый URL => новый URL
    $redirects = array(
        '/narugnaja-reklama/bukvy-svetovye/slim-lajt.html' => '/narugnaja-reklama/bukvy-svetovye/bright-light.html',
        '/narugnaja-reklama/svetovye-koroba/ekonom-boks.html' => '/narugnaja-reklama/svetovye-koroba/alyu-boks.html',
        '/narugnaja-reklama/svetovye-koroba/metall-boks.html' => '/narugnaja-reklama/световye-koroba/alyu-boks.html',
        '/narugnaja-reklama/svetovye-koroba/slim-boks.html' => '/narugnaja-reklama/svetovye-koroba/klej-boks.html',
        '/narugnaja-reklama/svetovye-koroba/totem-metall.html' => '/narugnaja-reklama/stely-pilony-pilarsy/pilony-otdelno-stoyashchie.html',
        '/narugnaja-reklama/svetovye-koroba/totem-alyuminij.html' => '/narugnaja-reklama/stely-pilony-pilarsy/pilony-otdelno-stoyashchie.html',
        '/narugnaja-reklama/stely-pilony-pilarsy/pilarsy-siti-format-3kh-storonnie.html' => '/narugnaja-reklama/stely-pilony-pilarsy/pilony-siti-format-1-2-kh-1-8-m.html',
        '/narugnaja-reklama/vitriny-oformlenie-vitrin.html' => '/shirokoformatnaja-pechat/naklejki-na-vitriny.html',
        '/production/reklama-na-transporte/reklama-na-avtotransporte.html' => '/shirokoformatnaja-pechat/naklejki-na-vitriny.html',
        '/production/reklama-na-transporte/reklama-na-vodnom-transporte.html' => '/shirokoformatnaja-pechat/naklejki-na-vitriny.html',
        '/narugnaja-reklama/ulichnye-informatsionnye-stendy-i-shchity/stendy-ulichnye-informatsionnye-sb/ulichnyj-informatsionnyj-stend-sb1.html' => '/narugnaja-reklama/ulichnye-informatsionnye-stendy-i-shchity.html',
        '/narugnaja-reklama/ulichnye-informatsionnye-stendy-i-shchity/stendy-nashe-podmoskove.html' => '/narugnaja-reklama/ulichnye-informatsionnye-stendy-i-shchity.html',
        '/narugnaja-reklama/ulichnye-informatsionnye-stendy-i-shchity/stendy-ulichnye-informatsionnye-se.html' => '/narugnaja-reklama/ulichnye-informatsionnye-stendy-i-shchity.html',
        '/narugnaja-reklama/ulichnye-informatsionnye-stendy-i-shchity/stendy-ulichnye-informatsionnye-su.html' => '/narugnaja-reklama/ulichnye-informatsionnye-stendy-i-shchity.html',
        '/narugnaja-reklama/ulichnye-informatsionnye-stendy-i-shchity/stendy-ulichnye-informatsionnye-sm.html' => '/narugnaja-reklama/ulichnye-informatsionnye-stendy-i-shchity.html',
        '/narugnaja-reklama/ulichnye-informatsionnye-stendy-i-shchity/stendy-ulichnye-informatsionnye-sv.html' => '/narugnaja-reklama/ulichnye-informatsionnye-stendy-i-shchity.html',
        '/narugnaja-reklama/ulichnye-informatsionnye-stendy-i-shchity/stendy-ulichnye-informatsionnye-skh.html' => '/narugnaja-reklama/ulichnye-informatsionnye-stendy-i-shchity.html',
        '/narugnaja-reklama/ulichnye-informatsionnye-stendy-i-shchity/stendy-ulichnye-informatsionnye-sb.html' => '/narugnaja-reklama/ulichnye-informatsionnye-stendy-i-shchity.html',
        '/narugnaja-reklama/ulichnye-tonkie-svetovye-paneli.html' => '/product_cat/products/koroba-dlya-afish-and-menyu/',
        '/narugnaja-reklama/stroitelnye-informatsionnye-shchity-pasport-ob-ekta-stroitelstva.html' => '/ulichnye-informatsionnye-stendy-i-shchity/stendy-ulichnye-informatsionnye-se/stend-ulichnyj-informatsionnyj-se1.html/',
        '/interiernaja-reklama/mobilnye-vystavochnye-stendy/iks-banner-x-banner.html' => '/interiernaja-reklama/mobilnye-vystavochnye-stendy.html/',
        '/interiernaja-reklama/mobilnye-vystavochnye-stendy/l-banner-l-banner.html' => '/interiernaja-reklama/mobilnye-vystavochnye-stendy.html/',
        '/interiernaja-reklama/mobilnye-vystavochnye-stendy/u-banner-y-banner.html' => '/interiernaja-reklama/mobilnye-vystavochnye-stendy.html/',
        '/interiernaja-reklama/magneticbox-52.html' => '/product/magnetik-2/',
        '/shirokoformatnaja-pechat/postery.html' => 'shirokoformatnaja-pechat.html',
        '/shirokoformatnaja-pechat/bannery-v-interer.html' => 'shirokoformatnaja-pechat.html',
        '/shirokoformatnaja-pechat/plakaty-afishi.html' => 'shirokoformatnaja-pechat.html',
        '/shirokoformatnaja-pechat/naklejki-na-avtotransport.html' => 'shirokoformatnaja-pechat.html',
        '/shirokoformatnaja-pechat/bannery-peretyazhki-na-ulitsu.html' => 'shirokoformatnaja-pechat.html',
        '/shirokoformatnaja-pechat/chertezhi.html' => 'shirokoformatnaja-pechat.html',
        '/shirokoformatnaja-pechat/naklejki-raznye.html' => 'shirokoformatnaja-pechat.html',
        '/shirokoformatnaja-pechat/pechat-na-kholste.html' => 'shirokoformatnaja-pechat.html',
        '/shirokoformatnaja-pechat/bannernaya-setka.html' => 'shirokoformatnaja-pechat.html',
        '/shirokoformatnaja-pechat/fotooboi.html' => 'shirokoformatnaja-pechat.html',
        '/shirokoformatnaja-pechat/pechat-na-plenke.html' => 'shirokoformatnaja-pechat.html',
        '/uslugi/registrazija-reklamy.html' => '/uslugi.html/',
        '/uslugi/consulting.html' => '/uslugi.html/',
        '/uslugi/dizayn-reklamy/dizajn-interera.html' => '/uslugi/dizayn-reklamy/dizajn-naruzhnoj-reklamy.html',
        '/uslugi/dizayn-reklamy/shirokoformatnaya-pechat.html' => '/uslugi/dizayn-reklamy.html',
        '/uslugi/dizayn-reklamy/izgotovlenie-reklamnogo-oborudovaniya.html' => '/uslugi/dizayn-reklamy.html',
        '/uslugi/dizayn-reklamy/dizajn-vystavochnykh-stendov.html' => '/uslugi/dizayn-reklamy.html',
        '/uslugi/dizayn-reklamy/dizajn-malykh-reklamnykh-nositelej.html' => '/uslugi/dizayn-reklamy.html',
        '/uslugi/dizayn-reklamy/razrabotka-firmennogo-stilya.html' => '/uslugi/dizayn-reklamy.html',
        '/tipografi/polnotsvetnaya-poligrafiya.html' => '/uslugi.html/',
        '/tipografi/polnotsvetnaya-poligrafiya/vizitki.html' => '/uslugi.html/',
        '/tipografi/polnotsvetnaya-poligrafiya/listovki.html' => '/uslugi.html/',
        '/tipografi/polnotsvetnaya-poligrafiya/buklety.html' => '/uslugi.html/',
        '/tipografi/polnotsvetnaya-poligrafiya/katalogi.html' => '/uslugi.html/',
        '/tipografi/rizograf.html' => '/uslugi.html/',
        '/gallery.html' => '/portfolio/',
    );
    
    // Проверяем, есть ли текущий URL в массиве переадресаций
    if (array_key_exists($current_url, $redirects)) {
        wp_redirect($redirects[$current_url], 301);
        exit();
    }
}