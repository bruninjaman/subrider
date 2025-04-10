<?php
// Incluir inicialização segura (sessão, config, db, security, permissions)
require_once __DIR__ . '/../../../config/init.php';
require_once __DIR__ . '/../../../src/Database/Database.php';
require_once __DIR__ . '/../../../src/Security/Security.php';
require_once __DIR__ . '/../../../src/Permissions/PermissionManager.php';
// require_once __DIR__ . '/../../../scripts/functions.php'; // Remover se pagination não for usada

use Subrider\Database\Database;
use Subrider\Security\Security;
use Subrider\Permissions\PermissionManager;

// Definição de $baseUrl (idealmente vindo de init.php)
$baseUrl = defined('BASE_URL') ? BASE_URL : '/subrider';

// Obter conexão PDO
try {
    $db = Database::getInstance();
    $conn = $db->getConnection();
} catch (PDOException $e) {
    echo "<div class='table-wrapper'><p>Erro ao conectar ao banco de dados.</p></div>";
    error_log("Erro de conexão PDO em carregarServicos.php: " . $e->getMessage());
    exit;
}

// --- Parâmetros e Validação ---
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$pesquisa = isset($_GET['pesquisa']) ? trim($_GET['pesquisa']) : '';
$selectPesquisa = isset($_GET['selectPesquisa']) ? trim($_GET['selectPesquisa']) : '';
$orderby = isset($_GET['orderby']) ? trim($_GET['orderby']) : '';
$itemsPerPage = 5; // Ou outro valor desejado

// Whitelists para colunas (Tabela 'servicos')
// TODO: Confirmar nomes exatos das colunas em 'servicos'
$allowedSearchColumns = ['item', 'tipo', 'descricao']; // Colunas pesquisáveis
$allowedOrderColumns = ['item', 'tipo', 'preco', 'servicoId']; // Colunas ordenáveis

// Validar colunas
if (!empty($selectPesquisa) && !in_array($selectPesquisa, $allowedSearchColumns)) {
    $selectPesquisa = '';
}
if (!empty($orderby) && !in_array($orderby, $allowedOrderColumns)) {
    $orderby = 'servicoId';
    $orderDirection = 'DESC';
} else {
    $orderDirection = ($orderby === 'servicoId') ? 'DESC' : 'ASC';
}

// --- Construção da Query com Prepared Statements ---
$params = [];
$whereClause = '';
$orderClause = '';

if (!empty($pesquisa) && !empty($selectPesquisa)) {
    $whereClause = "WHERE {$selectPesquisa} LIKE :pesquisa";
    $params[':pesquisa'] = '%' . $pesquisa . '%';
}

$orderClause = "ORDER BY {$orderby} {$orderDirection}";

// --- Query para Contagem Total ---
$sqlCount = "SELECT COUNT(*) FROM servicos {$whereClause}";
$totalItems = 0;
try {
    $stmtCount = $conn->prepare($sqlCount);
    if (!empty($params[':pesquisa'])) {
        $stmtCount->bindParam(':pesquisa', $params[':pesquisa'], PDO::PARAM_STR);
    }
    $stmtCount->execute();
    $totalItems = $stmtCount->fetchColumn();
} catch (PDOException $e) {
    error_log("Erro na contagem em carregarServicos.php: " . $e->getMessage());
}

$totalPages = ($itemsPerPage > 0) ? ceil($totalItems / $itemsPerPage) : 0;
$offset = ($page - 1) * $itemsPerPage;

