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

// Se for localhost, usa o valor do .env, senão usa o caminho relativo ao root
if ($is_localhost) {
    $baseAddress = isset($_ENV['BASE_ADDRESS']) ? $_ENV['BASE_ADDRESS'] : '';
} else {
    // No servidor, será o path absoluto para o diretório do site
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $baseAddress = $protocol . '://' . $server_name . dirname($_SERVER['PHP_SELF']);
    
    // Remover '/pages/ordemservico' ou outros subdiretórios específicos
    $baseAddress = preg_replace('~/pages/[^/]+~', '', $baseAddress);
    
    // Remover qualquer '/' extra no final
    $baseAddress = rtrim($baseAddress, '/');
}

// Definir base URL para JavaScript
$baseURL = $baseAddress; 