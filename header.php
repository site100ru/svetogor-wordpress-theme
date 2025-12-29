<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package svetogor
 */

?>
<!doctype html>
<html <?php language_attributes(); ?>>

<head>
	<meta charset="<?php bloginfo('charset'); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">

    <?php $favicon = get_template_directory_uri() . '/assets/img/favicon/'; ?>

    <link rel="apple-touch-icon" sizes="57x57" href="<?php echo $favicon; ?>apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="<?php echo $favicon; ?>apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="<?php echo $favicon; ?>apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="<?php echo $favicon; ?>apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="<?php echo $favicon; ?>apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="<?php echo $favicon; ?>apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="<?php echo $favicon; ?>apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="<?php echo $favicon; ?>apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo $favicon; ?>apple-icon-180x180.png">

    <link rel="icon" type="image/png" sizes="192x192" href="<?php echo $favicon; ?>android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo $favicon; ?>favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="<?php echo $favicon; ?>favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo $favicon; ?>favicon-16x16.png">

    <link rel="manifest" href="<?php echo $favicon; ?>manifest.json">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="<?php echo $favicon; ?>ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">

	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
	<?php wp_body_open(); ?>
	<div id="page" class="site">
		<a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e('Skip to content', 'svetogor'); ?></a>

		<header id="masthead" class="site-header">
			<!-- Top navigation bar -->
			<nav class="header-nav-top navbar navbar-expand-lg navbar-light d-none d-lg-block py-1 py-lg-0">
				<div class="container">
					<div class="collapse navbar-collapse">
						<ul class="navbar-nav align-items-center justify-content-between w-100">
							<!-- Company Address -->
							<li class="nav-item me-3 me-md-1 me-xl-3">
								<div
									class="nav-link d-flex align-items-center gap-3 gap-md-2 gap-xl-3 lh-1 nav-link-text nav-link-email">
									<img loading="lazy" src="<?php echo esc_url(get_contact_icon_url('location_icon', 'location-ico.svg')); ?>" alt="Адрес">
									<span>
										<?php echo esc_html(get_company_address()); ?>
									</span>
								</div>
							</li>

							<!-- Company Email -->
							<li class="nav-item me-3 me-md-1 me-xl-3">
								<a href="mailto:<?php echo esc_attr(get_company_email()); ?>"
									class="nav-link d-flex align-items-center gap-3 gap-md-2 gap-xl-3 lh-1 nav-link-text">
									<img loading="lazy" src="<?php echo esc_url(get_contact_icon_url('email_icon', 'email-ico.svg')); ?>" alt="Email" >
									<?php echo esc_html(get_company_email()); ?>
								</a>
							</li>

							<!-- Callback Button -->
							<li class="nav-item me-3 me-md-1 me-xl-3">
								<button class="nav-link d-flex align-items-center gap-3 gap-md-2 gap-xl-3 lh-1" data-bs-toggle="modal"
									data-bs-target="#callbackModal">
									<img loading="lazy" src="<?php echo esc_url(get_contact_icon_url('callback_icon', 'callback-ico.svg')); ?>" alt="Обратный звонок" >
									Обратный звонок
								</button>
							</li>

							<!-- Calculator Button -->
							<li class="nav-item me-3 me-md-1 me-xl-3">
								<?php
								// Проверяем, находимся ли мы на странице товара
								$target_modal = (is_product()) ? '#callbackModalFree' : '#callbackModalTwo';
								?>
								<button class="nav-link d-flex align-items-center gap-3 gap-md-2 gap-xl-3 lh-1" data-bs-toggle="modal"
									data-bs-target="<?php echo $target_modal; ?>">
									<img loading="lazy" src="<?php echo esc_url(get_contact_icon_url('calculator_icon', 'calculator.svg')); ?>" alt="Калькулятор" >
									Рассчитать стоимость
								</button>
							</li>
							<!-- Main Phone -->
							<?php
							$main_phone_data = get_main_phone_data();
							if ($main_phone_data && isset($main_phone_data['phone_number']) && $main_phone_data['phone_number']):
								?>
								<li class="nav-item ms-auto me-3 me-md-1 me-xl-3">
									<a class="top-menu-tel nav-link gap-3"
										href="tel:<?php echo esc_attr(format_phone_for_href($main_phone_data['phone_number'])); ?>">
										<img loading="lazy" src="<?php echo esc_url(get_contact_icon_url('global_phone_icon', 'mobile-phone-ico.svg')); ?>" alt="Телефон" >
										<?php echo esc_html($main_phone_data['phone_number']); ?>
									</a>
								</li>
							<?php endif; ?>

							<!-- Header Social Networks -->
							<?php
							$header_socials = get_header_social_networks();
							if ($header_socials):
								foreach ($header_socials as $social):
									if ($social['icon'] && $social['url']):
										?>
										<li class="nav-item">
											<a class="nav-link ico-button" href="<?php echo esc_url($social['url']); ?>" target="_blank">
												<img loading="lazy" src="<?php echo esc_url($social['icon']['url']); ?>" alt="<?php echo esc_attr($social['name']); ?>" >
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
			</nav>

			<!-- Main site branding and navigation -->
			<?php svetogor_safe_navigation_v5(); ?>
		</header><!-- #masthead -->