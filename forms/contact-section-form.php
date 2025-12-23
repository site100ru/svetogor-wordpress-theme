<!-- forms/contact-section-form.php -->
<!-- Форма из секции (contact-section-form) -->
<form id="custom-contact-form" class="contact-form" method="post" action="<?php echo get_template_directory_uri(); ?>/mails/contact-section-handler.php"
  enctype="multipart/form-data" novalidate>
  <!-- Первая строка: Имя, Телефон, Почта -->
  <div class="row mb-4">
    <div class="col-md-4 col-12 mb-3 mb-md-0">
      <label for="name-input" class="form-label">Имя</label>
      <input type="text" class="form-control" name="your-name" id="name-input" required />
      <div class="invalid-feedback">Введите ваше имя</div>
    </div>
    <div class="col-md-4 col-12 mb-3 mb-md-0">
      <label for="phone-input" class="form-label">Телефон</label>
      <input type="tel" class="form-control" name="your-phone" id="phone-input" required />
      <div class="invalid-feedback">Введите корректный телефон</div>
    </div>
    <div class="col-md-4 col-12">
      <label for="email-input" class="form-label">Email</label>
      <input type="email" class="form-control" name="your-email" id="email-input" required />
      <div class="invalid-feedback">Введите корректный email</div>
    </div>
  </div>

  <!-- Вторая строка: Чекбоксы с картинками -->
  <div class="row mb-4">
    <p>Хочу получить расчет на:</p>
    <div class="d-flex gap-4">
      <label class="image-checkbox-container">
        <input type="checkbox" name="contact-methods[]" value="WhatsApp" />
        <span class="custom-checkbox"></span>
        <img loading="lazy" src="<?php echo get_template_directory_uri(); ?>/assets/img/ico/whatsapp.svg" alt="WhatsApp" />
      </label>
      <label class="image-checkbox-container">
        <input type="checkbox" name="contact-methods[]" value="Telegram" />
        <span class="custom-checkbox"></span>
        <img loading="lazy" src="<?php echo get_template_directory_uri(); ?>/assets/img/ico/telegram.svg" alt="Telegram" />
      </label>
      <label class="image-checkbox-container">
        <input type="checkbox" name="contact-methods[]" value="Email" />
        <span class="custom-checkbox"></span>
        <img loading="lazy" src="<?php echo get_template_directory_uri(); ?>/assets/img/ico/message.svg" alt="Email" />
      </label>
    </div>
  </div>

  <!-- Четвертая строка: Текстареа -->
  <div class="row mb-4">
    <p class="mb-2">Мне нужно:</p>
    <div class="col-12">
      <textarea class="form-control" name="your-message" rows="4"></textarea>
    </div>
  </div>

  <!-- Шестая строка: Прикрепить файл -->
  <div class="row mb-4">
    <div class="col-md-6 col-xl-4 col-12">
      <div class="file-upload-wrapper w-100 m-0">
        <button type="button" class="btn btn-invert btn-big file-upload-btn">
          Прикрепить файл
        </button>
        <input type="file" name="your-file" class="file-upload" accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx" />
        <span class="file-name">Файл не прикреплен</span>
      </div>
    </div>
  </div>

  <!-- Скрытые поля -->
  <input type="hidden" name="page-url" value="" />
  <input type="hidden" name="page-title" value="" />
  <input type="hidden" name="referrer" value="" />

  <!-- Седьмая строка: Кнопка отправить -->
  <div class="row">
    <div class="col-12">
        <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" id="gridCheck-5" checked>
            <label class="form-check-label" for="gridCheck-5">
                <p class="mb-0"><small>Даю согласие на обработку персональных данных. Подробнее об обработке персональных данных в <a href="<?php echo get_template_directory_uri(); ?>/docs/Privacy-Policy.pdf" target="_blank">Политике конфиденциальности.</a></small></p>
            </label>
        </div>	
    </div>
    <div class="col-md-6 col-xl-4 col-12">
      <button type="submit" class="btn w-100" id="submit-btn">
        <span class="btn-text">Отправить</span>
        <span class="btn-spinner" style="display: none;">
          <span class="spinner-border spinner-border-sm" role="status"></span>
          Отправка...
        </span>
      </button>
    </div>
  </div>

  <!-- Сообщения -->
  <div class="row mt-3">
    <div class="col-12">
      <div id="form-messages"></div>
    </div>
  </div>
</form>

<script>
  // Скрипт для работы с файлом
  document.addEventListener('DOMContentLoaded', function () {
    const fileUpload = document.querySelector('.file-upload');
    const fileName = document.querySelector('.file-name');

    if (fileUpload && fileName) {
      fileUpload.addEventListener('change', function (e) {
        if (e.target.files.length > 0) {
          fileName.textContent = e.target.files[0].name;
        } else {
          fileName.textContent = 'Файл не прикреплен';
        }
      });
    }
  });
</script>