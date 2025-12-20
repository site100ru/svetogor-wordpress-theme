<?php
/**
 * svetogor functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package svetogor
 */

if (!defined('_S_VERSION')) {
	// Replace the version number of the theme on each release.
	define('_S_VERSION', '1.0.0');
}

/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function svetogor_setup()
{
	/*
	 * Make theme available for translation.
	 * Translations can be filed in the /languages/ directory.
	 * If you're building a theme based on svetogor, use a find and replace
	 * to change 'svetogor' to the name of your theme in all the template files.
	 */
	load_theme_textdomain('svetogor', get_template_directory() . '/languages');

	// Add default posts and comments RSS feed links to head.
	add_theme_support('automatic-feed-links');

	/*
	 * Let WordPress manage the document title.
	 * By adding theme support, we declare that this theme does not use a
	 * hard-coded <title> tag in the document head, and expect WordPress to
	 * provide it for us.
	 */
	add_theme_support('title-tag');

	/*
	 * Enable support for Post Thumbnails on posts and pages.
	 *
	 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
	 */
	add_theme_support('post-thumbnails');

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus(
		array(
			'menu-1' => esc_html__('Primary', 'svetogor'),
		)
	);

	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		)
	);

	// Set up the WordPress core custom background feature.
	add_theme_support(
		'custom-background',
		apply_filters(
			'svetogor_custom_background_args',
			array(
				'default-color' => 'ffffff',
				'default-image' => '',
			)
		)
	);

	// Add theme support for selective refresh for widgets.
	add_theme_support('customize-selective-refresh-widgets');

	/**
	 * Add support for core custom logo.
	 *
	 * @link https://codex.wordpress.org/Theme_Logo
	 */
	add_theme_support(
		'custom-logo',
		array(
			'height' => 250,
			'width' => 250,
			'flex-width' => true,
			'flex-height' => true,
		)
	);
}
add_action('after_setup_theme', 'svetogor_setup');

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function svetogor_content_width()
{
	$GLOBALS['content_width'] = apply_filters('svetogor_content_width', 640);
}
add_action('after_setup_theme', 'svetogor_content_width', 0);

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function svetogor_widgets_init()
{
	register_sidebar(
		array(
			'name' => esc_html__('Sidebar', 'svetogor'),
			'id' => 'sidebar-1',
			'description' => esc_html__('Add widgets here.', 'svetogor'),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget' => '</section>',
			'before_title' => '<h2 class="widget-title">',
			'after_title' => '</h2>',
		)
	);
}
add_action('widgets_init', 'svetogor_widgets_init');

/**
 * Enqueue scripts and styles.
 */
function svetogor_scripts()
{
	wp_enqueue_style('svetogor-style', get_stylesheet_uri(), array(), _S_VERSION);
	wp_style_add_data('svetogor-style', 'rtl', 'replace');

	wp_enqueue_script('svetogor-navigation', get_template_directory_uri() . '/js/navigation.js', array(), _S_VERSION, true);


	// Регистрация и подключение стилей
	wp_enqueue_style('bootstrap', get_template_directory_uri() . '/assets/css/bootstrap.min.css', array(), '1.0');
	wp_enqueue_style('theme-style', get_template_directory_uri() . '/assets/css/theme.css', array('bootstrap'), '1.0');
	wp_enqueue_style('font-style', get_template_directory_uri() . '/assets/css/font.css', array(), '1.0');
	wp_enqueue_style('glide-core', 'https://cdn.jsdelivr.net/npm/@glidejs/glide/dist/css/glide.core.min.css', array(), '1.0');


	// Регистрация и подключение скриптов
	wp_enqueue_script('inputmask', get_template_directory_uri() . '/assets/js/inputmask.min.js', array('jquery'), '1.0', true);
	wp_enqueue_script('tel-mask', get_template_directory_uri() . '/assets/js/telMask.js', array('jquery', 'inputmask'), '1.0', true);

	// Общие скрипты темы
	wp_enqueue_script('theme-script', get_template_directory_uri() . '/assets/js/theme.js', array('jquery', 'bootstrap-bundle'), '1.0', true);

	// Инициализация jQuery в режиме $ (решение проблемы "$ is not a function")
	wp_add_inline_script('jquery-core', 'var $ = jQuery;');

}
add_action('wp_enqueue_scripts', 'svetogor_scripts');

// Регистрируем ACF блоки
add_action('init', 'register_carousel_block');

function register_carousel_block()
{
	// Проверяем что ACF доступен
	if (function_exists('acf_register_block_type')) {

		// Регистрируем блок карусели
		acf_register_block_type(array(
			'name' => 'carousel-main',
			'title' => 'Главная карусель',
			'description' => 'Блок карусели с настраиваемыми слайдами',
			'render_template' => 'template-parts/blocks/carousel-main.php',
			'category' => 'custom-blocks',
			'icon' => 'slides',
			'keywords' => array('carousel', 'slider', 'карусель', 'слайдер'),
			'supports' => array(
				'align' => array('wide', 'full'),
				'anchor' => true,
				'customClassName' => true,
			),
		));

		// Регистрируем блок "Раскрывающий текст"
		acf_register_block_type(array(
			'name' => 'general-info',
			'title' => 'Раскрывающий текст',
			'description' => 'Блок с общей информацией и дополнительным текстом',
			'render_template' => 'template-parts/blocks/general-info/general-info.php',
			'category' => 'custom-blocks',
			'icon' => 'info',
			'keywords' => array('info', 'information', 'информация', 'текст'),
			'supports' => array(
				'align' => array('wide', 'full'),
				'anchor' => true,
				'customClassName' => true,
			),
		));

		// Регистрируем блок "Как заказать"
		acf_register_block_type(array(
			'name' => 'how-to-order',
			'title' => 'Как заказать',
			'description' => 'Блок с пошаговым процессом заказа',
			'render_template' => 'template-parts/blocks/how-to-order.php',
			'category' => 'custom-blocks',
			'icon' => 'list-view',
			'keywords' => array('steps', 'process', 'order', 'заказ', 'шаги'),
			'supports' => array(
				'align' => array('wide', 'full'),
				'anchor' => true,
				'customClassName' => true,
			),
		));

		// Регистрируем блок "О нас"
		acf_register_block_type(array(
			'name' => 'about-us',
			'title' => 'О нас',
			'description' => 'Блок с информацией о компании и фоновым изображением',
			'render_template' => 'template-parts/blocks/about-us.php',
			'category' => 'custom-blocks',
			'icon' => 'groups',
			'keywords' => array('about', 'company', 'о нас', 'компания'),
			'supports' => array(
				'align' => array('wide', 'full'),
				'anchor' => true,
				'customClassName' => true,
			),
		));

		// Регистрируем универсальный блок "Половинная секция"
		// acf_register_block_type(array(
		// 	'name' => 'half-section',
		// 	'title' => 'Половинная секция',
		// 	'description' => 'Блок с контентом в одной половине и изображением в другой',
		// 	'render_template' => 'template-parts/blocks/half-section.php',
		// 	'category' => 'custom-blocks',
		// 	'icon' => 'align-pull-left',
		// 	'keywords' => array('half', 'section', 'image', 'половина', 'секция'),
		// 	'supports' => array(
		// 		'align' => array('wide', 'full'),
		// 		'anchor' => true,
		// 		'customClassName' => true,
		// 	),
		// ));

		// Регистрируем универсальный блок "Преимуществами"
		acf_register_block_type([
			'name' => 'section-advantages',
			'title' => 'Секция с преимуществами',
			'description' => 'Блок для отображения преимуществ компании в колонках с иконками',
			'render_template' => get_template_directory() . '/template-parts/blocks/section-advantages.php',
			'category' => 'custom-blocks',
			'icon' => 'star-filled',
			'keywords' => ['преимущества', 'услуги', 'особенности'],
			'mode' => 'preview',
			'supports' => [
				'align' => false,
				'mode' => true,
			]
		]);

		// Регистрируем блок "Контент с изображением"
		acf_register_block_type(array(
			'name' => 'content-with-image',
			'title' => 'Контент с изображением',
			'description' => 'Блок с текстом и изображением в колонках',
			'render_template' => 'template-parts/blocks/content-with-image.php',
			'category' => 'custom-blocks',
			'icon' => 'align-pull-left',
			'keywords' => array('content', 'image', 'text', 'контент', 'изображение', 'текст'),
			'supports' => array(
				'align' => array('wide', 'full'),
				'anchor' => true,
				'customClassName' => true,
			),
		));

		// Регистрируем блок "Только текст"
		acf_register_block_type(array(
			'name' => 'text-only',
			'title' => 'Только текст',
			'description' => 'Блок только с текстовым контентом',
			'render_template' => 'template-parts/blocks/text-only.php',
			'category' => 'custom-blocks',
			'icon' => 'editor-alignleft',
			'keywords' => array('text', 'content', 'текст', 'контент'),
			'supports' => array(
				'align' => array('wide', 'full'),
				'anchor' => true,
				'customClassName' => true,
			),
		));


		// Регистрация блока клиентов
		acf_register_block_type(array(
			'name' => 'clients-slider',
			'title' => 'Слайдер клиентов',
			'description' => 'Блок для отображения слайдера с логотипами клиентов',
			'render_template' => get_template_directory() . '/template-parts/blocks/clients-slider/clients-slider.php',
			'category' => 'custom-blocks',
			'icon' => 'groups',
			'keywords' => array('clients', 'slider', 'клиенты', 'слайдер', 'логотипы'),
			'supports' => array(
				'align' => array('wide', 'full'),
				'anchor' => true,
				'customClassName' => true,
			),
		));

		// Регистрируем блок "Секция-герой"
		// acf_register_block_type(array(
		// 	'name' => 'hero-section',
		// 	'title' => 'Секция-герой',
		// 	'description' => 'Блок с заголовком и фоновым изображением',
		// 	'render_template' => 'template-parts/blocks/hero-section.php',
		// 	'category' => 'custom-blocks',
		// 	'icon' => 'cover-image',
		// 	'keywords' => array('hero', 'banner', 'header', 'герой', 'баннер', 'заголовок'),
		// 	'supports' => array(
		// 		'align' => array('wide', 'full'),
		// 		'anchor' => true,
		// 		'customClassName' => true,
		// 	),
		// ));

		// Регистрируем блок "Слайдер комплексного оформления"
		acf_register_block_type(array(
			'name' => 'complex-design-slider',
			'title' => 'Слайдер комплексного оформления',
			'description' => 'Блок слайдера с терминами комплексного оформления',
			'render_template' => 'template-parts/blocks/complex-design-slider.php',
			'category' => 'custom-blocks',
			'icon' => 'slides',
			'keywords' => array('slider', 'complex', 'design', 'слайдер', 'оформление'),
			'supports' => array(
				'align' => array('wide', 'full'),
				'anchor' => true,
				'customClassName' => true,
			),
		));

		// Регистрируем блок FAQ
		acf_register_block_type(array(
			'name' => 'faq',
			'title' => 'FAQ',
			'description' => 'Блок частых вопросов с аккордеоном',
			'render_template' => 'template-parts/blocks/faq/faq.php',
			'category' => 'custom-blocks',
			'icon' => 'editor-help',
			'keywords' => array('faq', 'вопросы', 'аккордеон', 'accordion'),
			'supports' => array(
				'align' => array('wide', 'full'),
				'anchor' => true,
				'customClassName' => true,
			),
		));

		// Регистрируем блок Gallery
		acf_register_block_type(array(
			'name' => 'gallery',
			'title' => 'Галерея изображений',
			'description' => 'Блок галереи изображений с настройками',
			'render_template' => 'template-parts/blocks/gallery/gallery.php',
			'category' => 'custom-blocks',
			'icon' => 'format-gallery',
			'keywords' => array('gallery', 'галерея', 'изображения', 'фото'),
			'supports' => array(
				'align' => array('wide', 'full'),
				'anchor' => true,
				'customClassName' => true,
			),
		));

		// Регистрируем блок "Категории товаров"
		acf_register_block_type(array(
			'name' => 'product-categories',
			'title' => 'Категории товаров',
			'description' => 'Блок для отображения категорий товаров с настройками',
			'render_template' => 'template-parts/blocks/product-categories/product-categories.php',
			'category' => 'custom-blocks',
			'icon' => 'products',
			'keywords' => array('product', 'categories', 'товары', 'категории', 'продукция'),
			'supports' => array(
				'align' => array('wide', 'full'),
				'anchor' => true,
				'customClassName' => true,
			),
		));


		// "Не нашли нужного товара?"
		acf_register_block_type(array(
			'name' => 'not-found-product',
			'title' => 'Не нашли нужного товара?',
			'description' => 'Блок "Не нашли нужного товара?" с данными из настроек сайта',
			'render_template' => 'template-parts/blocks/not-found-product/not-found-product.php',
			'category' => 'custom-blocks',
			'icon' => 'search',
			'keywords' => array('не нашли', 'товар', 'консультация', 'заявка'),
			'supports' => array(
				'align' => array('wide', 'full'),
				'anchor' => true,
				'customClassName' => true,
			),
			'example' => array(
				'attributes' => array(
					'mode' => 'preview',
					'data' => array(
						'nfp_block_background_color_unique' => 'bg-grey',
					)
				)
			)
		));

		// блок "Контакты"
		acf_register_block_type(array(
			'name' => 'contacts',
			'title' => 'Контакты',
			'description' => 'Блок для отображения контактной информации компании',
			'render_template' => 'template-parts/blocks/contacts/contacts.php',
			'category' => 'custom-blocks',
			'icon' => 'phone',
			'keywords' => array('contacts', 'phone', 'address', 'email', 'контакты', 'телефон', 'адрес', 'почта'),
			'supports' => array(
				'align' => array('wide', 'full'),
				'anchor' => true,
				'customClassName' => true,
			),
		));

		// Регистрируем блок Яндекс.Карты
		acf_register_block_type(array(
			'name' => 'yandex-map',
			'title' => __('Яндекс Карта'),
			'description' => __('Блок с Яндекс Картой и маркером местоположения'),
			'render_template' => 'template-parts/blocks/yandex-map/yandex-map.php',
			'category' => 'custom-blocks',
			'icon' => 'location-alt',
			'keywords' => array('карта', 'яндекс', 'местоположение', 'map'),
			'mode' => 'preview',
			'supports' => array(
				'align' => array('wide', 'full'),
				'anchor' => true,
				'customClassName' => true,
			),
		));

		// Регистрируем блок "Простая форма обратной связи"
		acf_register_block_type(array(
			'name' => 'simple-contact-form',
			'title' => 'Простая форма обратной связи',
			'description' => 'Простая форма с основными полями и чекбоксами с картинками',
			'render_template' => 'template-parts/blocks/forms/form.php',
			'category' => 'custom-blocks',
			'icon' => 'email',
			'keywords' => array('form', 'contact', 'форма', 'обратная связь', 'заявка'),
			'supports' => array(
				'align' => array('wide', 'full'),
				'anchor' => true,
				'customClassName' => true,
			),
		));

		// Регистрируем блок "Расширенная форма обратной связи"
		acf_register_block_type(array(
			'name' => 'extended-contact-form',
			'title' => 'Расширенная форма обратной связи',
			'description' => 'Расширенная форма с типами продукции и дополнительными услугами',
			'render_template' => 'template-parts/blocks/forms/extended-form.php',
			'category' => 'custom-blocks',
			'icon' => 'feedback',
			'keywords' => array('form', 'contact', 'extended', 'форма', 'расширенная', 'продукция', 'услуги'),
			'supports' => array(
				'align' => array('wide', 'full'),
				'anchor' => true,
				'customClassName' => true,
			),
		));

		acf_register_block_type(array(
			'name' => 'woocommerce-category-products',
			'title' => 'Категория товаров WooCommerce',
			'description' => 'Блок для вывода товаров из выбранной категории WooCommerce',
			'render_template' => 'template-parts/blocks/woocommerce-category-products/woocommerce-category-products.php',
			'category' => 'custom-blocks',
			'icon' => 'products',
			'keywords' => array('woocommerce', 'категория', 'товары', 'продукты', 'магазин'),
			'supports' => array(
				'align' => false,
				'mode' => true,
				'jsx' => true,
			),
			'example' => array(
				'attributes' => array(
					'mode' => 'preview',
					'data' => array(
						'wc_category_block_bg_color_unique_2024' => 'bg-white',
						'wc_category_block_selected_category_unique' => '',
						'wc_category_block_products_count_unique' => 3,
					)
				)
			)
		));

		// "Хлебные крошки / Заголовок"
		acf_register_block_type(array(
			'name' => 'breadcrumbs-header',
			'title' => 'Хлебные крошки / Заголовок',
			'description' => 'Блок с хлебными крошками и заголовком страницы',
			'render_template' => 'template-parts/blocks/breadcrumbs-header/breadcrumbs-header.php',
			'category' => 'custom-blocks',
			'icon' => 'admin-links',
			'keywords' => array('хлебные крошки', 'заголовок', 'навигация', 'breadcrumbs'),
			'supports' => array(
				'align' => false,
				'mode' => true,
				'jsx' => true,
			),
			'example' => array(
				'attributes' => array(
					'mode' => 'preview',
					'data' => array(
						'breadcrumbs_block_page_title_unique_2024' => 'Магазин',
						'breadcrumbs_block_bg_color_unique_2024' => 'section-mini',
						'breadcrumbs_block_parent_link_unique' => '',
					)
				)
			)
		));
	}
}

