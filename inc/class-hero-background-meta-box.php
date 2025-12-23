<?php
/**
 * Универсальная система управления hero-фоном для разных типов постов
 * Поддерживает: статьи (post), новости (news), услуги (services), страницы (page)
 * 
 * @version 2.1.0
 */

class Hero_Background_Meta_Box {
    
    /**
     * Конфигурация типов постов
     */
    private static $post_types_config = [
        'post' => [
            'label' => 'article',
            'title' => 'Фоновое изображение для заголовочной секции',
            'show_warning' => false
        ],
        'news' => [
            'label' => 'news',
            'title' => 'Фоновое изображение для заголовочной секции',
            'show_warning' => false
        ],
        'services' => [
            'label' => 'service',
            'title' => 'Фоновое изображение для заголовочной секции',
            'show_warning' => false
        ],
        'page' => [
            'label' => 'page',
            'title' => 'Фоновое изображение для заголовочной секции',
            'show_warning' => true, // Показывать предупреждение о шаблоне
            'warning_text' => 'Фоновое изображение будет отображаться только при использовании шаблона "Страница с хлебными крошками".',
            'template_check' => 'page_with_bread_crumbs.php' // Шаблон для проверки
        ]
    ];

    /**
     * Инициализация хуков
     */
    public static function init() {
        add_action('add_meta_boxes', [__CLASS__, 'add_meta_boxes']);
        add_action('save_post', [__CLASS__, 'save_meta_box']);
        add_action('admin_enqueue_scripts', [__CLASS__, 'enqueue_scripts']);
        add_action('admin_footer-post.php', [__CLASS__, 'add_template_change_script']);
        add_action('admin_footer-post-new.php', [__CLASS__, 'add_template_change_script']);
    }

    /**
     * Регистрация мета-боксов для всех типов постов
     */
    public static function add_meta_boxes() {
        foreach (self::$post_types_config as $post_type => $config) {
            add_meta_box(
                $config['label'] . '_hero_bg',
                $config['title'],
                [__CLASS__, 'render_meta_box'],
                $post_type,
                'side',
                'default',
                ['post_type' => $post_type, 'config' => $config]
            );
        }
    }

    /**
     * Отрисовка мета-бокса (универсальная)
     */
    public static function render_meta_box($post, $metabox) {
        $config = $metabox['args']['config'];
        $label = $config['label'];
        $meta_key = $label . '_hero_bg';
        
        wp_nonce_field($meta_key . '_meta_box', $meta_key . '_meta_box_nonce');

        $hero_bg_id = get_post_meta($post->ID, $meta_key, true);
        $hero_bg_url = $hero_bg_id ? wp_get_attachment_image_src($hero_bg_id, 'large')[0] : '';
        
        // Предупреждение для страниц
        if (!empty($config['show_warning'])): ?>
            <div id="template-warning"
                style="background: #fff3cd; border: 1px solid #ffeaa7; padding: 10px; margin-bottom: 15px; border-radius: 4px; display: none;">
                <strong>⚠️ Внимание:</strong> <?php echo esc_html($config['warning_text']); ?>
            </div>
        <?php endif; ?>

        <div id="<?php echo $label; ?>-hero-bg-container">
            <div id="<?php echo $label; ?>-hero-bg-preview" style="margin-bottom: 15px;">
                <?php if ($hero_bg_url): ?>
                    <img src="<?php echo esc_url($hero_bg_url); ?>"
                        style="width: 100%; max-height: 150px; object-fit: cover; border-radius: 4px;">
                <?php else: ?>
                    <div style="width: 100%; height: 80px; background: #f0f0f0; border: 2px dashed #ccc; display: flex; align-items: center; justify-content: center; border-radius: 4px;">
                        <span style="color: #666;">Фон не выбран</span>
                    </div>
                <?php endif; ?>
            </div>

            <button type="button" id="select-<?php echo $label; ?>-hero-bg" class="button">
                Выбрать фоновое изображение
            </button>
            <button type="button" id="remove-<?php echo $label; ?>-hero-bg" class="button"
                style="<?php echo $hero_bg_id ? '' : 'display: none;'; ?>">
                Удалить фон
            </button>
            <input type="hidden" id="<?php echo $label; ?>-hero-bg-id" 
                name="<?php echo $meta_key; ?>" value="<?php echo esc_attr($hero_bg_id); ?>">
        </div>

        <p class="description" style="margin-top: 10px;">
            Рекомендуемый размер: 1920x600px. Если фон не выбран, будет использоваться стандартный фон из CSS.
        </p>
        <?php
    }

