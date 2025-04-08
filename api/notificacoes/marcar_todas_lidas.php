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

if (!$permManager->hasPermission('notifications.update')) {
    http_response_code(403);
    echo json_encode(['error' => 'Permissão negada']);
    exit;
}

// Inicializa gerenciador
$notificationManager = new NotificationManager();

// Tenta marcar todas como lidas
try {
    $success = $notificationManager->marcarTodasComoLidas($_SESSION['user_id']);
    
    if ($success) {
        echo json_encode(['success' => true]);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Nenhuma notificação encontrada']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro ao marcar notificações como lidas']);
} 