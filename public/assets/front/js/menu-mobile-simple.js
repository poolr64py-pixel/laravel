/**
 * Menu Mobile - Solução Profissional
 * Funciona para qualquer dropdown no menu
 */
(function() {
    'use strict';
    
    function initMobileMenu() {
        const isMobile = window.innerWidth <= 991;
        
        if (!isMobile) return;
        
        const menuToggles = document.querySelectorAll('.navbar-nav .nav-link.toggle');
        
        menuToggles.forEach(function(toggle) {
            const parentItem = toggle.closest('.nav-item');
            const dropdown = parentItem.querySelector('.menu-dropdown');
            
            if (!dropdown) return;
            
            // Remover eventos anteriores (clonar)
            const newToggle = toggle.cloneNode(true);
            toggle.parentNode.replaceChild(newToggle, toggle);
            
            // Adicionar evento de click
            newToggle.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                const icon = this.querySelector('i');
                const isOpen = dropdown.classList.contains('open');
                
                // Fechar outros dropdowns
                document.querySelectorAll('.menu-dropdown.open').forEach(function(d) {
                    if (d !== dropdown) {
                        d.classList.remove('open');
                        d.style.display = 'none';
                    }
                });
                
                // Resetar ícones
                document.querySelectorAll('.navbar-nav .toggle i').forEach(function(i) {
                    if (i !== icon) {
                        i.classList.remove('fa-minus');
                        i.classList.add('fa-plus');
                    }
                });
                
                // Toggle este dropdown
                if (isOpen) {
                    dropdown.classList.remove('open');
                    dropdown.style.display = 'none';
                    if (icon) {
                        icon.classList.remove('fa-minus');
                        icon.classList.add('fa-plus');
                    }
                } else {
                    dropdown.classList.add('open');
                    dropdown.style.display = 'block';
                    if (icon) {
                        icon.classList.remove('fa-plus');
                        icon.classList.add('fa-minus');
                    }
                }
            });
            
            // Impedir que cliques dentro do dropdown o fechem
            dropdown.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        });
        
        // Fechar dropdown ao clicar fora do menu
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.navbar-nav')) {
                document.querySelectorAll('.menu-dropdown.open').forEach(function(d) {
                    d.classList.remove('open');
                    d.style.display = 'none';
                });
                document.querySelectorAll('.navbar-nav .toggle i').forEach(function(i) {
                    i.classList.remove('fa-minus');
                    i.classList.add('fa-plus');
                });
            }
        });
    }
    
    // Inicializar quando DOM carregar
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initMobileMenu);
    } else {
        initMobileMenu();
    }
    
    // Reinicializar ao redimensionar
    let resizeTimer;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(initMobileMenu, 250);
    });
})();