    /**
     * Сохранение мета-бокса (универсальное)
     */
    public static function save_meta_box($post_id) {
        // Проверка автосохранения
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        // Получаем текущий тип поста
        $post_type = get_post_type($post_id);
        
        // Проверяем, есть ли конфигурация для этого типа
        if (!isset(self::$post_types_config[$post_type])) {
            return;
        }

        $label = self::$post_types_config[$post_type]['label'];
        $meta_key = $label . '_hero_bg';
        $nonce_key = $meta_key . '_meta_box_nonce';

        // Проверка nonce
        if (!isset($_POST[$nonce_key]) || !wp_verify_nonce($_POST[$nonce_key], $meta_key . '_meta_box')) {
            return;
        }

        // Проверка прав
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        // Сохранение или удаление мета-данных
        if (isset($_POST[$meta_key])) {
            $hero_bg_id = intval($_POST[$meta_key]);
            if ($hero_bg_id) {
                update_post_meta($post_id, $meta_key, $hero_bg_id);
            } else {
                delete_post_meta($post_id, $meta_key);
            }
        }
    }

    /**
     * Подключение скриптов
     */
    public static function enqueue_scripts($hook) {
        if ('post.php' !== $hook && 'post-new.php' !== $hook) {
            return;
        }

        $screen = get_current_screen();
        $post_types = array_keys(self::$post_types_config);
        
        if (!in_array($screen->post_type, $post_types)) {
            return;
        }

        wp_enqueue_media();
        wp_enqueue_script(
            'hero-bg-uploader',
            get_template_directory_uri() . '/js/admin/hero-bg-uploader.js',
            ['jquery'],
            '2.1.0',
            true
        );
    }

    /**
     * JavaScript для динамического показа/скрытия предупреждения при смене шаблона страницы
     */
    public static function add_template_change_script() {
        global $post;
        
        if (!$post || $post->post_type !== 'page') {
            return;
        }

        $config = self::$post_types_config['page'];
        if (empty($config['show_warning'])) {
            return;
        }
        ?>
        <script type="text/javascript">
            jQuery(document).ready(function ($) {
                function toggleHeroBgWarning() {
                    var selectedTemplate = $('#page_template').val();
                    var metaBox = $('#page_hero_bg');
                    var warning = $('#template-warning');

                    if (selectedTemplate === '<?php echo esc_js($config['template_check']); ?>') {
                        metaBox.show();
                        warning.hide();
                    } else {
                        metaBox.show();
                        warning.show();
                    }
                }

                toggleHeroBgWarning();

                $('#page_template').on('change', function () {
                    toggleHeroBgWarning();
                });
            });
        </script>
        <?php
    }

    /**
     * Получение hero-фона для поста
     */
    public static function get_hero_background($post_id = null) {
        if (!$post_id) {
            $post_id = get_the_ID();
        }

        $post_type = get_post_type($post_id);
        
        if (!isset(self::$post_types_config[$post_type])) {
            return false;
        }

        $label = self::$post_types_config[$post_type]['label'];
        $meta_key = $label . '_hero_bg';
        $hero_bg_id = get_post_meta($post_id, $meta_key, true);

        if ($hero_bg_id) {
            $image = wp_get_attachment_image_src($hero_bg_id, 'full');
            return $image ? $image[0] : false;
        }

        return false;
    }
}

// Инициализация класса
Hero_Background_Meta_Box::init();

/**
 * Helper-функция для получения hero-фона в шаблонах
 */
function get_hero_bg($post_id = null) {
    return Hero_Background_Meta_Box::get_hero_background($post_id);
}