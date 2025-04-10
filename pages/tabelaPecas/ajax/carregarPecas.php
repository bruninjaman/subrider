<?php
// Caminho relativo seguro para init.php
require_once __DIR__ . '/../../../config/init.php';
require_once __DIR__ . '/../../../src/Database/Database.php';

use Subrider\\Database\\Database;

// Definição de $baseUrl (idealmente vindo de init.php)
$baseUrl = defined('BASE_URL') ? BASE_URL : '/subrider';

// Obter conexão PDO
try {
    $db = Database::getInstance();
    $conn = $db->getConnection();
} catch (PDOException $e) {
    // Em um cenário real, logar o erro e retornar uma resposta JSON de erro
    // Para simplificar, retornamos uma mensagem HTML
    echo "<div class='table-wrapper'><p>Erro ao conectar ao banco de dados.</p></div>";
    error_log("Erro de conexão PDO em carregarPecas.php: " . $e->getMessage());
    exit;
}

// --- Parâmetros e Validação ---
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$pesquisa = isset($_GET['pesquisa']) ? trim($_GET['pesquisa']) : '';
$selectPesquisa = isset($_GET['selectPesquisa']) ? trim($_GET['selectPesquisa']) : '';
$orderby = isset($_GET['orderby']) ? trim($_GET['orderby']) : '';
$itemsPerPage = 5;

// Whitelists para colunas
$allowedSearchColumns = ['grupo', 'item', 'parte'];
$allowedOrderColumns = ['grupo', 'item', 'parte', 'pecaId'];

// Validar colunas (importante para ORDER BY e WHERE)
if (!empty($selectPesquisa) && !in_array($selectPesquisa, $allowedSearchColumns)) {
    $selectPesquisa = ''; // Ignora coluna inválida
}
if (!empty($orderby) && !in_array($orderby, $allowedOrderColumns)) {
    $orderby = 'pecaId'; // Fallback para coluna padrão
    $orderDirection = 'DESC';
} else {
    // Poderia adicionar suporte para direção (ASC/DESC) aqui se necessário
    $orderDirection = ($orderby === 'pecaId') ? 'DESC' : 'ASC'; // Exemplo: ID DESC, outros ASC
}

// --- Construção da Query com Prepared Statements ---
$params = [];
$whereClause = '';
$orderClause = '';

// Cláusula WHERE
if (!empty($pesquisa) && !empty($selectPesquisa)) {
    // Usar a coluna validada
    $whereClause = "WHERE {$selectPesquisa} LIKE :pesquisa";
    $params[':pesquisa'] = '%' . $pesquisa . '%';
}

// Cláusula ORDER BY
// Usar a coluna e direção validadas. Concatenar ORDER BY é seguro APÓS validação.
$orderClause = "ORDER BY {$orderby} {$orderDirection}";

// --- Query para Contagem Total (para paginação) ---
$sqlCount = "SELECT COUNT(*) FROM pecas {$whereClause}";
try {
    $stmtCount = $conn->prepare($sqlCount);
    // Bind apenas se houver pesquisa
    if (!empty($params[':pesquisa'])) {
        $stmtCount->bindParam(':pesquisa', $params[':pesquisa'], PDO::PARAM_STR);
    }
    $stmtCount->execute();
    $totalItems = $stmtCount->fetchColumn();
} catch (PDOException $e) {
    error_log("Erro na contagem em carregarPecas.php: " . $e->getMessage());
    $totalItems = 0; // Evita erros na paginação
}

$totalPages = ceil($totalItems / $itemsPerPage);
$offset = ($page - 1) * $itemsPerPage;

// --- Query Principal (com LIMIT e OFFSET) ---
$sqlQuery = "SELECT * FROM pecas {$whereClause} {$orderClause} LIMIT :limit OFFSET :offset";

