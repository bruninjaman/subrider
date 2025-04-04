// Função genérica para confirmação de exclusão
function confirmDelete(itemId, itemType, additionalParams = '') {
    if (confirm('Deseja realmente excluir este item?')) {
        let url = '';
        switch(itemType) {
            case 'peca':
                url = `scripts/tabelaPecasDelete/delete-peca.php?pecaID=${itemId}`;
                break;
            case 'moto':
                url = `scripts/tabelaMotos/delete-moto.php?motoID=${itemId}`;
                break;
            case 'servico':
                url = `scripts/tabelaOrdensDelete/delete-service.php?ordemID=${itemId}&Ordem=${additionalParams}`;
                break;
        }
        location.href = url;
        return true;
    }
    return false;
}

// Funções específicas para cada tipo de item
function deletePeca(pecaID) {
    return confirmDelete(pecaID, 'peca');
}

function deleteMoto(motoID) {
    return confirmDelete(motoID, 'moto');
}

function deleteServico(ordemID, Ordem) {
    return confirmDelete(ordemID, 'servico', Ordem);
} 