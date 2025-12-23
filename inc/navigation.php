<?php
/**
 * ФИНАЛЬНАЯ ВЕРСИЯ НАВИГАЦИИ SVETOGOR
 * Исправленная и оптимизированная версия с правильной структурой уровней
 */

/**
 * Получение категорий ВТОРОГО уровня для левой колонки мега-меню
 * (дочерние от корневых категорий, у которых parent.parent = 0)
 */
function svetogor_get_second_level_categories()
{
  try {
    if (!class_exists('WooCommerce')) {
      return array();
    }

    // Получаем ВСЕ категории с галочкой "Выводить в шапке"
    $all_categories = get_terms(array(
      'taxonomy' => 'product_cat',
      'hide_empty' => false,
      'meta_query' => array(
        array(
          'key' => 'show_in_header',
          'value' => '1',
          'compare' => '='
        )
      ),
      'orderby' => 'menu_order',
      'order' => 'ASC'
    ));

    if (!$all_categories || is_wp_error($all_categories)) {
      return array();
    }

    // Фильтруем - оставляем только те, чей родитель является корневой категорией
    $second_level = array();

    foreach ($all_categories as $cat) {
      if ($cat->parent == 0) {
        continue; // Пропускаем корневые категории
      }

      // Проверяем, является ли родитель корневой категорией
      $parent_term = get_term($cat->parent, 'product_cat');

      if ($parent_term && !is_wp_error($parent_term) && $parent_term->parent == 0) {
        // Родитель является корневой категорией, значит это второй уровень
        $second_level[] = $cat;
      }
    }

    return $second_level;

  } catch (Exception $e) {
    if (current_user_can('administrator')) {
      error_log('Ошибка получения категорий второго уровня: ' . $e->getMessage());
    }
    return array();
  }
}

/**
 * Получение категорий ТРЕТЬЕГО уровня для конкретной категории второго уровня
 */
function svetogor_get_third_level_categories($second_level_parent_id)
{
  try {
    if (!class_exists('WooCommerce')) {
      return array();
    }

    $third_level = get_terms(array(
      'taxonomy' => 'product_cat',
      'hide_empty' => false,
      'parent' => $second_level_parent_id, // Прямые дочерние от категории второго уровня
      'meta_query' => array(
        array(
          'key' => 'show_in_header',
          'value' => '1',
          'compare' => '='
        )
      ),
      'orderby' => 'menu_order',
      'order' => 'ASC',
      'number' => 20
    ));

    return $third_level && !is_wp_error($third_level) ? $third_level : array();

  } catch (Exception $e) {
    return array();
  }
}

/**
 * Безопасное получение иконки категории
 */
function svetogor_get_category_icon_safe($term_id)
{
  try {
    $thumbnail_id = get_term_meta($term_id, 'thumbnail_id', true);
    if ($thumbnail_id) {
      $url = wp_get_attachment_image_url($thumbnail_id, 'thumbnail');
      if ($url) {
        return $url;
      }
    }
  } catch (Exception $e) {
    // Тихо игнорируем ошибку
  }

  // Fallback иконка
  return get_template_directory_uri() . '/assets/img/ico/default-category.svg';
}

/**
 * Безопасный вывод товаров для категории
 */
function svetogor_output_subcategory_products($category_id)
{
  try {
    $products = get_posts(array(
      'post_type' => 'product',
      'posts_per_page' => 5, // Ограничиваем для безопасности
      'post_status' => 'publish',
      'tax_query' => array(
        array(
          'taxonomy' => 'product_cat',
          'field' => 'term_id',
          'terms' => $category_id,
        ),
      ),
      'orderby' => 'menu_order',
      'order' => 'ASC'
    ));

    if (!$products || !is_array($products) || empty($products)) {
      return; // Просто не выводим список если нет товаров
    }

    echo '<ul class="subcategory-list">';
    foreach ($products as $product) {
      if (!$product || !isset($product->ID)) {
        continue;
      }

      echo '<li>';
      echo '<a href="' . get_permalink($product->ID) . '">' . esc_html($product->post_title) . '</a>';
      echo '</li>';
    }
    echo '</ul>';

  } catch (Exception $e) {
    // Тихо игнорируем ошибки товаров
    if (current_user_can('administrator')) {
      echo '<p>Ошибка товаров: ' . $e->getMessage() . '</p>';
    }
  }
}

/**
 * Вывод категорий ВТОРОГО уровня в левой колонке
 */
