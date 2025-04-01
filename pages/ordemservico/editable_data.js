class DateEditor {
    constructor() {
        this.dateElement = document.getElementById('dateValue');
        this.errorElement = document.getElementById('errorMessage');
        this.initializeEventListeners();
    }

    initializeEventListeners() {
        this.dateElement.addEventListener('click', () => this.enableEditing());
        this.dateElement.addEventListener('input', () => this.validateInput());
        this.dateElement.addEventListener('keypress', (event) => this.handleKeyPress(event));
    }

    enableEditing() {
        this.dateElement.setAttribute('contenteditable', 'true');
        this.dateElement.classList.add('editing');
    }

    validateInput() {
        this.dateElement.style.color = 'yellow';
        const newData = this.dateElement.textContent.trim();
        
        if (!this.isValidDateFormat(newData)) {
            this.showError('Formato de data inválido. Digite a data em formato válido dd/mm/yyyy.');
            return false;
        }

        if (!this.isValidDate(newData)) {
            this.showError('Data inválida. Verifique o dia, mês e ano.');
            return false;
        }

        this.hideError();
        return true;
    }

    isValidDateFormat(dateStr) {
        const dateRegex = /^\d{2}\/\d{2}\/\d{4}$/;
        return dateRegex.test(dateStr);
    }

    isValidDate(dateStr) {
        const [day, month, year] = dateStr.split('/').map(Number);
        const date = new Date(year, month - 1, day);
        return date.getDate() === day && 
               date.getMonth() === month - 1 && 
               date.getFullYear() === year;
    }

    showError(message) {
        this.errorElement.textContent = message;
        this.errorElement.style.display = 'block';
    }

    hideError() {
        this.errorElement.textContent = '';
        this.errorElement.style.display = 'none';
    }

    async saveDate() {
        if (!this.validateInput()) {
            return;
        }

        const newData = this.dateElement.textContent.trim();
        const ordem = new URLSearchParams(window.location.search).get('ordem');

        try {
            const response = await fetch(`${baseAddress}/ajax/update_date.php`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `ordem=${ordem}&newData=${newData}`
            });

            if (!response.ok) {
                throw new Error('Erro na requisição');
            }

            this.dateElement.style.color = 'white';
            this.dateElement.removeAttribute('contenteditable');
            this.dateElement.classList.remove('editing');
        } catch (error) {
            console.error('Erro ao atualizar data:', error);
            this.showError('Erro ao atualizar a data. Tente novamente.');
        }
    }

    handleKeyPress(event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            this.saveDate();
        }
    }
}

// Inicialização quando o DOM estiver carregado
document.addEventListener('DOMContentLoaded', () => {
    window.dateEditor = new DateEditor();
});