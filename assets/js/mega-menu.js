document.addEventListener('DOMContentLoaded', function () {
    // Функция для работы с категориями меню на десктопе
    const categoryLinks = document.querySelectorAll('.category-menu .nav-link');
    categoryLinks.forEach((link) => {
        link.addEventListener('mouseover', function () {
            // Удаляем активный класс со всех ссылок
            categoryLinks.forEach((l) => l.classList.remove('active'));
            // Добавляем активный класс текущей ссылке
            this.classList.add('active');

            // Показываем соответствующий контент
            const target = this.getAttribute('data-target');
            document.querySelectorAll('.subcategory-content').forEach((content) => {
                content.classList.remove('active');
            });

            document.getElementById(`${target}-content`).classList.add('active');
        });
    });

    // Обработка мобильной навигации с кнопками "Назад"
    // Переключение между уровнями меню
    const menuItems = document.querySelectorAll('.mobile-menu-item');
    const backButtons = document.querySelectorAll('.back-button');

    function navigateToView(viewId) {
        // Скрыть все представления
        document.querySelectorAll('.mobile-view').forEach((view) => {
            view.classList.remove('active');
        });

        // Показать выбранное представление
        document.getElementById(viewId).classList.add('active');
    }

    // Клик по элементу меню для перехода на следующий уровень
    menuItems.forEach((item) => {
        item.addEventListener('click', function () {
            const targetView = this.getAttribute('data-view');
            navigateToView(targetView);
        });
    });

    // Клик по кнопке "Назад" для возврата на предыдущий уровень
    backButtons.forEach((button) => {
        button.addEventListener('click', function () {
            const targetView = this.getAttribute('data-view');
            navigateToView(targetView);
        });
    });

    // Открытие/закрытие мега-меню при наведении на десктопе
    const productsDropdown = document.getElementById('productsDropdown');
    const megaMenu = document.querySelector('.dropdown-menu.mega-menu');

    // Функция для проверки размера экрана (десктоп или мобильный)
    function isDesktop() {
        return window.innerWidth >= 992; // Bootstrap lg breakpoint
    }

    // Если это десктоп, используем hover для открытия меню
    if (isDesktop()) {
        productsDropdown.addEventListener('mouseover', function () {
            megaMenu.classList.add('show');
        });

        document
            .querySelector('.nav-item.dropdown')
            .addEventListener('mouseleave', function () {
                megaMenu.classList.remove('show');
            });
    }

    // Обновить поведение при изменении размера окна
    window.addEventListener('resize', function () {
        if (isDesktop()) {
            // Десктопное поведение
            productsDropdown.addEventListener('mouseover', function () {
                megaMenu.classList.add('show');
            });

            document
                .querySelector('.nav-item.dropdown')
                .addEventListener('mouseleave', function () {
                    megaMenu.classList.remove('show');
                });
        } else {
            // Мобильное поведение - используем только клики
            megaMenu.classList.remove('show');
        }
    });

    // Добавляем обработчик для кнопки закрытия мобильного меню
    const closeButton = document.querySelector('.offcanvas .btn-close');
    closeButton.addEventListener('click', function () {
        // Возвращаем активное состояние первому уровню меню
        document.querySelectorAll('.mobile-view').forEach((view) => {
            view.classList.remove('active');
        });
        // Активируем главное меню (первый уровень)
        document.getElementById('main-menu-view').classList.add('active');
    });

    // Также обрабатываем случай, когда меню закрывается кликом вне меню
    const offcanvasElement = document.querySelector('#mobileMenu');
    offcanvasElement.addEventListener('hidden.bs.offcanvas', function () {
        // Возвращаем активное состояние первому уровню меню
        document.querySelectorAll('.mobile-view').forEach((view) => {
            view.classList.remove('active');
        });
        // Активируем главное меню (первый уровень)
        document.getElementById('main-menu-view').classList.add('active');
    });

    document.addEventListener('DOMContentLoaded', function () {
        // Получаем ссылку на навбар
        const navbar = document.querySelector('.navbar');

        // Создаем заполнитель для компенсации высоты
        const placeholder = document.createElement('div');
        placeholder.className = 'navbar-placeholder';

        // Вставляем заполнитель после навбара
        navbar.parentNode.insertBefore(placeholder, navbar.nextSibling);

        // Функция для обработки скролла
        function handleScroll() {
            const scrollPosition = window.scrollY;

            // Если прокрутили достаточно, делаем панель фиксированной
            if (scrollPosition > 50) {
                // можно настроить нужное значение
                if (!navbar.classList.contains('navbar-fixed')) {
                    // Устанавливаем высоту заполнителя равной высоте навбара
                    placeholder.style.height = navbar.offsetHeight + 'px';
                    placeholder.classList.add('active');

                    // Делаем навбар фиксированным
                    navbar.classList.add('navbar-fixed');
                }
            } else {
                // Возвращаем все в исходное состояние
                navbar.classList.remove('navbar-fixed');
                placeholder.classList.remove('active');
            }
        }

        // Вызываем функцию при скролле
        window.addEventListener('scroll', handleScroll);

        // Также вызываем при изменении размера окна
        window.addEventListener('resize', function () {
            if (navbar.classList.contains('navbar-fixed')) {
                placeholder.style.height = navbar.offsetHeight + 'px';
            }
        });

        // Начальная проверка при загрузке
        handleScroll();
    });
});

document.addEventListener('DOMContentLoaded', function () {
    // Получаем ссылку на навбар
    const navbar = document.querySelector('#navbar');

    // Создаем заполнитель для компенсации высоты
    const placeholder = document.createElement('div');
    placeholder.className = 'navbar-placeholder';

    // Вставляем заполнитель после навбара
    navbar.parentNode.insertBefore(placeholder, navbar.nextSibling);

    // Функция для обработки скролла
    function handleScroll() {
        const scrollPosition = window.scrollY;

        // Если прокрутили достаточно, делаем панель фиксированной
        if (scrollPosition > 30) {
            // можно настроить нужное значение
            if (!navbar.classList.contains('navbar-fixed')) {
                // Устанавливаем высоту заполнителя равной высоте навбара
                placeholder.style.height = navbar.offsetHeight + 'px';
                placeholder.classList.add('active');

                // Делаем навбар фиксированным
                navbar.classList.add('navbar-fixed');
            }
        } else {
            // Возвращаем все в исходное состояние
            navbar.classList.remove('navbar-fixed');
            placeholder.classList.remove('active');
        }
    }

    // Вызываем функцию при скролле
    window.addEventListener('scroll', handleScroll);

    // Также вызываем при изменении размера окна
    window.addEventListener('resize', function () {
        if (navbar.classList.contains('navbar-fixed')) {
            placeholder.style.height = navbar.offsetHeight + 'px';
        }
    });

    // Начальная проверка при загрузке
    handleScroll();
});