function svetogor_output_second_level_categories()
{
  try {
    $second_level_categories = svetogor_get_second_level_categories();

    if (empty($second_level_categories)) {
      echo '<p>Категории второго уровня не найдены</p>';
      if (current_user_can('administrator')) {
        echo '<p><small>Проверьте структуру: Корневая → Вторая (с галочкой) → Третья (с галочкой)</small></p>';
      }
      return;
    }

    $first = true;
    foreach ($second_level_categories as $category) {
      if (!$category || !isset($category->term_id)) {
        continue;
      }

      $icon_url = svetogor_get_category_icon_safe($category->term_id);
      $active_class = $first ? ' active' : '';

      echo '<a class="nav-link' . $active_class . '" href="' . get_term_link($category) . '" data-target="' . $category->term_id . '">';
      echo '<span class="category-icon">';
      echo '<img loading="lazy" src="' . esc_url($icon_url) . '" alt="Иконка ' . esc_attr($category->name) . '" >';
      echo '</span>';
      echo '<span>' . esc_html($category->name) . '</span>';
      echo '<span class="category-arrow"></span>';
      echo '</a>';

      $first = false;
    }

    if (current_user_can('administrator')) {
      echo '<!-- ВТОРОЙ УРОВЕНЬ: Найдено категорий: ' . count($second_level_categories) . ' -->';
    }

  } catch (Exception $e) {
    if (current_user_can('administrator')) {
      echo '<p>ОШИБКА ВТОРОГО УРОВНЯ: ' . $e->getMessage() . '</p>';
    }
  }
}

/**
 * Вывод категорий ТРЕТЬЕГО уровня в правой колонке (ИСПРАВЛЕННАЯ ВЕРСИЯ)
 */
function svetogor_output_third_level_categories()
{
  try {
    $second_level_categories = svetogor_get_second_level_categories();

    if (empty($second_level_categories)) {
      echo '<p>Подкатегории третьего уровня не найдены</p>';
      return;
    }

    $first_content = true;
    foreach ($second_level_categories as $second_level_cat) {
      if (!$second_level_cat || !isset($second_level_cat->term_id)) {
        continue;
      }

      $active_class = $first_content ? ' active' : '';

      echo '<div class="subcategory-content' . $active_class . '" id="' . $second_level_cat->term_id . '-content">';
      echo '<div class="row">';

      // Получаем категории третьего уровня для этой категории второго уровня
      $third_level_categories = svetogor_get_third_level_categories($second_level_cat->term_id);

      if (!empty($third_level_categories)) {
        foreach ($third_level_categories as $third_level_cat) {
          if (!$third_level_cat || !isset($third_level_cat->term_id)) {
            continue;
          }

          echo '<div class="col-md-3">';
          
          // ИСПРАВЛЕНО: Создаем якорную ссылку на второй уровень
          $anchor_link = svetogor_create_anchor_link($second_level_cat, $third_level_cat);
          
          echo '<a href="' . esc_url($anchor_link) . '" class="subcategory-title h5">';
          echo esc_html($third_level_cat->name);
          echo '</a>';

          // Получаем товары для категории третьего уровня
          svetogor_output_subcategory_products($third_level_cat->term_id);

          echo '</div>';
        }
      } else {
        echo '<div class="col-md-12">';
        echo '<p>Подкатегории третьего уровня для "' . esc_html($second_level_cat->name) . '" будут добавлены позже.</p>';
        echo '</div>';
      }

      echo '</div>';
      echo '</div>';

      $first_content = false;
    }

    if (current_user_can('administrator')) {
      echo '<!-- ТРЕТИЙ УРОВЕНЬ: Обработано категорий второго уровня: ' . count($second_level_categories) . ' -->';
    }

  } catch (Exception $e) {
    if (current_user_can('administrator')) {
      echo '<p>ОШИБКА ТРЕТЬЕГО УРОВНЯ: ' . $e->getMessage() . '</p>';
    }
  }
}

/**
 * Мега-меню для продукции
 */
