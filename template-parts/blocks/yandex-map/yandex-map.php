<?php
/**
 * Block Name: Яндекс Карта
 * Description: Блок с Яндекс Картой и маркером местоположения
 */

// Создаем уникальный ID для блока
$id = 'yandex-map-' . $block['id'];
$map_id = 'map-' . uniqid();

// Добавляем дополнительные классы, если они есть
$className = '';
if (!empty($block['className'])) {
  $className .= ' ' . $block['className'];
}

// Координаты маркера и центра карты 
$lat = 55.889916;
$lng = 37.634012;

// Путь к иконке маркера
$marker_icon = get_template_directory_uri() . '/assets/img/ico/placemark.png';
?>

<!-- Map -->
<div id="<?php echo esc_attr($id); ?>" class="container mb-5 <?php echo esc_attr($className); ?>">
  <div class="row">
    <div class="col" id="map2">
      <div id="<?php echo esc_attr($map_id); ?>"></div>
    </div>
  </div>
</div>
<!-- /Map -->

<script>
  document.addEventListener('DOMContentLoaded', function () {
    // Проверяем, загружен ли уже API Яндекс.Карт
    if (typeof ymaps === 'undefined') {
      // Если API еще не загружен, загружаем его
      var script = document.createElement('script');
      script.src = 'https://api-maps.yandex.ru/2.1/?apikey=7a322092-0e89-4de6-8bff-0a1795b5548e&lang=ru_RU';
      script.type = 'text/javascript';
      document.head.appendChild(script);

      script.onload = function () {
        // Инициализируем карту после загрузки API
        initYandexMap();
      };
    } else {
      // Если API уже загружен, просто инициализируем карту
      initYandexMap();
    }

    function initYandexMap() {
      ymaps.ready(function () {
        // Определяем размер экрана для масштаба
        var screenWidth = document.documentElement.clientWidth;
        var zoom = screenWidth > 1000 ? 17 : 15;

        // Координаты центра и маркера
        var center = [<?php echo esc_js($lat); ?>, <?php echo esc_js($lng); ?>];

        // Создание карты
        var myMap = new ymaps.Map('<?php echo esc_js($map_id); ?>', {
          center: center,
          zoom: zoom,
        });

        // Создание маркера
        var myPlacemark = new ymaps.Placemark(
          center,
          {},
          {
            iconLayout: 'default#image',
            iconImageHref: '<?php echo esc_js($marker_icon); ?>',
            iconImageSize: [240, 240],
            iconImageOffset: [-143, -230],
          }
        );

        // Отключаем прокрутку колесиком мыши
        myMap.behaviors.disable('scrollZoom');

        // Добавляем маркер на карту
        myMap.geoObjects.add(myPlacemark);
      });
    }
  });
</script>