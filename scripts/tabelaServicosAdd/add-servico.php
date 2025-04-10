<?php
// Incluir inicialização segura (sessão, config, db, security, permissions)
require_once __DIR__ . '/../../config/init.php';
require_once __DIR__ . '/../../src/Database/Database.php';
require_once __DIR__ . '/../../src/Security/Security.php';
require_once __DIR__ . '/../../src/Permissions/PermissionManager.php';

use Subrider\Database\Database;
use Subrider\Security\Security;
use Subrider\Permissions\PermissionManager;

// Definição de $baseUrl
$baseUrl = defined('BASE_URL') ? BASE_URL : '/subrider';

// --- Verificações Iniciais ---

// 1. Verificar Permissão (Ex: ADMIN)
// TODO: Definir a permissão correta
if (!PermissionManager::hasPermission(PERMISSION_ADMIN)) {
    $_SESSION['form_errors'] = ['Permissão negada para adicionar serviços.'];
    header("Location: {$baseUrl}/tabelaServicosAdd.php?error=permission");
    exit;
}

// 2. Verificar método POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['form_errors'] = ['Método de requisição inválido.'];
    header("Location: {$baseUrl}/tabelaServicosAdd.php?error=invalid_method");
    exit;
}

// 3. Verificar Token CSRF (Assumindo que init.php configura e formulário envia)
/*
if (!isset($_POST['csrf_token']) || !Security::verifyCsrfToken($_POST['csrf_token'])) {
    $_SESSION['form_errors'] = ['Token de segurança inválido ou ausente.'];
    header("Location: {$baseUrl}/tabelaServicosAdd.php?error=csrf");
    exit;
}
*/

// --- Processamento do Formulário ---

// 1. Obter e Validar Dados
$item = trim($_POST['item'] ?? '');
$tipo = trim($_POST['tipo'] ?? '');
$preco = trim($_POST['preco'] ?? '');
$descricao = trim($_POST['descricao'] ?? '');

$errors = [];
if (empty($item)) $errors[] = "O campo 'Item' é obrigatório.";
if (empty($tipo)) $errors[] = "O campo 'Tipo' é obrigatório.";
if (empty($descricao)) $errors[] = "O campo 'Descrição' é obrigatório.";

// Validar preço
if (!is_numeric($preco) || $preco < 0) {
    $errors[] = "O campo 'Preço' deve ser um número válido maior ou igual a zero.";
} else {
    // Converter para formato numérico adequado para o DB (ex: float)
    $preco = floatval(str_replace(',', '.', $preco)); // Assume que o input pode ter vírgula
}

// Se houver erros, redirecionar
if (!empty($errors)) {
    $_SESSION['form_errors'] = $errors;
    // TODO: Passar valores antigos de volta
    header("Location: {$baseUrl}/tabelaServicosAdd.php?error=validation");
    exit;
}

// 2. Sanitizar Dados
$itemSanitized = Security::sanitizeString($item);
$tipoSanitized = Security::sanitizeString($tipo);
// Preço já é float
$descricaoSanitized = Security::sanitizeString($descricao);

// --- Inserir no Banco de Dados ---
try {
    $db = Database::getInstance();
    $conn = $db->getConnection();

    // TODO: Confirmar nomes das colunas (item, tipo, preco, descricao)
    $sql = "INSERT INTO servicos (item, tipo, preco, descricao) VALUES (:item, :tipo, :preco, :descricao)";
    $stmt = $conn->prepare($sql);

    $stmt->bindParam(':item', $itemSanitized, PDO::PARAM_STR);
    $stmt->bindParam(':tipo', $tipoSanitized, PDO::PARAM_STR);
    $stmt->bindParam(':preco', $preco, PDO::PARAM_STR); // PDO trata float como string aqui
    $stmt->bindParam(':descricao', $descricaoSanitized, PDO::PARAM_STR);

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Serviço adicionado com sucesso!";
        header("Location: {$baseUrl}/tabelaServicos.php?status=success");
        exit;
    } else {
        $errors[] = "Erro ao salvar o serviço no banco de dados.";
        error_log("Erro PDO em add-servico.php: " . implode('; ', $stmt->errorInfo()));
    }

} catch (PDOException $e) {
    $errors[] = "Erro de conexão ou SQL: " . $e->getMessage();
    error_log("Exceção PDO em add-servico.php: " . $e->getMessage());
}

// --- Lidar com Erros Finais ---
if (!empty($errors)) {
    $_SESSION['form_errors'] = $errors;
    header("Location: {$baseUrl}/tabelaServicosAdd.php?error=final");
    exit;
}

// Fallback
echo "Ocorreu um erro inesperado.";
exit;

?>
