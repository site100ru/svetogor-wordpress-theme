<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package svetogor
 */

?>

<section class="footer">
	<!-- Desktop version -->
	<div class="container py-5 d-none d-xl-block">
		<div class="row align-items-center">
			<div class="col-xl-3">
				<a href="<?php echo esc_url(home_url('/')); ?>" id="navbar-brand-img">
					<?php
					$company_logo = get_company_logo();
					if ($company_logo):
						?>
						<img src="<?php echo esc_url($company_logo['url']); ?>" class="img-fluid"
							alt="<?php echo esc_attr($company_logo['alt'] ?: get_bloginfo('name')); ?>" />
					<?php endif; ?>
				</a>
			</div>
			<div class="col-xl-7">
				<div class="navbar-collapse">
					<ul class="navbar-nav ms-auto mb-3 mb-lg-0 d-flex flex-row justify-content-center align-items-center">
						<!-- Company Address -->
						<li class="nav-item me-3">
							<div class="d-flex align-items-center gap-3 lh-1 nav-link-email">
								<img src="<?php echo esc_url(get_contact_icon_url('location_icon', 'location-ico.svg')); ?>"
									alt="Адрес" />
								<span>
									<?php echo nl2br(esc_html(get_company_address())); ?>
								</span>
							</div>
						</li>

						<!-- Work Hours -->
						<li class="nav-item me-3">
							<div class="d-flex align-items-center gap-3 lh-1 nav-link-hourse">
								<img src="<?php echo esc_url(get_contact_icon_url('clock_icon', 'clock-ico.svg')); ?>"
									alt="Время работы" />
								<span>
									<?php echo nl2br(esc_html(get_company_work_hours())); ?>
								</span>
							</div>
						</li>

						<!-- Callback Button -->
						<li class="nav-item me-3">
							<button class="nav-link d-flex text-start align-items-center gap-3 lh-1" data-bs-toggle="modal"
								data-bs-target="#callbackModal">
								<img src="<?php echo esc_url(get_contact_icon_url('callback_icon', 'callback-ico.svg')); ?>"
									alt="Обратный звонок" />
								<span>Обратный звонок</span>
							</button>
						</li>
					</ul>
				</div>
			</div>
			<div class="col-xl-2 text-end">

				<!-- Main Phone -->
				<?php
				$main_phone_data = get_main_phone_data();
				if ($main_phone_data && isset($main_phone_data['phone_number']) && $main_phone_data['phone_number']):
					?>
					<a href="tel:<?php echo esc_attr(format_phone_for_href($main_phone_data['phone_number'])); ?>"
						class="top-menu-tel nav-link">
						<img src="<?php echo esc_url(get_contact_icon_url('global_phone_icon', 'mobile-phone-ico.svg')); ?>"
							class="me-2" alt="Телефон" />
						<?php echo esc_html($main_phone_data['phone_number']); ?>
					</a>
				<?php endif; ?>
			</div>
		</div>

		<!-- Footer Navigation Menu -->
		<div class="row">
			<div class="col py-4 d-flex justify-content-center align-items-center">
				<?php display_footer_menu(); ?>
			</div>
		</div>

		<!-- Social Networks -->
		<div class="row justify-content-center footer-icon">
			<div class="col">
				<ul class="nav justify-content-center">
					<?php
					$footer_socials = get_footer_social_networks();
					if ($footer_socials):
						foreach ($footer_socials as $social):
							if ($social['icon'] && $social['url']):
								?>
								<li class="nav-item">
									<a class="nav-link ico-button px-2" href="<?php echo esc_url($social['url']); ?>" target="_blank">
										<img src="<?php echo esc_url($social['icon']['url']); ?>"
											alt="<?php echo esc_attr($social['name']); ?>" />
									</a>
								</li>
								<?php
							endif;
						endforeach;
					endif;
					?>
				</ul>
			</div>
		</div>
	</div>
	<!-- /Desktop version -->

	<!-- Mobile version -->
	<div class="container d-xl-none">
		<div class="row">
			<div class="col py-5">
				<a href="<?php echo esc_url(home_url('/')); ?>" id="navbar-brand-img">
					<?php
					$company_logo = get_company_logo();
					if ($company_logo):
						?>
						<img src="<?php echo esc_url($company_logo['url']); ?>" class="img-fluid"
							alt="<?php echo esc_attr($company_logo['alt'] ?: get_bloginfo('name')); ?>" />
					<?php endif; ?>
				</a>
				<ul class="ps-0 pt-0 pt-md-3 pb-2 navbar-nav">
					<!-- Company Address -->
					<li class="nav-item">
						<div class="nav-link ps-0 pb-2">
							<img src="<?php echo esc_url(get_contact_icon_url('location_icon', 'location-ico.svg')); ?>" class="me-2"
								alt="Адрес" />
							<?php echo esc_html(get_company_address()); ?>
						</div>
					</li>

					<!-- Work Hours -->
					<li class="nav-item">
						<div class="nav-link ps-0 py-2">
							<img src="<?php echo esc_url(get_contact_icon_url('clock_icon', 'clock-ico.svg')); ?>" class="me-2"
								alt="Время работы" />
							<?php echo esc_html(get_company_work_hours()); ?>
						</div>
					</li>

					<!-- Callback Button -->
					<li class="nav-item">
						<button class="nav-link ps-0 pt-2" data-bs-toggle="modal" data-bs-target="#callbackModal">
							<img src="<?php echo esc_url(get_contact_icon_url('callback_icon', 'callback-ico.svg')); ?>" class="me-2"
								alt="Обратный звонок" />
							Обратный звонок
						</button>
					</li>
				</ul>

				<!-- Main Phone -->
				<?php
				$main_phone_data = get_main_phone_data();
				if ($main_phone_data && isset($main_phone_data['phone_number']) && $main_phone_data['phone_number']):
					?>
					<a href="tel:<?php echo esc_attr(format_phone_for_href($main_phone_data['phone_number'])); ?>"
						class="top-menu-tel nav-link">
						<img src="<?php echo esc_url(get_phone_icon_url($main_phone_data)); ?>" class="me-2"
							style="position: relative; bottom: 1px" alt="Телефон" />
						<?php echo esc_html($main_phone_data['phone_number']); ?>
					</a>
				<?php endif; ?>

				<!-- Social Networks -->
				<ul class="nav pt-4 pb-3">
					<?php
					$footer_socials = get_footer_social_networks();
					if ($footer_socials):
						foreach ($footer_socials as $social):
							if ($social['icon'] && $social['url']):
								?>
								<li class="nav-item">
									<a class="nav-link ico-button ps-0 px-2" href="<?php echo esc_url($social['url']); ?>" target="_blank">
										<img src="<?php echo esc_url($social['icon']['url']); ?>"
											alt="<?php echo esc_attr($social['name']); ?>" />
									</a>
								</li>
								<?php
							endif;
						endforeach;
					endif;
					?>
				</ul>

				<!-- Mobile Footer Menu -->
				<?php display_footer_menu_mobile(); ?>
			</div>
		</div>
	</div>

	<hr />

	<!-- Footer -->
	<footer id="colophon" class="site-footer">
		<div class="container">
			<div class="row">
				<div class="col text-start text-md-center">
					<div id="im-in-footer">
						Создание, продвижение и поддержка:
						<a href="https://site100.ru" class="text-decoration-underline">site100.ru</a>
                        <div class="policy-in-footer">
                            <a href="<?php echo get_template_directory_uri(); ?>/docs/Privacy-Policy.pdf" class="text-decoration-underline" target="_blank">Политика конфиденциальности</a> | <a class="text-decoration-underline" href="<?php echo get_template_directory_uri(); ?>/docs/Consent-to-the-processing-of-personal-data.pdf" target="_blank">Согласие на обработку персональных данных</a>
                        </div>
					</div>
				</div>
			</div>
		</div>
	</footer>
