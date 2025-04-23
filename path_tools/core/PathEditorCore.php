<?php
namespace PathTools\Core;

/**
 * Classe principal para edição e correção de caminhos em arquivos
 */
class PathEditorCore {
    private $projectRoot;
    private $debugLog = [];

    /**
     * Construtor da classe
     * 
     * @param string $projectRoot Diretório raiz do projeto
     */
    public function __construct($projectRoot = null) {
        $this->projectRoot = $projectRoot ?: dirname(dirname(__DIR__));
        $this->debugLog[] = "Inicializado com raiz do projeto: {$this->projectRoot}";
    }

    /**
     * Verifica se um arquivo existe a partir de um caminho relativo
     * 
     * @param string $path Caminho para verificar
     * @param string $sourceFile Arquivo fonte que contém o caminho
     * @return bool Se o arquivo existe
     */
    public function checkFileExists($path, $sourceFile) {
        $this->logDebug("Verificando se o arquivo existe: $path (no contexto de $sourceFile)");
        
        // Para URLs externas, consideramos como válido por padrão
        if (strpos($path, 'http') === 0 || strpos($path, '//') === 0) {
            $this->logDebug("URL externa detectada, considerando válida");
            return true;
        }
        
        // PROTEÇÃO: Verifica se o caminho está vazio
        if (empty($path)) {
            $this->logDebug("ALERTA: Caminho vazio passado para checkFileExists");
            return false;
        }
        
        // Verifica se o caminho contém código PHP
        if (strpos($path, '<?php') !== false || strpos($path, '<?=') !== false) {
            $this->logDebug("Caminho contém código PHP, extraindo parte estática");
            
            // Extrair parte estática do caminho (após o código PHP)
            preg_match('/(?:\?>|;)(.+)$/', $path, $matches);
            if (!empty($matches[1])) {
                $staticPart = trim($matches[1]);
                $this->logDebug("Parte estática extraída: $staticPart");
                $path = $staticPart;
            } else {
                // Tenta extrair a parte depois de uma variável PHP
                preg_match('/.*?(?:;|\?>)\s*\/(.+)$/', $path, $matches);
                if (!empty($matches[1])) {
                    $this->logDebug("Parte após variável PHP: $matches[1]");
                    $path = $matches[1];
                } else {
                    $this->logDebug("Não foi possível extrair parte estática do caminho PHP");
                    return false;
                }
            }
        }
        
        // Obter caminho absoluto do arquivo fonte
        $sourceDir = dirname(realpath($sourceFile));
        if (!$sourceDir) {
            $sourceDir = dirname($sourceFile);
        }
        
        // 1. Verificar o caminho exatamente como está
        $absolutePath = $sourceDir . '/' . $path;
        $this->logDebug("Tentativa 1 - Direto: $absolutePath");
        
        if (file_exists($absolutePath)) {
            $this->logDebug("Arquivo encontrado diretamente: $absolutePath");
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
        $this->logDebug("Tentativa 2 - Resolvido: $absoluteResolved");
        
        if (file_exists($absoluteResolved)) {
            $this->logDebug("Arquivo encontrado (resolvido): $absoluteResolved");
            return true;
        }
        
        // 3. Tentar em diretórios comuns
        $filename = basename($path);
        $commonDirs = ['', 'images', 'img', 'css', 'js', 'scripts', 'assets', 'includes', 'media'];
        
        foreach ($commonDirs as $dir) {
            $simplePath = ($dir ? "$dir/" : '') . $filename;
            $absoluteSimple = $sourceDir . '/' . $simplePath;
            $this->logDebug("Tentativa 3 - Simples: $absoluteSimple");
            
            if (file_exists($absoluteSimple)) {
                $this->logDebug("Arquivo encontrado (caminho simples): $absoluteSimple");
                return true;
            }
        }
        
        $this->logDebug("Arquivo não encontrado em nenhuma tentativa");
        return false;
    }

    /**
     * Encontra um caminho alternativo para um caminho quebrado
     * 
     * @param string $oldPath Caminho original (quebrado)
     * @param string $sourceFile Arquivo fonte onde o caminho foi encontrado
     * @return string Caminho alternativo encontrado
     */
    public function findAlternativePath($oldPath, $sourceFile) {
        $this->logDebug("\n------ INICIANDO BUSCA POR CAMINHO ALTERNATIVO ------");
        $this->logDebug("Caminho original: $oldPath");
        $this->logDebug("Arquivo fonte: $sourceFile");
        
        // Verifica se o caminho contém código PHP
        $hasPhpCode = false;
        $staticPath = $oldPath;
        
        if (strpos($oldPath, '<?php') !== false || strpos($oldPath, '<?=') !== false) {
            $hasPhpCode = true;
            $this->logDebug("Caminho contém código PHP, extraindo parte estática");
            
            // Extrair parte estática do caminho (após o código PHP)
            preg_match('/(?:\?>|;)\s*\/(.+)$/', $oldPath, $matches);
            if (!empty($matches[1])) {
                $staticPath = $matches[1];
                $this->logDebug("Parte estática extraída: $staticPath");
            } else {
                // Tenta extrair o último segmento do caminho (após a última /)
                $parts = explode('/', $oldPath);
                $staticPath = end($parts);
                $this->logDebug("Último segmento do caminho: $staticPath");
            }
        }
        
        // Obter diretório absoluto do arquivo fonte
        $sourceDir = dirname(realpath($sourceFile));
        
        // Extrair o nome do arquivo do caminho antigo
        $filename = basename($staticPath);
        $this->logDebug("Nome do arquivo a procurar: $filename");
        
        // ABORDAGEM 1: Verificar se o caminho existe com a estrutura atual
        $possiblePath = $staticPath;
        $absolutePath = $sourceDir . '/' . $possiblePath;
        $this->logDebug("Tentando caminho direto: $absolutePath");
        
        if (file_exists($absolutePath)) {
            $this->logDebug("Caminho original existe! Mantendo-o: $possiblePath");
            return $possiblePath;
        }
        
        // ABORDAGEM 2: Buscar em diretórios comuns
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
        
        foreach ($commonDirs as $dir) {
            $testPath = $dir . $filename;
            $fullPath = $sourceDir . '/' . $testPath;
            $this->logDebug("Tentando em diretório comum: $fullPath");
            
            if (file_exists($fullPath)) {
                $this->logDebug("SUCESSO! Encontrado em diretório comum: $testPath");
                return $testPath;
            }
        }
        
        // ABORDAGEM 3: Busca recursiva no projeto
        $this->logDebug("Iniciando busca recursiva pelo arquivo: $filename");
        $foundFiles = [];
        $this->findFileRecursively($this->projectRoot, $filename, $foundFiles);
        
        if (!empty($foundFiles)) {
            $this->logDebug("Encontrado(s) " . count($foundFiles) . " arquivo(s) com o nome");
            
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
            
            $relativePath = $this->makeRelativePath($sourceDir, $bestMatch);
            
            $this->logDebug("Melhor correspondência: $relativePath");
            return $relativePath;
        }
        
        // ABORDAGEM 4: Último recurso - manter apenas o nome do arquivo
        $this->logDebug("Último recurso: apenas o nome do arquivo: $filename");
        return $filename;
    }
    
    /**
     * Busca arquivos recursivamente no sistema de arquivos
     * 
     * @param string $baseDir Diretório base para iniciar a busca
     * @param string $filename Nome do arquivo para procurar
     * @param array &$results Array para armazenar resultados
     * @param int $depth Profundidade atual da recursão
     * @param int $maxDepth Profundidade máxima de busca
     */
    public function findFileRecursively($baseDir, $filename, &$results, $depth = 0, $maxDepth = 5) {
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
            $this->findFileRecursively($dir, $filename, $results, $depth + 1, $maxDepth);
        }
    }
    
