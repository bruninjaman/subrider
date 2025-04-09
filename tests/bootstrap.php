<?php
/**
 * Bootstrap para testes
 * 
 * Este arquivo é carregado antes de todos os testes
 */

namespace Tests;

use Dotenv\Dotenv;
use Subrider\Database;

// Carrega o autoloader do Composer se existir
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
}

// Carrega configurações do sistema
// require_once __DIR__ . '/../config.php'; // Comentado ou removido - não existe mais
require_once __DIR__ . '/../config/init.php'; // Carrega o novo arquivo de inicialização

// Carrega autoloader personalizado
require_once __DIR__ . '/autoload.php';

// Carrega variáveis de ambiente de teste
if (file_exists(__DIR__ . '/../.env.testing')) {
    $dotenv = \Dotenv\Dotenv::createImmutable(__DIR__ . '/../', '.env.testing');
    $dotenv->load();
} else {
    // Configura variáveis de ambiente padrão para testes
    $_ENV['DB_HOST'] = 'localhost';
    $_ENV['DB_DATABASE'] = 'subrider_test';
    $_ENV['DB_USERNAME'] = 'root';
    $_ENV['DB_PASSWORD'] = '';
    $_ENV['DB_PORT'] = '3306';
    $_ENV['APP_ENV'] = 'testing';
    $_ENV['DISABLE_NOTIFICATIONS'] = 'true';
    $_ENV['DISABLE_WHATSAPP'] = 'true';
}

// Configura o banco de dados de teste
$db = new Database(
    $_ENV['DB_HOST'],
    $_ENV['DB_DATABASE'],
    $_ENV['DB_USERNAME'],
    $_ENV['DB_PASSWORD']
);

// Limpa e recria as tabelas necessárias para os testes
function resetDatabase($db) {
    // Remove tabelas se existirem
    $db->query("DROP TABLE IF EXISTS avaliacoes");
    
    // Cria tabela de avaliações
    $db->query("
        CREATE TABLE avaliacoes (
            id INT AUTO_INCREMENT PRIMARY KEY,
            ordem_id VARCHAR(50) NOT NULL,
            nota INT NOT NULL,
            comentario TEXT,
            data_avaliacao DATETIME DEFAULT CURRENT_TIMESTAMP,
            proprietario_id INT NOT NULL,
            status ENUM('pendente', 'aprovada', 'rejeitada') DEFAULT 'pendente',
            motivo_rejeicao TEXT
        )
    ");
}

// Reseta o banco de dados antes dos testes
resetDatabase($db);

// Configuração do ambiente de testes
$_ENV['APP_ENV'] = 'testing';
$_ENV['DB_CONNECTION'] = 'testing';

// Define constantes para ambiente de teste
define('TESTING', true);

// Configura error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Configura timezone
date_default_timezone_set('America/Sao_Paulo');

// Função helper para mock de sessão
if (!function_exists('mockSession')) {
    function mockSession(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
}

// Função helper para limpar sessão
if (!function_exists('clearSession')) {
    function clearSession(): void {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
    }
}

// Registra função de cleanup ao finalizar os testes
register_shutdown_function(function() {
    clearSession();
});

// Função para mock de sessão
if (!function_exists('session_start')) {
    function session_start() {
        return true;
    }
}

if (!function_exists('session_destroy')) {
    function session_destroy() {
        $_SESSION = [];
        return true;
    }
}

// Mock para funções de header
if (!function_exists('header')) {
    function header($header, $replace = true, $http_response_code = null) {
        return true;
    }
}

if (!function_exists('headers_sent')) {
    function headers_sent(&$file = null, &$line = null) {
        return false;
    }
}

// Configuração do banco de dados de teste
require_once __DIR__ . '/setup_test_db.php'; 