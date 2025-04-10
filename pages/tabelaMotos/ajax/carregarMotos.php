<?php
// Define que é uma requisição AJAX
define('IS_AJAX_REQUEST', true);

// Incluir inicialização segura (sessão, config, db, security, permissions)
require_once __DIR__ . '/../../../config/init.php';
require_once __DIR__ . '/../../../src/Database/Database.php';
require_once __DIR__ . '/../../../src/Security/Security.php';
require_once __DIR__ . '/../../../src/Permissions/PermissionManager.php';

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
    // Retornar uma mensagem de erro HTML (ou JSON em uma API real)
    echo "<div class='table-wrapper'><p>Erro ao conectar ao banco de dados.</p></div>";
    error_log("Erro de conexão PDO em carregarMotos.php: " . $e->getMessage());
    exit;
}

// --- Parâmetros e Validação ---
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$pesquisa = isset($_GET['pesquisa']) ? trim($_GET['pesquisa']) : '';
$selectPesquisa = isset($_GET['selectPesquisa']) ? trim($_GET['selectPesquisa']) : '';
$orderby = isset($_GET['orderby']) ? trim($_GET['orderby']) : '';
$itemsPerPage = 5;

// Whitelists para colunas (MUITO IMPORTANTE para segurança)
$allowedSearchColumns = ['endereco', 'modelo', 'marca', 'placa', 'proprietario']; // Colunas pesquisáveis
$allowedOrderColumns = ['endereco', 'ano', 'modelo', 'marca', 'placa', 'km', 'proprietario', 'motoId']; // Colunas ordenáveis

// Validar colunas
if (!empty($selectPesquisa) && !in_array($selectPesquisa, $allowedSearchColumns)) {
    $selectPesquisa = ''; // Ignora coluna de pesquisa inválida
}
if (!empty($orderby) && !in_array($orderby, $allowedOrderColumns)) {
    $orderby = 'motoId'; // Fallback para ordenação padrão
    $orderDirection = 'DESC';
} else {
    // Define direção padrão (ex: ID DESC, outros ASC)
    $orderDirection = ($orderby === 'motoId') ? 'DESC' : 'ASC';
    // TODO: Permitir que o usuário escolha a direção (ASC/DESC) se necessário, validando-a também.
}

// --- Construção da Query com Prepared Statements ---
$params = [];
$whereClause = '';
$orderClause = '';

// Cláusula WHERE (apenas se pesquisa e coluna de pesquisa forem válidas)
if (!empty($pesquisa) && !empty($selectPesquisa)) {
    // Usar a coluna validada
    $whereClause = "WHERE {$selectPesquisa} LIKE :pesquisa";
    $params[':pesquisa'] = '%' . $pesquisa . '%';
}

// Cláusula ORDER BY (usando coluna e direção validadas)
$orderClause = "ORDER BY {$orderby} {$orderDirection}";

// --- Query para Contagem Total (para paginação) ---
$sqlCount = "SELECT COUNT(*) FROM motocicletas {$whereClause}";
$totalItems = 0; // Inicializa
try {
    $stmtCount = $conn->prepare($sqlCount);
    if (!empty($params[':pesquisa'])) {
        $stmtCount->bindParam(':pesquisa', $params[':pesquisa'], PDO::PARAM_STR);
    }
    $stmtCount->execute();
    $totalItems = $stmtCount->fetchColumn();
} catch (PDOException $e) {
    error_log("Erro na contagem em carregarMotos.php: " . $e->getMessage());
    // Não sai, apenas $totalItems será 0, evitando erros na paginação
}

$totalPages = ($itemsPerPage > 0) ? ceil($totalItems / $itemsPerPage) : 0;
$offset = ($page - 1) * $itemsPerPage;

