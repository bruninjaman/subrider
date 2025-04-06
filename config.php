<?php
// Configurações básicas
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/logs/error.log');

// Configurações do Banco de Dados
define('DB_HOST', 'localhost');
define('DB_USER', 'subrider_user');
define('DB_PASS', 'sua_senha_aqui');
define('DB_NAME', 'subrider_db');

// Email do Administrador
define('ADMIN_EMAIL', 'admin@subrider.com');

// Outras configurações
define('UPLOAD_DIR', __DIR__ . '/uploads');
define('BACKUP_DIR', __DIR__ . '/backups');
define('MAX_BACKUPS', 30);

// Timezone
date_default_timezone_set('America/Sao_Paulo');

// Arquivos de configuração comum
require_once(__DIR__ . '/scripts/security.php');
require_once(__DIR__ . '/scripts/perm.php');
require_once(__DIR__ . '/connection/connection.php');
require_once(__DIR__ . '/scripts/functions.php');

// Iniciar sessão segura se ainda não foi iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar autenticação (exceto para páginas públicas)
$public_pages = ['login.php', 'index.php','log-in.php'];
$current_page = basename($_SERVER['PHP_SELF']);

if (!in_array($current_page, $public_pages)) {
    check_auth();
}

// Gerar CSRF token para formulários
if (empty($_SESSION['csrf_token'])) {
    generate_csrf_token();
}
?> 