<?php
/**
 * Utilitários para manipulação e busca de arquivos
 */

namespace PathTools\Utils;

class FileUtils {
    /**
     * Verifica se um arquivo existe
     * 
     * @param string $path Caminho do arquivo a ser verificado
     * @param string $sourceFile Arquivo fonte para contexto
     * @return bool True se o arquivo existe, false caso contrário
     */
    public static function checkFileExists($path, $sourceFile) {
        error_log("Verificando se o arquivo existe: $path (no contexto de $sourceFile)");
        
        // Para URLs externas, consideramos como válido por padrão
        if (strpos($path, 'http') === 0 || strpos($path, '//') === 0) {
            error_log("URL externa detectada, considerando válida");
            return true;
        }
        
        // PROTEÇÃO: Verifica se o caminho está vazio
        if (empty($path)) {
            error_log("ALERTA: Caminho vazio passado para checkFileExists");
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

    /**
     * Encontra todos os arquivos PHP em um diretório
     * 
     * @param string $dir Diretório para procurar
     * @param array &$result Array para armazenar os resultados
     * @return void
     */
    public static function findPhpFiles($dir, &$result) {
        // Ignorar diretórios específicos
        $baseName = basename($dir);
        if (in_array($baseName, IGNORED_DIRS)) {
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
            self::findPhpFiles($subdir, $result);
        }
    }

    /**
     * Busca recursivamente um arquivo em um diretório
     * 
     * @param string $dir Diretório para iniciar a busca
     * @param string $filename Nome do arquivo a ser procurado
     * @param int $maxDepth Profundidade máxima de busca
     * @param int $currentDepth Profundidade atual da busca
     * @return string|null Caminho do arquivo encontrado ou null se não encontrado
     */
    public static function findFileRecursive($dir, $filename, $maxDepth = 3, $currentDepth = 0) {
        // Diretórios a serem ignorados
        $baseName = basename($dir);
        
        // Verifica se o diretório atual deve ser ignorado
        if (in_array($baseName, IGNORED_DIRS)) {
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
            $result = self::findFileRecursive($subdir, $filename, $maxDepth, $currentDepth + 1);
            if ($result) return $result;
        }
        
        return null;
    }
    
    /**
     * Busca recursivamente um arquivo e guarda todos os resultados encontrados
     * 
     * @param string $baseDir Diretório base para começar a busca
     * @param string $filename Nome do arquivo a ser procurado
     * @param array &$results Array onde os resultados serão armazenados
     * @param int $depth Profundidade atual da busca
     * @param int $maxDepth Profundidade máxima de busca
     */
    public static function findFileRecursively($baseDir, $filename, &$results, $depth = 0, $maxDepth = 5) {
        // Limitar profundidade para evitar loops
        if ($depth > $maxDepth) return;
        
        // Ignorar diretórios específicos
        $currentDir = basename($baseDir);
        if (in_array($currentDir, IGNORED_DIRS)) return;
        
        // Verificar se o arquivo existe neste diretório
        $fullPath = $baseDir . '/' . $filename;
        if (file_exists($fullPath)) {
            $results[] = $fullPath;
        }
        
        // Procurar em subdiretórios
        $subdirs = glob($baseDir . '/*', GLOB_ONLYDIR);
        foreach ($subdirs as $dir) {
            self::findFileRecursively($dir, $filename, $results, $depth + 1, $maxDepth);
        }
    }
    
    /**
     * Cria um caminho relativo entre dois caminhos
     * 
     * @param string $from Caminho de origem
     * @param string $to Caminho de destino
     * @return string Caminho relativo
     */
    public static function makeRelativePath($from, $to) {
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
} 