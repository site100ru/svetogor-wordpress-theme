<?php
/**
 * Block Name: WooCommerce Category Products
 * Description: Блок для вывода товаров из выбранной категории WooCommerce
 */

// Проверяем что WooCommerce активен
if (!class_exists('WooCommerce')) {
  echo '<p>WooCommerce не активен</p>';
  return;
}

// Получаем поля блока
$selected_category = get_field('wc_category_block_selected_category_unique');
$products_count = get_field('wc_category_block_products_count_unique') ?: 3;
$background_color = get_field('wc_category_block_bg_color_unique_2024') ?: 'section-glide';
$show_price = get_field('wc_category_block_show_price_unique');
$prev_arrow = get_field('carousel_prev_arrow', 'option');
$next_arrow = get_field('carousel_next_arrow', 'option');

// Если категория не выбрана, показываем сообщение
if (!$selected_category) {
  echo '<p>Пожалуйста, выберите категорию товаров в настройках блока</p>';
  return;
}

// Получаем товары из выбранной категории
$args = array(
  'post_type' => 'product',
  'posts_per_page' => $products_count,
  'post_status' => 'publish',
  'tax_query' => array(
    array(
      'taxonomy' => 'product_cat',
      'field' => 'term_id',
      'terms' => $selected_category->term_id,
    ),
  ),
);

$products = new WP_Query($args);

if (!$products->have_posts()) {
  echo '<p>В данной категории товары не найдены</p>';
  return;
}

// Генерируем уникальный ID для слайдера
$slider_id = 'slider-category-' . $selected_category->term_id . '-' . uniqid();

// Принудительно подключаем стили и скрипты для блока портфолио
wp_enqueue_script('portfolio-slider-js', get_template_directory_uri() . '/template-parts/blocks/portfolio-slider/portfolio-slider.js', array('jquery'), filemtime(get_template_directory() . '/template-parts/blocks/portfolio-slider/portfolio-slider.js'), true);
?>

