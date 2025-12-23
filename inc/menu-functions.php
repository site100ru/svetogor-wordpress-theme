<?php
/**
 * Функции для работы с меню
 * 
 * @package svetogor
 */

if (!defined('ABSPATH')) {
    exit;
}

// ============================================================================
// Подвальное меню
// ============================================================================

/**
 * Кастомный Walker для подвального меню (без вложенности)
 */
class Footer_Menu_Walker extends Walker_Nav_Menu {

    /**
     * Начало списка (не выводим подменю)
     */
    function start_lvl(&$output, $depth = 0, $args = null) {
        return;
    }

    /**
     * Конец списка
     */
    function end_lvl(&$output, $depth = 0, $args = null) {
        return;
    }

    /**
     * Начало элемента списка (только первый уровень)
     */
    function start_el(&$output, $item, $depth = 0, $args = null, $id = 0) {
        if ($depth > 0) {
            return;
        }

        $indent = ($depth) ? str_repeat("\t", $depth) : '';

        $classes = empty($item->classes) ? array() : (array) $item->classes;
        $classes[] = 'nav-item';

        // Проверяем активность пункта меню
        if (
            in_array('current-menu-item', $classes) ||
            in_array('current_page_item', $classes) ||
            in_array('current-menu-ancestor', $classes)
        ) {
            $classes[] = 'active';
        }

        $class_names = join(' ', apply_filters('nav_menu_css_class', array_filter($classes), $item, $args));
        $class_names = $class_names ? ' class="' . esc_attr($class_names) . '"' : '';

        $id = apply_filters('nav_menu_item_id', 'menu-item-' . $item->ID, $item, $args);
        $id = $id ? ' id="' . esc_attr($id) . '"' : '';

        $output .= $indent . '<li' . $id . $class_names . '>';

        $attributes = !empty($item->attr_title) ? ' title="' . esc_attr($item->attr_title) . '"' : '';
        $attributes .= !empty($item->target) ? ' target="' . esc_attr($item->target) . '"' : '';
        $attributes .= !empty($item->xfn) ? ' rel="' . esc_attr($item->xfn) . '"' : '';
        $attributes .= !empty($item->url) ? ' href="' . esc_attr($item->url) . '"' : '';

        // Определяем активный класс для ссылки
        $link_classes = 'nav-link';
        if (in_array('active', $classes)) {
            $link_classes .= ' active';
        }

        $item_output = isset($args->before) ? $args->before : '';
        $item_output .= '<a class="' . $link_classes . '"' . $attributes . '>';
        $item_output .= (isset($args->link_before) ? $args->link_before : '') . apply_filters('the_title', $item->title, $item->ID) . (isset($args->link_after) ? $args->link_after : '');
        $item_output .= '</a>';
        $item_output .= isset($args->after) ? $args->after : '';

        $output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
    }

    /**
     * Конец элемента списка
     */
    function end_el(&$output, $item, $depth = 0, $args = null) {
        if ($depth > 0) {
            return;
        }
        $output .= "</li>\n";
    }
}

/**
 * Функция для вывода подвального меню
 */
function display_footer_menu() {
    if (has_nav_menu('footer_menu')) {
        wp_nav_menu(array(
            'theme_location' => 'footer_menu',
            'menu_class' => 'nav footer-nav align-items-center',
            'container' => false,
            'walker' => new Footer_Menu_Walker(),
            'depth' => 1,
            'fallback_cb' => false
        ));
    }
}

/**
 * JavaScript для добавления разделителей в подвальное меню
 */
function add_footer_menu_separators_js() {
    ?>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const footerMenu = document.querySelector('.footer-nav');
            if (!footerMenu) return;

            const menuItems = footerMenu.querySelectorAll('li.nav-item:not(.d-none)');
            if (menuItems.length <= 1) return;

            const processedElements = new Set();

            for (let i = 0; i < menuItems.length - 1; i++) {
                const currentItem = menuItems[i];

                if (processedElements.has(currentItem)) continue;

                const separator = document.createElement('li');
                separator.className = 'nav-item d-none d-lg-inline';

                const img = document.createElement('img');
                img.className = 'nav-link';
                img.src = '<?php echo get_template_directory_uri(); ?>/assets/img/ico/menu-decoration-point.svg';
                img.alt = 'Разделитель меню';

                separator.appendChild(img);

                if (currentItem.nextSibling) {
                    footerMenu.insertBefore(separator, currentItem.nextSibling);
                } else {
                    footerMenu.appendChild(separator);
                }

                processedElements.add(currentItem);
            }
        });
    </script>
    <?php
}
add_action('wp_footer', 'add_footer_menu_separators_js');

/**
 * Функция для мобильного подвального меню (две колонки)
 */
function display_footer_menu_mobile() {
    if (!has_nav_menu('footer_menu')) {
        return;
    }
    
    $menu_items = wp_get_nav_menu_items(get_nav_menu_locations()['footer_menu']);

    if (!$menu_items) {
        return;
    }

    // Фильтруем только родительские элементы
    $parent_items = array_filter($menu_items, function ($item) {
        return $item->menu_item_parent == 0;
    });

    // Разделяем на две колонки
    $total_items = count($parent_items);
    $half = ceil($total_items / 2);

    $first_column = array_slice($parent_items, 0, $half);
    $second_column = array_slice($parent_items, $half);

    echo '<div class="row footer-menu footer-menu-mobile">';

    // Первая колонка
    echo '<div class="col-6"><ul class="nav flex-column">';
    foreach ($first_column as $item) {
        $active_class = (in_array('current-menu-item', $item->classes) ||
            in_array('current_page_item', $item->classes)) ? ' active' : '';
        echo '<li class="nav-item">';
        echo '<a class="nav-link ps-0' . $active_class . '" href="' . esc_url($item->url) . '">' . esc_html($item->title) . '</a>';
        echo '</li>';
    }
    echo '</ul></div>';

    // Вторая колонка
    echo '<div class="col-6"><ul class="nav flex-column">';
    foreach ($second_column as $item) {
        $active_class = (in_array('current-menu-item', $item->classes) ||
            in_array('current_page_item', $item->classes)) ? ' active' : '';
        echo '<li class="nav-item">';
        echo '<a class="nav-link ps-0' . $active_class . '" href="' . esc_url($item->url) . '">' . esc_html($item->title) . '</a>';
        echo '</li>';
    }
    echo '</ul></div>';

    echo '</div>';
}
