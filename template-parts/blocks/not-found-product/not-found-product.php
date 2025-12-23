<?php
/**
 * Block Name: Not Found Product
 * Description: Блок "Не нашли нужного товара?" с данными из настроек сайта
 */

global $how_custom_background_color;
$background_color = $how_custom_background_color ?: get_field('nfp_block_background_color_unique');

// Получаем данные из настроек сайта (options)
$title = get_field('not_found_product_title', 'option') ?: 'Не нашли нужного товара?';
$description = get_field('not_found_product_description', 'option') ?: 'Оставьте заявку и мы свяжемся с Вами в течение рабочего дня или напишите нам в мессенджер и мы проконсультируем Вас по всем вопросам касающимся нашей продукции.';
$background_image = get_field('not_found_product_image', 'option');
$button_text = get_field('not_found_button_text', 'option') ?: 'Получить консультацию';

// Определяем CSS класс для фона (по умолчанию bg-grey)
$bg_class = $background_color ?: 'bg-grey';

// Изображение по умолчанию
$default_image_path = get_template_directory_uri() . '/assets/img/order-bg.jpg';
$image_src = $background_image ? $background_image['url'] : $default_image_path;
$image_alt = $background_image ? $background_image['alt'] : 'Изображение';
?>

<!-- Не нашли нужного товара? -->
<section class="section section-half <?php echo esc_attr($bg_class); ?>">
  <div class="d-flex flex-wrap half-bg">
    <!-- Левая часть с фоном -->
    <div class="left-part flex-grow-1"></div>

    <!-- Правая часть с картинкой (скрывается на мобилках) -->
    <div class="right-part d-none d-md-block">
      <img loading="lazy" src="<?php echo esc_url($image_src); ?>" alt="<?php echo esc_attr($image_alt); ?>" class="img-cover" />
    </div>
  </div>

  <div class="container">
    <div class="row justify-content-center py-5">
      <div class="col-md-6 col-lg-5 text-md-end">
        <h2 class="mb-1"><?php echo esc_html($title); ?></h2>

        <div class="order-description mb-3">
          <?php echo wp_kses_post($description); ?>
        </div>

        <img loading="lazy" src="<?php echo get_template_directory_uri(); ?>/assets/img/ico/points.svg" alt="Точки" class="img-fluid mb-5" />

        <br>

        <?php if (!empty($button_text)): ?>
          <button type="button" class="btn mb-4" data-bs-toggle="modal" data-bs-target="#callbackModal">
            <?php echo esc_html($button_text); ?>
          </button>
        <?php endif; ?>

        <!-- Социальные сети из настроек (только те, что отмечены для блоков) -->
        <?php
        $social_networks = get_field('social_networks', 'option');
        if ($social_networks):
          $block_socials = array_filter($social_networks, function ($social) {
            return !empty($social['show_in_blocks']);
          });

          if (!empty($block_socials)):
            ?>
            <div class="row justify-content-md-end">
              <div class="col">
                <ul class="nav justify-content-md-end gap-3">
                  <?php foreach ($block_socials as $social): ?>
                    <li class="nav-item">
                      <a class="nav-link ico-button" href="<?php echo esc_url($social['url']); ?>"
                        title="<?php echo esc_attr($social['name']); ?>">
                        <img loading="lazy" src="<?php echo esc_url($social['icon']['url']); ?>" alt="<?php echo esc_attr($social['name']); ?>" />
                      </a>
                    </li>
                  <?php endforeach; ?>
                </ul>
              </div>
            </div>
          <?php endif; ?>
        <?php endif; ?>
      </div>

      <div class="col-md-6 col-lg-7"></div>
    </div>
  </div>
</section>