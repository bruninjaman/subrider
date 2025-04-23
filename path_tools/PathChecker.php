<?php
namespace PathTools;

/**
 * Classe responsável por verificar os caminhos e encontrar arquivos específicos
 */
class PathChecker {
    private $projectRoot;
    private $foundFiles = [];
    private $scannedDirs = 0;
    private $scannedFiles = 0;
    
    /**
     * Construtor da classe
     * 
     * @param string $projectRoot Caminho raiz do projeto
     */
    public function __construct($projectRoot) {
        $this->projectRoot = $projectRoot;
    }
    
    /**
     * Verifica os caminhos fornecidos procurando por arquivos específicos
     * 
     * @param array $paths Caminhos a serem verificados
     * @param array $extensions Extensões de arquivo a serem procuradas
     * @return array Arquivos encontrados
     */
    public function scanPaths($paths, $extensions = []) {
        $this->foundFiles = [];
        $this->scannedDirs = 0;
        $this->scannedFiles = 0;
        
        if (empty($paths)) {
            return $this->foundFiles;
        }
        
        foreach ($paths as $path) {
            $fullPath = $this->projectRoot . '/' . trim($path, '/');
            if (is_dir($fullPath)) {
                $this->scanDirectory($fullPath, $extensions);
            }
        }
        
        return $this->foundFiles;
    }
    
    /**
     * Escaneia recursivamente um diretório
     * 
     * @param string $dir Diretório a ser escaneado
     * @param array $extensions Extensões de arquivo a serem procuradas
     */
    private function scanDirectory($dir, $extensions) {
        $this->scannedDirs++;
        
        $files = scandir($dir);
        foreach ($files as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }
            
            $path = $dir . '/' . $file;
            
            if (is_dir($path)) {
                $this->scanDirectory($path, $extensions);
            } else {
                $this->scannedFiles++;
                
                // Se não há extensões específicas para procurar, adiciona todos os arquivos
                if (empty($extensions)) {
                    $relativePath = str_replace($this->projectRoot . '/', '', $path);
                    $this->foundFiles[] = [
                        'path' => $relativePath,
                        'extension' => pathinfo($path, PATHINFO_EXTENSION),
                        'size' => filesize($path),
                        'modified' => date("Y-m-d H:i:s", filemtime($path))
                    ];
                } else {
                    // Verifica se o arquivo tem uma das extensões procuradas
                    $extension = pathinfo($path, PATHINFO_EXTENSION);
                    if (in_array($extension, $extensions)) {
                        $relativePath = str_replace($this->projectRoot . '/', '', $path);
                        $this->foundFiles[] = [
                            'path' => $relativePath,
                            'extension' => $extension,
                            'size' => filesize($path),
                            'modified' => date("Y-m-d H:i:s", filemtime($path))
                        ];
                    }
                }
            }
        }
    }
    
    /**
     * Obtém a contagem de diretórios escaneados
     * 
     * @return int Número de diretórios escaneados
     */
    public function getScannedDirsCount() {
        return $this->scannedDirs;
    }
    
    /**
     * Obtém a contagem de arquivos escaneados
     * 
     * @return int Número de arquivos escaneados
     */
    public function getScannedFilesCount() {
        return $this->scannedFiles;
    }
    
    /**
     * Obtém a contagem de arquivos encontrados
     * 
     * @return int Número de arquivos encontrados
     */
    public function getFoundFilesCount() {
        return count($this->foundFiles);
    }
} 