function svetogor_output_products_dropdown_final($title)
{
  try {
    echo '<li class="nav-item nav-item-hero dropdown">';
    echo '<a class="nav-link dropdown-toggle" href="#" id="productsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">';
    echo esc_html($title);
    echo '</a>';

    // МЕГА-МЕНЮ СТРУКТУРА
    echo '<div class="dropdown-menu mega-menu" aria-labelledby="productsDropdown">';
    echo '<div class="container">';
    echo '<div class="row">';

    // Левая колонка - категории ВТОРОГО уровня
    echo '<div class="col-lg-3">';
    echo '<div class="category-menu">';
    echo '<nav class="nav flex-column">';

    svetogor_output_second_level_categories();

    echo '</nav>';
    echo '</div>';
    echo '</div>';

    // Правая колонка - категории ТРЕТЬЕГО уровня
    echo '<div class="col-lg-9">';
    svetogor_output_third_level_categories();
    echo '</div>';

    echo '</div>';
    echo '</div>';
    echo '</div>';
    echo '</li>';

  } catch (Exception $e) {
    if (current_user_can('administrator')) {
      echo '<li>ОШИБКА МЕГА-МЕНЮ: ' . $e->getMessage() . '</li>';
    }
    // Fallback к простому пункту меню
    echo '<li class="nav-item nav-item-hero">';
    echo '<a class="nav-link" href="#">' . esc_html($title) . '</a>';
    echo '</li>';
  }
}

/**
 * Вывод пункта меню (проверка на "Продукция")
 */
function svetogor_output_menu_item_final($item)
{
  try {
    if (!$item || !isset($item->title)) {
      return;
    }

    $title = trim($item->title);
    $url = isset($item->url) ? $item->url : '#';

    // Точная проверка на "Продукцию" (по ID или названию)
    if ($item->ID == 1226 || $title === 'Продукция') {
      // Выводим МЕГА-МЕНЮ для продукции
      svetogor_output_products_dropdown_final($title);
    } else {
      // Обычный пункт меню
      echo '<li class="nav-item nav-item-hero">';
      echo '<a class="nav-link" href="' . esc_url($url) . '">' . esc_html($title) . '</a>';
      echo '</li>';
    }

  } catch (Exception $e) {
    if (current_user_can('administrator')) {
      echo '<li>ОШИБКА ПУНКТА МЕНЮ: ' . $e->getMessage() . '</li>';
    }
  }
}

/**
 * Функция для создания якорной ссылки из slug категории
 */
function svetogor_create_anchor_link($parent_category, $current_category) {
  try {
    $parent_link = get_term_link($parent_category);
    if (is_wp_error($parent_link)) {
      return get_term_link($current_category);
    }
    
    // Получаем slug текущей категории для якоря
    $anchor = $current_category->slug;
    
    // Формируем ссылку: ссылка_на_родителя#slug_текущей_категории
    return $parent_link . '#' . $anchor;
    
  } catch (Exception $e) {
    // В случае ошибки возвращаем обычную ссылку на категорию
    return get_term_link($current_category);
  }
}


/**
 * Вывод всех пунктов меню
 */
function svetogor_output_safe_menu_final()
{
  try {
    $menu_locations = get_nav_menu_locations();

    if (!isset($menu_locations['header_menu'])) {
      echo '<li><a href="' . home_url() . '">Главная</a></li>';
      return;
    }

    $menu_items = wp_get_nav_menu_items($menu_locations['header_menu']);

    if (!$menu_items || !is_array($menu_items)) {
      echo '<li><a href="' . home_url() . '">Главная</a></li>';
      return;
    }

    // Фильтруем родительские элементы
    $parent_items = array();
    foreach ($menu_items as $item) {
      if ($item && $item->menu_item_parent == 0) {
        $parent_items[] = $item;
      }
    }

    // Находим позицию пункта "Продукция"
    $products_position = -1;
    for ($i = 0; $i < count($parent_items); $i++) {
      $title = trim($parent_items[$i]->title);
      if ($parent_items[$i]->ID == 1226 || $title === 'Продукция') {
        $products_position = $i;
        break;
      }
    }

    // Выводим пункты меню с разделителями (ИСПРАВЛЕНО)
    $counter = 0;
    foreach ($parent_items as $item) {
      // Добавляем разделитель перед пунктом, НО НЕ если предыдущий пункт был "Продукция"
      $previous_was_products = ($counter > 0 && $counter - 1 == $products_position);
      
      if ($counter > 0 && !$previous_was_products) {
        echo '<li class="nav-item d-none d-lg-inline align-content-center">';
        echo '<img loading="lazy" class="nav-link" src="' . get_template_directory_uri() . '/assets/img/ico/menu-decoration-point.svg" alt="Декоративная точка - разделитель">';
        echo '</li>';
      }

      svetogor_output_menu_item_final($item);
      $counter++;
    }

  } catch (Exception $e) {
    if (current_user_can('administrator')) {
      echo '<li>ОШИБКА МЕНЮ: ' . $e->getMessage() . '</li>';
    }
  }
}

