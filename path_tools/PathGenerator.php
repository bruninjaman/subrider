<?php
namespace PathTools;

/**
 * Classe responsável por gerar e manipular caminhos
 */
class PathGenerator {
    private $projectRoot;
    
    /**
     * Construtor da classe
     * 
     * @param string $projectRoot Caminho raiz do projeto
     */
    public function __construct($projectRoot) {
        $this->projectRoot = rtrim($projectRoot, '/');
    }
    
    /**
     * Normaliza um caminho, removendo componentes redundantes
     * 
     * @param string $path Caminho a ser normalizado
     * @return string Caminho normalizado
     */
    public function normalizePath($path) {
        // Substituir múltiplas barras por uma única
        $path = preg_replace('|/{2,}|', '/', $path);
        
        // Eliminar './' do caminho
        $path = str_replace('./', '', $path);
        
        // Processar '../' no caminho
        $parts = explode('/', $path);
        $newParts = [];
        
        foreach ($parts as $part) {
            if ($part === '..') {
                array_pop($newParts);
            } elseif ($part !== '' && $part !== '.') {
                $newParts[] = $part;
            }
        }
        
        return implode('/', $newParts);
    }
    
    /**
     * Gera um caminho absoluto a partir de um caminho relativo
     * 
     * @param string $relativePath Caminho relativo
     * @return string Caminho absoluto
     */
    public function getAbsolutePath($relativePath) {
        $relativePath = trim($relativePath, '/');
        return $this->projectRoot . '/' . $this->normalizePath($relativePath);
    }
    
    /**
     * Gera um caminho relativo a partir de um caminho absoluto
     * 
     * @param string $absolutePath Caminho absoluto
     * @return string|null Caminho relativo ou null se não estiver dentro do diretório raiz
     */
    public function getRelativePath($absolutePath) {
        $absolutePath = rtrim($absolutePath, '/');
        
        if (strpos($absolutePath, $this->projectRoot) !== 0) {
            return null; // O caminho não está dentro do diretório raiz
        }
        
        $relativePath = substr($absolutePath, strlen($this->projectRoot) + 1);
        return $relativePath;
    }
    
    /**
     * Cria um diretório recursivamente se ele não existir
     * 
     * @param string $path Caminho do diretório
     * @param int $permissions Permissões para o diretório (padrão: 0755)
     * @return bool Sucesso ou falha na operação
     */
    public function createDirectory($path, $permissions = 0755) {
        $absolutePath = $this->getAbsolutePath($path);
        
        if (!file_exists($absolutePath)) {
            return mkdir($absolutePath, $permissions, true);
        }
        
        return is_dir($absolutePath);
    }
    
    /**
     * Verifica se um caminho existe
     * 
     * @param string $path Caminho a ser verificado
     * @return bool Se o caminho existe
     */
    public function pathExists($path) {
        $absolutePath = $this->getAbsolutePath($path);
        return file_exists($absolutePath);
    }
    
    /**
     * Obtém informações sobre um arquivo ou diretório
     * 
     * @param string $path Caminho do arquivo ou diretório
     * @return array|false Informações ou false se o caminho não existir
     */
    public function getPathInfo($path) {
        $absolutePath = $this->getAbsolutePath($path);
        
        if (!file_exists($absolutePath)) {
            return false;
        }
        
        return [
            'path' => $path,
            'absolutePath' => $absolutePath,
            'isDir' => is_dir($absolutePath),
            'isFile' => is_file($absolutePath),
            'size' => is_file($absolutePath) ? filesize($absolutePath) : 0,
            'modified' => date("Y-m-d H:i:s", filemtime($absolutePath)),
            'permissions' => substr(sprintf('%o', fileperms($absolutePath)), -4)
        ];
    }
    
    /**
     * Obtém o caminho raiz do projeto
     * 
     * @return string Caminho raiz do projeto
     */
    public function getProjectRoot() {
        return $this->projectRoot;
    }
} 