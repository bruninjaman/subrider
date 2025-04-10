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
    $_SESSION['form_errors'] = ['Permissão negada para editar serviços.'];
    header("Location: {$baseUrl}/tabelaServicos.php?error=permission"); // Redireciona para tabela
    exit;
}

// 2. Verificar método POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['form_errors'] = ['Método de requisição inválido.'];
    header("Location: {$baseUrl}/tabelaServicos.php?error=invalid_method");
    exit;
}

// 3. Verificar ID do Serviço na URL
if (!isset($_GET['servicoID']) || !ctype_digit((string)$_GET['servicoID'])) {
    $_SESSION['form_errors'] = ['ID do serviço inválido ou não fornecido na URL.'];
    header("Location: {$baseUrl}/tabelaServicos.php?error=invalid_id");
    exit;
}
$servicoId = intval($_GET['servicoID']);

// 4. Verificar Token CSRF (Assumindo que init.php configura e formulário envia)
/*
if (!isset($_POST['csrf_token']) || !Security::verifyCsrfToken($_POST['csrf_token'])) {
    $_SESSION['form_errors'] = ['Token de segurança inválido ou ausente.'];
    header("Location: {$baseUrl}/tabelaServicosEdit.php?servicoID={$servicoId}&error=csrf");
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
$validation_errors = []; // Para erros específicos

if (empty($item)) $validation_errors['item'] = "Campo obrigatório";
if (empty($tipo)) $validation_errors['tipo'] = "Campo obrigatório";
if (empty($descricao)) $validation_errors['descricao'] = "Campo obrigatório";

if (!is_numeric(str_replace([' ', '.', ','], '', $preco)) || floatval(str_replace(',', '.', $preco)) < 0) {
     $validation_errors['preco'] = "Preço inválido.";
} else {
    $preco = floatval(str_replace(',', '.', $preco));
}

// Combinar erros
if (!empty($validation_errors)) {
    $errors = array_values($validation_errors);
}

// Se houver erros, redirecionar de volta
if (!empty($errors)) {
    $_SESSION['form_errors'] = $errors;
    $_SESSION['validation_errors'] = $validation_errors;
    // TODO: Passar valores antigos de volta
    header("Location: {$baseUrl}/tabelaServicosEdit.php?servicoID={$servicoId}&error=validation");
    exit;
}

// 2. Sanitizar Dados
$itemSanitized = Security::sanitizeString($item);
$tipoSanitized = Security::sanitizeString($tipo);
// Preço já é float
$descricaoSanitized = Security::sanitizeString($descricao);

// --- Atualizar no Banco de Dados ---
try {
    $db = Database::getInstance();
    $conn = $db->getConnection();

    // TODO: Confirmar nomes das colunas
    $sql = "UPDATE servicos SET item = :item, tipo = :tipo, preco = :preco, descricao = :descricao WHERE servicoId = :servicoId";
    $stmt = $conn->prepare($sql);

    $params = [
        ':item' => $itemSanitized,
        ':tipo' => $tipoSanitized,
        ':preco' => $preco,
        ':descricao' => $descricaoSanitized,
        ':servicoId' => $servicoId
    ];

    if ($stmt->execute($params)) {
        if ($stmt->rowCount() > 0) {
            $_SESSION['success_message'] = "Serviço atualizado com sucesso!";
        } else {
             $_SESSION['success_message'] = "Nenhuma alteração detectada."; // Ou um aviso?
        }
        header("Location: {$baseUrl}/tabelaServicosEdit.php?servicoID={$servicoId}&status=success");
        exit;
    } else {
        $errors[] = "Erro ao atualizar o serviço no banco de dados.";
        error_log("Erro PDO em edit-servico.php: " . implode('; ', $stmt->errorInfo()));
    }

} catch (PDOException $e) {
    $errors[] = "Erro de conexão ou SQL: " . $e->getMessage();
    error_log("Exceção PDO em edit-servico.php: " . $e->getMessage());
}

// --- Lidar com Erros Finais ---
if (!empty($errors)) {
    $_SESSION['form_errors'] = $errors;
    $_SESSION['validation_errors'] = $validation_errors;
    header("Location: {$baseUrl}/tabelaServicosEdit.php?servicoID={$servicoId}&error=final");
    exit;
}

// Fallback
echo "Ocorreu um erro inesperado.";
exit;

?>
