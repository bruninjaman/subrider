<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/notification_manager.php';

// Verifica se é uma requisição POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método não permitido']);
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
        http_response_code(500);
        echo json_encode(['error' => 'Erro ao marcar notificações como lidas']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro ao marcar notificações como lidas']);
} 