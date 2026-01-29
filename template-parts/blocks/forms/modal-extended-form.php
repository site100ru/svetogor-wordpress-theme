<!-- template-parts/blocks/forms/modal-extended-form.php - модальная версия -->
<form class="contact-form modal-content" method="post"
    action="<?php echo get_template_directory_uri(); ?>/mails/extended-form-handler.php" enctype="multipart/form-data">

    <div class="modal-header">
        <h2 class="modal-title" id="callbackModalLabel">Оставить заявку</h2>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>

    <div class="modal-body">
        <?php include get_template_directory() . '/forms/extended-form-content.php'; ?>
    </div>

</form>