// --- Query Principal (com LIMIT e OFFSET) ---
// TODO: Confirmar nomes das colunas (item, tipo, preco, descricao?)
$sqlQuery = "SELECT servicoId, item, tipo, preco, descricao FROM servicos {$whereClause} {$orderClause} LIMIT :limit OFFSET :offset";
$servicos = [];
try {
    $stmt = $conn->prepare($sqlQuery);
    if (!empty($params[':pesquisa'])) {
        $stmt->bindParam(':pesquisa', $params[':pesquisa'], PDO::PARAM_STR);
    }
    $stmt->bindValue(':limit', $itemsPerPage, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $servicos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Erro na busca em carregarServicos.php: " . $e->getMessage());
}

// --- Geração do HTML ---
?>
<div class="table-wrapper">
    <div class="table-wrapper">
        <table class="table servicetable">
            <thead>
                <tr>
                    <!-- TODO: Ajustar colunas e ordenação conforme a tabela 'servicos' -->
                    <th><button class="sort <?php echo ($orderby == 'item') ? 'active' : ''; ?>" type="button" data-orderby="item">Item <i class="fa-solid fa-sort"></i></button></th>
                    <th><button class="sort <?php echo ($orderby == 'tipo') ? 'active' : ''; ?>" type="button" data-orderby="tipo">Tipo <i class="fa-solid fa-sort"></i></button></th>
                    <th><button class="sort <?php echo ($orderby == 'preco') ? 'active' : ''; ?>" type="button" data-orderby="preco">Preço <i class="fa-solid fa-sort"></i></button></th>
                    <th>Descrição</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody id="tabela-servicos">
                <?php if (empty($servicos)): ?>
                    <tr><td colspan="5">Nenhum serviço encontrado.</td></tr>
                <?php else: ?>
                    <?php foreach ($servicos as $servico):
                        // Sanitizar output
                        $item = htmlspecialchars($servico['item'] ?? '', ENT_QUOTES, 'UTF-8');
                        $tipo = htmlspecialchars($servico['tipo'] ?? '', ENT_QUOTES, 'UTF-8');
                        $preco = isset($servico['preco']) ? 'R$ ' . number_format($servico['preco'], 2, ',', '.') : 'N/A';
                        $descricao = htmlspecialchars($servico['descricao'] ?? '', ENT_QUOTES, 'UTF-8');
                        $servicoId = htmlspecialchars($servico['servicoId'] ?? '', ENT_QUOTES, 'UTF-8');
                        // URL de exclusão para AJAX
                        $deleteUrl = $baseUrl . '/scripts/tabelaServicos/delete-serv.php?servID=' . $servicoId;
                    ?>
                        <tr>
                            <td data-cell="Item"><?php echo $item; ?></td>
                            <td data-cell="Tipo"><?php echo $tipo; ?></td>
                            <td data-cell="Preço"><?php echo $preco; ?></td>
                            <td data-cell="Descrição"><?php echo $descricao; ?></td>
                            <td>
                                <!-- Links usam $baseUrl e apontam para scripts na raiz -->
                                <button style="background: none; border: none;" onclick="location.href='<?php echo $baseUrl; ?>/tabelaServicosEdit.php?servicoID=<?php echo $servicoId; ?>'"><img src="<?php echo $baseUrl; ?>/assets/css/images/edit.png" style="height: 30px; width: 30px;"></button>
                                <!-- Chama a função global deleteServico de delete_confirm.js -->
                                <button style="background: none; border: none;" onclick="deleteServico('<?php echo $servicoId; ?>', '<?php echo $deleteUrl; ?>')"><img src="<?php echo $baseUrl; ?>/assets/css/images/x-button.png" style="height: 30px; width: 30px;"></button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
        <div class="row">
            <div class="col-3">
                 <!-- Link usa $baseUrl e aponta para script na raiz -->
                <a class="button primary" href='<?php echo $baseUrl; ?>/tabelaServicosAdd.php'>Adicionar Serviço</a>
            </div>
            <div class="col-9" id="paginacao-container">
                <?php
                // --- Geração da Paginação ---
                if ($totalPages > 1):
                    $range = 2;
                ?>
                    <nav aria-label="Paginação Serviços">
                        <ul class="pagination" style="justify-content: flex-end;">
                            <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                                <a class="page-link paginacao-btn" href="#" data-page="<?php echo $page - 1; ?>" aria-label="Anterior"><span aria-hidden="true">&laquo;</span></a>
                            </li>
                            <?php for ($i = 1; $i <= $totalPages; $i++):
                                if ($i == 1 || $i == $totalPages || ($i >= $page - $range && $i <= $page + $range)):
                            ?>
                                <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                                    <a class="page-link paginacao-btn" href="#" data-page="<?php echo $i; ?>"><?php echo $i; ?></a>
                                </li>
                            <?php elseif ($i == $page - $range - 1 || $i == $page + $range + 1):
                            ?>
                                <li class="page-item disabled"><span class="page-link">...</span></li>
                            <?php endif; endfor; ?>
                            <li class="page-item <?php echo ($page >= $totalPages) ? 'disabled' : ''; ?>">
                                <a class="page-link paginacao-btn" href="#" data-page="<?php echo $page + 1; ?>" aria-label="Próximo"><span aria-hidden="true">&raquo;</span></a>
                            </li>
                        </ul>
                    </nav>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php // Não incluir redefinição de deleteServico ?> 