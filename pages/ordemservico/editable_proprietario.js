class ProprietarioEditor {
    constructor() {
        this.proprietarioElement = document.getElementById('editableproprietario');
        this.errorElement = document.getElementById('errorMessage');
        this.initializeEventListeners();
    }

    initializeEventListeners() {
        this.proprietarioElement.addEventListener('click', () => this.enableEditing());
        this.proprietarioElement.addEventListener('input', () => this.validateInput());
        this.proprietarioElement.addEventListener('keypress', (event) => this.handleKeyPress(event));
    }

    enableEditing() {
        this.proprietarioElement.setAttribute('contenteditable', 'true');
        this.proprietarioElement.classList.add('editing');
    }

    validateInput() {
        this.proprietarioElement.style.color = 'yellow';
        const newData = this.proprietarioElement.textContent.trim();
        
        if (!this.isValidProprietario(newData)) {
            this.showError('Proprietário não pode estar vazio.');
            return false;
        }

        this.hideError();
        return true;
    }

    isValidProprietario(proprietario) {
        return proprietario.length > 0;
    }

    showError(message) {
        this.errorElement.textContent = message;
        this.errorElement.style.display = 'block';
    }

    hideError() {
        this.errorElement.textContent = '';
        this.errorElement.style.display = 'none';
    }

    async saveProprietario() {
        if (!this.validateInput()) {
            return;
        }

        const newData = this.proprietarioElement.textContent.trim();
        const ordem = new URLSearchParams(window.location.search).get('ordem');

        try {
            const response = await fetch(`${baseAddress}/ajax/update_proprietario.php`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `ordem=${ordem}&newProprietario=${encodeURIComponent(newData)}`
            });

            if (!response.ok) {
                throw new Error('Erro na requisição');
            }

            this.proprietarioElement.style.color = 'white';
            this.proprietarioElement.removeAttribute('contenteditable');
            this.proprietarioElement.classList.remove('editing');
        } catch (error) {
            console.error('Erro ao atualizar proprietário:', error);
            this.showError('Erro ao atualizar o proprietário. Tente novamente.');
        }
    }

    handleKeyPress(event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            this.saveProprietario();
        }
    }
}

// Inicialização quando o DOM estiver carregado
document.addEventListener('DOMContentLoaded', () => {
    window.proprietarioEditor = new ProprietarioEditor();
});