/**
 * Маска для полей ввода телефона
 * 
 * Этот скрипт применяет маску ввода к элементам с классом "telMask".
 * Используется библиотека Inputmask.
 * <script src="js/inputmask.min.js"></script>
 * Маска формата: +7(999)999-99-99
 * 
 */

document.addEventListener('DOMContentLoaded', function () {
    // Проверяем наличие библиотеки Inputmask
    if (typeof Inputmask === 'undefined') {
        console.warn('Inputmask не загружена. Маска для телефонов не применена.');
        return;
    }

    const telInputs = document.querySelectorAll('.telMask');
    if (telInputs.length > 0) {
        const im = new Inputmask('+7(999)999-99-99');
        im.mask(telInputs);
    }
})