/**
 * Добавляем страницу настроек с вкладками
 */
add_action('acf/init', 'add_carousel_options_page');


// Подключение скриптов и стилей для блока клиентов
function clients_block_assets()
{
	// Проверяем, есть ли блок на странице
	if (has_block('acf/clients-slider')) {
		// Подключаем Glide.js
		wp_enqueue_style('glide-css', 'https://cdn.jsdelivr.net/npm/@glidejs/glide@3.6.0/dist/css/glide.core.min.css', array(), '3.6.0');
		wp_enqueue_script('glide-js', 'https://cdn.jsdelivr.net/npm/@glidejs/glide@3.6.0/dist/glide.min.js', array(), '3.6.0', true);

		// Подключаем кастомные стили и скрипты блока
		wp_enqueue_script('clients-slider-js', get_template_directory_uri() . '/template-parts/blocks/clients-slider/clients-slider.js', array('jquery'), filemtime(get_template_directory() . '/template-parts/blocks/clients-slider/clients-slider.js'), true);
	}
}
add_action('wp_enqueue_scripts', 'clients_block_assets');

function add_carousel_options_page()
{
	if (function_exists('acf_add_options_page')) {
		// Главная страница настроек
		acf_add_options_page(array(
			'page_title' => 'Настройки сайта',
			'menu_title' => 'Настройки сайта',
			'menu_slug' => 'site-settings',
			'capability' => 'edit_posts',
			'icon_url' => 'dashicons-admin-generic',
		));

		// Вкладка для иконок
		acf_add_options_sub_page(array(
			'page_title' => 'Иконки',
			'menu_title' => 'Иконки',
			'menu_slug' => 'site-icons',
			'parent_slug' => 'site-settings',
		));
	}
}

/**
 * Добавляем категорию для наших блоков (опционально)
 */
add_filter('block_categories_all', 'add_custom_block_categories', 10, 2);

function add_custom_block_categories($categories, $post)
{
	return array_merge(
		$categories,
		array(
			array(
				'slug' => 'custom-blocks',
				'title' => 'Кастомные блоки',
			),
		)
	);
}

/**
 * Подключение библиотеки Glide.js
 */
add_action('wp_enqueue_scripts', function () {
	// Подключаем Glide.js только на страницах, где есть блок с партнерами
	if (has_block('acf/section-partners')) {
		// CSS файл Glide.js
		wp_enqueue_style(
			'glide-core',
			'https://cdnjs.cloudflare.com/ajax/libs/Glide.js/3.6.0/css/glide.core.min.css',
			[],
			'3.6.0'
		);

		// JavaScript файл Glide.js
		wp_enqueue_script(
			'glide-js',
			'https://cdnjs.cloudflare.com/ajax/libs/Glide.js/3.6.0/glide.min.js',
			[],
			'3.6.0',
			true
		);
	}
});

require_once get_template_directory() . '/inc/transliteration.php';



/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
if (defined('JETPACK__VERSION')) {
	require get_template_directory() . '/inc/jetpack.php';
}



//======================================================================
// Регистрация блока слайдера портфолио
//======================================================================

function register_portfolio_slider_block()
{
	if (function_exists('acf_register_block_type')) {
		acf_register_block_type(array(
			'name' => 'portfolio-slider',
			'title' => 'Слайдер портфолио',
			'description' => 'Блок для отображения слайдера работ портфолио',
			'render_template' => get_template_directory() . '/template-parts/blocks/portfolio-slider/portfolio-slider.php',
			'category' => 'custom-blocks',
			'icon' => 'images-alt2',
			'keywords' => array('portfolio', 'slider', 'портфолио', 'слайдер'),
			'supports' => array(
				'align' => array('wide', 'full'),
				'mode' => true,
				'jsx' => true
			)
		));
	}
}
add_action('acf/init', 'register_portfolio_slider_block');

// Подключение скриптов и стилей для блока
function portfolio_slider_block_assets()
{
	// Проверяем, есть ли блок на странице
	if (has_block('acf/portfolio-slider')) {
		// Подключаем Glide.js
		wp_enqueue_style('glide-css', 'https://cdn.jsdelivr.net/npm/@glidejs/glide@3.6.0/dist/css/glide.core.min.css', array(), '3.6.0');
		wp_enqueue_script('glide-js', 'https://cdn.jsdelivr.net/npm/@glidejs/glide@3.6.0/dist/glide.min.js', array(), '3.6.0', true);

		// Подключаем кастомные стили и скрипты блока
		wp_enqueue_script('portfolio-slider-js', get_template_directory_uri() . '/template-parts/blocks/portfolio-slider/portfolio-slider.js', array('jquery'), filemtime(get_template_directory() . '/template-parts/blocks/portfolio-slider/portfolio-slider.js'), true);

		// Передаем AJAX URL для скрипта
		wp_localize_script('portfolio-slider-js', 'portfolio_ajax', array(
			'ajax_url' => admin_url('admin-ajax.php')
		));
	}
}
add_action('wp_enqueue_scripts', 'portfolio_slider_block_assets');

//======================================================================
// Регистрация блока сетки портфолио
//======================================================================
function register_portfolio_grid_block()
{
	if (function_exists('acf_register_block_type')) {
		acf_register_block_type(array(
			'name' => 'portfolio-grid',
			'title' => 'Портфолио сетка',
			'description' => 'Блок для отображения работ портфолио в виде сетки',
			'render_template' => get_template_directory() . '/template-parts/blocks/portfolio-grid/portfolio-grid.php',
			'category' => 'custom-blocks',
			'icon' => 'grid-view',
			'keywords' => array('portfolio', 'grid', 'портфолио', 'сетка'),
			'supports' => array(
				'align' => array('wide', 'full'),
				'mode' => true,
				'jsx' => true
			)
		));
	}
}
add_action('acf/init', 'register_portfolio_grid_block');

// Подключение скриптов и стилей для блока сетки
function portfolio_grid_block_assets()
{
	// Проверяем, есть ли блок на странице
	if (has_block('acf/portfolio-grid')) {
		// Подключаем кастомные стили и скрипты блока
		wp_enqueue_script('portfolio-grid-js', get_template_directory_uri() . '/template-parts/blocks/portfolio-grid/portfolio-grid.js', array('jquery'), filemtime(get_template_directory() . '/template-parts/blocks/portfolio-grid/portfolio-grid.js'), true);

		// Передаем AJAX URL для скрипта
		wp_localize_script('portfolio-grid-js', 'portfolio_grid_ajax', array(
			'ajax_url' => admin_url('admin-ajax.php')
		));
	}
}
add_action('wp_enqueue_scripts', 'portfolio_grid_block_assets');








//======================================================================
// WooCommerce настройки и кастомизация
//======================================================================

add_action('after_setup_theme', 'woocommerce_support');
function woocommerce_support()
{
	add_theme_support('woocommerce');
	add_theme_support('wc-product-gallery-zoom');
	add_theme_support('wc-product-gallery-lightbox');
	add_theme_support('wc-product-gallery-slider');
}

//======================================================================
// Убираем стандартные стили WooCommerce (опционально)
//======================================================================

add_filter('woocommerce_enqueue_styles', '__return_empty_array');

//======================================================================
// Убираем стандартные элементы из страницы товара
//======================================================================

remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_title', 5);
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10);
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_price', 10);
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20);
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30);
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40);
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 50);

//======================================================================
// Добавляем только цену и кнопку в нашем стиле
//======================================================================

add_action('woocommerce_single_product_summary', 'custom_single_product_price', 10);
add_action('woocommerce_single_product_summary', 'custom_single_product_button', 20);
add_action('init', 'remove_wc_related_products');
function remove_wc_related_products()
{
	remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20);
	remove_action('woocommerce_single_product_summary', 'woocommerce_output_related_products', 25);
}


/**
 * Выводит кросселы WooCommerce в правильном дизайне
 */
function render_woocommerce_crosssells($product_id = null, $limit = 6, $background_color = "bg-grey")
{
	if (!$product_id) {
		$product_id = get_the_ID();
	}

	$product = wc_get_product($product_id);
	if (!$product) {
		return;
	}

	// Получаем кросселы
	$cross_sells = $product->get_cross_sell_ids();

	if (empty($cross_sells)) {
		return;
	}

	// Ограничиваем количество
	$cross_sells = array_slice($cross_sells, 0, $limit);

	// Получаем товары
	$cross_sell_products = array();
	foreach ($cross_sells as $cross_sell_id) {
		$cross_sell_product = wc_get_product($cross_sell_id);
		if ($cross_sell_product && $cross_sell_product->is_visible()) {
			$cross_sell_products[] = $cross_sell_product;
		}
	}

	if (empty($cross_sell_products)) {
		return;
	}
	?>

	<!-- А еще Вам может пригодиться -->
	<section class="section section-product-recoment box-shadow-main-img <?php echo $background_color ?>">
		<div class="container">
			<div class="section-title text-center">
				<h3>А еще Вам может пригодиться</h3>
				<img src="<?php echo get_template_directory_uri(); ?>/assets/img/ico/points.svg" alt="Точки" class="img-fluid" />
			</div>
			<!-- Карточки -->
			<div class="row g-4">
				<?php foreach ($cross_sell_products as $cross_sell_product): ?>
					<?php
					$product_title = $cross_sell_product->get_title();
					$product_link = $cross_sell_product->get_permalink();
					$product_image_id = $cross_sell_product->get_image_id();
					$product_image_url = wp_get_attachment_image_url($product_image_id, 'medium');

					if (!$product_image_url) {
						$product_image_url = wc_placeholder_img_src('medium');
					}
					?>

					<!-- Карточка товара -->
					<article class="col-12 col-md-6 col-lg-4">
						<a href="<?php echo esc_url($product_link); ?>" class="card bg-transparent">
							<div class="card">
								<div class="card-img-container">
									<img src="<?php echo esc_url($product_image_url); ?>" alt="<?php echo esc_attr($product_title); ?>"
										class="img-fluid" />
								</div>
							</div>
							<div class="card-body text-center">
								<h5><?php echo esc_html($product_title); ?></h5>
							</div>
						</a>
					</article>

				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<?php
}

// Кастомная цена товара
function custom_single_product_price()
{
	global $product;

	if ($product->get_price()) {
		echo '<p class="mb-0" style="font-weight: 500">';
		echo 'Стоимость: <strong class="price-text">' . $product->get_price_html() . '</strong>';
		echo '</p>';
	}
}

// Кастомная кнопка


function custom_single_product_button()
{
	global $product;

	// Получаем ID текущего товара
	$product_id = $product->get_id();

	// Проверяем, принадлежит ли товар к категории 'shop'
	if (has_term('shop', 'product_cat', $product_id)) {
		// Для категории shop - модалка callbackModalFour и текст "Заказать"
		echo '<button data-bs-toggle="modal" data-bs-target="#callbackModalFour" class="btn btn-big">Заказать</button>';
	} else {
		// Для всех остальных категорий - модалка callbackModalFree и текст "Рассчитать стоимость"
		echo '<button data-bs-toggle="modal" data-bs-target="#callbackModalFree" class="btn btn-big">Рассчитать стоимость</button>';
	}
}

//======================================================================
// Убираем стандартные табы и добавляем нужные
//======================================================================

