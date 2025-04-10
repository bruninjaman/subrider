<?php
// Iniciar sessão se necessário (verificar se há controle de acesso)
// session_start();

// Usar caminhos relativos seguros e consistentes
require_once __DIR__ . '/../../config/init.php';
require_once __DIR__ . '/../../src/Database/Database.php';
require_once __DIR__ . '/../../src/Permissions/PermissionManager.php';

use Subrider\Database\Database;
use Subrider\Permissions\PermissionManager;

// Define o tipo de conteúdo como JSON
header('Content-Type: application/json');

// Resposta padrão de erro
$response = ['success' => false, 'message' => 'Erro desconhecido.'];

// 1. Verificar se o usuário está autenticado e tem permissão
if (!PermissionManager::hasPermission(PERMISSION_ADMIN)) {
    $response['message'] = 'Permissão negada.';
    echo json_encode($response);
    exit();
}

// 2. Verificar se o ID da peça foi fornecido e é válido
if (!isset($_GET['pecaID']) || !is_numeric($_GET['pecaID'])) {
    $response['message'] = 'ID da peça inválido ou não fornecido.';
    echo json_encode($response);
    exit();
}

$pecaId = intval($_GET['pecaID']);

// 3. Tentar excluir a peça
try {
    $db = Database::getInstance();
    $conn = $db->getConnection();

    // TODO: Adicionar verificação de permissão do usuário aqui, se aplicável

    // Preparar a declaração usando PDO
    $stmt = $conn->prepare("DELETE FROM pecas WHERE pecaId = :pecaId");

    // Bind do parâmetro
    $stmt->bindParam(':pecaId', $pecaId, PDO::PARAM_INT);

    // Executar a declaração
    if ($stmt->execute()) {
        if ($stmt->rowCount() > 0) {
            $response['success'] = true;
            $response['message'] = 'Peça excluída com sucesso.';
        } else {
            $response['message'] = 'Peça não encontrada ou já excluída.';
        }
    } else {
        $response['message'] = 'Erro ao executar a exclusão no banco de dados.';
        // Logar o erro real em um ambiente de produção
        error_log("Erro PDO ao excluir peça ID {$pecaId}: " . implode(' - ', $stmt->errorInfo()));
    }

} catch (PDOException $e) {
    $response['message'] = 'Erro de conexão ou na operação com o banco de dados.';
    error_log("Erro PDO em delete-peca.php: " . $e->getMessage());
}

// 4. Retornar a resposta JSON
echo json_encode($response);
exit(); // Garante que nada mais seja enviado
?>
