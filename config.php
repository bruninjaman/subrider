<?php
// Carregar o autoloader e as dependências se existirem
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
    
    // Carregar variáveis de ambiente do arquivo .env se existir
    if (file_exists(__DIR__ . '/.env')) {
        $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
        $dotenv->load();
    }
}
// Define a constante para o caminho da URL.
define('PROJECT_ROOT_URL', $projectRootUrl);

?> 