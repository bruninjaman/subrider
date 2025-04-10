<?php
require_once __DIR__ . '/../../../config/init.php'; // Ajustar caminho para init.php
require_once __DIR__ . '/../../../src/Database/Database.php';
// require_once __DIR__ . '/../../../scripts/functions.php'; // Comentado - Verificar necessidade

use Subrider\\Database\\Database;

// Obter instância do banco de dados (PDO)
try {
    $db = Database::getInstance();
    $conn = $db->getConnection();
} catch (PDOException $e) {
    // Tratar erro de conexão adequadamente (log, mensagem amigável)
    echo "<tr><td colspan='5'>Erro ao conectar ao banco de dados.</td></tr>";
    // Possivelmente sair ou logar o erro
    error_log("Erro de conexão PDO em tabelaPecas: " . $e->getMessage());
    // exit; // Descomentar se apropriado
    $conn = null; // Garante que não tentaremos usar uma conexão inválida
}

// Inicializa variáveis
$params = [];
$whereClause = '';
$orderClause = '';

// Lógica de Pesquisa (com Prepared Statements)
if (isset($_GET["pesquisa"]) && !empty($_GET["pesquisa"]) && isset($_GET['selectPesquisa']) && !empty($_GET['selectPesquisa'])) {
    $allowedColumns = ['grupo', 'item', 'parte']; // Colunas permitidas para pesquisa
    $searchColumn = strtolower($_GET['selectPesquisa']);
    if (in_array($searchColumn, $allowedColumns)) {
        $whereClause = "WHERE {$searchColumn} LIKE :pesquisa";
        $params[':pesquisa'] = '%' . $_GET["pesquisa"] . '%';
    }
}

// Lógica de Ordenação (com validação)
if (isset($_GET["orderby"]) && !empty($_GET["orderby"])) {
    $allowedOrderColumns = ['grupo', 'item', 'parte', 'pecaId']; // Colunas permitidas para ordenação
    $orderByColumn = $_GET["orderby"];
    // Adicionar suporte opcional para ASC/DESC se necessário, validando também a direção
    if (in_array($orderByColumn, $allowedOrderColumns)) {
        $orderClause = "ORDER BY {$orderByColumn}"; // Adicionar validação de direção se necessário (ASC/DESC)
    } else {
        $orderClause = "ORDER BY pecas.pecaId DESC"; // Fallback seguro
    }
} else {
    $orderClause = "ORDER BY pecas.pecaId DESC"; // Default ordering
}

// Define BASE_URL (idealmente, viria de init.php)
// Se não estiver definido em init.php, descomente e ajuste a linha abaixo
// define('BASE_URL', '/subrider'); 
// Se já estiver definido em init.php, pode remover a linha acima.
// Por segurança, vamos verificar se está definido antes de usar.
$baseUrl = defined('BASE_URL') ? BASE_URL : '/subrider'; // Usar a constante ou um fallback

$sql_base = "SELECT * FROM pecas";
$sql_query = $sql_base . ' ' . $whereClause . ' ' . $orderClause;

// Lógica de Paginação (ainda depende da função pagination())
$sql_query_without_limit = $sql_query;
$limitClause = " LIMIT :offset, :limit";
$itemsPerPage = 5;
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($currentPage - 1) * $itemsPerPage;

$sql_query .= $limitClause;
$params[':offset'] = $offset;
$params[':limit'] = $itemsPerPage;

// Categorias de pesquisa - Convertido para PDO
$categoriasPesquisaQuery = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'pecas'";
$categoriasParaExcluir = ['pecaId', 'foto']; // Colunas a não incluir na pesquisa
$colunasPesquisa = [];
if ($conn) {
    try {
        $stmtCategorias = $conn->query($categoriasPesquisaQuery);
        $colunasBrutas = $stmtCategorias->fetchAll(PDO::FETCH_COLUMN);
        // Filtra colunas indesejadas e sanitiza
        foreach ($colunasBrutas as $coluna) {
            if (!in_array($coluna, $categoriasParaExcluir)) {
                $colunasPesquisa[] = htmlspecialchars($coluna, ENT_QUOTES, 'UTF-8');
            }
        }
    } catch (PDOException $e) {
        error_log("Erro ao buscar colunas para pesquisa em tabelaPecas: " . $e->getMessage());
        // Lidar com erro (ex: usar uma lista padrão de colunas)
        $colunasPesquisa = ['grupo', 'item', 'parte']; // Fallback
    }
} else {
     $colunasPesquisa = ['grupo', 'item', 'parte']; // Fallback se não houver conexão
}