/**
 * Мобильное меню - Уровень 1 (основное меню)
 */
function svetogor_output_mobile_level1()
{
  echo '<div class="mobile-view level-1 active" id="main-menu-view">';
  echo '<ul class="navbar-nav">';

  try {
    $menu_locations = get_nav_menu_locations();

    if (isset($menu_locations['header_menu'])) {
      $menu_items = wp_get_nav_menu_items($menu_locations['header_menu']);

      if ($menu_items && is_array($menu_items)) {
        $parent_items = array_filter($menu_items, function ($item) {
          return $item->menu_item_parent == 0;
        });

        foreach ($parent_items as $item) {
          $title = trim($item->title);

          if ($item->ID == 1226 || $title === 'Продукция') {
            echo '<li class="nav-item">';
            echo '<div class="mobile-menu-item" data-view="products-menu-view">';
            echo '<div class="d-flex align-items-center">';
            echo '<span>' . esc_html($title) . '</span>';
            echo '</div>';
            echo '<span class="arrow"></span>';
            echo '</div>';
            echo '</li>';
          } else {
            echo '<li class="nav-item">';
            echo '<a class="nav-link" href="' . esc_url($item->url) . '">' . esc_html($title) . '</a>';
            echo '</li>';
          }
        }
      }
    }

    // Дополнительная информация для мобильного меню
    svetogor_output_mobile_footer_info();

  } catch (Exception $e) {
    echo '<li>Ошибка меню: ' . $e->getMessage() . '</li>';
  }

  echo '</ul>';
  echo '</div>';
}

/**
 * Мобильное меню - Уровень 2 (категории второго уровня)
 */
function svetogor_output_mobile_second_level()
{
  echo '<div class="mobile-view level-2" id="products-menu-view">';
  echo '<h5 class="mobile-view-title">Продукция</h5>';

  try {
    $second_level_categories = svetogor_get_second_level_categories();

    if (!empty($second_level_categories)) {
      foreach ($second_level_categories as $category) {
        $icon_url = svetogor_get_category_icon_safe($category->term_id);

        echo '<div class="mobile-menu-item" data-view="' . $category->term_id . '-menu-view">';
        echo '<div class="d-flex align-items-center">';
        echo '<img loading="lazy" src="' . esc_url($icon_url) . '" alt="Иконка ' . esc_attr($category->name) . '" style="width: 20px; height: 20px; margin-right: 10px;" >';
        echo '<span>' . esc_html($category->name) . '</span>';
        echo '</div>';
        echo '<span class="arrow"></span>';
        echo '</div>';
      }
    } else {
      echo '<p>Категории второго уровня не найдены</p>';
    }

  } catch (Exception $e) {
    echo '<p>Ошибка мобильных категорий: ' . $e->getMessage() . '</p>';
  }

  echo '<button class="back-button" data-view="main-menu-view">Назад в меню</button>';
  echo '</div>';
}

/**
 * Мобильное меню - Уровень 3 (категории третьего уровня)
 */
function svetogor_output_mobile_third_level()
{
  try {
    $second_level_categories = svetogor_get_second_level_categories();

    foreach ($second_level_categories as $second_level_cat) {
      echo '<div class="mobile-view level-3" id="' . $second_level_cat->term_id . '-menu-view">';
      echo '<a href="' . get_term_link($second_level_cat) . '" class="mobile-view-title h5">' . esc_html($second_level_cat->name) . '</a>';

      // Получаем категории третьего уровня
      $third_level_categories = svetogor_get_third_level_categories($second_level_cat->term_id);

      if (!empty($third_level_categories)) {
        foreach ($third_level_categories as $third_level_cat) {
          $products = get_posts(array(
            'post_type' => 'product',
            'posts_per_page' => 5,
            'post_status' => 'publish',
            'tax_query' => array(
              array(
                'taxonomy' => 'product_cat',
                'field' => 'term_id',
                'terms' => $third_level_cat->term_id,
              ),
            ),
          ));

          echo '<div class="mb-4">';
          echo '<a href="' . get_term_link($third_level_cat) . '">' . esc_html($third_level_cat->name) . '</a>';

          if (!empty($products)) {
            echo '<ul class="list-unstyled ps-3 mt-2">';
            foreach ($products as $product) {
              echo '<li class="mb-2">';
              echo '<a href="' . get_permalink($product->ID) . '" class="text-decoration-none">' . esc_html($product->post_title) . '</a>';
              echo '</li>';
            }
            echo '</ul>';
          }

          echo '</div>';
        }
      } else {
        echo '<div class="mb-4">';
        echo '<p>Подкатегории будут добавлены позже.</p>';
        echo '</div>';
      }

      echo '<button class="back-button" data-view="products-menu-view">Назад к продукции</button>';
      echo '</div>';
    }

  } catch (Exception $e) {
    echo '<div>Ошибка мобильного третьего уровня: ' . $e->getMessage() . '</div>';
  }
}

