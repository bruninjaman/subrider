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
    
    // Verifica se o caminho contém código PHP
    if (strpos($path, '<?php') !== false || strpos($path, '<?=') !== false) {
        error_log("Caminho contém código PHP, extraindo parte estática");
        
        // Extrair parte estática do caminho (após o código PHP)
        preg_match('/(?:\?>|;)(.+)$/', $path, $matches);
        if (!empty($matches[1])) {
            $staticPart = trim($matches[1]);
            error_log("Parte estática extraída: $staticPart");
            $path = $staticPart;
        } else {
            // Tenta extrair a parte depois de uma variável PHP
            preg_match('/.*?(?:;|\?>)\s*\/(.+)$/', $path, $matches);
            if (!empty($matches[1])) {
                error_log("Parte após variável PHP: $matches[1]");
                $path = $matches[1];
            } else {
                error_log("Não foi possível extrair parte estática do caminho PHP");
                return false;
            }
        }
    }
    
    // Obter caminho absoluto do arquivo fonte
    $sourceDir = dirname(realpath($sourceFile));
    if (!$sourceDir) {
        $sourceDir = dirname($sourceFile);
    }
    
    // Verificar se o arquivo fonte está sendo incluído por outro script
    // Analisar o conteúdo do arquivo para verificar se ele é incluído
    $isIncluded = false;
    $includingScripts = find_including_scripts($sourceFile);
    $alternativePaths = [];
    
    if (!empty($includingScripts)) {
        error_log("O arquivo $sourceFile é incluído por outros scripts: " . implode(", ", $includingScripts));
        $isIncluded = true;
        
        // Tentar resolver caminhos em relação ao script que faz a inclusão
        foreach ($includingScripts as $index => $includingScript) {
            $includingDir = dirname(realpath($includingScript));
            
            // Tentar com caminho relativo do inclusor
            if (strpos($path, '../') === 0) {
                $adjustedBase = adjust_base_for_relative_path($includingDir, $path);
                $pathWithoutDots = preg_replace('/^(\.\.\/)+/', '', $path);
                $possiblePath = $adjustedBase . '/' . $pathWithoutDots;
                $exists = file_exists($possiblePath);
                
                if ($exists) {
                    $relativePath = path_make_relative($sourceDir, $possiblePath);
                    $alternativePaths["inclusor_adjusted_$index"] = [
                        'path' => $relativePath,
                        'exists' => true,
                        'method' => "Caminho ajustado do inclusor: " . basename($includingScript)
                    ];
                }
            }
            
            // Tentar com caminho direto do inclusor
            $directPath = $includingDir . '/' . $path;
            $exists = file_exists($directPath);
            
            if ($exists) {
                $relativePath = path_make_relative($sourceDir, $directPath);
                $alternativePaths["inclusor_direct_$index"] = [
                    'path' => $relativePath,
                    'exists' => true,
                    'method' => "Direto do inclusor: " . basename($includingScript)
                ];
            }
            
            // Tentar padrões comuns de inclusão
            $filenameToTest = basename($path);
            $commonIncludePaths = [
                './assets/' . $filenameToTest,
                '../assets/' . $filenameToTest,
                './css/' . $filenameToTest,
                '../css/' . $filenameToTest,
                './js/' . $filenameToTest,
                '../js/' . $filenameToTest
            ];
            
            foreach ($commonIncludePaths as $idx => $includePath) {
                $fullPath = $includingDir . '/' . ltrim($includePath, './');
                $exists = file_exists($fullPath);
                
                if ($exists) {
                    $alternativePaths["inclusor_common_{$index}_{$idx}"] = [
                        'path' => $includePath,
                        'exists' => true,
                        'method' => "Padrão de inclusão: $includePath"
                    ];
                }
            }
        }
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
    
    // 5. Se o arquivo é incluído, tentar buscar na raiz do projeto
    if ($isIncluded) {
        $projectRoot = realpath(dirname(__DIR__));
        $rootPaths = [
            $projectRoot . '/' . $path,
            $projectRoot . '/assets/' . $filename,
            $projectRoot . '/css/' . $filename,
            $projectRoot . '/js/' . $filename,
            $projectRoot . '/includes/' . $filename,
        ];
        
        foreach ($rootPaths as $rootPath) {
            error_log("Tentativa 5 - Raiz do projeto: $rootPath");
            if (file_exists($rootPath)) {
                error_log("Arquivo encontrado na raiz do projeto: $rootPath");
                return true;
            }
        }
    }
    
    error_log("Arquivo não encontrado em nenhuma tentativa");
    return false;
}