<!--КОНТЕНТ-->
<section
  class="section <?php echo esc_attr($background_color); ?> section-catalog-product box-shadow-main section-catalog-product-list">
  <div class="container">
    <!-- <?php echo esc_html($selected_category->name); ?> -->
    <div class="section-content-cards">
      <div class="section-title text-center">
        <h3><?php echo esc_html($selected_category->name); ?></h3>
        <img loading="lazy" src="<?php echo get_template_directory_uri(); ?>/assets/img/ico/points.svg" alt="Точки" class="img-fluid">
      </div>

      <!-- Десктопная версия -->
      <div class="row justify-content-center d-none d-lg-flex">
        <?php while ($products->have_posts()):
          $products->the_post(); ?>
          <?php
          global $product;
          $product_image = wp_get_attachment_image_src(get_post_thumbnail_id(), 'full');
          $product_image_url = $product_image ? $product_image[0] : wc_placeholder_img_src();
          ?>
          <!-- Карточка товара -->
          <article class="col-lg-4">
            <a href="<?php echo esc_url(get_permalink()); ?>" class="card card-img-container">
              <div class="card-img-container">
                <img loading="lazy" src="<?php echo esc_url($product_image_url); ?>" alt="<?php echo esc_attr(get_the_title()); ?>" class="card-img-top">
              </div>
              <div class="card-body">
                <h5 class="card-title"><?php the_title(); ?></h5>
                <p class="card-text">
                  <?php echo wp_trim_words(get_the_excerpt(), 15, '...'); ?>
                </p>
                <div class="d-flex justify-content-between align-items-center mt-auto">
                  <?php if ($show_price && $product->get_price()): ?>
                    <span class="product-price"><?php echo $product->get_price_html(); ?></span>
                  <?php else: ?>
                    <span></span>
                  <?php endif; ?>
                  <button type="button" class="btn btn-order btn-min" data-bs-toggle="modal"
                    data-bs-target="#callbackModalFour" data-product-id="<?php echo get_the_ID(); ?>"
                    data-product-name="<?php echo esc_attr(get_the_title()); ?>"
                    onclick="event.preventDefault(); event.stopPropagation();">
                    Заказать
                  </button>
                </div>
              </div>
            </a>
          </article>
        <?php endwhile; ?>
      </div>

      <!-- Слайдер для планшетов и мобильных -->
      <div class="d-block d-lg-none">
        <div class="products-glide light-letters-slider" id="<?php echo esc_attr($slider_id); ?>">
          <div class="glide__track" data-glide-el="track">
            <div class="glide__slides">
              <?php
              // Сбрасываем запрос для слайдера
              $products->rewind_posts();
              while ($products->have_posts()):
                $products->the_post();
                ?>
                <?php
                global $product;
                $product_image = wp_get_attachment_image_src(get_post_thumbnail_id(), 'full');
                $product_image_url = $product_image ? $product_image[0] : wc_placeholder_img_src();
                ?>
                <!-- Слайд -->
                <article class="glide__slide">
                  <a href="<?php echo esc_url(get_permalink()); ?>" class="card card-img-container">
                    <div class="card-img-container">
                      <img loading="lazy" src="<?php echo esc_url($product_image_url); ?>" alt="<?php echo esc_attr(get_the_title()); ?>" class="card-img-top">
                    </div>
                    <div class="card-body">
                      <h5 class="card-title"><?php the_title(); ?></h5>
                      <p class="card-text">
                        <?php echo wp_trim_words(get_the_excerpt(), 15, '...'); ?>
                      </p>
                      <div class="d-flex justify-content-between align-items-center mt-auto">
                        <?php if ($show_price && $product->get_price()): ?>
                          <span class="product-price"><?php echo $product->get_price_html(); ?></span>
                        <?php else: ?>
                          <span></span>
                        <?php endif; ?>
                        <button type="button" class="btn btn-order btn-min" data-bs-toggle="modal"
                          data-bs-target="#callbackModalFour" data-product-id="<?php echo get_the_ID(); ?>"
                          data-product-name="<?php echo esc_attr(get_the_title()); ?>"
                          onclick="event.preventDefault(); event.stopPropagation();">
                          Заказать
                        </button>
                      </div>
                    </div>
                  </a>
                </article>
              <?php endwhile; ?>
            </div>
          </div>
          <!-- Стрелки навигации -->
          <div class="glide__arrows" data-glide-el="controls">
            <button class="glide__arrow glide__arrow--left btn-carousel-left" data-glide-dir="&lt;">
              <img loading="lazy"
                src="<?php echo esc_url(isset($prev_arrow['url']) ? $prev_arrow['url'] : get_template_directory_uri() . '/assets/img/ico/arrow-left.svg'); ?>"
                alt="Назад">
            </button>
            <button class="glide__arrow glide__arrow--right btn-carousel-right" data-glide-dir="&gt;">
              <img loading="lazy"
                src="<?php echo esc_url(isset($next_arrow['url']) ? $next_arrow['url'] : get_template_directory_uri() . '/assets/img/ico/arrow-right.svg'); ?>"
                alt="Вперед">
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>



<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Обработчик для кнопок "Заказать"
        const orderButtons = document.querySelectorAll('.btn-order[data-product-id]');

        orderButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                const productId = this.getAttribute('data-product-id');
                const productName = this.getAttribute('data-product-name');

                // Заполняем скрытые поля в модальной форме
                const modal = document.querySelector('#callbackModalFour');
                if (modal) {
                    const productIdField = modal.querySelector('input[name="product-id"]');
                    const productNameField = modal.querySelector('input[name="product-name"]');

                    if (productIdField) productIdField.value = productId;
                    if (productNameField) productNameField.value = productName;

                    // Обновляем заголовок модалки
                    const modalTitle = modal.querySelector('.modal-title');
                    if (modalTitle) {
                        modalTitle.textContent = 'Заказать: ' + productName;
                    }
                }
            });
        });
    });
</script>

<?php
// Сбрасываем глобальные данные
wp_reset_postdata();
?>