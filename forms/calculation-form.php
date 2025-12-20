<!-- Форма для расчета (callbackModalFree) -->
<form class="contact-form modal-content" method="post" action="<?php echo get_template_directory_uri(); ?>/mails/calculation-form-handler.php"
  enctype="multipart/form-data">
  <div class="modal-header">
    <h5 class="modal-title" id="callbackModalLabel">Заявка на расчет</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
  </div>

  <div class="modal-body">
    <!-- Первая строка: Имя, Телефон, Почта -->
    <div class="row mb-4">
      <div class="col-md-4 col-12 mb-3 mb-md-0">
        <label for="name-input" class="form-label">Имя</label>
        <input type="text" class="form-control" name="your-name" id="name-input" required />
      </div>
      <div class="col-md-4 col-12 mb-3 mb-md-0">
        <label for="phone-input" class="form-label">Телефон</label>
        <input type="tel" class="form-control" name="your-phone" id="phone-input" required />
      </div>
      <div class="col-md-4 col-12">
        <label for="email-input" class="form-label">Email</label>
        <input type="email" class="form-control" name="your-email" id="email-input" required />
      </div>
    </div>

    <!-- Вторая строка: Чекбоксы с картинками -->
    <div class="row mb-4">
      <p>Хочу получить расчет на:</p>
      <div class="d-flex gap-4">
        <label class="image-checkbox-container">
          <input type="checkbox" name="contact-methods[]" value="WhatsApp" />
          <span class="custom-checkbox"></span>
          <img src="<?php echo get_template_directory_uri(); ?>/assets/img/ico/whatsapp.svg" alt="WhatsApp" />
        </label>
        <label class="image-checkbox-container">
          <input type="checkbox" name="contact-methods[]" value="Telegram" />
          <span class="custom-checkbox"></span>
          <img src="<?php echo get_template_directory_uri(); ?>/assets/img/ico/telegram.svg" alt="Telegram" />
        </label>
        <label class="image-checkbox-container">
          <input type="checkbox" name="contact-methods[]" value="Email" />
          <span class="custom-checkbox"></span>
          <img src="<?php echo get_template_directory_uri(); ?>/assets/img/ico/message.svg" alt="Email" />
        </label>
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
          <input type="checkbox" name="additional-services[]" value="Выезд на замер" />
          <span class="custom-checkbox"></span>
          <span>Выезд на замер</span>
        </label>
        <label class="custom-checkbox-container">
          <input type="checkbox" name="additional-services[]" value="Разработка дизайна" />
          <span class="custom-checkbox"></span>
          <span>Разработка дизайна</span>
        </label>
        <label class="custom-checkbox-container">
          <input type="checkbox" name="additional-services[]" value="Монтаж" />
          <span class="custom-checkbox"></span>
          <span>Монтаж</span>
        </label>
        <label class="custom-checkbox-container">
          <input type="checkbox" name="additional-services[]" value="Доставка" />
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
          <input type="file" name="your-file" class="file-upload" />
          <span class="file-name">Файл не прикреплен</span>
        </div>
      </div>
    </div>

    <!-- Скрытые поля -->
    <input type="hidden" name="page-url" value="" />
    <input type="hidden" name="page-title" value="" />
    <input type="hidden" name="g-recaptcha-response" value="" />

     <div class="row w-100 m-0">
        <div class="col-12">
            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" id="gridCheck-5" checked>
                <label class="form-check-label" for="gridCheck-5">
                    <p class="mb-0"><small>Даю согласие на обработку персональных данных. Подробнее об обработке персональных данных в <a href="<?php echo get_template_directory_uri(); ?>/docs/Privacy-Policy.pdf" target="_blank">Политике конфиденциальности.</a></small></p>
                </label>
            </div>	
        </div>
      <div class="col-md-6 col-xl-4 col-12 mb-0 ps-0">
        <button type="submit" class="btn btn-big me-auto pe-4 modal-btn w-100" style="max-width: 212px">
          Оставить заявку
        </button>
      </div>
    </div>
  </div>
</form>

<script>
  // Скрипт для работы с файлом в модальном окне
  document.addEventListener('DOMContentLoaded', function () {
    // Находим элементы загрузки файла внутри формы
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