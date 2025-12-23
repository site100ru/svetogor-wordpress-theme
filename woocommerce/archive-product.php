<?php

/**
 * Шаблон архива товаров / категории товаров с подкатегориями
 */

defined('ABSPATH') || exit;

get_header('shop');

$current_category = null;
$is_category = false;
$category_id = null;

if (is_product_category()) {
    $current_category = get_queried_object();
    $is_category = true;
    $category_id = $current_category->term_id;
} elseif (is_tax('product_cat')) {
    $current_category = get_queried_object();
    $is_category = true;
    $category_id = $current_category->term_id;
}

// Получаем иконки из настроек
$prev_arrow = get_field('carousel_prev_arrow', 'option');
$next_arrow = get_field('carousel_next_arrow', 'option');

// Получаем подкатегории для текущей категории
$subcategories = array();
if ($is_category && $current_category) {
    $subcategories = get_terms(array(
        'taxonomy' => 'product_cat',
        'child_of' => $current_category->term_id,
        'hide_empty' => true,
    ));
}

// Принудительно подключаем стили и скрипты
/**
 * Функция для вывода карточки товара
 */
function render_product_card($product)
{
?>
    <a href="<?php echo esc_url(get_permalink($product->get_id())); ?>" class="card card-hover-images">
        <div class="product-image-hover position-relative">
            <?php
            // Основное изображение товара
            $image_id = $product->get_image_id();
            if ($image_id) {
                echo wp_get_attachment_image($image_id, 'medium', false, array(
                    'class' => 'card-img-top product-image-main',
                    'alt' => $product->get_name()
                ));
            } else {
                echo '<img loading="lazy" src="' . wc_placeholder_img_src() . '" alt="' . esc_attr($product->get_name()) . '" class="card-img-top product-image-main" >';
            }

            // Второе изображение из галереи
            $gallery_image_ids = $product->get_gallery_image_ids();
            if (!empty($gallery_image_ids)) {
                $second_image_id = $gallery_image_ids[0];
                echo wp_get_attachment_image($second_image_id, 'medium', false, array(
                    'class' => 'card-img-top product-image-hover-second',
                    'alt' => $product->get_name()
                ));
            }
            ?>
        </div>
        <div class="card-body">
            <h5 class="card-title"><?php echo esc_html($product->get_name()); ?></h5>
            <p class="card-text">
                <?php
                $description = $product->get_description();
                if ($description) {
                    echo trim_html_words($description, 15);
                } else {
                    echo 'Краткое описание товара ' . esc_html($product->get_name());
                }
                ?>
            </p>
            <div class="d-flex justify-content-between align-items-center mt-auto">
                <span class="product-price"><?php echo $product->get_price_html(); ?></span>
                <button type="button" class="btn btn-order btn-min" data-bs-toggle="modal"
                    data-bs-target="#callbackModalFour" data-product-id="<?php echo get_the_ID(); ?>"
                    data-product-name="<?php echo esc_attr(get_the_title()); ?>"
                    onclick="event.preventDefault(); event.stopPropagation();">
                    Заказать
                </button>
            </div>
        </div>
    </a>
<?php
}

function trim_html_words($text, $num_words = 15, $more = '...')
{
    if (empty($text)) {
        return '';
    }

    $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');

    $words_array = preg_split('/(\s+)/', strip_tags($text, '<br><strong><em><b><i>'), -1, PREG_SPLIT_DELIM_CAPTURE);
    $words_count = 0;
    $result = '';

    foreach ($words_array as $word) {
        if (trim($word) === '') {
            $result .= $word;
        } else {
            if ($words_count >= $num_words) {
                break;
            }
            $result .= $word;
            $words_count++;
        }
    }

    if ($words_count >= $num_words) {
        $result .= $more;
    }

    return $result;
}

/**
 * Функция для вывода секции с товарами
 */
