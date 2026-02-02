// Menu Mobile - Versão Ultra Simples
(function() {
    'use strict';
    
    console.log('Menu mobile v7 - carregado');
    
    // Esperar tudo carregar
    window.addEventListener('load', function() {
        
        if (window.innerWidth > 991) {
            console.log('Desktop - não fazer nada');
            return;
        }
        
        console.log('Mobile detectado');
        
        // Pegar TODOS os links com classe .toggle
        const toggles = document.querySelectorAll('.navbar-nav .toggle');
        console.log('Toggles:', toggles.length);
        
        toggles.forEach(function(toggle, i) {
            const dropdown = toggle.nextElementSibling;
            
            if (!dropdown || !dropdown.classList.contains('menu-dropdown')) {
                console.log('Toggle', i, 'sem dropdown');
                return;
            }
            
            console.log('Toggle', i, 'configurado:', toggle.textContent.trim());
            
            // SEM CLONAR - apenas adicionar evento
            toggle.onclick = function(e) {
                console.log('CLICK em:', toggle.textContent.trim());
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                
                // Toggle simples
                if (dropdown.style.display === 'block') {
                    console.log('Fechando');
                    dropdown.style.display = 'none';
                } else {
                    console.log('Abrindo');
                    // Fechar outros
                    document.querySelectorAll('.menu-dropdown').forEach(d => d.style.display = 'none');
                    // Abrir este
                    dropdown.style.display = 'block';
                }
                
                return false;
            };
        });
        
        console.log('Configuração OK');
    });
})();
