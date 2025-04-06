<?php
// Não verifica permissões durante o processo de login
if (defined('IS_LOGIN_PROCESS') && IS_LOGIN_PROCESS === true) {
    return;
}

// Verifica se está em uma página que não requer autenticação
$public_pages = ['login.php', 'register.php', 'forgot-password.php'];
$current_page = basename($_SERVER['PHP_SELF']);

if (!in_array($current_page, $public_pages)) {
    // Verifica se o usuário está logado
    if (!isset($_SESSION["user"]) || !isset($_SESSION["type"])) {
        header("Location: /subrider/login.php");
        exit();
    }
    
    // Verifica se o usuário tem permissão adequada
    if ($_SESSION["type"] < 1) {
        // Destruir a sessão se o usuário não tem permissão adequada
        session_destroy();
        header("Location: /subrider/login.php?error=permission");
        exit();
    }
}