function render_products_section($products_query, $slider_id, $section_title = '', $section_id = '')
{
    if (!$products_query->have_posts()) {
        return false;
    }
?>
    <section class="section section-glide section-catalog-product box-shadow-main" <?php echo $section_id ? 'id="' . esc_attr($section_id) . '"' : ''; ?>>
        <div class="container">
            <div class="section-content-cards">
                <?php if ($section_title): ?>
                    <div class="section-title text-center">
                        <h3><?php echo esc_html($section_title); ?></h3>
                        <img loading="lazy" src="<?php echo get_template_directory_uri(); ?>/assets/img/ico/points.svg" alt="Точки" class="img-fluid">
                    </div>
                <?php endif; ?>

                <!-- Десктоп версия -->
                <div class="row justify-content-center d-none d-lg-flex">
                    <?php
                    while ($products_query->have_posts()):
                        $products_query->the_post();
                        global $product;
                    ?>
                        <article class="col-lg-4">
                            <?php render_product_card($product); ?>
                        </article>
                    <?php endwhile; ?>
                </div>

                <!-- Слайдер для планшетов и мобильных -->
                <div class="d-block d-lg-none">
                    <div class="products-glide light-letters-slider" id="<?php echo esc_attr($slider_id); ?>">
                        <div class="glide__track" data-glide-el="track">
                            <div class="glide__slides">
                                <?php
                                $products_query->rewind_posts();
                                while ($products_query->have_posts()):
                                    $products_query->the_post();
                                    global $product;
                                ?>
                                    <article class="glide__slide">
                                        <div class="card-img-container product-image-hover position-relative">
                                            <?php render_product_card($product); ?>
                                        </div>
                                    </article>
                                <?php endwhile; ?>
                            </div>
                        </div>

                        <!-- Стрелки навигации -->
                        <?php render_slider_arrows(); ?>
                    </div>
                </div>
            </div>
        </div>
    </section>


<?php
    return true;
}

/**
 * Функция для вывода стрелок слайдера
 */
function render_slider_arrows()
{
    global $prev_arrow, $next_arrow;
?>
    <div class="glide__arrows" data-glide-el="controls">
        <button class="glide__arrow glide__arrow--left" data-glide-dir="<">
            <?php if ($prev_arrow): ?>
                <img loading="lazy" src="<?php echo esc_url($prev_arrow['url']); ?>" alt="Назад">
            <?php else: ?>
                <img loading="lazy" src="<?php echo get_template_directory_uri(); ?>/assets/img/ico/arrow-left.svg" alt="Назад">
            <?php endif; ?>
        </button>
        <button class="glide__arrow glide__arrow--right" data-glide-dir=">">
            <?php if ($next_arrow): ?>
                <img loading="lazy" src="<?php echo esc_url($next_arrow['url']); ?>" alt="Вперед">
            <?php else: ?>
                <img loading="lazy" src="<?php echo get_template_directory_uri(); ?>/assets/img/ico/arrow-right.svg" alt="Вперед">
            <?php endif; ?>
        </button>
    </div>
<?php
}

/**
 * Функция для вывода заглушки "товары скоро появятся"
 */
function render_no_products_placeholder($category_name = '')
{
?>
    <section class="section section-catalog-product box-shadow-main">
        <div class="container">
            <div class="section-content-cards">
                <div class="text-center py-5">
                    <div class="no-products-placeholder" style="padding: 60px 20px;">
                        <h3 style="color: #666; margin-bottom: 15px;">
                            <?php if ($category_name): ?>
                                Товары в категории "<?php echo esc_html($category_name); ?>" скоро появятся
                            <?php else: ?>
                                Товары скоро появятся
                            <?php endif; ?>
                        </h3>
                        <p style="color: #999; font-size: 1.1rem; margin-bottom: 30px;">
                            Мы работаем над наполнением каталога. Следите за обновлениями!
                        </p>
                        <a href="<?php echo home_url('/product_cat/products/'); ?>" class="btn">
                            Перейти к продукции
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php
}
?>

