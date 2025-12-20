<?php
/**
 * Шаблон архива таксономии "Комплексное оформление"
 */

defined('ABSPATH') || exit;

get_header('shop');

// Получаем текущий термин
$current_term = get_queried_object();
$linked_categories = get_term_meta($current_term->term_id, 'linked_categories', true);
$linked_categories = is_array($linked_categories) ? $linked_categories : array();

// Получаем связанные товары
$linked_products = get_term_meta($current_term->term_id, 'linked_products', true);
$linked_products = is_array($linked_products) ? $linked_products : array();

// Принудительно подключаем стили и скрипты для блока портфолио
wp_enqueue_style('glide-css', 'https://cdn.jsdelivr.net/npm/@glidejs/glide@3.6.0/dist/css/glide.core.min.css', array(), '3.6.0');
wp_enqueue_script('glide-js', 'https://cdn.jsdelivr.net/npm/@glidejs/glide@3.6.0/dist/glide.min.js', array(), '3.6.0', true);
wp_enqueue_script('portfolio-slider-js', get_template_directory_uri() . '/template-parts/blocks/portfolio-slider/portfolio-slider.js', array('jquery'), filemtime(get_template_directory() . '/template-parts/blocks/portfolio-slider/portfolio-slider.js'), true);
wp_localize_script('portfolio-slider-js', 'portfolio_ajax', array(
  'ajax_url' => admin_url('admin-ajax.php')
));
?>

<!-- ХЛЕБНЫЕ КРОШКИ -->
<section class="section-mini">
  <div class="container">
    <nav aria-label="breadcrumb" class="mb-0">
      <ol class="breadcrumb bg-transparent p-0 m-0">
        <li class="breadcrumb-item">
          <a href="<?php echo esc_url(home_url('/')); ?>" class="text-decoration-none text-secondary">
            <img src="<?php echo get_template_directory_uri(); ?>/assets/img/ico/breadcrumbs.svg" loading="lazy" />
          </a>
        </li>
        <li class="breadcrumb-item">Комплексное оформление</li>
        <li class="breadcrumb-item active" aria-current="page"><?php echo esc_html($current_term->name); ?></li>
      </ol>
    </nav>
  </div>
</section>

