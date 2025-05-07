<?php
//test
// Carregar o autoloader e as dependências se existirem
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
    
    // Carregar variáveis de ambiente do arquivo .env se existir
    if (file_exists(__DIR__ . '/.env')) {
        $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
        $dotenv->load();
    }
}

// Definir base address de forma dinâmica
$server_name = $_SERVER['SERVER_NAME'];
$is_localhost = (strpos($server_name, 'localhost') !== false || $server_name === '127.0.0.1');

$baseAddress = isset($_ENV['BASE_ADDRESS']) ? $_ENV['BASE_ADDRESS'] : '';

// Definir base URL para JavaScript
$baseURL = $baseAddress; 