<!-- ХЛЕБНЫЕ КРОШКИ/ ЗАГОЛОВОК -->
<section class="section-mini">
    <div class="container">
        <!-- Хлебные крошки -->
        <nav aria-label="breadcrumb" class="mb-0">
            <ol class="breadcrumb bg-transparent p-0 m-0">
                <!-- Иконка главной -->
                <li class="breadcrumb-item">
                    <a href="<?php echo home_url('/'); ?>">
                        <img loading="lazy" src="<?php echo get_template_directory_uri(); ?>/assets/img/ico/breadcrumbs.svg" alt="Изображение домика"/>
                    </a>
                </li>

                <?php if ($is_category && $current_category): ?>
                    <?php
                    // Получаем категорию первого уровня
                    $top_category = $current_category;
                    if ($current_category->parent) {
                        // Если есть родитель, ищем самый верхний уровень
                        while ($top_category->parent) {
                            $top_category = get_term($top_category->parent, 'product_cat');
                        }
                    }
                    ?>

                    <?php if ($current_category->parent): ?>
                        <!-- Есть родитель - показываем категорию 1 уровня и текущую -->
                        <li class="breadcrumb-item">
                            <a href="<?php echo get_term_link($top_category); ?>">
                                <?php echo esc_html($top_category->name); ?>
                            </a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">
                            <?php echo esc_html($current_category->name); ?>
                        </li>
                    <?php else: ?>
                        <!-- Нет родителя - это категория первого уровня, показываем только её -->
                        <li class="breadcrumb-item active" aria-current="page">
                            <?php echo esc_html($current_category->name); ?>
                        </li>
                    <?php endif; ?>
                <?php else: ?>
                    <!-- Если это не категория, выводим стандартный заголовок -->
                    <li class="breadcrumb-item active" aria-current="page">
                        <?php woocommerce_page_title(); ?>
                    </li>
                <?php endif; ?>
            </ol>
        </nav>
        <h1 class="text-center mb-0 section-mini-title section-mini-title-min">
            <?php
            if ($is_category && $current_category) {
                echo esc_html($current_category->name);
            } else {
                woocommerce_page_title();
            }
            ?>
        </h1>
    </div>
</section>

<?php if ($is_category && $current_category && !empty($subcategories)): ?>
    <!-- Есть подкатегории - выводим товары по подкатегориям -->
    <?php
    $has_products = false;
    foreach ($subcategories as $index => $subcategory):
        // Получаем товары для этой подкатегории
        $products_query = new WP_Query(array(
            'post_type' => 'product',
            'posts_per_page' => -1,
            'tax_query' => array(
                array(
                    'taxonomy' => 'product_cat',
                    'field' => 'term_id',
                    'terms' => $subcategory->term_id,
                ),
            ),
            'meta_query' => array(
                array(
                    'key' => '_stock_status',
                    'value' => 'instock'
                )
            )
        ));

        if (!$products_query->have_posts()) {
            continue;
        }

        $has_products = true;
        $slider_id = 'slider-' . $subcategory->slug;

        // Выводим секцию с товарами
        render_products_section(
            $products_query,
            $slider_id,
            $subcategory->name,
            $subcategory->slug
        );

        wp_reset_postdata();
    endforeach;

    // Если нет товаров ни в одной подкатегории
    if (!$has_products):
        render_no_products_placeholder($current_category->name);
    endif;
    ?>

<?php else: ?>
    <!-- Нет подкатегорий - выводим товары текущей категории -->
    <?php if ($is_category && $current_category): ?>
        <?php
        // Получаем товары текущей категории
        $current_products_query = new WP_Query(array(
            'post_type' => 'product',
            'posts_per_page' => -1,
            'tax_query' => array(
                array(
                    'taxonomy' => 'product_cat',
                    'field' => 'term_id',
                    'terms' => $current_category->term_id,
                ),
            ),
            'meta_query' => array(
                array(
                    'key' => '_stock_status',
                    'value' => 'instock'
                )
            )
        ));

        if ($current_products_query->have_posts()) {
            $current_slider_id = 'slider-' . $current_category->slug;

            // Выводим секцию с товарами (без заголовка для текущей категории)
            render_products_section($current_products_query, $current_slider_id);

            wp_reset_postdata();
        } else {
            // Выводим заглушку если нет товаров
            render_no_products_placeholder($current_category->name);
        }
        ?>
    <?php endif; ?>
