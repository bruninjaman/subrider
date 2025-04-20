<?php
// Ativar buffer de saída para capturar quaisquer mensagens de erro
ob_start();

// Configurar manipulador de erros para capturar erros e evitar saída direta
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    // Registrar o erro em um formato que podemos retornar como JSON
    $GLOBALS['error_occurred'] = true;
    $GLOBALS['error_message'] = $errstr;
    $GLOBALS['error_line'] = $errline;
    $GLOBALS['error_file'] = $errfile;
    
    // Retornar true para evitar manipulação de erro padrão
    return true;
});

// Definir flag de erro
$GLOBALS['error_occurred'] = false;

session_start();
header('Content-Type: application/json');

// Função auxiliar para verificar se um arquivo existe
function check_file_exists($path, $sourceFile) {
    error_log("Verificando se o arquivo existe: $path (no contexto de $sourceFile)");
    
    // Para URLs externas, consideramos como válido por padrão
    if (strpos($path, 'http') === 0 || strpos($path, '//') === 0) {
        error_log("URL externa detectada, considerando válida");
        return true;
    }
    
    // PROTEÇÃO: Verifica se o caminho está vazio
    if (empty($path)) {
        error_log("ALERTA: Caminho vazio passado para check_file_exists");
        return false;
    }
    
    // Obter caminho absoluto do arquivo fonte
    $sourceDir = dirname(realpath($sourceFile));
    if (!$sourceDir) {
        $sourceDir = dirname($sourceFile);
    }
    
    // 1. Verificar o caminho exatamente como está (preservar estrutura)
    $absolutePath = $sourceDir . '/' . $path;
    error_log("Tentativa 1 - Direto: $absolutePath");
    
    if (file_exists($absolutePath)) {
        error_log("Arquivo encontrado diretamente: $absolutePath");
        return true;
    }
    
    // 2. Tentar resolver caminho relativo considerando ../ e ./
    $pathParts = explode('/', str_replace('\\', '/', $path));
    $resultParts = [];
    
    foreach ($pathParts as $part) {
        if ($part === '..') {
            // Subir um nível
            if (!empty($resultParts)) {
                array_pop($resultParts);
            }
        } elseif ($part !== '.' && $part !== '') {
            // Adicionar parte ao resultado
            $resultParts[] = $part;
        }
    }
    
    // Reconstruir o caminho
    $resolvedPath = implode('/', $resultParts);
    $absoluteResolved = $sourceDir . '/' . $resolvedPath;
    error_log("Tentativa 2 - Resolvido: $absoluteResolved");
    
    if (file_exists($absoluteResolved)) {
        error_log("Arquivo encontrado (resolvido): $absoluteResolved");
        return true;
    }
    
    // 3. Tentar em diretórios comuns, preservando o máximo da estrutura
    $filename = basename($path);
    $dirPath = dirname($path);
    
    if ($dirPath !== '.' && $dirPath !== '/') {
        $dirParts = explode('/', $dirPath);
        $lastDir = end($dirParts);
        
        $commonRoots = ['', 'assets', 'images', 'img', 'css', 'js', 'includes', 'media'];
        
        foreach ($commonRoots as $root) {
            // Tentar com última pasta + nome do arquivo
            $commonPath = $root . ($root ? '/' : '') . $lastDir . '/' . $filename;
            $absoluteCommon = $sourceDir . '/' . $commonPath;
            error_log("Tentativa 3 - Estrutura parcial: $absoluteCommon");
            
            if (file_exists($absoluteCommon)) {
                error_log("Arquivo encontrado (estrutura parcial): $absoluteCommon");
                return true;
            }
        }
    }
    
    // 4. Último recurso: procurar apenas pelo nome do arquivo em diretórios comuns
    $commonDirs = ['', 'images', 'img', 'css', 'js', 'scripts', 'assets', 'includes', 'media'];
    
    foreach ($commonDirs as $dir) {
        $simplePath = ($dir ? "$dir/" : '') . $filename;
        $absoluteSimple = $sourceDir . '/' . $simplePath;
        error_log("Tentativa 4 - Simples: $absoluteSimple");
        
        if (file_exists($absoluteSimple)) {
            error_log("Arquivo encontrado (caminho simples): $absoluteSimple");
            return true;
        }
    }
    
    error_log("Arquivo não encontrado em nenhuma tentativa");
    return false;
}

