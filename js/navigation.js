/**
 * SVETOGOR NAVIGATION
 * Объединенная логика для десктопа и мобильных устройств
 */

(function() {
    'use strict';

    // =========================================================================
    // UTILITY FUNCTIONS
    // =========================================================================
    
    const isDesktop = () => window.innerWidth >= 992;
    
    const navigateToView = (viewId) => {
        document.querySelectorAll('.mobile-view').forEach(view => 
            view.classList.remove('active')
        );
        
        const targetView = document.getElementById(viewId);
        if (targetView) {
            targetView.classList.add('active');
        }
    };

    // =========================================================================
    // DESKTOP MEGA MENU
    // =========================================================================
    
    const initDesktopMegaMenu = () => {
        const categoryLinks = document.querySelectorAll('.category-menu .nav-link');
        
        categoryLinks.forEach(link => {
            link.addEventListener('mouseover', function() {
                // Убрать активный класс со всех
                categoryLinks.forEach(l => l.classList.remove('active'));
                
                // Добавить текущей
                this.classList.add('active');
                
                // Показать контент
                const target = this.getAttribute('data-target');
                if (target) {
                    document.querySelectorAll('.subcategory-content').forEach(content =>
                        content.classList.remove('active')
                    );
                    
                    const targetContent = document.getElementById(`${target}-content`);
                    if (targetContent) {
                        targetContent.classList.add('active');
                    }
                }
            });
        });
    };

    const initMegaMenuHover = () => {
        const productsDropdown = document.getElementById('productsDropdown');
        const megaMenu = document.querySelector('.dropdown-menu.mega-menu');
        
        if (!productsDropdown || !megaMenu) return;
        
        const parentLi = productsDropdown.closest('li');
        if (!parentLi) return;

        if (isDesktop()) {
            parentLi.addEventListener('mouseenter', () => {
                megaMenu.classList.add('show');
            });

            parentLi.addEventListener('mouseleave', () => {
                megaMenu.classList.remove('show');
            });
        }
    };

    // =========================================================================
    // MOBILE MENU
    // =========================================================================
    
    const initMobileMenu = () => {
        // Навигация между уровнями
        document.querySelectorAll('.mobile-menu-item').forEach(item => {
            item.addEventListener('click', function() {
                const targetView = this.getAttribute('data-view');
                if (targetView) {
                    navigateToView(targetView);
                }
            });
        });

        // Кнопки "Назад"
        document.querySelectorAll('.back-button').forEach(button => {
            button.addEventListener('click', function() {
                const targetView = this.getAttribute('data-view');
                if (targetView) {
                    navigateToView(targetView);
                }
            });
        });

        // Сброс при закрытии
        const closeButton = document.querySelector('.offcanvas .btn-close');
        if (closeButton) {
            closeButton.addEventListener('click', () => {
                navigateToView('main-menu-view');
            });
        }

        const offcanvasElement = document.querySelector('#mobileMenu');
        if (offcanvasElement) {
            offcanvasElement.addEventListener('hidden.bs.offcanvas', () => {
                navigateToView('main-menu-view');
            });
        }
    };

    // =========================================================================
    // STICKY NAVBAR
    // =========================================================================
    
    const initStickyNavbar = () => {
        const navbar = document.querySelector('#navbar');
        if (!navbar) return;

        const placeholder = document.createElement('div');
        placeholder.className = 'navbar-placeholder';
        navbar.parentNode.insertBefore(placeholder, navbar.nextSibling);

        const handleScroll = () => {
            const scrollPosition = window.scrollY;

            if (scrollPosition > 30) {
                if (!navbar.classList.contains('navbar-fixed')) {
                    placeholder.style.height = navbar.offsetHeight + 'px';
                    placeholder.classList.add('active');
                    navbar.classList.add('navbar-fixed');
                }
            } else {
                navbar.classList.remove('navbar-fixed');
                placeholder.classList.remove('active');
            }
        };

        window.addEventListener('scroll', handleScroll);
        window.addEventListener('resize', () => {
            if (navbar.classList.contains('navbar-fixed')) {
                placeholder.style.height = navbar.offsetHeight + 'px';
            }
        });

        handleScroll(); // Начальная проверка
    };

    // =========================================================================
    // INITIALIZATION
    // =========================================================================
    
    document.addEventListener('DOMContentLoaded', () => {
        initDesktopMegaMenu();
        initMegaMenuHover();
        initMobileMenu();
        initStickyNavbar();
    });

})();