/**
 * Универсальный скрипт для мета-бокса фонового изображения Hero-секции
 * Используется для: статей, новостей, услуг
 * 
 * @version 1.0.0
 */

(function ($) {
    'use strict';

    /**
     * Инициализация медиа-загрузчика для hero-background
     * @param {string} postType - Тип поста (article, news, service)
     */
    function initHeroBackgroundUploader(postType) {
        var mediaUploader;
        var selectButtonId = '#select-' + postType + '-hero-bg';
        var removeButtonId = '#remove-' + postType + '-hero-bg';
        var previewContainerId = '#' + postType + '-hero-bg-preview';
        var hiddenInputId = '#' + postType + '-hero-bg-id';

        // Обработчик кнопки "Выбрать изображение"
        $(selectButtonId).on('click', function (e) {
            e.preventDefault();

            // Если загрузчик уже создан, просто открываем
            if (mediaUploader) {
                mediaUploader.open();
                return;
            }

            // Создаём новый медиа-загрузчик
            mediaUploader = wp.media({
                title: 'Выберите фоновое изображение',
                button: {
                    text: 'Использовать как фон'
                },
                multiple: false
            });

            // Обработчик выбора изображения
            mediaUploader.on('select', function () {
                var attachment = mediaUploader.state().get('selection').first().toJSON();

                // Сохраняем ID изображения
                $(hiddenInputId).val(attachment.id);

                // Обновляем превью
                var imageUrl = attachment.sizes.medium ? attachment.sizes.medium.url : attachment.url;
                $(previewContainerId).html(
                    '<img src="' + imageUrl + '" style="width: 100%; max-height: 150px; object-fit: cover; border-radius: 4px;">'
                );

                // Показываем кнопку удаления
                $(removeButtonId).show();
            });

            mediaUploader.open();
        });

        // Обработчик кнопки "Удалить фон"
        $(removeButtonId).on('click', function () {
            // Очищаем скрытое поле
            $(hiddenInputId).val('');

            // Восстанавливаем placeholder
            $(previewContainerId).html(
                '<div style="width: 100%; height: 80px; background: #f0f0f0; border: 2px dashed #ccc; display: flex; align-items: center; justify-content: center; border-radius: 4px;">' +
                '<span style="color: #666;">Фон не выбран</span>' +
                '</div>'
            );

            // Скрываем кнопку удаления
            $(this).hide();
        });
    }

    // Инициализация при загрузке страницы
    $(document).ready(function () {
        // Автоопределение типа поста по наличию элементов
        if ($('#select-article-hero-bg').length) {
            initHeroBackgroundUploader('article');
        }

        if ($('#select-news-hero-bg').length) {
            initHeroBackgroundUploader('news');
        }

        if ($('#select-service-hero-bg').length) {
            initHeroBackgroundUploader('service');
        }
        
        if ($('#select-page-hero-bg').length) {
            initHeroBackgroundUploader('page');
        }
    });

})(jQuery);