/**
 * Скрипт для управления галереей портфолио в админке
 * 
 * @version 1.0.0
 */

(function ($) {
    'use strict';

    var mediaUploader;

    // Обработчик добавления изображений в галерею
    $('#add-portfolio-gallery-images').on('click', function (e) {
        e.preventDefault();

        // Если загрузчик уже создан, просто открываем
        if (mediaUploader) {
            mediaUploader.open();
            return;
        }

        // Создаём медиа-загрузчик с поддержкой множественного выбора
        mediaUploader = wp.media({
            title: 'Выберите изображения для галереи',
            button: {
                text: 'Добавить в галерею'
            },
            multiple: true
        });

        // Обработчик выбора изображений
        mediaUploader.on('select', function () {
            var attachments = mediaUploader.state().get('selection').toJSON();
            var currentIds = $('#portfolio-gallery-ids').val().split(',').filter(Boolean);

            attachments.forEach(function (attachment) {
                // Проверяем, не добавлено ли изображение уже
                if (currentIds.indexOf(attachment.id.toString()) === -1) {
                    currentIds.push(attachment.id);

                    // Создаём HTML для превью изображения
                    var imageHtml = '<div class="gallery-image-item" data-id="' + attachment.id + '">';
                    imageHtml += '<img src="' + attachment.sizes.thumbnail.url + '" style="width: 100px; height: 100px; object-fit: cover;">';
                    imageHtml += '<button type="button" class="remove-gallery-image" data-id="' + attachment.id + '">×</button>';
                    imageHtml += '</div>';

                    $('#portfolio-gallery-images').append(imageHtml);
                }
            });

            // Обновляем скрытое поле с ID изображений
            $('#portfolio-gallery-ids').val(currentIds.join(','));
        });

        mediaUploader.open();
    });

    // Обработчик удаления изображения из галереи (делегированное событие)
    $(document).on('click', '.remove-gallery-image', function () {
        var imageId = $(this).data('id');
        var currentIds = $('#portfolio-gallery-ids').val().split(',').filter(Boolean);
        var index = currentIds.indexOf(imageId.toString());

        if (index > -1) {
            currentIds.splice(index, 1);
        }

        // Обновляем скрытое поле
        $('#portfolio-gallery-ids').val(currentIds.join(','));

        // Удаляем элемент из DOM
        $(this).parent().remove();
    });

})(jQuery);