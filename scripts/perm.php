<?php
// Não verifica permissões durante o processo de login
if (defined('IS_LOGIN_PROCESS') && IS_LOGIN_PROCESS === true) {
    return;
}

// Verifica se está na página de login para evitar redirecionamento cíclico
$current_page = basename($_SERVER['PHP_SELF']);
if ($current_page !== 'login.php') {
    if (!isset($_SESSION["type"])) {
        header("Location: /subrider/login.php");
        exit();
    } else {
        if($_SESSION["type"] < 1) {
            header("Location: /subrider/login.php");
            exit();
        }
    }
}