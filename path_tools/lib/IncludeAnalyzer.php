<?php
/**
 * Analisador de inclusões de arquivos PHP
 */

namespace PathTools\Lib;

use PathTools\Utils\FileUtils;

class IncludeAnalyzer {
    /**
     * Encontra scripts que incluem o arquivo fonte
     * 
     * @param string $sourceFile Arquivo fonte a ser verificado
     * @return array Lista de arquivos que incluem o arquivo fonte
     */
    public static function findIncludingScripts($sourceFile) {
        $result = [];
        $projectRoot = realpath(PROJECT_ROOT);
        
        // Encontrar todos os arquivos PHP no projeto
        $phpFiles = [];
        FileUtils::findPhpFiles($projectRoot, $phpFiles);
        
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
} 