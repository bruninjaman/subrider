<section id="banner">
    <div class="content">
        <!-- search bar -->
        <?php
        // Define $baseUrl (idealmente vindo de init.php)
        $baseUrl = defined('BASE_URL') ? BASE_URL : '/subrider';

        // Conecta ao DB via PDO (necessário para obter colunas de pesquisa)
        // TODO: Idealmente, as colunas poderiam ser fixas ou carregadas de outra forma
        $colunasPesquisa = ['endereco', 'modelo', 'marca', 'placa', 'proprietario']; // Fallback inicial
        try {
            $db = \Subrider\Database\Database::getInstance();
            $conn = $db->getConnection();

            $categoriasPesquisaQuery = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'motocicletas'";
            $categoriasParaExcluir = ['motoId', 'foto', 'ano', 'km']; // Colunas a não incluir na pesquisa

            $stmtCategorias = $conn->query($categoriasPesquisaQuery);
            $colunasBrutas = $stmtCategorias->fetchAll(PDO::FETCH_COLUMN);
            $colunasPesquisaTemp = [];
            foreach ($colunasBrutas as $coluna) {
                if (!in_array($coluna, $categoriasParaExcluir)) {
                    $colunasPesquisaTemp[] = htmlspecialchars($coluna, ENT_QUOTES, 'UTF-8');
                }
            }
            if (!empty($colunasPesquisaTemp)) {
                $colunasPesquisa = $colunasPesquisaTemp;
            }

        } catch (PDOException $e) {
            error_log("Erro ao buscar colunas para pesquisa em tabelaMotos: " . $e->getMessage());
            // Usa o fallback definido acima em caso de erro
        }

        // Inclui a searchbar passando as colunas (compatível com a versão PDO)
        include(__DIR__ . '/../../../includes/searchbar.php');
        ?>
        <div id="resultados-tabela">
            <!-- O conteúdo da tabela será carregado via AJAX -->
            <div class="table-wrapper">
                 <div class="table-wrapper" style="overflow: hidden;">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Foto</th>
                                <th><button class="sort" type="button" data-orderby="endereco">Endereço <i class="fa-solid fa-sort"></i></button> </th>
                                <th><button class="sort" type="button" data-orderby="ano">Ano <i class="fa-solid fa-sort"></i></button></th>
                                <th><button class="sort" type="button" data-orderby="modelo">Modelo <i class="fa-solid fa-sort"></i></button></th>
                                <th><button class="sort" type="button" data-orderby="marca">Marca <i class="fa-solid fa-sort"></i></button></th>
                                <th><button class="sort" type="button" data-orderby="placa">Placa <i class="fa-solid fa-sort"></i></button></th>
                                <th><button class="sort" type="button" data-orderby="km">KM <i class="fa-solid fa-sort"></i></button></th>
                                <th><button class="sort" type="button" data-orderby="proprietario">Proprietario <i class="fa-solid fa-sort"></i></button></th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody id="tabela-motos">
                            <!-- Placeholder enquanto o AJAX carrega -->
                             <tr><td colspan="9">Carregando motos...</td></tr>
                        </tbody>
                    </table>
                    <div class="row">
                        <div class="col-3">
                            <a class="button primary" href='<?php echo $baseUrl; ?>/addmotos.php' style="display: flex; align-items: center; justify-content: center; white-space: nowrap; width: fit-content; min-width: 100%;">
                                <img src="<?php echo $baseUrl; ?>/assets/css/images/addmoto.png" style="margin-right: 12px;">
                                Adicionar Motocicleta
                            </a>
                        </div>
                        <div class="col-9" id="paginacao-container">
                            <!-- A paginação será carregada via AJAX -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Script para carregar a tabela sem recarregar a página -->
<script>
// Função global para carregar os dados via AJAX
window.carregarTabela = function(pagina = 1, pesquisa = '', selectPesquisa = '', orderby = '') {
    var xhr = new XMLHttpRequest();
    // Usa a variável $baseUrl definida no PHP
    var ajaxUrl = '<?php echo $baseUrl; ?>/pages/tabelaMotos/ajax/carregarMotos.php';
    var queryParams = '?page=' + pagina +
                      '&pesquisa=' + encodeURIComponent(pesquisa) +
                      '&selectPesquisa=' + encodeURIComponent(selectPesquisa) +
                      '&orderby=' + encodeURIComponent(orderby);

    // Adiciona CSRF token se estiver disponível (assumindo que init.php o define em $_SESSION)
    <?php if (!empty($_SESSION['csrf_token'])): ?>
    queryParams += '&csrf_token=' + encodeURIComponent('<?php echo $_SESSION["csrf_token"]; ?>');
    <?php endif; ?>

    xhr.open('GET', ajaxUrl + queryParams, true);

    xhr.onload = function() {
        if (this.status == 200) {
            document.getElementById('resultados-tabela').innerHTML = this.responseText;
            // Reaplica os eventos após o carregamento
            aplicarEventos();
        } else {
            // Tratar erros de AJAX (ex: exibir mensagem)
             document.getElementById('resultados-tabela').innerHTML = "<p>Erro ao carregar dados. Tente novamente.</p>";
        }
    };

    xhr.onerror = function() {
        // Tratar erros de conexão
        document.getElementById('resultados-tabela').innerHTML = "<p>Erro de conexão ao carregar dados.</p>";
    };

    xhr.send();
};

