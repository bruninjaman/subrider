// Script para gerenciar responsividade da página de ordem de serviço

document.addEventListener('DOMContentLoaded', function() {
    adjustForMobile();
    applyModernLayout();
    
    // Observador de mudanças no DOM para conteúdo dinâmico
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'childList') {
                adjustForMobile();
                applyModernLayout();
            }
        });
    });
    
    observer.observe(document.body, {
        childList: true,
        subtree: true
    });
});

// Timeout para redimensionamento da janela
let resizeTimeout;
window.addEventListener('resize', function() {
    clearTimeout(resizeTimeout);
    resizeTimeout = setTimeout(function() {
        adjustForMobile();
        applyModernLayout();
    }, 250);
});

function adjustForMobile() {
    const isMobile = window.innerWidth <= 768;
    const isSmallScreen = window.innerWidth <= 480;
    const isVerySmall = window.innerWidth <= 320;
    
    if (isMobile) {
        // Aplicar tema escuro
        document.documentElement.classList.add('dark-theme');
        document.body.classList.add('is-mobile', 'dark-theme');
        
        if (isVerySmall) {
            document.body.classList.add('is-very-small');
        } else {
            document.body.classList.remove('is-very-small');
        }
        
        // Ajustar tabelas
        adjustTables();
        
        // Ajustar imagens
        adjustImages(isSmallScreen, isVerySmall);
        
        // Ajustar botões
        adjustButtons(isSmallScreen, isVerySmall);
        
        // Aplicar wrappers em tabelas
        applyTableWrappers();
        
        // Ajustar layout de colunas
        adjustColumnLayout();
        
        // Ajustar informações da moto
        adjustMotoInfo();
        
        // Ajustar modais
        adjustModals();
        
    } else {
        // Remover ajustes mobile quando não é móvel
        document.documentElement.classList.remove('dark-theme');
        document.body.classList.remove('is-mobile', 'dark-theme', 'is-very-small');
    }
}

function applyModernLayout() {
    const isMobile = window.innerWidth <= 768;
    
    if (isMobile) {
        // Aplicar cards modernos às seções
        applySectionCards();
        
        // Aplicar grid moderno às informações da moto
        applyMotoInfoGrid();
        
        // Aplicar botões de ação modernos
        applyActionButtons();
        
        // Aplicar seção de totais moderna
        applyTotalSections();
        
        // Adicionar animações de entrada
        addEntranceAnimations();
    }
}

function applySectionCards() {
    // Envolver seções principais em cards
    const sections = document.querySelectorAll('#banner .content > *');
    sections.forEach(section => {
        if (!section.classList.contains('section-card') && 
            !section.classList.contains('headers-tabela')) {
            section.classList.add('section-card');
        }
    });
}

function applyMotoInfoGrid() {
    // Converter informações da moto em grid moderno
    const motoInfos = document.querySelectorAll('.motoinfo');
    motoInfos.forEach(motoInfo => {
        if (!motoInfo.classList.contains('moto-info-grid')) {
            motoInfo.classList.add('moto-info-grid');
            
            const items = motoInfo.querySelectorAll('li');
            items.forEach(item => {
                item.classList.add('moto-info-item');
                
                const text = item.textContent.trim();
                const parts = text.split(':');
                if (parts.length === 2) {
                    item.innerHTML = `
                        <div class="moto-info-label">${parts[0].trim()}</div>
                        <div class="moto-info-value">${parts[1].trim()}</div>
                    `;
                }
            });
        }
    });
}

function applyActionButtons() {
    // Agrupar botões de ação
    const buttons = document.querySelectorAll('button[onclick*="edit"], button[onclick*="delete"]');
    buttons.forEach(button => {
        if (!button.parentElement.classList.contains('action-buttons')) {
            const wrapper = document.createElement('div');
            wrapper.className = 'action-buttons';
            button.parentNode.insertBefore(wrapper, button);
            wrapper.appendChild(button);
            
            // Adicionar classes específicas
            if (button.onclick && button.onclick.toString().includes('edit')) {
                button.classList.add('btn-action', 'btn-edit');
            } else if (button.onclick && button.onclick.toString().includes('delete')) {
                button.classList.add('btn-action', 'btn-delete');
            }
        }
    });
}

function applyTotalSections() {
    // Aplicar estilo moderno aos totais
    const totalRows = document.querySelectorAll('tr.total, .total');
    totalRows.forEach(total => {
        if (!total.classList.contains('total-section')) {
            total.classList.add('total-section');
            
            const valueElement = total.querySelector('td:last-child, .total-value');
            if (valueElement && !valueElement.classList.contains('total-value')) {
                const value = valueElement.textContent.trim();
                valueElement.innerHTML = `
                    <div class="total-label">Total</div>
                    <div class="total-value">${value}</div>
                `;
            }
        }
    });
}

function addEntranceAnimations() {
    // Adicionar animações de entrada suaves
    const cards = document.querySelectorAll('.section-card');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            card.style.transition = 'all 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
}

function adjustTables() {
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
    
    // Verificar overflow nas tabelas e aplicar wrapper
    const tables = document.querySelectorAll('table');
    tables.forEach(table => {
        // Verificar se já tem wrapper
        if (!table.parentElement.classList.contains('table-wrapper')) {
            const wrapper = document.createElement('div');
            wrapper.className = 'table-wrapper';
            table.parentNode.insertBefore(wrapper, table);
            wrapper.appendChild(table);
        }
        
        // Ajustar largura da tabela
        if (table.offsetWidth > window.innerWidth) {
            table.style.width = '100%';
            table.style.minWidth = window.innerWidth <= 320 ? '280px' : '300px';
        }
        
        // Adicionar classe alt se não tiver
        if (!table.classList.contains('alt')) {
            table.classList.add('alt');
        }
    });
}

function adjustImages(isSmallScreen, isVerySmall) {
    const images = document.querySelectorAll('button img');
    images.forEach(img => {
        // Ajustar tamanho das imagens em botões
        if (isVerySmall) {
            img.style.height = '18px';
            img.style.width = '18px';
        } else if (isSmallScreen) {
            img.style.height = '22px';
            img.style.width = '22px';
        } else {
            img.style.height = '25px';
            img.style.width = '25px';
        }
    });
}

function adjustButtons(isSmallScreen, isVerySmall) {
    const actionButtons = document.querySelectorAll('button');
    actionButtons.forEach(button => {
        if (!button.classList.contains('mobile-adjusted')) {
            button.classList.add('mobile-adjusted');
        }
    });
}

function applyTableWrappers() {
    // Esta função já está incluída em adjustTables()
}

function adjustColumnLayout() {
    // Ajustar layout de colunas
    const rows = document.querySelectorAll('.row');
    rows.forEach(row => {
        const cols = row.querySelectorAll('.col-6');
        cols.forEach(col => {
            col.style.width = '100%';
            col.style.marginLeft = '0';
            col.style.marginBottom = '0.8em';
        });
    });
}

function adjustMotoInfo() {
    // Ajustar informações da moto
    const motoInfo = document.querySelector('#motoinfo');
    if (motoInfo) {
        motoInfo.style.display = 'block';
        motoInfo.style.width = '100%';
    }
}

function adjustModals() {
    // Ajustar modais para mobile
    const modals = document.querySelectorAll('.modal-content');
    modals.forEach(modal => {
        modal.style.width = '95%';
        modal.style.maxWidth = '100%';
        modal.style.margin = '10px auto';
    });
}