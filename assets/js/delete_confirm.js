// Função genérica para confirmação de exclusão
// Modificada para usar fetch API e retornar Promise
function confirmDelete(itemId, itemType, additionalParams = '', deleteUrl = null) {
    // Retorna uma Promise para que o chamador possa reagir ao resultado do AJAX
    return new Promise((resolve, reject) => {
        if (confirm('Deseja realmente excluir este item?')) {
            let url = deleteUrl; // Usa a URL passada como parâmetro se existir

            // Se a URL não foi passada, tenta construir como antes (fallback)
            if (!url) {
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
                    // Adicionar outros casos conforme necessário
                    default:
                        console.error("Tipo de item desconhecido para exclusão:", itemType);
                        alert("Erro: Tipo de item desconhecido para exclusão.");
                        return reject("Tipo de item desconhecido"); // Rejeita a Promise
                }
                console.warn("URL de exclusão não fornecida para", itemType, itemId, "- usando fallback.");
            }

            if (url) {
                fetch(url, {
                    method: 'GET', // Ou 'POST' ou 'DELETE', dependendo da API
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest' // Comum para identificar requisições AJAX no backend
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        // Tenta ler a mensagem de erro do JSON, se houver
                        return response.json().then(errData => {
                            throw new Error(errData.message || `Erro HTTP: ${response.status}`);
                        }).catch(() => {
                            // Se não conseguir ler JSON, lança erro genérico
                            throw new Error(`Erro HTTP: ${response.status}`);
                        });
                    }
                    return response.json(); // Converte a resposta para JSON
                })
                .then(data => {
                    if (data.success) {
                        alert(data.message || 'Item excluído com sucesso!');
                        resolve(true); // Resolve a Promise com sucesso
                    } else {
                        alert(data.message || 'Falha ao excluir o item.');
                        reject(data.message || 'Falha na exclusão'); // Rejeita a Promise com a mensagem de erro
                    }
                })
                .catch(error => {
                    console.error('Erro ao tentar excluir:', error);
                    alert('Ocorreu um erro ao tentar excluir o item: ' + error.message);
                    reject(error); // Rejeita a Promise com o erro
                });
            } else {
                // Caso a URL não seja definida por algum motivo
                alert("Erro: URL para exclusão não definida.");
                reject("URL não definida");
            }
        } else {
            // Usuário cancelou a confirmação
            resolve(false); // Resolve a Promise indicando cancelamento
        }
    });
}

// Funções específicas para cada tipo de item
// Agora usam a Promise retornada por confirmDelete
async function deletePeca(pecaID, url) {
    try {
        const success = await confirmDelete(pecaID, 'peca', '', url);
        if (success === true) {
            // Encontra o botão que foi clicado
            const buttonElement = event.target.closest('button');
            if (buttonElement) {
                // Encontra a linha da tabela (tr) pai do botão
                const rowElement = buttonElement.closest('tr');
                if (rowElement) {
                    rowElement.remove(); // Remove a linha da tabela
                    console.log(`Linha da peça ${pecaID} removida.`);
                    // Opcional: Recarregar a tabela ou ajustar a paginação se necessário
                    // window.carregarTabela(/* parâmetros atuais */); 
                } else {
                    console.warn("Não foi possível encontrar a linha (tr) para remover.");
                }
            } else {
                 console.warn("Não foi possível encontrar o botão clicado.");
            }
        } // else { // Usuário cancelou ou houve erro (já tratado em confirmDelete) }
    } catch (error) {
        console.error("Erro no processo de exclusão da peça:", error);
        // A mensagem de erro já foi mostrada ao usuário em confirmDelete
    }
    // Impede o comportamento padrão do botão/link, se houver
    // return false; // O async/await não retorna false diretamente como antes
    // O evento original (ex: onclick='return deletePeca(...)') pode precisar ser ajustado
    // para não depender do 'return false' ou usar event.preventDefault() se for um link.
    // Como estamos usando onclick em um <button>, o 'return' não é estritamente necessário
    // para prevenir navegação, mas pode ser útil se a função fosse chamada de outra forma.
}

async function deleteMoto(motoID, url = null) {
    try {
        const success = await confirmDelete(motoID, 'moto', '', url);
        if (success === true) {
            const buttonElement = event.target.closest('button');
            if (buttonElement) {
                const rowElement = buttonElement.closest('tr');
                if (rowElement) {
                    rowElement.remove();
                } else { console.warn("Não foi possível encontrar a linha (tr) para remover."); }
            } else { console.warn("Não foi possível encontrar o botão clicado."); }
        }
    } catch (error) {
        console.error("Erro no processo de exclusão da moto:", error);
    }
}

async function deleteServico(ordemID, Ordem, url = null) {
    try {
        const success = await confirmDelete(ordemID, 'servico', Ordem, url);
        if (success === true) {
            const buttonElement = event.target.closest('button');
            if (buttonElement) {
                const rowElement = buttonElement.closest('tr');
                if (rowElement) {
                    rowElement.remove();
                } else { console.warn("Não foi possível encontrar a linha (tr) para remover."); }
            } else { console.warn("Não foi possível encontrar o botão clicado."); }
        }
    } catch (error) {
        console.error("Erro no processo de exclusão do serviço:", error);
    }
} 