add_filter('woocommerce_product_tabs', 'custom_product_tabs');
function custom_product_tabs($tabs)
{
	global $product;

	// Убираем стандартные табы
	unset($tabs['reviews']); // Отзывы
	unset($tabs['additional_information']); // Дополнительная информация

	// Принудительно добавляем таб описания (даже если стандартное описание пустое)
	$tabs['description'] = array(
		'title' => 'Описание',
		'priority' => 10,
		'callback' => 'custom_description_tab_content'
	);

	// Добавляем таб "Характеристики" (автоматически из атрибутов)
	$tabs['specifications'] = array(
		'title' => 'Характеристики',
		'priority' => 20,
		'callback' => 'specifications_tab_content'
	);

	// Добавляем таб "Прайс"
	$tabs['price_list'] = array(
		'title' => 'Прайс',
		'priority' => 30,
		'callback' => 'price_list_tab_content'
	);

	return $tabs;
}

//======================================================================
// Кастомная обработка описания через ACF
//======================================================================

function custom_description_tab_content()
{
	global $product;

	// Получаем строки описания из ACF
	$description_rows = get_field('description_rows', $product->get_id());

	if (!empty($description_rows) && is_array($description_rows)) {
		echo '<div class="row">';

		foreach ($description_rows as $row) {
			$layout = $row['layout']; // 'one_column' или 'two_columns'

			if ($layout === 'one_column') {
				// Строка в одну колонку
				echo '<div class="col-12 mb-0 mb-lg-4">';
				if (!empty($row['content_full'])) {
					echo wpautop(wp_kses_post($row['content_full']));
				}
				echo '</div>';

			} else {
				// Строка в две колонки
				echo '<div class="col-12 col-lg-6 mb-0 mb-lg-4">';
				if (!empty($row['content_first'])) {
					echo wpautop(wp_kses_post($row['content_first']));
				}
				echo '</div>';

				echo '<div class="col-12 col-lg-6 mb-0 mb-lg-4">';
				if (!empty($row['content_second'])) {
					echo wpautop(wp_kses_post($row['content_second']));
				}
				echo '</div>';
			}
		}

		echo '</div>';
	} else {
		// Fallback на стандартное описание если ACF не заполнено
		$description = $product->get_description();
		if ($description) {
			echo '<div class="row">';
			echo '<div class="col-12">';
			echo wpautop(wp_kses_post($description));
			echo '</div>';
			echo '</div>';
		}
	}
}

//======================================================================
// Контент таба "Характеристики" из атрибутов товара
//======================================================================

function specifications_tab_content()
{
	global $product;

	$attributes = $product->get_attributes();

	if (!empty($attributes)) {
		echo '<div class="row">';

		// Разделяем атрибуты на две колонки
		$attributes_array = array();
		foreach ($attributes as $attribute) {
			$name = wc_attribute_label($attribute->get_name());
			$values = array();

			if ($attribute->is_taxonomy()) {
				$attribute_values = wc_get_product_terms($product->get_id(), $attribute->get_name(), array('fields' => 'names'));
				foreach ($attribute_values as $attribute_value) {
					$values[] = $attribute_value;
				}
			} else {
				$values = $attribute->get_options();
			}

			$attributes_array[] = array(
				'name' => $name,
				'value' => implode(', ', $values)
			);
		}

		// Первая колонка
		echo '<div class="col-12 col-lg-6 mb-0 mb-lg-4">';
		$half = ceil(count($attributes_array) / 2);
		for ($i = 0; $i < $half; $i++) {
			if (isset($attributes_array[$i])) {
				echo '<p><strong style="font-weight: 500">' . esc_html($attributes_array[$i]['name']) . ':</strong> ' . esc_html($attributes_array[$i]['value']) . '</p>';
			}
		}
		echo '</div>';

		// Вторая колонка
		echo '<div class="col-12 col-lg-6 mb-0 mb-lg-4">';
		for ($i = $half; $i < count($attributes_array); $i++) {
			if (isset($attributes_array[$i])) {
				echo '<p><strong style="font-weight: 500">' . esc_html($attributes_array[$i]['name']) . ':</strong> ' . esc_html($attributes_array[$i]['value']) . '</p>';
			}
		}
		echo '</div>';

		echo '</div>';
	} else {
		echo '<div class="row">';
		echo '<div class="col-12">';
		echo '<p>Характеристики не указаны. Добавьте атрибуты товара в административной панели.</p>';
		echo '</div>';
		echo '</div>';
	}
}

//======================================================================
// Контент таба "Прайс" из ACF полей
//======================================================================

function price_list_tab_content()
{
	global $product;

	// Получаем строки прайс-листа из ACF
	$price_rows = get_field('price_list_rows', $product->get_id());

	if (!empty($price_rows) && is_array($price_rows)) {
		echo '<div class="row">';

		foreach ($price_rows as $row) {
			$layout = $row['layout']; // 'one_column' или 'two_columns'
			$items = $row['items'];

			if (empty($items) || !is_array($items)) {
				continue;
			}

			if ($layout === 'one_column') {
				// Строка в одну колонку - все позиции подряд
				echo '<div class="col-12 mb-0 mb-lg-4">';
				foreach ($items as $item) {
					if (!empty($item['name']) && !empty($item['price'])) {
						echo '<div class="price-item">';
						echo '<span class="price-name">' . esc_html($item['name']) . '</span>';
						echo '<span class="price-value price-text">' . esc_html($item['price']) . '</span>';
						echo '</div>';
					}
				}
				echo '</div>';

			} else {
				// Строка в две колонки - делим позиции пополам
				$total_items = count($items);
				$half = ceil($total_items / 2);

				// Первая колонка
				echo '<div class="col-12 col-lg-6 mb-0 mb-lg-4">';
				for ($i = 0; $i < $half; $i++) {
					if (isset($items[$i]) && !empty($items[$i]['name']) && !empty($items[$i]['price'])) {
						echo '<div class="price-item">';
						echo '<span class="price-name">' . esc_html($items[$i]['name']) . '</span>';
						echo '<span class="price-value price-text">' . esc_html($items[$i]['price']) . '</span>';
						echo '</div>';
					}
				}
				echo '</div>';

				// Вторая колонка
				echo '<div class="col-12 col-lg-6 mb-0 mb-lg-4">';
				for ($i = $half; $i < $total_items; $i++) {
					if (isset($items[$i]) && !empty($items[$i]['name']) && !empty($items[$i]['price'])) {
						echo '<div class="price-item">';
						echo '<span class="price-name">' . esc_html($items[$i]['name']) . '</span>';
						echo '<span class="price-value price-text">' . esc_html($items[$i]['price']) . '</span>';
						echo '</div>';
					}
				}
				echo '</div>';
			}
		}

		echo '</div>';
	} else {
		echo '<div class="row">';
		echo '<div class="col-12 mb-0 mb-lg-4">';
		echo '<p>Прайс-лист не заполнен. Добавьте позиции в административной панели в разделе "Прайс-лист товара".</p>';
		echo '</div>';
		echo '</div>';
	}
}


//======================================================================
// FAQ для товаров WooCommerce - функции для шаблонов
//======================================================================

/**
 * Получает FAQ для категории по ID
 */
function get_category_faq($category_id)
{
	if (function_exists('get_field')) {
		$faq_data = get_field('category_faq_questions', 'product_cat_' . $category_id);
		if (!empty($faq_data) && is_array($faq_data)) {
			return $faq_data;
		}
	}

	// Если стандартный способ не работает, собираем данные вручную
	return get_category_faq_manual($category_id);
}

function get_category_faq_manual($category_id)
{
	// Получаем количество элементов repeater
	$count = get_term_meta($category_id, 'category_faq_questions', true);

	if (empty($count) || !is_numeric($count)) {
		return false;
	}

	$faq_items = array();

	// Собираем каждый элемент
	for ($i = 0; $i < intval($count); $i++) {
		$question = get_term_meta($category_id, "category_faq_questions_{$i}_question", true);
		$answer = get_term_meta($category_id, "category_faq_questions_{$i}_answer", true);
		$expanded = get_term_meta($category_id, "category_faq_questions_{$i}_expanded", true);

		// Проверяем, что есть и вопрос и ответ
		if (!empty($question) && !empty($answer)) {
			$faq_items[] = array(
				'question' => $question,
				'answer' => $answer,
				'expanded' => ($expanded === '1' || $expanded === 1)
			);
		}
	}

	return !empty($faq_items) ? $faq_items : false;
}

/**
 * Получает FAQ для товара с учетом настроек товара
 * Приоритет: выбранная в товаре категория > подкатегория первого уровня > любая категория с FAQ
 */
function get_product_faq($product_id)
{
	// Проверяем, включено ли отображение FAQ для товара
	$show_faq = get_field('product_show_faq', $product_id);
	if ($show_faq === false || $show_faq === '0') {
		return false;
	}

	// Проверяем, выбрана ли конкретная категория для FAQ в настройках товара
	$selected_category_id = get_field('product_faq_category', $product_id);
	if ($selected_category_id) {
		$faq = get_category_faq($selected_category_id);
		if (!empty($faq)) {
			$category = get_term($selected_category_id, 'product_cat');
			if ($category && !is_wp_error($category)) {
				return array(
					'faq' => $faq,
					'category' => $category,
					'source' => 'manual_selection'
				);
			}
		}
	}

	// Если категория не выбрана или в ней нет FAQ, ищем автоматически
	return get_product_faq_auto($product_id);
}

/**
 * Автоматический поиск FAQ для товара
 */
function get_product_faq_auto($product_id)
{
	// Получаем все категории товара
	$terms = wp_get_post_terms($product_id, 'product_cat');

	if (empty($terms) || is_wp_error($terms)) {
		return false;
	}

	// Сортируем категории по уровню вложенности
	$categories_by_level = array();

	foreach ($terms as $term) {
		$level = get_category_level($term->term_id);
		if (!isset($categories_by_level[$level])) {
			$categories_by_level[$level] = array();
		}
		$categories_by_level[$level][] = $term;
	}

	// Сортируем по уровню (сначала более глубокие)
	krsort($categories_by_level);

	// Ищем FAQ, начиная с более глубоких категорий
	foreach ($categories_by_level as $level => $categories) {
		foreach ($categories as $category) {
			$faq = get_category_faq($category->term_id);
			if (!empty($faq)) {
				return array(
					'faq' => $faq,
					'category' => $category,
					'source' => 'auto_selection',
					'level' => $level
				);
			}
		}
	}

	return false;
}

/**
 * Получает FAQ для архива категории
 */
function get_archive_faq($category_id = null)
{
	if (!$category_id) {
		$current_category = get_queried_object();
		if ($current_category && isset($current_category->term_id)) {
			$category_id = $current_category->term_id;
		} else {
			return false;
		}
	}

	$faq = get_category_faq($category_id);
	if (!empty($faq)) {
		$category = get_term($category_id, 'product_cat');
		if ($category && !is_wp_error($category)) {
			return array(
				'faq' => $faq,
				'category' => $category,
				'source' => 'archive_category'
			);
		}
	}

	return false;
}

/**
 * Получает уровень вложенности категории
 */
function get_category_level($category_id)
{
	$level = 0;
	$current_id = $category_id;

	while ($current_id) {
		$parent = wp_get_term_taxonomy_parent_id($current_id, 'product_cat');
		if ($parent) {
			$level++;
			$current_id = $parent;
		} else {
			break;
		}
	}

	return $level;
}

/**
 * Выводит FAQ блок для товара используя ваш существующий шаблон
 */
function render_product_faq($product_id, $background_color = 'grey', $container_width = 10)
{
	$product_faq = get_product_faq($product_id);

	if (!$product_faq || empty($product_faq['faq'])) {
		return;
	}

	$faq_items = $product_faq['faq'];

	// Симулируем ACF поля для вашего блока
	$simulated_acf_data = array(
		'title' => 'Частые вопросы',
		'background_color' => $background_color,
		'container_width' => $container_width,
		'questions' => array()
	);

	// Преобразуем данные в формат вашего блока
	foreach ($faq_items as $index => $item) {
		if (!empty($item['question']) && !empty($item['answer'])) {
			$simulated_acf_data['questions'][] = array(
				'question_answer' => array(
					'question' => $item['question'],
					'answer' => $item['answer']
				),
				'expanded' => !empty($item['expanded'])
			);
		}
	}

	// Временно устанавливаем данные для ACF
	global $temp_acf_data;
	$temp_acf_data = $simulated_acf_data;
	add_filter('acf/load_value', 'temp_acf_load_value', 10, 3);

	// Подключаем ваш шаблон
	include get_template_directory() . '/template-parts/blocks/faq/faq.php';

	// Очищаем временные данные
	$temp_acf_data = null;
	remove_filter('acf/load_value', 'temp_acf_load_value', 10);
}

/**
 * Выводит FAQ блок для архива категории используя ваш существующий шаблон
 */
function render_archive_faq($category_id = null, $background_color = 'grey', $container_width = 10)
{
	$archive_faq = get_archive_faq($category_id);

	if (!$archive_faq || empty($archive_faq['faq'])) {
		return;
	}

	$faq_items = $archive_faq['faq'];

	// Симулируем ACF поля для вашего блока
	$simulated_acf_data = array(
		'title' => 'Частые вопросы',
		'background_color' => $background_color,
		'container_width' => $container_width,
		'questions' => array()
	);

	// Преобразуем данные в формат вашего блока
	foreach ($faq_items as $index => $item) {
		if (!empty($item['question']) && !empty($item['answer'])) {
			$simulated_acf_data['questions'][] = array(
				'question_answer' => array(
					'question' => $item['question'],
					'answer' => $item['answer']
				),
				'expanded' => !empty($item['expanded'])
			);
		}
	}

	// Временно устанавливаем данные для ACF
	global $temp_acf_data;
	$temp_acf_data = $simulated_acf_data;
	add_filter('acf/load_value', 'temp_acf_load_value', 10, 3);

	// Подключаем ваш шаблон
	$template_path = get_template_directory() . '/template-parts/blocks/faq/faq.php';
	if (file_exists($template_path)) {
		include $template_path;
	} else {
		// Fallback: простой вывод FAQ
		render_simple_faq($simulated_acf_data);
	}

	// Очищаем временные данные
	$temp_acf_data = null;
	remove_filter('acf/load_value', 'temp_acf_load_value', 10);
}






/**
 * Выводит раскрывающий текст для товара из выбранной категории
 * Фон всегда серый (bg-grey)
 */
