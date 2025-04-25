class DeleteConfirmation {
    constructor() {
        this.baseAddress = window.baseAddress || '';
    }

    confirm(question, id, ordem) {
        if (!this.validateParameters(id, ordem)) {
            console.error('Parâmetros inválidos para exclusão');
            return false;
        }

        if (confirm(question)) {
            const url = this.buildDeleteUrl(id, ordem);
            window.location.href = url;
            return true;
        }

        return false;
    }

    validateParameters(id, ordem) {
        return (
            typeof id === 'number' && 
            id > 0 && 
            typeof ordem === 'string' && 
            ordem.length > 0
        );
    }

    buildDeleteUrl(id, ordem) {
        const params = new URLSearchParams({
            ordem: ordem,
            id: id
        });
        return `${this.baseAddress}scripts/ordem-delete/ordem-delete.php?${params.toString()}`;
    }
}

// Inicialização quando o DOM estiver carregado
document.addEventListener('DOMContentLoaded', () => {
    window.deleteConfirmation = new DeleteConfirmation();
});

// Função global para compatibilidade com código existente
function delete_confirm(question, id, ordem) {
    return window.deleteConfirmation.confirm(question, id, ordem);
}