document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('custom-contact-form');
  const submitBtn = document.getElementById('submit-btn');
  const btnText = submitBtn.querySelector('.btn-text');
  const btnSpinner = submitBtn.querySelector('.btn-spinner');
  const messagesDiv = document.getElementById('form-messages');

  // Заполняем скрытые поля
  fillHiddenFields();

  // Обработка загрузки файла
  setupFileUpload();

  // Обработка отправки формы
  form.addEventListener('submit', handleFormSubmit);

  function fillHiddenFields() {
    document.querySelector('input[name="page-url"]').value = window.location.href;
    document.querySelector('input[name="page-title"]').value = document.title;
    document.querySelector('input[name="referrer"]').value = document.referrer || '';
    document.querySelector('input[name="user-agent"]').value = navigator.userAgent;
  }

  function setupFileUpload() {
    const fileInput = document.querySelector('.file-upload');
    const fileName = document.querySelector('.file-name');

    if (fileInput && fileName) {
      fileInput.addEventListener('change', function (e) {
        if (e.target.files && e.target.files.length > 0) {
          const file = e.target.files[0];
          const maxSize = 5 * 1024 * 1024; // 5MB

          if (file.size > maxSize) {
            showMessage('Размер файла не должен превышать 5MB', 'danger');
            this.value = '';
            fileName.textContent = 'Файл не прикреплен';
            return;
          }

          fileName.textContent = file.name;
        } else {
          fileName.textContent = 'Файл не прикреплен';
        }
      });
    }
  }

  function handleFormSubmit(e) {
    e.preventDefault();

    // Очищаем предыдущие сообщения
    clearMessages();
    clearValidation();

    // Валидация
    if (!validateForm()) {
      return;
    }

    // Проверяем reCAPTCHA
    const recaptchaResponse = grecaptcha.getResponse();
    if (!recaptchaResponse) {
      showMessage('Пожалуйста, подтвердите, что вы не робот', 'danger');
      return;
    }

    // Показываем спиннер
    setLoadingState(true);

    // Собираем данные формы
    const formData = new FormData(form);
    formData.append('action', 'submit_custom_contact_form');
    formData.append('nonce', customForm.nonce);
    formData.append('g-recaptcha-response', recaptchaResponse);

    // Отправляем AJAX запрос
    fetch(customForm.ajaxUrl, {
      method: 'POST',
      body: formData
    })
      .then(response => response.json())
      .then(data => {
        setLoadingState(false);

        if (data.success) {
          showMessage(data.data.message || 'Спасибо! Ваша заявка отправлена. Мы свяжемся с вами в ближайшее время.', 'success');
          resetForm();
        } else {
          showMessage(data.data || 'Произошла ошибка при отправке. Попробуйте еще раз.', 'danger');
        }

        // Сбрасываем reCAPTCHA
        grecaptcha.reset();
      })
      .catch(error => {
        setLoadingState(false);
        showMessage('Произошла ошибка при отправке. Попробуйте еще раз.', 'danger');
        grecaptcha.reset();
      });
  }

  function validateForm() {
    let isValid = true;

    // Валидация имени
    const name = form.querySelector('input[name="your-name"]');
    if (!name.value.trim()) {
      markFieldInvalid(name);
      isValid = false;
    }

    // Валидация телефона
    const phone = form.querySelector('input[name="your-phone"]');
    const phoneRegex = /^[\+]?[1-9][\d]{0,15}$/;
    if (!phone.value.trim() || !phoneRegex.test(phone.value.replace(/[\s\-\(\)]/g, ''))) {
      markFieldInvalid(phone);
      isValid = false;
    }

    // Валидация email
    const email = form.querySelector('input[name="your-email"]');
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!email.value.trim() || !emailRegex.test(email.value)) {
      markFieldInvalid(email);
      isValid = false;
    }

    return isValid;
  }

  function markFieldInvalid(field) {
    field.classList.add('is-invalid');
    field.addEventListener('input', function () {
      this.classList.remove('is-invalid');
    }, { once: true });
  }

  function clearValidation() {
    form.querySelectorAll('.is-invalid').forEach(field => {
      field.classList.remove('is-invalid');
    });
  }

  function setLoadingState(loading) {
    if (loading) {
      submitBtn.disabled = true;
      btnText.style.display = 'none';
      btnSpinner.style.display = 'inline-block';
    } else {
      submitBtn.disabled = false;
      btnText.style.display = 'inline-block';
      btnSpinner.style.display = 'none';
    }
  }

  function showMessage(message, type) {
    messagesDiv.innerHTML = `<div class="alert alert-${type}">${message}</div>`;
    messagesDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
  }

  function clearMessages() {
    messagesDiv.innerHTML = '';
  }

  function resetForm() {
    form.reset();

    // Сбрасываем чекбоксы
    form.querySelectorAll('.image-checkbox-container input[type="checkbox"]').forEach(checkbox => {
      checkbox.checked = false;
    });

    // Сбрасываем файл
    const fileName = document.querySelector('.file-name');
    if (fileName) {
      fileName.textContent = 'Файл не прикреплен';
    }

    // Очищаем валидацию
    clearValidation();
  }
});