</section>

<div class="modal fade" id="callbackModal">
	<div class="modal-dialog modal-dialog-centered">
		<?php include 'forms/simple-form.php'; ?>
	</div>
</div>

<!-- Callback Modal -->
<div class="modal fade" id="callbackModalTwo">
	<div class="modal-dialog modal-dialog-centered modal-lg">
		<?php include get_template_directory() . '/template-parts/blocks/forms/modal-extended-form.php'; ?>
	</div>
</div>
<!-- /Callback Modal -->

<div class="modal fade" id="callbackModalFree">
	<div class="modal-dialog modal-dialog-centered modal-lg">
		<?php include 'forms/calculation-form.php'; ?>
	</div>
</div>

<div class="modal fade" id="callbackModalFour">
	<div class="modal-dialog modal-dialog-centered modal-lg">
		<?php include 'forms/quantity-form.php'; ?>
	</div>
</div>

<?php
if (isset($_SESSION['win'])) {
    unset($_SESSION['win']);
    $display = "block";
} else {
    $display = "none";
}
?>

<div style="display: <?php echo $display; ?>;" onclick="f1();">
	<div id="background-msg" style="display: <?php echo $display; ?>;"></div>
	<div id="message">
		<?php
		if (isset($_SESSION['recaptcha'])) {
			echo $_SESSION['recaptcha'];
			unset($_SESSION['recaptcha']);
		}
		?>
	</div>
