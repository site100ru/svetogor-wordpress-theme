<?php
/**
 * Регистрация и настройка ACF блоков
 * 
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Конфигурация всех ACF блоков
 */
function get_acf_blocks_config() {
    return array(
        'carousel-main' => array(
            'title' => 'Главная карусель',
            'description' => 'Блок карусели с настраиваемыми слайдами',
            'icon' => 'slides',
            'keywords' => array('carousel', 'slider', 'карусель', 'слайдер'),
        ),
        
        'general-info' => array(
            'title' => 'Раскрывающий текст',
            'description' => 'Блок с общей информацией и дополнительным текстом',
            'icon' => 'info',
            'keywords' => array('info', 'information', 'информация', 'текст'),
            'template_path' => 'template-parts/blocks/general-info/general-info.php',
        ),
        
        'how-to-order' => array(
            'title' => 'Как заказать',
            'description' => 'Блок с пошаговым процессом заказа',
            'icon' => 'list-view',
            'keywords' => array('steps', 'process', 'order', 'заказ', 'шаги'),
        ),
        
        'about-us' => array(
            'title' => 'О нас',
            'description' => 'Блок с информацией о компании и фоновым изображением',
            'icon' => 'groups',
            'keywords' => array('about', 'company', 'о нас', 'компания'),
        ),
        
        'section-advantages' => array(
            'title' => 'Секция с преимуществами',
            'description' => 'Блок для отображения преимуществ компании в колонках с иконками',
            'icon' => 'star-filled',
            'keywords' => array('преимущества', 'услуги', 'особенности'),
            'mode' => 'preview',
        ),
        
        'content-with-image' => array(
            'title' => 'Контент с изображением',
            'description' => 'Блок с текстом и изображением в колонках',
            'icon' => 'align-pull-left',
            'keywords' => array('content', 'image', 'text', 'контент', 'изображение', 'текст'),
        ),
        
        'text-only' => array(
            'title' => 'Только текст',
            'description' => 'Блок только с текстовым контентом',
            'icon' => 'editor-alignleft',
            'keywords' => array('text', 'content', 'текст', 'контент'),
        ),
        
        'clients-slider' => array(
            'title' => 'Слайдер клиентов',
            'description' => 'Блок для отображения слайдера с логотипами клиентов',
            'icon' => 'groups',
            'keywords' => array('clients', 'slider', 'клиенты', 'слайдер', 'логотипы'),
            'template_path' => 'template-parts/blocks/clients-slider/clients-slider.php',
        ),
        
        'complex-design-slider' => array(
            'title' => 'Слайдер комплексного оформления',
            'description' => 'Блок слайдера с терминами комплексного оформления',
            'icon' => 'slides',
            'keywords' => array('slider', 'complex', 'design', 'слайдер', 'оформление'),
        ),
        
        'faq' => array(
            'title' => 'FAQ',
            'description' => 'Блок частых вопросов с аккордеоном',
            'icon' => 'editor-help',
            'keywords' => array('faq', 'вопросы', 'аккордеон', 'accordion'),
            'template_path' => 'template-parts/blocks/faq/faq.php',
        ),
        
        'gallery' => array(
            'title' => 'Галерея изображений',
            'description' => 'Блок галереи изображений с настройками',
            'icon' => 'format-gallery',
            'keywords' => array('gallery', 'галерея', 'изображения', 'фото'),
            'template_path' => 'template-parts/blocks/gallery/gallery.php',
        ),
        
        'product-categories' => array(
            'title' => 'Категории товаров',
            'description' => 'Блок для отображения категорий товаров с настройками',
            'icon' => 'products',
            'keywords' => array('product', 'categories', 'товары', 'категории', 'продукция'),
            'template_path' => 'template-parts/blocks/product-categories/product-categories.php',
        ),
        
        'not-found-product' => array(
            'title' => 'Не нашли нужного товара?',
            'description' => 'Блок "Не нашли нужного товара?" с данными из настроек сайта',
            'icon' => 'search',
            'keywords' => array('не нашли', 'товар', 'консультация', 'заявка'),
            'template_path' => 'template-parts/blocks/not-found-product/not-found-product.php',
            'example' => array(
                'attributes' => array(
                    'mode' => 'preview',
                    'data' => array(
                        'nfp_block_background_color_unique' => 'bg-grey',
                    )
                )
            )
        ),
        
        'contacts' => array(
            'title' => 'Контакты',
            'description' => 'Блок для отображения контактной информации компании',
            'icon' => 'phone',
            'keywords' => array('contacts', 'phone', 'address', 'email', 'контакты', 'телефон', 'адрес', 'почта'),
            'template_path' => 'template-parts/blocks/contacts/contacts.php',
        ),
        
        'yandex-map' => array(
            'title' => 'Яндекс Карта',
            'description' => 'Блок с Яндекс Картой и маркером местоположения',
            'icon' => 'location-alt',
            'keywords' => array('карта', 'яндекс', 'местоположение', 'map'),
            'template_path' => 'template-parts/blocks/yandex-map/yandex-map.php',
            'mode' => 'preview',
        ),
        
        'simple-contact-form' => array(
            'title' => 'Простая форма обратной связи',
            'description' => 'Простая форма с основными полями и чекбоксами с картинками',
            'icon' => 'email',
            'keywords' => array('form', 'contact', 'форма', 'обратная связь', 'заявка'),
            'template_path' => 'template-parts/blocks/forms/form.php',
        ),
        
        'extended-contact-form' => array(
            'title' => 'Расширенная форма обратной связи',
            'description' => 'Расширенная форма с типами продукции и дополнительными услугами',
            'icon' => 'feedback',
            'keywords' => array('form', 'contact', 'extended', 'форма', 'расширенная', 'продукция', 'услуги'),
            'template_path' => 'template-parts/blocks/forms/extended-form.php',
        ),
        
        'woocommerce-category-products' => array(
            'title' => 'Категория товаров WooCommerce',
            'description' => 'Блок для вывода товаров из выбранной категории WooCommerce',
            'icon' => 'products',
            'keywords' => array('woocommerce', 'категория', 'товары', 'продукты', 'магазин'),
            'template_path' => 'template-parts/blocks/woocommerce-category-products/woocommerce-category-products.php',
            'align' => false,
            'jsx' => true,
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
        ),
        
        'breadcrumbs-header' => array(
            'title' => 'Хлебные крошки / Заголовок',
            'description' => 'Блок с хлебными крошками и заголовком страницы',
            'icon' => 'admin-links',
            'keywords' => array('хлебные крошки', 'заголовок', 'навигация', 'breadcrumbs'),
            'template_path' => 'template-parts/blocks/breadcrumbs-header/breadcrumbs-header.php',
            'align' => false,
            'jsx' => true,
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
        ),
        
        'portfolio-slider' => array(
            'title' => 'Слайдер портфолио',
            'description' => 'Блок для отображения слайдера работ портфолио',
            'icon' => 'images-alt2',
            'keywords' => array('portfolio', 'slider', 'портфолио', 'слайдер'),
            'template_path' => 'template-parts/blocks/portfolio-slider/portfolio-slider.php',
            'jsx' => true,
        ),
        
        'portfolio-grid' => array(
            'title' => 'Портфолио сетка',
            'description' => 'Блок для отображения работ портфолио в виде сетки',
            'icon' => 'grid-view',
            'keywords' => array('portfolio', 'grid', 'портфолио', 'сетка'),
            'template_path' => 'template-parts/blocks/portfolio-grid/portfolio-grid.php',
            'jsx' => true,
        ),
    );
}