// Passa as colunas para searchbar.php
// Nota: searchbar.php precisará ser ajustado para receber $colunasPesquisa em vez de $resultCategorias (mysqli result)
// TODO: Verificar se searchbar.php está usando $colunasPesquisa corretamente.
include(__DIR__ . '/../../../includes/searchbar.php');
?>

<section id="banner">
    <div class="content">
        <!-- search bar -->
        <?php
        // O searchbar agora é incluído uma vez só, antes desta seção, usando as colunas do PDO.
        // O bloco mysqli_* abaixo foi removido.
        /*
        //Categorias de pesquisa -- REMOVIDO
        $categoriasPesquisa = "SHOW COLUMNS FROM pecas";
        $resultCategorias = mysqli_query($conn, $categoriasPesquisa);
        include_once("./includes/searchbar.php"); // REMOVIDO - Já incluído acima
        */
        ?>
        <div id="resultados-tabela">
            <div class="table-wrapper">
                <div class="table-wrapper" style="overflow: hidden;">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Foto</th>
                                <th><button class="sort" type="button" data-orderby="grupo">Grupo <i class="fa-solid fa-sort"></i></button> </th>
                                <th><button class="sort" type="button" data-orderby="item">Item <i class="fa-solid fa-sort"></i></button></th>
                                <th><button class="sort" type="button" data-orderby="parte">Parte <i class="fa-solid fa-sort"></i></button></th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="tabela-pecas">
                            <?php
                            // O CONTEÚDO DA TABELA AGORA É CARREGADO VIA AJAX INICIALMENTE E EM ATUALIZAÇÕES
                            // O bloco PHP original que buscava dados com PDO (linhas ~60-113) foi comentado abaixo
                            // para evitar busca desnecessária, assumindo que carregarPecas.php faz isso.
                            // TODO: Verificar se carregarPecas.php realmente busca os dados e calcula a paginação corretamente.
                            // Se sim, o bloco comentado abaixo pode ser removido permanentemente.
                            /*
                            if ($conn) { // Verifica se a conexão foi estabelecida -- BLOCO COMENTADO
                                try {
                                    $stmt = $conn->prepare($sql_query);

                                    // Bind dos parâmetros
                                    foreach ($params as $key => $value) {
                                        if ($key === ':offset' || $key === ':limit') {
                                            $stmt->bindValue($key, $value, PDO::PARAM_INT);
                                        } else {
                                            $stmt->bindValue($key, $value, PDO::PARAM_STR);
                                        }
                                    }

                                    $stmt->execute();
                                    $pecas = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                    if (empty($pecas)) {
                                        echo "<tr><td colspan='5'>Nenhum resultado encontrado.</td></tr>";
                                    } else {
                                        foreach ($pecas as $peca) {
                                            // Sanitizar output com htmlspecialchars
                                            $foto = htmlspecialchars($peca['foto'] ?? '', ENT_QUOTES, 'UTF-8');
                                            $grupo = htmlspecialchars($peca['grupo'] ?? '', ENT_QUOTES, 'UTF-8');
                                            $item = htmlspecialchars($peca['item'] ?? '', ENT_QUOTES, 'UTF-8');
                                            $parte = htmlspecialchars($peca['parte'] ?? '', ENT_QUOTES, 'UTF-8');
                                            $pecaId = htmlspecialchars($peca['pecaId'] ?? '', ENT_QUOTES, 'UTF-8');
                                            ?>
                                            <tr>
                                                <td class="img-table"><img src='<?php echo $baseUrl; ?>/<?php echo ltrim($foto, '/'); ?>'></td>
                                                <td data-cell="Grupo"><?php echo $grupo; ?></td>
                                                <td data-cell="Item"><?php echo $item; ?></td>
                                                <td data-cell="Parte"><?php echo $parte; ?></td>
                                                <td>
                                                    <!-- Padronizar caminhos para raiz -->
                                                    <button style="background: none; border: none;" onclick="location.href='<?php echo $baseUrl; ?>/tabelaPecasEdit.php?pecaID=<?php echo $pecaId ?>'"><img src="<?php echo $baseUrl; ?>/assets/css/images/edit-peca.png" style="height: 30px; width: 30px;"></button>
                                                    <button style="background: none; border: none;" onclick="return deletePeca('<?php echo $pecaId; ?>')"><img src="<?php echo $baseUrl; ?>/assets/css/images/x-button-peca.png" style="height: 30px; width: 30px;"></button>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                    }
                                } catch (PDOException $e) {
                                    echo "<tr><td colspan='5'>Erro ao buscar dados.</td></tr>";
                                    error_log("Erro na execução da query em tabelaPecas: " . $e->getMessage());
                                }
                            } // Fim do if ($conn) -- BLOCO COMENTADO
                            */

                            // Adicionando um placeholder simples enquanto o AJAX carrega:
                            ?>
                            <tr><td colspan="5">Carregando peças...</td></tr>
                            <?php
                            /* Bloco PHP com mysqli_* removido daqui
                               linhas ~130 a ~170 do arquivo original
                            */
                            ?>
                        </tbody>
                    </table>
                    <div class="row">
                        <div class="col-3">
                            <a class="button primary" href='<?php echo $baseUrl; ?>/tabelaPecasAdd.php' style="display: flex; align-items: center; justify-content: center; white-space: nowrap; width: fit-content; min-width: 100%;">
                                <img src="<?php echo $baseUrl; ?>/assets/css/images/addpeca.png" style="margin-right: 12px; width: 40px; height: 40px;">
                                Adicionar Item
                            </a>
                        </div>
                        <div class="col-9" id="paginacao-container">
                            <?php
                            // A paginação agora é tratada pelo AJAX (carregarPecas.php)
                            // A chamada original pagination() foi removida/comentada.
                            // O container será preenchido pelo AJAX.
                            // $sql_query = $sql_query_without_limit;
                            // pagination($conn, $sql_query); 
                            ?>
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
    // Usa a variável $baseUrl definida no PHP para construir a URL do AJAX
    var ajaxUrl = '<?php echo $baseUrl; ?>/pages/tabelaPecas/ajax/carregarPecas.php'; 
    var queryParams = '?page=' + pagina + 
                      '&pesquisa=' + encodeURIComponent(pesquisa) + 
                      '&selectPesquisa=' + encodeURIComponent(selectPesquisa) + 
                      '&orderby=' + encodeURIComponent(orderby);
                      
    xhr.open('GET', ajaxUrl + queryParams, true);
    
    xhr.onload = function() {
        if (this.status == 200) {
            document.getElementById('resultados-tabela').innerHTML = this.responseText;
            
            // Reaplica os eventos após o carregamento
            aplicarEventos();
        }
    };
    
    xhr.send();
};

