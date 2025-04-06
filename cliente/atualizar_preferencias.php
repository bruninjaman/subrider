<?php
require_once '../includes/session_manager.php';
require_once '../includes/database.php';

$session = new SessionManager();

// Verifica se o cliente está logado
if (!$session->isClienteLoggedIn()) {
    header('Location: login.php');
    exit;
}

// Verifica se é uma requisição POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: perfil.php');
    exit;
}

$clienteId = $session->getClienteId();
$db = new Database();

// Obtém os valores do formulário
$notificacoesEmail = isset($_POST['notificacoes_email']) ? 1 : 0;
$notificacoesSms = isset($_POST['notificacoes_sms']) ? 1 : 0;
$tema = $_POST['tema'] ?? 'claro';

// Valida o tema
if (!in_array($tema, ['claro', 'escuro'])) {
    $tema = 'claro';
}

try {
    // Verifica se já existem preferências
    $sql = "SELECT id FROM preferencias_cliente WHERE proprietario_id = ?";
    $preferencias = $db->query($sql, [$clienteId]);
    
    if (empty($preferencias)) {
        // Insere novas preferências
        $sql = "INSERT INTO preferencias_cliente 
                    (proprietario_id, notificacoes_email, notificacoes_sms, tema) 
                VALUES (?, ?, ?, ?)";
        $db->query($sql, [$clienteId, $notificacoesEmail, $notificacoesSms, $tema]);
    } else {
        // Atualiza preferências existentes
        $sql = "UPDATE preferencias_cliente 
                SET notificacoes_email = ?,
                    notificacoes_sms = ?,
                    tema = ?,
                    updated_at = NOW()
                WHERE proprietario_id = ?";
        $db->query($sql, [$notificacoesEmail, $notificacoesSms, $tema, $clienteId]);
    }
    
    // Atualiza a sessão com o tema
    $_SESSION['cliente_tema'] = $tema;
    
    // Redireciona com mensagem de sucesso
    header('Location: perfil.php?msg=preferencias_atualizadas');
    exit;
} catch (Exception $e) {
    // Redireciona com mensagem de erro
    header('Location: perfil.php?erro=erro_atualizar_preferencias');
    exit;
} 