/**
 * Дополнительная информация в мобильном меню
 */
function svetogor_output_mobile_footer_info()
{
  echo '<li class="nav-item d-lg-none py-2">';
  echo '<div class="d-flex align-items-center gap-2">';
  echo '<img loading="lazy" src="' . get_template_directory_uri() . '/assets/img/ico/location-ico.svg" alt="Иконка карты" style="max-height: 14px">';
  echo '<span style="font-size: 14px">г. Москва, ул. Полярная, 31В, оф. 141</span>';
  echo '</div>';
  echo '<a class="top-menu-tel nav-link price-text" style="font-size: 18px" href="tel:+74952450325">+7 (495) 245-03-25</a>';
  echo '<a href="mailto:svetogor.sv@mail.ru" class="d-flex align-items-center gap-2">';
  echo '<img loading="lazy" src="' . get_template_directory_uri() . '/assets/img/ico/email-ico.svg" alt="Иконка почтовой собаки" style="max-height: 16px">';
  echo '<span style="font-size: 14px">svetogor.sv@mail.ru</span>';
  echo '</a>';
  echo '</li>';

  echo '<li class="nav-item">';
  echo '<a class="ico-button pe-2" href="https://wa.me/" target="_blank">';
  echo '<img loading="lazy" src="' . get_template_directory_uri() . '/assets/img/ico/whatsapp.svg" alt="WhatsApp">';
  echo '</a>';
  echo '<a class="ico-button pe-0" href="https://t.me/+79511014610" target="_blank">';
  echo '<img loading="lazy" src="' . get_template_directory_uri() . '/assets/img/ico/telegram.svg" alt="Telegram">';
  echo '</a>';
  echo '</li>';
}

/**
 * Мобильное меню полностью
 */
function svetogor_add_mobile_menu_final()
{
  try {
    echo '<div class="offcanvas offcanvas-end" tabindex="-1" id="mobileMenu" aria-labelledby="mobileMenuLabel">';
    echo '<div class="offcanvas-header">';
    echo '<h5 class="offcanvas-title" id="mobileMenuLabel">Меню</h5>';
    echo '<button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>';
    echo '</div>';
    echo '<div class="offcanvas-body position-relative">';

    // Уровень 1: Основное меню
    svetogor_output_mobile_level1();

    // Уровень 2: Категории ВТОРОГО уровня
    svetogor_output_mobile_second_level();

    // Уровень 3: Категории ТРЕТЬЕГО уровня
    svetogor_output_mobile_third_level();

    echo '</div>';
    echo '</div>';

  } catch (Exception $e) {
    if (current_user_can('administrator')) {
      echo '<div>ОШИБКА МОБИЛЬНОГО МЕНЮ: ' . $e->getMessage() . '</div>';
    }
  }
}

/**
 * ГЛАВНАЯ ФУНКЦИЯ НАВИГАЦИИ (заменяет svetogor_safe_navigation_v5)
 */
