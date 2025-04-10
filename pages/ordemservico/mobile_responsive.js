// Script para gerenciar responsividade da página de ordem de serviço

document.addEventListener('DOMContentLoaded', function() {
    // Função para ajustar tabelas em dispositivos móveis
    function adjustTablesToMobile() {
        const isMobile = window.innerWidth <= 768;
        const isVerySmall = window.innerWidth <= 320;
        
        // Adicionar classes específicas para mobile e tema escuro
        document.body.classList.toggle('is-mobile', isMobile);
        document.body.classList.toggle('is-very-small', isVerySmall);
        document.body.classList.toggle('dark-theme', isMobile);
        
        // Ajustar elementos específicos para visualização mobile
        if (isMobile) {
            // Adicionar data-cell para células da tabela que não possuem
            const tableCells = document.querySelectorAll('td:not([data-cell])');
            tableCells.forEach(cell => {
                // Pegar o texto do header correspondente
                const headerIndex = Array.from(cell.parentNode.children).indexOf(cell);
                const tableHeaders = cell.closest('table').querySelectorAll('th');
                
                if (tableHeaders[headerIndex]) {
                    const headerText = tableHeaders[headerIndex].textContent.trim();
                    cell.setAttribute('data-cell', headerText);
                }
            });
            
            // Ajustar tamanhos de imagens e botões
            const actionButtons = document.querySelectorAll('button');
            actionButtons.forEach(button => {
                if (!button.classList.contains('mobile-adjusted')) {
                    const images = button.querySelectorAll('img');
                    images.forEach(img => {
                        // Ajustar tamanho das imagens em botões
                        if (isVerySmall) {
                            img.style.height = '20px';
                            img.style.width = '20px';
                        } else {
                            img.style.height = '25px';
                            img.style.width = '25px';
                        }
                    });
                    button.classList.add('mobile-adjusted');
                }
            });
            
            // Verificar overflow nas tabelas
            const tables = document.querySelectorAll('table');
            tables.forEach(table => {
                if (table.offsetWidth > window.innerWidth) {
                    table.style.width = '100%';
                    table.style.minWidth = isVerySmall ? '280px' : '300px';
                }
            });
        }
    }
    
    // Executar a função no carregamento
    adjustTablesToMobile();
    
    // Executar a função quando a janela for redimensionada
    window.addEventListener('resize', adjustTablesToMobile);
}); 