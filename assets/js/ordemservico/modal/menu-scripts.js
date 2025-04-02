// Funções para navegação entre páginas do modal
function showPage(pageId) {
    // Esconde todas as páginas
    document.querySelectorAll('.modal-page').forEach(page => {
        page.style.display = 'none';
    });
    
    // Mostra a página selecionada
    const selectedPage = document.getElementById(pageId);
    if (selectedPage) {
        selectedPage.style.display = 'block';
    }
}

// Função para voltar ao menu principal
document.addEventListener('DOMContentLoaded', function() {
    const backButtons = document.querySelectorAll('#backToMenu');
    backButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            showPage('page1');
        });
    });
});

// Função para toggle de campos condicionais
function toggleFields() {
    const ohcFields = document.getElementById('ohc_fields');
    const dohcFields = document.getElementById('dohc_fields');
    const valveType = document.querySelector('input[name="valve_type"]:checked');

    if (ohcFields && dohcFields) {
        ohcFields.style.display = valveType && valveType.value === 'ohc' ? 'block' : 'none';
        dohcFields.style.display = valveType && valveType.value === 'dohc' ? 'block' : 'none';
    }
}

// Função para toggle de campos do virabrequim
function toggleVirabrequimFields() {
    const rolamentoType = document.querySelector('input[name="rolamento_type"]:checked');
    const sections = {
        'folga_lateral_biela_section': ['rolamento', 'bronzina'],
        'folga_eixo_bronzina_section': ['bronzina'],
        'folga_eixo_mancal_section': ['rolamento'],
        'folga_lateral_eixo_section': ['rolamento', 'bronzina'],
        'empenamento_max_section': ['rolamento', 'bronzina']
    };

    Object.entries(sections).forEach(([sectionId, allowedTypes]) => {
        const section = document.getElementById(sectionId);
        if (section) {
            section.style.display = rolamentoType && allowedTypes.includes(rolamentoType.value) ? 'block' : 'none';
        }
    });
}

// Adiciona listeners para os campos de tipo de rolamento
document.addEventListener('DOMContentLoaded', function() {
    const rolamentoInputs = document.querySelectorAll('input[name="rolamento_type"]');
    rolamentoInputs.forEach(input => {
        input.addEventListener('change', toggleVirabrequimFields);
    });
});