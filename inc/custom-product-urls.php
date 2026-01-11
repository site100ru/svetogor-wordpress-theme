<?php
/**
 * Кастомные постоянные ссылки для товаров и постов
 */

if (!defined('ABSPATH')) {
    exit;
}

// ============================================================================
// НАСТРОЙКИ
// ============================================================================

define('CPURL_POST_TYPES', array('post', 'product', 'page', 'news', 'services', 'portfolio'));
define('CPURL_TAXONOMIES', array('product_cat', 'complex_design'));

// ============================================================================
// ТРАНСЛИТЕРАЦИЯ (использует функцию из transliteration.php)
// ============================================================================

function cpurl_transliterate($text) {
    // Используем существующую функцию транслитерации
    if (function_exists('custom_transliterate_slug')) {
        return custom_transliterate_slug($text);
    }
    
    // Если функция недоступна, используем базовую транслитерацию
    return sanitize_title_with_dashes($text, '', 'save');
}

// ============================================================================
// МЕТАБОКС ДЛЯ ПОСТОВ
// ============================================================================

add_action('add_meta_boxes', 'cpurl_register_metabox');
function cpurl_register_metabox() {
    foreach (CPURL_POST_TYPES as $post_type) {
        add_meta_box(
            'custom_permalink_box',
            'Свой вариант URL',
            'cpurl_display_metabox',
            $post_type,
            'side',
            'high'
        );
    }
}

// Отобразить метабокс
function cpurl_display_metabox($post) {
    $post_id = $post->ID;
    $custom_permalink = get_post_meta($post_id, 'custom_permalink', true);
    $error = get_transient('cpurl_error_' . $post_id);
    $error_url = get_transient('cpurl_error_url_' . $post_id);
    
    wp_nonce_field('cpurl_save_meta', 'cpurl_nonce');
    ?>
    <div class="cpurl-field">
        <?php if ($error === 'duplicate'): ?>
            <div style="background: #fcf0f1; border-left: 4px solid #d63638; padding: 8px 12px; margin-bottom: 12px;">
                <strong>Ошибка:</strong> URL "<?php echo esc_html($error_url ? $error_url : 'этот'); ?>" уже используется!
            </div>
            <?php 
            delete_transient('cpurl_error_' . $post_id); 
            delete_transient('cpurl_error_url_' . $post_id);
            ?>
        <?php endif; ?>
        
        <p style="margin: 0 0 8px 0;">
            <label for="custom_permalink_field" style="display: block; margin-bottom: 5px; font-weight: 600;">URL адрес:</label>
        </p>
        <div style="display: flex; border: 1px solid #8c8f94; border-radius: 2px; overflow: hidden;">
            <span style="padding: 6px 8px; background: #f0f0f1; font-size: 12px; color: #2c3338; white-space: nowrap;"><?php echo esc_html(home_url('/')); ?></span>
            <input 
                type="text" 
                id="custom_permalink_field"
                name="custom_permalink" 
                value="<?php echo esc_attr($custom_permalink); ?>" 
                placeholder="Введите свой вариант URL"
                style="flex: 1; padding: 6px 8px; border: none; border-left: 1px solid #8c8f94; font-size: 13px; outline: none; width: 100%;" 
            />
        </div>
        <p style="margin: 8px 0 0 0; color: #646970; font-size: 12px;">
            Оставьте пустым для стандартного URL.<br>
            <em>Кириллица будет автоматически транслитерирована в латиницу.</em>
        </p>
    </div>
    <?php
}