<?php endif; ?>

<?php
// Блок "Как заказать" 
$how_to_order_template = get_template_directory() . '/template-parts/blocks/how-to-order.php';

if (file_exists($how_to_order_template)) {
    // Устанавливаем значения ACF-полей
    add_filter('acf/load_value', function ($value, $post_id, $field) {
        if ($field['name'] === 'background_color') {
            return 'grey'; // серый фон
        }
        if ($field['name'] === 'section_title') {
            return 'Как заказать'; // заголовок
        }
        return $value;
    }, 10, 3);

    // Эмуляция блока ACF
    $block = ['id' => 'manual-how-to-order', 'className' => ''];
    set_query_var('block', $block);

    // Подключаем шаблон
    include $how_to_order_template;

    // Удаляем фильтры после вывода
    remove_all_filters('acf/load_value');
}
?>

<?php
// Выводим портфолио блок для категории архива
if ($is_category && $category_id && function_exists('render_archive_portfolio')) {
    render_archive_portfolio($category_id, 'default');
}
?>

<?php
// Выводим блок связанных категорий, если это категория верхнего уровня
if ($is_category && $category_id && function_exists('render_related_categories_block')) {
    render_related_categories_block($category_id);
}
?>

<?php
if ($is_category && $category_id && function_exists('has_category_expanding_text')) {
    if (has_category_expanding_text($category_id)) {
        // Устанавливаем данные категории для блока
        global $category_expanding_data;
        $category_expanding_data = get_category_expanding_text_data($category_id);

        // Добавляем фильтр для подмены ACF данных
        add_filter('acf/load_value', 'category_expanding_acf_filter', 10, 3);

        // Подключаем готовый блок
        get_template_part('template-parts/blocks/general-info/general-info');

        // Очищаем
        remove_filter('acf/load_value', 'category_expanding_acf_filter', 10);
        $category_expanding_data = null;
    }
}

// Функция фильтра для подмены ACF данных
function category_expanding_acf_filter($value, $post_id, $field)
{
    global $category_expanding_data;

    if ($category_expanding_data && isset($field['name'])) {
        switch ($field['name']) {
            case 'section_title':
            case 'section_title_general_info':
                return $category_expanding_data['section_title'];
            case 'background_color':
            case 'background_color_general_info':
                return $category_expanding_data['background_color'] = 'bg-white';
            case 'main_content':
                return $category_expanding_data['main_content'];
            case 'additional_content':
                return $category_expanding_data['additional_content'];
        }
    }

    return $value;
}

if (function_exists('render_archive_text_block') && $category_id) {
    render_archive_text_block($category_id);
}

?>

<?php
if (function_exists('render_archive_faq') && $category_id) {
    // Вызываем функцию FAQ с правильным category_id
    render_archive_faq($category_id, 'grey', 10);
}
?>


<?php include get_template_directory() . '/template-parts/blocks/forms/extended-form.php'; ?>



<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Обработчик для кнопок "Заказать"
        const orderButtons = document.querySelectorAll('.btn-order[data-product-id]');

        orderButtons.forEach(function(button) {
            button.addEventListener('click', function() {
                const productId = this.getAttribute('data-product-id');
                const productName = this.getAttribute('data-product-name');

                // Заполняем скрытые поля в модальной форме
                const modal = document.querySelector('#callbackModalFour');
                if (modal) {
                    const productIdField = modal.querySelector('input[name="product-id"]');
                    const productNameField = modal.querySelector('input[name="product-name"]');

                    if (productIdField) productIdField.value = productId;
                    if (productNameField) productNameField.value = productName;

                    // Обновляем заголовок модалки
                    const modalTitle = modal.querySelector('.modal-title');
                    if (modalTitle) {
                        modalTitle.textContent = 'Заказать: ' + productName;
                    }
                }
            });
        });
    });
</script>
<?php
get_footer();
?>