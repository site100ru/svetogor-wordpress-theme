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
    
    wp_nonce_field('cpurl_save_meta', 'cpurl_nonce');
    ?>
    <div class="cpurl-field">
        <?php if ($error === 'duplicate'): ?>
            <div style="background: #fcf0f1; border-left: 4px solid #d63638; padding: 8px 12px; margin-bottom: 12px;">
                <strong>Ошибка:</strong> Этот URL уже используется!
            </div>
            <?php delete_transient('cpurl_error_' . $post_id); ?>
        <?php endif; ?>
        
        <p style="margin: 0 0 8px 0;">
            <label for="custom_permalink_field" style="display: block; margin-bottom: 5px; font-weight: 600;">URL адрес:</label>
        </p>
        <div style="display: flex; border: 1px solid #8c8f94; border-radius: 2px; overflow: hidden;">
            <span style="padding: 6px 8px; background: #f0f0f1; font-size: 12px; color: #2c3338; white-space: nowrap;align-content: center;"><?php echo esc_html(home_url('/')); ?></span>
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
        $old_permalink = get_post_meta($post_id, 'custom_permalink', true);
        
        if (!empty($custom_permalink)) {
            $custom_permalink = cpurl_transliterate($custom_permalink);
            
            if (cpurl_is_unique($custom_permalink, $post_id, 'post')) {
                update_post_meta($post_id, 'custom_permalink', $custom_permalink);
            } else {
                set_transient('cpurl_error_' . $post_id, 'duplicate', 10);
                return;
            }
        } else {
            delete_post_meta($post_id, 'custom_permalink');
        }
        
        // Если изменилось - сбрасываем правила и перезагружаем
        if ($old_permalink !== $custom_permalink) {
            delete_option('rewrite_rules');
            set_transient('cpurl_need_reload_' . $post_id, true, 10);
        }
    }
}

// ============================================================================
// СКРИПТ ПЕРЕЗАГРУЗКИ
// ============================================================================