add_action('save_post', 'cpurl_save_metabox', 10, 3);
function cpurl_save_metabox($post_id, $post, $update) {
    // Проверки
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!isset($_POST['cpurl_nonce']) || !wp_verify_nonce($_POST['cpurl_nonce'], 'cpurl_save_meta')) return;
    if (!current_user_can('edit_post', $post_id)) return;
    if (!in_array($post->post_type, CPURL_POST_TYPES)) return;
    
    if (isset($_POST['custom_permalink'])) {
        $custom_permalink = sanitize_text_field($_POST['custom_permalink']);
        $custom_permalink = trim($custom_permalink, '/');
        
        if (!empty($custom_permalink)) {
            $custom_permalink = cpurl_transliterate($custom_permalink);
            
            if (cpurl_is_unique($custom_permalink, $post_id, 'post')) {
                update_post_meta($post_id, 'custom_permalink', $custom_permalink);
                // Принудительный полный сброс
                delete_option('rewrite_rules');
                flush_rewrite_rules(false);
                wp_cache_flush();
            } else {
                set_transient('cpurl_error_' . $post_id, 'duplicate', 10);
            }
        } else {
            delete_post_meta($post_id, 'custom_permalink');
            delete_option('rewrite_rules');
            flush_rewrite_rules(false);
            wp_cache_flush();
        }
    }
}

// ============================================================================
// ОБНОВЛЕНИЕ ПРЕВЬЮ ССЫЛКИ В АДМИНКЕ
// ============================================================================

add_action('admin_footer-post.php', 'cpurl_refresh_permalink_script');
add_action('admin_footer-post-new.php', 'cpurl_refresh_permalink_script');
function cpurl_refresh_permalink_script() {
    global $post;
    
    // Проверяем, что это нужный тип поста
    if (!$post || !in_array($post->post_type, CPURL_POST_TYPES)) {
        return;
    }
    
    $custom_permalink = get_post_meta($post->ID, 'custom_permalink', true);
    if (empty($custom_permalink)) {
        return;
    }
    
    $new_url = home_url('/' . trim($custom_permalink, '/') . '/');
    ?>
    <script type="text/javascript">
    jQuery(document).ready(function($) {
        var customUrl = '<?php echo esc_js($new_url); ?>';
        
        // Функция обновления всех ссылок
        function updateAllLinks() {
            // Кнопка "Посмотреть пост/страницу"
            $('#post-preview, .preview').attr('href', customUrl);
            
            $('#wp-admin-bar-view a, #wp-admin-bar-preview a').attr('href', customUrl);
            
            // Permalink в блоке публикации
            if ($('#sample-permalink a').length) {
                $('#sample-permalink a').attr('href', customUrl).text(customUrl);
            }
            
            // Для Gutenberg
            if (typeof wp !== 'undefined' && wp.data) {
                setTimeout(function() {
                    $('.editor-post-url__link').attr('href', customUrl).text(customUrl);
                    $('.components-external-link').each(function() {
                        if ($(this).attr('href') && $(this).attr('href').indexOf('preview=true') !== -1) {
                            $(this).attr('href', customUrl);
                        }
                    });
                }, 300);
            }
        }
        
        // Обновить сразу
        updateAllLinks();
        
        // Обновить после сохранения
        $(document).on('heartbeat-tick', function() {
            setTimeout(updateAllLinks, 500);
        });
        
        // Обновить после сохранения (Gutenberg)
        if (typeof wp !== 'undefined' && wp.data) {
            var isSaving = false;
            wp.data.subscribe(function() {
                var editor = wp.data.select('core/editor');
                if (editor && typeof editor.isSavingPost === 'function') {
                    var currentlySaving = editor.isSavingPost();
                    if (isSaving && !currentlySaving) {
                        setTimeout(function() {
                            location.reload(); // Перезагружаем страницу чтобы обновить превью
                        }, 1000);
                    }
                    isSaving = currentlySaving;
                }
            });
        }
        
        // Перезагрузка после сохранения в классическом редакторе
        $('#publish, #save-post').on('click', function() {
            if ($('#custom_permalink_field').val().trim()) {
                setTimeout(function() {
                    if ($('#message.updated').length > 0) {
                        location.reload();
                    }
                }, 2000);
            }
        });
    });
    </script>
    <?php
}

// ============================================================================
// ПОЛЯ ДЛЯ ТАКСОНОМИЙ
// ============================================================================

