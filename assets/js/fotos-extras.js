// Função para excluir uma foto extra
function excluirFotoExtra(fotoId) {
    if (confirm('Tem certeza que deseja excluir esta foto?')) {
        // Criar FormData para enviar o ID da foto
        const formData = new FormData();
        formData.append('foto_id', fotoId);
        
        // Enviar requisição AJAX para excluir a foto
        fetch('../scripts/tabelaMotos/excluir-foto-extra.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Remover o elemento da foto da interface
                const fotoElement = document.querySelector(`.foto-extra-item[data-foto-id="${fotoId}"]`);
                if (fotoElement) {
                    fotoElement.remove();
                } else {
                    // Recarregar a página se não conseguir encontrar o elemento
                    location.reload();
                }
            } else {
                alert('Erro ao excluir a foto: ' + (data.message || 'Erro desconhecido'));
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('Ocorreu um erro ao tentar excluir a foto.');
        });
    }
}