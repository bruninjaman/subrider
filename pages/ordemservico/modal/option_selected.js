class FormSectionManager {
    constructor() {
        this.selectedOptionElement = document.getElementById('selected_option');
        this.initializeEventListeners();
    }

    initializeEventListeners() {
        // Adiciona listeners para todos os botões de navegação
        const navigationButtons = document.querySelectorAll('[onclick^="setFormSection"]');
        navigationButtons.forEach(button => {
            button.addEventListener('click', (event) => {
                event.preventDefault();
                const section = button.getAttribute('onclick').match(/'([^']+)'/)[1];
                this.setFormSection(section);
            });
        });
    }

    setFormSection(section) {
        if (!this.selectedOptionElement) {
            console.error('Elemento selected_option não encontrado');
            return;
        }

        this.selectedOptionElement.value = section;
        this.handleSectionChange(section);
    }

    handleSectionChange(section) {
        // Aqui você pode adicionar lógica adicional para cada mudança de seção
    }
}

// Inicialização quando o DOM estiver carregado
document.addEventListener('DOMContentLoaded', () => {
    window.formSectionManager = new FormSectionManager();
});

// Função global para compatibilidade com código existente
function setFormSection(section) {
    if (window.formSectionManager) {
        window.formSectionManager.setFormSection(section);
    }
}