/**
 * Регистрация всех ACF блоков через конфиг
 */
function register_all_acf_blocks() {
    if (!is_acf_available()) {
        return;
    }
    
    $blocks = get_acf_blocks_config();
    
    foreach ($blocks as $slug => $config) {
        // Формируем путь к шаблону
        $template_path = isset($config['template_path']) 
            ? $config['template_path'] 
            : "template-parts/blocks/{$slug}.php";
        
        // Базовые параметры блока
        $block_args = array(
            'name' => $slug,
            'title' => $config['title'],
            'description' => $config['description'],
            'render_template' => get_template_directory() . '/' . $template_path,
            'category' => 'custom-blocks',
            'icon' => $config['icon'],
            'keywords' => $config['keywords'],
            'supports' => array(
                'align' => isset($config['align']) ? $config['align'] : array('wide', 'full'),
                'anchor' => true,
                'customClassName' => true,
                'mode' => isset($config['mode']) ? $config['mode'] : true,
                'jsx' => isset($config['jsx']) ? $config['jsx'] : false,
            ),
        );
        
        // Добавляем example если задан
        if (isset($config['example'])) {
            $block_args['example'] = $config['example'];
        }
        
        acf_register_block_type($block_args);
    }
}
add_action('acf/init', 'register_all_acf_blocks');

/**
 * Добавление страницы настроек с вкладками
 */
function add_carousel_options_page() {
    if (!is_acf_available()) {
        return;
    }
    
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
add_action('acf/init', 'add_carousel_options_page');

/**
 * Условная загрузка скриптов для блоков
 */
function enqueue_block_specific_assets() {
    // Portfolio Slider
    if (safe_has_block('acf/portfolio-slider')) {
        wp_enqueue_script('portfolio-slider-js', get_template_directory_uri() . '/template-parts/blocks/portfolio-slider/portfolio-slider.js', array('jquery'), filemtime(get_template_directory() . '/template-parts/blocks/portfolio-slider/portfolio-slider.js'), true);
        
        }
    
    // Portfolio Grid
    if (safe_has_block('acf/portfolio-grid')) {
        wp_enqueue_script('portfolio-grid-js', get_template_directory_uri() . '/template-parts/blocks/portfolio-grid/portfolio-grid.js', array('jquery'), filemtime(get_template_directory() . '/template-parts/blocks/portfolio-grid/portfolio-grid.js'), true);
        
        }
    
    // Glide.js для блока партнеров (если есть)
    if (safe_has_block('acf/section-partners')) {
        wp_enqueue_script('portfolio-slider', get_template_directory_uri() . '/assets/js/portfolio-single.js', array('glide-js'), '', true);

    }
}
add_action('wp_enqueue_scripts', 'enqueue_block_specific_assets');

/**
 * Фильтр для обертки стандартных блоков контейнером на страницах с хлебными крошками
 */
function wrap_standard_blocks_with_container_pages($block_content, $block) {
    // Пропускаем если не используем шаблон с хлебными крошками
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
add_filter('render_block', 'wrap_standard_blocks_with_container_pages', 10, 2);