add_action('admin_footer-post.php', 'cpurl_reload_script');
add_action('admin_footer-post-new.php', 'cpurl_reload_script');
function cpurl_reload_script() {
    global $post;
    
    if (!$post || !in_array($post->post_type, CPURL_POST_TYPES)) {
        return;
    }
    
    $need_reload = get_transient('cpurl_need_reload_' . $post->ID);
    
    if ($need_reload) {
        delete_transient('cpurl_need_reload_' . $post->ID);
        ?>
        <script type="text/javascript">
        setTimeout(function() {
            window.location.reload();
        }, 500);
        </script>
        <?php
        return;
    }
    ?>
    <script type="text/javascript">
    jQuery(document).ready(function($) {
        var initialValue = $('#custom_permalink_field').val();
        
        // Gutenberg
        if (typeof wp !== 'undefined' && wp.data) {
            var isSaving = false;
            wp.data.subscribe(function() {
                var editor = wp.data.select('core/editor');
                if (editor && typeof editor.isSavingPost === 'function') {
                    var currentlySaving = editor.isSavingPost();
                    if (isSaving && !currentlySaving) {
                        var currentValue = $('#custom_permalink_field').val();
                        if (initialValue !== currentValue) {
                            setTimeout(function() {
                                location.reload();
                            }, 1000);
                        }
                    }
                    isSaving = currentlySaving;
                }
            });
        }
        
        // Классический редактор
        $('#publish, #save-post').on('click', function() {
            var currentValue = $('#custom_permalink_field').val();
            if (initialValue !== currentValue) {
                setTimeout(function() {
                    if ($('#message.updated').length > 0 || $('.notice-success').length > 0) {
                        location.reload();
                    }
                }, 1000);
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
            <span style="padding: 6px 8px; background: #f0f0f1; font-size: 12px; color: #2c3338; white-space: nowrap;align-content: center;"><?php echo esc_html(home_url('/')); ?></span>
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
    ?>
    <tr class="form-field term-custom-permalink-wrap">
        <th scope="row">
            <label for="term_custom_permalink">Свой вариант URL</label>
        </th>
        <td>
            <?php if ($error === 'duplicate'): ?>
                <div style="background: #fcf0f1; border-left: 4px solid #d63638; padding: 8px 12px; margin-bottom: 12px;">
                    <strong>Ошибка:</strong> Этот URL уже используется!
                </div>
                <?php delete_transient('cpurl_term_error_' . $term->term_id); ?>
            <?php endif; ?>
            
            <div style="display: flex; border: 1px solid #8c8f94; border-radius: 2px; overflow: hidden; max-width: 600px;">
                <span style="padding: 6px 8px; background: #f0f0f1; font-size: 12px; color: #2c3338; white-space: nowrap;align-content: center;"><?php echo esc_html(home_url('/')); ?></span>
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
            } else {
                set_transient('cpurl_term_error_' . $term_id, 'duplicate', 10);
                return;
            }
        } else {
            delete_term_meta($term_id, 'custom_permalink');
        }
        
        delete_option('rewrite_rules');
    }
}

// ============================================================================
// ПРОВЕРКА УНИКАЛЬНОСТИ
// ============================================================================

function cpurl_is_unique($permalink, $current_id, $type = 'post') {
    global $wpdb;
    
    $permalink = trim($permalink, '/');
    
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
    $post_id = is_object($post) ? $post->ID : $post;
    $custom = get_post_meta($post_id, 'custom_permalink', true);
    return $custom ? home_url('/' . trim($custom, '/') . '/') : $permalink;
}

add_filter('term_link', 'cpurl_custom_term_link', 10, 3);
function cpurl_custom_term_link($termlink, $term, $taxonomy) {
    if (!in_array($taxonomy, CPURL_TAXONOMIES)) return $termlink;
    
    $term_id = is_object($term) ? $term->term_id : $term;
    $custom = get_term_meta($term_id, 'custom_permalink', true);
    return $custom ? home_url('/' . trim($custom, '/') . '/') : $termlink;
}

// ============================================================================
//  REWRITE RULES
// ============================================================================

add_action('template_redirect', 'cpurl_handle_request', 1);
function cpurl_handle_request() {
    // Получаем текущий путь
    $request_uri = $_SERVER['REQUEST_URI'];
    $home_path = parse_url(home_url(), PHP_URL_PATH);
    $home_path = $home_path ? trim($home_path, '/') : '';
    
    $path = trim($request_uri, '/');
    if ($home_path) {
        $path = preg_replace('#^' . preg_quote($home_path, '#') . '/?#', '', $path);
    }
    
    $path = strtok($path, '?');
    $path = trim($path, '/');
    
    if (empty($path)) {
        return;
    }
    
    global $wpdb, $wp_query, $post;
    
    // Ищем пост с таким URL (ПРЯМОЙ запрос к БД каждый раз)
    $result = $wpdb->get_row($wpdb->prepare(
        "SELECT pm.post_id, p.post_name, p.post_type 
         FROM {$wpdb->postmeta} pm
         INNER JOIN {$wpdb->posts} p ON pm.post_id = p.ID
         WHERE pm.meta_key = 'custom_permalink' 
         AND pm.meta_value = %s 
         AND p.post_status = 'publish'
         LIMIT 1",
        $path
    ));
    
    if ($result) {
        // Найден пост - загружаем его полностью
        $found_post = get_post($result->post_id);
        
        if ($found_post) {
            // Сбрасываем текущий запрос
            $wp_query = new WP_Query();
            
            // Создаем новый запрос для найденного поста
            if (in_array($result->post_type, array('product', 'news', 'services', 'portfolio'))) {
                $wp_query->query(array(
                    'post_type' => $result->post_type,
                    'name' => $result->post_name,
                    'posts_per_page' => 1
                ));
            } else {
                $wp_query->query(array(
                    'p' => $result->post_id,
                    'post_type' => 'any'
                ));
            }
            
            // Устанавливаем глобальные переменные
            $wp_query->is_singular = true;
            $wp_query->is_single = ($result->post_type !== 'page');
            $wp_query->is_page = ($result->post_type === 'page');
            $wp_query->is_404 = false;
            
            // ВАЖНО: Устанавливаем глобальный $post
            $post = $found_post;
            setup_postdata($post);
            
            return;
        }
    }
    
    // Ищем термин
    $term_result = $wpdb->get_row($wpdb->prepare(
        "SELECT tm.term_id, t.slug, tt.taxonomy
         FROM {$wpdb->termmeta} tm
         INNER JOIN {$wpdb->terms} t ON tm.term_id = t.term_id
         INNER JOIN {$wpdb->term_taxonomy} tt ON t.term_id = tt.term_id
         WHERE tm.meta_key = 'custom_permalink' 
         AND tm.meta_value = %s
         LIMIT 1",
        $path
    ));
    
    if ($term_result) {
        $wp_query = new WP_Query();
        $wp_query->query(array(
            'taxonomy' => $term_result->taxonomy,
            $term_result->taxonomy => $term_result->slug
        ));
        
        $wp_query->is_tax = true;
        $wp_query->is_archive = true;
        $wp_query->is_404 = false;
    }
}

// ============================================================================
// РЕДИРЕКТ (ТОЛЬКО СО СТАРОГО URL НА НОВЫЙ)
// ============================================================================

add_action('template_redirect', 'cpurl_redirect', 99);
function cpurl_redirect() {
    if (is_admin()) return;
    
    global $post;
    
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
        if ($term && isset($term->term_id)) {
            $custom = get_term_meta($term->term_id, 'custom_permalink', true);
            if (!empty($custom)) {
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