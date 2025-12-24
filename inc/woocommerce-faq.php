<?php
/**
 * FAQ для товаров и категорий WooCommerce
 * 
 * @package svetogor
 */

if (!defined('ABSPATH')) {
    exit;
}

// ============================================================================
// Получение FAQ для товаров
// ============================================================================

/**
 * Получает FAQ для товара с учетом настроек товара
 * Приоритет: выбранная в товаре категория > подкатегория первого уровня > любая категория с FAQ
 */
function get_product_faq($product_id) {
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
function get_product_faq_auto($product_id) {
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
function get_archive_faq($category_id = null) {
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
 * Получает все категории товара с FAQ
 */
function get_product_categories_with_faq($product_id) {
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

// ============================================================================
// Вывод FAQ блоков
// ============================================================================

/**
 * Выводит FAQ блок для товара используя существующий шаблон
 */
function render_product_faq($product_id, $background_color = 'grey', $container_width = 10) {
    $product_faq = get_product_faq($product_id);

    if (!$product_faq || empty($product_faq['faq'])) {
        return;
    }

    $faq_items = $product_faq['faq'];

    // Симулируем ACF поля для блока
    $simulated_acf_data = array(
        'title' => 'Частые вопросы',
        'background_color' => $background_color,
        'container_width' => $container_width,
        'questions' => array()
    );

    // Преобразуем данные в формат блока
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

    // Подключаем шаблон
    $template_path = get_template_directory() . '/template-parts/blocks/faq/faq.php';
    if (file_exists($template_path)) {
        include $template_path;
    }

    // Очищаем временные данные
    $temp_acf_data = null;
    remove_filter('acf/load_value', 'temp_acf_load_value', 10);
}

/**
 * Выводит FAQ блок для архива категории
 */
function render_archive_faq($category_id = null, $background_color = 'grey', $container_width = 10) {
    $archive_faq = get_archive_faq($category_id);

    if (!$archive_faq || empty($archive_faq['faq'])) {
        return;
    }

    $faq_items = $archive_faq['faq'];

    // Симулируем ACF поля
    $simulated_acf_data = array(
        'title' => 'Частые вопросы',
        'background_color' => $background_color,
        'container_width' => $container_width,
        'questions' => array()
    );

    // Преобразуем данные
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

    // Временно устанавливаем данные
    global $temp_acf_data;
    $temp_acf_data = $simulated_acf_data;
    add_filter('acf/load_value', 'temp_acf_load_value', 10, 3);

    // Подключаем шаблон
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
 * Простой вывод FAQ (fallback)
 */
function render_simple_faq($data) {
    if (empty($data['questions'])) {
        return;
    }
    
    $bg_class = isset($data['background_color']) && $data['background_color'] === 'grey' ? 'bg-grey' : '';
    $accordion_id = 'faqAccordion-' . uniqid();
    ?>
    <section class="section faq-section <?php echo esc_attr($bg_class); ?>">
        <div class="container">
            <?php if (!empty($data['title'])): ?>
                <h2 class="text-center mb-4"><?php echo esc_html($data['title']); ?></h2>
            <?php endif; ?>
            
            <div class="accordion" id="<?php echo $accordion_id; ?>">
                <?php foreach ($data['questions'] as $index => $question): ?>
                    <?php
                    $q = $question['question_answer']['question'];
                    $a = $question['question_answer']['answer'];
                    $expanded = !empty($question['expanded']);
                    
                    $heading_id = 'faqHeading-' . $accordion_id . '-' . $index;
                    $collapse_id = 'faqCollapse-' . $accordion_id . '-' . $index;
                    ?>
                    <div class="accordion-item">
                        <h3 class="accordion-header" id="<?php echo $heading_id; ?>">
                            <button class="accordion-button <?php echo $expanded ? '' : 'collapsed'; ?>" 
                                    type="button" 
                                    data-bs-toggle="collapse" 
                                    data-bs-target="#<?php echo $collapse_id; ?>" 
                                    aria-expanded="<?php echo $expanded ? 'true' : 'false'; ?>" 
                                    aria-controls="<?php echo $collapse_id; ?>">
                                <?php echo esc_html($q); ?>
                            </button>
                        </h3>
                        <div id="<?php echo $collapse_id; ?>" 
                             class="accordion-collapse collapse <?php echo $expanded ? 'show' : ''; ?>" 
                             data-bs-parent="#<?php echo $accordion_id; ?>">
                            <div class="accordion-body">
                                <?php echo wp_kses_post($a); ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php
}