    /**
     * Cria um caminho relativo entre dois caminhos absolutos
     * 
     * @param string $from Caminho de origem
     * @param string $to Caminho de destino
     * @return string Caminho relativo
     */
    public function makeRelativePath($from, $to) {
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
    
    /**
     * Atualiza um caminho em um arquivo
     * 
     * @param string $sourceFile Arquivo fonte
     * @param string $oldPath Caminho antigo
     * @param string $newPath Novo caminho
     * @return array Resultado da atualização
     */
    public function updatePath($sourceFile, $oldPath, $newPath) {
        $this->logDebug("Atualizando caminho: $oldPath -> $newPath no arquivo $sourceFile");
        
        // Lê o conteúdo do arquivo
        $content = file_get_contents($sourceFile);
        if ($content === false) {
            return [
                'success' => false,
                'message' => 'Não foi possível ler o arquivo fonte'
            ];
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
                $this->logDebug("Padrão $i substituiu $count ocorrências");
            }
        }

        // PROTEÇÃO: Verificar se não houve substituições que resultaram em caminhos vazios
        if (strpos($newContent, '=""') !== false || 
            strpos($newContent, "=''") !== false || 
            strpos($newContent, 'url()') !== false) {
            
            $this->logDebug("ALERTA: Substituições resultaram em caminhos vazios!");
            return [
                'success' => false,
                'message' => "Erro: O processamento resultaria em caminhos vazios"
            ];
        }

        // Se houve substituições, salvamos o arquivo
        if ($replacementCount > 0) {
            if (file_put_contents($sourceFile, $newContent) !== false) {
                return [
                    'success' => true,
                    'message' => "Path atualizado com sucesso! ($replacementCount substituições)",
                    'replacementCount' => $replacementCount,
                    'exists' => $this->checkFileExists($newPath, $sourceFile)
                ];
            } else {
                return [
                    'success' => false,
                    'message' => "Erro ao atualizar o arquivo"
                ];
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
                        return [
                            'success' => true,
                            'message' => "Path atualizado com sucesso (fallback)!",
                            'replacementCount' => $count,
                            'exists' => $this->checkFileExists($newPath, $sourceFile)
                        ];
                    } else {
                        return [
                            'success' => false,
                            'message' => "Erro ao atualizar o arquivo"
                        ];
                    }
                } else {
                    return [
                        'success' => false,
                        'message' => "Erro: A substituição resultaria em caminhos vazios"
                    ];
                }
            } else {
                return [
                    'success' => false,
                    'message' => "Não foi possível encontrar o padrão para substituir"
                ];
            }
        }
    }
    
    /**
     * Processa um lote de atualizações de caminhos
     * 
     * @param array $items Lista de itens para atualizar
     * @return array Resultados do processamento
     */
    public function processBatch($items) {
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
            
            // Encontrar caminho alternativo
            $newPath = $this->findAlternativePath($oldPath, $sourceFile);
            
            if (empty($newPath)) {
                $results[] = [
                    'success' => false,
                    'message' => "Não foi possível encontrar um caminho alternativo para: $oldPath",
                    'old_path' => $oldPath,
                    'new_path' => null
                ];
                continue;
            }
            
            // Atualizar o caminho
            $updateResult = $this->updatePath($sourceFile, $oldPath, $newPath);
            
            if ($updateResult['success']) {
                $totalFixed++;
                $results[] = [
                    'success' => true,
                    'message' => $updateResult['message'],
                    'old_path' => $oldPath,
                    'new_path' => $newPath,
                    'exists' => $updateResult['exists']
                ];
            } else {
                $results[] = [
                    'success' => false,
                    'message' => $updateResult['message'],
                    'old_path' => $oldPath,
                    'new_path' => $newPath
                ];
            }
        }
        
        return [
            'success' => $totalFixed > 0,
            'message' => $totalFixed > 0 
                        ? "Foram consertados $totalFixed de " . count($items) . " caminhos." 
                        : "Não foi possível consertar nenhum caminho.",
            'results' => $results,
            'total_fixed' => $totalFixed
        ];
    }
    
    /**
     * Adiciona mensagem ao log de depuração
     * 
     * @param string $message Mensagem para o log
     */
    private function logDebug($message) {
        $this->debugLog[] = date('H:i:s') . " - " . $message;
    }

    /**
     * Retorna o log de depuração
     * 
     * @return array Log de depuração
     */
    public function getDebugLog() {
        return $this->debugLog;
    }
} 