// reCAPTCHA v3 для всех форм - ОБЪЕДИНЕННЫЙ СКРИПТ
document.addEventListener('DOMContentLoaded', function () {

    const SITE_KEY = '6LdV1IcUAAAAADRQAhpGL8dVj5_t0nZDPh9m_0tn';
    let recaptchaLoaded = false;
    let recaptchaAPI = null;

    // Функция загрузки reCAPTCHA API
    function loadRecaptchaAPI() {
        return new Promise((resolve, reject) => {
            // Проверяем, не загружена ли уже
            if (recaptchaLoaded && typeof grecaptcha !== 'undefined') {
                resolve();
                return;
            }

            // Удаляем старые скрипты если есть
            const existingScript = document.querySelector('script[src*="recaptcha"]');
            if (existingScript) {
                existingScript.remove();
            }

            const script = document.createElement('script');
            script.src = `https://www.google.com/recaptcha/api.js?render=${SITE_KEY}`;
            script.async = true;
            script.defer = true;

            script.onload = function () {
                // Ждем готовности API
                const checkReady = () => {
                    if (typeof grecaptcha !== 'undefined' && grecaptcha.ready) {
                        grecaptcha.ready(() => {
                            recaptchaLoaded = true;
                            recaptchaAPI = grecaptcha;
                            resolve();
                        });
                    } else {
                        setTimeout(checkReady, 100);
                    }
                };
                checkReady();
            };

            script.onerror = function () {
                reject(new Error('Ошибка загрузки reCAPTCHA API'));
            };

            document.head.appendChild(script);
        });
    }

    // Расширенная функция заполнения скрытых полей
    function fillHiddenFields(form) {
        // Заполняем URL страницы
        const pageUrlField = form.querySelector('input[name="page-url"]');
        if (pageUrlField) {
            pageUrlField.value = window.location.href;
        }

        // Заполняем заголовок страницы
        const pageTitleField = form.querySelector('input[name="page-title"]');
        if (pageTitleField) {
            pageTitleField.value = document.title;
        }

        // Для формы с товаром заполняем данные о продукте
        const productIdField = form.querySelector('input[name="product-id"]');
        const productNameField = form.querySelector('input[name="product-name"]');

        if (productIdField || productNameField) {
            // Попытаемся найти ID товара в URL или на странице
            const urlParams = new URLSearchParams(window.location.search);
            const productId = urlParams.get('product_id') || getProductIdFromPage();
            const productName = getProductNameFromPage();

            if (productIdField && productId && !productIdField.value) {
                productIdField.value = productId;
            }

            if (productNameField && productName && !productNameField.value) {
                productNameField.value = productName;
            }
        }
    }

    // Функция для получения ID товара со страницы
    function getProductIdFromPage() {
        // Ищем ID товара в различных местах
        const bodyClass = document.body.className;
        const match = bodyClass.match(/postid-(\d+)/);
        if (match) {
            return match[1];
        }

        // Ищем в data-атрибутах
        const productElement = document.querySelector('[data-product-id]');
        if (productElement) {
            return productElement.getAttribute('data-product-id');
        }

        // Ищем в мета-тегах
        const metaProductId = document.querySelector('meta[name="product-id"]');
        if (metaProductId) {
            return metaProductId.getAttribute('content');
        }

        return '';
    }

    // Функция для получения названия товара со страницы
    function getProductNameFromPage() {
        // Ищем заголовок товара
        const h1 = document.querySelector('h2.product_title, h2.entry-title, .product-title h2');
        if (h1) {
            return h1.textContent.trim();
        }

        // Ищем в title страницы
        const title = document.title;
        if (title) {
            return title.split(' | ')[0].split(' - ')[0].trim();
        }

        return '';
    }

    // Функция выполнения reCAPTCHA v3
    function executeRecaptcha(form, action = 'submit') {
        return new Promise((resolve, reject) => {
            if (!recaptchaLoaded || !recaptchaAPI) {
                reject(new Error('reCAPTCHA API не готова'));
                return;
            }

            try {
                recaptchaAPI.ready(function () {
                    recaptchaAPI.execute(SITE_KEY, { action: action })
                        .then(function (token) {
                            // Добавляем токен в форму
                            let tokenInput = form.querySelector('input[name="g-recaptcha-response"]');
                            if (!tokenInput) {
                                tokenInput = document.createElement('input');
                                tokenInput.type = 'hidden';
                                tokenInput.name = 'g-recaptcha-response';
                                form.appendChild(tokenInput);
                            }
                            tokenInput.value = token;

                            // Заполняем скрытые поля
                            fillHiddenFields(form);

                            resolve(token);
                        })
                        .catch(function (error) {
                            reject(error);
                        });
                });
            } catch (error) {
                reject(error);
            }
        });
    }

    // Простая валидация формы
    function validateForm(form) {
        const requiredFields = form.querySelectorAll('[required]');
        let isValid = true;

        requiredFields.forEach(function (field) {
            if (!field.value.trim()) {
                field.classList.add('is-invalid');
                isValid = false;
            } else {
                field.classList.remove('is-invalid');
            }
        });

        if (!isValid) {
            let messageContainer = form.querySelector('#form-messages, .form-messages');
            if (messageContainer) {
                messageContainer.innerHTML = '<div class="alert alert-danger">Пожалуйста, заполните все обязательные поля</div>';
            }
        }

        return isValid;
    }

    // Обработка отправки форм
    function setupFormHandlers() {
        const forms = document.querySelectorAll('form[action*="mails/"], #custom-contact-form, form[id*="contact"], form[id*="callback"], .contact-form');

        forms.forEach(function (form, index) {
            // Проверяем, не обработана ли уже форма
            if (form.hasAttribute('data-recaptcha-processed')) {
                return;
            }

            // Помечаем форму как обработанную
            form.setAttribute('data-recaptcha-processed', 'true');

            form.addEventListener('submit', function (e) {
                e.preventDefault();

                // Проверяем валидацию
                if (!validateForm(form)) {
                    return;
                }

                // Показываем индикатор загрузки
                const submitBtn = form.querySelector('button[type="submit"]');
                const originalText = submitBtn ? submitBtn.innerHTML : '';
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Отправка...';
                }

                // Выполняем reCAPTCHA и отправляем форму
                executeRecaptcha(form, 'contact_form')
                    .then(function (token) {
                        form.submit();
                    })
                    .catch(function (error) {
                        console.error('Ошибка reCAPTCHA:', error);

                        // Восстанавливаем кнопку
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = originalText;
                        }

                        // Показываем ошибку
                        let messageContainer = form.querySelector('#form-messages, .form-messages');
                        if (messageContainer) {
                            messageContainer.innerHTML = `<div class="alert alert-danger">Ошибка безопасности. Попробуйте обновить страницу.</div>`;
                        }
                    });
            });
        });
    }

    // Обработчик для загрузки файлов
    function setupFileHandlers() {
        const fileInputs = document.querySelectorAll('.file-upload');
        fileInputs.forEach(function (fileInput) {
            const wrapper = fileInput.closest('.file-upload-wrapper');
            const fileName = wrapper ? wrapper.querySelector('.file-name') : null;

            if (fileName) {
                fileInput.addEventListener('change', function (e) {
                    if (e.target.files.length > 0) {
                        fileName.textContent = e.target.files[0].name;
                    } else {
                        fileName.textContent = 'Файл не прикреплен';
                    }
                });
            }
        });

        // Обработка кнопок file upload
        const fileUploadBtns = document.querySelectorAll('.file-upload-btn');
        fileUploadBtns.forEach(function (btn) {
            btn.addEventListener('click', function () {
                const wrapper = btn.closest('.file-upload-wrapper');
                const fileInput = wrapper ? wrapper.querySelector('.file-upload') : null;

                if (fileInput) {
                    fileInput.click();
                }
            });
        });
    }

    // Заполнение полей во всех формах при загрузке
    function initializeAllForms() {
        const forms = document.querySelectorAll('form');
        forms.forEach(function (form) {
            fillHiddenFields(form);
        });

        // Обработчик для модальных окон - заполняем поля при открытии модалки
        const modals = document.querySelectorAll('.modal');
        modals.forEach(function (modal) {
            modal.addEventListener('show.bs.modal', function () {
                const form = modal.querySelector('form');
                if (form) {
                    fillHiddenFields(form);
                }
            });
        });

        // Для форм с количеством - обновляем данные при изменении количества
        const quantityInputs = document.querySelectorAll('input[name="quantity"]');
        quantityInputs.forEach(function (input) {
            input.addEventListener('change', function () {
                const form = input.closest('form');
                if (form) {
                    fillHiddenFields(form);
                }
            });
        });
    }

    // Инициализация
    loadRecaptchaAPI()
        .then(() => {
            initializeAllForms();
            setupFormHandlers();
            setupFileHandlers();
        })
        .catch((error) => {
            // Если reCAPTCHA не работает, все равно настраиваем формы
            initializeAllForms();
            setupFormHandlers();
            setupFileHandlers();
            alert('Ошибка загрузки системы безопасности. Обновите страницу или попробуйте позже.');
        });
});