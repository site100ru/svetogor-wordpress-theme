<!-- template-parts/blocks/forms/extended-form.php - версия для страницы -->
<section class="section section-half section-call">
    <div class="d-flex flex-wrap half-bg flex-row-reverse">
        <!-- Левая часть с фоном -->
        <div class="left-part flex-grow-1"></div>

        <!-- Правая часть с картинкой (скрывается на мобилках) -->
        <div class="right-part d-none d-lg-block">
            <img loading="lazy" src="<?php echo get_template_directory_uri(); ?>/assets/img/about-us.png" alt="Изображение" class="img-cover">
        </div>
    </div>

    <div class="container">
        <div class="row justify-content-center flex-row-reverse section-call-content">
            <div class="col-lg-7">
                <div class="order-description about-description">
                    <h2 class="mb-1">Оставьте заявку</h2>
                    <p>И получите смету в день обращения в 2-х ценовых категориях. Мы с радостью обсудим с Вами все детали работы
                        и возникшие вопросы.</p>
                    <img loading="lazy" src="<?php echo get_template_directory_uri(); ?>/assets/img/ico/points.svg" alt="Точки" class="img-fluid">
                </div>

                <!-- Расширенная кастомная форма -->
                <form class="contact-form" method="post"
                    action="<?php echo get_template_directory_uri(); ?>/mails/extended-form-handler.php"
                    enctype="multipart/form-data">

                    <?php include get_template_directory() . '/forms/extended-form-content.php'; ?>

                </form>
            </div>

            <div class="col-lg-5"></div>
        </div>
    </div>
</section>