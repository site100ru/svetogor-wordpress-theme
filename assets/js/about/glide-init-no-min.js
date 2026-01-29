/**
 * Оптимизированный универсальный инициализатор Glide.js
 */

(function() {
    'use strict';

    // Кэш для отслеживания инициализированных слайдеров
    const initializedSliders = new Set();

    /**
     * Универсальная функция инициализации Glide
     */
    const initGlide = function(selector, options) {
        try {
            // Проверяем валидность селектора
            if (!selector || typeof selector !== 'string') {
                return null;
            }

            // Проверяем, не инициализирован ли уже
            if (initializedSliders.has(selector)) {
                return null;
            }

            const element = document.querySelector(selector);
            
            // Проверяем существование элемента
            if (!element) {
                return null;
            }

            // Проверяем наличие слайдов
            const track = element.querySelector('.glide__track');
            const slides = element.querySelector('.glide__slides');
            
            if (!track || !slides) {
                return null;
            }

            const slideItems = slides.children;
            if (slideItems.length === 0) {
                return null;
            }

            // Показываем слайдер перед инициализацией
            element.style.opacity = '0';
            element.style.visibility = 'hidden';

            // Инициализируем Glide
            const glide = new Glide(selector, options);
            glide.mount();

            // Добавляем в кэш
            initializedSliders.add(selector);
            element.dataset.glideInitialized = 'true';

            // Плавное появление после инициализации
            requestAnimationFrame(() => {
                element.style.transition = 'opacity 0.3s ease';
                element.style.opacity = '1';
                element.style.visibility = 'visible';
            });

            return glide;

        } catch (error) {
            const element = document.querySelector(selector);
            if (element) {
                element.style.opacity = '1';
                element.style.visibility = 'visible';
            }
            return null;
        }
    };

    /**
     * Инициализация после полной загрузки DOM
     */
    const initializeSliders = function() {
        if (typeof Glide === 'undefined') {
            return;
        }

        // 1. Автоинициализация слайдеров с data-атрибутами
        document.querySelectorAll('.glide-auto:not([data-glide-initialized])').forEach(function(el) {
            const config = {
                type: el.dataset.glideType || 'carousel',
                perView: parseInt(el.dataset.glidePerview) || 3,
                gap: parseInt(el.dataset.glideGap) || 30,
                autoplay: el.dataset.glideAutoplay ? parseInt(el.dataset.glideAutoplay) : false,
                hoverpause: el.dataset.glideHoverpause !== 'false',
                animationDuration: 400,
                breakpoints: {
                    1024: {
                        perView: parseInt(el.dataset.glidePerviewMd) || Math.max(1, Math.floor(parseInt(el.dataset.glidePerview) * 0.66)),
                        gap: parseInt(el.dataset.glideGapMd) || parseInt(el.dataset.glideGap) || 20
                    },
                    768: {
                        perView: parseInt(el.dataset.glidePerviewSm) || 1,
                        gap: parseInt(el.dataset.glideGapSm) || 15
                    }
                }
            };

            // Используем ID или генерируем селектор
            const selector = el.id ? `#${el.id}` : `.glide-auto[data-glide-id="${Date.now()}"]`;
            if (!el.id) {
                el.dataset.glideId = Date.now();
            }

            initGlide(selector, config);
        });

        // 2. Статичные слайдеры (по фиксированным ID)
        const staticSliders = [
            { selector: '#partners-glide', config: {
                type: 'carousel', perView: 4, gap: 24,
                breakpoints: { 992: { perView: 3 }, 768: { perView: 1 } }
            }},
            { selector: '#section-works', config: {
                type: 'carousel', perView: 3, gap: 24,
                breakpoints: { 992: { perView: 2 }, 768: { perView: 1 } }
            }},
            { selector: '#section-product', config: {
                type: 'carousel', perView: 3, gap: 24,
                breakpoints: { 992: { perView: 2 }, 768: { perView: 1 } }
            }},
            { selector: '#clients-glide', config: {
                type: 'carousel', perView: 6, gap: 24,
                breakpoints: {
                    1400: { perView: 5 }, 1200: { perView: 4 },
                    992: { perView: 3 }, 768: { perView: 2 }, 590: { perView: 1 }
                }
            }},
            { selector: '#galleryGlide', config: {
                type: 'carousel', perView: 1, gap: 0
            }}
        ];

        staticSliders.forEach(slider => {
            if (document.querySelector(slider.selector)) {
                initGlide(slider.selector, slider.config);
            }
        });

        // 3. Динамические слайдеры (по префиксам ID)
        const dynamicSliders = [
            {
                selector: '[id^="clients-glide-"]:not([data-glide-initialized]), [id^="clients-slider-"]:not([data-glide-initialized])',
                config: {
                    type: 'carousel', perView: 6, gap: 24,
                    breakpoints: {
                        1400: { perView: 5 }, 1200: { perView: 4 },
                        992: { perView: 3 }, 768: { perView: 2 }, 590: { perView: 1 }
                    }
                }
            },
            {
                selector: '[id^="portfolio-slider-"]:not([data-glide-initialized])',
                config: {
                    type: 'carousel', perView: 3, gap: 30,
                    autoplay: 4000, hoverpause: true,
                    breakpoints: {
                        1024: { perView: 2, gap: 20 },
                        768: { perView: 1, gap: 15 }
                    }
                }
            },
            {
                selector: '[id^="complex-design-slider-"]:not([data-glide-initialized])',
                config: {
                    type: 'carousel', perView: 4, gap: 12,
                    breakpoints: {
                        992: { perView: 3 }, 768: { perView: 2 }, 590: { perView: 1 }
                    }
                }
            },
            {
                selector: '[id^="slider-category-"]:not([data-glide-initialized]), [id^="slider-light-"]:not([data-glide-initialized]), [id^="slider-contour"]:not([data-glide-initialized])',
                config: {
                    type: 'carousel', perView: 2, gap: 24,
                    breakpoints: { 767: { perView: 1 } }
                }
            }
        ];

        dynamicSliders.forEach(slider => {
            document.querySelectorAll(slider.selector).forEach(el => {
                if (el.id) {
                    initGlide(`#${el.id}`, slider.config);
                }
            });
        });

        // 4. Слайдеры товаров на архивных страницах
        document.querySelectorAll('.products-glide:not([data-glide-initialized])').forEach(el => {
            if (el.id) {
                initGlide(`#${el.id}`, {
                    type: 'carousel', perView: 2, gap: 20,
                    breakpoints: { 992: { perView: 2 }, 590: { perView: 1 } }
                });
            }
        });

        // 5. Fallback для оставшихся .glide элементов
        document.querySelectorAll('.glide:not(.glide-auto):not([data-glide-initialized])').forEach(el => {
            if (el.id) {
                initGlide(`#${el.id}`, {
                    type: 'carousel', perView: 3, gap: 24,
                    breakpoints: { 992: { perView: 2 }, 768: { perView: 1 } }
                });
            }
        });
    };

    // Запуск инициализации
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializeSliders);
    } else {
        // DOM уже загружен
        initializeSliders();
    }

    // Экспорт для глобального доступа (если нужно)
    window.initGlideSlider = initGlide;

})();