// Função recursiva para procurar o arquivo
function find_file_recursive($dir, $filename, $maxDepth = 3, $currentDepth = 0) {
    // Diretórios a serem ignorados
    $ignoredDirs = ['vendor', 'node_modules', '.git', 'bower_components', 'cache', 'dist'];
    $baseName = basename($dir);
    
    // Verifica se o diretório atual deve ser ignorado
    if (in_array($baseName, $ignoredDirs)) {
        return null;
    }
    
    // Limitamos a profundidade da busca
    if ($currentDepth > $maxDepth) return null;
    
    // Verificamos se o arquivo existe diretamente nesta pasta
    $potentialPath = $dir . '/' . $filename;
    if (file_exists($potentialPath)) {
        return $potentialPath;
    }
    
    // Procuramos em todas as subpastas
    $subdirs = glob($dir . '/*', GLOB_ONLYDIR);
    foreach ($subdirs as $subdir) {
        $result = find_file_recursive($subdir, $filename, $maxDepth, $currentDepth + 1);
        if ($result) return $result;
    }
    
    return null;
}

// Função para encontrar caminho alternativo válido
function find_alternative_path($oldPath, $sourceFile) {
    error_log("\n------ INICIANDO BUSCA POR CAMINHO ALTERNATIVO ------");
    error_log("Caminho original: $oldPath");
    error_log("Arquivo fonte: $sourceFile");
    
    // Obter diretório absoluto do arquivo fonte
    $sourceDir = dirname(realpath($sourceFile));
    
    // Extrair o nome do arquivo do caminho antigo
    $filename = basename($oldPath);
    error_log("Nome do arquivo a procurar: $filename");
    
    // Raiz do projeto
    $projectRoot = realpath(dirname(__DIR__));
    
    // ABORDAGEM 1: Verificar se o caminho existe com a estrutura atual
    // Tenta primeiro corrigir erros simples de capitalização ou pequenas diferenças
    $oldDirPath = dirname($oldPath);
    $oldDirParts = explode('/', $oldDirPath);
    
    // Tentar manter a estrutura original intacta
    $possiblePath = $oldPath;
    $absolutePath = $sourceDir . '/' . $possiblePath;
    error_log("Tentando caminho direto: $absolutePath");
    
    if (file_exists($absolutePath)) {
        error_log("Caminho original existe! Mantendo-o: $possiblePath");
        return $possiblePath;
    }
    
    // ABORDAGEM 2: Tentar encontrar o arquivo na mesma estrutura de diretórios
    // mas corrigindo possíveis erros de capitalização/naming
    $dirsToSearch = [$sourceDir]; // Iniciar com o diretório do arquivo fonte
    
    // Adicionar mais lugares para procurar
    if (!empty($oldDirParts) && count($oldDirParts) > 0) {
        // Tentar em subdiretórios comuns com a mesma estrutura
        $commonParentDirs = ['', 'assets', 'images', 'img', 'css', 'js', 'includes', 'media'];
        foreach ($commonParentDirs as $parentDir) {
            if (!empty($parentDir)) {
                $dirsToSearch[] = $sourceDir . '/' . $parentDir;
            }
        }
    }
    
    error_log("Procurando em " . count($dirsToSearch) . " diretórios, preservando estrutura");
    foreach ($dirsToSearch as $baseDir) {
        // Recriar a estrutura de pastas, mas a partir do diretório base atual
        $pathToTry = '';
        if (count($oldDirParts) > 0 && $oldDirParts[0] !== '.') {
            $pathToTry = implode('/', $oldDirParts);
            $fullDirPath = $baseDir . '/' . $pathToTry;
            $fullFilePath = $fullDirPath . '/' . $filename;
            
            error_log("Tentando: $fullFilePath");
            if (file_exists($fullFilePath)) {
                $relativePath = path_make_relative($sourceDir, $fullFilePath);
                error_log("SUCESSO! Encontrado mantendo estrutura: $relativePath");
                return $relativePath;
            }
            
            // Tentar apenas pela última parte do diretório + filename
            if (count($oldDirParts) > 0) {
                $lastDir = $oldDirParts[count($oldDirParts) - 1];
                $partialPath = $lastDir . '/' . $filename;
                $fullDirPath = $baseDir . '/' . $lastDir;
                $fullFilePath = $fullDirPath . '/' . $filename;
                
                error_log("Tentando com última pasta: $fullFilePath");
                if (file_exists($fullFilePath)) {
                    $relativePath = path_make_relative($sourceDir, $fullFilePath);
                    error_log("SUCESSO! Encontrado com última pasta: $relativePath");
                    return $relativePath;
                }
            }
        }
    }
    
    // ABORDAGEM 3: Busca recursiva mais ampla para achar o arquivo em qualquer lugar
    error_log("Iniciando busca recursiva pelo arquivo: $filename");
    
    // Armazenar todos os caminhos encontrados
    $foundFiles = array();
    find_file_recursively($projectRoot, $filename, $foundFiles);
    
    if (!empty($foundFiles)) {
        error_log("Encontrado(s) " . count($foundFiles) . " arquivo(s) com o nome");
        
        // Ordenar por comprimento, preferindo caminhos mais curtos
        usort($foundFiles, function($a, $b) {
            return strlen($a) - strlen($b);
        });
        
        // Obter o melhor caminho encontrado
        $bestMatch = $foundFiles[0];
        $relativePath = path_make_relative($sourceDir, $bestMatch);
        
        error_log("Melhor correspondência: $relativePath");
        return $relativePath;
    }
    
    // ABORDAGEM 4: Último recurso - manter apenas o nome do arquivo
    error_log("Último recurso: apenas o nome do arquivo: $filename");
    return $filename;
}

