<?php
/**
 * Универсальный шаблон галереи портфолио
 * Используется в archive-portfolio.php, taxonomy-portfolio_category.php и portfolio-slider.php
 */

// Параметры по умолчанию
$defaults = array(
  'modal_id' => 'portfolioGalleryModal',
  'container_id' => 'dynamic-carousel-container',
  'slider_id' => 'default',  // Изменено с пустой строки на 'default'
  'carousel_class' => 'product-carousel',
  'image_max_width' => '75vw',
  'image_max_height' => '75vh',
  'show_alt_text' => true,
  'alt_text_default' => 'Изображение портфолио',
  'loading_text' => 'Загрузка...',
  'no_images_text' => 'Изображения не найдены',
  'error_text' => 'Ошибка загрузки'
);

// Объединяем переданные параметры с настройками по умолчанию
$config = wp_parse_args($args ?? array(), $defaults);

// Если передан slider_id, добавляем его к ID элементов
if (!empty($config['slider_id']) && $config['slider_id'] !== 'default') {
  $config['modal_id'] .= '-' . $config['slider_id'];
  $config['container_id'] .= '-' . $config['slider_id'];
}

// Генерируем nonce для AJAX безопасности
$ajax_nonce = wp_create_nonce('portfolio_gallery_nonce');
?>

<!-- Модальное окно для галереи портфолио -->
<div id="<?php echo esc_attr($config['modal_id']); ?>" class="portfolio-gallery-modal" style="
        background: rgba(0, 0, 0, 0.85);
        display: none;
        position: fixed;
        top: 0;
        bottom: 0;
        left: 0;
        right: 0;
        z-index: 9999;
    ">
  <!-- Динамический слайдер будет загружаться здесь -->
  <div id="<?php echo esc_attr($config['container_id']); ?>"></div>

  <!-- Кнопка закрытия галереи -->
  <button type="button" onclick="closePortfolioGallery('<?php echo esc_attr($config['slider_id']); ?>');"
    class="btn-close btn-close-white" style="position: fixed; top: 25px; right: 25px; z-index: 99999"
    aria-label="Close"></button>
</div>