function svetogor_safe_navigation_v5()
{
  try {
    echo '<div class="navbar-wrapper">';
    echo '<nav class="navbar navbar-expand-lg navbar-light bg-white" id="navbar">';
    echo '<div class="container flex-wrap">';

    // Логотип
    echo '<a class="navbar-brand mx-lg-auto ms-xxl-0" href="' . home_url() . '">';
    echo '<img loading="lazy" src="' . get_template_directory_uri() . '/assets/img/logo.svg" alt="Логотип">';
    echo '</a>';

    // Кнопка мобильного меню
    echo '<button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileMenu" aria-controls="mobileMenu" aria-expanded="false" aria-label="Toggle navigation">';
    echo '<span class="navbar-toggler-icon"></span>';
    echo '</button>';

    // Меню для десктопа
    echo '<div class="collapse navbar-collapse" id="navbarContent">';
    echo '<ul class="navbar-nav mx-md-auto me-xxl-0">';

    // ВЫВОДИМ МЕНЮ
    svetogor_output_safe_menu_final();

    echo '</ul>';
    echo '</div>';
    echo '</div>';
    echo '</nav>';
    echo '</div>';

    // ДОБАВЛЯЕМ МОБИЛЬНОЕ МЕНЮ
    svetogor_add_mobile_menu_final();

  } catch (Exception $e) {
    if (current_user_can('administrator')) {
      echo '<div style="background: red; color: white; padding: 10px;">ОШИБКА НАВИГАЦИИ: ' . $e->getMessage() . '</div>';
    }
  }
}

/**
 * JavaScript для мега-меню и мобильной навигации
 */
function svetogor_add_navigation_js()
{
  ?>
  <script>
    document.addEventListener('DOMContentLoaded', function () {

      // === ДЕСКТОПНОЕ МЕГА-МЕНЮ ===
      const categoryLinks = document.querySelectorAll('.category-menu .nav-link');
      if (categoryLinks.length > 0) {

        categoryLinks.forEach((link) => {
          link.addEventListener('mouseover', function () {
            // Удаляем активный класс со всех ссылок
            categoryLinks.forEach((l) => l.classList.remove('active'));
            // Добавляем активный класс текущей ссылке
            this.classList.add('active');

            // Показываем соответствующий контент
            const target = this.getAttribute('data-target');
            if (target) {
              document.querySelectorAll('.subcategory-content').forEach((content) => {
                content.classList.remove('active');
              });

              const targetContent = document.getElementById(target + '-content');
              if (targetContent) {
                targetContent.classList.add('active');
              }
            }
          });
        });
      }

      // Hover для открытия мега-меню
      const productsDropdown = document.getElementById('productsDropdown');
      const megaMenu = document.querySelector('.dropdown-menu.mega-menu');

      if (productsDropdown && megaMenu) {

        const parentLi = productsDropdown.closest('li');

        if (parentLi && window.innerWidth >= 992) {
          parentLi.addEventListener('mouseenter', function () {
            megaMenu.classList.add('show');
          });

          parentLi.addEventListener('mouseleave', function () {
            megaMenu.classList.remove('show');
          });
        }
      } else {
        console.log('❌ Мега-меню не найдено');
      }

      // === МОБИЛЬНАЯ НАВИГАЦИЯ ===
      const menuItems = document.querySelectorAll('.mobile-menu-item');
      const backButtons = document.querySelectorAll('.back-button');

      function navigateToView(viewId) {

        document.querySelectorAll('.mobile-view').forEach((view) => {
          view.classList.remove('active');
        });

        const targetView = document.getElementById(viewId);
        if (targetView) {
          targetView.classList.add('active');
        } else {
          console.log('❌ Вид не найден:', viewId);
        }
      }

      // Клик по элементу меню для перехода на следующий уровень
      menuItems.forEach((item) => {
        item.addEventListener('click', function () {
          const targetView = this.getAttribute('data-view');
          if (targetView) {
            navigateToView(targetView);
          }
        });
      });

      // Клик по кнопке "Назад"
      backButtons.forEach((button) => {
        button.addEventListener('click', function () {
          const targetView = this.getAttribute('data-view');
          if (targetView) {
            navigateToView(targetView);
          }
        });
      });

      // Обработка закрытия мобильного меню
      const closeButton = document.querySelector('.offcanvas .btn-close');
      if (closeButton) {
        closeButton.addEventListener('click', function () {
          navigateToView('main-menu-view');
        });
      }

      const offcanvasElement = document.querySelector('#mobileMenu');
      if (offcanvasElement) {
        offcanvasElement.addEventListener('hidden.bs.offcanvas', function () {
          navigateToView('main-menu-view');
        });
      }

    });
  </script>
  <?php
}

// Подключаем JavaScript
add_action('wp_footer', 'svetogor_add_navigation_js');

/**
 * СОВМЕСТИМОСТЬ С СУЩЕСТВУЮЩИМИ ФУНКЦИЯМИ
 * Эти функции нужны для работы с header.php
 */

/**
 * Форматирование телефона для href
 */
if (!function_exists('format_phone_for_href')) {
  function format_phone_for_href($phone)
  {
    // Удаляем все символы кроме цифр и +
    return preg_replace('/[^+\d]/', '', $phone);
  }
}