foreach (CPURL_TAXONOMIES as $taxonomy) {
    add_action($taxonomy . '_add_form_fields', 'cpurl_add_taxonomy_field', 1, 2);
    add_action($taxonomy . '_edit_form_fields', 'cpurl_edit_taxonomy_field', 1, 2);
    add_action('created_' . $taxonomy, 'cpurl_save_taxonomy_field', 10, 2);
    add_action('edited_' . $taxonomy, 'cpurl_save_taxonomy_field', 10, 2);
}

function cpurl_add_taxonomy_field($taxonomy) {
    ?>
    <div class="form-field term-custom-permalink-wrap">
        <label for="term_custom_permalink">Свой вариант URL</label>
        <div style="display: flex; border: 1px solid #8c8f94; border-radius: 2px; overflow: hidden; max-width: 600px;">
            <span style="padding: 6px 8px; background: #f0f0f1; font-size: 12px; color: #2c3338; white-space: nowrap;"><?php echo esc_html(home_url('/')); ?></span>
            <input 
                type="text" 
                id="term_custom_permalink"
                name="term_custom_permalink" 
                value="" 
                placeholder="Введите свой вариант URL"
                style="flex: 1; padding: 6px 8px; border: none; border-left: 1px solid #8c8f94; font-size: 13px; outline: none;" 
            />
        </div>
        <p class="description">Оставьте пустым для стандартного URL. Кириллица будет автоматически транслитерирована.</p>
    </div>
    <?php
}

function cpurl_edit_taxonomy_field($term, $taxonomy) {
    $custom_permalink = get_term_meta($term->term_id, 'custom_permalink', true);
    $error = get_transient('cpurl_term_error_' . $term->term_id);
    $error_url = get_transient('cpurl_term_error_url_' . $term->term_id);
    ?>
    <tr class="form-field term-custom-permalink-wrap">
        <th scope="row">
            <label for="term_custom_permalink">Свой вариант URL</label>
        </th>
        <td>
            <?php if ($error === 'duplicate'): ?>
                <div style="background: #fcf0f1; border-left: 4px solid #d63638; padding: 8px 12px; margin-bottom: 12px;">
                    <strong>Ошибка:</strong> URL "<?php echo esc_html($error_url ? $error_url : 'этот'); ?>" уже используется!
                </div>
                <?php 
                delete_transient('cpurl_term_error_' . $term->term_id); 
                delete_transient('cpurl_term_error_url_' . $term->term_id);
                ?>
            <?php endif; ?>
            
            <div style="display: flex; border: 1px solid #8c8f94; border-radius: 2px; overflow: hidden; max-width: 600px;">
                <span style="padding: 6px 8px; background: #f0f0f1; font-size: 12px; color: #2c3338; white-space: nowrap;"><?php echo esc_html(home_url('/')); ?></span>
                <input 
                    type="text" 
                    id="term_custom_permalink"
                    name="term_custom_permalink" 
                    value="<?php echo esc_attr($custom_permalink); ?>" 
                    placeholder="Введите свой вариант URL"
                    style="flex: 1; padding: 6px 8px; border: none; border-left: 1px solid #8c8f94; font-size: 13px; outline: none;" 
                />
            </div>
            <p class="description">Оставьте пустым для стандартного URL. Кириллица будет автоматически транслитерирована.</p>
        </td>
    </tr>
    <?php
}

function cpurl_save_taxonomy_field($term_id, $tt_id = '') {
    if (isset($_POST['term_custom_permalink'])) {
        $custom_permalink = sanitize_text_field($_POST['term_custom_permalink']);
        $custom_permalink = trim($custom_permalink, '/');
        
        if (!empty($custom_permalink)) {
            $custom_permalink = cpurl_transliterate($custom_permalink);
            
            if (cpurl_is_unique($custom_permalink, $term_id, 'term')) {
                update_term_meta($term_id, 'custom_permalink', $custom_permalink);
                delete_option('rewrite_rules');
                flush_rewrite_rules(false);
                wp_cache_flush();
            } else {
                set_transient('cpurl_term_error_' . $term_id, 'duplicate', 10);
            }
        } else {
            delete_term_meta($term_id, 'custom_permalink');
            delete_option('rewrite_rules');
            flush_rewrite_rules(false);
            wp_cache_flush();
        }
    }
}