function render_product_expanding_text($product_id)
{
	// Получаем настройки товара - из какой категории брать текст
	$expanding_text_settings = get_field('expanding_text_settings', $product_id);

	if (!$expanding_text_settings || !isset($expanding_text_settings['category_source']) || empty($expanding_text_settings['category_source'])) {
		return;
	}

	$category_id = $expanding_text_settings['category_source'];

	// Проверяем, что категория действительно связана с товаром
	$product_categories = wp_get_post_terms($product_id, 'product_cat', array('fields' => 'ids'));

	if (!in_array($category_id, $product_categories)) {
		return; // Категория не связана с товаром
	}

	// Получаем данные раскрывающегося текста из категории
	$category_expanding_data = get_category_expanding_text_data($category_id);

	// Проверяем, есть ли данные для вывода
	if (!has_category_expanding_text($category_id)) {
		return;
	}

	// ПРИНУДИТЕЛЬНО устанавливаем серый фон для товара
	$category_expanding_data['background_color'] = 'grey';

	// Проверяем, есть ли переопределение заголовка в настройках товара
	if (!empty($expanding_text_settings['custom_section_title'])) {
		$category_expanding_data['section_title'] = $expanding_text_settings['custom_section_title'];
	}

	// Добавляем кнопку по умолчанию если её нет
	if (empty($category_expanding_data['button_text'])) {
		$category_expanding_data['button_text'] = 'Читать далее';
	}

	// Временно устанавливаем данные для ACF фильтра
	global $temp_expanding_text_data;
	$temp_expanding_text_data = $category_expanding_data;

	// Создаем блок как в Gutenberg
	global $block;
	$block = array(
		'id' => uniqid('expanding-text-'),
		'className' => ''
	);

	// Добавляем фильтр для подмены ACF данных
	add_filter('acf/load_value', 'temp_expanding_text_acf_filter', 10, 3);

	// Подключаем шаблон раскрывающегося текста
	$template_path = get_template_directory() . '/template-parts/blocks/general-info/general-info.php';
	if (file_exists($template_path)) {
		include $template_path;
	}

	// Очищаем временные данные
	$temp_expanding_text_data = null;
	$block = null;
	remove_filter('acf/load_value', 'temp_expanding_text_acf_filter', 10);
}

/**
 * Фильтр для подмены ACF данных раскрывающегося текста товара
 */
function temp_expanding_text_acf_filter($value, $post_id, $field)
{
	global $temp_expanding_text_data;

	if ($temp_expanding_text_data && isset($field['name'])) {
		switch ($field['name']) {
			case 'section_title':
			case 'section_title_general_info':
				return $temp_expanding_text_data['section_title'];
			case 'background_color':
			case 'background_color_general_info':
				return 'grey';
			case 'main_content':
				return $temp_expanding_text_data['main_content'];
			case 'additional_content':
				return $temp_expanding_text_data['additional_content'];
			case 'button_text':
				return $temp_expanding_text_data['button_text'];
		}
	}

	return $value;
}

/**
 * Получает все категории товара для выбора в ACF поле
 */
function get_product_categories_for_acf_choices($product_id = null)
{
	if (!$product_id) {
		global $post;
		$product_id = $post ? $post->ID : null;
	}

	if (!$product_id) {
		return array();
	}

	$categories = wp_get_post_terms($product_id, 'product_cat');
	$choices = array();

	if (!empty($categories)) {
		foreach ($categories as $category) {
			// Проверяем, есть ли в категории настроенный раскрывающийся текст
			if (has_category_expanding_text($category->term_id)) {
				$choices[$category->term_id] = $category->name . ' ✓';
			} else {
				$choices[$category->term_id] = $category->name . ' (не настроен)';
			}
		}
	}

	return $choices;
}

/**
 * Хук для обновления выборов в ACF поле при загрузке
 */
add_filter('acf/load_field/name=category_source', 'load_category_choices_for_expanding_text');

function load_category_choices_for_expanding_text($field)
{
	global $post;

	if ($post && $post->post_type === 'product') {
		$choices = get_product_categories_for_acf_choices($post->ID);

		if (!empty($choices)) {
			$field['choices'] = $choices;
		} else {
			$field['choices'] = array('' => 'Нет категорий с настроенным текстом');
		}
	}

	return $field;
}






// Добавляем JavaScript для динамического обновления предпросмотра
add_action('acf/input/admin_head', 'add_expanding_text_preview_script');

function add_expanding_text_preview_script()
{
	global $post;

	if (!$post || $post->post_type !== 'product') {
		return;
	}
	?>
	<script type="text/javascript">
		jQuery(document).ready(function ($) {
			// Функция для обновления предпросмотра
			function updateExpandingTextPreview() {
				var categoryId = $('[data-name="category_source"] select').val();
				var previewField = $('[data-name="show_preview"] .acf-input');

				if (!categoryId) {
					previewField.html('<div class="acf-message-wrapper"><p class="acf-message">Выберите категорию для просмотра настроек</p></div>');
					return;
				}

				// AJAX запрос для получения данных категории
				$.ajax({
					url: ajaxurl,
					type: 'POST',
					data: {
						action: 'get_category_expanding_text_preview',
						category_id: categoryId,
						nonce: '<?php echo wp_create_nonce("category_preview_nonce"); ?>'
					},
					success: function (response) {
						if (response.success) {
							var data = response.data;
							var previewHtml = '<div style="background: #f9f9f9; padding: 15px; border-radius: 4px; border-left: 4px solid #0073aa;">';

							if (data.section_title) {
								previewHtml += '<h4 style="margin: 0 0 10px 0; color: #0073aa;">📝 ' + data.section_title + '</h4>';
							}

							previewHtml += '<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">';

							// Левая колонка
							previewHtml += '<div>';
							previewHtml += '<p style="margin: 0 0 8px 0;"><strong>Фон:</strong> ' + (data.background_color === 'grey' ? 'Серый' : 'Белый') + '</p>';

							if (data.main_content) {
								var mainContentPreview = data.main_content.length > 100 ?
									data.main_content.substring(0, 100) + '...' :
									data.main_content;
								previewHtml += '<p style="margin: 0 0 8px 0;"><strong>Основной текст:</strong><br><em>' + mainContentPreview + '</em></p>';
							}
							previewHtml += '</div>';

							// Правая колонка
							previewHtml += '<div>';
							if (data.additional_content) {
								var additionalContentPreview = data.additional_content.length > 100 ?
									data.additional_content.substring(0, 100) + '...' :
									data.additional_content;
								previewHtml += '<p style="margin: 0 0 8px 0;"><strong>Дополнительный текст:</strong><br><em>' + additionalContentPreview + '</em></p>';
							}

							previewHtml += '<p style="margin: 0;"><strong>Статус:</strong> <span style="color: green;">✓ Настроен</span></p>';
							previewHtml += '</div>';

							previewHtml += '</div>';
							previewHtml += '</div>';

							previewField.html(previewHtml);
						} else {
							previewField.html('<div class="acf-message-wrapper"><p class="acf-message acf-message-error">Ошибка загрузки данных категории</p></div>');
						}
					},
					error: function () {
						previewField.html('<div class="acf-message-wrapper"><p class="acf-message acf-message-error">Ошибка AJAX запроса</p></div>');
					}
				});
			}

			// Обновляем предпросмотр при изменении категории
			$(document).on('change', '[data-name="category_source"] select', function () {
				updateExpandingTextPreview();
			});

			// Обновляем предпросмотр при загрузке страницы
			setTimeout(updateExpandingTextPreview, 500);
		});
	</script>
	<?php
}

// AJAX обработчик для получения данных категории
add_action('wp_ajax_get_category_expanding_text_preview', 'handle_category_expanding_text_preview');

function handle_category_expanding_text_preview()
{
	// Проверяем nonce
	if (!wp_verify_nonce($_POST['nonce'], 'category_preview_nonce')) {
		wp_die('Ошибка безопасности');
	}

	$category_id = intval($_POST['category_id']);

	if (!$category_id) {
		wp_send_json_error('Неверный ID категории');
	}

	// Получаем данные категории
	$category_data = get_category_expanding_text_data($category_id);

	// Очищаем HTML теги для предпросмотра
	if (isset($category_data['main_content'])) {
		$category_data['main_content'] = wp_strip_all_tags($category_data['main_content']);
	}

	if (isset($category_data['additional_content'])) {
		$category_data['additional_content'] = wp_strip_all_tags($category_data['additional_content']);
	}

	wp_send_json_success($category_data);
}

// Обновляем поле предпросмотра при загрузке
add_filter('acf/load_field/name=show_preview', 'load_expanding_text_preview_field');

function load_expanding_text_preview_field($field)
{
	$field['message'] = '<div id="expanding-text-preview">
        <div style="text-align: center; padding: 20px; color: #666;">
            <p>Выберите категорию выше, чтобы увидеть предпросмотр настроек</p>
        </div>
    </div>';

	return $field;
}












/**
 * Фильтр для временной подмены ACF данных
 */
function temp_acf_load_value($value, $post_id, $field)
{
	global $temp_acf_data;

	if ($temp_acf_data && isset($temp_acf_data[$field['name']])) {
		return $temp_acf_data[$field['name']];
	}

	return $value;
}

//======================================================================
// Дополнительные функции для управления FAQ
//======================================================================

/**
 * Проверяет, есть ли FAQ у категории
 */
function category_has_faq($category_id)
{
	$faq = get_category_faq($category_id);
	return !empty($faq);
}

/**
 * Получает количество вопросов в FAQ категории
 */
function get_category_faq_count($category_id)
{
	$faq = get_category_faq($category_id);
	return is_array($faq) ? count($faq) : 0;
}

/**
 * Получает все категории товара с FAQ
 */
function get_product_categories_with_faq($product_id)
{
	$terms = wp_get_post_terms($product_id, 'product_cat');
	$categories_with_faq = array();

	if (!empty($terms) && !is_wp_error($terms)) {
		foreach ($terms as $term) {
			if (category_has_faq($term->term_id)) {
				$categories_with_faq[] = array(
					'category' => $term,
					'faq_count' => get_category_faq_count($term->term_id),
					'level' => get_category_level($term->term_id)
				);
			}
		}

		// Сортируем по уровню (сначала более глубокие)
		usort($categories_with_faq, function ($a, $b) {
			return $b['level'] - $a['level'];
		});
	}

	return $categories_with_faq;
}











//======================================================================
// Портфолио для товаров и категорий WooCommerce
//======================================================================

/**
 * Получает настройки портфолио для категории товаров
 */
function get_category_portfolio_settings($category_id)
{
	if (!function_exists('get_field')) {
		return false;
	}

	$portfolio_category = get_field('category_portfolio_category', 'product_cat_' . $category_id);
	$portfolio_count = get_field('category_portfolio_count', 'product_cat_' . $category_id) ?: 6;
	$portfolio_title = get_field('category_portfolio_title', 'product_cat_' . $category_id);

	if (!$portfolio_category) {
		return false;
	}

	return array(
		'portfolio_category' => $portfolio_category,
		'portfolio_count' => min($portfolio_count, 6), // Максимум 6
		'portfolio_title' => $portfolio_title ?: 'Наши работы'
	);
}

/**
 * Получает настройки портфолио для товара с учетом приоритета
 */
function get_product_portfolio_settings($product_id)
{
	if (!function_exists('get_field')) {
		return false;
	}

	// Проверяем, включено ли отображение портфолио для товара
	$show_portfolio = get_field('product_show_portfolio', $product_id);
	if ($show_portfolio === false || $show_portfolio === '0') {
		return false;
	}

	// Получаем настройки товара
	$product_portfolio_category = get_field('product_portfolio_category', $product_id);
	$product_portfolio_count = get_field('product_portfolio_count', $product_id) ?: 6;
	$product_portfolio_title = get_field('product_portfolio_title', $product_id);

	// Если в товаре выбрана категория портфолио - используем её
	if ($product_portfolio_category) {
		return array(
			'portfolio_category' => $product_portfolio_category,
			'portfolio_count' => min($product_portfolio_count, 6),
			'portfolio_title' => $product_portfolio_title ?: 'Наши работы',
			'source' => 'product'
		);
	}

	// Иначе ищем в категориях товара
	$terms = wp_get_post_terms($product_id, 'product_cat');

	if (empty($terms) || is_wp_error($terms)) {
		return false;
	}

	// Сортируем категории по уровню вложенности (сначала более глубокие)
	$categories_by_level = array();

	foreach ($terms as $term) {
		$level = get_category_level($term->term_id);
		if (!isset($categories_by_level[$level])) {
			$categories_by_level[$level] = array();
		}
		$categories_by_level[$level][] = $term;
	}

	krsort($categories_by_level);

	// Ищем портфолио в категориях, начиная с более глубоких
	foreach ($categories_by_level as $level => $categories) {
		foreach ($categories as $category) {
			$category_settings = get_category_portfolio_settings($category->term_id);
			if ($category_settings) {
				// Переопределяем заголовок из товара если есть
				if ($product_portfolio_title) {
					$category_settings['portfolio_title'] = $product_portfolio_title;
				}
				// Переопределяем количество из товара если есть
				$category_settings['portfolio_count'] = min($product_portfolio_count, 6);
				$category_settings['source'] = 'category';
				$category_settings['source_category'] = $category;

				return $category_settings;
			}
		}
	}

	return false;
}

/**
 * Получает настройки портфолио для архива категории
 */
function get_archive_portfolio_settings($category_id = null)
{
	if (!$category_id) {
		$current_category = get_queried_object();
		if ($current_category && isset($current_category->term_id)) {
			$category_id = $current_category->term_id;
		} else {
			return false;
		}
	}

	$settings = get_category_portfolio_settings($category_id);
	if ($settings) {
		$settings['source'] = 'archive';
		return $settings;
	}

	return false;
}

/**
 * Получает работы портфолио по категории
 */
function get_portfolio_posts_by_category($portfolio_category_id, $count = 6)
{
	$args = array(
		'post_type' => 'portfolio',
		'posts_per_page' => min($count, 6),
		'post_status' => 'publish',
		'orderby' => 'date',
		'order' => 'DESC',
		'tax_query' => array(
			array(
				'taxonomy' => 'portfolio_category',
				'field' => 'term_id',
				'terms' => $portfolio_category_id,
			),
		),
	);

	$query = new WP_Query($args);
	$posts = array();

	if ($query->have_posts()) {
		while ($query->have_posts()) {
			$query->the_post();
			$posts[] = get_post();
		}
		wp_reset_postdata();
	}

	return $posts;
}

/**
 * Выводит блок портфолио для товара используя существующий шаблон
 */