/**
 * Получение URL иконки контакта с fallback
 */
if (!function_exists('get_contact_icon_url')) {
  function get_contact_icon_url($field_name, $default_filename)
  {
    // Попробуем получить из ACF полей, если они есть
    if (function_exists('get_field')) {
      $icon = get_field($field_name, 'option');
      if ($icon && isset($icon['url'])) {
        return $icon['url'];
      }
    }

    // Fallback на стандартную иконку
    return get_template_directory_uri() . '/assets/img/ico/' . $default_filename;
  }
}

/**
 * Получение логотипа компании
 */
if (!function_exists('get_company_logo')) {
  function get_company_logo()
  {
    if (function_exists('get_field')) {
      $logo = get_field('company_logo', 'option');
      if ($logo) {
        return $logo;
      }
    }

    // Fallback
    return array(
      'url' => get_template_directory_uri() . '/assets/img/logo.svg',
      'alt' => get_bloginfo('name')
    );
  }
}

// Если функций нет - создаем заглушки для WooCommerce
if (!function_exists('get_header_woocommerce_categories')) {
  function get_header_woocommerce_categories()
  {
    return svetogor_get_second_level_categories();
  }
}

if (!function_exists('get_header_subcategories')) {
  function get_header_subcategories($parent_id)
  {
    return svetogor_get_third_level_categories($parent_id);
  }
}

if (!function_exists('get_category_products')) {
  function get_category_products($category_id, $limit = 10)
  {
    if (!class_exists('WooCommerce')) {
      return array();
    }

    return get_posts(array(
      'post_type' => 'product',
      'posts_per_page' => $limit,
      'post_status' => 'publish',
      'tax_query' => array(
        array(
          'taxonomy' => 'product_cat',
          'field' => 'term_id',
          'terms' => $category_id,
        ),
      ),
      'orderby' => 'menu_order',
      'order' => 'ASC'
    ));
  }
}

/**
 * Проверка активности пункта меню
 */
if (!function_exists('is_menu_item_active')) {
  function is_menu_item_active($item)
  {
    if (!$item) {
      return false;
    }

    $classes = (array) $item->classes;

    return in_array('current-menu-item', $classes) ||
      in_array('current_page_item', $classes) ||
      in_array('current-menu-ancestor', $classes);
  }
}

/**
 * Добавление классов к body для стилизации
 */
if (!function_exists('svetogor_body_classes')) {
  function svetogor_body_classes($classes)
  {
    // Добавляем класс если это страница с WooCommerce
    if (class_exists('WooCommerce')) {
      if (is_woocommerce() || is_cart() || is_checkout() || is_account_page()) {
        $classes[] = 'woocommerce-page';
      }
    }

    return $classes;
  }
  add_filter('body_class', 'svetogor_body_classes');
}

/**
 * Безопасная функция получения логотипа (дубль для совместимости)
 */
if (!function_exists('get_company_logo_custom')) {
  function get_company_logo_custom()
  {
    return get_company_logo();
  }
}

/**
 * Получение подкатегорий для мобильного меню
 */
if (!function_exists('get_category_with_products_mobile')) {
  function get_category_with_products_mobile($category_id)
  {
    $subcategories = svetogor_get_third_level_categories($category_id);
    $result = array();

    foreach ($subcategories as $subcategory) {
      $products = get_category_products($subcategory->term_id, 10);

      if (!empty($products)) {
        $result[] = array(
          'category' => $subcategory,
          'products' => $products
        );
      }
    }

    return $result;
  }
}

/**
 * РЕГИСТРАЦИЯ МЕНЮ И ИНИЦИАЛИЗАЦИЯ ТЕМЫ
 */
function svetogor_setup_navigation_final()
{
  // Поддержка меню
  add_theme_support('menus');

  // Регистрация местоположений меню
  register_nav_menus(array(
    'header_menu' => __('Основное меню', 'svetogor'),
    'footer_menu' => __('Подвальное меню', 'svetogor'),
  ));
}
add_action('after_setup_theme', 'svetogor_setup_navigation_final');

/**
 * Инициализация функций навигации (отложенная загрузка)
 */
function svetogor_init_navigation()
{
  // Здесь можно добавить дополнительную инициализацию при необходимости
  // Все основные функции уже определены выше
}
add_action('init', 'svetogor_init_navigation', 5); // Приоритет 5 для раннего запуска