// ============================================================================
// ПРОВЕРКА УНИКАЛЬНОСТИ
// ============================================================================

function cpurl_is_unique($permalink, $current_id, $type = 'post') {
    global $wpdb;
    
    $permalink = trim($permalink, '/');
    
    // Проверяем в постах
    $post_exists = $wpdb->get_var($wpdb->prepare(
        "SELECT post_id FROM {$wpdb->postmeta} 
         WHERE meta_key = 'custom_permalink' 
         AND meta_value = %s 
         AND post_id != %d
         LIMIT 1",
        $permalink,
        ($type === 'post' ? $current_id : 0)
    ));
    
    if ($post_exists) return false;
    
    // Проверяем в терминах
    $term_exists = $wpdb->get_var($wpdb->prepare(
        "SELECT term_id FROM {$wpdb->termmeta} 
         WHERE meta_key = 'custom_permalink' 
         AND meta_value = %s 
         AND term_id != %d
         LIMIT 1",
        $permalink,
        ($type === 'term' ? $current_id : 0)
    ));
    
    return !$term_exists;
}

// ============================================================================
// ИЗМЕНЕНИЕ ССЫЛОК
// ============================================================================

add_filter('post_link', 'cpurl_custom_link', 10, 2);
add_filter('post_type_link', 'cpurl_custom_link', 10, 2);
add_filter('page_link', 'cpurl_custom_link', 10, 2);
function cpurl_custom_link($permalink, $post) {
    // $post может быть объектом или ID
    $post_id = is_object($post) ? $post->ID : $post;
    $custom = get_post_meta($post_id, 'custom_permalink', true);
    return $custom ? home_url('/' . trim($custom, '/') . '/') : $permalink;
}

add_filter('term_link', 'cpurl_custom_term_link', 10, 3);
function cpurl_custom_term_link($termlink, $term, $taxonomy) {
    if (!in_array($taxonomy, CPURL_TAXONOMIES)) return $termlink;
    
    // $term может быть объектом или ID
    $term_id = is_object($term) ? $term->term_id : $term;
    $custom = get_term_meta($term_id, 'custom_permalink', true);
    return $custom ? home_url('/' . trim($custom, '/') . '/') : $termlink;
}

// ============================================================================
// REWRITE RULES
// ============================================================================

add_action('init', 'cpurl_rewrite_rules', 999); // Изменили приоритет на 999
function cpurl_rewrite_rules() {
    global $wpdb;
    
    // Посты
    $posts = $wpdb->get_results(
        "SELECT p.ID as post_id, p.post_name, p.post_type, pm.meta_value 
         FROM {$wpdb->posts} p
         INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
         WHERE pm.meta_key = 'custom_permalink' AND pm.meta_value != ''"
    );
    
    if ($posts) {
        foreach ($posts as $row) {
            $path = trim($row->meta_value, '/');
            if (!empty($path)) {
                // Для товаров и всех кастомных типов постов
                if (in_array($row->post_type, array('product', 'news', 'services', 'portfolio'))) {
                    add_rewrite_rule(
                        '^' . preg_quote($path, '/') . '/?$',
                        'index.php?post_type=' . $row->post_type . '&name=' . $row->post_name,
                        'top' 
                    );
                } else {
                    // Для обычных постов и страниц
                    add_rewrite_rule(
                        '^' . preg_quote($path, '/') . '/?$',
                        'index.php?p=' . $row->post_id,
                        'top'
                    );
                }
            }
        }
    }
    
    // Термины
    $terms = $wpdb->get_results(
        "SELECT term_id, meta_value FROM {$wpdb->termmeta} 
         WHERE meta_key = 'custom_permalink' AND meta_value != ''"
    );
    
    if ($terms) {
        foreach ($terms as $row) {
            $term = get_term($row->term_id);
            if ($term && !is_wp_error($term)) {
                $path = trim($row->meta_value, '/');
                if (!empty($path)) {
                    add_rewrite_rule(
                        '^' . preg_quote($path, '/') . '/?$',
                        'index.php?' . $term->taxonomy . '=' . $term->slug,
                        'top'
                    );
                }
            }
        }
    }
}