function render_product_portfolio($product_id, $background = 'bg-grey')
{
	$settings = get_product_portfolio_settings($product_id);

	if (!$settings) {
		return;
	}

	// Получаем работы портфолио
	$portfolio_posts = get_portfolio_posts_by_category(
		$settings['portfolio_category'],
		$settings['portfolio_count']
	);

	if (empty($portfolio_posts)) {
		return;
	}

	// Симулируем ACF поля для блока портфолио
	$simulated_acf_data = array(
		'grid_title' => $settings['portfolio_title'],
		'grid_background' => $background,
		'grid_display_type' => 'custom',
		'grid_posts_count' => $settings['portfolio_count'],
		'grid_custom_posts' => $portfolio_posts,
		'grid_show_all_works_button' => true,
		'grid_button_text' => 'Все наши работы',
		'portfolio_category_id' => $settings['portfolio_category'] // Передаем ID категории
	);

	// Временно устанавливаем данные для ACF
	global $temp_acf_data;
	$temp_acf_data = $simulated_acf_data;
	add_filter('acf/load_value', 'temp_acf_load_value', 10, 3);

	// Подключаем шаблон блока портфолио
	include get_template_directory() . '/template-parts/blocks/portfolio-grid/portfolio-grid.php';

	// Очищаем временные данные
	$temp_acf_data = null;
	remove_filter('acf/load_value', 'temp_acf_load_value', 10);
}

/**
 * Выводит блок портфолио для архива категории
 */
function render_archive_portfolio($category_id = null, $background = 'bg-grey')
{
	$settings = get_archive_portfolio_settings($category_id);

	if (!$settings) {
		return;
	}

	// Получаем работы портфолио
	$portfolio_posts = get_portfolio_posts_by_category(
		$settings['portfolio_category'],
		$settings['portfolio_count']
	);

	if (empty($portfolio_posts)) {
		return;
	}

	// Симулируем ACF поля для блока портфолио
	$simulated_acf_data = array(
		'grid_title' => $settings['portfolio_title'],
		'grid_background' => $background,
		'grid_display_type' => 'custom',
		'grid_posts_count' => $settings['portfolio_count'],
		'grid_custom_posts' => $portfolio_posts,
		'grid_show_all_works_button' => true,
		'grid_button_text' => 'Все наши работы',
		'portfolio_category_id' => $settings['portfolio_category'] // Передаем ID категории
	);

	// Временно устанавливаем данные для ACF
	global $temp_acf_data;
	$temp_acf_data = $simulated_acf_data;
	add_filter('acf/load_value', 'temp_acf_load_value', 10, 3);

	// Подключаем шаблон блока портфолио
	include get_template_directory() . '/template-parts/blocks/portfolio-grid/portfolio-grid.php';

	// Очищаем временные данные
	$temp_acf_data = null;
	remove_filter('acf/load_value', 'temp_acf_load_value', 10);
}

//======================================================================
// Дополнительные функции для управления портфолио
//======================================================================

/**
 * Проверяет, есть ли настройки портфолио у категории товаров
 */
function category_has_portfolio($category_id)
{
	$settings = get_category_portfolio_settings($category_id);
	return !empty($settings);
}

/**
 * Получает количество работ в категории портфолио
 */
function get_portfolio_category_count($portfolio_category_id)
{
	$count = wp_count_posts('portfolio');

	$args = array(
		'post_type' => 'portfolio',
		'posts_per_page' => -1,
		'post_status' => 'publish',
		'fields' => 'ids',
		'tax_query' => array(
			array(
				'taxonomy' => 'portfolio_category',
				'field' => 'term_id',
				'terms' => $portfolio_category_id,
			),
		),
	);

	$query = new WP_Query($args);
	return $query->found_posts;
}

/**
 * Получает все категории товаров с настройками портфолио
 */
function get_product_categories_with_portfolio($product_id)
{
	$terms = wp_get_post_terms($product_id, 'product_cat');
	$categories_with_portfolio = array();

	if (!empty($terms) && !is_wp_error($terms)) {
		foreach ($terms as $term) {
			if (category_has_portfolio($term->term_id)) {
				$settings = get_category_portfolio_settings($term->term_id);
				$portfolio_count = get_portfolio_category_count($settings['portfolio_category']);

				$categories_with_portfolio[] = array(
					'category' => $term,
					'portfolio_settings' => $settings,
					'portfolio_count' => $portfolio_count,
					'level' => get_category_level($term->term_id)
				);
			}
		}

		// Сортируем по уровню (сначала более глубокие)
		usort($categories_with_portfolio, function ($a, $b) {
			return $b['level'] - $a['level'];
		});
	}

	return $categories_with_portfolio;
}



//======================================================================
// Убираем ненужные мета-боксы из админки товара
//======================================================================

add_action('add_meta_boxes', 'remove_product_meta_boxes', 99);
function remove_product_meta_boxes()
{
	remove_meta_box('commentstatusdiv', 'product', 'normal'); // Отзывы
	remove_meta_box('commentsdiv', 'product', 'normal'); // Комментарии
	remove_meta_box('product_tag', 'product', 'side'); // Метки товаров
}

//======================================================================
// Ограничиваем возможности редактора для описания товара
//======================================================================

add_filter('wp_editor_settings', 'limit_product_editor_settings', 10, 2);
function limit_product_editor_settings($settings, $editor_id)
{
	// Ограничиваем только для описания и краткого описания товара
	if ($editor_id == 'content' || $editor_id == 'excerpt') {
		global $post_type;
		if ($post_type == 'product') {
			$settings['media_buttons'] = false;
			$settings['tinymce'] = array(
				'toolbar1' => 'bold,italic,underline,separator,bullist,numlist,separator,link,unlink',
				'toolbar2' => '',
				'toolbar3' => ''
			);
		}
	}
	return $settings;
}

//======================================================================
// Убираем возможность загружать медиафайлы в описание товара
//======================================================================

add_filter('user_can_richedit', 'disable_media_buttons_for_products');
function disable_media_buttons_for_products($wp_rich_edit)
{
	global $post_type;
	if ($post_type == 'product') {
		return false;
	}
	return $wp_rich_edit;
}

//======================================================================
// Убираем вкладки из админки товара
//======================================================================

add_filter('woocommerce_product_data_tabs', 'remove_product_data_tabs');
function remove_product_data_tabs($tabs)
{
	unset($tabs['reviews']); // Отзывы
	return $tabs;
}


//======================================================================
// Изменяем бейдж "Распродажа!" на "Товар со скидкой"
//======================================================================

add_filter('woocommerce_sale_flash', 'custom_sale_flash', 10, 3);

function custom_sale_flash($text, $post, $product)
{
	return '<span class="onsale">Товар со скидкой</span>';
}

//======================================================================
// Изменяем порядок цены
//======================================================================

add_filter('woocommerce_get_price_html', 'custom_price_html', 10, 2);
function custom_price_html($price, $product)
{
	if ($product->is_on_sale()) {
		$regular_price = $product->get_regular_price();
		$sale_price = $product->get_sale_price();

		if ($regular_price && $sale_price) {
			$price = '<span class="price">';
			$price .= '<ins>' . wc_price($sale_price) . '</ins> ';
			$price .= '<del>' . wc_price($regular_price) . '</del>';
			$price .= '</span>';
		}
	}

	return $price;
}


//======================================================================
// Обновленная функция хлебных крошек в functions.php ПРОВЕРИТЬ
//======================================================================

function custom_product_breadcrumbs()
{
	global $product;

	echo '<ol class="breadcrumb bg-transparent p-0 m-0">';

	// Домой с картинкой
	echo '<li class="breadcrumb-item">';
	echo '<a href="' . home_url() . '">';
	echo '<img src="' . get_template_directory_uri() . '/assets/img/ico/breadcrumbs.svg" loading="lazy" alt="Главная" />';
	echo '</a>';
	echo '</li>';

	// Получаем категории товара
	$terms = wp_get_post_terms($product->get_id(), 'product_cat');
	if (!empty($terms) && !is_wp_error($terms)) {
		// Находим самую глубокую категорию (с наибольшим количеством предков)
		$deepest_term = null;
		$max_depth = -1;

		foreach ($terms as $term) {
			$ancestors = get_ancestors($term->term_id, 'product_cat');
			$depth = count($ancestors);

			if ($depth > $max_depth) {
				$max_depth = $depth;
				$deepest_term = $term;
			}
		}

		if ($deepest_term) {
			// Получаем всю иерархию для самой глубокой категории
			$ancestors = get_ancestors($deepest_term->term_id, 'product_cat');
			$ancestors = array_reverse($ancestors); // Переворачиваем, чтобы получить от корня

			// Добавляем саму категорию в конец
			$ancestors[] = $deepest_term->term_id;

			// Ограничиваем до 3 уровней (исключая товар)
			// Всего должно быть: Главная + макс 2 категории + товар = 4 элемента
			$max_categories = 2; // максимум 2 уровня категорий

			if (count($ancestors) > $max_categories) {
				// Берем первую (корневую) и последнюю категории
				$limited_ancestors = array(
					$ancestors[0], // Первая (корневая) категория
					$ancestors[count($ancestors) - 1] // Последняя (самая глубокая) категория
				);
			} else {
				$limited_ancestors = $ancestors;
			}

			// Выводим категории
			foreach ($limited_ancestors as $ancestor_id) {
				$ancestor_term = get_term($ancestor_id, 'product_cat');
				if ($ancestor_term && !is_wp_error($ancestor_term)) {
					echo '<li class="breadcrumb-item">';
					echo '<a href="' . get_term_link($ancestor_term) . '">' . $ancestor_term->name . '</a>';
					echo '</li>';
				}
			}
		}
	}

	// Текущий товар
	echo '<li class="breadcrumb-item active" aria-current="page">' . get_the_title() . '</li>';

	echo '</ol>';
}

//======================================================================
// Подключение разных шаблонов для архивных страниц
//======================================================================

add_filter('archive_template', 'custom_archive_product_template');
function custom_archive_product_template($template)
{
	if (is_product_category()) {
		$term = get_queried_object();

		// Для категории "shop" (магазин)
		if ($term->slug == 'shop') {
			$custom_template = locate_template('woocommerce/archive-product-shop.php');
			if ($custom_template) {
				return $custom_template;
			}
		}
		// Для категории "product" (продукция)
		elseif ($term->slug == 'product') {
			$custom_template = locate_template('woocommerce/archive-product-product.php');
			if ($custom_template) {
				return $custom_template;
			}
		}

		// Fallback на базовый шаблон archive-product.php для всех остальных категорий
		$custom_template = locate_template('woocommerce/archive-product.php');
		if ($custom_template) {
			return $custom_template;
		}
	}

	return $template;
}

//======================================================================
// Подключение разных шаблонов для разных категорий
//======================================================================

add_filter('single_template', 'custom_single_product_template');
function custom_single_product_template($template)
{
	global $post;

	if ($post->post_type == 'product') {
		$terms = wp_get_post_terms($post->ID, 'product_cat');

		if (!empty($terms) && !is_wp_error($terms)) {

			// Проверяем категории товара по приоритету
			$has_shop_category = false;
			$has_product_category = false;

			foreach ($terms as $term) {
				if ($term->slug == 'shop') {
					$has_shop_category = true;
					break; // shop имеет высший приоритет
				} elseif ($term->slug == 'product') {
					$has_product_category = true;
				}
			}

			if ($has_shop_category) {
				// Шаблон для товаров из категории "shop"
				$custom_template = locate_template('woocommerce/single-product-shop.php');
				if ($custom_template) {
					return $custom_template;
				}
			} elseif ($has_product_category) {
				// Шаблон для товаров из категории "product"
				$custom_template = locate_template('woocommerce/single-product-product.php');
				if ($custom_template) {
					return $custom_template;
				}
			}

			// Fallback на базовый шаблон single-product.php для всех остальных
			$custom_template = locate_template('woocommerce/single-product.php');
			if ($custom_template) {
				return $custom_template;
			}
		}
	}

	return $template;
}

function is_product_in_shop_category($product_id = null)
{
	if (!$product_id) {
		$product_id = get_the_ID();
	}

	$terms = wp_get_post_terms($product_id, 'product_cat');

	if (!empty($terms) && !is_wp_error($terms)) {
		foreach ($terms as $term) {
			if ($term->slug == 'shop') {
				return true;
			}
		}
	}

	return false;
}

/**
 * Проверяет, принадлежит ли товар к категории "product"
 */
function is_product_in_product_category($product_id = null)
{
	if (!$product_id) {
		$product_id = get_the_ID();
	}

	$terms = wp_get_post_terms($product_id, 'product_cat');

	if (!empty($terms) && !is_wp_error($terms)) {
		foreach ($terms as $term) {
			if ($term->slug == 'product') {
				return true;
			}
		}
	}

	return false;
}

/**
 * Проверяет, находимся ли мы в категории "shop"
 */
function is_shop_category()
{
	if (is_product_category()) {
		$term = get_queried_object();
		return ($term->slug == 'shop');
	}

	return false;
}

/**
 * Проверяет, находимся ли мы в категории "product"
 */
function is_product_category_page()
{
	if (is_product_category()) {
		$term = get_queried_object();
		return ($term->slug == 'product');
	}

	return false;
}

/**
 * Получает тип категории товара (shop, product, или other)
 */
function get_product_category_type($product_id = null)
{
	if (!$product_id) {
		$product_id = get_the_ID();
	}

	if (is_product_in_shop_category($product_id)) {
		return 'shop';
	} elseif (is_product_in_product_category($product_id)) {
		return 'product';
	} else {
		return 'other';
	}
}

// Добавление мета-бокса для фонового изображения для всех страниц, но с условным отображением

function add_page_hero_bg_meta_box()
{
	add_meta_box(
		'page_hero_bg',
		'Фоновое изображение для заголовочной секции',
		'page_hero_bg_meta_box_callback',
		'page',
		'side',
		'default'
	);
}
add_action('add_meta_boxes', 'add_page_hero_bg_meta_box');

// JavaScript для динамического показа/скрытия мета-бокса при смене шаблона
function add_page_template_change_script()
{
	global $post;
	if (!$post || $post->post_type !== 'page') {
		return;
	}
	?>
	<script type="text/javascript">
		jQuery(document).ready(function ($) {
			// Функция для показа/скрытия мета-бокса
			function toggleHeroBgMetaBox() {
				var selectedTemplate = $('#page_template').val();
				var metaBox = $('#page_hero_bg');
				var warning = $('#template-warning');

				if (selectedTemplate === 'page_with_bread_crumbs.php') {
					metaBox.show();
					warning.hide();
				} else {
					metaBox.show(); // Показываем мета-бокс всегда
					warning.show(); // Но показываем предупреждение
				}
			}

			// Проверяем при загрузке страницы
			toggleHeroBgMetaBox();

			// Отслеживаем изменение шаблона
			$('#page_template').on('change', function () {
				toggleHeroBgMetaBox();
			});
		});
	</script>
	<?php
}
add_action('admin_footer-post.php', 'add_page_template_change_script');
add_action('admin_footer-post-new.php', 'add_page_template_change_script');

