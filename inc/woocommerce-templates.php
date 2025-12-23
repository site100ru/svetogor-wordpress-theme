<?php
/**
 * Функции шаблонов и контента WooCommerce
 * 
 * @package svetogor
 */

if (!defined('ABSPATH')) {
    exit;
}

// ============================================================================
// Контент табов товара
// ============================================================================

/**
 * Контент таба "Описание" через ACF
 */
function custom_description_tab_content() {
    global $product;

    $description_rows = get_field('description_rows', $product->get_id());

    if (!empty($description_rows) && is_array($description_rows)) {
        echo '<div class="row">';

        foreach ($description_rows as $row) {
            $layout = $row['layout'];

            if ($layout === 'one_column') {
                echo '<div class="col-12 mb-0 mb-lg-4">';
                if (!empty($row['content_full'])) {
                    echo wpautop(wp_kses_post($row['content_full']));
                }
                echo '</div>';
            } else {
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
        // Fallback на стандартное описание
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

/**
 * Контент таба "Характеристики" из атрибутов товара
 */
function specifications_tab_content() {
    global $product;

    $attributes = $product->get_attributes();

    if (!empty($attributes)) {
        echo '<div class="row">';

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

/**
 * Контент таба "Прайс" из ACF полей
 */
function price_list_tab_content() {
    global $product;

    $price_rows = get_field('price_list_rows', $product->get_id());

    if (!empty($price_rows) && is_array($price_rows)) {
        echo '<div class="row">';

        foreach ($price_rows as $row) {
            $layout = $row['layout'];
            $items = $row['items'];

            if (empty($items) || !is_array($items)) {
                continue;
            }

            if ($layout === 'one_column') {
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

// ============================================================================
// Хлебные крошки для товаров
// ============================================================================

/**
 * Кастомные хлебные крошки для товаров
 */
function custom_product_breadcrumbs() {
    global $product;

    echo '<ol class="breadcrumb bg-transparent p-0 m-0">';

    // Домой с картинкой
    echo '<li class="breadcrumb-item">';
    echo '<a href="' . home_url() . '">';
    echo '<img src="' . get_template_directory_uri() . '/assets/img/ico/breadcrumbs.svg" loading="lazy" alt="Изображение домика">';
    echo '</a>';
    echo '</li>';

    $terms = wp_get_post_terms($product->get_id(), 'product_cat');
    
    if (!empty($terms) && !is_wp_error($terms)) {
        // Находим самую глубокую категорию
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
            $ancestors = get_ancestors($deepest_term->term_id, 'product_cat');
            $ancestors = array_reverse($ancestors);
            $ancestors[] = $deepest_term->term_id;

            // Ограничиваем до 2 уровней категорий
            $max_categories = 2;

            if (count($ancestors) > $max_categories) {
                $limited_ancestors = array(
                    $ancestors[0],
                    $ancestors[count($ancestors) - 1]
                );
            } else {
                $limited_ancestors = $ancestors;
            }

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

// ============================================================================
// Кросселы (перекрестные продажи)
// ============================================================================

/**
 * Вывод кросселов WooCommerce
 */
function render_woocommerce_crosssells($product_id = null, $limit = 6, $background_color = "bg-grey") {
    if (!$product_id) {
        $product_id = get_the_ID();
    }

    $product = wc_get_product($product_id);
    if (!$product) {
        return;
    }

    $cross_sells = $product->get_cross_sell_ids();

    if (empty($cross_sells)) {
        return;
    }

    $cross_sells = array_slice($cross_sells, 0, $limit);

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

    <section class="section section-product-recoment box-shadow-main-img <?php echo esc_attr($background_color); ?>">
        <div class="container">
            <div class="section-title text-center">
                <h3>А еще Вам может пригодиться</h3>
                <img src="<?php echo get_template_directory_uri(); ?>/assets/img/ico/points.svg" alt="Точки" class="img-fluid" />
            </div>
            
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

                    <article class="col-12 col-md-6 col-lg-4">
                        <a href="<?php echo esc_url($product_link); ?>" class="card bg-transparent">
                            <div class="card">
                                <div class="card-img-container">
                                    <img src="<?php echo esc_url($product_image_url); ?>" alt="<?php echo esc_attr($product_title); ?>" class="img-fluid" />
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

// ============================================================================
// Связанные категории
// ============================================================================

/**
 * Вывод блока связанных категорий
 */
function render_related_categories_block($category_id, $title = 'А еще Вам может пригодиться', $background_class = 'bg-grey') {
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
                                    <img src="<?php echo esc_url($category_photo_url); ?>" alt="<?php echo esc_attr($related_category->name); ?>" class="img-fluid" />
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