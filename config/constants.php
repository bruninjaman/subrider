<?php
/**
 * Arquivo de constantes globais
 * 
 * @package Subrider
 * @version 1.0.0
 */

// Paths
define('BASE_PATH', dirname(__DIR__));
define('CONFIG_PATH', __DIR__);
define('LOG_PATH', BASE_PATH . '/logs');
define('UPLOAD_PATH', BASE_PATH . '/upload');
define('TEMP_PATH', BASE_PATH . '/temp');

// Níveis de permissão
define('PERMISSION_GUEST', 0);
define('PERMISSION_USER', 1);
define('PERMISSION_ADMIN', 2);

// Configurações de sessão
define('SESSION_LIFETIME', 3600); // 1 hora
define('SESSION_NAME', 'SUBRIDER_SESSION');

// Configurações de segurança
define('CSRF_TOKEN_NAME', 'csrf_token');
define('PASSWORD_MIN_LENGTH', 8);
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOGIN_TIMEOUT', 300); // 5 minutos

// URLs e endpoints
define('BASE_URL', (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://') . $_SERVER['HTTP_HOST']);
define('API_VERSION', 'v1');
define('API_BASE_URL', BASE_URL . '/api/' . API_VERSION); 