// ============================================================================
// ОБРАБОТКА ЗАПРОСОВ
// ============================================================================

add_filter('request', 'cpurl_parse_request');
function cpurl_parse_request($query_vars) {
    global $wpdb;
    
    // Получаем текущий путь
    $request_uri = $_SERVER['REQUEST_URI'];
    $home_path = trim(parse_url(home_url(), PHP_URL_PATH), '/');
    
    // Очищаем путь
    $path = trim($request_uri, '/');
    if ($home_path) {
        $path = preg_replace('#^' . preg_quote($home_path, '#') . '/?#', '', $path);
    }
    
    // Убираем query string
    $path = strtok($path, '?');
    $path = trim($path, '/');
    
    if (empty($path)) {
        return $query_vars;
    }
    
    // Ищем пост
    $post_id = $wpdb->get_var($wpdb->prepare(
        "SELECT post_id FROM {$wpdb->postmeta} 
         WHERE meta_key = 'custom_permalink' 
         AND meta_value = %s 
         LIMIT 1",
        $path
    ));
    
    if ($post_id) {
        $post = get_post($post_id);
        if ($post) {
            // Для кастомных типов постов (товары, услуги, новости, портфолио)
            if (in_array($post->post_type, array('product', 'news', 'services', 'portfolio'))) {
                return array(
                    'post_type' => $post->post_type,
                    'name' => $post->post_name
                );
            }
            // Для обычных постов и страниц
            return array('p' => $post_id);
        }
    }
    
    // Ищем термин
    $term_id = $wpdb->get_var($wpdb->prepare(
        "SELECT term_id FROM {$wpdb->termmeta} 
         WHERE meta_key = 'custom_permalink' 
         AND meta_value = %s 
         LIMIT 1",
        $path
    ));
    
    if ($term_id) {
        $term = get_term($term_id);
        if ($term && !is_wp_error($term)) {
            return array($term->taxonomy => $term->slug);
        }
    }
    
    return $query_vars;
}

// ============================================================================
// РЕДИРЕКТ (ТОЛЬКО СО СТАРОГО URL НА НОВЫЙ)
// ============================================================================

add_action('template_redirect', 'cpurl_redirect', 1);
function cpurl_redirect() {
    if (is_admin()) return;
    
    global $post;
    $custom = '';
    
    // Для постов
    if (is_singular() && $post) {
        $custom = get_post_meta($post->ID, 'custom_permalink', true);
        if ($custom) {
            $custom = trim($custom, '/');
            $new_url = home_url('/' . $custom . '/');
            $current_url = home_url(add_query_arg(array(), $_SERVER['REQUEST_URI']));
            
            if (strpos($current_url, '/' . $custom . '/') === false && 
                strpos($current_url, '/' . $custom) === false) {
                wp_safe_redirect($new_url, 301);
                exit;
            }
        }
    }
    
    // Для таксономий
    if (is_tax(CPURL_TAXONOMIES) || is_product_category()) {
        $term = get_queried_object();
        if ($term) {
            $custom = get_term_meta($term->term_id, 'custom_permalink', true);
            if ($custom) {
                $custom = trim($custom, '/');
                $new_url = home_url('/' . $custom . '/');
                $current_url = home_url(add_query_arg(array(), $_SERVER['REQUEST_URI']));
                
                if (strpos($current_url, '/' . $custom . '/') === false && 
                    strpos($current_url, '/' . $custom) === false) {
                    wp_safe_redirect($new_url, 301);
                    exit;
                }
            }
        }
    }
}

// ============================================================================
// АКТИВАЦИЯ
// ============================================================================

add_action('after_switch_theme', 'cpurl_activate');
function cpurl_activate() {
    delete_option('rewrite_rules');
}