</div>

<script>
	function f1() {
		document.getElementById('background-msg').style.display = 'none';
		document.getElementById('message').style.display = 'none';
	}

	<?php if ($display === 'block'): ?>
		setTimeout(function () {
			f1();
		}, 3000);
	<?php endif; ?>
</script>

<script src="<?php echo get_template_directory_uri(); ?>/js/global-recaptcha.js"></script>

<style>
    
.popup-form.active {
	bottom: 0;
}

.popup-form {
	position: fixed;
	bottom: -500px;
	left: 0;
	width: 100%;
	background: rgba(255, 255, 255, 0.8);
	backdrop-filter: blur(8px);
	-webkit-backdrop-filter: blur(8px);
	box-shadow: 0 -2px 20px rgba(0, 0, 0, 0.2);
	padding: 10px;
	box-sizing: border-box;
	transition: bottom 0.5s ease-out;
	z-index: 1000;
	max-width: 100%;
}
</style>

<!-- Всплывающая форма Политики конфиденциальности -->
<div class="popup-form py-3" id="popupForm">
    <div class="form-content container">
        <div class="row justify-content-center align-items-center">
            <div class="col-md-9">
                <p class="mb-md-0">
                    На нашем сайте используются cookie-файлы, в том числе сервисов
                    веб-аналитики. Используя сайт, вы соглашаетесь на <a
                        href="<?= get_template_directory_uri(); ?>/docs/Consent-to-the-processing-of-personal-data.pdf"
                        target="blank"
                        >обработку персональных данных</a
                    > при помощи cookie-файлов. Подробнее об обработке персональных данных
                    вы можете узнать в <a
                        href="<?= get_template_directory_uri(); ?>/docs/Privacy-Policy.pdf"
                        target="blank"
                        >Политике конфиденциальности.</a
                    >
                </p>
            </div>
            <div class="col-md-3 text-md-center">
                <button id="closeBtn" class="btn action-btn">Понятно</button>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const popupForm = document.getElementById('popupForm');
        const closeBtn = document.getElementById('closeBtn');

        // Проверяем нужно ли показывать форму
        function shouldShowPopup() {
            const lastClosed = localStorage.getItem('popupLastClosed');

            // Если пользователь никогда не закрывал форму
            if (!lastClosed) return true;

            // Если прошло более 1 часа (3600000 миллисекунд) с последнего закрытия
            const now = new Date().getTime();
            return now - parseInt(lastClosed) > 3600000;
        }

        // Показываем форму если нужно
        if (shouldShowPopup()) {
            setTimeout(() => {
                popupForm.classList.add('active');
            }, 3000);
        }

        // Функция закрытия формы
        function closePopup() {
            popupForm.classList.remove('active');

            // Сохраняем время закрытия
            localStorage.setItem('popupLastClosed', new Date().getTime().toString());
        }

        // Закрытие по кнопке
        closeBtn.addEventListener('click', closePopup);
    });
</script>
<!-- /Всплывающая форма Политики конфиденциальности -->

<?php wp_footer(); ?>

</body>

</html>