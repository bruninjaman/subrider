<?php
// Configurações específicas para testes
define('APP_ENV', 'testing');
define('DB_CONNECTION', 'testing');

// Desativa output de headers
define('TESTING', true);

// Mock do SessionManager
if (!class_exists('SessionManager')) {
    require_once __DIR__ . '/Mocks/SessionManager.php';
    class_alias('Tests\Mocks\SessionManager', 'SessionManager');
}

// Configurações de banco de dados para testes
define('DB_HOST', 'localhost');
define('DB_NAME', 'subrider_test');
define('DB_USER', 'root');
define('DB_PASS', '');

// Outras configurações
error_reporting(E_ALL);
ini_set('display_errors', 'Off');

// Mock de funções globais
if (!function_exists('redirect')) {
    function redirect($url) {
        return true;
    }
}

if (!function_exists('header')) {
    function header($header, $replace = true, $http_response_code = null) {
        return true;
    }
}

if (!function_exists('session_start')) {
    function session_start() {
        return true;
    }
}

if (!function_exists('session_destroy')) {
    function session_destroy() {
        return true;
    }
}