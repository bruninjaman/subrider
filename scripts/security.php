<?php
/**
 * Arquivo de configurações de segurança
 * 
 * Este arquivo contém:
 * - Configurações de segurança básicas
 * - Funções de validação e sanitização
 * - Proteção contra ataques comuns
 */

// Prevenir acesso direto ao arquivo
// if (!defined('SECURITY_INCLUDED')) {
//     die('Acesso direto não permitido');
// }

// Configurações de segurança
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Content-Security-Policy: default-src \'self\'; script-src \'self\' \'unsafe-inline\' \'unsafe-eval\'; style-src \'self\' \'unsafe-inline\';');

// Funções de segurança
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

function validate_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function check_session_timeout() {
    $timeout = 3600; // 1 hora
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeout)) {
        session_unset();
        session_destroy();
        return false;
    }
    return true;
}

// Verificar se a sessão está ativa e válida
if (session_status() === PHP_SESSION_ACTIVE) {
    if (!check_session_timeout()) {
        header('Location: /login.php?error=session_expired');
        exit;
    }
    $_SESSION['last_activity'] = time();
}

/**
 * Verifica se o acesso é direto e não permitido
 * 
 * @return bool Retorna true se o acesso for direto e não permitido
 */
function is_direct_access() {
    $script_name = basename($_SERVER['SCRIPT_FILENAME']);
    $is_index = $script_name === 'index.php';
    $no_referer = !isset($_SERVER['HTTP_REFERER']);
    $not_localhost = !in_array($_SERVER['SERVER_NAME'], ['localhost', '127.0.0.1']);
    
    return $is_index && $no_referer && $not_localhost;
}

// Verificar acesso direto
if (is_direct_access()) {
    error_log("Tentativa de acesso direto detectada em security.php: " . $_SERVER['REQUEST_URI']);
    header('Location: /pages/access-denied.php');
    exit;
}

// Definir constante para indicar que o arquivo foi incluído
// define('SECURITY_INCLUDED', true); 