<!-- КОНТЕНТ -->
<section class="section section-page-comprehensive box-shadow-main">
  <div class="container">
    <div class="section-content-cards">
      <div class="section-title text-center">
        <h3>
          Комплексное оформление <br />
          <?php echo esc_html(mb_strtolower($current_term->name)); ?>
        </h3>
        <img src="<?php echo get_template_directory_uri(); ?>/assets/img/ico/points.svg" alt="Точки" class="img-fluid">
      </div>

      <?php if (!empty($linked_categories) || !empty($linked_products)): ?>
        <div class="row row-cards">
          <?php
          // Выводим связанные категории
          foreach ($linked_categories as $category_id) {
            $category = get_term($category_id, 'product_cat');
            if ($category && !is_wp_error($category)) {
              // Получаем фотографию категории из кастомного поля
              $category_photo_id = get_term_meta($category_id, 'category_photo', true);
              $thumbnail_url = '';

              if ($category_photo_id) {
                $thumbnail_url = wp_get_attachment_image_url($category_photo_id, 'medium');
              }

              // Fallback на placeholder
              if (!$thumbnail_url) {
                $thumbnail_url = wc_placeholder_img_src();
              }
              ?>
              <div class="col-12 col-md-6 mb-4">
                <a href="<?php echo esc_url(get_term_link($category_id, 'product_cat')); ?>" class="card"
                  style="height: calc(100% - 12px);">
                  <div class="row g-0 align-items-center h-100">
                    <div class="col-12 col-lg-4 text-center card-img-container">
                      <img src="<?php echo esc_url($thumbnail_url); ?>" alt="<?php echo esc_attr($category->name); ?>"
                        class="img-fluid" />
                    </div>
                    <div class="col-12 col-lg-8">
                      <div class="card-body">
                        <h5 class="card-title mb-3"><?php echo esc_html($category->name); ?></h5>
                        <p class="card-text">
                          <?php
                          if ($category->description) {
                            echo wp_trim_words($category->description, 20, '...');
                          } else {
                            echo 'Посмотрите нашу продукцию в категории ' . esc_html($category->name) . '.';
                          }
                          ?>
                        </p>
                        <span class="btn btn-invert">Подробнее</span>
                      </div>
                    </div>
                  </div>
                </a>
              </div>
              <?php
            }
          }

          // Выводим связанные товары в том же стиле
          if (!empty($linked_products)) {
            $products_query = new WP_Query(array(
              'post_type' => 'product',
              'post__in' => $linked_products,
              'posts_per_page' => -1,
              'orderby' => 'post__in',
              'meta_query' => array(
                array(
                  'key' => '_stock_status',
                  'value' => 'instock'
                )
              )
            ));

            if ($products_query->have_posts()) {
            while ($products_query->have_posts()) {
                $products_query->the_post();
                global $product;

                // Получаем изображение товара
                $image_id = $product->get_image_id();
                if ($image_id) {
                $thumbnail_url = wp_get_attachment_image_url($image_id, 'medium');
                } else {
                $thumbnail_url = wc_placeholder_img_src();
                }

                // Получаем краткое описание
                $excerpt = $product->get_short_description();
                ?>
                <div class="col-12 col-md-6 mb-4">
                <a href="<?php echo esc_url(get_permalink($product->get_id())); ?>" class="card"
                    style="height: calc(100% - 12px);">
                    <div class="row g-0 align-items-center h-100">
                    <div class="col-12 col-lg-4 text-center card-img-container">
                        <img src="<?php echo esc_url($thumbnail_url); ?>" alt="<?php echo esc_attr($product->get_name()); ?>"
                        class="img-fluid" />
                    </div>
                    <div class="col-12 col-lg-8">
                        <div class="card-body">
                        <h5 class="card-title mb-3"><?php echo esc_html($product->get_name()); ?></h5>
                        <p class="card-text">
                            <?php
                            if ($excerpt) {
                            echo wp_trim_words($excerpt, 20, '...');
                            } else {
                            echo 'Краткое описание товара ' . esc_html($product->get_name());
                            }
                            ?>
                        </p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="product-price"><?php echo $product->get_price_html(); ?></span>
                            <button type="button" class="btn btn-invert" data-bs-toggle="modal"
                            data-bs-target="#callbackModalFour" data-product-id="<?php echo $product->get_id(); ?>"
                            data-product-name="<?php echo esc_attr($product->get_name()); ?>"
                            onclick="event.preventDefault(); event.stopPropagation();">
                            Заказать
                            </button>
                        </div>
                        </div>
                    </div>
                    </div>
                </a>
                </div>
                <?php
            }
            wp_reset_postdata();
            }
          }
          ?>
        </div>
      <?php else: ?>
        <div class="row">
          <div class="col-12 text-center">
            <p>К этому оформлению пока не привязаны категории или товары.</p>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </div>
</section>

<?php get_template_part('template-parts/blocks/forms/extended-form'); ?>

<?php if ($current_term->description): ?>
  <section class="section description bg-grey">
    <div class="container">
      <div class="row">
        <div class="col-12 col-lg-10 mx-auto text-lg-center">
          <?php echo wpautop(wp_kses_post($current_term->description)); ?>
        </div>
      </div>
    </div>
  </section>
<?php endif; ?>

<?php
// БЛОК ПОРТФОЛИО (без данных из админки)
$portfolio_template = get_template_directory() . '/template-parts/blocks/portfolio-slider/portfolio-slider.php';
if (file_exists($portfolio_template)) {
  // Переопределяем только фон
  add_filter('acf/load_value', function ($value, $post_id, $field) {
    if ($field['name'] == 'slider_background') {
      return 'white'; 
    }
    if ($field['name'] == 'show_all_works_button') {
      return True;
    }
    return $value;
  }, 10, 3);

  // Подключаем шаблон
  include $portfolio_template;

  // Убираем фильтр
  remove_all_filters('acf/load_value');
}
?>

<?php
get_footer('shop');
?>