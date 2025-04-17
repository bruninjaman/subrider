<?php
header('Content-Type: application/json');

function normalizePathForComparison($path) {
    // Normaliza separadores de diretório para forward slash
    $path = str_replace('\\', '/', $path);
    // Remove múltiplas barras
    $path = preg_replace('#/+#', '/', $path);
    // Remove trailing slash
    return rtrim($path, '/');
}

function findPathInContent($oldPath, $content) {
    // Escapa caracteres especiais para uso em regex
    $oldPathEscaped = preg_quote($oldPath, '/');
    
    // Array de padrões para encontrar o path
    $patterns = [
        // Path direto com aspas simples ou duplas
        '/([\'"])'.$oldPathEscaped.'([\'"])/',
        
        // Path em concatenação PHP com echo
        '/echo\s+[\'"]'.$oldPathEscaped.'[\'"]/',
        
        // Path em concatenação PHP com variável
        '/\$[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*\s*\.\s*[\'"]'.$oldPathEscaped.'[\'"]/',
        
        // Path como parte de uma string maior
        '/[\'"][^\'"]+'.$oldPathEscaped.'[^\'"]+'.'[\'"]/',
        
        // Path em include/require
        '/(include|require)(_once)?\s*\([\'"]'.$oldPathEscaped.'[\'"]\)/',
        
        // Path em atributo HTML
        '/(src|href|url)\s*=\s*[\'"]'.$oldPathEscaped.'[\'"]/',
    ];

    // Procura por todas as ocorrências
    $matches = [];
    foreach ($patterns as $pattern) {
        if (preg_match($pattern, $content, $match)) {
            $matches[] = $match[0];
        }
    }

    return $matches;
}

function checkPathExists($path, $sourceFile, $hasPhpVars = false) {
    // Se o path contém variáveis PHP, considera válido
    if ($hasPhpVars) {
        return true;
    }

    // Normaliza os paths
    $path = normalizePathForComparison($path);
    $sourceFile = normalizePathForComparison($sourceFile);
    $sourceDir = dirname($sourceFile);
    
    // Se for URL externa, considera válido
    if (preg_match('#^(https?:)?//#i', $path)) {
        return true;
    }
    
    // Tenta diferentes formas de resolver o path
    $possiblePaths = [
        $path,                                          // Path como está
        $sourceDir . '/' . $path,                      // Relativo ao arquivo fonte
        dirname(__DIR__) . '/' . ltrim($path, '/'),    // Relativo à raiz do projeto
        realpath($sourceDir . '/' . $path),            // Path real resolvido
    ];
    
    foreach ($possiblePaths as $testPath) {
        if ($testPath && file_exists($testPath)) {
            return true;
        }
    }
    
    return false;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sourceFile = $_POST['source_file'];
    $oldPath = $_POST['old_path'];
    $newPath = $_POST['new_path'];
    $hasPhpVars = isset($_POST['has_php_vars']) && $_POST['has_php_vars'] === 'true';

    $response = [
        'success' => false,
        'message' => '',
        'exists' => false,
        'debug' => [] // Array para informações de debug
    ];

    // Validação básica
    if (empty($sourceFile) || empty($oldPath) || empty($newPath)) {
        $response['message'] = 'Parâmetros inválidos';
        echo json_encode($response);
        exit;
    }

    // Verifica se o arquivo fonte existe
    if (!file_exists($sourceFile)) {
        $response['message'] = 'Arquivo fonte não encontrado: ' . $sourceFile;
        echo json_encode($response);
        exit;
    }

    // Lê o conteúdo do arquivo
    $content = file_get_contents($sourceFile);
    if ($content === false) {
        $response['message'] = 'Não foi possível ler o arquivo fonte';
        echo json_encode($response);
        exit;
    }

    // Encontra todas as ocorrências do path antigo
    $matches = findPathInContent($oldPath, $content);
    
    if (empty($matches)) {
        $response['message'] = "Não foi possível encontrar o path antigo no arquivo";
        $response['debug']['patterns_tried'] = $matches;
        echo json_encode($response);
        exit;
    }

    // Substitui cada ocorrência encontrada
    $newContent = $content;
    foreach ($matches as $match) {
        // Preserva o formato original (aspas, concatenação, etc)
        $replacement = str_replace($oldPath, $newPath, $match);
        $newContent = str_replace($match, $replacement, $newContent);
    }

    // Se tem variáveis PHP, considera o path como válido
    $pathExists = $hasPhpVars || checkPathExists($newPath, $sourceFile);
    $response['debug'][] = [
        'path_check' => [
            'exists' => $pathExists,
            'checked_path' => $newPath,
            'has_php_vars' => $hasPhpVars,
            'matches_found' => $matches
        ]
    ];

    // Tenta salvar o arquivo
    if (file_put_contents($sourceFile, $newContent) !== false) {
        $response['success'] = true;
        $response['message'] = "Path atualizado com sucesso!";
        $response['exists'] = true;
    } else {
        $response['message'] = "Erro ao salvar o arquivo";
    }

    echo json_encode($response);
    exit;
}

// Se não for POST, retorna erro
$response = [
    'success' => false,
    'message' => 'Método não permitido'
];
echo json_encode($response);
exit;
?> 