<script>
  // Конфигурация для текущего экземпляра галереи
  window.portfolioGalleryConfig = window.portfolioGalleryConfig || {};
  window.portfolioGalleryConfig['<?php echo esc_js($config['slider_id']); ?>'] = {
    modalId: '<?php echo esc_js($config['modal_id']); ?>',
    containerId: '<?php echo esc_js($config['container_id']); ?>',
    sliderId: '<?php echo esc_js($config['slider_id']); ?>',
    carouselClass: '<?php echo esc_js($config['carousel_class']); ?>',
    imageMaxWidth: '<?php echo esc_js($config['image_max_width']); ?>',
    imageMaxHeight: '<?php echo esc_js($config['image_max_height']); ?>',
    showAltText: <?php echo $config['show_alt_text'] ? 'true' : 'false'; ?>,
    altTextDefault: '<?php echo esc_js($config['alt_text_default']); ?>',
    loadingText: '<?php echo esc_js($config['loading_text']); ?>',
    noImagesText: '<?php echo esc_js($config['no_images_text']); ?>',
    errorText: '<?php echo esc_js($config['error_text']); ?>',
    ajaxUrl: '<?php echo admin_url('admin-ajax.php'); ?>',
    nonce: '<?php echo $ajax_nonce; ?>'
  };

  // Универсальные функции для работы с галереей
  if (typeof window.openPortfolioGallery === 'undefined') {
    // Функция открытия модального окна с загрузкой галереи через AJAX
    window.openPortfolioGallery = function (index, postId, sliderId = 'default') {
      // Ищем конфигурацию для данного sliderId
      let config = window.portfolioGalleryConfig[sliderId];

      if (!config) {
        // Если не найдена, пробуем 'default'
        config = window.portfolioGalleryConfig['default'];
      }

      if (!config) {
        // Если и 'default' нет, берем первую доступную
        const configKeys = Object.keys(window.portfolioGalleryConfig);
        if (configKeys.length > 0) {
          config = window.portfolioGalleryConfig[configKeys[0]];
        }
      }

      if (!config) {
        return;
      }


      // Показываем модальное окно
      const modal = document.getElementById(config.modalId);
      if (!modal) {
        return;
      }
      modal.style.display = 'block';

      // Показываем индикатор загрузки
      const container = document.getElementById(config.containerId);
      if (!container) {
        return;
      }
      container.innerHTML = `<div style="position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); color: white; font-size: 18px;">${config.loadingText}</div>`;

      // AJAX запрос для получения изображений галереи
      const url = `${config.ajaxUrl}?action=get_portfolio_gallery&post_id=${postId}&nonce=${config.nonce}`;

      fetch(url)
        .then(response => response.json())
        .then(data => {
          if (data.success && data.data.length > 0) {
            window.createPortfolioCarousel(data.data, index, sliderId);
          } else {
            container.innerHTML = `<div style="position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); color: white; font-size: 18px;">${config.noImagesText}</div>`;
          }
        })
        .catch(error => {
          container.innerHTML = `<div style="position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); color: white; font-size: 18px;">${config.errorText}</div>`;
        });

      // Добавляем обработчик закрытия по клавише Esc
      const escHandler = function (event) {
        if (event.key === 'Escape' || event.keyCode === 27) {
          window.closePortfolioGallery(sliderId);
        }
      };

      // Сохраняем ссылку на обработчик для последующего удаления
      modal.escHandler = escHandler;
      document.addEventListener('keydown', escHandler);
    };

    // Функция создания карусели
    window.createPortfolioCarousel = function (images, index, sliderId = 'default') {
      // Ищем конфигурацию для данного sliderId
      let config = window.portfolioGalleryConfig[sliderId];

      if (!config) {
        config = window.portfolioGalleryConfig['default'];
      }

      if (!config) {
        const configKeys = Object.keys(window.portfolioGalleryConfig);
        if (configKeys.length > 0) {
          config = window.portfolioGalleryConfig[configKeys[0]];
        }
      }

      if (!config) {
        console.error('Portfolio gallery config not found for sliderId:', sliderId);
        return;
      }

      const carouselId = `dynamic-portfolio-carousel-${sliderId}-${index}`;
      let carouselHTML = `
        <div id="${carouselId}" class="${config.carouselClass} carousel slide" data-bs-ride="false" data-bs-interval="false" style="position: fixed; top: 0; height: 100%; width: 100%;">
            <div class="carousel-inner h-100">
      `;

      // Создаем слайды
      images.forEach((image, i) => {
        const activeClass = i === 0 ? 'active' : '';
        const altText = config.showAltText ? (image.alt || config.altTextDefault) : '';
        carouselHTML += `
            <div class="carousel-item h-100 ${activeClass}">
                <div class="row align-items-center h-100">
                    <div class="col text-center">
                        <img src="${image.url}" class="img-fluid" loading="lazy" 
                             style="max-width: ${config.imageMaxWidth}; max-height: ${config.imageMaxHeight}" 
                             alt="${altText}">
                        ${config.showAltText ? `<p style="color: #c8c8c8">${altText}</p>` : ''}
                    </div>
                </div>
            </div>
        `;
      });

      carouselHTML += '</div>';

      // Добавляем индикаторы если больше одного изображения
      if (images.length > 1) {
        carouselHTML += '<div class="carousel-indicators">';
        images.forEach((image, i) => {
          const activeClass = i === 0 ? 'active' : '';
          const ariaCurrent = i === 0 ? 'aria-current="true"' : '';
          carouselHTML += `
                <button type="button" data-bs-target="#${carouselId}" data-bs-slide-to="${i}" 
                        aria-label="Slide ${i + 1}" class="${activeClass}" ${ariaCurrent}></button>
            `;
        });
        carouselHTML += '</div>';

        // Добавляем кнопки навигации
        carouselHTML += `
            <button class="carousel-control-prev" type="button" data-bs-target="#${carouselId}" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#${carouselId}" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        `;
      }

      carouselHTML += '</div>';

      // Вставляем HTML в контейнер
      const container = document.getElementById(config.containerId);
      if (container) {
        container.innerHTML = carouselHTML;

        // Инициализируем Bootstrap карусель если больше одного изображения
        if (images.length > 1 && typeof bootstrap !== 'undefined') {
          const carouselElement = document.getElementById(carouselId);
          if (carouselElement) {
            new bootstrap.Carousel(carouselElement);
          }
        }
      }
    };

    // Функция закрытия модального окна
    window.closePortfolioGallery = function (sliderId = 'default') {
      // Ищем конфигурацию для данного sliderId
      let config = window.portfolioGalleryConfig[sliderId];

      if (!config) {
        config = window.portfolioGalleryConfig['default'];
      }

      if (!config) {
        const configKeys = Object.keys(window.portfolioGalleryConfig);
        if (configKeys.length > 0) {
          config = window.portfolioGalleryConfig[configKeys[0]];
        }
      }

      if (!config) {
        console.error('Portfolio gallery config not found for sliderId:', sliderId);
        return;
      }

      const modal = document.getElementById(config.modalId);
      const container = document.getElementById(config.containerId);

      if (modal) {
        modal.style.display = 'none';

        // Удаляем обработчик клавиши Esc
        if (modal.escHandler) {
          document.removeEventListener('keydown', modal.escHandler);
          delete modal.escHandler;
        }
      }

      if (container) {
        container.innerHTML = '';
      }
    };

    // Обратная совместимость со старыми функциями
    window.openProductGallery = function (index, postId) {
      window.openPortfolioGallery(index, postId, 'default');
    };

    window.closeProductGallery = function () {
      window.closePortfolioGallery('default');
    };

    window.openPortfolioSlider = function (index, postId, sliderId) {
      window.openPortfolioGallery(index, postId, sliderId);
    };

    window.closePortfolioSliderModal = function (sliderId) {
      window.closePortfolioGallery(sliderId);
    };
  }

  // Добавляем обработчик при загрузке страницы
  document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('<?php echo esc_js($config['modal_id']); ?>');
    if (modal) {
      // Закрываем модальное окно при клике на фон
      modal.addEventListener('click', function (event) {
        if (event.target === modal) {
          window.closePortfolioGallery('<?php echo esc_js($config['slider_id']); ?>');
        }
      });
    } else {
      console.error('Portfolio Gallery: Модальное окно не найдено:', '<?php echo esc_js($config['modal_id']); ?>');
    }
  });
</script>