// Callback функция для мета-бокса фонового изображения страниц
function page_hero_bg_meta_box_callback($post)
{
	wp_nonce_field('page_hero_bg_meta_box', 'page_hero_bg_meta_box_nonce');

	$hero_bg_id = get_post_meta($post->ID, 'page_hero_bg', true);
	$hero_bg_url = '';

	if ($hero_bg_id) {
		$hero_bg_url = wp_get_attachment_image_src($hero_bg_id, 'large')[0];
	}
	?>

	<div id="template-warning"
		style="background: #fff3cd; border: 1px solid #ffeaa7; padding: 10px; margin-bottom: 15px; border-radius: 4px; display: none;">
		<strong>⚠️ Внимание:</strong> Фоновое изображение будет отображаться только при использовании шаблона "Страница с
		хлебными крошками".
	</div>

	<div id="page-hero-bg-container">
		<div id="page-hero-bg-preview" style="margin-bottom: 15px;">
			<?php if ($hero_bg_url): ?>
				<img src="<?php echo $hero_bg_url; ?>"
					style="width: 100%; max-height: 150px; object-fit: cover; border-radius: 4px;">
			<?php else: ?>
				<div
					style="width: 100%; height: 80px; background: #f0f0f0; border: 2px dashed #ccc; display: flex; align-items: center; justify-content: center; border-radius: 4px;">
					<span style="color: #666;">Фон не выбран</span>
				</div>
			<?php endif; ?>
		</div>

		<button type="button" id="select-page-hero-bg" class="button">Выбрать фоновое изображение</button>
		<button type="button" id="remove-page-hero-bg" class="button"
			style="<?php echo $hero_bg_id ? '' : 'display: none;'; ?>">Удалить фон</button>
		<input type="hidden" id="page-hero-bg-id" name="page_hero_bg" value="<?php echo $hero_bg_id; ?>">
	</div>

	<p class="description" style="margin-top: 10px;">
		Рекомендуемый размер: 1920x600px. Если фон не выбран, будет использоваться стандартный фон из CSS.
	</p>

	<script type="text/javascript">
		jQuery(document).ready(function ($) {
			var mediaUploader;

			$('#select-page-hero-bg').on('click', function (e) {
				e.preventDefault();

				if (mediaUploader) {
					mediaUploader.open();
					return;
				}

				mediaUploader = wp.media({
					title: 'Выберите фоновое изображение',
					button: {
						text: 'Использовать как фон'
					},
					multiple: false
				});

				mediaUploader.on('select', function () {
					var attachment = mediaUploader.state().get('selection').first().toJSON();

					$('#page-hero-bg-id').val(attachment.id);
					$('#page-hero-bg-preview').html('<img src="' + attachment.sizes.medium.url + '" style="width: 100%; max-height: 150px; object-fit: cover; border-radius: 4px;">');
					$('#remove-page-hero-bg').show();
				});

				mediaUploader.open();
			});

			$('#remove-page-hero-bg').on('click', function () {
				$('#page-hero-bg-id').val('');
				$('#page-hero-bg-preview').html('<div style="width: 100%; height: 80px; background: #f0f0f0; border: 2px dashed #ccc; display: flex; align-items: center; justify-content: center; border-radius: 4px;"><span style="color: #666;">Фон не выбран</span></div>');
				$(this).hide();
			});
		});
	</script>
	<?php
}

// Сохранение данных мета-бокса фонового изображения для страниц
function save_page_hero_bg_meta_box($post_id)
{
	if (!isset($_POST['page_hero_bg_meta_box_nonce'])) {
		return;
	}

	if (!wp_verify_nonce($_POST['page_hero_bg_meta_box_nonce'], 'page_hero_bg_meta_box')) {
		return;
	}

	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
		return;
	}

	if (isset($_POST['post_type']) && 'page' == $_POST['post_type']) {
		if (!current_user_can('edit_page', $post_id)) {
			return;
		}
	}

	if (isset($_POST['page_hero_bg'])) {
		$hero_bg_id = intval($_POST['page_hero_bg']);
		if ($hero_bg_id) {
			update_post_meta($post_id, 'page_hero_bg', $hero_bg_id);
		} else {
			delete_post_meta($post_id, 'page_hero_bg');
		}
	}
}
add_action('save_post', 'save_page_hero_bg_meta_box');

// Функция для проверки использования шаблона с хлебными крошками
function is_breadcrumbs_page_template()
{
	global $post;
	if (!is_page() || !$post) {
		return false;
	}

	$page_template = get_page_template_slug($post->ID);
	return $page_template === 'page_with_bread_crumbs.php';
}

// Обновляем фильтр render_block для работы со страницами с хлебными крошками
add_filter('render_block', 'wrap_standard_blocks_with_container_pages', 10, 2);

function wrap_standard_blocks_with_container_pages($block_content, $block)
{
	// Пропускаем если мы не используем шаблон с хлебными крошками
	if (!is_breadcrumbs_page_template()) {
		return $block_content;
	}

	// Пропускаем пустые блоки
	if (empty(trim($block_content))) {
		return $block_content;
	}

	// Автоматически определяем все ACF блоки (начинающиеся с 'acf/')
	if (isset($block['blockName']) && strpos($block['blockName'], 'acf/') === 0) {
		return $block_content;
	}

	// Для всех остальных блоков добавляем специальный класс
	if (isset($block['blockName']) && !empty($block['blockName'])) {
		return '<div class="standard-block-wrapper-page">' . $block_content . '</div>';
	}

	return $block_content;
}

// Функция для вывода контента страниц с группировкой стандартных блоков
function render_page_content($content)
{
	// Применяем все фильтры WordPress включая наш
	$processed_content = apply_filters('the_content', $content);

	// Простая замена: группируем блоки с классом standard-block-wrapper-page
	$pattern = '/(<div class="standard-block-wrapper-page">.*?<\/div>)/s';
	$parts = preg_split($pattern, $processed_content, -1, PREG_SPLIT_DELIM_CAPTURE);

	$current_standard_group = '';

	foreach ($parts as $part) {
		if (empty(trim($part)))
			continue;

		if (strpos($part, 'standard-block-wrapper-page') !== false) {
			// Накапливаем стандартные блоки
			$clean_content = preg_replace('/<div class="standard-block-wrapper-page">(.*?)<\/div>/s', '$1', $part);
			$current_standard_group .= $clean_content;
		} else {
			// Если накопились стандартные блоки, выводим их в контейнере
			if (!empty(trim($current_standard_group))) {
				?>
				<section class="section single-page-content">
					<div class="container">
						<div class="row justify-content-center">
							<div class="col-12 col-lg-8">
								<div class="page-content">
									<?php echo $current_standard_group; ?>
								</div>
							</div>
						</div>
					</div>
				</section>
				<?php
				$current_standard_group = '';
			}

			// Выводим кастомный блок как есть
			echo $part;
		}
	}

	// Если остались стандартные блоки в конце
	if (!empty(trim($current_standard_group))) {
		?>
		<section class="section single-page-content">
			<div class="container">
				<div class="row justify-content-center">
					<div class="col-12 col-lg-8">
						<div class="page-content">
							<?php echo $current_standard_group; ?>
						</div>
					</div>
				</div>
			</div>
		</section>
		<?php
	}
}

// Функция для генерации хлебных крошек для страниц
function render_page_breadcrumbs($page_title = '')
{
	global $post;

	if (empty($page_title)) {
		$page_title = get_the_title();
	}

	echo '<nav aria-label="breadcrumb" class="mb-0">';
	echo '<ol class="breadcrumb bg-transparent p-0 m-0">';

	// Главная страница
	echo '<li class="breadcrumb-item">';
	echo '<a href="' . home_url() . '" class="text-decoration-none text-secondary">';
	echo '<img src="' . get_template_directory_uri() . '/assets/img/ico/breadcrumbs.svg" loading="lazy" />';
	echo '</a>';
	echo '</li>';

	// Родительские страницы
	if ($post->post_parent) {
		$parent_ids = array_reverse(get_post_ancestors($post->ID));
		foreach ($parent_ids as $parent_id) {
			echo '<li class="breadcrumb-item">';
			echo '<a href="' . get_permalink($parent_id) . '" class="text-decoration-none text-secondary">';
			echo get_the_title($parent_id);
			echo '</a>';
			echo '</li>';
		}
	}

	// Текущая страница
	echo '<li class="breadcrumb-item active" aria-current="page">';
	echo wp_trim_words($page_title, 6);
	echo '</li>';

	echo '</ol>';
	echo '</nav>';
}





/**
 * Система "Комплексное оформление" для WooCommerce
 */

// Регистрируем таксономию "Комплексное оформление"
add_action('init', 'register_complex_design_taxonomy');
function register_complex_design_taxonomy()
{
	$labels = array(
		'name' => 'Комплексное оформление',
		'singular_name' => 'Комплексное оформление',
		'search_items' => 'Поиск оформлений',
		'all_items' => 'Все оформления',
		'edit_item' => 'Редактировать оформление',
		'update_item' => 'Обновить оформление',
		'add_new_item' => 'Добавить новое оформление',
		'new_item_name' => 'Название нового оформления',
		'menu_name' => 'Комплексное оформление',
	);

	$args = array(
		'hierarchical' => false,
		'labels' => $labels,
		'show_ui' => true,
		'show_admin_column' => false,
		'query_var' => true,
		'rewrite' => array('slug' => 'complex-design'),
		'public' => true,
		'show_in_menu' => true,
		'show_tagcloud' => false,
		'show_in_rest' => true,
	);

	register_taxonomy('complex_design', array('product'), $args);
}

// Поле миниатюры для формы создания
function add_complex_design_thumbnail_field($tag)
{
	?>
	<div class="form-field">
		<label for="complex_design_thumbnail">Миниатюра</label>
		<input type="hidden" id="complex_design_thumbnail" name="complex_design_thumbnail" value="" />
		<div id="complex_design_thumbnail_preview"></div>
		<button type="button" class="button complex-design-thumbnail-upload">Выбрать изображение</button>
		<button type="button" class="button complex-design-thumbnail-remove" style="display:none;">Удалить изображение</button>
		<p>Выберите изображение для миниатюры оформления.</p>
	</div>

	<script>
		jQuery(document).ready(function ($) {
			var mediaUploader;

			$('.complex-design-thumbnail-upload').click(function (e) {
				e.preventDefault();

				if (mediaUploader) {
					mediaUploader.open();
					return;
				}

				mediaUploader = wp.media({
					title: 'Выберите миниатюру',
					button: { text: 'Использовать это изображение' },
					multiple: false
				});

				mediaUploader.on('select', function () {
					var attachment = mediaUploader.state().get('selection').first().toJSON();
					$('#complex_design_thumbnail').val(attachment.id);
					$('#complex_design_thumbnail_preview').html('<img src="' + attachment.sizes.thumbnail.url + '" style="max-width: 150px;" />');
					$('.complex-design-thumbnail-remove').show();
				});

				mediaUploader.open();
			});

			$('.complex-design-thumbnail-remove').click(function (e) {
				e.preventDefault();
				$('#complex_design_thumbnail').val('');
				$('#complex_design_thumbnail_preview').html('');
				$(this).hide();
			});
		});
	</script>
	<?php
}

// Форма редактирования - миниатюра
function edit_complex_design_thumbnail_field($tag)
{
	$thumbnail_id = get_term_meta($tag->term_id, 'thumbnail_id', true);
	$thumbnail_url = '';
	if ($thumbnail_id) {
		$thumbnail_url = wp_get_attachment_image_url($thumbnail_id, 'thumbnail');
	}
	?>
	<tr class="form-field">
		<th scope="row" valign="top"><label for="complex_design_thumbnail">Миниатюра</label></th>
		<td>
			<input type="hidden" id="complex_design_thumbnail" name="complex_design_thumbnail"
				value="<?php echo esc_attr($thumbnail_id); ?>" />
			<div id="complex_design_thumbnail_preview">
				<?php if ($thumbnail_url): ?>
					<img src="<?php echo esc_url($thumbnail_url); ?>" style="max-width: 150px;" />
				<?php endif; ?>
			</div>
			<button type="button" class="button complex-design-thumbnail-upload">Выбрать изображение</button>
			<button type="button" class="button complex-design-thumbnail-remove"
				style="<?php echo $thumbnail_url ? '' : 'display:none;'; ?>">Удалить изображение</button>
			<br />
			<span class="description">Миниатюра оформления.</span>
		</td>
	</tr>

	<script>
		jQuery(document).ready(function ($) {
			var mediaUploader;

			$('.complex-design-thumbnail-upload').click(function (e) {
				e.preventDefault();

				if (mediaUploader) {
					mediaUploader.open();
					return;
				}

				mediaUploader = wp.media({
					title: 'Выберите миниатюру',
					button: { text: 'Использовать это изображение' },
					multiple: false
				});

				mediaUploader.on('select', function () {
					var attachment = mediaUploader.state().get('selection').first().toJSON();
					$('#complex_design_thumbnail').val(attachment.id);
					$('#complex_design_thumbnail_preview').html('<img src="' + attachment.sizes.thumbnail.url + '" style="max-width: 150px;" />');
					$('.complex-design-thumbnail-remove').show();
				});

				mediaUploader.open();
			});

			$('.complex-design-thumbnail-remove').click(function (e) {
				e.preventDefault();
				$('#complex_design_thumbnail').val('');
				$('#complex_design_thumbnail_preview').html('');
				$(this).hide();
			});
		});
	</script>
	<?php
}

