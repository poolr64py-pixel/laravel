(function() {
    console.log('Menu Mobile v6 - SEM CLONAR');
    
    setTimeout(function() {
        const allToggles = document.querySelectorAll('.navbar-nav .toggle');
        console.log('Toggles encontrados:', allToggles.length);
        
        allToggles.forEach(function(toggle) {
            const texto = toggle.textContent.toLowerCase();
            console.log('Toggle texto:', texto);
            
            if (!texto.includes('imoveis') && !texto.includes('imóveis')) {
                console.log('Ignorando - nao e imoveis');
                return;
            }
            
            console.log('Configurando Imoveis...');
            
            const dropdown = toggle.parentElement.querySelector('.menu-dropdown');
            if (!dropdown) {
                console.log('ERRO: dropdown nao encontrado');
                return;
            }
            
            // NÃO clonar - adicionar evento em capture phase (mais cedo)
            toggle.addEventListener('click', function(e) {
                console.log('>>> CLICK CAPTURADO <<<');
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                
                const aberto = dropdown.style.display === 'block';
                console.log('Menu esta:', aberto ? 'aberto' : 'fechado');
                
                if (aberto) {
                    dropdown.style.display = 'none';
                    console.log('Fechando...');
                } else {
                    // Fechar todos primeiro
                    document.querySelectorAll('.menu-dropdown').forEach(function(d) {
                        d.style.display = 'none';
                    });
                    dropdown.style.display = 'block';
                    console.log('Abrindo...');
                }
                
                return false;
            }, true); // true = capture phase (executa ANTES de outros)
            
            console.log('Toggle configurado!');
        });
        
        console.log('Setup completo!');
    }, 1000);
})();
