<section id="banner">
    <div class="content">
        <!-- search bar -->
        <?php
        // Define $baseUrl (idealmente vindo de init.php, mas incluímos aqui por segurança)
        // TODO: Garantir que tabelaServicos.php (raiz) use init.php
        $baseUrl = defined('BASE_URL') ? BASE_URL : '/subrider';

        // Conecta ao DB via PDO (necessário para obter colunas de pesquisa)
        $colunasPesquisa = ['Nome', 'Tipo', 'Descricao']; // Fallback inicial
        try {
            // Inclui init.php para garantir que Database e $baseUrl estejam disponíveis
            // Idealmente, tabelaServicos.php (raiz) faria isso.
             if (!class_exists('\Subrider\Database\Database')) {
                 require_once __DIR__ . '/../../../config/init.php';
             }
            $db = \Subrider\Database\Database::getInstance();
            $conn = $db->getConnection();

            $categoriasPesquisaQuery = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'servicos'";
            // TODO: Confirmar colunas da tabela 'servicos'
            $categoriasParaExcluir = ['servicoId', 'Preco']; // Excluir ID e preço da pesquisa?

            $stmtCategorias = $conn->query($categoriasPesquisaQuery);
            $colunasBrutas = $stmtCategorias->fetchAll(PDO::FETCH_COLUMN);
            $colunasPesquisaTemp = [];
            foreach ($colunasBrutas as $coluna) {
                if (!in_array($coluna, $categoriasParaExcluir)) {
                    // Transforma nome_coluna em Nome Coluna para exibição
                    $nomeExibicao = ucwords(str_replace('_', ' ', $coluna));
                    $colunasPesquisaTemp[htmlspecialchars($coluna, ENT_QUOTES, 'UTF-8')] = htmlspecialchars($nomeExibicao, ENT_QUOTES, 'UTF-8');
                }
            }
            if (!empty($colunasPesquisaTemp)) {
                $colunasPesquisa = $colunasPesquisaTemp;
            }

        } catch (Exception $e) { // Captura Exception genérica também (ex: se init.php falhar)
            error_log("Erro ao buscar colunas para pesquisa em tabelaServicos: " . $e->getMessage());
            // Usa o fallback definido acima em caso de erro
             $colunasPesquisa = ['Nome' => 'Nome', 'Tipo' => 'Tipo', 'Descricao' => 'Descricao']; // Usa formato chave=>valor
        }

        // Inclui a searchbar passando as colunas
        // Ajuste: Passar array associativo para searchbar
        include(__DIR__ . '/../../../includes/searchbar.php');
        ?>
        <div id="resultados-tabela">
            <!-- Placeholder enquanto o AJAX carrega -->
             <div class="table-wrapper">
                 <p>Carregando serviços...</p>
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
    var ajaxUrl = '<?php echo $baseUrl; ?>/pages/tabelaServicos/ajax/carregarServicos.php';
    var queryParams = '?page=' + pagina +
                      '&pesquisa=' + encodeURIComponent(pesquisa) +
                      '&selectPesquisa=' + encodeURIComponent(selectPesquisa) +
                      '&orderby=' + encodeURIComponent(orderby);

    // Adiciona CSRF token se estiver disponível
    <?php if (!empty($_SESSION['csrf_token'])): ?>
    queryParams += '&csrf_token=' + encodeURIComponent('<?php echo $_SESSION["csrf_token"]; ?>');
    <?php endif; ?>

    xhr.open('GET', ajaxUrl + queryParams, true);

    // Adicionar um indicador de carregamento
    const resultadosTabela = document.getElementById('resultados-tabela');
    if (resultadosTabela) {
        resultadosTabela.innerHTML = '<div class="table-wrapper"><p>Carregando serviços...</p></div>';
    }

    xhr.onload = function() {
        if (this.status == 200) {
            if (resultadosTabela) {
                resultadosTabela.innerHTML = this.responseText;
                // Não precisa chamar aplicarEventos com delegação
            }
        } else {
             if (resultadosTabela) {
                resultadosTabela.innerHTML = "<div class='table-wrapper'><p>Erro ao carregar dados. Tente novamente.</p></div>";
            }
        }
    };

     xhr.onerror = function() {
         if (resultadosTabela) {
            resultadosTabela.innerHTML = "<div class='table-wrapper'><p>Erro de conexão ao carregar dados.</p></div>";
        }
    };

    xhr.send();
};


// Inicializar a tabela ao carregar a página e configurar listeners com DELEGAÇÃO
document.addEventListener('DOMContentLoaded', function() {
    // Carrega a primeira página
    window.carregarTabela(1);

    // Configurar listeners na searchbar (fora da área que recarrega)
    const btnPesquisar = document.getElementById('btn-pesquisar');
    const inputPesquisa = document.getElementById('input-pesquisa');
    const selectPesquisaEl = document.getElementById('selectPesquisa');

    if (btnPesquisar && inputPesquisa && selectPesquisaEl) { // Verifica todos
        btnPesquisar.addEventListener('click', function() {
            var pesquisa = inputPesquisa.value;
            var selectPesquisa = selectPesquisaEl.value;
             // Ao pesquisar, volta para a página 1 e mantém a ordenação atual (se houver)
             var orderby = document.querySelector('#resultados-tabela .sort.active') ?
                         document.querySelector('#resultados-tabela .sort.active').getAttribute('data-orderby') : '';
            window.carregarTabela(1, pesquisa, selectPesquisa, orderby);
        });

         inputPesquisa.addEventListener('keypress', function(e) {
             if (e.key === 'Enter') {
                 e.preventDefault();
                 btnPesquisar.click();
             }
         });
    }

    // Configurar listeners com DELEGAÇÃO no container estático
    const resultadosTabelaContainer = document.getElementById('resultados-tabela');
    if (resultadosTabelaContainer) {
        resultadosTabelaContainer.addEventListener('click', function(e) {
            // Delegação para botões de ordenação
            const sortButton = e.target.closest('.sort');
            if (sortButton) {
                var orderby = sortButton.getAttribute('data-orderby');
                var pesquisa = inputPesquisa ? inputPesquisa.value : '';
                var selectPesquisa = selectPesquisaEl ? selectPesquisaEl.value : '';
                window.carregarTabela(1, pesquisa, selectPesquisa, orderby);
                return; // Importante para não processar outros listeners
            }

            // Delegação para links de paginação
            const paginationButton = e.target.closest('.paginacao-btn');
            if (paginationButton) {
                e.preventDefault();
                var pagina = paginationButton.getAttribute('data-page');
                // Não carrega se for disabled
                if (paginationButton.parentElement.classList.contains('disabled')) {
                    return;
                }
                var pesquisa = inputPesquisa ? inputPesquisa.value : '';
                var selectPesquisa = selectPesquisaEl ? selectPesquisaEl.value : '';
                var orderby = document.querySelector('#resultados-tabela .sort.active') ?
                              document.querySelector('#resultados-tabela .sort.active').getAttribute('data-orderby') : '';
                window.carregarTabela(pagina, pesquisa, selectPesquisa, orderby);
                return; // Importante
            }

            // Delegação para botões de exclusão (se aplicável e se não usar onclick)
            // const deleteButton = e.target.closest('.delete-servico-btn');
            // if (deleteButton) {
            //     // Lógica para chamar deleteServico
            //     return;
            // }
        });
    }

    // A função global deleteServico deve ser definida em assets/js/delete_confirm.js
});
</script>