// Поле выбора товаров для формы создания
function add_complex_design_products_field($tag)
{
	?>
	<div class="form-field">
		<label for="complex_design_products">Связанные товары</label>
		<input type="text" id="product_search" placeholder="Поиск товара..." style="width: 100%; margin-bottom: 10px;" />
		<?php
		$products = get_posts(array(
			'post_type' => 'product',
			'posts_per_page' => -1,
			'orderby' => 'title',
			'order' => 'ASC',
		));

		if (!empty($products)) {
			echo '<div style="max-height: 200px; overflow-y: auto; border: 1px solid #ddd; padding: 10px;">';
			foreach ($products as $product) {
				echo '<label class="product-item" style="display: block; margin-bottom: 5px;">';
				echo '<input type="checkbox" name="complex_design_products[]" value="' . $product->ID . '" /> ';
				echo esc_html($product->post_title);
				echo '</label>';
			}
			echo '</div>';
		}
		?>
		<p>Выберите товары, которые относятся к этому оформлению.</p>
	</div>

	<script>
		jQuery(document).ready(function($) {
			$('#product_search').on('keyup', function() {
				var searchText = $(this).val().toLowerCase();
				$('.product-item').each(function() {
					var productName = $(this).text().toLowerCase();
					if (productName.indexOf(searchText) > -1) {
						$(this).show();
					} else {
						$(this).hide();
					}
				});
			});
		});
	</script>
	<?php
}

// Поле выбора товаров для формы редактирования
function edit_complex_design_products_field($tag)
{
	$selected_products = get_term_meta($tag->term_id, 'linked_products', true);
	$selected_products = is_array($selected_products) ? $selected_products : array();
	?>
	<tr class="form-field">
		<th scope="row" valign="top"><label for="complex_design_products">Связанные товары</label></th>
		<td>
			<input type="text" id="product_search" placeholder="Поиск товара..." style="width: 100%; margin-bottom: 10px;" />
			<?php
			$products = get_posts(array(
				'post_type' => 'product',
				'posts_per_page' => -1,
				'orderby' => 'title',
				'order' => 'ASC',
			));

			if (!empty($products)) {
				echo '<div style="max-height: 200px; overflow-y: auto; border: 1px solid #ddd; padding: 10px;">';
				foreach ($products as $product) {
					$checked = in_array($product->ID, $selected_products) ? 'checked="checked"' : '';
					echo '<label class="product-item" style="display: block; margin-bottom: 5px;">';
					echo '<input type="checkbox" name="complex_design_products[]" value="' . $product->ID . '" ' . $checked . ' /> ';
					echo esc_html($product->post_title);
					echo '</label>';
				}
				echo '</div>';
			}
			?>
			<br />
			<span class="description">Товары, связанные с этим оформлением.</span>
		</td>
	</tr>

	<script>
		jQuery(document).ready(function($) {
			$('#product_search').on('keyup', function() {
				var searchText = $(this).val().toLowerCase();
				$('.product-item').each(function() {
					var productName = $(this).text().toLowerCase();
					if (productName.indexOf(searchText) > -1) {
						$(this).show();
					} else {
						$(this).hide();
					}
				});
			});
		});
	</script>
	<?php
}

// Поле связанных категорий для формы создания
function add_complex_design_categories_field($tag)
{
	?>
	<div class="form-field">
		<label for="complex_design_categories">Связанные категории</label>
		<input type="text" id="category_search" placeholder="Поиск категории..." style="width: 100%; margin-bottom: 10px;" />
		<?php
		$categories = get_terms(array(
			'taxonomy' => 'product_cat',
			'hide_empty' => false,
		));

		if (!empty($categories) && !is_wp_error($categories)) {
			echo '<div style="max-height: 200px; overflow-y: auto; border: 1px solid #ddd; padding: 10px;">';
			foreach ($categories as $category) {
				echo '<label class="category-item" style="display: block; margin-bottom: 5px;">';
				echo '<input type="checkbox" name="complex_design_categories[]" value="' . $category->term_id . '" /> ';
				echo esc_html($category->name);
				echo '</label>';
			}
			echo '</div>';
		}
		?>
		<p>Выберите категории товаров, которые относятся к этому оформлению.</p>
	</div>

	<script>
		jQuery(document).ready(function($) {
			$('#category_search').on('keyup', function() {
				var searchText = $(this).val().toLowerCase();
				$('.category-item').each(function() {
					var categoryName = $(this).text().toLowerCase();
					if (categoryName.indexOf(searchText) > -1) {
						$(this).show();
					} else {
						$(this).hide();
					}
				});
			});
		});
	</script>
	<?php
}

// Поле связанных категорий для формы редактирования
function edit_complex_design_categories_field($tag)
{
	$selected_categories = get_term_meta($tag->term_id, 'linked_categories', true);
	$selected_categories = is_array($selected_categories) ? $selected_categories : array();
	?>
	<tr class="form-field">
		<th scope="row" valign="top"><label for="complex_design_categories">Связанные категории</label></th>
		<td>
			<input type="text" id="category_search" placeholder="Поиск категории..." style="width: 100%; margin-bottom: 10px;" />
			<?php
			$categories = get_terms(array(
				'taxonomy' => 'product_cat',
				'hide_empty' => false,
			));

			if (!empty($categories) && !is_wp_error($categories)) {
				echo '<div style="max-height: 200px; overflow-y: auto; border: 1px solid #ddd; padding: 10px;">';
				foreach ($categories as $category) {
					$checked = in_array($category->term_id, $selected_categories) ? 'checked="checked"' : '';
					echo '<label class="category-item" style="display: block; margin-bottom: 5px;">';
					echo '<input type="checkbox" name="complex_design_categories[]" value="' . $category->term_id . '" ' . $checked . ' /> ';
					echo esc_html($category->name);
					echo '</label>';
				}
				echo '</div>';
			}
			?>
			<br />
			<span class="description">Категории, связанные с этим оформлением.</span>
		</td>
	</tr>

	<script>
		jQuery(document).ready(function($) {
			$('#category_search').on('keyup', function() {
				var searchText = $(this).val().toLowerCase();
				$('.category-item').each(function() {
					var categoryName = $(this).text().toLowerCase();
					if (categoryName.indexOf(searchText) > -1) {
						$(this).show();
					} else {
						$(this).hide();
					}
				});
			});
		});
	</script>
	<?php
}

// Добавляем хуки
add_action('complex_design_add_form_fields', 'add_complex_design_thumbnail_field', 20);
add_action('complex_design_add_form_fields', 'add_complex_design_products_field', 30);
add_action('complex_design_add_form_fields', 'add_complex_design_categories_field', 40);

add_action('complex_design_edit_form_fields', 'edit_complex_design_thumbnail_field', 20);
add_action('complex_design_edit_form_fields', 'edit_complex_design_products_field', 30);
add_action('complex_design_edit_form_fields', 'edit_complex_design_categories_field', 40);



// Обновляем функцию сохранения
function save_complex_design_fields($term_id)
{
	// Сохраняем миниатюру
	if (isset($_POST['complex_design_thumbnail'])) {
		update_term_meta($term_id, 'thumbnail_id', sanitize_text_field($_POST['complex_design_thumbnail']));
	}

	// Сохраняем связанные категории
	if (isset($_POST['complex_design_categories'])) {
		$categories = array_map('intval', $_POST['complex_design_categories']);
		update_term_meta($term_id, 'linked_categories', $categories);
	} else {
		delete_term_meta($term_id, 'linked_categories');
	}

	// Сохраняем связанные товары
	if (isset($_POST['complex_design_products'])) {
		$products = array_map('intval', $_POST['complex_design_products']);
		update_term_meta($term_id, 'linked_products', $products);
	} else {
		delete_term_meta($term_id, 'linked_products');
	}
}

// Сохранение полей
add_action('edited_complex_design', 'save_complex_design_fields');
add_action('create_complex_design', 'save_complex_design_fields');


// Разрешаем HTML в описании таксономии
remove_filter('pre_term_description', 'wp_filter_kses');
remove_filter('term_description', 'wp_kses_data');



// Удаляем стандартное поле описания
add_action('admin_init', 'remove_complex_design_description_field');
function remove_complex_design_description_field() {
	remove_action('complex_design_edit_form_fields', 'taxonomy_metadata_wpdbfix_edit', 9);
}

// Скрываем стандартное описание через CSS
add_action('admin_head-term.php', 'hide_default_description_css');
add_action('admin_head-edit-tags.php', 'hide_default_description_css');
function hide_default_description_css() {
	global $current_screen;
	if ($current_screen && $current_screen->taxonomy == 'complex_design') {
		echo '<style>
			.term-description-wrap { display: none !important; }
			.term-description-wrap.custom-description-wrap { display: table-row !important; }
		</style>';
	}
}

// Добавляем свой визуальный редактор для редактирования
add_action('complex_design_edit_form_fields', 'add_complex_design_description_editor');
function add_complex_design_description_editor($term)
{
	?>
	<tr class="form-field term-description-wrap custom-description-wrap">
		<th scope="row"><label for="custom_description">Описание</label></th>
		<td>
			<?php
			$content = htmlspecialchars_decode($term->description, ENT_QUOTES);
			
			$settings = array(
				'textarea_name' => 'description',
				'textarea_rows' => 10,
				'wpautop' => true,
				'media_buttons' => false,
				'tinymce' => array(
					'toolbar1' => 'formatselect,bold,italic,underline,bullist,numlist,blockquote,alignleft,aligncenter,alignright,link,unlink,removeformat',
				),
				'quicktags' => true,
			);
			
			wp_editor($content, 'custom_description', $settings);
			?>
			<p class="description">Описание оформления с поддержкой форматирования.</p>
		</td>
	</tr>
	<script>
		jQuery(document).ready(function($) {
			// Копируем значение из визуального редактора в скрытое поле description перед отправкой формы
			$('#edittag').on('submit', function() {
				var editor_content = '';
				if (typeof tinyMCE !== 'undefined' && tinyMCE.get('custom_description')) {
					editor_content = tinyMCE.get('custom_description').getContent();
				} else {
					editor_content = $('#custom_description').val();
				}
				
				// Создаем скрытое поле с именем description для отправки
				$('<input>').attr({
					type: 'hidden',
					name: 'description',
					value: editor_content
				}).appendTo('#edittag');
			});
		});
	</script>
	<?php
}

// Для формы добавления нового термина
add_action('complex_design_add_form_fields', 'add_complex_design_description_editor_add');
function add_complex_design_description_editor_add()
{
	?>
	<div class="form-field term-description-wrap custom-description-wrap">
		<label for="custom_description">Описание</label>
		<?php
		$settings = array(
			'textarea_name' => 'description',
			'textarea_rows' => 10,
			'wpautop' => true,
			'media_buttons' => false,
			'tinymce' => array(
				'toolbar1' => 'formatselect,bold,italic,underline,bullist,numlist,blockquote,alignleft,aligncenter,alignright,link,unlink,removeformat',
			),
			'quicktags' => true,
		);
		
		wp_editor('', 'custom_description', $settings);
		?>
		<p class="description">Описание оформления с поддержкой форматирования.</p>
	</div>
	<script>
		jQuery(document).ready(function($) {
			// Копируем значение из визуального редактора в скрытое поле description перед отправкой формы
			$('#addtag').on('submit', function() {
				var editor_content = '';
				if (typeof tinyMCE !== 'undefined' && tinyMCE.get('custom_description')) {
					editor_content = tinyMCE.get('custom_description').getContent();
				} else {
					editor_content = $('#custom_description').val();
				}
				
				// Создаем скрытое поле с именем description для отправки
				$('<input>').attr({
					type: 'hidden',
					name: 'description',
					value: editor_content
				}).appendTo('#addtag');
			});
		});
	</script>
	<style>
		.term-description-wrap:not(.custom-description-wrap) { 
			display: none !important; 
		}
	</style>
	<?php
}



// Подключаем медиабиблиотеку в админке
add_action('admin_enqueue_scripts', 'enqueue_complex_design_admin_scripts');
function enqueue_complex_design_admin_scripts($hook)
{
	if ($hook == 'edit-tags.php' || $hook == 'term.php') {
		global $taxonomy;
		if ($taxonomy == 'complex_design') {
			wp_enqueue_media();
		}
	}
}

// Функции для получения данных
function get_complex_design_by_category($category_id)
{
	$designs = get_terms(array(
		'taxonomy' => 'complex_design',
		'hide_empty' => false,
	));

	$matched_designs = array();
	foreach ($designs as $design) {
		$linked_categories = get_term_meta($design->term_id, 'linked_categories', true);
		if (is_array($linked_categories) && in_array($category_id, $linked_categories)) {
			$matched_designs[] = $design;
		}
	}

	return $matched_designs;
}

function get_complex_design_thumbnail($term_id, $size = 'thumbnail')
{
	$thumbnail_id = get_term_meta($term_id, 'thumbnail_id', true);
	if ($thumbnail_id) {
		return wp_get_attachment_image_url($thumbnail_id, $size);
	}
	return false;
}

if (file_exists(get_template_directory() . '/inc/common-functions.php')) {
	require_once get_template_directory() . '/inc/common-functions.php';
}

// Подключаем функции для портфолио
if (file_exists(get_template_directory() . '/portfolio/functions.php')) {
	require_once get_template_directory() . '/portfolio/functions.php';
}

// Подключаем функции для новостей
if (file_exists(get_template_directory() . '/news/functions.php')) {
	require_once get_template_directory() . '/news/functions.php';
}

// Подключаем функции для статей
if (file_exists(get_template_directory() . '/articles/functions.php')) {
	require_once get_template_directory() . '/articles/functions.php';
}

// Подключаем функции для услуг
if (file_exists(get_template_directory() . '/services/functions.php')) {
	require_once get_template_directory() . '/services/functions.php';
}


// Подключаем кастомные поля для категорий WooCommerce
if (file_exists(get_template_directory() . '/woocommerce/woocommerce-category-fields.php')) {
	require_once get_template_directory() . '/woocommerce/woocommerce-category-fields.php';
}

//======================================================================
// Функция для вывода блока связанных категорий (можно использовать в любом месте)
//======================================================================

function render_related_categories_block($category_id, $title = 'А еще Вам может пригодиться', $background_class = 'bg-grey')
{
	$related_categories = get_related_categories($category_id);

	if (empty($related_categories)) {
		return; // Если нет связанных категорий, ничего не выводим
	}
	?>
	<!-- Блок связанных категорий -->
	<section class="section section-product-recoment <?php echo esc_attr($background_class); ?> box-shadow-main-img">
		<div class="container">
			<div class="section-title text-center">
				<h3><?php echo esc_html($title); ?></h3>
				<img src="<?php echo get_template_directory_uri(); ?>/assets/img/ico/points.svg" alt="Точки" class="img-fluid" />
			</div>

			<!-- Карточки связанных категорий -->
			<div class="row g-4">
				<?php foreach ($related_categories as $related_category): ?>
					<?php
					$category_photo_url = get_category_photo_url($related_category->term_id, 'medium');
					$category_link = get_term_link($related_category);


					// Fallback изображение если фото категории не установлено
					if (!$category_photo_url) {
						$category_photo_url = wc_placeholder_img_src(); // Заглушка от WooCommerce
					}
					?>
					<article class="col-12 col-md-6 col-lg-4">
						<a href="<?php echo esc_url($category_link); ?>" class="card bg-transparent">
							<div class="card">
								<div class="card-img-container">
									<img src="<?php echo esc_url($category_photo_url); ?>"
										alt="<?php echo esc_attr($related_category->name); ?>" class="img-fluid" />
								</div>
							</div>
							<div class="card-body text-center">
								<h5><?php echo esc_html($related_category->name); ?></h5>
							</div>
						</a>
					</article>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
	<?php
}