// Função para buscar arquivos recursivamente
function find_file_recursively($baseDir, $filename, &$results, $depth = 0, $maxDepth = 5) {
    // Limitar profundidade para evitar loops
    if ($depth > $maxDepth) return;
    
    // Ignorar diretórios específicos
    $ignoreDirs = ['vendor', 'node_modules', '.git', '.svn', 'cache', 'dist', 'build'];
    $currentDir = basename($baseDir);
    if (in_array($currentDir, $ignoreDirs)) return;
    
    // Verificar se o arquivo existe neste diretório
    $fullPath = $baseDir . '/' . $filename;
    if (file_exists($fullPath)) {
        $results[] = $fullPath;
    }
    
    // Procurar em subdiretórios
    $subdirs = glob($baseDir . '/*', GLOB_ONLYDIR);
    foreach ($subdirs as $dir) {
        find_file_recursively($dir, $filename, $results, $depth + 1, $maxDepth);
    }
}

// Função para criar caminho relativo entre dois caminhos
function path_make_relative($from, $to) {
    // Normalizar caminhos
    $from = rtrim(str_replace('\\', '/', $from), '/');
    $to = str_replace('\\', '/', $to);
    
    // Se o arquivo está no mesmo diretório, retornar apenas o nome
    if (dirname($to) === $from) {
        return basename($to);
    }
    
    // Se $to já parece ser um caminho relativo, retorná-lo como está
    if (strpos($to, $from) !== 0) {
        // Já é relativo ou em uma árvore diferente
        
        // Dividir caminhos em componentes
        $fromParts = explode('/', $from);
        $toParts = explode('/', $to);
        
        // Encontrar componentes comuns
        $commonParts = 0;
        $length = min(count($fromParts), count($toParts));
        
        for ($i = 0; $i < $length; $i++) {
            if ($fromParts[$i] === $toParts[$i]) {
                $commonParts++;
            } else {
                break;
            }
        }
        
        // Construir caminho relativo
        $upLevels = count($fromParts) - $commonParts;
        $newParts = array_fill(0, $upLevels, '..');
        
        // Adicionar componentes restantes do caminho de destino
        $newParts = array_merge($newParts, array_slice($toParts, $commonParts));
        
        return implode('/', $newParts);
    }
    
    // Converter caminho absoluto para relativo
    $rel = substr($to, strlen($from) + 1);
    return $rel;
}

// Função para garantir que toda saída seja JSON válido
function ensure_json_response() {
    // Capturar qualquer saída de buffer
    $output = ob_get_clean();
    
    // Se um erro ocorreu, enviar resposta de erro
    if ($GLOBALS['error_occurred']) {
        $response = [
            'success' => false,
            'message' => 'Erro PHP: ' . $GLOBALS['error_message'],
            'error_details' => [
                'file' => $GLOBALS['error_file'],
                'line' => $GLOBALS['error_line']
            ]
        ];
        echo json_encode($response);
        exit;
    }
    
    // Se tiver alguma saída não JSON, isso é um erro
    if (!empty($output) && substr($output, 0, 1) !== '{' && substr($output, 0, 1) !== '[') {
        $response = [
            'success' => false,
            'message' => 'Resposta inválida do servidor',
            'debug_output' => $output
        ];
        echo json_encode($response);
        exit;
    }
    
    // Se tudo estiver ok, apenas exibe a saída
    echo $output;
}

