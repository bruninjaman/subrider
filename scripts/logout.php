<?php
require_once __DIR__ . '/system/session_manager.php';

$sessionManager = SessionManager::getInstance();
$sessionManager->destroySession();

// Redireciona para a página de login
header("Location: /login.php");
exit();
?> 