<?php
namespace PathTools;

/**
 * Classe responsável por transformar caminhos em diferentes formatos
 */
class PathTransformer {
    private $pathGenerator;
    
    /**
     * Construtor da classe
     * 
     * @param PathGenerator $pathGenerator Instância de PathGenerator
     */
    public function __construct(PathGenerator $pathGenerator) {
        $this->pathGenerator = $pathGenerator;
    }
    
    /**
     * Converte barras invertidas para barras normais
     * 
     * @param string $path Caminho a ser transformado
     * @return string Caminho com barras normais
     */
    public function backslashToForwardslash($path) {
        return str_replace('\\', '/', $path);
    }
    
    /**
     * Converte barras normais para barras invertidas
     * 
     * @param string $path Caminho a ser transformado
     * @return string Caminho com barras invertidas
     */
    public function forwardslashToBackslash($path) {
        return str_replace('/', '\\', $path);
    }
    
    /**
     * Converte um caminho para URL
     * 
     * @param string $path Caminho a ser transformado
     * @param string $baseUrl URL base opcional
     * @return string URL do caminho
     */
    public function pathToUrl($path, $baseUrl = '') {
        $path = $this->backslashToForwardslash($path);
        $path = trim($path, '/');
        
        if (!empty($baseUrl)) {
            $baseUrl = rtrim($baseUrl, '/');
            return $baseUrl . '/' . $path;
        }
        
        return '/' . $path;
    }
    
    /**
     * Transforma um caminho absoluto em um caminho relativo a outro diretório
     * 
     * @param string $path Caminho a ser transformado
     * @param string $relativeTo Diretório de referência
     * @return string Caminho relativo
     */
    public function getPathRelativeTo($path, $relativeTo) {
        $path = $this->backslashToForwardslash($path);
        $relativeTo = $this->backslashToForwardslash($relativeTo);
        
        // Obter caminhos absolutos
        $absPath = $this->pathGenerator->getAbsolutePath($path);
        $absRelativeTo = $this->pathGenerator->getAbsolutePath($relativeTo);
        
        // Dividir os caminhos em componentes
        $pathParts = explode('/', trim($absPath, '/'));
        $relativeToParts = explode('/', trim($absRelativeTo, '/'));
        
        // Encontrar partes comuns
        $i = 0;
        while($i < count($pathParts) && $i < count($relativeToParts) && $pathParts[$i] === $relativeToParts[$i]) {
            $i++;
        }
        
        // Construir o caminho relativo
        $relativePath = str_repeat('../', count($relativeToParts) - $i);
        $relativePath .= implode('/', array_slice($pathParts, $i));
        
        return $relativePath;
    }
    
    /**
     * Obtém a extensão de um arquivo a partir do caminho
     * 
     * @param string $path Caminho do arquivo
     * @return string Extensão do arquivo
     */
    public function getExtension($path) {
        return pathinfo($path, PATHINFO_EXTENSION);
    }
    
    /**
     * Obtém o nome do arquivo a partir do caminho (sem extensão)
     * 
     * @param string $path Caminho do arquivo
     * @return string Nome do arquivo
     */
    public function getFilename($path) {
        return pathinfo($path, PATHINFO_FILENAME);
    }
    
    /**
     * Obtém o nome completo do arquivo a partir do caminho (com extensão)
     * 
     * @param string $path Caminho do arquivo
     * @return string Nome completo do arquivo
     */
    public function getBasename($path) {
        return pathinfo($path, PATHINFO_BASENAME);
    }
    
    /**
     * Obtém o diretório a partir do caminho
     * 
     * @param string $path Caminho do arquivo
     * @return string Diretório do arquivo
     */
    public function getDirname($path) {
        return pathinfo($path, PATHINFO_DIRNAME);
    }
    
    /**
     * Altera a extensão de um arquivo no caminho
     * 
     * @param string $path Caminho do arquivo
     * @param string $newExtension Nova extensão
     * @return string Caminho com nova extensão
     */
    public function changeExtension($path, $newExtension) {
        $dirname = $this->getDirname($path);
        $filename = $this->getFilename($path);
        $newExtension = ltrim($newExtension, '.');
        
        if ($dirname === '.') {
            return $filename . '.' . $newExtension;
        }
        
        return $dirname . '/' . $filename . '.' . $newExtension;
    }
    
    /**
     * Adiciona um sufixo ao nome do arquivo no caminho
     * 
     * @param string $path Caminho do arquivo
     * @param string $suffix Sufixo a ser adicionado
     * @return string Caminho com sufixo
     */
    public function addSuffix($path, $suffix) {
        $dirname = $this->getDirname($path);
        $filename = $this->getFilename($path);
        $extension = $this->getExtension($path);
        
        if ($dirname === '.') {
            return $filename . $suffix . ($extension ? '.' . $extension : '');
        }
        
        return $dirname . '/' . $filename . $suffix . ($extension ? '.' . $extension : '');
    }
    
    /**
     * Adiciona um prefixo ao nome do arquivo no caminho
     * 
     * @param string $path Caminho do arquivo
     * @param string $prefix Prefixo a ser adicionado
     * @return string Caminho com prefixo
     */
    public function addPrefix($path, $prefix) {
        $dirname = $this->getDirname($path);
        $filename = $this->getFilename($path);
        $extension = $this->getExtension($path);
        
        if ($dirname === '.') {
            return $prefix . $filename . ($extension ? '.' . $extension : '');
        }
        
        return $dirname . '/' . $prefix . $filename . ($extension ? '.' . $extension : '');
    }
} 