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
     * @param string $sourceFile Arquivo fonte onde o caminho será atualizado
     * @param string $oldPath Caminho antigo/original
     * @param string $newPath Caminho novo para substituir
     * @return bool|array Sucesso da operação ou array com informações de erro
     */
    public function updatePath($sourceFile, $oldPath, $newPath) {
        $this->logDebug("\n------ INICIANDO ATUALIZAÇÃO DE CAMINHO ------");
        $this->logDebug("Arquivo fonte: $sourceFile");
        $this->logDebug("Caminho antigo: $oldPath");
        $this->logDebug("Caminho novo: $newPath");
        
        // Validações básicas
        if (empty($sourceFile) || empty($oldPath) || empty($newPath)) {
            $this->logDebug("ERRO: Parâmetros incompletos para atualização");
            return [
                'success' => false,
                'message' => 'Parâmetros incompletos para atualização de caminho'
            ];
        }
        
        if (!file_exists($sourceFile)) {
            $this->logDebug("ERRO: Arquivo fonte não encontrado: $sourceFile");
            return [
                'success' => false,
                'message' => 'Arquivo fonte não encontrado: ' . $sourceFile
            ];
        }
        
        // Ler o conteúdo do arquivo
        $content = file_get_contents($sourceFile);
        if ($content === false) {
            $this->logDebug("ERRO: Não foi possível ler o arquivo: $sourceFile");
            return [
                'success' => false,
                'message' => 'Falha ao ler o arquivo fonte'
            ];
        }
        
        // Escapar o caminho antigo para usar em uma expressão regular
        $escapedOldPath = preg_quote($oldPath, '/');
        
        // Criar padrões de substituição para diferentes contextos
        $patterns = [
            // Para atributos src e href em tags HTML
            "/(['\"])(" . $escapedOldPath . ")(['\"])/",
            // Para inclusões de arquivos PHP
            "/(include[_once]*\\s*\\(\\s*['\"])(" . $escapedOldPath . ")(['\"]\\s*\\))/",
            // Para requires de arquivos PHP
            "/(require[_once]*\\s*\\(\\s*['\"])(" . $escapedOldPath . ")(['\"]\\s*\\))/",
            // Para URL de chamadas AJAX
            "/(url:\\s*['\"])(" . $escapedOldPath . ")(['\"])/",
        ];
        
        $replacements = [
            "$1" . $newPath . "$3",
            "$1" . $newPath . "$3",
            "$1" . $newPath . "$3",
            "$1" . $newPath . "$3",
        ];
        
        // Aplicar substituições
        $newContent = preg_replace($patterns, $replacements, $content, -1, $count);
        
        if ($count === 0) {
            $this->logDebug("AVISO: Nenhuma correspondência encontrada para substituição");
            return [
                'success' => false,
                'message' => 'Caminho não encontrado no arquivo fonte'
            ];
        }
        
        $this->logDebug("$count substituições realizadas");
        
        // Fazer backup do arquivo original
        $backupPath = $sourceFile . '.bak';
        $backupSuccess = copy($sourceFile, $backupPath);
        
        if (!$backupSuccess) {
            $this->logDebug("AVISO: Não foi possível criar backup: $backupPath");
        } else {
            $this->logDebug("Backup criado: $backupPath");
        }
        
        // Salvar o arquivo com o conteúdo atualizado
        $writeSuccess = file_put_contents($sourceFile, $newContent);
        
        if ($writeSuccess === false) {
            $this->logDebug("ERRO: Não foi possível escrever no arquivo: $sourceFile");
            return [
                'success' => false,
                'message' => 'Falha ao escrever no arquivo fonte'
            ];
        }
        
        $this->logDebug("Atualização concluída com sucesso");
        return [
            'success' => true,
            'message' => 'Caminho atualizado com sucesso',
            'old_path' => $oldPath,
            'new_path' => $newPath,
            'replacements' => $count,
            'exists' => $this->checkFileExists($newPath, $sourceFile),
        ];
    }

    /**
     * Processa uma lista de itens para atualização de caminhos
     * 
     * @param array $items Lista de itens para processar
     * @return array Resultados do processamento
     */
    public function processBatch($items) {
        $this->logDebug("\n------ INICIANDO PROCESSAMENTO EM LOTE ------");
        $this->logDebug("Itens a processar: " . count($items));
        
        $results = [
            'success' => true,
            'message' => 'Processamento em lote concluído',
            'total' => count($items),
            'processed' => 0,
            'successful' => 0,
            'failed' => 0,
            'items' => []
        ];
        
        if (empty($items)) {
            $this->logDebug("Lista de itens vazia");
            $results['message'] = 'Nenhum item para processar';
            return $results;
        }
        
        foreach ($items as $index => $item) {
            $this->logDebug("\nProcessando item $index");
            
            // Validar item
            if (!isset($item['source_file']) || !isset($item['old_path']) || !isset($item['new_path'])) {
                $this->logDebug("ERRO: Item $index não contém os campos obrigatórios");
                $results['items'][] = [
                    'success' => false,
                    'message' => 'Item inválido: campos obrigatórios ausentes',
                    'item_index' => $index
                ];
                $results['failed']++;
                continue;
            }
            
            $sourceFile = $item['source_file'];
            $oldPath = $item['old_path'];
            $newPath = $item['new_path'];
            
            // Verificar campos obrigatórios
            if (empty($sourceFile) || empty($oldPath) || empty($newPath)) {
                $this->logDebug("ERRO: Item $index contém campos vazios");
                $results['items'][] = [
                    'success' => false,
                    'message' => 'Campos obrigatórios não podem estar vazios',
                    'source_file' => $sourceFile,
                    'old_path' => $oldPath,
                    'new_path' => $newPath,
                    'item_index' => $index
                ];
                $results['failed']++;
                continue;
            }
            
            // Tentar atualizar o caminho
            $updateResult = $this->updatePath($sourceFile, $oldPath, $newPath);
            $results['processed']++;
            
            if ($updateResult['success']) {
                $results['successful']++;
                $this->logDebug("Item $index processado com sucesso");
            } else {
                $results['failed']++;
                $this->logDebug("Falha ao processar item $index: " . $updateResult['message']);
            }
            
            // Adicionar resultado ao array de itens
            $updateResult['item_index'] = $index;
            $updateResult['source_file'] = $sourceFile;
            $updateResult['old_path'] = $oldPath;
            $updateResult['new_path'] = $newPath;
            $results['items'][] = $updateResult;
        }
        
        $this->logDebug("\nProcessamento em lote concluído");
        $this->logDebug("Total processado: {$results['processed']}");
        $this->logDebug("Sucessos: {$results['successful']}");
        $this->logDebug("Falhas: {$results['failed']}");
        
        // Atualizar mensagem final
        if ($results['failed'] > 0) {
            if ($results['successful'] > 0) {
                $results['message'] = "Processamento concluído com {$results['successful']} sucessos e {$results['failed']} falhas";
            } else {
                $results['message'] = "Falha no processamento: nenhum item foi atualizado com sucesso";
                $results['success'] = false;
            }
        } else {
            $results['message'] = "Todos os {$results['successful']} itens foram atualizados com sucesso";
        }
        
        return $results;
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