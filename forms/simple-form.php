<!-- Простая форма (callbackModal) -->
<form method="post" action="<?php echo get_template_directory_uri(); ?>/mails/simple-form-handler.php"
  class="modal-content">
  <div class="modal-header">
    <h5 class="modal-title" id="simple-callbackModalLabel">Оставить заявку</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
  </div>
  <div class="modal-body">
    <div class="row">
      <div class="col-md-6 mb-3 mb-md-0">
        <label for="simple-name-input" class="form-label">Имя</label>
        <input type="text" name="name" id="simple-name-input" class="form-control" required>
      </div>
      <div class="col-md-6">
        <label for="simple-tel-input" class="form-label">Телефон</label>
        <input type="text" name="tel" id="simple-tel-input" class="form-control telMask" required>
      </div>
    </div>

    <!-- Скрытые поля -->
    <input type="hidden" name="page-url" value="">
    <input type="hidden" name="page-title" value="">
  </div>
  <div class="modal-footer justify-content-">
    <div class="row w-100 m-0">
        <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" id="simple-privacy-check" checked>
            <label class="form-check-label" for="simple-privacy-check">
                <small>Даю согласие на обработку персональных данных. Подробнее об обработке персональных данных в <a href="<?php echo get_template_directory_uri(); ?>/docs/Privacy-Policy.pdf" target="_blank">Политике конфиденциальности.</a></small>
            </label>
        </div>	
      <div class="col-md-6 mb-0 p-0">
        <button type="submit" class="btn btn-big me-auto pe-4 modal-btn">
          Оставить заявку
        </button>
      </div>
    </div>
  </div>
</form>