//======================================================================
// Вспомогательная функция для проверки наличия связанных категорий
//======================================================================

function has_related_categories($category_id)
{
	$related_categories = get_related_categories($category_id);
	return !empty($related_categories);
}

//======================================================================
// Функция для получения количества связанных категорий
//======================================================================

function get_related_categories_count($category_id)
{
	$related_categories = get_related_categories($category_id);
	return count($related_categories);
}

//======================================================================
// AJAX функция для предварительного просмотра связанных категорий в админке
//======================================================================

add_action('wp_ajax_preview_related_categories', 'ajax_preview_related_categories');

function ajax_preview_related_categories()
{
	// Проверяем права доступа
	if (!current_user_can('manage_product_terms')) {
		wp_die('Недостаточно прав доступа');
	}

	// Получаем данные из POST
	$category_ids = isset($_POST['category_ids']) ? array_map('intval', $_POST['category_ids']) : array();

	if (empty($category_ids)) {
		echo '<p>Выберите категории для предварительного просмотра</p>';
		wp_die();
	}

	// Ограничиваем до 6 категорий
	$category_ids = array_slice($category_ids, 0, 6);

	echo '<div class="related-categories-preview" style="display: flex; flex-wrap: wrap; gap: 10px; margin-top: 15px;">';

	foreach ($category_ids as $cat_id) {
		$category = get_term($cat_id, 'product_cat');
		if ($category && !is_wp_error($category)) {
			$photo_url = get_category_photo_url($cat_id, 'thumbnail');
			if (!$photo_url) {
				$photo_url = get_template_directory_uri() . '/assets/img/default-category.jpg';
			}

			echo '<div style="width: 80px; text-align: center; padding: 10px; border: 1px solid #ddd; border-radius: 4px; background: #f9f9f9;">';
			echo '<img src="' . esc_url($photo_url) . '" alt="' . esc_attr($category->name) . '" style="width: 60px; height: 40px; object-fit: cover; border-radius: 2px;">';
			echo '<div style="font-size: 11px; margin-top: 5px; line-height: 1.2;">' . esc_html($category->name) . '</div>';
			echo '</div>';
		}
	}

	echo '</div>';

	wp_die();
}

//======================================================================
// Добавляем JavaScript для предварительного просмотра в админке
//======================================================================

add_action('admin_footer-edit-tags.php', 'related_categories_preview_script');
add_action('admin_footer-term.php', 'related_categories_preview_script');

function related_categories_preview_script()
{
	global $taxnow;

	if ($taxnow !== 'product_cat') {
		return;
	}
	?>
	<script type="text/javascript">
		jQuery(document).ready(function ($) {
			// Добавляем кнопку предварительного просмотра
			if ($('#related_categories_container').length) {
				var previewButton = '<button type="button" id="preview-related-categories" class="button" style="margin-top: 10px;">Предварительный просмотр</button><div id="preview-container"></div>';
				$('#related_categories_container').append(previewButton);
				$('#preview-related-categories').on('click', function () {
					var selectedCategories = [];
					$('.related-category-checkbox:checked').each(function () {
						selectedCategories.push($(this).val());
					});
					if (selectedCategories.length === 0) {
						$('#preview-container').html('<p style="color: #666; font-style: italic;">Выберите категории для предварительного просмотра</p>');
						return;
					}
					$.post(ajaxurl, {
						action: 'preview_related_categories',
						category_ids: selectedCategories
					}, function (response) {
						$('#preview-container').html(response);
					});
				});
			}
		});
	</script>
	<?php
}





/**
 * Функции для работы с контактными данными
 */

// Получение основного телефона
function get_main_phone()
{
	$phones = get_field('company_phones', 'option');
	if (!$phones)
		return '';

	foreach ($phones as $phone) {
		if ($phone['phone_is_main']) {
			return $phone['phone_number'];
		}
	}

	// Если основной не найден, возвращаем первый
	return $phones[0]['phone_number'] ?? '';
}

// Получение URL иконки телефона с fallback
function get_phone_icon_url($phone_data)
{
	// Проверяем есть ли у телефона своя иконка
	if (isset($phone_data['phone_icon']) && $phone_data['phone_icon'] && isset($phone_data['phone_icon']['url'])) {
		return $phone_data['phone_icon']['url'];
	}

	// Fallback на общую иконку телефона
	return get_contact_icon_url('phone_icon', 'mobile-phone-ico.svg');
}

// Получение телефонов для страницы контактов
function get_contacts_phones()
{
	$phones = get_field('company_phones', 'option');
	if (!$phones)
		return [];

	$contact_phones = [];
	foreach ($phones as $phone) {
		if ($phone['phone_show_contacts']) {
			$contact_phones[] = $phone;
		}
	}

	return $contact_phones;
}

// Получение социальных сетей для подвала
function get_footer_social_networks()
{
	$social_networks = get_field('social_networks', 'option');
	if (!$social_networks)
		return [];

	$footer_socials = [];
	foreach ($social_networks as $social) {
		if ($social['show_in_footer']) {
			$footer_socials[] = $social;
		}
	}

	return $footer_socials;
}

// Получение социальных сетей для блоков

function get_block_social_networks()
{
	$social_networks = get_field('social_networks', 'option');
	$block_socials = array();

	if ($social_networks) {
		foreach ($social_networks as $social) {
			if (isset($social['show_in_blocks']) && $social['show_in_blocks']) {
				$block_socials[] = $social;
			}
		}
	}

	return $block_socials;
}


// Получение времени работы
function get_company_work_hours()
{
	return get_field('company_work_hours', 'option') ?: 'Пн-Пт: 10:00-18:00. Сб, Вс: Выходной';
}

// Получение названия компании
function get_company_name()
{
	return get_field('company_name', 'option') ?: 'ИП Авинников Евгений Максимович';
}

// Получение ИНН компании
function get_company_inn()
{
	return get_field('company_inn', 'option') ?: '450127005482';
}
// Получить социальные сети для страницы контактов
function get_contacts_social_networks()
{
	$social_networks = get_field('social_networks', 'option');
	$contacts_socials = array();

	if ($social_networks) {
		foreach ($social_networks as $social) {
			// Проверяем, включено ли отображение на странице контактов
			if (isset($social['show_in_contacts']) && $social['show_in_contacts']) {
				$contacts_socials[] = $social;
			}
		}
	}

	return $contacts_socials;
}



// Получение юридического адреса
function get_company_legal_address()
{
	return get_field('company_legal_address', 'option') ?: '141700, Россия, Московская обл., г. Долгопрудный, Лихачевский пр-кт, дом 76, корпус 1, квартира 76.';
}


















/**
 * Регистрация подвального меню
 */
function register_footer_menu()
{
	register_nav_menus(array(
		'footer_menu' => __('Подвальное меню', 'svetogor'),
	));
}
add_action('init', 'register_footer_menu');

/**
 * Кастомный Walker для подвального меню (без вложенности)
 */
class Footer_Menu_Walker extends Walker_Nav_Menu
{

	// Начало списка
	function start_lvl(&$output, $depth = 0, $args = null)
	{
		// Не выводим подменю для подвала
		return;
	}

	// Конец списка
	function end_lvl(&$output, $depth = 0, $args = null)
	{
		// Не выводим подменю для подвала
		return;
	}

	// Начало элемента списка
	function start_el(&$output, $item, $depth = 0, $args = null, $id = 0)
	{
		// Только элементы первого уровня для подвала
		if ($depth > 0) {
			return;
		}

		$indent = ($depth) ? str_repeat("\t", $depth) : '';

		$classes = empty($item->classes) ? array() : (array) $item->classes;
		$classes[] = 'nav-item';

		// Проверяем активность пункта меню
		if (
			in_array('current-menu-item', $classes) ||
			in_array('current_page_item', $classes) ||
			in_array('current-menu-ancestor', $classes)
		) {
			$classes[] = 'active';
		}

		$class_names = join(' ', apply_filters('nav_menu_css_class', array_filter($classes), $item, $args));
		$class_names = $class_names ? ' class="' . esc_attr($class_names) . '"' : '';

		$id = apply_filters('nav_menu_item_id', 'menu-item-' . $item->ID, $item, $args);
		$id = $id ? ' id="' . esc_attr($id) . '"' : '';

		$output .= $indent . '<li' . $id . $class_names . '>';

		$attributes = !empty($item->attr_title) ? ' title="' . esc_attr($item->attr_title) . '"' : '';
		$attributes .= !empty($item->target) ? ' target="' . esc_attr($item->target) . '"' : '';
		$attributes .= !empty($item->xfn) ? ' rel="' . esc_attr($item->xfn) . '"' : '';
		$attributes .= !empty($item->url) ? ' href="' . esc_attr($item->url) . '"' : '';

		// Определяем активный класс для ссылки
		$link_classes = 'nav-link';
		if (in_array('active', $classes)) {
			$link_classes .= ' active';
		}

		$item_output = isset($args->before) ? $args->before : '';
		$item_output .= '<a class="' . $link_classes . '"' . $attributes . '>';
		$item_output .= (isset($args->link_before) ? $args->link_before : '') . apply_filters('the_title', $item->title, $item->ID) . (isset($args->link_after) ? $args->link_after : '');
		$item_output .= '</a>';
		$item_output .= isset($args->after) ? $args->after : '';

		$output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
	}

	// Конец элемента списка
	function end_el(&$output, $item, $depth = 0, $args = null)
	{
		if ($depth > 0) {
			return;
		}
		$output .= "</li>\n";
	}
}

/**
 * Функция для вывода подвального меню
 */
function display_footer_menu()
{
	if (has_nav_menu('footer_menu')) {
		wp_nav_menu(array(
			'theme_location' => 'footer_menu',
			'menu_class' => 'nav footer-nav align-items-center',
			'container' => false,
			'walker' => new Footer_Menu_Walker(),
			'depth' => 1, // Только первый уровень
			'fallback_cb' => false
		));
	}
}

/**
 * Функция для добавления JavaScript разделителей в подвальное меню
 */
function add_footer_menu_separators_js()
{
	?>
	<script>
		document.addEventListener('DOMContentLoaded', function () {
			// Находим подвальное меню
			const footerMenu = document.querySelector('.footer-nav');
			if (!footerMenu) return;

			// Находим все элементы меню первого уровня (исключая уже существующие разделители)
			const menuItems = footerMenu.querySelectorAll('li.nav-item:not(.d-none)');
			if (menuItems.length <= 1) return;

			// Массив для отслеживания уже обработанных элементов
			const processedElements = new Set();

			// Проходим по всем элементам меню
			for (let i = 0; i < menuItems.length - 1; i++) {
				const currentItem = menuItems[i];

				// Пропускаем, если элемент уже обработан
				if (processedElements.has(currentItem)) continue;

				// Создаем элемент разделителя
				const separator = document.createElement('li');
				separator.className = 'nav-item d-none d-lg-inline';

				// Создаем изображение-разделитель
				const img = document.createElement('img');
				img.className = 'nav-link';
				img.src = '<?php echo get_template_directory_uri(); ?>/assets/img/ico/menu-decoration-point.svg';
				img.alt = 'Разделитель меню';

				// Добавляем изображение в разделитель
				separator.appendChild(img);

				// Вставляем разделитель после текущего элемента меню
				if (currentItem.nextSibling) {
					footerMenu.insertBefore(separator, currentItem.nextSibling);
				} else {
					footerMenu.appendChild(separator);
				}

				// Отмечаем элемент как обработанный
				processedElements.add(currentItem);
			}
		});
	</script>
	<?php
}
add_action('wp_footer', 'add_footer_menu_separators_js');

/**
 * Функция для мобильного подвального меню (две колонки)
 */
function display_footer_menu_mobile()
{
	if (has_nav_menu('footer_menu')) {
		$menu_items = wp_get_nav_menu_items(get_nav_menu_locations()['footer_menu']);

		if ($menu_items) {
			// Фильтруем только родительские элементы
			$parent_items = array_filter($menu_items, function ($item) {
				return $item->menu_item_parent == 0;
			});

			// Разделяем на две колонки
			$total_items = count($parent_items);
			$half = ceil($total_items / 2);

			$first_column = array_slice($parent_items, 0, $half);
			$second_column = array_slice($parent_items, $half);

			echo '<div class="row footer-menu footer-menu-mobile">';

			// Первая колонка
			echo '<div class="col-6"><ul class="nav flex-column">';
			foreach ($first_column as $item) {
				$active_class = (in_array('current-menu-item', $item->classes) ||
					in_array('current_page_item', $item->classes)) ? ' active' : '';
				echo '<li class="nav-item">';
				echo '<a class="nav-link ps-0' . $active_class . '" href="' . esc_url($item->url) . '">' . esc_html($item->title) . '</a>';
				echo '</li>';
			}
			echo '</ul></div>';

			// Вторая колонка
			echo '<div class="col-6"><ul class="nav flex-column">';
			foreach ($second_column as $item) {
				$active_class = (in_array('current-menu-item', $item->classes) ||
					in_array('current_page_item', $item->classes)) ? ' active' : '';
				echo '<li class="nav-item">';
				echo '<a class="nav-link ps-0' . $active_class . '" href="' . esc_url($item->url) . '">' . esc_html($item->title) . '</a>';
				echo '</li>';
			}
			echo '</ul></div>';

			echo '</div>';
		}
	}
}











require_once get_template_directory() . '/inc/navigation.php';
require_once get_template_directory() . '/inc/helpers.php';




$footer_socials = apply_filters('pre_get_footer_social_networks', false);

if (!$footer_socials) {
    $footer_socials = get_footer_social_networks();
}

global $wc_archive_social_networks;
if (empty($footer_socials) && !empty($wc_archive_social_networks)) {
    $footer_socials = $wc_archive_social_networks;
}

// Инициализация сессий в WordPress
function init_custom_session() {
    if (!session_id()) {
        session_start();
    }
}
add_action('init', 'init_custom_session');
?>
