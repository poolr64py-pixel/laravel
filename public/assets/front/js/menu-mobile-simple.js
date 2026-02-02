// Menu Mobile - FORÇAR ABERTO
(function() {
    'use strict';
    
    if (window.innerWidth > 991) return;
    
    console.log('🔧 Mobile menu anti-close');
    
    window.addEventListener('load', function() {
        
        setTimeout(function() {
            
            const toggles = document.querySelectorAll('.navbar-nav .nav-link.toggle');
            console.log('Toggles:', toggles.length);
            
            toggles.forEach(function(toggle, idx) {
                
                const dropdown = toggle.nextElementSibling;
                if (!dropdown || !dropdown.classList.contains('menu-dropdown')) return;
                
                console.log('Config toggle', idx);
                
                // Forçar fechado inicial
                dropdown.style.display = 'none';
                dropdown.dataset.menuIndex = idx;
                
                // Remover TODOS os eventos
                const newToggle = toggle.cloneNode(true);
                toggle.replaceWith(newToggle);
                
                // Clonar dropdown também
                const newDropdown = dropdown.cloneNode(true);
                dropdown.replaceWith(newDropdown);
                
                // Variável de estado
                let menuAberto = false;
                
                // Adicionar evento NO CAPTURE
                newToggle.onclick = function(e) {
                    console.log('>>> CLICK menu', idx, 'aberto?', menuAberto);
                    
                    e.preventDefault();
                    e.stopPropagation();
                    e.stopImmediatePropagation();
                    
                    if (menuAberto) {
                        console.log('Já aberto, ignorando');
                        return false;
                    }
                    
                    // Fechar TODOS
                    document.querySelectorAll('.menu-dropdown').forEach(d => {
                        d.style.display = 'none';
                    });
                    
                    // Abrir ESTE
                    newDropdown.style.display = 'block';
                    menuAberto = true;
                    
                    console.log('✅ Abriu menu', idx);
                    
                    // NUNCA FECHAR - só muda quando clica em outro
                    
                    return false;
                };
                
                // Bloquear propagação no dropdown
                newDropdown.onclick = function(e) {
                    e.stopPropagation();
                };
                
            });
            
            // Bloquear clicks no document
            document.addEventListener('click', function(e) {
                if (e.target.closest('.navbar-nav .menu-dropdown')) {
                    e.stopPropagation();
                }
            }, true);
            
            console.log('✅ Mobile menu configurado');
            
        }, 1000);
        
    });
    
})();
