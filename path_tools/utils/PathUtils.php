<?php
/**
 * Utilitários para manipulação de caminhos
 */

namespace PathTools\Utils;

class PathUtils {
    /**
     * Resolve um caminho relativo a partir de uma base
     * 
     * @param string $base Caminho base
     * @param string $relativePath Caminho relativo
     * @return string Caminho completo resolvido
     */
    public static function resolvePathFromBase($base, $relativePath) {
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

    /**
     * Ajusta a base com base no número de ../ no caminho
     * 
     * @param string $base Caminho base
     * @param string $path Caminho relativo
     * @return string Base ajustada
     */
    public static function adjustBaseForRelativePath($base, $path) {
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
    
    /**
     * Verifica se um padrão corresponde a uma inclusão PHP
     * 
     * @param string $pattern Padrão de regex a ser verificado
     * @return bool True se o padrão for uma inclusão PHP, false caso contrário
     */
    public static function isPhpIncludePattern($pattern) {
        return (
            strpos($pattern, 'include') !== false || 
            strpos($pattern, 'require') !== false
        );
    }
} 