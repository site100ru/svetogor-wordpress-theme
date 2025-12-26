<?php
/**
 * Универсальный визуальный редактор для описаний таксономий
 * 
 * @package svetogor
 */

if (!defined('ABSPATH')) {
    exit;
}

// ============================================================================
// Настройка таксономий с визуальным редактором
// ============================================================================

/**
 * Список таксономий, для которых нужен визуальный редактор описания
 */
function get_taxonomies_with_visual_description() {
    return array(
        'product_cat',      // Категории товаров
        'complex_design',   // Комплексное оформление
        // Добавьте сюда другие таксономии при необходимости
    );
}

// ============================================================================
// Скрытие стандартного поля описания
// ============================================================================

/**
 * Скрываем стандартное описание через CSS
 */
function hide_default_taxonomy_description_css() {
    global $current_screen;
    
    if (!$current_screen || !isset($current_screen->taxonomy)) {
        return;
    }
    
    $taxonomies = get_taxonomies_with_visual_description();
    
    if (in_array($current_screen->taxonomy, $taxonomies)) {
        echo '<style>
            .term-description-wrap { display: none !important; }
            .term-description-wrap.custom-description-wrap { display: table-row !important; }
        </style>';
    }
}
add_action('admin_head-term.php', 'hide_default_taxonomy_description_css');
add_action('admin_head-edit-tags.php', 'hide_default_taxonomy_description_css');

// ============================================================================
// Визуальный редактор для редактирования
// ============================================================================

/**
 * Добавляем визуальный редактор для редактирования описания
 */
function add_taxonomy_description_editor($term) {
    ?>
    <tr class="form-field term-description-wrap custom-description-wrap">
        <th scope="row"><label for="custom_description">Описание</label></th>
        <td>
            <?php
            $content = htmlspecialchars_decode($term->description, ENT_QUOTES);
            
            $settings = array(
                'textarea_name' => 'description',
                'textarea_rows' => 10,
                'wpautop' => true,
                'media_buttons' => true,
                'tinymce' => array(
                    'toolbar1' => 'formatselect,bold,italic,underline,bullist,numlist,blockquote,alignleft,aligncenter,alignright,link,unlink,removeformat',
                ),
                'quicktags' => true,
            );
            
            wp_editor($content, 'custom_description', $settings);
            ?>
        </td>
    </tr>
    <script>
        jQuery(document).ready(function($) {
            $('#edittag').on('submit', function() {
                var editor_content = '';
                if (typeof tinyMCE !== 'undefined' && tinyMCE.get('custom_description')) {
                    editor_content = tinyMCE.get('custom_description').getContent();
                } else {
                    editor_content = $('#custom_description').val();
                }
                
                // Удаляем старое скрытое поле если есть
                $('input[name="description"][type="hidden"]').remove();
                
                $('<input>').attr({
                    type: 'hidden',
                    name: 'description',
                    value: editor_content
                }).appendTo('#edittag');
            });
        });
    </script>
    <?php
}

// ============================================================================
// Визуальный редактор для создания
// ============================================================================

/**
 * Редактор для формы добавления нового термина
 */
function add_taxonomy_description_editor_add() {
    ?>
    <div class="form-field term-description-wrap custom-description-wrap">
        <label for="custom_description">Описание</label>
        <?php
        $settings = array(
            'textarea_name' => 'description',
            'textarea_rows' => 10,
            'wpautop' => true,
            'media_buttons' => true,
            'tinymce' => array(
                'toolbar1' => 'formatselect,bold,italic,underline,bullist,numlist,blockquote,alignleft,aligncenter,alignright,link,unlink,removeformat',
            ),
            'quicktags' => true,
        );
        
        wp_editor('', 'custom_description', $settings);
        ?>
        <p class="description">Описание с поддержкой форматирования и HTML.</p>
    </div>
    <script>
        jQuery(document).ready(function($) {
            $('#addtag').on('submit', function() {
                var editor_content = '';
                if (typeof tinyMCE !== 'undefined' && tinyMCE.get('custom_description')) {
                    editor_content = tinyMCE.get('custom_description').getContent();
                } else {
                    editor_content = $('#custom_description').val();
                }
                
                // Удаляем старое скрытое поле если есть
                $('input[name="description"][type="hidden"]').remove();
                
                $('<input>').attr({
                    type: 'hidden',
                    name: 'description',
                    value: editor_content
                }).appendTo('#addtag');
            });
        });
    </script>
    <style>
        .term-description-wrap:not(.custom-description-wrap) { 
            display: none !important; 
        }
    </style>
    <?php
}

// ============================================================================
// Подключение скриптов
// ============================================================================

/**
 * Подключаем медиабиблиотеку для таксономий
 */
function enqueue_taxonomy_visual_description_scripts($hook) {
    if ($hook !== 'edit-tags.php' && $hook !== 'term.php') {
        return;
    }
    
    global $taxonomy;
    $taxonomies = get_taxonomies_with_visual_description();
    
    if (in_array($taxonomy, $taxonomies)) {
        wp_enqueue_media();
    }
}
add_action('admin_enqueue_scripts', 'enqueue_taxonomy_visual_description_scripts');

// ============================================================================
// Динамическая регистрация хуков
// ============================================================================

/**
 * Регистрируем хуки для всех таксономий из списка
 */
function register_visual_description_hooks() {
    $taxonomies = get_taxonomies_with_visual_description();
    
    foreach ($taxonomies as $taxonomy) {
        // Для редактирования
        add_action("{$taxonomy}_edit_form_fields", 'add_taxonomy_description_editor', 2);
        
        // Для создания
        add_action("{$taxonomy}_add_form_fields", 'add_taxonomy_description_editor_add', 2);
    }
}
add_action('admin_init', 'register_visual_description_hooks');

// ============================================================================
// Разрешение HTML в описаниях
// ============================================================================

/**
 * Разрешаем HTML в описаниях таксономий
 */
remove_filter('pre_term_description', 'wp_filter_kses');
remove_filter('term_description', 'wp_kses_data');