/**
 * ПОДКЛЮЧЕНИЕ BOOTSTRAP (если еще не подключен)
 */
function svetogor_navigation_assets_final()
{
  // Bootstrap JS (если еще не подключен)
  if (!wp_script_is('bootstrap', 'enqueued')) {
    wp_enqueue_script('bootstrap',  get_template_directory_uri() . '/assets/js/bootstrap-custom.min.js', array(), '1.0');
  }

  // Bootstrap CSS (если еще не подключен)
  if (!wp_style_is('bootstrap', 'enqueued')) {
    wp_enqueue_style('bootstrap', get_template_directory_uri() . '/assets/css/bootstrap-custom.min.css', array(), '1.0');
  }
}
add_action('wp_enqueue_scripts', 'svetogor_navigation_assets_final');

/**
 * ОТЛАДОЧНЫЕ ФУНКЦИИ (для администраторов)
 */
function svetogor_debug_navigation()
{
  if (!current_user_can('administrator') || !isset($_GET['debug_nav'])) {
    return;
  }

  echo '<div style="position: fixed; top: 100px; right: 20px; background: white; border: 3px solid red; padding: 20px; z-index: 9999; max-width: 500px; font-family: monospace; font-size: 12px;">';
  echo '<h3 style="color: red;">🔍 ОТЛАДКА НАВИГАЦИИ</h3>';

  // 1. Проверяем меню
  $menu_locations = get_nav_menu_locations();
  echo '<strong>1. Местоположения меню:</strong><br>';
  foreach ($menu_locations as $location => $menu_id) {
    echo "- {$location} => {$menu_id}<br>";
  }

  // 2. Получаем пункты меню
  echo '<br><strong>2. Пункты меню:</strong><br>';
  if (isset($menu_locations['header_menu'])) {
    $menu_items = wp_get_nav_menu_items($menu_locations['header_menu']);

    if ($menu_items) {
      echo 'Всего пунктов: ' . count($menu_items) . '<br>';

      $counter = 1;
      foreach ($menu_items as $item) {
        if ($item->menu_item_parent == 0) {
          echo "<div style='border: 1px solid #ccc; margin: 5px; padding: 5px;'>";
          echo "<strong>Пункт #{$counter}:</strong><br>";
          echo "ID: {$item->ID}<br>";
          echo "Заголовок: '{$item->title}'<br>";
          echo "URL: {$item->url}<br>";

          // Проверяем на "Продукцию"
          if ($item->ID == 1226 || $item->title === 'Продукция') {
            echo "✅ <strong style='color: green;'>ЭТО ПУНКТ ПРОДУКЦИЯ!</strong><br>";
          }

          echo "</div>";
          $counter++;
        }
      }
    } else {
      echo '<strong style="color: red;">❌ ПУНКТЫ МЕНЮ НЕ НАЙДЕНЫ!</strong><br>';
    }
  } else {
    echo '<strong style="color: red;">❌ МЕСТОПОЛОЖЕНИЕ header_menu НЕ НАЙДЕНО!</strong><br>';
  }

  // 3. WooCommerce категории
  echo '<br><strong>3. Категории второго уровня:</strong><br>';
  $second_level = svetogor_get_second_level_categories();
  echo 'Найдено категорий: ' . count($second_level) . '<br>';

  if (!empty($second_level)) {
    foreach ($second_level as $cat) {
      echo "- {$cat->name} (ID: {$cat->term_id}, Parent: {$cat->parent})<br>";

      // Проверяем третий уровень
      $third_level = svetogor_get_third_level_categories($cat->term_id);
      if (!empty($third_level)) {
        echo "  └ Подкатегорий 3-го уровня: " . count($third_level) . "<br>";
      }
    }
  }

  echo '<button onclick="this.parentElement.style.display=\'none\'" style="margin-top: 10px;">Закрыть</button>';
  echo '</div>';
}
add_action('wp_head', 'svetogor_debug_navigation');

/**
 * Добавляем ссылку для отладки в админ бар
 */
function svetogor_add_debug_link($wp_admin_bar)
{
  if (!current_user_can('administrator')) {
    return;
  }

  $wp_admin_bar->add_node(array(
    'id' => 'debug_navigation',
    'title' => '🔧 Отладка навигации',
    'href' => add_query_arg('debug_nav', '1', home_url())
  ));
}
add_action('admin_bar_menu', 'svetogor_add_debug_link', 100);

?>