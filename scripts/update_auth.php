<?php
/**
 * Script para atualizar os arquivos que precisam de autenticação
 * Adiciona a inclusão do arquivo de inicialização e remove código redundante
 */

// Lista de arquivos que precisam ser atualizados
$files_to_update = [
    'index.php',
    'ordemservico.php',
    'criar_ordem.php',
    'editproprietario.php',
    'addproprietario.php',
    'change_password.php',
    'addmotos.php',
    'editmotos.php',
    'medicoes.php',
    'relatorio.php',
    'proprietario.php'
];

$base_dir = dirname(__DIR__);

foreach ($files_to_update as $file) {
    $full_path = $base_dir . '/' . $file;
    if (!file_exists($full_path)) {
        echo "Arquivo não encontrado: $file\n";
        continue;
    }

    $content = file_get_contents($full_path);
    
    // Remove todos os blocos PHP do início do arquivo e ?> soltos
    $content = preg_replace('/^<\?php[\s\S]*?\?>/m', '', $content);
    $content = preg_replace('/^\s*\?>\s*$/m', '', $content);
    
    // Remove espaços em branco extras no início
    $content = ltrim($content);
    
    // Adiciona require do init.php no início do arquivo
    $init_include = '<?php
require_once __DIR__ . \'/config/init.php\';
?>';
    
    // Adiciona o novo código no início
    $content = $init_include . "\n" . $content;
    
    // Salva o arquivo
    file_put_contents($full_path, $content);
    echo "Arquivo atualizado: $file\n";
}

echo "Atualização concluída!\n"; 