// Função para aplicar eventos aos elementos carregados via AJAX
function aplicarEventos() {
    // Eventos para os botões de ordenação
    document.querySelectorAll('#resultados-tabela .sort').forEach(function(botao) {
        // Remover listener antigo para evitar duplicação
        botao.replaceWith(botao.cloneNode(true));
        // Adicionar novo listener
        document.getElementById(botao.id || '').addEventListener('click', function() { // Re-selecionar pelo ID se tiver
             if (!this.id) { // Se não tiver ID, tentar re-selecionar pelo atributo data-orderby
                 const currentOrderBy = this.getAttribute('data-orderby');
                 const newButton = document.querySelector(`#resultados-tabela .sort[data-orderby="${currentOrderBy}"]`);
                 if (newButton) newButton.addEventListener('click', handleSortClick);
                 return; // Sai se não conseguiu re-selecionar
             }
             document.getElementById(botao.id).addEventListener('click', handleSortClick);
        });
    });

    // Eventos para os links de paginação
    document.querySelectorAll('#resultados-tabela .paginacao-btn').forEach(function(botao) {
        // Remover listener antigo
        botao.replaceWith(botao.cloneNode(true));
        // Adicionar novo listener
        const newButton = document.querySelector(`a.paginacao-btn[data-page="${botao.getAttribute('data-page')}"]`);
        if (newButton) {
            newButton.addEventListener('click', handlePaginationClick);
        }
    });

    // Eventos para botões de exclusão (assumindo que deleteMoto global existe)
    document.querySelectorAll('#resultados-tabela button[onclick*="deleteMoto(']').forEach(function(botao) {
         // Não precisa remover/readicionar listeners para atributos onclick inline
         // Apenas garantir que a função global deleteMoto esteja definida e funcione
    });
}

// Handlers separados para clareza
function handleSortClick() {
    var orderby = this.getAttribute('data-orderby');
    var pesquisa = document.getElementById('input-pesquisa') ?
                  document.getElementById('input-pesquisa').value : '';
    var selectPesquisa = document.getElementById('selectPesquisa') ?
                        document.getElementById('selectPesquisa').value : '';

    window.carregarTabela(1, pesquisa, selectPesquisa, orderby);
}

function handlePaginationClick(e) {
    e.preventDefault();

    var pagina = this.getAttribute('data-page');
    // Não carrega se for disabled ou já ativo
    if (this.parentElement.classList.contains('disabled') || this.parentElement.classList.contains('active')) {
        return;
    }

    var pesquisa = document.getElementById('input-pesquisa') ?
                  document.getElementById('input-pesquisa').value : '';
    var selectPesquisa = document.getElementById('selectPesquisa') ?
                        document.getElementById('selectPesquisa').value : '';
    // Pega ordenação atual pelo botão com classe active
    var orderby = document.querySelector('#resultados-tabela .sort.active') ?
                 document.querySelector('#resultados-tabela .sort.active').getAttribute('data-orderby') : 'motoId'; // Fallback

    window.carregarTabela(pagina, pesquisa, selectPesquisa, orderby);
}


// Inicializar a tabela ao carregar a página
document.addEventListener('DOMContentLoaded', function() {
    // Carrega a primeira página sem filtros ou ordenação específica (usará os padrões do PHP)
    window.carregarTabela(1);

    // Adiciona listener ao botão de pesquisa da searchbar
    const btnPesquisar = document.getElementById('btn-pesquisar');
    if (btnPesquisar) {
        btnPesquisar.addEventListener('click', function() {
            var pesquisa = document.getElementById('input-pesquisa').value;
            var selectPesquisa = document.getElementById('selectPesquisa').value;
            // Ao pesquisar, volta para a página 1 e mantém a ordenação atual (se houver)
             var orderby = document.querySelector('#resultados-tabela .sort.active') ?
                         document.querySelector('#resultados-tabela .sort.active').getAttribute('data-orderby') : 'motoId';
            window.carregarTabela(1, pesquisa, selectPesquisa, orderby);
        });
    }
    // Opcional: Adicionar listener para pesquisar com Enter no input
    const inputPesquisa = document.getElementById('input-pesquisa');
    if (inputPesquisa) {
         inputPesquisa.addEventListener('keypress', function(e) {
             if (e.key === 'Enter') {
                 e.preventDefault(); // Previne submit do form (se houver)
                 btnPesquisar.click(); // Simula clique no botão de pesquisa
             }
         });
    }

    // A função global deleteMoto é definida em assets/js/delete_confirm.js
    // Não é necessário redefini-la aqui.
});
</script>