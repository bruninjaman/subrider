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

// Define um separador de diretório consistente
define('DS', DIRECTORY_SEPARATOR);

// Caminho absoluto no sistema de arquivos para a raiz do projeto
// __DIR__ retorna o diretório do arquivo atual (config.php), que está na raiz.
define('PROJECT_ROOT_PATH', __DIR__);

// --- Cálculo do Caminho da URL Relativo à Raiz do Domínio ---

// Obtém a raiz do documento do servidor web, tratando barras invertidas e removendo a barra final.
$docRoot = isset($_SERVER['DOCUMENT_ROOT']) ? rtrim(str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']), '/') : '';

// Obtém o diretório do projeto (onde config.php está), tratando barras invertidas e removendo a barra final.
$projectDir = rtrim(str_replace('\\', '/', __DIR__), '/');

// Inicializa o caminho da URL como '/' (caso o projeto esteja na raiz do domínio ou DOC_ROOT não ajude).
$projectRootUrl = '/';

// Verifica se DOC_ROOT está definido, não está vazio, e se o diretório do projeto começa com DOC_ROOT.
// Isso indica que o projeto está em um subdiretório do DocumentRoot.
if (!empty($docRoot) && strpos($projectDir, $docRoot) === 0 && strlen($projectDir) > strlen($docRoot)) {
    // Extrai a parte do caminho que vem depois do DocumentRoot.
    $projectRootUrl = substr($projectDir, strlen($docRoot));
}
// Fallback: Se o cálculo acima falhar (ex: config fora do doc root) mas a pasta base for 'subrider', assume '/subrider'.
// Isso pode ser útil em alguns ambientes de desenvolvimento.
elseif (basename($projectDir) === 'subrider') {
     $projectRootUrl = '/subrider';
}

// Garante que o caminho da URL comece com uma barra, a menos que seja a raiz do domínio ('/').
if (substr($projectRootUrl, 0, 1) !== '/') {
     $projectRootUrl = '/' . $projectRootUrl;
}

// Garante que o caminho da URL não termine com uma barra, a menos que seja apenas '/' (raiz do domínio).
if (strlen($projectRootUrl) > 1) {
    $projectRootUrl = rtrim($projectRootUrl, '/');
}

// Define a constante para o caminho da URL.
define('PROJECT_ROOT_URL', $projectRootUrl);

/*
Exemplos de uso:

// Para includes/requires PHP (usando caminho do sistema de arquivos):
require_once(PROJECT_ROOT_PATH . DS . 'includes' . DS . 'arquivo.php');
require_once(PROJECT_ROOT_PATH . DS . 'connection' . DS . 'connection.php');

// Para assets (imagens, CSS, JS) em HTML/PHP (usando caminho da URL):
<img src="<?php echo PROJECT_ROOT_URL; ?>/assets/images/logo.png">
<link rel="stylesheet" href="<?php echo PROJECT_ROOT_URL; ?>/assets/css/style.css">
<script src="<?php echo PROJECT_ROOT_URL; ?>/assets/js/main.js"></script>

// Para links de navegação em HTML/PHP (usando caminho da URL):
<a href="<?php echo PROJECT_ROOT_URL; ?>/pages/tabelaOrdens/tabela.php">Ordens</a>

// Para URLs de chamadas AJAX em JavaScript (passando a URL via PHP):
// No PHP/HTML:
<script>
  const projectRootUrl = '<?php echo PROJECT_ROOT_URL; ?>';
</script>
// No JS:
fetch(projectRootUrl + '/ajax/carregarDados.php')
  .then(...)

// Ou diretamente no JS se estiver dentro de um bloco PHP ou arquivo .php:
fetch('<?php echo PROJECT_ROOT_URL; ?>/ajax/carregarDados.php')
  .then(...)

// Para redirecionamentos em PHP:
header('Location: ' . PROJECT_ROOT_URL . '/login.php');
exit;

// Para redirecionamentos em JavaScript:
window.location.href = '<?php echo PROJECT_ROOT_URL; ?>/dashboard.php';
// ou usando a variável JS:
window.location.href = projectRootUrl + '/dashboard.php';

*/

?> 