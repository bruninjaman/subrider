<?php
/**
 * Arquivo de configuração de segurança
 * Contém funções e configurações para proteção da aplicação
 */

// Configurações de Sessão
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 1);
session_set_cookie_params([
    'lifetime' => 3600,
    'path' => '/',
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Strict'
]);

// Headers de Segurança
header("X-XSS-Protection: 1; mode=block");
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: SAMEORIGIN");
header("Referrer-Policy: strict-origin-when-cross-origin");
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval' https://code.jquery.com; style-src 'self' 'unsafe-inline';");

// Funções de Segurança
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

function generate_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf_token($token) {
    if (!isset($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {
        die('Erro de validação CSRF');
    }
    return true;
}

function check_auth() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: /login.php');
        exit();
    }
}

function escape_output($data) {
    if (is_array($data)) {
        return array_map('escape_output', $data);
    }
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

function validate_file_upload($file) {
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    $max_size = 5 * 1024 * 1024; // 5MB
    
    if (!in_array($file['type'], $allowed_types)) {
        throw new Exception('Tipo de arquivo não permitido');
    }
    
    if ($file['size'] > $max_size) {
        throw new Exception('Arquivo muito grande');
    }
    
    return true;
}

// Função para logs de segurança
function security_log($message, $level = 'INFO') {
    $log_file = __DIR__ . '/../logs/security.log';
    $timestamp = date('Y-m-d H:i:s');
    $log_entry = "[$timestamp] [$level] $message\n";
    
    if (!file_exists(dirname($log_file))) {
        mkdir(dirname($log_file), 0755, true);
    }
    
    file_put_contents($log_file, $log_entry, FILE_APPEND);
} 