// Função para encontrar scripts que incluem o arquivo fonte
function find_including_scripts($sourceFile) {
    $result = [];
    $projectRoot = realpath(dirname(__DIR__));
    
    // Encontrar todos os arquivos PHP no projeto
    $phpFiles = [];
    find_php_files($projectRoot, $phpFiles);
    
    $sourceBasename = basename($sourceFile);
    $includePatterns = [
        '/include\s*\(\s*[\'"]([^\'"]*' . preg_quote($sourceBasename, '/') . ')[\'"]/',
        '/include_once\s*\(\s*[\'"]([^\'"]*' . preg_quote($sourceBasename, '/') . ')[\'"]/',
        '/require\s*\(\s*[\'"]([^\'"]*' . preg_quote($sourceBasename, '/') . ')[\'"]/',
        '/require_once\s*\(\s*[\'"]([^\'"]*' . preg_quote($sourceBasename, '/') . ')[\'"]/'
    ];
    
    foreach ($phpFiles as $phpFile) {
        if ($phpFile === $sourceFile) continue;
        
        $content = file_get_contents($phpFile);
        if ($content === false) continue;
        
        foreach ($includePatterns as $pattern) {
            if (preg_match($pattern, $content)) {
                $result[] = $phpFile;
                break;
            }
        }
    }
    
    return $result;
}

// Função auxiliar para encontrar todos os arquivos PHP em um diretório
function find_php_files($dir, &$result) {
    // Ignorar diretórios específicos
    $ignoreDirs = ['vendor', 'node_modules', '.git', 'bower_components', 'cache', 'dist'];
    $baseName = basename($dir);
    
    if (in_array($baseName, $ignoreDirs)) {
        return;
    }
    
    // Encontrar arquivos PHP
    $phpFilesInDir = glob($dir . '/*.php');
    foreach ($phpFilesInDir as $file) {
        $result[] = $file;
    }
    
    // Buscar em subdiretórios
    $subdirs = glob($dir . '/*', GLOB_ONLYDIR);
    foreach ($subdirs as $subdir) {
        find_php_files($subdir, $result);
    }
}

// Função para resolver um caminho relativo a partir de uma base
function resolve_path_from_base($base, $relativePath) {
    // Normalizar caminhos
    $base = rtrim(str_replace('\\', '/', $base), '/');
    $relativePath = str_replace('\\', '/', $relativePath);
    
    // Se o caminho não é relativo, retornar direto
    if (strpos($relativePath, './') !== 0 && strpos($relativePath, '../') !== 0) {
        return $base . '/' . $relativePath;
    }
    
    // Dividir o caminho relativo
    $parts = explode('/', $relativePath);
    $baseParts = explode('/', $base);
    
    $resultParts = $baseParts;
    
    foreach ($parts as $part) {
        if ($part === '.') {
            continue; // Ficar no mesmo diretório
        } elseif ($part === '..') {
            array_pop($resultParts); // Subir um nível
        } else {
            $resultParts[] = $part; // Adicionar ao caminho
        }
    }
    
    return implode('/', $resultParts);
}

