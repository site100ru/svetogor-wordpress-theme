<?php

/**
 * Block Name: Раскрывающий текст
 * Description: Блок с общей информацией и возможностью скрыть/показать дополнительный текст
 * template-parts/blocks/general-info/general-info.php
 */

// Создаем уникальный ID для блока
$id = 'general-info-' . $block['id'];

// Добавляем дополнительные классы, если они есть
$className = '';
if (!empty($block['className'])) {
    $className .= ' ' . $block['className'];
}

// Получаем данные полей из ACF
$section_title_general_info = get_field('section_title_general_info');
$main_content = get_field('main_content');
$additional_content = get_field('additional_content');
$button_text = get_field('button_text') ?: 'Читать далее';
$button_text_collapse = get_field('button_text_collapse') ?: 'Свернуть';
$background_color_general_info = get_field('background_color_general_info') ?: 'white';

// Определяем классы на основе настроек фона
$section_class = 'section section-product-list';
$section_class .= $background_color_general_info === 'grey' ? ' bg-grey' : '';
?>

<section id="<?php echo esc_attr($id); ?>" class="<?php echo esc_attr($section_class); ?> <?php echo esc_attr($className); ?> general-info base-text">
    <div class="container">
        <?php if (!empty($section_title_general_info)): ?>
            <div class="section-title text-center">
                <h3><?php echo esc_html($section_title_general_info); ?></h3>
                <img width="62" height="14" loading="lazy" src="<?php echo get_template_directory_uri(); ?>/assets/img/ico/points.svg" class="img-fluid" alt="Точки">
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-12">
                <?php if (!empty($main_content)): ?>
                    <div class="main-content">
                        <?php echo wp_kses_post($main_content); ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($additional_content)): ?>
                    <div class="additional-content" id="additionalText-<?php echo esc_attr($block['id']); ?>">
                        <?php echo wp_kses_post($additional_content); ?>
                    </div>

                    <div class="text-center mt-4">
                        <button class="btn expand-btn"
                            type="button"
                            data-target="#additionalText-<?php echo esc_attr($block['id']); ?>"
                            data-text-show="<?php echo esc_attr($button_text); ?>"
                            data-text-hide="<?php echo esc_attr($button_text_collapse); ?>"
                            data-section-id="<?php echo esc_attr($id); ?>">
                            <?php echo esc_html($button_text); ?>
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<script>
    (function() {
        const initExpandButton = function() {
            const button = document.querySelector('[data-target="#additionalText-<?php echo esc_js($block['id']); ?>"]');
            
            if (!button) return;
            
            const targetId = button.getAttribute('data-target');
            const targetElement = document.querySelector(targetId);

            if (targetElement) {
                const textShow = button.getAttribute('data-text-show');
                const textHide = button.getAttribute('data-text-hide');
                const sectionId = button.getAttribute('data-section-id');

                let isExpanded = false;

                button.addEventListener('click', function() {
                    if (!isExpanded) {
                        targetElement.classList.add('expanding');
                        button.textContent = textHide;
                        isExpanded = true;

                        setTimeout(() => {
                            targetElement.classList.add('expanded');
                            targetElement.classList.remove('expanding');
                        }, 500);

                    } else {
                        targetElement.classList.remove('expanded');
                        targetElement.classList.add('expanding');
                        button.textContent = textShow;
                        isExpanded = false;

                        const section = document.getElementById(sectionId);
                        if (section) {
                            section.scrollIntoView({
                                behavior: 'smooth',
                                block: 'start'
                            });
                        }

                        setTimeout(() => {
                            targetElement.classList.remove('expanding');
                        }, 500);
                    }
                });
            }
        };

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initExpandButton);
        } else {
            initExpandButton();
        }
    })();
</script>