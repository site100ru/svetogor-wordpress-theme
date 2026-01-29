<?php
// mails/calculation-form-handler.php
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
    if ($Return->success == true && $Return->score > 0.125) {

        // Получаем данные из формы с защитой
        $name = isset($_POST['your-name']) && $_POST['your-name'] ? sanitize_text_field($_POST['your-name']) : 'Не указано';
        $phone = isset($_POST['your-phone']) && $_POST['your-phone'] ? sanitize_text_field($_POST['your-phone']) : 'Не указан';
        $email = isset($_POST['your-email']) && $_POST['your-email'] ? sanitize_email($_POST['your-email']) : 'Не указан';
        $message = isset($_POST['your-message']) && $_POST['your-message'] ? sanitize_textarea_field($_POST['your-message']) : 'Не указано';

        // Обрабатываем чекбоксы
        $contact_methods = isset($_POST['contact-methods']) ? implode(', ', array_map('sanitize_text_field', $_POST['contact-methods'])) : 'Не выбраны';
        $additional_services = isset($_POST['additional-services']) ? implode(', ', array_map('sanitize_text_field', $_POST['additional-services'])) : 'Не выбраны';

        // Получаем данные о странице
        $page_url = isset($_POST['page-url']) && $_POST['page-url'] ? esc_url($_POST['page-url']) : 'Не указан';
        $page_title = isset($_POST['page-title']) && $_POST['page-title'] ? sanitize_text_field($_POST['page-title']) : 'Не указан';

        // Проверяем обязательные поля
        if ($name !== 'Не указано' && $phone !== 'Не указан' && $email !== 'Не указан') {

            // Обработка файла
            $file_info = '';
            $attachments = array();

            if (isset($_FILES['your-file']) && $_FILES['your-file']['error'] == 0) {
                $upload_dir = wp_upload_dir();
                $upload_path = $upload_dir['path'] . '/';

                $file_name = time() . '_' . sanitize_file_name($_FILES['your-file']['name']);
                $file_path = $upload_path . $file_name;

                // Проверяем тип файла
                $allowed_types = array('jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx');
                $file_extension = strtolower(pathinfo($_FILES['your-file']['name'], PATHINFO_EXTENSION));

                if (in_array($file_extension, $allowed_types)) {
                    if (move_uploaded_file($_FILES['your-file']['tmp_name'], $file_path)) {
                        $file_info = "Файл загружен: " . $_FILES['your-file']['name'] . " (размер: " . size_format($_FILES['your-file']['size']) . ")";
                        $attachments[] = $file_path;
                    } else {
                        $file_info = "Ошибка загрузки файла";
                    }
                } else {
                    $file_info = "Недопустимый тип файла";
                }
            } else {
                $file_info = "Файл не загружен";
            }

            // Формируем сообщение для email
            $email_message = "=== ЗАЯВКА НА РАСЧЕТ С САЙТА ===\n\n";
            $email_message .= "Контактная информация:\n";
            $email_message .= "Имя: " . $name . "\n";
            $email_message .= "Телефон: " . $phone . "\n";
            $email_message .= "Email: " . $email . "\n\n";

            $email_message .= "Детали заявки:\n";
            $email_message .= "Способы связи для расчета: " . $contact_methods . "\n";
            $email_message .= "Дополнительные услуги: " . $additional_services . "\n";
            $email_message .= "Сообщение: " . $message . "\n";
            $email_message .= $file_info . "\n\n";

            $email_message .= "Информация о странице:\n";
            $email_message .= "URL страницы: " . $page_url . "\n";
            $email_message .= "Заголовок страницы: " . $page_title . "\n";
            $email_message .= "Дата и время: " . date('d.m.Y H:i:s') . "\n";
            $email_message .= "reCAPTCHA Score: " . $Return->score . "\n";

            // Настройки для wp_mail
            $to = 'sidorov-vv3@mail.ru, vasilyev-r@mail.ru';
            $subject = 'Заявка на расчет с сайта svetogor.ru';
            $headers = array(
                'Content-Type: text/plain; charset=UTF-8',
                'From: Сайт site100svetogor.site <info@site100svetogor.site>',
                'Reply-To: ' . $name . ' <info@site100svetogor.site>'
            );

            // Отправка через WordPress wp_mail
            $emailSent = wp_mail($to, $subject, $email_message, $headers, $attachments);

            // Логируем результат отправки
            error_log('Calculation form email sent result: ' . ($emailSent ? 'SUCCESS' : 'FAILED'));

            if ($emailSent) {
                $_SESSION['win'] = 1;
                $_SESSION['display'] = 'block';
                $_SESSION['recaptcha'] = '<p class="text-light">Спасибо за заявку! Мы подготовим для Вас индивидуальное предложение и свяжемся в ближайшее время.</p>';
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
            $_SESSION['recaptcha'] = '<p class="text-light">Пожалуйста, заполните все обязательные поля (Имя, Телефон, Email)!</p>';
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
