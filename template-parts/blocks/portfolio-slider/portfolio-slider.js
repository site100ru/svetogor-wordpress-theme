/**
 * Универсальный JavaScript для портфолио
 */

// Глобальные функции для работы с галереей портфолио
window.openPortfolioGallery = openPortfolioGallery;
window.closePortfolioGallery = closePortfolioGallery;
window.createPortfolioCarousel = createPortfolioCarousel;
window.handlePortfolioEscKeyPress = handlePortfolioEscKeyPress;

// Функция открытия модального окна с загрузкой галереи через AJAX
function openPortfolioGallery(index, postId, modalSuffix = '') {
    const modalId = modalSuffix ? 'portfolioModal-' + modalSuffix : 'productGalleryModal';
    const containerId = modalSuffix ? 'dynamic-carousel-container-' + modalSuffix : 'dynamic-carousel-container';

    // Показываем модальное окно
    const modal = document.getElementById(modalId);
    if (!modal) {
        return;
    }
    modal.style.display = 'block';

    // Показываем индикатор загрузки
    const container = document.getElementById(containerId);
    if (!container) {
        return;
    }
    container.innerHTML = '<div style="position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); color: white; font-size: 18px;">Загрузка...</div>';

    // Определяем URL для AJAX запроса
    let ajaxUrl;
    if (typeof portfolio_ajax !== 'undefined' && portfolio_ajax.ajax_url) {
        ajaxUrl = portfolio_ajax.ajax_url;
    } else if (typeof wp !== 'undefined' && wp.ajax && wp.ajax.settings) {
        ajaxUrl = wp.ajax.settings.url;
    } else {
        // Fallback для WordPress
        ajaxUrl = window.location.origin + '/wp-admin/admin-ajax.php';
    }

    // Получаем nonce
    let nonce = '';
    if (typeof portfolio_ajax !== 'undefined' && portfolio_ajax.nonce) {
        nonce = '&nonce=' + portfolio_ajax.nonce;
    }

    // AJAX запрос для получения изображений галереи
    const url = ajaxUrl + '?action=get_portfolio_gallery&post_id=' + postId + nonce;

    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data && data.data.length > 0) {
                createPortfolioCarousel(data.data, index, modalSuffix);
            } else {
                container.innerHTML = '<div style="position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); color: white; font-size: 18px;">Изображения не найдены</div>';
            }
        })
        .catch(error => {
            container.innerHTML = '<div style="position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); color: white; font-size: 18px;">Ошибка загрузки</div>';
        });

    // Добавляем обработчик закрытия по клавише Esc
    document.addEventListener('keydown', function (event) {
        handlePortfolioEscKeyPress(event, modalSuffix);
    });
}

// Функция создания карусели
function createPortfolioCarousel(images, index, modalSuffix = '') {

    const containerId = modalSuffix ? 'dynamic-carousel-container-' + modalSuffix : 'dynamic-carousel-container';
    const carouselId = modalSuffix ? 'dynamic-portfolio-carousel-' + modalSuffix + '-' + index : 'dynamic-product-carousel-' + index;

    let carouselHTML = `
    <div id="${carouselId}" class="product-carousel carousel slide" data-bs-ride="false" data-bs-interval="false" style="position: fixed; top: 0; height: 100%; width: 100%;">
      <div class="carousel-inner h-100">
  `;

    // Создаем слайды
    images.forEach((image, i) => {
        const activeClass = i === 0 ? 'active' : '';
        carouselHTML += `
      <div class="carousel-item h-100 ${activeClass}">
        <div class="row align-items-center h-100">
          <div class="col text-center">
            <img src="${image.url}" class="img-fluid mb-3" loading="lazy" 
                 style="max-width: 90%; max-height: 90%" 
                 alt="${image.alt || 'Изображение портфолио'}" />
            <p class="mb-0" style="color: #c8c8c8">${image.alt || 'Галерея изображения'}</p>
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
    const container = document.getElementById(containerId);
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
}

// Функция закрытия модального окна
function closePortfolioGallery(modalSuffix = '') {

    const modalId = modalSuffix ? 'portfolioModal-' + modalSuffix : 'productGalleryModal';
    const containerId = modalSuffix ? 'dynamic-carousel-container-' + modalSuffix : 'dynamic-carousel-container';

    const modal = document.getElementById(modalId);
    const container = document.getElementById(containerId);

    if (modal) {
        modal.style.display = 'none';
    }

    if (container) {
        container.innerHTML = '';
    }

    // Удаляем обработчик клавиши Esc
    document.removeEventListener('keydown', function (event) {
        handlePortfolioEscKeyPress(event, modalSuffix);
    });
}

// Функция-обработчик нажатия клавиши Esc
function handlePortfolioEscKeyPress(event, modalSuffix = '') {
    if (event.key === 'Escape' || event.keyCode === 27) {
        closePortfolioGallery(modalSuffix);
    }
}

// Функции для обратной совместимости со старыми шаблонами
window.openProductGallery = function (index, postId) {
    openPortfolioGallery(index, postId, '');
};

window.closeProductGallery = function () {
    closePortfolioGallery('');
};

window.createCarousel = function (images, index) {
    createPortfolioCarousel(images, index, '');
};

window.handleEscKeyPress = function (event) {
    handlePortfolioEscKeyPress(event, '');
};

// Функции для слайдера блока
window.openPortfolioSlider = function (index, postId, sliderId) {
    openPortfolioGallery(index, postId, sliderId);
};

window.closePortfolioSliderModal = function (sliderId) {
    closePortfolioGallery(sliderId);
};