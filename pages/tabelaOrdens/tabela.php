<section id="banner">
    <div class="content">
        <!-- search bar -->
        <?php
        // Incluir a search bar (verificar se precisa de ajustes de BASE_URL internamente)
        include_once(__DIR__ . "/../../includes/searchbar_ordemservicos.php");

        // Definir BASE_URL se ainda não estiver definida
        if (!defined('BASE_URL')) {
            // Tentar incluir o config.php para obter BASE_URL
            // Ajuste o caminho conforme necessário para sua estrutura
            $configPath = __DIR__ . '/../../config/config.php';
            if (file_exists($configPath)) {
                require_once $configPath;
            } else {
                // Fallback ou erro se config.php não for encontrado
                // Defina um valor padrão ou lance um erro
                // Exemplo: define('BASE_URL', '/subrider/'); // Ou outra lógica
                 echo "Erro: config.php não encontrado.";
                 exit; // Ou trate o erro como preferir
            }
        }
        ?>
        <div id="resultados-tabela">
            <!-- Tabela será carregada via AJAX -->
            <div class="table-wrapper">
                <div class="table-wrapper">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Foto</th>
                                <th><button class="sort" type="button" data-orderby="Codigo">Ordem <i class="fa-solid fa-sort"></i></button></th>
                                <th><button class="sort" type="button" data-orderby="ano">Ano <i class="fa-solid fa-sort"></i></button></th>
                                <th><button class="sort" type="button" data-orderby="modelo">Modelo <i class="fa-solid fa-sort"></i></button></th>
                                <th><button class="sort" type="button" data-orderby="marca">Marca <i class="fa-solid fa-sort"></i></button></th>
                                <th><button class="sort" type="button" data-orderby="proprietario_ordem">Proprietario <i class="fa-solid fa-sort"></i></button></th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody id="tabela-ordens">
                            <!-- Conteúdo carregado via AJAX -->
                            <tr><td colspan="7">Carregando ordens...</td></tr>
                        </tbody>
                    </table>
                     <div class="row">
                        <div class="col-3">
                            <a class="button primary" href='<?php echo BASE_URL; ?>tabelaOrdensAdd.php'>Gerar Ordem de Serviço</a>
                        </div>
                        <div class="col-9" id="paginacao-container">
                            <!-- Paginação será carregada via AJAX -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Fim #resultados-tabela -->
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const resultadosTabela = document.getElementById('resultados-tabela');
    const searchForm = document.getElementById('form-pesquisa'); // Assumindo que o ID do form na searchbar é 'form-pesquisa'
    const searchInput = document.getElementById('input-pesquisa'); // Assumindo que o ID do input na searchbar é 'input-pesquisa'
    const searchSelect = document.getElementById('selectPesquisa'); // Assumindo que o ID do select na searchbar é 'selectPesquisa'

    let currentPage = 1;
    let currentOrderBy = 'Codigo'; // Default order
    let currentSearchQuery = '';
    let currentSearchSelect = '';

    // Função para carregar dados via AJAX
    function carregarOrdens() {
        const params = new URLSearchParams({
            page: currentPage,
            pesquisa: currentSearchQuery,
            selectPesquisa: currentSearchSelect,
            orderby: currentOrderBy
        });

        // Mostrar indicador de carregamento
        if(resultadosTabela) {
             const tbody = resultadosTabela.querySelector('#tabela-ordens');
             const paginationContainer = resultadosTabela.querySelector('#paginacao-container');
             if(tbody) tbody.innerHTML = '<tr><td colspan="7">Carregando...</td></tr>';
             if(paginationContainer) paginationContainer.innerHTML = '';
        }


        fetch(`<?php echo BASE_URL; ?>pages/tabelaOrdens/ajax/carregarOrdens.php?${params.toString()}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.text();
            })
            .then(data => {
                if (resultadosTabela) {
                    resultadosTabela.innerHTML = data;
                     // Adicionar classe 'active' ao botão de ordenação atual
                    const currentSortButton = resultadosTabela.querySelector(`.sort[data-orderby="${currentOrderBy}"]`);
                    if (currentSortButton) {
                        currentSortButton.classList.add('active');
                         // Atualizar ícone de ordenação (opcional, requer lógica adicional para ASC/DESC)
                        // currentSortButton.querySelector('i').className = 'fa-solid fa-sort-up'; // ou fa-sort-down
                    }
                } else {
                    console.error("Elemento #resultados-tabela não encontrado após fetch.");
                }
            })
            .catch(error => {
                console.error('Erro ao carregar ordens:', error);
                 if (resultadosTabela) {
                    const tbody = resultadosTabela.querySelector('#tabela-ordens');
                     if(tbody) tbody.innerHTML = '<tr><td colspan="7">Erro ao carregar ordens. Tente novamente.</td></tr>';
                 }
            });
    }

    // Delegação de eventos para cliques dentro de #resultados-tabela
    if (resultadosTabela) {
        resultadosTabela.addEventListener('click', function(event) {
            const target = event.target;

            // Botões de Ordenação (.sort)
            const sortButton = target.closest('.sort');
            if (sortButton) {
                event.preventDefault();
                const newOrderBy = sortButton.getAttribute('data-orderby');
                 // Opcional: lógica para alternar ASC/DESC se necessário
                 // if (newOrderBy === currentOrderBy) { /* toggle direction */ }
                currentOrderBy = newOrderBy;
                currentPage = 1; // Resetar para a primeira página ao mudar ordenação
                carregarOrdens();
                 // Remover classe 'active' de outros botões e adicionar ao clicado
                 resultadosTabela.querySelectorAll('.sort.active').forEach(btn => btn.classList.remove('active'));
                 sortButton.classList.add('active');
            }

            // Links de Paginação (.paginacao-btn)
            const paginacaoBtn = target.closest('.paginacao-btn');
            if (paginacaoBtn) {
                event.preventDefault();
                const page = paginacaoBtn.getAttribute('data-page');
                if (page) {
                    currentPage = parseInt(page, 10);
                    carregarOrdens();
                }
            }

             // Botões de Edição (.ordemedit - apenas para navegação)
             const editButton = target.closest('.ordemedit');
             if (editButton) {
                 // A ação onclick="location.href=..." já deve funcionar, mas podemos centralizar se necessário
                 // window.location.href = editButton.getAttribute('data-href'); // Se adicionarmos data-href
             }

             // Botões de Exclusão (.ordemdelete) - Renomeado de deleteServico para deleteOrdem
             const deleteButton = target.closest('.ordemdelete'); // Assumindo classe .ordemdelete no botão
             if (deleteButton) {
                 event.preventDefault(); // Prevenir qualquer ação padrão
                 const ordemCodigo = deleteButton.getAttribute('data-codigo'); // Pegar o código da ordem do atributo data-codigo
                 if (ordemCodigo && confirm(`Tem certeza que deseja excluir a Ordem de Serviço ${ordemCodigo}? Esta ação não pode ser desfeita.`)) {
                     // Chamar a função de exclusão (definida abaixo ou globalmente)
                     deleteOrdem(ordemCodigo);
                 }
             }
        });
    } else {
        console.error("Elemento #resultados-tabela não encontrado no DOM.");
    }

    // Evento para o formulário de pesquisa
    if (searchForm) {
        searchForm.addEventListener('submit', function(event) {
            event.preventDefault();
            currentSearchQuery = searchInput ? searchInput.value : '';
            currentSearchSelect = searchSelect ? searchSelect.value : '';
            currentPage = 1; // Resetar para a primeira página ao pesquisar
            carregarOrdens();
        });
    }

     // Função de Exclusão
     window.deleteOrdem = function(ordemCodigo) {
         const params = new URLSearchParams({ servID: ordemCodigo }); // Assumindo que o script espera servID como parâmetro
         // Adicionar CSRF token se implementado
         // params.append('csrf_token', 'SEU_TOKEN_CSRF');

         fetch(`<?php echo BASE_URL; ?>scripts/tabelaOrdens/delete-ordem.php`, {
             method: 'POST', // Ou GET, dependendo do script delete-ordem.php
             headers: {
                 'Content-Type': 'application/x-www-form-urlencoded', // Se enviar como POST form
             },
             body: params // Enviar parâmetros no corpo para POST
             // Para GET seria: fetch(`<?php echo BASE_URL; ?>scripts/tabelaOrdens/delete-ordem.php?${params.toString()}`, { method: 'GET' })
         })
         .then(response => response.json()) // Espera uma resposta JSON do servidor
         .then(data => {
             if (data.success) {
                 alert('Ordem de Serviço excluída com sucesso!');
                 carregarOrdens(); // Recarregar a tabela
             } else {
                 alert('Erro ao excluir Ordem de Serviço: ' + (data.message || 'Erro desconhecido.'));
             }
         })
         .catch(error => {
             console.error('Erro na requisição de exclusão:', error);
             alert('Erro ao conectar com o servidor para excluir a ordem.');
         });
     }

    // Carregar a tabela inicialmente
    carregarOrdens();
});
</script>