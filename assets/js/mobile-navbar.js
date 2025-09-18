/**
 * Mobile Navbar - Menu Hambúrguer
 * Funcionalidades: abrir/fechar menu, submenu, animações
 */

document.addEventListener('DOMContentLoaded', function() {
    // Elementos do DOM
    const hamburgerMenu = document.querySelector('.hamburger-menu');
    const mobileMenuOverlay = document.querySelector('.mobile-menu-overlay');
    const body = document.body;
    const submenuToggles = document.querySelectorAll('.submenu-toggle');
    
    // Verificar se os elementos existem
    if (!hamburgerMenu || !mobileMenuOverlay) {
        console.warn('Mobile navbar elements not found');
        return;
    }
    
    // Função para abrir menu
    function openMenu() {
        hamburgerMenu.classList.add('active');
        mobileMenuOverlay.classList.add('active');
        body.classList.add('menu-open');
        
        // Adicionar listener para fechar com ESC
        document.addEventListener('keydown', handleEscKey);
    }
    
    // Função para fechar menu
    function closeMenu() {
        hamburgerMenu.classList.remove('active');
        mobileMenuOverlay.classList.remove('active');
        body.classList.remove('menu-open');
        
        // Remover listener do ESC
        document.removeEventListener('keydown', handleEscKey);
        
        // Fechar todos os submenus
        closeAllSubmenus();
    }
    
    // Função para alternar menu
    function toggleMenu() {
        if (mobileMenuOverlay.classList.contains('active')) {
            closeMenu();
        } else {
            openMenu();
        }
    }
    
    // Função para lidar com tecla ESC
    function handleEscKey(event) {
        if (event.key === 'Escape') {
            closeMenu();
        }
    }
    
    // Função para fechar todos os submenus
    function closeAllSubmenus() {
        const activeSubmenus = document.querySelectorAll('.mobile-submenu.active');
        const activeToggles = document.querySelectorAll('.submenu-toggle.active');
        
        activeSubmenus.forEach(submenu => {
            submenu.classList.remove('active');
        });
        
        activeToggles.forEach(toggle => {
            toggle.classList.remove('active');
        });
    }
    
    // Função para alternar submenu
    function toggleSubmenu(event) {
        event.preventDefault();
        event.stopPropagation();
        
        const toggle = event.currentTarget;
        const parentLi = toggle.closest('.has-submenu');
        const submenu = parentLi ? parentLi.querySelector('.mobile-submenu') : null;
        
        if (!submenu) {
            console.warn('Submenu not found');
            return;
        }
        
        // Fechar outros submenus
        submenuToggles.forEach(otherToggle => {
            if (otherToggle !== toggle) {
                otherToggle.classList.remove('active');
                const otherParentLi = otherToggle.closest('.has-submenu');
                const otherSubmenu = otherParentLi ? otherParentLi.querySelector('.mobile-submenu') : null;
                if (otherSubmenu) {
                    otherSubmenu.classList.remove('active');
                }
            }
        });
        
        // Alternar submenu atual
        const isActive = toggle.classList.contains('active');
        if (isActive) {
            toggle.classList.remove('active');
            submenu.classList.remove('active');
        } else {
            toggle.classList.add('active');
            submenu.classList.add('active');
        }
    }
    
    // Event Listeners
    
    // Click no botão hambúrguer
    hamburgerMenu.addEventListener('click', function(event) {
        event.preventDefault();
        event.stopPropagation();
        toggleMenu();
    });
    
    // Click no overlay para fechar (apenas no fundo, não nos itens do menu)
    mobileMenuOverlay.addEventListener('click', function(event) {
        if (event.target === mobileMenuOverlay) {
            closeMenu();
        }
    });
    
    // Click nos botões de submenu
    submenuToggles.forEach(toggle => {
        toggle.addEventListener('click', toggleSubmenu);
    });
    
    // Click nos links do menu principal (fechar menu)
    const mainMenuLinks = document.querySelectorAll('.mobile-menu-items > li > a:not(.has-submenu)');
    mainMenuLinks.forEach(link => {
        link.addEventListener('click', function() {
            // Delay para permitir navegação suave
            setTimeout(closeMenu, 100);
        });
    });
    
    // Click nos links do submenu (fechar menu)
    const submenuLinks = document.querySelectorAll('.mobile-submenu a');
    submenuLinks.forEach(link => {
        link.addEventListener('click', function() {
            // Delay para permitir navegação suave
            setTimeout(closeMenu, 100);
        });
    });
    
    // Redimensionamento da janela - fechar menu se sair do mobile
    let resizeTimer;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            if (window.innerWidth > 980) {
                closeMenu();
            }
        }, 250);
    });
    
    // Prevenir scroll quando menu está aberto (iOS Safari fix)
    let startY = 0;
    mobileMenuOverlay.addEventListener('touchstart', function(event) {
        startY = event.touches[0].clientY;
    });
    
    mobileMenuOverlay.addEventListener('touchmove', function(event) {
        const currentY = event.touches[0].clientY;
        const menuItems = document.querySelector('.mobile-menu-items');
        
        // Se não estamos scrollando dentro dos itens do menu, prevenir scroll
        if (!menuItems.contains(event.target)) {
            event.preventDefault();
        }
    });
    
    // Função para smooth scroll para âncoras
    function smoothScrollToAnchor(targetId) {
        const targetElement = document.getElementById(targetId);
        if (targetElement) {
            const offsetTop = targetElement.offsetTop - 70; // Compensar altura do navbar
            window.scrollTo({
                top: offsetTop,
                behavior: 'smooth'
            });
        }
    }
    
    // Lidar com links de âncora
    const anchorLinks = document.querySelectorAll('.mobile-menu-items a[href^="#"]');
    anchorLinks.forEach(link => {
        link.addEventListener('click', function(event) {
            const href = this.getAttribute('href');
            if (href.length > 1) { // Não é apenas "#"
                event.preventDefault();
                const targetId = href.substring(1);
                closeMenu();
                setTimeout(() => {
                    smoothScrollToAnchor(targetId);
                }, 300);
            }
        });
    });
    
    // Debug info (remover em produção)
    console.log('Mobile navbar initialized successfully');
});

// Função global para fechar menu (pode ser chamada de outros scripts)
window.closeMobileMenu = function() {
    const mobileMenuOverlay = document.querySelector('.mobile-menu-overlay');
    const hamburgerMenu = document.querySelector('.hamburger-menu');
    const body = document.body;
    
    if (mobileMenuOverlay && hamburgerMenu) {
        hamburgerMenu.classList.remove('active');
        mobileMenuOverlay.classList.remove('active');
        body.classList.remove('menu-open');
    }
};