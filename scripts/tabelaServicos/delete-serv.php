<?php
// Incluir inicialização segura (sessão, config, db, security, permissions)
require_once __DIR__ . '/../../config/init.php';
require_once __DIR__ . '/../../src/Database/Database.php';
require_once __DIR__ . '/../../src/Security/Security.php';
require_once __DIR__ . '/../../src/Permissions/PermissionManager.php';

use Subrider\Database\Database;
use Subrider\Security\Security;
use Subrider\Permissions\PermissionManager;

// Define o tipo de conteúdo como JSON para a resposta AJAX
header('Content-Type: application/json');

// Resposta padrão de erro
$response = ['success' => false, 'message' => 'Erro desconhecido ao excluir serviço.'];

// --- Verificações Iniciais ---

// 1. Verificar Permissão (Ex: ADMIN)
// TODO: Definir a permissão correta
if (!PermissionManager::hasPermission(PERMISSION_ADMIN)) {
    $response['message'] = 'Permissão negada para excluir serviços.';
    echo json_encode($response);
    exit;
}

// 2. Verificar ID do Serviço na URL (parâmetro é servID)
if (!isset($_GET['servID']) || !ctype_digit((string)$_GET['servID'])) {
    $response['message'] = 'ID do serviço inválido ou não fornecido.';
    echo json_encode($response);
    exit;
}
$servicoId = intval($_GET['servID']);

// 3. Verificar Token CSRF (Importante)
// TODO: Adicionar verificação CSRF se aplicável (ex: ?csrf_token=...)
/*
$token = $_GET['csrf_token'] ?? null;
if (!$token || !Security::validateCsrfToken($token)) {
    $response['message'] = 'Token de segurança inválido ou ausente.';
    echo json_encode($response);
    exit;
}
*/

// --- Operações com Banco de Dados ---
try {
    $db = Database::getInstance();
    $conn = $db->getConnection();

    // 4. Tentar excluir do Banco de Dados
    $stmtDelete = $conn->prepare("DELETE FROM servicos WHERE servicoId = :servicoId");
    $stmtDelete->bindParam(':servicoId', $servicoId, PDO::PARAM_INT);

    if ($stmtDelete->execute()) {
        if ($stmtDelete->rowCount() > 0) {
            $response['success'] = true;
            $response['message'] = 'Serviço excluído com sucesso.';
            // TODO: Excluir itens de ordens associados a este serviço?
        } else {
            // Não afetou linhas, talvez já tivesse sido excluído?
            $response['success'] = true; // Considera sucesso idempotente
            $response['message'] = 'Serviço não encontrado ou já excluído.';
        }
    } else {
        $response['message'] = 'Erro ao excluir o serviço do banco de dados.';
        error_log("Erro PDO em delete-serv.php: " . implode('; ', $stmtDelete->errorInfo()));
    }

} catch (PDOException $e) {
    // Verificar erro de chave estrangeira (se serviço estiver em uso em ordens)
    if ($e->getCode() == '23000') { // Código SQLSTATE para violação de integridade
         $response['message'] = 'Erro: Este serviço não pode ser excluído pois está sendo utilizado em uma ou mais Ordens de Serviço.';
    } else {
        $response['message'] = 'Erro de conexão ou SQL ao excluir serviço.';
        error_log("Exceção PDO em delete-serv.php: " . $e->getMessage());
    }
}

// --- Resposta Final ---
echo json_encode($response);
exit;

?>
