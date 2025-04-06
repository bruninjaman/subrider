<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/notification_manager.php';

// Verifica se é uma requisição POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método não permitido']);
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

// Tenta marcar como lida
try {
    $success = $notificationManager->marcarComoLida($data['id'], $_SESSION['user_id']);
    
    if ($success) {
        echo json_encode(['success' => true]);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Notificação não encontrada']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro ao marcar notificação como lida']);
} 