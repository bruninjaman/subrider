<?php
/**
 * Autoloader para as classes do PathTools
 */

spl_autoload_register(function ($class) {
    // Base directory for the namespace prefix
    $base_dir = __DIR__ . '/';

    // Namespace prefix para nossa aplicação
    $namespace_prefix = 'PathTools\\';

    // Verificar se a classe usa o namespace do projeto
    $len = strlen($namespace_prefix);
    if (strncmp($namespace_prefix, $class, $len) !== 0) {
        // A classe não usa nosso namespace, então não tentamos carregar
        return;
    }

    // Pegar o caminho relativo da classe
    $relative_class = substr($class, $len);

    // Mapeamento de namespaces para diretórios
    $mappings = [
        'Core\\' => 'core/',
        'UI\\' => 'ui/',
        'Utils\\' => 'utils/',
        'Lib\\' => 'lib/'
    ];
    
    $file = null;
    
    // Verificar se o namespace se encaixa em algum dos mapeamentos
    foreach ($mappings as $prefix => $dir) {
        if (strpos($relative_class, $prefix) === 0) {
            $sub_class = substr($relative_class, strlen($prefix));
            $file = $base_dir . $dir . str_replace('\\', '/', $sub_class) . '.php';
            break;
        }
    }
    
    // Se não se encaixar em nenhum mapeamento, usar o caminho padrão
    if ($file === null) {
        $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    }
    
    // Se o arquivo existir, incluí-lo
    if (file_exists($file)) {
        require $file;
    }
}); 