<?php

/**
 * Блок контактов
 */
?>

<!-- КОНТЕНТ -->
<section class="section section-about">
    <div class="container">
        <div class="section-title text-center">
            <h1>Контакты</h1>
            <img width="62" height="14" loading="lazy" src="<?php echo get_template_directory_uri(); ?>/assets/img/ico/points.svg" alt="Точки" class="img-fluid">
        </div>

        <!-- Контактный контент -->
        <div class="row mb-5 section-contacts">
            <!-- Первый блок - Адрес, время работы, email -->
            <div class="col-12 col-md-6 col-xl-4">
                <!-- Адрес -->
                <div class="d-flex align-items-center mb-3">
                    <img width="24" height="24" loading="lazy" src="<?php echo esc_url(get_contact_icon_url('location_icon', 'location-ico.svg')); ?>" alt="Адрес" class="me-3 img-fluid">
                    <p class="mb-0"><?php echo esc_html(get_company_address()); ?></p>
                </div>

                <!-- Время работы -->
                <div class="d-flex align-items-center mb-3">
                    <img width="24" height="24" loading="lazy" src="<?php echo esc_url(get_contact_icon_url('clock_icon', 'clock-ico.svg')); ?>" alt="Время работы" class="me-3 img-fluid">
                    <p class="mb-0"><?php echo esc_html(get_company_work_hours()); ?></p>
                </div>

                <!-- Email -->
                <div class="d-flex align-items-center mb-3">
                    <img width="24" height="24" loading="lazy" src="<?php echo esc_url(get_contact_icon_url('email_icon', 'email-ico.svg')); ?>" alt="Email" class="me-3 img-fluid">
                    <a href="mailto:<?php echo esc_attr(get_company_email()); ?>" class="text-decoration-none">
                        <?php echo esc_html(get_company_email()); ?>
                    </a>
                </div>
            </div>

            <!-- Второй блок - Телефоны -->
            <div class="col-12 col-md-6 col-xl-4">
                <?php
                $contact_phones = get_contacts_phones();
                if ($contact_phones):
                    foreach ($contact_phones as $phone):
                        if ($phone['phone_number']):
                            // Используем индивидуальную иконку телефона или общую по умолчанию
                            $phone_icon_url = $phone['phone_icon'] ? $phone['phone_icon']['url'] : get_contact_icon_url('phone_icon', 'mobile-phone-ico.svg');
                ?>
                            <div class="d-flex align-items-start mb-3">
                                <img width="24" height="24" loading="lazy" src="<?php echo esc_url($phone_icon_url); ?>" alt="Телефон" class="me-3 img-fluid" aria-hidden="true">
                                <div>
                                    <div class="d-flex flex-wrap">
                                        <a href="tel:<?php echo esc_attr(format_phone_for_href($phone['phone_number'])); ?>"
                                            class="text-decoration-none me-1">
                                            <?php echo esc_html($phone['phone_number']); ?>
                                        </a>
                                        <?php if ($phone['phone_description']): ?>
                                            <span><?php echo esc_html($phone['phone_description']); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                <?php
                        endif;
                    endforeach;
                endif;
                ?>
            </div>

            <!-- Третий блок - Юридическая информация -->
            <div class="col-12 col-md-12 col-xl-4">
                <div class="d-flex flex-column">
                    <p><?php echo esc_html(get_company_name()); ?></p>

                    <p>
                        <?php echo esc_html(get_company_legal_address()); ?>
                    </p>

                    <p><?php echo esc_html(get_company_inn()); ?></p>
                </div>
            </div>
        </div>

        <!-- Социальные сети -->
        <?php
        $contacts_socials = get_contacts_social_networks();
        if ($contacts_socials):
        ?>
            <div class="d-flex justify-content-center gap-4 flex-wrap">
                <?php
                foreach ($contacts_socials as $social):
                    if ($social['icon'] && $social['url']):
                ?>
                        <a href="<?php echo esc_url($social['url']); ?>" target="_blank" rel="noopener">
                            <img loading="lazy" src="<?php echo esc_url($social['icon']['url']); ?>" alt="<?php echo esc_attr($social['name']); ?>" width="40" height="40">
                        </a>
                <?php
                    endif;
                endforeach;
                ?>
            </div>
        <?php endif; ?>
    </div>
</section>