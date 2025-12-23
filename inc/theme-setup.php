<?php
/**
 * Базовая настройка темы Svetogor
 * 
 * @package svetogor
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Настройка темы
 */
function svetogor_setup() {
    // Поддержка переводов
    load_theme_textdomain('svetogor', get_template_directory() . '/languages');

    // RSS ссылки в head
    add_theme_support('automatic-feed-links');

    // WordPress управляет title tag
    add_theme_support('title-tag');

    // Поддержка миниатюр
    add_theme_support('post-thumbnails');

    // Регистрация меню
    register_nav_menus(array(
        'menu-1' => esc_html__('Primary', 'svetogor'),
        'footer_menu' => esc_html__('Подвальное меню', 'svetogor'),
    ));

    // HTML5 разметка
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style',
        'script',
    ));

    // Произвольный фон
    add_theme_support('custom-background', apply_filters('svetogor_custom_background_args', array(
        'default-color' => 'ffffff',
        'default-image' => '',
    )));

    // Selective refresh для виджетов
    add_theme_support('customize-selective-refresh-widgets');

    // Поддержка логотипа
    add_theme_support('custom-logo', array(
        'height' => 250,
        'width' => 250,
        'flex-width' => true,
        'flex-height' => true,
    ));
}
add_action('after_setup_theme', 'svetogor_setup');

/**
 * Установка ширины контента
 */
function svetogor_content_width() {
    $GLOBALS['content_width'] = apply_filters('svetogor_content_width', 1200);
}
add_action('after_setup_theme', 'svetogor_content_width', 0);

/**
 * Регистрация области виджетов
 */
function svetogor_widgets_init() {
    register_sidebar(array(
        'name' => esc_html__('Sidebar', 'svetogor'),
        'id' => 'sidebar-1',
        'description' => esc_html__('Add widgets here.', 'svetogor'),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget' => '</section>',
        'before_title' => '<h2 class="widget-title">',
        'after_title' => '</h2>',
    ));
}
add_action('widgets_init', 'svetogor_widgets_init');

/**
 * Подключение стилей и скриптов
 */
function svetogor_scripts() {
    // Основной стиль темы
    wp_enqueue_style('svetogor-style', get_stylesheet_uri(), array(), _S_VERSION);
    wp_style_add_data('svetogor-style', 'rtl', 'replace');

    // Навигация
    wp_enqueue_script('svetogor-navigation', get_template_directory_uri() . '/js/navigation.js', array(), _S_VERSION, true);

    // Регистрация и подключение стилей
    wp_enqueue_style('bootstrap', get_template_directory_uri() . '/assets/css/bootstrap-custom.min.css', array(), '1.0');
    wp_enqueue_style('theme-style', get_template_directory_uri() . '/assets/css/theme.css', array('bootstrap'), '1.0');
    wp_enqueue_style('font-style', get_template_directory_uri() . '/assets/css/font.css', array(), '1.0');
    
    // Glide.js - ЛОКАЛЬНЫЕ ФАЙЛЫ
    wp_enqueue_style('glide-core', get_template_directory_uri() . '/assets/css/glide.core.min.css', array(), '3.6.0');
    
    wp_enqueue_script(
        'glide-js',
        get_template_directory_uri() . '/assets/js/glide.min.js',
        array(),
        '3.6.0',
        true 
    );
    
    wp_enqueue_script(
        'glide-init',
        get_template_directory_uri() . '/assets/js/about/glide-init.js',
        array('glide-js'),
        filemtime(get_template_directory() . '/assets/js/about/glide-init.js'),
        true
    );

    // Скрипты
    wp_enqueue_script('inputmask', get_template_directory_uri() . '/assets/js/inputmask.min.js', array('jquery'), '1.0', true);
    wp_enqueue_script('tel-mask', get_template_directory_uri() . '/assets/js/telMask.js', array('jquery', 'inputmask'), '1.0', true);
    wp_enqueue_script('theme-script', get_template_directory_uri() . '/assets/js/theme.js', array('jquery', 'bootstrap-bundle'), '1.0', true);
    
    // Portfolio single
    if (is_singular('portfolio')) {
        wp_enqueue_script('portfolio-single', get_template_directory_uri() . '/assets/js/portfolio-single.js', array('jquery'), filemtime(get_template_directory() . '/assets/js/portfolio-single.js'), true);
    }

    // jQuery фикс
    wp_add_inline_script('jquery-core', 'var $ = jQuery;');
}
add_action('wp_enqueue_scripts', 'svetogor_scripts');

/**
 * Инлайн критичный CSS для слайдеров
 */
function svetogor_critical_css() {
    ?>
    <style id="glide-critical-css">
        /* для слайдеров */
        .glide:not([data-glide-initialized]) {
            opacity: 0;
            visibility: hidden;
        }
        
        /* Скелетон для слайдеров во время загрузки */
        .glide:not([data-glide-initialized]) .glide__slides {
            display: flex;
            gap: 24px;
        }
        
        .glide:not([data-glide-initialized]) .glide__slide {
            flex-shrink: 0;
            min-height: 200px;
            background: #f5f5f5;
            animation: pulse 1.5s ease-in-out infinite;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.6; }
        }
        
        /* Показываем инициализированные слайдеры */
        .glide[data-glide-initialized="true"] {
            opacity: 1 !important;
            visibility: visible !important;
        }
    </style>
    <?php
}
add_action('wp_head', 'svetogor_critical_css', 1);

/**
 * Инициализация сессий
 */
function init_custom_session() {
    if (!session_id()) {
        session_start();
    }
}
add_action('init', 'init_custom_session');

/**
 * Добавление категории для кастомных блоков
 */
function add_custom_block_categories($categories, $post) {
    return array_merge($categories, array(
        array(
            'slug' => 'custom-blocks',
            'title' => 'Кастомные блоки',
        ),
    ));
}
add_filter('block_categories_all', 'add_custom_block_categories', 10, 2);

/**
 * Разрешаем HTML в описании таксономий
 */
remove_filter('pre_term_description', 'wp_filter_kses');
remove_filter('term_description', 'wp_kses_data');

/**
 * Отложенная загрузка для не критичных скриптов
 */
function svetogor_defer_scripts($tag, $handle, $src) {
    // Список скриптов для defer
    $defer_scripts = array(
        'inputmask',
        'tel-mask',
        'theme-script'
    );
    
    if (in_array($handle, $defer_scripts)) {
        return str_replace(' src', ' defer src', $tag);
    }
    
    return $tag;
}
add_filter('script_loader_tag', 'svetogor_defer_scripts', 10, 3);