<?php

/**
 * Универсальная система рендеринга контента
 * дублирующиеся функции для статей, новостей, услуг
 */

/**
 * Универсальный фильтр для обертки стандартных блоков
 */
add_filter('render_block', 'universal_wrap_standard_blocks', 10, 2);

function universal_wrap_standard_blocks($block_content, $block)
{
    // Определяем тип текущей страницы
    $wrapper_class = false;

    if (is_singular('post')) {
        $wrapper_class = 'standard-block-wrapper';
    } elseif (is_singular('news')) {
        $wrapper_class = 'standard-block-wrapper';
    } elseif (is_singular('services')) {
        $wrapper_class = 'standard-block-wrapper-service';
    }

    // Если не нужная страница - возвращаем как есть
    if (!$wrapper_class) {
        return $block_content;
    }

    // Пропускаем пустые блоки
    if (empty(trim($block_content))) {
        return $block_content;
    }

    // Пропускаем все ACF блоки
    if (isset($block['blockName']) && strpos($block['blockName'], 'acf/') === 0) {
        return $block_content;
    }

    // Оборачиваем стандартные блоки
    if (isset($block['blockName']) && !empty($block['blockName'])) {
        return '<div class="' . $wrapper_class . '">' . $block_content . '</div>';
    }

    return $block_content;
}

/**
 * Универсальная функция рендеринга контента
 */
function render_universal_content($content, $post_type = 'post')
{
    // Определяем класс обертки и CSS класс контента
    $wrapper_map = array(
        'post' => array(
            'wrapper_class' => 'standard-block-wrapper',
            'section_class' => 'single-article-content',
            'content_class' => 'article-content'
        ),
        'news' => array(
            'wrapper_class' => 'standard-block-wrapper',
            'section_class' => 'single-news-content',
            'content_class' => 'news-content'
        ),
        'services' => array(
            'wrapper_class' => 'standard-block-wrapper-service',
            'section_class' => 'single-service-content',
            'content_class' => 'service-content'
        )
    );

    $config = isset($wrapper_map[$post_type]) ? $wrapper_map[$post_type] : $wrapper_map['post'];

    // Применяем фильтры WordPress
    $processed_content = apply_filters('the_content', $content);

    // Разбиваем на части
    $pattern = '/(<div class="' . $config['wrapper_class'] . '">.*?<\/div>)/s';
    $parts = preg_split($pattern, $processed_content, -1, PREG_SPLIT_DELIM_CAPTURE);

    $current_standard_group = '';

    foreach ($parts as $part) {
        if (empty(trim($part)))
            continue;

        if (strpos($part, $config['wrapper_class']) !== false) {
            // Накапливаем стандартные блоки
            $clean_content = preg_replace('/<div class="' . $config['wrapper_class'] . '">(.*?)<\/div>/s', '$1', $part);
            $current_standard_group .= $clean_content;
        } else {
            // Выводим накопленные стандартные блоки
            if (!empty(trim($current_standard_group))) {
                render_content_section($current_standard_group, $config['section_class'], $config['content_class']);
                $current_standard_group = '';
            }

            // Выводим кастомный блок как есть
            echo $part;
        }
    }

    // Если остались стандартные блоки в конце
    if (!empty(trim($current_standard_group))) {
        render_content_section($current_standard_group, $config['section_class'], $config['content_class']);
    }
}

/**
 * Вывод секции контента
 */
function render_content_section($content, $section_class, $content_class)
{
?>
    <section class="section <?php echo esc_attr($section_class); ?>">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12 col-lg-8">
                    <div class="<?php echo esc_attr($content_class); ?>">
                        <?php echo $content; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php
}

/**
 * Универсальная функция получения excerpt
 */
function get_universal_excerpt($post_id, $length = 150)
{
    $post = get_post($post_id);

    if (!$post) {
        return '';
    }

    // Используем стандартный excerpt WordPress
    if (!empty($post->post_excerpt)) {
        return $post->post_excerpt;
    }

    // Если excerpt пустой, обрезаем контент
    $content = strip_tags($post->post_content);
    $content = wp_trim_words($content, 25, '...');

    return $content;
}

// Переопределяем старые функции через универсальную
function get_article_excerpt($post_id, $length = 150)
{
    return get_universal_excerpt($post_id, $length);
}

function get_news_excerpt($post_id, $length = 150)
{
    return get_universal_excerpt($post_id, $length);
}

function get_service_excerpt($post_id, $length = 150)
{
    return get_universal_excerpt($post_id, $length);
}

// Оставляем для обратной совместимости
function render_article_content($content)
{
    render_universal_content($content, 'post');
}

function render_news_content($content)
{
    render_universal_content($content, 'news');
}

function render_service_content($content)
{
    render_universal_content($content, 'services');
}
