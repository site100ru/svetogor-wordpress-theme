
/**
 * Скрипт для одиночной страницы портфолио
 * Управляет каруселью, превьюшками и лайтбоксом
 */

(function ($) {
    'use strict';

    // Проверяем, что данные портфолио доступны
    if (typeof portfolioData === 'undefined') {
        return;
    }

    const { portfolioId, galleryImages, hasMultipleImages } = portfolioData;

    // Глобальные переменные для лайтбокса
    let currentLightboxIndex = 0;

    /**
     * Смена слайда по клику на превьюшку
     */
    function changeSlide(slideIndex) {
        $(`#carousel-${portfolioId}`).carousel(slideIndex);

        // Обновляем активный класс у превьюшек
        $('.preview-image').each(function (index) {
            $(this).toggleClass('active', index === slideIndex);
        });
    }

    /**
     * Показ изображения в лайтбоксе
     */
    function showLightboxImage() {
        if (!galleryImages[currentLightboxIndex]) {
            return;
        }

        const currentImage = galleryImages[currentLightboxIndex];
        const $lightboxImage = $('#lightboxImage');
        const $counter = $('#lightboxCounter');

        if ($lightboxImage.length) {
            $lightboxImage.attr('src', currentImage.url);
            $lightboxImage.attr('alt', currentImage.alt);
        }

        if ($counter.length) {
            $counter.text(`${currentLightboxIndex + 1} / ${galleryImages.length}`);
        }
    }

    /**
     * Предыдущее изображение в лайтбоксе
     */
    window.lightboxPrev = function () {
        currentLightboxIndex = (currentLightboxIndex - 1 + galleryImages.length) % galleryImages.length;
        showLightboxImage();
    };

    /**
     * Следующее изображение в лайтбоксе
     */
    window.lightboxNext = function () {
        currentLightboxIndex = (currentLightboxIndex + 1) % galleryImages.length;
        showLightboxImage();
    };

    /**
     * Обработчик клавиш в лайтбоксе
     */
    window.handleLightboxKeydown = function (event) {
        switch (event.key) {
            case 'Escape':
                if (typeof galleryOff === 'function') {
                    galleryOff();
                }
                break;
            case 'ArrowLeft':
                if (galleryImages.length > 1) {
                    lightboxPrev();
                }
                break;
            case 'ArrowRight':
                if (galleryImages.length > 1) {
                    lightboxNext();
                }
                break;
        }
    };

    // Инициализация при загрузке страницы
    $(document).ready(function () {

        // Обработчик кликов на превьюшки
        $('.preview-image').on('click', function () {
            const slideIndex = parseInt($(this).data('slide-index'));
            if (!isNaN(slideIndex)) {
                changeSlide(slideIndex);
            }
        });

        // Автоматическое обновление активной превьюшки при смене слайда
        if (hasMultipleImages) {
            const carousel = document.getElementById(`carousel-${portfolioId}`);
            if (carousel) {
                carousel.addEventListener('slide.bs.carousel', function (event) {
                    const nextIndex = event.to;
                    $('.preview-image').each(function (index) {
                        $(this).toggleClass('active', index === nextIndex);
                    });
                });
            }
        }

        // Закрытие лайтбокса по клику на фон
        const lightbox = document.getElementById('portfolioLightbox');
        if (lightbox) {
            lightbox.addEventListener('click', function (event) {
                if (event.target === lightbox && typeof galleryOff === 'function') {
                    galleryOff();
                }
            });
        }
    });

})(jQuery);