// Registrar função para ser chamada quando o script terminar
register_shutdown_function('ensure_json_response');

// Processar a requisição
try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Para teste de AutoFix
        if (isset($_POST['test_autofix']) && $_POST['test_autofix'] === 'true') {
            $path = $_POST['path'];
            $sourceFile = $_POST['source_file'];
            
            error_log("========== TESTE DE AUTOFIX INICIADO ==========");
            error_log("Testando AutoFix para: $path no arquivo $sourceFile");
            
            // PROTEÇÃO: Validação de entrada
            if (empty($path) || empty($sourceFile) || !file_exists($sourceFile)) {
                $errorMessage = empty($path) ? "Caminho vazio" : 
                              (!file_exists($sourceFile) ? "Arquivo fonte não existe: $sourceFile" : "Erro desconhecido");
                
                error_log("Erro de validação: $errorMessage");
                echo json_encode([
                    'success' => false,
                    'message' => $errorMessage,
                    'debug_info' => [
                        'path' => $path,
                        'source_file' => $sourceFile
                    ]
                ]);
                exit;
            }
            
            // Testar todos os algoritmos de busca
            $alternativePaths = [];
            
            // 1. Busca principal usando find_alternative_path
            $mainAlternative = find_alternative_path($path, $sourceFile);
            if (!empty($mainAlternative)) {
                $alternativePaths['main'] = [
                    'path' => $mainAlternative,
                    'exists' => check_file_exists($mainAlternative, $sourceFile),
                    'method' => 'find_alternative_path()'
                ];
            }
            
            // 2. Testar apenas com o nome do arquivo em vários diretórios comuns
            $filename = basename($path);
            $commonDirs = ['', 'images/', 'img/', 'css/', 'js/', 'scripts/', 'assets/'];
            
            foreach ($commonDirs as $index => $dir) {
                $testPath = $dir . $filename;
                $exists = check_file_exists($testPath, $sourceFile);
                
                if ($exists) {
                    $alternativePaths["common_$index"] = [
                        'path' => $testPath,
                        'exists' => true,
                        'method' => "Diretório comum: '$dir'"
                    ];
                }
            }
            
            // 3. Testar mantendo a estrutura parcial
            $dirPath = dirname($path);
            if ($dirPath !== '.' && $dirPath !== '/') {
                $dirParts = explode('/', $dirPath);
                $lastDir = end($dirParts);
                
                $testPath = $lastDir . '/' . $filename;
                $exists = check_file_exists($testPath, $sourceFile);
                
                if ($exists) {
                    $alternativePaths['structure'] = [
                        'path' => $testPath,
                        'exists' => true,
                        'method' => "Preservando última pasta: '$lastDir'"
                    ];
                }
            }
            
            // Determinar o melhor caminho entre as alternativas
            $bestPath = null;
            $bestMethod = null;
            
            foreach ($alternativePaths as $key => $info) {
                if ($info['exists']) {
                    if ($bestPath === null || strlen($info['path']) < strlen($bestPath)) {
                        $bestPath = $info['path'];
                        $bestMethod = $info['method'];
                    }
                }
            }
            
            // Verificação final: se não encontramos nada, usar o nome do arquivo
            if (empty($bestPath)) {
                $bestPath = $filename;
                $bestMethod = "Nome do arquivo (último recurso)";
                $alternativePaths['fallback'] = [
                    'path' => $bestPath,
                    'exists' => check_file_exists($bestPath, $sourceFile),
                    'method' => $bestMethod
                ];
            }
            
            // Agora, testar a substituição com o melhor caminho
            $content = file_get_contents($sourceFile);
            if ($content === false) {
                echo json_encode([
                    'success' => false,
                    'message' => "Não foi possível ler o arquivo: $sourceFile",
                    'debug_info' => [
                        'error' => error_get_last(),
                        'source_file' => $sourceFile
                    ]
                ]);
                exit;
            }
            
            // Escapar caracteres especiais
            $escapedOldPath = preg_quote($path, '/');
            
            // Testar os padrões
            $patterns = [
                '/href=(["\'])' . $escapedOldPath . '(["\'])/i',
                '/src=(["\'])' . $escapedOldPath . '(["\'])/i',
                '/url\s*:\s*(["\'])' . $escapedOldPath . '(["\'])/i',
                '/include(?:_once)?\s*\(\s*(["\'])' . $escapedOldPath . '(["\'])\s*\)/i',
                '/require(?:_once)?\s*\(\s*(["\'])' . $escapedOldPath . '(["\'])\s*\)/i',
                '/url\(\s*(["\']?)' . $escapedOldPath . '(["\']?)\s*\)/i',
                '/(["\'])' . $escapedOldPath . '(["\'])/i'
            ];
            
            // Contar substituições possíveis
            $replacementCount = 0;
            $debugPatterns = [];
            
            foreach ($patterns as $i => $pattern) {
                preg_match_all($pattern, $content, $matches);
                $count = count($matches[0]);
                $replacementCount += $count;
                
                if (!empty($matches[0])) {
                    $debugPatterns["pattern_$i"] = [
                        'regex' => $pattern,
                        'count' => $count,
                        'matches' => array_map('htmlspecialchars', array_slice($matches[0], 0, 5)) // Mostrar até 5 exemplos
                    ];
                }
            }
            
            // Verificar se a substituição resultaria em caminhos vazios
            $testContent = $content;
            $replacements = [
                'href=$1' . $bestPath . '$2',
                'src=$1' . $bestPath . '$2',
                'url: $1' . $bestPath . '$2',
                'include$1($1' . $bestPath . '$2)',
                'require$1($1' . $bestPath . '$2)',
                'url($1' . $bestPath . '$2)',
                '$1' . $bestPath . '$2'
            ];
            
            // Aplicar substituições para teste
            foreach ($patterns as $i => $pattern) {
                $testContent = preg_replace($pattern, $replacements[$i], $testContent);
            }
            
            // Verificar por problemas
            $hasEmptyPaths = (strpos($testContent, '=""') !== false || 
                               strpos($testContent, "=''") !== false || 
                               strpos($testContent, 'url()') !== false);
            
            // Retornar resultados
            echo json_encode([
                'success' => true,
                'new_path' => $bestPath,
                'exists' => check_file_exists($bestPath, $sourceFile),
                'replacements' => $replacementCount,
                'has_empty_paths' => $hasEmptyPaths,
                'method_used' => $bestMethod,
                'alternative_paths' => $alternativePaths,
                'debug_info' => [
                    'patterns_found' => $debugPatterns,
                    'source_file' => $sourceFile,
                    'old_path' => $path,
                    'dirname_source' => dirname($sourceFile),
                    'basename_source' => basename($sourceFile),
                    'filename_only' => $filename
                ]
            ]);
            exit;
        }
        
        // Para processamento em lote
        if (isset($_POST['autofix']) && $_POST['autofix'] === 'true') {
            $items = json_decode($_POST['items'], true);
            $returnUrl = isset($_POST['return_url']) ? $_POST['return_url'] : '';
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                $response = [
                    'success' => false,
                    'message' => 'Erro ao decodificar JSON: ' . json_last_error_msg()
                ];
                echo json_encode($response);
                exit;
            }
            
            $results = [];
            $totalFixed = 0;
            
            foreach ($items as $item) {
                $sourceFile = $item['source_file'];
                $oldPath = $item['path'];
                
                // IMPORTANTE: Evitar processar caminhos vazios
                if (empty($oldPath)) {
                    $results[] = [
                        'success' => false,
                        'message' => "Caminho vazio ignorado",
                        'old_path' => $oldPath,
                        'new_path' => null
                    ];
                    continue;
                }
                
                // Registrar no log
                error_log("Tentando corrigir: $oldPath no arquivo $sourceFile");
                
                // FASE 1: Encontrar o melhor caminho alternativo
                
                // Armazenar todas as alternativas encontradas
                $alternativePaths = [];
                
                // 1. Busca principal usando find_alternative_path
                $mainAlternative = find_alternative_path($oldPath, $sourceFile);
                if (!empty($mainAlternative)) {
                    $alternativePaths['main'] = [
                        'path' => $mainAlternative,
                        'exists' => check_file_exists($mainAlternative, $sourceFile),
                        'method' => 'find_alternative_path()'
                    ];
                }
                
                // 2. Testar apenas com o nome do arquivo em vários diretórios comuns
                $filename = basename($oldPath);
                $commonDirs = ['', 'images/', 'img/', 'css/', 'js/', 'scripts/', 'assets/'];
                
                foreach ($commonDirs as $index => $dir) {
                    $testPath = $dir . $filename;
                    $exists = check_file_exists($testPath, $sourceFile);
                    
                    if ($exists) {
                        $alternativePaths["common_$index"] = [
                            'path' => $testPath,
                            'exists' => true,
                            'method' => "Diretório comum: '$dir'"
                        ];
                    }
                }
                
                // 3. Testar mantendo a estrutura parcial
                $dirPath = dirname($oldPath);
                if ($dirPath !== '.' && $dirPath !== '/') {
                    $dirParts = explode('/', $dirPath);
                    $lastDir = end($dirParts);
                    
                    $testPath = $lastDir . '/' . $filename;
                    $exists = check_file_exists($testPath, $sourceFile);
                    
                    if ($exists) {
                        $alternativePaths['structure'] = [
                            'path' => $testPath,
                            'exists' => true,
                            'method' => "Preservando última pasta: '$lastDir'"
                        ];
                    }
                }
                
                // Determinar o melhor caminho entre as alternativas
                $newPath = null;
                $method = null;
                
                foreach ($alternativePaths as $key => $info) {
                    if ($info['exists']) {
                        if ($newPath === null || strlen($info['path']) < strlen($newPath)) {
                            $newPath = $info['path'];
                            $method = $info['method'];
                        }
                    }
                }
                
                // Verificação final: se não encontramos nada, usar o nome do arquivo
                if (empty($newPath)) {
                    $newPath = $filename;
                    $method = "Nome do arquivo (último recurso)";
                    error_log("Último recurso: usando apenas o nome do arquivo: $newPath");
                }
                
                // FASE 2: Substituir o caminho antigo pelo novo no arquivo
                
                // Verificação final
                if (empty($newPath)) {
                    $results[] = [
                        'success' => false,
                        'message' => "AutoFix não encontrou um caminho alternativo válido para: $oldPath",
                        'old_path' => $oldPath,
                        'new_path' => null
                    ];
                    error_log("PROTEÇÃO: Não foi possível encontrar um caminho alternativo para $oldPath");
                    continue;
                }
                
                error_log("Novo caminho encontrado: $newPath (método: $method)");
                
                // Lê o conteúdo do arquivo
                $content = file_get_contents($sourceFile);
                if ($content === false) {
                    $results[] = [
                        'success' => false,
                        'message' => "Não foi possível ler o arquivo: $sourceFile",
                        'old_path' => $oldPath,
                        'new_path' => null
                    ];
                    continue;
                }
                
                // Escapamos caracteres especiais para uso em regex
                $escapedOldPath = preg_quote($oldPath, '/');
                
                // Padrões que podem conter caminhos de arquivos
                $patterns = [
                    // href em tags (HTML e CSS)
                    '/href=(["\'])' . $escapedOldPath . '(["\'])/i',
                    
                    // src em tags (HTML)
                    '/src=(["\'])' . $escapedOldPath . '(["\'])/i',
                    
                    // url em JavaScript
                    '/url\s*:\s*(["\'])' . $escapedOldPath . '(["\'])/i',
                    
                    // includes e requires em PHP
                    '/include(?:_once)?\s*\(\s*(["\'])' . $escapedOldPath . '(["\'])\s*\)/i',
                    '/require(?:_once)?\s*\(\s*(["\'])' . $escapedOldPath . '(["\'])\s*\)/i',
                    
                    // url() em CSS
                    '/url\(\s*(["\']?)' . $escapedOldPath . '(["\']?)\s*\)/i',
                    
                    // Caminho como string literal
                    '/(["\'])' . $escapedOldPath . '(["\'])/i'
                ];
                
                $replacements = [
                    'href=$1' . $newPath . '$2',
                    'src=$1' . $newPath . '$2',
                    'url: $1' . $newPath . '$2',
                    'include$1($1' . $newPath . '$2)',
                    'require$1($1' . $newPath . '$2)',
                    'url($1' . $newPath . '$2)',
                    '$1' . $newPath . '$2'
                ];
                
                // Aplicamos as substituições
                $newContent = $content;
                $replacementCount = 0;
                
                foreach ($patterns as $i => $pattern) {
                    $tempContent = preg_replace($pattern, $replacements[$i], $newContent, -1, $count);
                    if ($count > 0) {
                        $newContent = $tempContent;
                        $replacementCount += $count;
                        error_log("Padrão $i substituiu $count ocorrências");
                    }
                }
                
                // Se houve substituições, verificamos novamente se o novo conteúdo não removeu o caminho
                if ($replacementCount > 0) {
                    // PROTEÇÃO: Verificar se algum caminho foi substituído por string vazia
                    if (strpos($newContent, '=""') !== false || 
                        strpos($newContent, "=''") !== false || 
                        strpos($newContent, 'url()') !== false) {
                        
                        error_log("ALERTA: Detectadas substituições que resultam em caminhos vazios!");
                        $results[] = [
                            'success' => false,
                            'message' => "Proteção: Substituição resultaria em caminhos vazios para: $oldPath",
                            'old_path' => $oldPath,
                            'new_path' => $newPath
                        ];
                        continue;
                    }
                    
                    if (file_put_contents($sourceFile, $newContent) !== false) {
                        $totalFixed++;
                        $results[] = [
                            'success' => true,
                            'message' => "Path atualizado com sucesso! ($replacementCount substituições, método: $method)",
                            'old_path' => $oldPath,
                            'new_path' => $newPath,
                            'exists' => check_file_exists($newPath, $sourceFile),
                            'method' => $method
                        ];
                        
                        // Atualiza sessão se necessário
                        if (isset($_SESSION['path_checker_results'])) {
                            $sessionResults = $_SESSION['path_checker_results'];
                            foreach ($sessionResults as $key => $result) {
                                if ($result['source_file'] === $sourceFile && $result['path'] === $oldPath) {
                                    $sessionResults[$key]['path'] = $newPath;
                                    $sessionResults[$key]['exists'] = check_file_exists($newPath, $sourceFile);
                                }
                            }
                            $_SESSION['path_checker_results'] = $sessionResults;
                        }
                    } else {
                        $results[] = [
                            'success' => false,
                            'message' => "Erro ao atualizar o arquivo: $sourceFile",
                            'old_path' => $oldPath,
                            'new_path' => $newPath
                        ];
                    }
                } else {
                    $results[] = [
                        'success' => false,
                        'message' => "Não foi possível encontrar o padrão para substituir: $oldPath",
                        'old_path' => $oldPath,
                        'new_path' => $newPath
                    ];
                    error_log("Nenhuma substituição feita para $oldPath -> $newPath");
                }
            }
            
            $response = [
                'success' => $totalFixed > 0,
                'message' => $totalFixed > 0 ? "Foram consertados $totalFixed de " . count($items) . " caminhos." : "Não foi possível consertar nenhum caminho.",
                'results' => $results,
                'return_url' => $returnUrl
            ];
            
            echo json_encode($response);
            exit;
        }
        
        // Processamento individual
        $sourceFile = $_POST['source_file'];
        $oldPath = $_POST['old_path'];
        $newPath = $_POST['new_path'];
        $returnUrl = isset($_POST['return_url']) ? $_POST['return_url'] : '';

        $response = [
            'success' => false,
            'message' => '',
            'exists' => false,
            'return_url' => $returnUrl
        ];

        // Validação básica
        if (empty($sourceFile) || empty($oldPath)) {
            $response['message'] = 'Parâmetros inválidos: caminho original ou arquivo fonte não informados';
            echo json_encode($response);
            exit;
        }

        // PROTEÇÃO: Verificar se o novo caminho está vazio
        if (empty($newPath)) {
            // Tenta gerar automaticamente um novo caminho
            $newPath = find_alternative_path($oldPath, $sourceFile);
            
            if (empty($newPath)) {
                // Usar apenas o nome do arquivo como fallback
                $newPath = basename($oldPath);
                error_log("Gerado caminho básico: $newPath");
            }
            
            // Se ainda estiver vazio, aborta
            if (empty($newPath)) {
                $response['message'] = 'Erro: Não foi possível determinar um novo caminho válido';
                echo json_encode($response);
                exit;
            }
        }

        error_log("Atualizando manualmente: $oldPath -> $newPath no arquivo $sourceFile");

        // Lê o conteúdo do arquivo
        $content = file_get_contents($sourceFile);
        if ($content === false) {
            $response['message'] = 'Não foi possível ler o arquivo fonte';
            echo json_encode($response);
            exit;
        }

        // Escapa caracteres especiais para uso em regex
        $escapedOldPath = preg_quote($oldPath, '/');

        // Padrões que podem conter caminhos de arquivos
        $patterns = [
            // href em tags (HTML e CSS)
            '/href=(["\'])' . $escapedOldPath . '(["\'])/i',
            
            // src em tags (HTML)
            '/src=(["\'])' . $escapedOldPath . '(["\'])/i',
            
            // url em JavaScript
            '/url\s*:\s*(["\'])' . $escapedOldPath . '(["\'])/i',
            
            // includes e requires em PHP
            '/include(?:_once)?\s*\(\s*(["\'])' . $escapedOldPath . '(["\'])\s*\)/i',
            '/require(?:_once)?\s*\(\s*(["\'])' . $escapedOldPath . '(["\'])\s*\)/i',
            
            // url() em CSS
            '/url\(\s*(["\']?)' . $escapedOldPath . '(["\']?)\s*\)/i',
            
            // Caminho como string literal
            '/(["\'])' . $escapedOldPath . '(["\'])/i'
        ];

        $replacements = [
            'href=$1' . $newPath . '$2',
            'src=$1' . $newPath . '$2',
            'url: $1' . $newPath . '$2',
            'include$1($1' . $newPath . '$2)',
            'require$1($1' . $newPath . '$2)',
            'url($1' . $newPath . '$2)',
            '$1' . $newPath . '$2'
        ];

        // Aplicamos as substituições
        $newContent = $content;
        $replacementCount = 0;

        foreach ($patterns as $i => $pattern) {
            $tempContent = preg_replace($pattern, $replacements[$i], $newContent, -1, $count);
            if ($count > 0) {
                $newContent = $tempContent;
                $replacementCount += $count;
                error_log("Padrão $i substituiu $count ocorrências");
            }
        }

        // PROTEÇÃO: Verificar se não houve substituições que resultaram em caminhos vazios
        if (strpos($newContent, '=""') !== false || 
            strpos($newContent, "=''") !== false || 
            strpos($newContent, 'url()') !== false) {
            
            error_log("ALERTA: Substituições resultaram em caminhos vazios!");
            $response['message'] = "Erro: O processamento resultaria em caminhos vazios";
            echo json_encode($response);
            exit;
        }

        // Se houve substituições, salvamos o arquivo
        if ($replacementCount > 0) {
            if (file_put_contents($sourceFile, $newContent) !== false) {
                $response['success'] = true;
                $response['message'] = "Path atualizado com sucesso! ($replacementCount substituições)";
                
                // Verifica se o novo path existe
                $response['exists'] = check_file_exists($newPath, $sourceFile);
                
                // Se houver resultados na sessão, vamos atualizar também
                if (isset($_SESSION['path_checker_results'])) {
                    $results = $_SESSION['path_checker_results'];
                    foreach ($results as $key => $result) {
                        if ($result['source_file'] === $sourceFile && $result['path'] === $oldPath) {
                            $results[$key]['path'] = $newPath;
                            $results[$key]['exists'] = $response['exists'];
                        }
                    }
                    $_SESSION['path_checker_results'] = $results;
                }
            } else {
                $response['message'] = "Erro ao atualizar o arquivo";
            }
        } else {
            // Se não encontramos nenhum padrão para substituir, tentamos uma abordagem mais simples
            $fallbackPattern = '/(["\'])' . $escapedOldPath . '(["\'])/';
            $fallbackReplacement = '$1' . $newPath . '$2';
            $newContent = preg_replace($fallbackPattern, $fallbackReplacement, $content, -1, $count);
            
            if ($count > 0) {
                // Verificação de segurança
                if (strpos($newContent, '=""') === false && 
                    strpos($newContent, "=''") === false && 
                    strpos($newContent, 'url()') === false) {
                    
                    if (file_put_contents($sourceFile, $newContent) !== false) {
                        $response['success'] = true;
                        $response['message'] = "Path atualizado com sucesso (fallback)!";
                        $response['exists'] = check_file_exists($newPath, $sourceFile);
                        
                        // Atualiza sessão
                        if (isset($_SESSION['path_checker_results'])) {
                            $results = $_SESSION['path_checker_results'];
                            foreach ($results as $key => $result) {
                                if ($result['source_file'] === $sourceFile && $result['path'] === $oldPath) {
                                    $results[$key]['path'] = $newPath;
                                    $results[$key]['exists'] = $response['exists'];
                                }
                            }
                            $_SESSION['path_checker_results'] = $results;
                        }
                    } else {
                        $response['message'] = "Erro ao atualizar o arquivo";
                    }
                } else {
                    $response['message'] = "Erro: A substituição resultaria em caminhos vazios";
                }
            } else {
                $response['message'] = "Não foi possível encontrar o padrão para substituir";
                error_log("Nenhum padrão encontrado para substituir $oldPath -> $newPath no arquivo $sourceFile");
            }
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
} catch (Exception $e) {
    // Capturar qualquer exceção
    $response = [
        'success' => false,
        'message' => 'Exceção: ' . $e->getMessage(),
        'error_details' => [
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]
    ];
    echo json_encode($response);
    exit;
}
?> 