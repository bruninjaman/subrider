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
$response = ['success' => false, 'message' => 'Erro desconhecido ao excluir motocicleta.'];

// --- Verificações Iniciais ---

// 1. Verificar Permissão (Ex: ADMIN)
// TODO: Definir a permissão correta
if (!PermissionManager::hasPermission(PERMISSION_ADMIN)) {
    $response['message'] = 'Permissão negada para excluir motocicletas.';
    echo json_encode($response);
    exit;
}

// 2. Verificar método (pode ser GET se chamado pelo link antigo, idealmente deveria ser POST/DELETE com AJAX)
// Por ora, aceitamos GET, mas adicionamos verificação CSRF se possível
/*
if ($_SERVER['REQUEST_METHOD'] !== 'DELETE' && $_SERVER['REQUEST_METHOD'] !== 'POST') { // Idealmente
    $response['message'] = 'Método de requisição inválido.';
    echo json_encode($response);
    exit;
}
*/

// 3. Verificar ID da Moto na URL
if (!isset($_GET['motoID']) || !ctype_digit((string)$_GET['motoID'])) {
    $response['message'] = 'ID da motocicleta inválido ou não fornecido.';
    echo json_encode($response);
    exit;
}
$motoId = intval($_GET['motoID']);

// 4. Verificar Token CSRF (Importante se aceitar GET ou se o AJAX não enviar por header)
// TODO: Adicionar verificação CSRF se aplicável (ex: ?csrf_token=... na URL do AJAX)
/*
$token = $_GET['csrf_token'] ?? $_POST['csrf_token'] ?? null;
if (!$token || !Security::validateCsrfToken($token)) {
    $response['message'] = 'Token de segurança inválido ou ausente.';
    echo json_encode($response);
    exit;
}
*/

// --- Operações com Banco de Dados e Arquivos ---
try {
    $db = Database::getInstance();
    $conn = $db->getConnection();

    // 5. Buscar dados da moto (para obter caminho da foto)
    $stmtFetch = $conn->prepare("SELECT foto FROM motocicletas WHERE motoId = :motoId");
    $stmtFetch->bindParam(':motoId', $motoId, PDO::PARAM_INT);
    $stmtFetch->execute();
    $moto = $stmtFetch->fetch(PDO::FETCH_ASSOC);

    if (!$moto) {
        // Moto não encontrada, considera como sucesso para o usuário (já foi excluída?)
        $response['success'] = true;
        $response['message'] = 'Motocicleta não encontrada ou já excluída.';
        // Poderia retornar false se for importante saber que não foi excluída *agora*
        // $response['message'] = 'Motocicleta não encontrada.';
    } else {
        // 6. Tentar excluir do Banco de Dados
        $stmtDelete = $conn->prepare("DELETE FROM motocicletas WHERE motoId = :motoId");
        $stmtDelete->bindParam(':motoId', $motoId, PDO::PARAM_INT);

        if ($stmtDelete->execute()) {
            if ($stmtDelete->rowCount() > 0) {
                $response['success'] = true;
                $response['message'] = 'Motocicleta excluída com sucesso.';

                // 7. Excluir foto do servidor APÓS sucesso no DB
                $fotoPath = $moto['foto'];
                if (!empty($fotoPath)) {
                    $fullFotoPath = ROOT_DIR . '/' . ltrim($fotoPath, '/');
                    if (file_exists($fullFotoPath) && is_file($fullFotoPath)) {
                        if (!unlink($fullFotoPath)) {
                            // Logar erro, mas não falhar a operação principal
                            error_log("Falha ao excluir foto da moto: {$fullFotoPath} para moto ID {$motoId}");
                            $response['message'] .= ' (Aviso: falha ao excluir arquivo de foto associado)';
                        }
                    }
                }
                // TODO: Excluir histórico associado à moto, se necessário?

                // 8. Excluir histórico de proprietários associado
                try {
                    $stmtDeleteHist = $conn->prepare("DELETE FROM historico_proprietarios WHERE moto_id = :motoId");
                    $stmtDeleteHist->bindParam(':motoId', $motoId, PDO::PARAM_INT);
                    if (!$stmtDeleteHist->execute()) {
                        error_log("Falha ao excluir histórico de proprietários para moto ID {$motoId}: " . implode('; ', $stmtDeleteHist->errorInfo()));
                        $response['message'] .= ' (Aviso: falha ao excluir histórico de proprietários)';
                    }
                } catch (PDOException $e) {
                    error_log("Exceção PDO ao excluir histórico de proprietários para moto ID {$motoId}: " . $e->getMessage());
                    $response['message'] .= ' (Aviso: erro ao excluir histórico de proprietários)';
                }

            } else {
                // Não afetou linhas, talvez já tivesse sido excluída entre o fetch e o delete?
                $response['success'] = true; // Considera sucesso idempotente
                $response['message'] = 'Motocicleta não encontrada ou já excluída (delete não afetou linhas).';
            }
        } else {
            $response['message'] = 'Erro ao excluir a motocicleta do banco de dados.';
            error_log("Erro PDO em delete-moto.php: " . implode('; ', $stmtDelete->errorInfo()));
        }
    }

} catch (PDOException $e) {
    $response['message'] = 'Erro de conexão ou SQL ao excluir motocicleta.';
    error_log("Exceção PDO em delete-moto.php: " . $e->getMessage());
}

// --- Resposta Final ---
echo json_encode($response);
exit;

?>
