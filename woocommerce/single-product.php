<?php

/**
 * The Template for displaying all single products
 */
defined('ABSPATH') || exit;
get_header('shop'); ?>
<h1 class="d-none"><?php the_title(); ?></h1>
<?php while (have_posts()): ?>
    <?php the_post(); ?>

    <!-- ХЛЕБНЫЕ КРОШКИ -->
    <section class="section-mini">
        <h2 class=d-none>Секция навигации по сайту</h2>
        <div class="container">
            <nav aria-label="breadcrumb" class="mb-0">
                <ol class="breadcrumb bg-transparent p-0 m-0">
                    <!-- Иконка главной -->
                    <li class="breadcrumb-item">
                        <a href="<?php echo home_url('/'); ?>">
                            <img loading="lazy" src="<?php echo get_template_directory_uri(); ?>/assets/img/ico/breadcrumbs.svg" alt="Изображение домика">
                        </a>
                    </li>
                    <?php
                    global $product;
                    
                    // Получаем все категории товара
                    $terms = wp_get_post_terms($product->get_id(), 'product_cat');
                    
                    if (!empty($terms)) {
                        $main_category = $terms[0];
                        
                        // Собираем всю иерархию категорий
                        $category_hierarchy = array();
                        $current_cat = $main_category;
                        
                        while ($current_cat) {
                            array_unshift($category_hierarchy, $current_cat);
                            $current_cat = $current_cat->parent ? get_term($current_cat->parent, 'product_cat') : false;
                        }
                        
                        // Выводим все категории
                        foreach ($category_hierarchy as $category) {
                            echo '<li class="breadcrumb-item"><a href="' . get_term_link($category) . '">' . esc_html($category->name) . '</a></li>';
                        }
                    }
                    ?>
                    <!-- Название товара -->
                    <li class="breadcrumb-item active" aria-current="page">
                        <?php the_title(); ?>
                    </li>
                </ol>
            </nav>
        </div>
    </section>
    <?php
    /**
     * Hook: woocommerce_before_single_product.
     */
    do_action('woocommerce_before_single_product');

    if (post_password_required()) {
        echo get_the_password_form(); // WPCS: XSS ok.
        return;
    }
    ?>

    <div id="product-<?php the_ID(); ?>" <?php wc_product_class('', $product); ?>>

        <!-- Продукт -->
        <section class="section product-section">
            <div class="container">
                <div class="row justify-content-center">
                    <!-- Изображения товара -->
                    <div class="col-12 col-lg-8 mb-4 mb-lg-0 section-image">
                        <?php
                        /**
                         * Hook: woocommerce_before_single_product_summary.
                         */
                        do_action('woocommerce_before_single_product_summary');
                        ?>
                    </div>

                    <!-- Информация о товаре -->
                    <div class="col-12 col-lg-4 product-descriprion">
                        <div class="section-title mb-0">
                            <h2 class="product_title"><?php the_title(); ?></h2>
                            <img loading="lazy" src="<?php echo get_template_directory_uri(); ?>/assets/img/ico/points.svg" alt="Точки" class="img-fluid">
                        </div>

                        <?php
                        /**
                         * Hook: woocommerce_single_product_summary.
                         * Здесь выводятся только цена и кнопка (настроено в functions.php)
                         */
                        do_action('woocommerce_single_product_summary');
                        ?>
                    </div>
                </div>
            </div>
        </section>

        <!-- Табы с информацией -->
        <section class="section pt-0">
            <div class="container">
                <?php
                /**
                 * Hook: woocommerce_after_single_product_summary.
                 */
                do_action('woocommerce_after_single_product_summary');
                ?>
            </div>
        </section>

    </div>
    <?php do_action('woocommerce_after_single_product'); ?>

<?php endwhile; // end of the loop. 
?>

<?php
// Выводим портфолио блок для товара
if (function_exists('render_product_portfolio')) {
    render_product_portfolio(get_the_ID(), 'bg-grey');
}
?>

<?php
// Блок "Как заказать" 
global $block, $temp_how_to_order_data;

// Создаем переменную $block как в Gutenberg
$block = array(
    'id' => uniqid('how-to-order-'),
    'className' => ''
);

// Имитируем ACF поля
$temp_how_to_order_data = array(
    'background_color' => 'white',
    'columns' => '4',
    'section_title' => 'Как заказать'
);

// Добавляем фильтр для подмены ACF данных
add_filter('acf/load_value', 'temp_how_to_order_acf_filter_inline', 10, 3);

// Подключаем шаблон
$template_path = get_template_directory() . '/template-parts/blocks/how-to-order.php';
if (file_exists($template_path)) {
    include $template_path;
}

// Очищаем данные
$temp_how_to_order_data = null;
$block = null;
remove_filter('acf/load_value', 'temp_how_to_order_acf_filter_inline', 10);

/**
 * Фильтр для подмены ACF данных блока "Как заказать"
 */
function temp_how_to_order_acf_filter_inline($value, $post_id, $field)
{
    global $temp_how_to_order_data;

    if ($temp_how_to_order_data && isset($field['name'])) {
        switch ($field['name']) {
            case 'background_color':
                return $temp_how_to_order_data['background_color'];
            case 'columns':
                return $temp_how_to_order_data['columns'];
            case 'section_title':
                return $temp_how_to_order_data['section_title'];
        }
    }

    return $value;
}
?>


<?php
// Прямая передача фона через глобальную переменную
global $how_custom_background_color;
$how_custom_background_color = 'bg-grey-900'; // Устанавливаем нужный фон

// Подключаем шаблон
get_template_part('template-parts/blocks/not-found-product/not-found-product');

// Очищаем переменную
$how_custom_background_color = null;
?>


<?php
// Выводим кросселы WooCommerce
if (function_exists('render_woocommerce_crosssells')) {
    render_woocommerce_crosssells(get_the_ID(), 6, "white");
}
?>

<?php
if (function_exists('render_product_expanding_text')) {
    render_product_expanding_text(get_the_ID());
}

// Выводим FAQ блок для товара используя ваш ACF блок
if (function_exists('render_product_faq')) {
    render_product_faq(get_the_ID(), 'grey', 10);
}
?>

<?php
get_footer();
?>