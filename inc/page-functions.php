<?php
/**
 * Функции для работы со страницами
 * 
 * @package svetogor
 */

if (!defined('ABSPATH')) {
    exit;
}

function add_page_body_class($classes) {
    if (is_page()) {
        $classes[] = 'custom-page-layout';
        
        // Добавляем класс для страниц с hero-фоном
        if (get_hero_bg()) {
            $classes[] = 'has-hero-background';
        }
    }
    return $classes;
}
add_filter('body_class', 'add_page_body_class');