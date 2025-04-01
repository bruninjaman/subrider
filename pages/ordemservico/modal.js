class ModalManager {
    constructor() {
        this.modal = document.getElementById('modal');
        this.currentPage = 0;
        this.pages = document.querySelectorAll('.modal-page');
        this.initializeEventListeners();
    }

    initializeEventListeners() {
        // Botão de abrir modal
        document.getElementById('openModal').addEventListener('click', () => this.openModal());

        // Botões de fechar modal
        document.querySelectorAll('[id^="closeModal"]').forEach(button => {
            button.addEventListener('click', () => this.closeModal());
        });

        // Navegação entre páginas
        this.initializeNavigationButtons();

        // Botões de voltar ao menu principal
        document.querySelectorAll('#backToMenu').forEach(button => {
            button.addEventListener('click', (event) => {
                event.preventDefault();
                this.showPage(0);
            });
        });

        // Fechar modal ao clicar fora
        window.addEventListener('click', (event) => {
            if (event.target === this.modal) {
                this.closeModal();
            }
        });
    }

    initializeNavigationButtons() {
        const navigationMap = {
            'btnCabecote': 'cabecote',
            'btnMotor': 'motorPage',
            'btnVirabrequim': 'virabrequimPage',
            'btnEmbreagem': 'embreagemPage',
            'btnBombas': 'bombasPage',
            'btnDados': 'dados'
        };

        Object.entries(navigationMap).forEach(([buttonId, pageId]) => {
            document.getElementById(buttonId)?.addEventListener('click', () => this.showSpecificPage(pageId));
        });
    }

    openModal() {
        this.modal.style.display = 'flex';
        this.currentPage = 0;
        this.showPage(this.currentPage);
    }

    closeModal() {
        this.modal.style.display = 'none';
        this.currentPage = 0;
    }

    showSpecificPage(pageId) {
        this.pages.forEach(page => page.style.display = 'none');
        document.getElementById(pageId).style.display = 'block';
    }

    showPage(pageIndex) {
        this.pages.forEach((page, index) => {
            page.style.display = index === pageIndex ? 'block' : 'none';
        });
    }

    changePage(step) {
        this.currentPage += step;
        this.currentPage = Math.max(0, Math.min(this.currentPage, this.pages.length - 1));
        this.showPage(this.currentPage);
    }
}

// Inicialização do modal quando o DOM estiver carregado
document.addEventListener('DOMContentLoaded', () => {
    window.modalManager = new ModalManager();
});