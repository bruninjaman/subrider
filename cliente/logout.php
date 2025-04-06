<?php
require_once '../includes/client_manager.php';
require_once '../includes/session_manager.php';

$session = new SessionManager();

// Se houver um token de acesso, revoga ele
if ($token = $session->getClienteToken()) {
    $clientManager = new ClientManager();
    $clientManager->revogarToken($token);
}

// Encerra a sessão
$session->logout();

// Redireciona para o login
header('Location: login.php');
exit; 