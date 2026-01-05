<?php
/**
 * Кастомные постоянные ссылки для товаров и постов
 * Аналог плагина Custom Permalinks
 */

if (!defined('ABSPATH')) {
    exit;
}

// ============================================================================
// Добавление метабокса для всех редакторов
// ============================================================================

add_action('add_meta_boxes', 'cpurl_register_metabox');
function cpurl_register_metabox() {
    $post_types = array('post', 'product', 'page', 'news', 'services', 'portfolio');
    
    foreach ($post_types as $post_type) {
        add_meta_box(
            'custom_permalink_box',
            'Свой вариант URL',
            'cpurl_display_metabox',
            $post_type,
            'side',
            'default'
        );
    }
}

// Отобразить метабокс
function cpurl_display_metabox($post) {
    // Получить ID поста
    $post_id = is_object($post) ? $post->ID : $post;
    
    // Получить значение
    $custom_permalink = get_post_meta($post_id, 'custom_permalink', true);
    $home_url = home_url('/');
    
    // Nonce для безопасности
    wp_nonce_field('cpurl_save_meta', 'cpurl_nonce');
    
    ?>
    <div class="cpurl-field">
        <p style="margin: 0 0 8px 0;">
            <label for="custom_permalink_field" style="display: block; margin-bottom: 5px; font-weight: 600;">URL адрес:</label>
        </p>
        <div style="display: flex; border: 1px solid #8c8f94; border-radius: 2px; overflow: hidden;">
            <span style="padding: 6px 8px; background: #f0f0f1; font-size: 12px; color: #2c3338; white-space: nowrap;"><?php echo esc_html($home_url); ?></span>
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

// ============================================================================
// Сохранение данных
// ============================================================================

add_action('save_post', 'cpurl_save_metabox', 10, 3);
function cpurl_save_metabox($post_id, $post, $update) {
    // Проверки безопасности
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    if (!isset($_POST['cpurl_nonce']) || !wp_verify_nonce($_POST['cpurl_nonce'], 'cpurl_save_meta')) {
        return;
    }
    
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    // Разрешенные типы
    $allowed_types = array('post', 'product', 'page', 'news', 'services', 'portfolio');
    if (!in_array($post->post_type, $allowed_types)) {
        return;
    }
    
    // Сохранить значение
    if (isset($_POST['custom_permalink'])) {
        $custom_permalink = sanitize_text_field($_POST['custom_permalink']);
        $custom_permalink = trim($custom_permalink, '/');
        
        if (!empty($custom_permalink)) {
            update_post_meta($post_id, 'custom_permalink', $custom_permalink);
        } else {
            delete_post_meta($post_id, 'custom_permalink');
        }
        
        // Сбросить rewrite rules
        flush_rewrite_rules();
    }
}

// ============================================================================
// Изменение URL
// ============================================================================

add_filter('post_link', 'cpurl_custom_permalink', 10, 2);
add_filter('post_type_link', 'cpurl_custom_permalink', 10, 2);
add_filter('page_link', 'cpurl_custom_permalink', 10, 2);
function cpurl_custom_permalink($permalink, $post) {
    $post_id = is_object($post) ? $post->ID : $post;
    $custom_permalink = get_post_meta($post_id, 'custom_permalink', true);
    
    if (!empty($custom_permalink)) {
        return home_url('/' . $custom_permalink . '/');
    }
    
    return $permalink;
}

// ============================================================================
// Rewrite Rules
// ============================================================================

add_action('init', 'cpurl_add_rewrite_rules', 10);
function cpurl_add_rewrite_rules() {
    global $wpdb;
    
    // Получить все посты с кастомными permalink
    $results = $wpdb->get_results(
        "SELECT post_id, meta_value FROM {$wpdb->postmeta} 
         WHERE meta_key = 'custom_permalink' 
         AND meta_value != ''"
    );
    
    if ($results) {
        foreach ($results as $row) {
            $post_id = $row->post_id;
            $custom_path = trim($row->meta_value, '/');
            
            if (!empty($custom_path)) {
                // Экранировать для regex
                $custom_path_escaped = str_replace('/', '\/', $custom_path);
                
                add_rewrite_rule(
                    '^' . $custom_path_escaped . '/?$',
                    'index.php?p=' . $post_id,
                    'top'
                );
            }
        }
    }
}

// ============================================================================
// Редирект со старого URL на новый
// ============================================================================

add_action('template_redirect', 'cpurl_redirect_to_custom');
function cpurl_redirect_to_custom() {
    if (is_singular()) {
        global $post;
        
        if (!$post) {
            return;
        }
        
        $custom_permalink = get_post_meta($post->ID, 'custom_permalink', true);
        
        if (!empty($custom_permalink)) {
            $new_url = home_url('/' . $custom_permalink . '/');
            $current_url = home_url(add_query_arg(array(), $_SERVER['REQUEST_URI']));
            
            // Убрать trailing slash для сравнения
            $current_clean = rtrim($current_url, '/');
            $new_clean = rtrim($new_url, '/');
            
            if ($current_clean !== $new_clean) {
                wp_redirect($new_url, 301);
                exit;
            }
        }
    }
}

// ============================================================================
// Обработка запросов
// ============================================================================

add_action('parse_request', 'cpurl_parse_custom_request');
function cpurl_parse_custom_request($wp) {
    global $wpdb;
    
    // Получить текущий путь
    $request_path = trim($_SERVER['REQUEST_URI'], '/');
    
    // Убрать базовый путь WordPress
    $home_path = trim(parse_url(home_url(), PHP_URL_PATH), '/');
    if (!empty($home_path)) {
        $request_path = preg_replace('#^' . preg_quote($home_path) . '/?#', '', $request_path);
    }
    
    // Убрать query string
    $request_path = strtok($request_path, '?');
    $request_path = trim($request_path, '/');
    
    if (empty($request_path)) {
        return;
    }
    
    // Найти пост с таким custom permalink
    $post_id = $wpdb->get_var($wpdb->prepare(
        "SELECT post_id FROM {$wpdb->postmeta} 
         WHERE meta_key = 'custom_permalink' 
         AND meta_value = %s 
         LIMIT 1",
        $request_path
    ));
    
    if ($post_id) {
        $post = get_post($post_id);
        if ($post && $post->post_status === 'publish') {
            $wp->query_vars['p'] = $post_id;
            $wp->query_vars['post_type'] = $post->post_type;
            $wp->query_vars['name'] = $post->post_name;
        }
    }
}

// ============================================================================
// Сброс правил при активации темы
// ============================================================================

add_action('after_switch_theme', 'cpurl_flush_on_activate');
function cpurl_flush_on_activate() {
    cpurl_add_rewrite_rules();
    flush_rewrite_rules();
}