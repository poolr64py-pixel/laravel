// Menu Mobile - Versão Final Funcional
document.addEventListener('DOMContentLoaded', function() {
    console.log('Menu mobile iniciando...');
    
    // Só no mobile
    if (window.innerWidth > 991) return;
    
    console.log('Mobile confirmado');
    
    // Esperar 500ms para garantir que tudo carregou
    setTimeout(function() {
        
        const toggles = document.querySelectorAll('.navbar-nav .nav-link.toggle');
        console.log('Toggles encontrados:', toggles.length);
        
        toggles.forEach(function(toggle) {
            const parent = toggle.parentElement;
            const dropdown = parent.querySelector('.menu-dropdown');
            
            if (!dropdown) return;
            
            console.log('Configurando:', toggle.textContent.trim());
            
            // Forçar estado inicial fechado
            dropdown.style.display = 'none';
            
            // Remover TODOS os eventos antigos
            const newToggle = toggle.cloneNode(true);
            toggle.parentNode.replaceChild(newToggle, toggle);
            
            // Adicionar evento NO CAPTURE (antes de outros)
            newToggle.addEventListener('touchstart', handleClick, true);
            newToggle.addEventListener('click', handleClick, true);
            
            function handleClick(e) {
                console.log('>>> CLICK:', newToggle.textContent.trim());
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                
                const isOpen = dropdown.style.display === 'block';
                console.log('Estava aberto?', isOpen);
                
                if (isOpen) {
                    dropdown.style.display = 'none';
                    console.log('Fechou');
                } else {
                    // Fechar TODOS
                    document.querySelectorAll('.menu-dropdown').forEach(d => {
                        d.style.display = 'none';
                    });
                    // Abrir ESTE
                    dropdown.style.display = 'block';
                    console.log('Abriu!');
                }
                
                return false;
            }
        });
        
        console.log('Menu mobile pronto!');
        
    }, 500);
});
