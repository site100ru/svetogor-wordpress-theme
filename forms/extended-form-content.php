<!-- forms/extended-form-content.php - только содержимое формы -->
<?php
$id_prefix = isset($id_prefix) ? $id_prefix : 'form-' . uniqid();
?>

<!-- Первая строка: Имя, Телефон, Почта -->
<div class="row mb-4">
    <div class="col-md-4 col-12 mb-3 mb-md-0">
        <label for="<?php echo $id_prefix; ?>-name-input" class="form-label">Имя</label>
        <input type="text" class="form-control" name="your-name" id="<?php echo $id_prefix; ?>-name-input" required>
    </div>
    <div class="col-md-4 col-12 mb-3 mb-md-0">
        <label for="<?php echo $id_prefix; ?>-phone-input" class="form-label">Телефон</label>
        <input type="tel" class="form-control telMask" name="your-phone" id="<?php echo $id_prefix; ?>-phone-input" required>
    </div>
    <div class="col-md-4 col-12">
        <label for="<?php echo $id_prefix; ?>-email-input" class="form-label">Email</label>
        <input type="email" class="form-control" name="your-email" id="<?php echo $id_prefix; ?>-email-input" required>
    </div>
</div>

<!-- Вторая строка: Чекбоксы с картинками -->
<div class="row mb-4">
    <p>Хочу получить расчет на:</p>
    <div class="d-flex gap-4">
        <label class="image-checkbox-container">
            <input type="checkbox" name="contact-methods[]" value="WhatsApp">
            <span class="custom-checkbox"></span>
            <img loading="lazy" src="<?php echo get_template_directory_uri(); ?>/assets/img/ico/whatsapp.svg" alt="WhatsApp">
        </label>
        <label class="image-checkbox-container">
            <input type="checkbox" name="contact-methods[]" value="Telegram">
            <span class="custom-checkbox"></span>
            <img loading="lazy" src="<?php echo get_template_directory_uri(); ?>/assets/img/ico/telegram.svg" alt="Telegram">
        </label>
        <label class="image-checkbox-container">
            <input type="checkbox" name="contact-methods[]" value="Email">
            <span class="custom-checkbox"></span>
            <img loading="lazy" src="<?php echo get_template_directory_uri(); ?>/assets/img/ico/message.svg" alt="Email">
        </label>
    </div>
</div>

<!-- Третья строка: Тип продукции -->
<div class="row mb-4">
    <div class="col-md-8 col-12">
        <label class="form-label">Тип продукции</label>
        <select class="form-select form-control" name="product-type" required>
            <option value="" selected="">Выберите тип продукции</option>
            <?php
            // Сначала найдем родительскую категорию "products"
            $parent_category = get_term_by('slug', 'products', 'product_cat');
            if (!$parent_category) {
                // Если не нашли по slug "products", попробуем "product"
                $parent_category = get_term_by('slug', 'product', 'product_cat');
            }

            if ($parent_category) {
                // Получаем только прямые дочерние категории
                $product_categories = get_terms(array(
                    'taxonomy' => 'product_cat',
                    'parent' => $parent_category->term_id,  // Только дети этой категории
                    'hide_empty' => false,
                ));

                if (!empty($product_categories) && !is_wp_error($product_categories)) {
                    foreach ($product_categories as $category) {
                        echo '<option value="' . esc_attr($category->name) . '">' . esc_html($category->name) . '</option>';
                    }
                }
            } else {
                // Fallback опции если категории не найдены
                echo '<option value="Буквы">Буквы</option>';
                echo '<option value="Крышные установки">Крышные установки</option>';
                echo '<option value="Конструкции для выставок">Конструкции для выставок</option>';
                echo '<option value="Тонкие световые панели">Тонкие световые панели</option>';
                echo '<option value="Стеллы, пилоны, доски почета">Стеллы, пилоны, доски почета</option>';
                echo '<option value="Опен-боксы для афиш, меню">Опен-боксы для афиш, меню</option>';
                echo '<option value="Световые короба">Световые короба</option>';
                echo '<option value="Панель-кронштейны">Панель-кронштейны</option>';
                echo '<option value="Домовые знаки">Домовые знаки</option>';
                echo '<option value="Светильники">Светильники</option>';
                echo '<option value="Штендеры, ролл-апы">Штендеры, ролл-апы</option>';
                echo '<option value="Таблички, стенды">Таблички, стенды</option>';
                echo '<option value="Уличные информстенды">Уличные информстенды</option>';
                echo '<option value="Торговые точки">Торговые точки</option>';
                echo '<option value="Неоновая реклама">Неоновая реклама</option>';
                echo '<option value="Услуги">Услуги</option>';
            }
            ?>
        </select>
    </div>
</div>

<!-- Четвертая строка: Текстареа -->
<div class="row mb-4">
    <p class="mb-2">Размеры, материалы и другие детали</p>
    <div class="col-12">
        <textarea class="form-control" name="your-message" rows="4"></textarea>
    </div>
</div>

<!-- Пятая строка: Дополнительные услуги -->
<div class="row mb-4">
    <p class="form-label">Так же мне нужно:</p>
    <div class="col-12 d-flex gap-4 flex-wrap">
        <label class="custom-checkbox-container">
            <input type="checkbox" name="additional-services[]" value="Выезд на замер">
            <span class="custom-checkbox"></span>
            <span>Выезд на замер</span>
        </label>
        <label class="custom-checkbox-container">
            <input type="checkbox" name="additional-services[]" value="Разработка дизайна">
            <span class="custom-checkbox"></span>
            <span>Разработка дизайна</span>
        </label>
        <label class="custom-checkbox-container">
            <input type="checkbox" name="additional-services[]" value="Монтаж">
            <span class="custom-checkbox"></span>
            <span>Монтаж</span>
        </label>
        <label class="custom-checkbox-container">
            <input type="checkbox" name="additional-services[]" value="Доставка">
            <span class="custom-checkbox"></span>
            <span>Доставка</span>
        </label>
    </div>
</div>

<!-- Шестая строка: Прикрепить файл -->
<div class="row mb-4">
    <div class="col-md-6 col-xl-4 col-12">
        <div class="file-upload-wrapper w-100 mb-0">
            <button type="button" class="btn btn-invert btn-big file-upload-btn">
                Прикрепить файл
            </button>
            <input type="file" name="your-file" class="file-upload">
            <span class="file-name">Файл не прикреплен</span>
        </div>
    </div>
</div>

<!-- Скрытые поля -->
<input type="hidden" name="page-url" value="">
<input type="hidden" name="page-title" value="">

<input type="hidden" name="referrer" value="">
<input type="hidden" name="g-recaptcha-response" value="">

<!-- Кнопка отправки -->
<div class="row">
    <div class="col-12">
        <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" id="<?php echo $id_prefix; ?>-privacy-check" checked>
            <label class="form-check-label" for="<?php echo $id_prefix; ?>-privacy-check">
                <small>Даю согласие на обработку персональных данных. Подробнее об обработке персональных данных в <a href="<?php echo get_template_directory_uri(); ?>/docs/Privacy-Policy.pdf" target="_blank">Политике конфиденциальности.</a></small>
            </label>
        </div>
    </div>
    <div class="col-md-6 col-xl-4 col-12">
        <button type="submit" class="btn w-100" id="<?php echo $id_prefix; ?>-submit-btn">
            <span class="btn-text">Отправить</span>
            <span class="btn-spinner" style="display: none;">
                <span class="spinner-border spinner-border-sm" role="status"></span>
                Отправка…
            </span>
        </button>
    </div>

</div>