// Função para ajustar a base com base no número de ../ no caminho
function adjust_base_for_relative_path($base, $path) {
    if (strpos($path, '../') !== 0) {
        return $base;
    }
    
    // Contar quantos níveis subir
    $upLevels = 0;
    $pathParts = explode('/', $path);
    
    foreach ($pathParts as $part) {
        if ($part === '..') {
            $upLevels++;
        } else {
            break;
        }
    }
    
    // Subir os níveis necessários
    $baseParts = explode('/', $base);
    for ($i = 0; $i < $upLevels; $i++) {
        array_pop($baseParts);
    }
    
    return implode('/', $baseParts);
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
    
    // Verifica se o caminho contém código PHP
    $hasPhpCode = false;
    $staticPath = $oldPath;
    
    if (strpos($oldPath, '<?php') !== false || strpos($oldPath, '<?=') !== false) {
        $hasPhpCode = true;
        error_log("Caminho contém código PHP, extraindo parte estática");
        
        // Extrair parte estática do caminho (após o código PHP)
        preg_match('/(?:\?>|;)\s*\/(.+)$/', $oldPath, $matches);
        if (!empty($matches[1])) {
            $staticPath = $matches[1];
            error_log("Parte estática extraída: $staticPath");
        } else {
            // Tenta extrair o último segmento do caminho (após a última /)
            $parts = explode('/', $oldPath);
            $staticPath = end($parts);
            error_log("Último segmento do caminho: $staticPath");
        }
    }
    
    // Obter diretório absoluto do arquivo fonte
    $sourceDir = dirname(realpath($sourceFile));
    
    // Extrair o nome do arquivo do caminho antigo
    $filename = basename($staticPath);
    error_log("Nome do arquivo a procurar: $filename");
    
    // Raiz do projeto
    $projectRoot = realpath(dirname(__DIR__));
    
    // Verificar se o arquivo fonte está sendo incluído por outro script
    $isIncluded = false;
    $includingScripts = find_including_scripts($sourceFile);
    
    if (!empty($includingScripts)) {
        error_log("O arquivo $sourceFile é incluído por outros scripts: " . implode(", ", $includingScripts));
        $isIncluded = true;
        
        // ABORDAGEM 0: Primeiro tentar resolver em relação ao script inclusor
        foreach ($includingScripts as $includingScript) {
            $includingDir = dirname(realpath($includingScript));
            error_log("Verificando a partir do script inclusor: $includingScript");
            
            // Se o caminho começa com ../ remove os níveis necessários
            if (strpos($staticPath, '../') === 0) {
                $adjustedBase = adjust_base_for_relative_path($includingDir, $staticPath);
                $pathWithoutDots = preg_replace('/^(\.\.\/)+/', '', $staticPath);
                $possiblePath = $adjustedBase . '/' . $pathWithoutDots;
                error_log("Tentando caminho ajustado do inclusor: $possiblePath");
                
                if (file_exists($possiblePath)) {
                    // Construir o caminho relativo adequado para o inclusor
                    $relativePath = path_make_relative($sourceDir, $possiblePath);
                    error_log("SUCESSO! Caminho encontrado a partir do script inclusor (ajustado): $relativePath");
                    return $relativePath;
                }
            }
            
            // Tentar direto relativo ao inclusor
            $directPath = $includingDir . '/' . $staticPath;
            error_log("Tentando direto do inclusor: $directPath");
            
            if (file_exists($directPath)) {
                $relativePath = path_make_relative($sourceDir, $directPath);
                error_log("SUCESSO! Caminho encontrado a partir do script inclusor: $relativePath");
                return $relativePath;
            }
            
            // Tentar com ./
            $dotPath = $includingDir . '/' . ltrim($staticPath, './');
            if ($dotPath !== $directPath) {
                error_log("Tentando com ./ removido: $dotPath");
                if (file_exists($dotPath)) {
                    $relativePath = path_make_relative($sourceDir, $dotPath);
                    error_log("SUCESSO! Caminho encontrado com ./ removido: $relativePath");
                    return $relativePath;
                }
            }
            
            // Tentar com ./assets no inclusor
            $commonDirs = ['', 'assets', 'css', 'js', 'img', 'images'];
            foreach ($commonDirs as $dir) {
                $includingCommonPath = $includingDir . '/' . ($dir ? "$dir/" : '') . $filename;
                error_log("Tentando com diretório comum a partir do inclusor: $includingCommonPath");
                
                if (file_exists($includingCommonPath)) {
                    $relativePath = path_make_relative($sourceDir, $includingCommonPath);
                    error_log("SUCESSO! Caminho encontrado em diretório comum do inclusor: $relativePath");
                    return $relativePath;
                }
            }
        }
    }
    
    // ABORDAGEM 1: Verificar se o caminho existe com a estrutura atual
    // Tenta primeiro corrigir erros simples de capitalização ou pequenas diferenças
    $oldDirPath = dirname($staticPath);
    $oldDirParts = explode('/', $oldDirPath);
    
    // Tentar manter a estrutura original intacta
    $possiblePath = $staticPath;
    $absolutePath = $sourceDir . '/' . $possiblePath;
    error_log("Tentando caminho direto: $absolutePath");
    
    if (file_exists($absolutePath)) {
        error_log("Caminho original existe! Mantendo-o: $possiblePath");
        return $possiblePath;
    }
    
    // ABORDAGEM 2: Se tiver partes PHP e um diretório, tratar com especial cuidado
    if ($hasPhpCode && $oldDirPath !== '.' && $oldDirPath !== '/') {
        // Pega todos os componentes do caminho
        $allDirParts = explode('/', $oldPath);
        
        // Identifica a última parte (que deve ser o arquivo)
        $filenamePart = array_pop($allDirParts);
        
        // Verifica se o penúltimo componente contém código PHP
        $lastDirPart = array_pop($allDirParts);
        
        // Se o penúltimo componente não contém PHP, recoloca
        if (strpos($lastDirPart, '<?php') === false && strpos($lastDirPart, '<?=') === false) {
            array_push($allDirParts, $lastDirPart);
        }
        
        // Procura por diretórios que correspondem à última parte do caminho
        $parts = explode('/', $staticPath);
        if (count($parts) > 1) {
            $lastDir = $parts[count($parts) - 2]; // Penúltimo elemento é o diretório
            
            // Tentar com estrutura completa de diretório
            if (!empty($lastDir)) {
                $searchPaths = [
                    "$lastDir/$filename",
                    "assets/$lastDir/$filename",
                    "css/$lastDir/$filename",
                    "js/$lastDir/$filename"
                ];
                
                foreach ($searchPaths as $testPath) {
                    error_log("Tentando com estrutura de diretório preservada: $testPath");
                    $fullPath = $sourceDir . '/' . $testPath;
                    
                    if (file_exists($fullPath)) {
                        error_log("SUCESSO! Caminho encontrado com estrutura preservada: $testPath");
                        return $testPath;
                    }
                }
            }
        }
    }
    
    // ABORDAGEM 3: Tentar encontrar o arquivo na mesma estrutura de diretórios
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
    
    // ABORDAGEM 4: Busca recursiva mais ampla para achar o arquivo em qualquer lugar
    error_log("Iniciando busca recursiva pelo arquivo: $filename");
    
    // Armazenar todos os caminhos encontrados
    $foundFiles = array();
    find_file_recursively($projectRoot, $filename, $foundFiles);
    
    if (!empty($foundFiles)) {
        error_log("Encontrado(s) " . count($foundFiles) . " arquivo(s) com o nome");
        
        // Se temos múltiplos arquivos encontrados, damos preferência aos que mantêm a estrutura original
        if (count($foundFiles) > 1 && !empty($oldDirParts) && count($oldDirParts) > 0) {
            $bestMatches = [];
            
            // Se temos um caminho com PHP, tentar identificar componentes da estrutura de diretórios
            if ($hasPhpCode) {
                // Extrair quaisquer partes de diretório do caminho estático
                preg_match('/([^\/]+\/)+/', $staticPath, $dirMatches);
                $structureParts = [];
                
                if (!empty($dirMatches[0])) {
                    $structureParts = explode('/', rtrim($dirMatches[0], '/'));
                }
                
                // Dar peso alto para arquivos que mantêm estrutura similar
                foreach ($foundFiles as $foundFile) {
                    $foundParts = explode('/', $foundFile);
                    $matchCount = 0;
                    
                    foreach ($structureParts as $part) {
                        if (in_array($part, $foundParts)) {
                            $matchCount++;
                        }
                    }
                    
                    if ($matchCount > 0) {
                        $bestMatches[$foundFile] = $matchCount;
                    }
                }
                
                // Ordenar por número de correspondências
                if (!empty($bestMatches)) {
                    arsort($bestMatches);
                    $bestFile = array_key_first($bestMatches);
                    $relativePath = path_make_relative($sourceDir, $bestFile);
                    error_log("Melhor correspondência estrutural: $relativePath");
                    return $relativePath;
                }
            }
            
            // Verificar por correspondências de estrutura de diretórios
            $lastDir = end($oldDirParts);
            foreach ($foundFiles as $foundFile) {
                $foundDirname = dirname($foundFile);
                $foundDirParts = explode('/', $foundDirname);
                
                // Verificar se o último diretório do caminho original está presente
                if (in_array($lastDir, $foundDirParts)) {
                    $relativePath = path_make_relative($sourceDir, $foundFile);
                    error_log("Correspondência de estrutura encontrada: $relativePath");
                    return $relativePath;
                }
            }
        }
        
        // Ordenar por comprimento, preferindo caminhos mais curtos
        usort($foundFiles, function($a, $b) {
            return strlen($a) - strlen($b);
        });
        
        // Verificar se algum dos caminhos tem 'assets' ou 'css' como componente do caminho
        $assetsMatches = array_filter($foundFiles, function($file) {
            return strpos($file, '/assets/') !== false || 
                   strpos($file, '/css/') !== false;
        });
        
        // Se encontramos caminhos com 'assets' ou 'css', usar o primeiro deles
        if (!empty($assetsMatches)) {
            $bestMatch = reset($assetsMatches);
        } else {
            // Caso contrário, usar o mais curto
            $bestMatch = $foundFiles[0];
        }
        
        $relativePath = path_make_relative($sourceDir, $bestMatch);
        
        error_log("Melhor correspondência: $relativePath");
        return $relativePath;
    }
    
    // ABORDAGEM 5: Se for um arquivo incluído, tentar caminhos específicos para inclusões
    if ($isIncluded) {
        // Estas são suposições comuns para arquivos incluídos
        $commonIncludePaths = [
            './assets/' . $filename,
            '../assets/' . $filename,
            './css/' . $filename,
            '../css/' . $filename,
            './js/' . $filename,
            '../js/' . $filename,
            './includes/' . $filename,
            '../includes/' . $filename,
        ];
        
        foreach ($commonIncludePaths as $includePath) {
            error_log("Tentando caminho comum para arquivos incluídos: $includePath");
            
            // Verificar se existe a partir do diretório fonte
            $fullPath = $sourceDir . '/' . $includePath;
            if (file_exists($fullPath)) {
                error_log("SUCESSO! Caminho de inclusão encontrado: $includePath");
                return $includePath;
            }
        }
    }
    
    // ABORDAGEM 6: Se estamos lidando com CSS, tente procurar em diretórios de assets específicos
    if (strpos($filename, '.css') !== false) {
        $cssSpecificPaths = [
            'assets/css/' . $filename,
            'css/' . $filename,
            'styles/' . $filename,
            'assets/styles/' . $filename
        ];
        
        foreach ($cssSpecificPaths as $cssPath) {
            $fullPath = $sourceDir . '/' . $cssPath;
            error_log("Tentando caminho específico para CSS: $fullPath");
            
            if (file_exists($fullPath)) {
                error_log("SUCESSO! Caminho CSS encontrado: $cssPath");
                return $cssPath;
            }
            
            // Verificar na raiz do projeto também
            $rootPath = $projectRoot . '/' . $cssPath;
            if (file_exists($rootPath)) {
                $relativePath = path_make_relative($sourceDir, $rootPath);
                error_log("SUCESSO! Caminho CSS encontrado na raiz: $relativePath");
                return $relativePath;
            }
        }
    }
    
    // ABORDAGEM 7: Semelhante para JS
    if (strpos($filename, '.js') !== false) {
        $jsSpecificPaths = [
            'assets/js/' . $filename,
            'js/' . $filename,
            'scripts/' . $filename,
            'assets/scripts/' . $filename
        ];
        
        foreach ($jsSpecificPaths as $jsPath) {
            $fullPath = $sourceDir . '/' . $jsPath;
            error_log("Tentando caminho específico para JS: $fullPath");
            
            if (file_exists($fullPath)) {
                error_log("SUCESSO! Caminho JS encontrado: $jsPath");
                return $jsPath;
            }
            
            // Verificar na raiz do projeto também
            $rootPath = $projectRoot . '/' . $jsPath;
            if (file_exists($rootPath)) {
                $relativePath = path_make_relative($sourceDir, $rootPath);
                error_log("SUCESSO! Caminho JS encontrado na raiz: $relativePath");
                return $relativePath;
            }
        }
    }
    
    // ABORDAGEM 8: Se o caminho original tem uma parte estática após PHP com uma estrutura de diretório
    if ($hasPhpCode && strpos($staticPath, '/') !== false) {
        $staticParts = explode('/', $staticPath);
        
        // Se temos mais de um segmento (diretório/arquivo)
        if (count($staticParts) > 1) {
            $lastDir = $staticParts[count($staticParts) - 2]; // Penúltima parte deve ser o diretório
            $partialPath = $lastDir . '/' . $filename;
            
            // Verificar em vários locais comuns
            $commonParentDirs = ['', 'assets', 'css', 'js', 'images', 'includes'];
            foreach ($commonParentDirs as $parentDir) {
                $testPath = ($parentDir ? "$parentDir/" : '') . $partialPath;
                $fullPath = $sourceDir . '/' . $testPath;
                
                error_log("Tentando com estrutura parcial da variável PHP: $fullPath");
                if (file_exists($fullPath)) {
                    error_log("SUCESSO! Estrutura parcial encontrada: $testPath");
                    return $testPath;
                }
                
                // Verificar na raiz do projeto também
                $rootPath = $projectRoot . '/' . $testPath;
                if (file_exists($rootPath)) {
                    $relativePath = path_make_relative($sourceDir, $rootPath);
                    error_log("SUCESSO! Estrutura parcial encontrada na raiz: $relativePath");
                    return $relativePath;
                }
            }
        }
    }
    
    // ABORDAGEM 9: Para arquivos CSS/JS em particular, verificar em locais padrão comuns
    if (strpos($filename, '.css') !== false || strpos($filename, '.js') !== false) {
        $fileExt = substr($filename, strrpos($filename, '.') + 1);
        $assetDirs = [
            "assets/$fileExt",
            $fileExt,
            "assets"
        ];
        
        foreach ($assetDirs as $dir) {
            $assetPath = "$dir/$filename";
            $fullPath = $sourceDir . '/' . $assetPath;
            
            error_log("Tentando em diretório padrão para $fileExt: $fullPath");
            if (file_exists($fullPath)) {
                error_log("SUCESSO! Encontrado em diretório padrão: $assetPath");
                return $assetPath;
            }
            
            // Verificar na raiz do projeto
            $rootPath = $projectRoot . '/' . $assetPath;
            if (file_exists($rootPath)) {
                $relativePath = path_make_relative($sourceDir, $rootPath);
                error_log("SUCESSO! Encontrado em diretório padrão na raiz: $relativePath");
                return $relativePath;
            }
        }
    }
    
    // ABORDAGEM 10: Último recurso - manter apenas o nome do arquivo
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
            
            // Verificar se o caminho contém código PHP e extrair a parte estática
            $hasPhpCode = false;
            $staticPath = $path;
            
            if (strpos($path, '<?php') !== false || strpos($path, '<?=') !== false) {
                $hasPhpCode = true;
                error_log("Caminho contém código PHP, extraindo parte estática");
                
                // Extrair parte estática do caminho (após o código PHP)
                preg_match('/(?:\?>|;)\s*\/(.+)$/', $path, $matches);
                if (!empty($matches[1])) {
                    $staticPath = $matches[1];
                    error_log("Parte estática extraída: $staticPath");
                } else {
                    // Tenta extrair o último segmento do caminho (após a última /)
                    $parts = explode('/', $path);
                    $staticPath = end($parts);
                    error_log("Último segmento do caminho: $staticPath");
                }
            }
            
            // Testar todos os algoritmos de busca
            $alternativePaths = [];
            $sourceDir = dirname(realpath($sourceFile));
            
            // Verificar se o arquivo fonte está sendo incluído por outro script
            $isIncluded = false;
            $includingScripts = find_including_scripts($sourceFile);
            
            if (!empty($includingScripts)) {
                error_log("TESTE: O arquivo $sourceFile é incluído por outros scripts: " . implode(", ", $includingScripts));
                $isIncluded = true;
                
                // Tentar resolver caminhos em relação ao script que faz a inclusão
                foreach ($includingScripts as $index => $includingScript) {
                    $includingDir = dirname(realpath($includingScript));
                    
                    // Tentar com caminho relativo do inclusor
                    if (strpos($staticPath, '../') === 0) {
                        $adjustedBase = adjust_base_for_relative_path($includingDir, $staticPath);
                        $pathWithoutDots = preg_replace('/^(\.\.\/)+/', '', $staticPath);
                        $possiblePath = $adjustedBase . '/' . $pathWithoutDots;
                        $exists = file_exists($possiblePath);
                        
                        if ($exists) {
                            $relativePath = path_make_relative($sourceDir, $possiblePath);
                            $alternativePaths["inclusor_adjusted_$index"] = [
                                'path' => $relativePath,
                                'exists' => true,
                                'method' => "Caminho ajustado do inclusor: " . basename($includingScript)
                            ];
                        }
                    }
                    
                    // Tentar com caminho direto do inclusor
                    $directPath = $includingDir . '/' . $staticPath;
                    $exists = file_exists($directPath);
                    
                    if ($exists) {
                        $relativePath = path_make_relative($sourceDir, $directPath);
                        $alternativePaths["inclusor_direct_$index"] = [
                            'path' => $relativePath,
                            'exists' => true,
                            'method' => "Direto do inclusor: " . basename($includingScript)
                        ];
                    }
                    
                    // Obter o nome do arquivo do caminho
                    $filenameToTest = basename($staticPath);
                    
                    // Tentar padrões comuns de inclusão
                    $commonIncludePaths = [
                        './assets/' . $filenameToTest,
                        '../assets/' . $filenameToTest,
                        './css/' . $filenameToTest,
                        '../css/' . $filenameToTest,
                        './js/' . $filenameToTest,
                        '../js/' . $filenameToTest
                    ];
                    
                    foreach ($commonIncludePaths as $idx => $includePath) {
                        $fullPath = $includingDir . '/' . ltrim($includePath, './');
                        $exists = file_exists($fullPath);
                        
                        if ($exists) {
                            $alternativePaths["inclusor_common_{$index}_{$idx}"] = [
                                'path' => $includePath,
                                'exists' => true,
                                'method' => "Padrão de inclusão: $includePath"
                            ];
                        }
                    }
                }
            }
            
            // 1. Busca principal usando find_alternative_path
            $mainAlternative = find_alternative_path($path, $sourceFile);
            if (!empty($mainAlternative)) {
                $alternativePaths['main'] = [
                    'path' => $mainAlternative,
                    'exists' => check_file_exists($mainAlternative, $sourceFile),
                    'method' => 'find_alternative_path()'
                ];
            }
            
            // 2. Se estamos lidando com código PHP, tentar extrair a estrutura de diretório
            if ($hasPhpCode && strpos($staticPath, '/') !== false) {
                $parts = explode('/', $staticPath);
                if (count($parts) > 1) {
                    $lastDir = $parts[count($parts) - 2]; // Penúltimo elemento é o diretório
                    
                    // Definir o nome do arquivo para uso neste bloco
                    $filenameToTest = basename($staticPath);
                    
                    if (!empty($lastDir)) {
                        $phpSpecificPaths = [
                            "$lastDir/$filenameToTest",
                            "assets/$lastDir/$filenameToTest",
                            "css/$lastDir/$filenameToTest",
                            "js/$lastDir/$filenameToTest"
                        ];
                        
                        foreach ($phpSpecificPaths as $idx => $phpPath) {
                            $fullPath = $sourceDir . '/' . $phpPath;
                            $exists = file_exists($fullPath);
                            
                            if ($exists) {
                                $alternativePaths["php_struct_$idx"] = [
                                    'path' => $phpPath,
                                    'exists' => true,
                                    'method' => "Estrutura preservada do PHP: $phpPath"
                                ];
                            }
                        }
                    }
                }
            }
            
            // 3. Testar apenas com o nome do arquivo em vários diretórios comuns
            $filename = basename($staticPath);
            $commonDirs = [
                '', 
                'images/', 
                'img/', 
                'css/', 
                'js/', 
                'scripts/', 
                'assets/',
                'assets/css/',
                'assets/js/',
                'assets/images/'
            ];
            
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
            
            // 4. Testar mantendo a estrutura parcial
            $dirPath = dirname($staticPath);
            if ($dirPath !== '.' && $dirPath !== '/') {
                $dirParts = explode('/', $dirPath);
                $lastDir = end($dirParts);
                
                if (!empty($lastDir)) {
                    $structurePaths = [
                        "$lastDir/$filename",
                        "assets/$lastDir/$filename",
                        "css/$lastDir/$filename",
                        "js/$lastDir/$filename"
                    ];
                    
                    foreach ($structurePaths as $idx => $structPath) {
                        $fullPath = $sourceDir . '/' . $structPath;
                        $exists = file_exists($fullPath);
                        
                        if ($exists) {
                            $alternativePaths["structure_$idx"] = [
                                'path' => $structPath,
                                'exists' => true,
                                'method' => "Preservando estrutura: $structPath"
                            ];
                        }
                    }
                }
            }
            
            // Determinar o melhor caminho entre as alternativas
            $bestPath = null;
            $bestMethod = null;
            
            // Primeiro, tentar priorizar caminhos que contêm 'assets/css' para arquivos CSS
            if (strpos($filename, '.css') !== false) {
                foreach ($alternativePaths as $key => $info) {
                    if ($info['exists'] && (
                        strpos($info['path'], 'assets/css/') !== false || 
                        strpos($info['path'], 'css/') !== false
                    )) {
                        $bestPath = $info['path'];
                        $bestMethod = $info['method'];
                        break;
                    }
                }
            }
            
            // Priorização similar para JS
            if (empty($bestPath) && strpos($filename, '.js') !== false) {
                foreach ($alternativePaths as $key => $info) {
                    if ($info['exists'] && (
                        strpos($info['path'], 'assets/js/') !== false || 
                        strpos($info['path'], 'js/') !== false
                    )) {
                        $bestPath = $info['path'];
                        $bestMethod = $info['method'];
                        break;
                    }
                }
            }
            
            // Se não encontramos um caminho especializado, escolher o melhor entre todos
            if (empty($bestPath)) {
                foreach ($alternativePaths as $key => $info) {
                    if ($info['exists']) {
                        if ($bestPath === null || strlen($info['path']) < strlen($bestPath)) {
                            $bestPath = $info['path'];
                            $bestMethod = $info['method'];
                        }
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
                    'static_path' => $staticPath,
                    'has_php_code' => $hasPhpCode,
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
                
                // Verificar se o caminho contém código PHP e extrair a parte estática
                $hasPhpCode = false;
                $staticPath = $oldPath;
                
                if (strpos($oldPath, '<?php') !== false || strpos($oldPath, '<?=') !== false) {
                    $hasPhpCode = true;
                    error_log("Caminho contém código PHP, extraindo parte estática");
                    
                    // Extrair parte estática do caminho (após o código PHP)
                    preg_match('/(?:\?>|;)\s*\/(.+)$/', $oldPath, $matches);
                    if (!empty($matches[1])) {
                        $staticPath = $matches[1];
                        error_log("Parte estática extraída: $staticPath");
                    } else {
                        // Tenta extrair o último segmento do caminho (após a última /)
                        $parts = explode('/', $oldPath);
                        $staticPath = end($parts);
                        error_log("Último segmento do caminho: $staticPath");
                    }
                }
                
                // FASE 1: Encontrar o melhor caminho alternativo
                $sourceDir = dirname(realpath($sourceFile));
                
                // Armazenar todas as alternativas encontradas
                $alternativePaths = [];
                
                // Verificar se o arquivo fonte está sendo incluído por outro script
                $isIncluded = false;
                $includingScripts = find_including_scripts($sourceFile);
                
                if (!empty($includingScripts)) {
                    error_log("TESTE: O arquivo $sourceFile é incluído por outros scripts: " . implode(", ", $includingScripts));
                    $isIncluded = true;
                    
                    // Tentar resolver caminhos em relação ao script que faz a inclusão
                    foreach ($includingScripts as $index => $includingScript) {
                        $includingDir = dirname(realpath($includingScript));
                        
                        // Tentar com caminho relativo do inclusor
                        if (strpos($staticPath, '../') === 0) {
                            $adjustedBase = adjust_base_for_relative_path($includingDir, $staticPath);
                            $pathWithoutDots = preg_replace('/^(\.\.\/)+/', '', $staticPath);
                            $possiblePath = $adjustedBase . '/' . $pathWithoutDots;
                            $exists = file_exists($possiblePath);
                            
                            if ($exists) {
                                $relativePath = path_make_relative($sourceDir, $possiblePath);
                                $alternativePaths["inclusor_adjusted_$index"] = [
                                    'path' => $relativePath,
                                    'exists' => true,
                                    'method' => "Caminho ajustado do inclusor: " . basename($includingScript)
                                ];
                            }
                        }
                        
                        // Tentar com caminho direto do inclusor
                        $directPath = $includingDir . '/' . $staticPath;
                        $exists = file_exists($directPath);
                        
                        if ($exists) {
                            $relativePath = path_make_relative($sourceDir, $directPath);
                            $alternativePaths["inclusor_direct_$index"] = [
                                'path' => $relativePath,
                                'exists' => true,
                                'method' => "Direto do inclusor: " . basename($includingScript)
                            ];
                        }
                        
                        // Tentar padrões comuns de inclusão
                        $filenameToTest = basename($staticPath);
                        $commonIncludePaths = [
                            './assets/' . $filenameToTest,
                            '../assets/' . $filenameToTest,
                            './css/' . $filenameToTest,
                            '../css/' . $filenameToTest,
                            './js/' . $filenameToTest,
                            '../js/' . $filenameToTest
                        ];
                        
                        foreach ($commonIncludePaths as $idx => $includePath) {
                            $fullPath = $includingDir . '/' . ltrim($includePath, './');
                            $exists = file_exists($fullPath);
                            
                            if ($exists) {
                                $alternativePaths["inclusor_common_{$index}_{$idx}"] = [
                                    'path' => $includePath,
                                    'exists' => true,
                                    'method' => "Padrão de inclusão: $includePath"
                                ];
                            }
                        }
                    }
                }
                
                // 1. Busca principal usando find_alternative_path
                $mainAlternative = find_alternative_path($oldPath, $sourceFile);
                if (!empty($mainAlternative)) {
                    $alternativePaths['main'] = [
                        'path' => $mainAlternative,
                        'exists' => check_file_exists($mainAlternative, $sourceFile),
                        'method' => 'find_alternative_path()'
                    ];
                }
                
                // 2. Se estamos lidando com código PHP, tentar extrair a estrutura de diretório
                if ($hasPhpCode && strpos($staticPath, '/') !== false) {
                    $parts = explode('/', $staticPath);
                    if (count($parts) > 1) {
                        $lastDir = $parts[count($parts) - 2]; // Penúltimo elemento é o diretório
                        
                        // Definir o nome do arquivo para uso neste bloco
                        $filenameToTest = basename($staticPath);
                        
                        if (!empty($lastDir)) {
                            $phpSpecificPaths = [
                                "$lastDir/$filenameToTest",
                                "assets/$lastDir/$filenameToTest",
                                "css/$lastDir/$filenameToTest",
                                "js/$lastDir/$filenameToTest"
                            ];
                            
                            foreach ($phpSpecificPaths as $idx => $phpPath) {
                                $fullPath = $sourceDir . '/' . $phpPath;
                                $exists = file_exists($fullPath);
                                
                                if ($exists) {
                                    $alternativePaths["php_struct_$idx"] = [
                                        'path' => $phpPath,
                                        'exists' => true,
                                        'method' => "Estrutura preservada do PHP: $phpPath"
                                    ];
                                }
                            }
                        }
                    }
                }
                
                // 3. Testar apenas com o nome do arquivo em vários diretórios comuns
                $filename = basename($staticPath);
                $commonDirs = [
                    '', 
                    'images/', 
                    'img/', 
                    'css/', 
                    'js/', 
                    'scripts/', 
                    'assets/',
                    'assets/css/',
                    'assets/js/',
                    'assets/images/'
                ];
                
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
                
                // 4. Testar mantendo a estrutura parcial
                $dirPath = dirname($staticPath);
                if ($dirPath !== '.' && $dirPath !== '/') {
                    $dirParts = explode('/', $dirPath);
                    $lastDir = end($dirParts);
                    
                    if (!empty($lastDir)) {
                        $structurePaths = [
                            "$lastDir/$filename",
                            "assets/$lastDir/$filename",
                            "css/$lastDir/$filename",
                            "js/$lastDir/$filename"
                        ];
                        
                        foreach ($structurePaths as $idx => $structPath) {
                            $fullPath = $sourceDir . '/' . $structPath;
                            $exists = file_exists($fullPath);
                            
                            if ($exists) {
                                $alternativePaths["structure_$idx"] = [
                                    'path' => $structPath,
                                    'exists' => true,
                                    'method' => "Preservando estrutura: $structPath"
                                ];
                            }
                        }
                    }
                }
                
                // Determinar o melhor caminho entre as alternativas
                $newPath = null;
                $method = null;
                
                // Primeiro, tentar priorizar caminhos que contêm 'assets/css' para arquivos CSS
                if (strpos($filename, '.css') !== false) {
                    foreach ($alternativePaths as $key => $info) {
                        if ($info['exists'] && (
                            strpos($info['path'], 'assets/css/') !== false || 
                            strpos($info['path'], 'css/') !== false
                        )) {
                            $newPath = $info['path'];
                            $method = $info['method'];
                            break;
                        }
                    }
                }
                
                // Priorização similar para JS
                if (empty($newPath) && strpos($filename, '.js') !== false) {
                    foreach ($alternativePaths as $key => $info) {
                        if ($info['exists'] && (
                            strpos($info['path'], 'assets/js/') !== false || 
                            strpos($info['path'], 'js/') !== false
                        )) {
                            $newPath = $info['path'];
                            $method = $info['method'];
                            break;
                        }
                    }
                }
                
                // Se não encontramos um caminho especializado, escolher o melhor entre todos
                if (empty($newPath)) {
                    foreach ($alternativePaths as $key => $info) {
                        if ($info['exists']) {
                            if ($newPath === null || strlen($info['path']) < strlen($newPath)) {
                                $newPath = $info['path'];
                                $method = $info['method'];
                            }
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