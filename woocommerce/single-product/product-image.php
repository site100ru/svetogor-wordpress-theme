<?php
/**
 * Single Product Image
 */

defined('ABSPATH') || exit;

if (!function_exists('wc_get_gallery_image_html')) {
  return;
}

global $product;

$product_id = $product->get_id();
$attachment_ids = $product->get_gallery_image_ids();
$main_image_id = $product->get_image_id();

// Добавляем главное изображение в НАЧАЛО массива (если есть)
if ($main_image_id) {
  array_unshift($attachment_ids, $main_image_id);
}

// Удаляем дубликаты
$attachment_ids = array_unique($attachment_ids);

if (empty($attachment_ids)) {
  return;
}

$carousel_id = 'carousel-' . $product_id;
?>

<div id="<?php echo esc_attr($carousel_id); ?>" class="carousel slide" data-bs-ride="false" data-bs-interval="false">
  <div class="carousel-inner rounded">
    <?php foreach ($attachment_ids as $index => $attachment_id): ?>
      <?php
      $image_url = wp_get_attachment_image_url($attachment_id, 'large');
      $image_alt = get_post_meta($attachment_id, '_wp_attachment_image_alt', true);
      if (!$image_alt) {
        $image_alt = get_the_title($product_id);
      }
      ?>
      <!-- Карточка <?php echo $index + 1; ?> -->
      <div
        class="carousel-item gallery-product-wrapper gallery-<?php echo esc_attr($product_id); ?>-wrapper <?php echo $index === 0 ? 'active' : ''; ?>">
        <button class="gallery-product gallery-<?php echo esc_attr($product_id); ?> bg-transparent"
          onclick="galleryOn('gallery-<?php echo esc_attr($product_id); ?>');">
          <div class="single-product-img approximation img-wrapper position-relative">
            <img loading="lazy" src="<?php echo esc_url($image_url); ?>" class="d-block w-100 h-100" alt="<?php echo esc_attr($image_alt); ?>">
          </div>
        </button>
      </div>
    <?php endforeach; ?>
  </div>

  <?php if (count($attachment_ids) > 1): ?>
    <button class="carousel-control-prev" type="button" data-bs-target="#<?php echo esc_attr($carousel_id); ?>"
      data-bs-slide="prev">
      <span class="carousel-control-prev-icon" aria-hidden="true"></span>
      <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#<?php echo esc_attr($carousel_id); ?>"
      data-bs-slide="next">
      <span class="carousel-control-next-icon" aria-hidden="true"></span>
      <span class="visually-hidden">Next</span>
    </button>
  <?php endif; ?>
</div>

<?php if (count($attachment_ids) > 1): ?>
  <!-- Превьюшки изображений -->
  <div class="mt-3 product-section-preview">
    <?php
    $chunks = array_chunk($attachment_ids, 5);
    foreach ($chunks as $chunk_index => $chunk): ?>
      <div class="row mb-2">
        <?php foreach ($chunk as $index => $attachment_id): 
          $global_index = $chunk_index * 5 + $index;
          $thumb_url = wp_get_attachment_image_url($attachment_id, 'thumbnail');
          $image_alt = get_post_meta($attachment_id, '_wp_attachment_image_alt', true);
          if (!$image_alt) {
            $image_alt = 'Превью ' . ($global_index + 1);
          }
        ?>
          <div class="col">
            <img loading="lazy" src="<?php echo esc_url($thumb_url); ?>"
              class="img-fluid rounded cursor-pointer preview-image shadow-box <?php echo $global_index === 0 ? 'active' : ''; ?>"
              onclick="$('#<?php echo esc_attr($carousel_id); ?>').carousel(<?php echo $global_index; ?>)"
              alt="<?php echo esc_attr($image_alt); ?>">
          </div>
        <?php endforeach; ?>
      </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>