// --- Query Principal (com LIMIT e OFFSET) ---
$sqlQuery = "SELECT * FROM motocicletas {$whereClause} {$orderClause} LIMIT :limit OFFSET :offset";
$motos = []; // Inicializa
try {
    $stmt = $conn->prepare($sqlQuery);

    // Bind dos parâmetros (pesquisa, limit, offset)
    if (!empty($params[':pesquisa'])) {
        $stmt->bindParam(':pesquisa', $params[':pesquisa'], PDO::PARAM_STR);
    }
    $stmt->bindValue(':limit', $itemsPerPage, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

    $stmt->execute();
    $motos = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log("Erro na busca em carregarMotos.php: " . $e->getMessage());
    // Não sai, apenas $motos estará vazio, o HTML tratará disso
}

// --- Geração do HTML --- (Estrutura principal mantida)
?>
<div class="table-wrapper">
    <div class="table-wrapper" style="overflow: hidden;">
        <table class="table">
            <thead>
                <tr>
                    <th>Foto</th>
                    <!-- Adiciona classe 'active' ao botão de ordenação ativo -->
                    <th><button class="sort <?php echo ($orderby == 'endereco') ? 'active' : ''; ?>" type="button" data-orderby="endereco">Endereço <i class="fa-solid fa-sort"></i></button></th>
                    <th><button class="sort <?php echo ($orderby == 'ano') ? 'active' : ''; ?>" type="button" data-orderby="ano">Ano <i class="fa-solid fa-sort"></i></button></th>
                    <th><button class="sort <?php echo ($orderby == 'modelo') ? 'active' : ''; ?>" type="button" data-orderby="modelo">Modelo <i class="fa-solid fa-sort"></i></button></th>
                    <th><button class="sort <?php echo ($orderby == 'marca') ? 'active' : ''; ?>" type="button" data-orderby="marca">Marca <i class="fa-solid fa-sort"></i></button></th>
                    <th><button class="sort <?php echo ($orderby == 'placa') ? 'active' : ''; ?>" type="button" data-orderby="placa">Placa <i class="fa-solid fa-sort"></i></button></th>
                    <th><button class="sort <?php echo ($orderby == 'km') ? 'active' : ''; ?>" type="button" data-orderby="km">KM <i class="fa-solid fa-sort"></i></button></th>
                    <th><button class="sort <?php echo ($orderby == 'proprietario') ? 'active' : ''; ?>" type="button" data-orderby="proprietario">Proprietario <i class="fa-solid fa-sort"></i></button></th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody id="tabela-motos">
                <?php if (empty($motos)): ?>
                    <tr><td colspan="9">Nenhum resultado encontrado.</td></tr>
                <?php else: ?>
                    <?php foreach ($motos as $moto):
                        // Sanitizar output para prevenir XSS
                        $fotoUrl = !empty($moto['foto']) ? $baseUrl . '/' . ltrim(htmlspecialchars($moto['foto'], ENT_QUOTES, 'UTF-8'), '/') : $baseUrl . '/assets/css/images/placeholder-moto.png'; // Placeholder se não houver foto
                        $endereco = htmlspecialchars($moto['endereco'] ?? '', ENT_QUOTES, 'UTF-8');
                        $ano = htmlspecialchars($moto['ano'] ?? '', ENT_QUOTES, 'UTF-8');
                        $modelo = htmlspecialchars($moto['modelo'] ?? '', ENT_QUOTES, 'UTF-8');
                        $marca = htmlspecialchars($moto['marca'] ?? '', ENT_QUOTES, 'UTF-8');
                        $placa = htmlspecialchars($moto['placa'] ?? '', ENT_QUOTES, 'UTF-8');
                        // Usa number_format para formatar KM e adiciona " km"
                        $kmFormatted = isset($moto['km']) && is_numeric($moto['km']) ? number_format($moto['km'], 0, ',', '.') . ' km' : 'N/A';
                        $proprietario = htmlspecialchars($moto['proprietario'] ?? '', ENT_QUOTES, 'UTF-8');
                        $motoId = htmlspecialchars($moto['motoId'] ?? '', ENT_QUOTES, 'UTF-8');
                        // Gerar URL de exclusão segura
                        $deleteUrl = $baseUrl . '/scripts/tabelaMotos/delete-moto.php?motoID=' . $motoId;
                    ?>
                        <tr>
                            <td class="img-table"><img src='<?php echo $fotoUrl; ?>'></td>
                            <td data-cell="Endereço"><?php echo $endereco; ?></td>
                            <td data-cell="Ano"><?php echo $ano; ?></td>
                            <td data-cell="Modelo"><?php echo $modelo; ?></td>
                            <td data-cell="Marca"><?php echo $marca; ?></td>
                            <td data-cell="Placa"><?php echo $placa; ?></td>
                            <td data-cell="Km"><?php echo $kmFormatted; // Usa a variável formatada ?></td>
                            <td data-cell="Proprietario"><?php echo $proprietario; ?></td>
                            <td>
                                <!-- Links usam $baseUrl e apontam para scripts na raiz -->
                                <button style="background: none; border: none;" onclick="location.href='<?php echo $baseUrl; ?>/editmotos.php?motoID=<?php echo $motoId; ?>'"><img src="<?php echo $baseUrl; ?>/assets/css/images/edit-new.png" style="height: 28px; width: 38px;"></button>
                                <!-- Chama a função global deleteMoto do delete_confirm.js, passando a URL -->
                                <button style="background: none; border: none;" onclick="deleteMoto('<?php echo $motoId; ?>', '<?php echo $deleteUrl; ?>')"><img src="<?php echo $baseUrl; ?>/assets/css/images/x-button-new.png" style="height: 28px; width: 38px;"></button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
        <div class="row">
            <div class="col-3">
                <!-- Link usa $baseUrl e aponta para script na raiz -->
                <a class="button primary" href='<?php echo $baseUrl; ?>/addmotos.php' style="display: flex; align-items: center; justify-content: center; white-space: nowrap; width: fit-content; min-width: 100%;">
                    <img src="<?php echo $baseUrl; ?>/assets/css/images/addmoto.png" style="margin-right: 12px;">
                    Adicionar Motocicleta
                </a>
            </div>
            <div class="col-9" id="paginacao-container">
                <?php
                // --- Geração da Paginação (adaptado do carregarPecas.php) ---
                if ($totalPages > 1):
                    $range = 2; // Quantidade de links antes/depois
                ?>
                    <nav aria-label="Paginação Motos">
                        <ul class="pagination" style="justify-content: flex-end;">
                            <?php // Botão Anterior ?>
                            <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                                <a class="page-link paginacao-btn" href="#" data-page="<?php echo $page - 1; ?>" aria-label="Anterior">
                                    <span aria-hidden="true">&laquo;</span>
                                </a>
                            </li>

                            <?php // Links das Páginas
                            for ($i = 1; $i <= $totalPages; $i++):
                                // Mostra apenas links próximos + primeiro/último
                                if ($i == 1 || $i == $totalPages || ($i >= $page - $range && $i <= $page + $range)):
                            ?>
                                <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                                    <a class="page-link paginacao-btn" href="#" data-page="<?php echo $i; ?>"><?php echo $i; ?></a>
                                </li>
                            <?php
                                // Adiciona '...' se houver um salto
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
                <?php
                // A função pagination() original foi removida, pois a lógica agora está aqui.
                // Se pagination() fazia algo mais, essa lógica precisa ser incorporada.
                // pagination($conn, $sql_query_without_limit); // REMOVIDO
                ?>
            </div>
        </div>
    </div>
</div> 