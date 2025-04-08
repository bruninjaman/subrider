<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/notification_manager.php';
require_once __DIR__ . '/../../classes/Security/PermissionManager.php';

// Headers de segurança
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');

// Verifica se é uma requisição POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método não permitido']);
    exit;
}

// Verifica permissão
$permManager = \Security\PermissionManager::getInstance();
$permManager->loadUserPermissions($_SESSION['user_id']);

if (!$permManager->hasPermission('notifications.delete')) {
    http_response_code(403);
    echo json_encode(['error' => 'Permissão negada']);
    exit;
}

// Obtém dados da requisição
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'ID da notificação não fornecido']);
    exit;
}

// Inicializa gerenciador
$notificationManager = new NotificationManager();

// Tenta excluir a notificação
try {
    $success = $notificationManager->excluirNotificacao($data['id'], $_SESSION['user_id']);
    
    if ($success) {
        echo json_encode(['success' => true]);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Notificação não encontrada']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro ao excluir notificação']);
} 