try {
    $stmt = $conn->prepare($sqlQuery);

    // Bind dos parâmetros (pesquisa, limit, offset)
    if (!empty($params[':pesquisa'])) {
        $stmt->bindParam(':pesquisa', $params[':pesquisa'], PDO::PARAM_STR);
    }
    $stmt->bindValue(':limit', $itemsPerPage, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

    $stmt->execute();
    $pecas = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log("Erro na busca em carregarPecas.php: " . $e->getMessage());
    $pecas = []; // Define como array vazio para não quebrar o HTML
}

// --- Geração do HTML --- 
// (Mantendo a estrutura original que substitui #resultados-tabela)
?>
<div class="table-wrapper">
    <div class="table-wrapper" style="overflow: hidden;"> 
        <table class="table">
            <thead>
                <tr>
                    <th>Foto</th>
                    <th><button class="sort <?php echo ($orderby == 'grupo') ? 'active' : ''; ?>" type="button" data-orderby="grupo">Grupo <i class="fa-solid fa-sort"></i></button></th>
                    <th><button class="sort <?php echo ($orderby == 'item') ? 'active' : ''; ?>" type="button" data-orderby="item">Item <i class="fa-solid fa-sort"></i></button></th>
                    <th><button class="sort <?php echo ($orderby == 'parte') ? 'active' : ''; ?>" type="button" data-orderby="parte">Parte <i class="fa-solid fa-sort"></i></button></th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody id="tabela-pecas">
                <?php if (empty($pecas)): ?>
                    <tr><td colspan="5">Nenhum resultado encontrado.</td></tr>
                <?php else: ?>
                    <?php foreach ($pecas as $peca): 
                        // Sanitizar output
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
                                <!-- Usar $baseUrl -->
                                <button style="background: none; border: none;" onclick="location.href='<?php echo $baseUrl; ?>/tabelaPecasEdit.php?pecaID=<?php echo $pecaId; ?>'"><img src="<?php echo $baseUrl; ?>/assets/css/images/edit-peca.png" style="height: 30px; width: 30px;"></button>
                                <!-- A função deletePeca é global (delete_confirm.js), não precisa redefinir. Precisa do caminho correto para o script de delete -->
                                <button style="background: none; border: none;" onclick="return deletePeca('<?php echo $pecaId; ?>', '<?php echo $baseUrl; ?>/scripts/tabelaPecasDelete/delete-peca.php?pecaID=<?php echo $pecaId; ?>')"><img src="<?php echo $baseUrl; ?>/assets/css/images/x-button-peca.png" style="height: 30px; width: 30px;"></button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
        <div class="row">
            <div class="col-3">
                <!-- Usar $baseUrl -->
                <a class="button primary" href='<?php echo $baseUrl; ?>/tabelaPecasAdd.php' style="display: flex; align-items: center; justify-content: center; white-space: nowrap; width: fit-content; min-width: 100%;">
                    <img src="<?php echo $baseUrl; ?>/assets/css/images/addpeca.png" style="margin-right: 12px; width: 40px; height: 40px;">
                    Adicionar Item
                </a>
            </div>
            <div class="col-9" id="paginacao-container">
                <?php 
                // --- Geração da Paginação --- 
                if ($totalPages > 1): 
                    $range = 2; // Quantidade de links antes/depois da página atual
                ?>
                    <nav aria-label="Paginação">
                        <ul class="pagination" style="justify-content: flex-end;"> 
                            <?php // Botão Anterior ?>
                            <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                                <a class="page-link paginacao-btn" href="#" data-page="<?php echo $page - 1; ?>" aria-label="Anterior">
                                    <span aria-hidden="true">&laquo;</span>
                                </a>
                            </li>

                            <?php // Links das Páginas
                            for ($i = 1; $i <= $totalPages; $i++): 
                                // Mostra apenas links próximos à página atual + primeiro/último
                                if ($i == 1 || $i == $totalPages || ($i >= $page - $range && $i <= $page + $range)): 
                            ?>
                                <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                                    <a class="page-link paginacao-btn" href="#" data-page="<?php echo $i; ?>"><?php echo $i; ?></a>
                                </li>
                            <?php 
                                // Adiciona '...' se houver um salto nos números
                                elseif ($i == $page - $range - 1 || $i == $page + $range + 1): 
                            ?>
                                <li class="page-item disabled"><span class="page-link">...</span></li>
                            <?php 
                                endif;
                            endfor; 
                            ?>

                            <?php // Botão Próximo ?>
                            <li class="page-item <?php echo ($page >= $totalPages) ? 'disabled' : ''; ?>">
                                <a class="page-link paginacao-btn" href="#" data-page="<?php echo $page + 1; ?>" aria-label="Próximo">
                                    <span aria-hidden="true">&raquo;</span>
                                </a>
                            </li>
                        </ul>
                    </nav>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php 
// Não incluir o script deletePeca aqui, pois ele já está globalmente em delete_confirm.js
// Apenas garantir que a chamada onclick passe a URL correta para o script de delete.
// É necessário ajustar a função global deletePeca() em delete_confirm.js para aceitar a URL como segundo parâmetro.
?> 