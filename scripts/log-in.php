<?php
require_once('../config.php');
//CONNECTION
// require_once("../connection/connection.php");
//FUNCTIONS
// require_once("functions.php");

// Configuração da sessão para 30 dias
ini_set('session.gc_maxlifetime', 30 * 24 * 60 * 60); // 30 dias em segundos
ini_set('session.cookie_lifetime', 30 * 24 * 60 * 60); // 30 dias em segundos
session_set_cookie_params(30 * 24 * 60 * 60); // 30 dias em segundos

// Verificar CSRF token
if (!isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
    security_log("Tentativa de login com CSRF token inválido", "WARNING");
    die('Erro de validação');
}

// Proteção contra força bruta
$max_attempts = 5;
$lockout_time = 900; // 15 minutos

$ip = $_SERVER['REMOTE_ADDR'];
$attempts_key = "login_attempts_" . $ip;
$lockout_key = "login_lockout_" . $ip;

// Verificar se está bloqueado
if (isset($_SESSION[$lockout_key]) && time() < $_SESSION[$lockout_key]) {
    $remaining = $_SESSION[$lockout_key] - time();
    security_log("Tentativa de login durante bloqueio - IP: $ip", "WARNING");
    die("Muitas tentativas. Tente novamente em " . ceil($remaining/60) . " minutos.");
}

// Limpar bloqueio se já passou o tempo
if (isset($_SESSION[$lockout_key]) && time() >= $_SESSION[$lockout_key]) {
    unset($_SESSION[$lockout_key]);
    unset($_SESSION[$attempts_key]);
}

// Sanitizar inputs
$email = sanitize_input($_POST['email']);
$senha = $_POST['senha'];

// Validar email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    security_log("Tentativa de login com email inválido: $email", "WARNING");
    die('Email inválido');
}

// Buscar usuário
$stmt = $conn->prepare("SELECT id, senha, nome FROM usuarios WHERE email = ? LIMIT 1");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    
    // Verificar senha
    if (password_verify($senha, $user['senha'])) {
        // Login bem sucedido - resetar contadores
        unset($_SESSION[$attempts_key]);
        unset($_SESSION[$lockout_key]);
        
        // Registrar login
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_nome'] = $user['nome'];
        
        security_log("Login bem sucedido para usuário: " . $user['nome'], "INFO");
        
        // Regenerar ID da sessão
        session_regenerate_id(true);
        
        header('Location: ../index.php');
        exit();
    }
}

// Login falhou - incrementar contador
$_SESSION[$attempts_key] = ($_SESSION[$attempts_key] ?? 0) + 1;

security_log("Falha de login - Email: $email, IP: $ip, Tentativa: " . $_SESSION[$attempts_key], "WARNING");

// Verificar se excedeu tentativas
if ($_SESSION[$attempts_key] >= $max_attempts) {
    $_SESSION[$lockout_key] = time() + $lockout_time;
    security_log("IP bloqueado por excesso de tentativas: $ip", "WARNING");
    die("Muitas tentativas. Conta bloqueada por 15 minutos.");
}

header('Location: ../login.php?error=1');
exit();
?>