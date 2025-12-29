<?php
/**
 * Базовые настройки WooCommerce
 * 
 * @package svetogor
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Отключаем ненужные 
 */
function disable_woocommerce_scripts() {
    // Отключаем на всех страницах кроме админки
    if (!is_admin()) {
        wp_dequeue_script('wc-add-to-cart');
        wp_dequeue_script('woocommerce');
        wp_dequeue_script('jquery-blockui');
        wp_dequeue_script('js-cookie');
        wp_dequeue_script('wc-order-attribution');
        wp_dequeue_script('sourcebuster-js-js');
        
        // Стили WooCommerce
        wp_dequeue_style('woocommerce-layout');
        wp_dequeue_style('woocommerce-smallscreen');
        wp_dequeue_style('woocommerce-general');
        wp_dequeue_style('wc-blocks-style');
    }
}
add_action('wp_enqueue_scripts', 'disable_woocommerce_scripts', 100);

/**
 * Добавление поддержки WooCommerce
 */
function woocommerce_support() {
    add_theme_support('woocommerce');
    add_theme_support('wc-product-gallery-zoom');
    add_theme_support('wc-product-gallery-lightbox');
    add_theme_support('wc-product-gallery-slider');
}
add_action('after_setup_theme', 'woocommerce_support');

/**
 * Убираем стандартные стили WooCommerce (опционально)
 */
add_filter('woocommerce_enqueue_styles', '__return_empty_array');

/**
 * Убираем стандартные элементы из страницы товара
 */
function remove_woocommerce_single_product_elements() {
    remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_title', 5);
    remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10);
    remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_price', 10);
    remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20);
    remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30);
    remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40);
    remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 50);
}
add_action('init', 'remove_woocommerce_single_product_elements');

/**
 * Добавляем кастомные элементы
 */
function add_custom_single_product_elements() {
    add_action('woocommerce_single_product_summary', 'custom_single_product_price', 10);
    add_action('woocommerce_single_product_summary', 'custom_single_product_button', 20);
}
add_action('init', 'add_custom_single_product_elements');

/**
 * Удаляем связанные товары
 */
function remove_wc_related_products() {
    remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20);
    remove_action('woocommerce_single_product_summary', 'woocommerce_output_related_products', 25);
}
add_action('init', 'remove_wc_related_products');

/**
 * Кастомная цена товара
 */
function custom_single_product_price() {
    global $product;

    if ($product->get_price()) {
        echo '<p class="mb-0" style="font-weight: 500">';
        echo 'Стоимость: <strong class="price-text">' . $product->get_price_html() . '</strong>';
        echo '</p>';
    }
}

/**
 * Кастомная кнопка заказа
 */
function custom_single_product_button() {
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

/**
 * Изменяем бейдж "Распродажа!" на "Товар со скидкой"
 */
function custom_sale_flash($text, $post, $product) {
    return '<span class="onsale">Товар со скидкой</span>';
}
add_filter('woocommerce_sale_flash', 'custom_sale_flash', 10, 3);

/**
 * Изменяем порядок отображения цены (сначала цена со скидкой, потом обычная)
 */
function custom_price_html($price, $product) {
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
add_filter('woocommerce_get_price_html', 'custom_price_html', 10, 2);

/**
 * Кастомные табы товара
 */
function custom_product_tabs($tabs) {
    global $product;

    // Убираем стандартные табы
    unset($tabs['reviews']);
    unset($tabs['additional_information']);

    // Добавляем таб описания
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
add_filter('woocommerce_product_tabs', 'custom_product_tabs');

/**
 * Убираем ненужные мета-боксы из админки товара
 */
function remove_product_meta_boxes() {
    remove_meta_box('commentstatusdiv', 'product', 'normal');  // Отзывы
    remove_meta_box('commentsdiv', 'product', 'normal');   // Комментарии
    remove_meta_box('product_tag', 'product', 'side');  // Метки товаров
}
add_action('add_meta_boxes', 'remove_product_meta_boxes', 99);

/**
 * Ограничиваем возможности редактора для описания товара
 */
function limit_product_editor_settings($settings, $editor_id) {

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
add_filter('wp_editor_settings', 'limit_product_editor_settings', 10, 2);

/**
 * Убираем кнопки медиа для описания товара
 */
function disable_media_buttons_for_products($wp_rich_edit) {
    global $post_type;
    if ($post_type == 'product') {
        return false;
    }
    return $wp_rich_edit;
}
add_filter('user_can_richedit', 'disable_media_buttons_for_products');

/**
 * Убираем вкладку отзывов из админки товара
 */
function remove_product_data_tabs($tabs) {
    unset($tabs['reviews']);
    return $tabs;
}
add_filter('woocommerce_product_data_tabs', 'remove_product_data_tabs');

/**
 * Подключение разных шаблонов для архивных страниц
 */
function custom_archive_product_template($template) {
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
add_filter('archive_template', 'custom_archive_product_template');

/**
 * Подключение разных шаблонов для одиночных товаров
 */
function custom_single_product_template($template) {
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
                    break;  // shop имеет высший приоритет
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
add_filter('single_template', 'custom_single_product_template');