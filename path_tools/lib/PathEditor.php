<?php
/**
 * Editor de caminhos
 */

namespace PathTools\Lib;

use PathTools\Utils\FileUtils;
use PathTools\Utils\PathUtils;

class PathEditor {
    /**
     * Testa a correção automática de um caminho
     * 
     * @param string $path Caminho a ser corrigido
     * @param string $sourceFile Arquivo fonte
     * @return array Resultado do teste
     */
    public function testAutoFix($path, $sourceFile) {
        error_log("========== TESTE DE AUTOFIX INICIADO ==========");
        error_log("Testando AutoFix para: $path no arquivo $sourceFile");
        
        // PROTEÇÃO: Validação de entrada
        if (empty($path) || empty($sourceFile) || !file_exists($sourceFile)) {
            $errorMessage = empty($path) ? "Caminho vazio" : 
                          (!file_exists($sourceFile) ? "Arquivo fonte não existe: $sourceFile" : "Erro desconhecido");
            
            error_log("Erro de validação: $errorMessage");
            return [
                'success' => false,
                'message' => $errorMessage,
                'debug_info' => [
                    'path' => $path,
                    'source_file' => $sourceFile
                ]
            ];
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
        
        // Buscar caminho alternativo
        $newPath = PathFinder::findAlternativePath($path, $sourceFile);
        
        // Verificar se o novo caminho existe
        $exists = FileUtils::checkFileExists($newPath, $sourceFile);
        
        // Testar os padrões
        $content = file_get_contents($sourceFile);
        if ($content === false) {
            return [
                'success' => false,
                'message' => "Não foi possível ler o arquivo: $sourceFile"
            ];
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
        
        return [
            'success' => true,
            'new_path' => $newPath,
            'exists' => $exists,
            'replacements' => $replacementCount,
            'debug_info' => [
                'patterns_found' => $debugPatterns,
                'source_file' => $sourceFile,
                'old_path' => $path,
                'static_path' => $staticPath,
                'has_php_code' => $hasPhpCode
            ]
        ];
    }
    
    /**
     * Aplica autofix a múltiplos caminhos
     * 
     * @param array $items Lista de itens a serem corrigidos
     * @return array Resultados das correções
     */
    public function applyAutoFix($items) {
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
            
            // Buscar caminho alternativo
            $newPath = PathFinder::findAlternativePath($oldPath, $sourceFile);
            
            // PROTEÇÃO: Verificar se não encontramos um caminho alternativo
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
            
            // Aplicar substituição
            $result = $this->replacePathInContent($content, $oldPath, $newPath, $sourceFile);
            
            if ($result['success']) {
                // Salvar o arquivo atualizado
                if (file_put_contents($sourceFile, $result['content']) !== false) {
                    $totalFixed++;
                    $results[] = [
                        'success' => true,
                        'message' => "Path atualizado com sucesso! ({$result['replacements']} substituições)",
                        'old_path' => $oldPath,
                        'new_path' => $newPath,
                        'exists' => FileUtils::checkFileExists($newPath, $sourceFile)
                    ];
                } else {
                    $results[] = [
                        'success' => false,
                        'message' => "Erro ao salvar o arquivo: $sourceFile",
                        'old_path' => $oldPath,
                        'new_path' => $newPath
                    ];
                }
            } else {
                $results[] = [
                    'success' => false,
                    'message' => $result['message'],
                    'old_path' => $oldPath,
                    'new_path' => $newPath
                ];
            }
        }
        
        return [
            'success' => $totalFixed > 0,
            'message' => $totalFixed > 0 ? "Foram consertados $totalFixed de " . count($items) . " caminhos." : "Não foi possível consertar nenhum caminho.",
            'results' => $results
        ];
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
        // Validação básica
        if (empty($sourceFile) || empty($oldPath)) {
            return [
                'success' => false,
                'message' => 'Parâmetros inválidos: caminho original ou arquivo fonte não informados'
            ];
        }

        // PROTEÇÃO: Verificar se o novo caminho está vazio
        if (empty($newPath)) {
            // Tenta gerar automaticamente um novo caminho
            $newPath = PathFinder::findAlternativePath($oldPath, $sourceFile);
            
            if (empty($newPath)) {
                // Usar apenas o nome do arquivo como fallback
                $newPath = basename($oldPath);
                error_log("Gerado caminho básico: $newPath");
            }
            
            // Se ainda estiver vazio, aborta
            if (empty($newPath)) {
                return [
                    'success' => false,
                    'message' => 'Erro: Não foi possível determinar um novo caminho válido'
                ];
            }
        }

        error_log("Atualizando manualmente: $oldPath -> $newPath no arquivo $sourceFile");

        // Lê o conteúdo do arquivo
        $content = file_get_contents($sourceFile);
        if ($content === false) {
            return [
                'success' => false,
                'message' => 'Não foi possível ler o arquivo fonte'
            ];
        }

        // Aplicar substituição
        $result = $this->replacePathInContent($content, $oldPath, $newPath, $sourceFile);
        
        if ($result['success']) {
            // Salvar o arquivo atualizado
            if (file_put_contents($sourceFile, $result['content']) !== false) {
                return [
                    'success' => true,
                    'message' => "Path atualizado com sucesso! ({$result['replacements']} substituições)",
                    'exists' => FileUtils::checkFileExists($newPath, $sourceFile)
                ];
            } else {
                return [
                    'success' => false,
                    'message' => "Erro ao salvar o arquivo: $sourceFile"
                ];
            }
        } else {
            return [
                'success' => false,
                'message' => $result['message']
            ];
        }
    }
    
    /**
     * Substitui um caminho no conteúdo de um arquivo
     * 
     * @param string $content Conteúdo do arquivo
     * @param string $oldPath Caminho antigo
     * @param string $newPath Novo caminho
     * @param string $sourceFile Arquivo fonte (para logging)
     * @return array Resultado da substituição
     */
    private function replacePathInContent($content, $oldPath, $newPath, $sourceFile) {
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
            $isPHPInclude = PathUtils::isPhpIncludePattern($pattern);
            
            $tempContent = preg_replace($pattern, $replacements[$i], $newContent, -1, $count);
            if ($count > 0) {
                $newContent = $tempContent;
                $replacementCount += $count;
                error_log("Padrão $i" . ($isPHPInclude ? " (inclusão PHP)" : "") . " substituiu $count ocorrências");
            }
        }

        // PROTEÇÃO: Verificar se não houve substituições que resultaram em caminhos vazios
        if (strpos($newContent, '=""') !== false || 
            strpos($newContent, "=''") !== false || 
            strpos($newContent, 'url()') !== false) {
            
            error_log("ALERTA: Substituições resultaram em caminhos vazios!");
            return [
                'success' => false,
                'message' => "Erro: A substituição resultaria em caminhos vazios"
            ];
        }

        // Se houve substituições, retornamos o conteúdo atualizado
        if ($replacementCount > 0) {
            return [
                'success' => true,
                'content' => $newContent,
                'replacements' => $replacementCount
            ];
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
                    
                    return [
                        'success' => true,
                        'content' => $newContent,
                        'replacements' => $count
                    ];
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
} 