// Função para aplicar eventos aos elementos carregados
function aplicarEventos() {
    // Eventos para os botões de ordenação
    document.querySelectorAll('.sort').forEach(function(botao) {
        botao.addEventListener('click', function() {
            var orderby = this.getAttribute('data-orderby');
            var pesquisa = document.getElementById('input-pesquisa') ? 
                          document.getElementById('input-pesquisa').value : '';
            var selectPesquisa = document.getElementById('selectPesquisa') ? 
                                document.getElementById('selectPesquisa').value : '';
            
            window.carregarTabela(1, pesquisa, selectPesquisa, orderby);
        });
    });
    
    // Eventos para os links de paginação (garantir que seletores ainda funcionem)
    // O seletor original era '.paginacao-btn'. Verificar se carregarPecas.php retorna botões com essa classe.
    document.querySelectorAll('#paginacao-container .paginacao-btn').forEach(function(botao) { 
        botao.addEventListener('click', function(e) {
            e.preventDefault();
            
            var pagina = this.getAttribute('data-page');
            var pesquisa = document.getElementById('input-pesquisa') ? 
                          document.getElementById('input-pesquisa').value : '';
            var selectPesquisa = document.getElementById('selectPesquisa') ? 
                                document.getElementById('selectPesquisa').value : '';
            var orderby = document.querySelector('.sort.active') ? 
                         document.querySelector('.sort.active').getAttribute('data-orderby') : '';
            
            window.carregarTabela(pagina, pesquisa, selectPesquisa, orderby);
        });
    });
    
    // Adicionar listener para o formulário de pesquisa, se existir
    const formPesquisa = document.getElementById('form-pesquisa'); // Assumindo ID 'form-pesquisa' no searchbar.php
    if (formPesquisa) {
        formPesquisa.addEventListener('submit', function(e) {
            e.preventDefault();
            const pesquisa = document.getElementById('input-pesquisa') ? document.getElementById('input-pesquisa').value : '';
            const selectPesquisa = document.getElementById('selectPesquisa') ? document.getElementById('selectPesquisa').value : '';
            // Manter a ordenação atual ou resetar para padrão? Resetar parece mais intuitivo.
            window.carregarTabela(1, pesquisa, selectPesquisa, ''); 
        });
    }
}

// Carrega a tabela inicialmente quando a página é carregada
document.addEventListener('DOMContentLoaded', function() {
    window.carregarTabela(); // Chama sem parâmetros para carga inicial (página 1, sem pesquisa/ordem)
});
</script>
<script src="/assets/js/delete_confirm.js"></script>
<script src="/pages/tabelaPecas/js/deletePeca.js"></script>
</section>