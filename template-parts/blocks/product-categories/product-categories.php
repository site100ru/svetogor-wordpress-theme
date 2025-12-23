<?php
/**
 * Блок "Категории товаров"
 * добавлена сортировка по menu_order как в админке
 */

// Получаем настройки блока
$section_title = get_field('section_title') ?: 'Наша продукция';
$background_color = get_field('background_color_product_categories') ?: 'bg-grey';
$selection_type = get_field('selection_type') ?: 'manual';
$selected_categories = get_field('selected_categories');
$categories_limit = get_field('categories_limit') ?: 6;
$show_empty_categories = get_field('show_empty_categories');
$columns_count = get_field('columns_count') ?: '3';
$show_section_title = get_field('show_section_title');

// Устанавливаем сортировку как в админке
$sort_order = 'menu_order';

// Определяем CSS класс фона
$bg_class = $background_color === 'bg-grey' ? 'bg-grey' : '';

// Определяем CSS класс для колонок
$col_class_map = array(
  '2' => 'col-12 col-md-6',
  '3' => 'col-12 col-md-6 col-lg-4',
  '4' => 'col-12 col-md-6 col-lg-3'
);
$col_class = isset($col_class_map[$columns_count]) ? $col_class_map[$columns_count] : 'col-12 col-md-6 col-lg-4';

// Получаем категории в зависимости от типа выбора
$categories = array();

switch ($selection_type) {
  case 'manual':
    if (!empty($selected_categories)) {
      $selected_ids = array();
      $temp_categories = is_array($selected_categories) ? $selected_categories : array($selected_categories);
      
      // Извлекаем ID выбранных категорий
      foreach ($temp_categories as $cat) {
        if (is_object($cat) && isset($cat->term_id)) {
          $selected_ids[] = $cat->term_id;
        }
      }
      
      // Получаем категории с правильной сортировкой
      if (!empty($selected_ids)) {
        $categories = get_terms(array(
          'taxonomy' => 'product_cat',
          'include' => $selected_ids,
          'orderby' => $sort_order,
          'order' => 'ASC',
          'hide_empty' => false
        ));
      }
    }
    break;

  case 'header_categories':
    if (function_exists('get_header_categories')) {
      $categories = get_header_categories();
    }
    break;

  case 'parent_categories':
    $categories = get_terms(array(
      'taxonomy' => 'product_cat',
      'parent' => 0, // Только родительские категории
      'hide_empty' => !$show_empty_categories,
      'orderby' => $sort_order, // ИЗМЕНЕНО: используем настройку сортировки
      'order' => 'ASC',
      'number' => $categories_limit
    ));
    break;

  case 'second_level_categories':
    // НОВЫЙ ВАРИАНТ: категории второго уровня (как в навигации)
    if (function_exists('svetogor_get_second_level_categories')) {
      $all_second_level = svetogor_get_second_level_categories();
      $categories = array_slice($all_second_level, 0, $categories_limit);
    }
    break;

  case 'categories_with_show_in_header':
    // НОВЫЙ ВАРИАНТ: только категории с галочкой "Выводить в шапке"
    $categories = get_terms(array(
      'taxonomy' => 'product_cat',
      'hide_empty' => !$show_empty_categories,
      'meta_query' => array(
        array(
          'key' => 'show_in_header',
          'value' => '1',
          'compare' => '='
        )
      ),
      'orderby' => $sort_order, // ИЗМЕНЕНО: используем настройку сортировки
      'order' => 'ASC',
      'number' => $categories_limit
    ));
    break;
}

// Ограничиваем количество категорий если не ручной выбор
if ($selection_type !== 'manual' && !empty($categories)) {
  $categories = array_slice($categories, 0, $categories_limit);
}

// Проверяем есть ли категории для вывода
if (empty($categories) || is_wp_error($categories)) {
  return;
}

// ID блока для уникальности
$block_id = 'product-categories-' . $block['id'];
$block_classes = 'section section-product box-shadow-main-img ' . $bg_class;

// Добавляем кастомные классы если есть
if (!empty($block['className'])) {
  $block_classes .= ' ' . $block['className'];
}
?>

<section class="<?php echo esc_attr($block_classes); ?>" id="<?php echo esc_attr($block_id); ?>">
  <div class="container">
    <?php if ($show_section_title): ?>
      <div class="section-title text-center">
        <h3><?php echo esc_html($section_title); ?></h3>
        <img loading="lazy" src="<?php echo get_template_directory_uri(); ?>/assets/img/ico/points.svg" alt="Декоративные точки" class="img-fluid" />
      </div>
    <?php endif; ?>

    <!-- Карточки категорий -->
    <div class="row g-4">
      <?php foreach ($categories as $category): ?>
        <?php
        // Проверяем что это объект термина
        if (!is_object($category) || is_wp_error($category)) {
          continue;
        }

        // Получаем данные категории
        $category_name = $category->name;
        $category_link = get_term_link($category);
        $category_photo_url = '';

        // Получаем фото категории если функция существует
        if (function_exists('get_category_photo_url')) {
          $category_photo_url = get_category_photo_url($category->term_id, 'medium');
        }

        // Fallback на стандартную миниатюру WooCommerce
        if (!$category_photo_url) {
          $thumbnail_id = get_term_meta($category->term_id, 'thumbnail_id', true);
          if ($thumbnail_id) {
            $category_photo_url = wp_get_attachment_image_url($thumbnail_id, 'medium');
          }
        }

        // Финальный fallback на заглушку
        if (!$category_photo_url) {
          $category_photo_url = wc_placeholder_img_src();
        }

        // Проверяем что ссылка валидна
        if (is_wp_error($category_link)) {
          continue;
        }
        ?>

        <div class="<?php echo esc_attr($col_class); ?>">
          <a href="<?php echo esc_url($category_link); ?>" class="card-link card-categories">
            <div class="card">
              <div class="card-img-container">
                <img loading="lazy" src="<?php echo esc_url($category_photo_url); ?>" alt="<?php echo esc_attr($category_name); ?>" class="img-fluid"  />
              </div>

              <div class="card-body text-center">
                <h5><?php echo esc_html($category_name); ?></h5>
              </div>
            </div>
          </a>
        </div>

      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php
// ОТЛАДКА: для администраторов
if (current_user_can('administrator') && isset($_GET['debug_categories'])) {
  echo '<div style="background: #f0f0f0; border: 2px solid #333; padding: 15px; margin: 20px 0; font-family: monospace;">';
  echo '<h4>🔍 ОТЛАДКА БЛОКА КАТЕГОРИЙ</h4>';
  echo '<strong>Тип выбора:</strong> ' . $selection_type . '<br>';
  echo '<strong>Сортировка:</strong> ' . $sort_order . '<br>';
  echo '<strong>Лимит:</strong> ' . $categories_limit . '<br>';
  echo '<strong>Найдено категорий:</strong> ' . count($categories) . '<br>';
  
  if (!empty($categories)) {
    echo '<strong>Список категорий:</strong><br>';
    foreach ($categories as $cat) {
      if (is_object($cat)) {
        echo "- {$cat->name} (ID: {$cat->term_id})<br>";
      }
    }
  }
  echo '</div>';
}
?>