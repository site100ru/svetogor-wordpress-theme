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
  var telMask = document.getElementsByClassName('telMask');
  var im = new Inputmask('+7(999)999-99-99');
  im.mask(telMask);
})