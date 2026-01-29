<?php
// mails/quantity-form-handler.php
session_start(); // Запускаем сессию для сохранения сообщений

// Подключаем WordPress (нужно для wp_mail)
require_once(realpath(dirname(__FILE__) . '/../../../../wp-load.php'));

// Если существует переменная POST, то
if ($_POST) {
    // Функция проверки reCAPTCHA v3
    function getCaptcha($SecretKey)
    {
        $Response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=6LdV1IcUAAAAABnQ0mXIp5Yh7tLEcAXzdqG6rx9Y&response={$SecretKey}");
        $Return = json_decode($Response);
        return $Return;
    }

    // Проверяем наличие токена reCAPTCHA
    if (!isset($_POST['g-recaptcha-response']) || empty($_POST['g-recaptcha-response'])) {
        $_SESSION['win'] = 1;
        $_SESSION['display'] = 'block';
        $_SESSION['recaptcha'] = '<p class="text-light">reCAPTCHA не пройдена. Пожалуйста, попробуйте снова.</p>';
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit;
    }

    /* Принимаем данные обратно */
    $Return = getCaptcha($_POST['g-recaptcha-response']);

    // Если reCAPTCHA пройдена успешно
    if ($Return->success == true && $Return->score > 0.5) {

        // Получаем данные из формы с защитой
        $name = isset($_POST['name']) && $_POST['name'] ? sanitize_text_field($_POST['name']) : 'Не указано';
        $tel = isset($_POST['tel']) && $_POST['tel'] ? sanitize_text_field($_POST['tel']) : 'Не указан';
        $quantity = isset($_POST['quantity']) && $_POST['quantity'] ? intval($_POST['quantity']) : 1;

        // Получаем данные о товаре
        $product_id = isset($_POST['product-id']) && $_POST['product-id'] ? sanitize_text_field($_POST['product-id']) : 'Не указан';
        $product_name = isset($_POST['product-name']) && $_POST['product-name'] ? sanitize_text_field($_POST['product-name']) : 'Не указано';

        // Получаем данные о странице
        $page_url = isset($_POST['page-url']) && $_POST['page-url'] ? esc_url($_POST['page-url']) : 'Не указан';
        $page_title = isset($_POST['page-title']) && $_POST['page-title'] ? sanitize_text_field($_POST['page-title']) : 'Не указан';

        // Проверяем обязательные поля
        if ($name !== 'Не указано' && $tel !== 'Не указан') {

            // Формируем сообщение для email
            $email_message = "=== ЗАЯВКА НА ТОВАР С КОЛИЧЕСТВОМ ===\n\n";
            $email_message .= "Контактная информация:\n";
            $email_message .= "Имя: " . $name . "\n";
            $email_message .= "Телефон: " . $tel . "\n\n";

            $email_message .= "Информация о товаре:\n";
            $email_message .= "ID товара: " . $product_id . "\n";
            $email_message .= "Название товара: " . $product_name . "\n";
            $email_message .= "Количество: " . $quantity . " шт.\n\n";

            $email_message .= "Информация о странице:\n";
            $email_message .= "URL страницы: " . $page_url . "\n";
            $email_message .= "Заголовок страницы: " . $page_title . "\n";
            $email_message .= "Дата и время: " . date('d.m.Y H:i:s') . "\n";
            $email_message .= "reCAPTCHA Score: " . $Return->score . "\n";

            // Настройки для wp_mail
            $to = 'sidorov-vv3@mail.ru, vasilyev-r@mail.ru';
            $subject = 'Заявка на товар с сайта svetogor.ru';
            $headers = array(
                'Content-Type: text/plain; charset=UTF-8',
                'From: Сайт site100svetogor.site <info@site100svetogor.site>',
                'Reply-To: ' . $name . ' <info@site100svetogor.site>'
            );

            // Отправка через WordPress wp_mail
            $emailSent = wp_mail($to, $subject, $email_message, $headers);

            // Логируем результат отправки
            error_log('Quantity form email sent result: ' . ($emailSent ? 'SUCCESS' : 'FAILED'));

            if ($emailSent) {
                $_SESSION['win'] = 1;
                $_SESSION['display'] = 'block';
                $_SESSION['recaptcha'] = '<p class="text-light">Спасибо за заявку на товар "' . $product_name . '"! Мы свяжемся с Вами в ближайшее время.</p>';
            } else {
                $_SESSION['win'] = 1;
                $_SESSION['display'] = 'block';
                $_SESSION['recaptcha'] = '<p class="text-light">Заявка принята, но возникли проблемы с отправкой email. Мы обработаем вашу заявку вручную.</p>';
            }

            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit;
        } else {
            // Если обязательные поля не заполнены
            $_SESSION['win'] = 1;
            $_SESSION['display'] = 'block';
            $_SESSION['recaptcha'] = '<p class="text-light">Пожалуйста, заполните все обязательные поля (Имя, Телефон)!</p>';
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit;
        }
    } else {
        // Если reCAPTCHA не пройдена
        $error_details = '';
        if (isset($Return->{'error-codes'})) {
            $error_details = ' Ошибки: ' . implode(', ', $Return->{'error-codes'});
        }
        if (isset($Return->score)) {
            $error_details .= ' Score: ' . $Return->score;
        }

        $_SESSION['win'] = 1;
        $_SESSION['display'] = 'block';
        $_SESSION['recaptcha'] = '<p class="text-light"><strong>Извините!</strong><br>Проверка безопасности не пройдена. Попробуйте еще раз.' . $error_details . '</p>';
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit;
    }
} else {
    // Если нет POST данных
    header("Location: " . (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '/'));
    exit;
}
