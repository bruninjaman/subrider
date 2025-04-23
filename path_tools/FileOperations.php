<?php
namespace PathTools;

/**
 * Classe responsável por operações em arquivos
 */
class FileOperations {
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
     * Cria um arquivo com conteúdo específico
     * 
     * @param string $path Caminho do arquivo
     * @param string $content Conteúdo do arquivo
     * @param int $permissions Permissões do arquivo (octal)
     * @return bool Resultado da operação
     */
    public function createFile($path, $content = '', $permissions = 0644) {
        $absolutePath = $this->pathGenerator->getAbsolutePath($path);
        $dirName = dirname($absolutePath);
        
        // Verifica se o diretório existe, caso contrário, cria
        if (!is_dir($dirName)) {
            if (!$this->pathGenerator->createDirectory($dirName)) {
                return false;
            }
        }
        
        // Cria o arquivo
        if (file_put_contents($absolutePath, $content) === false) {
            return false;
        }
        
        // Define permissões
        return chmod($absolutePath, $permissions);
    }
    
    /**
     * Lê o conteúdo de um arquivo
     * 
     * @param string $path Caminho do arquivo
     * @return string|false Conteúdo do arquivo ou false em caso de erro
     */
    public function readFile($path) {
        $absolutePath = $this->pathGenerator->getAbsolutePath($path);
        
        if (!file_exists($absolutePath) || !is_readable($absolutePath)) {
            return false;
        }
        
        return file_get_contents($absolutePath);
    }
    
    /**
     * Atualiza o conteúdo de um arquivo
     * 
     * @param string $path Caminho do arquivo
     * @param string $content Novo conteúdo
     * @return bool Resultado da operação
     */
    public function updateFile($path, $content) {
        $absolutePath = $this->pathGenerator->getAbsolutePath($path);
        
        if (!file_exists($absolutePath)) {
            return false;
        }
        
        return file_put_contents($absolutePath, $content) !== false;
    }
    
    /**
     * Apaga um arquivo
     * 
     * @param string $path Caminho do arquivo
     * @return bool Resultado da operação
     */
    public function deleteFile($path) {
        $absolutePath = $this->pathGenerator->getAbsolutePath($path);
        
        if (!file_exists($absolutePath) || !is_file($absolutePath)) {
            return false;
        }
        
        return unlink($absolutePath);
    }
    
    /**
     * Copia um arquivo para outro local
     * 
     * @param string $source Caminho do arquivo de origem
     * @param string $destination Caminho do arquivo de destino
     * @return bool Resultado da operação
     */
    public function copyFile($source, $destination) {
        $absoluteSource = $this->pathGenerator->getAbsolutePath($source);
        $absoluteDestination = $this->pathGenerator->getAbsolutePath($destination);
        
        if (!file_exists($absoluteSource) || !is_file($absoluteSource)) {
            return false;
        }
        
        // Verifica se o diretório de destino existe
        $dirName = dirname($absoluteDestination);
        if (!is_dir($dirName)) {
            if (!$this->pathGenerator->createDirectory($dirName)) {
                return false;
            }
        }
        
        return copy($absoluteSource, $absoluteDestination);
    }
    
    /**
     * Move um arquivo para outro local
     * 
     * @param string $source Caminho do arquivo de origem
     * @param string $destination Caminho do arquivo de destino
     * @return bool Resultado da operação
     */
    public function moveFile($source, $destination) {
        if ($this->copyFile($source, $destination)) {
            return $this->deleteFile($source);
        }
        
        return false;
    }
    
    /**
     * Renomeia um arquivo
     * 
     * @param string $path Caminho do arquivo
     * @param string $newName Novo nome do arquivo
     * @return bool Resultado da operação
     */
    public function renameFile($path, $newName) {
        $absolutePath = $this->pathGenerator->getAbsolutePath($path);
        
        if (!file_exists($absolutePath) || !is_file($absolutePath)) {
            return false;
        }
        
        $dirName = dirname($absolutePath);
        $newPath = $dirName . '/' . $newName;
        
        return rename($absolutePath, $newPath);
    }
    
    /**
     * Verifica se um arquivo existe
     * 
     * @param string $path Caminho do arquivo
     * @return bool Resultado da verificação
     */
    public function fileExists($path) {
        $absolutePath = $this->pathGenerator->getAbsolutePath($path);
        return file_exists($absolutePath) && is_file($absolutePath);
    }
    
    /**
     * Obtém o tamanho de um arquivo
     * 
     * @param string $path Caminho do arquivo
     * @return int|false Tamanho do arquivo em bytes ou false em caso de erro
     */
    public function getFileSize($path) {
        $absolutePath = $this->pathGenerator->getAbsolutePath($path);
        
        if (!file_exists($absolutePath) || !is_file($absolutePath)) {
            return false;
        }
        
        return filesize($absolutePath);
    }
    
    /**
     * Obtém a data de modificação de um arquivo
     * 
     * @param string $path Caminho do arquivo
     * @return int|false Timestamp Unix ou false em caso de erro
     */
    public function getLastModified($path) {
        $absolutePath = $this->pathGenerator->getAbsolutePath($path);
        
        if (!file_exists($absolutePath) || !is_file($absolutePath)) {
            return false;
        }
        
        return filemtime($absolutePath);
    }
    
    /**
     * Verifica se um arquivo é gravável
     * 
     * @param string $path Caminho do arquivo
     * @return bool Resultado da verificação
     */
    public function isWritable($path) {
        $absolutePath = $this->pathGenerator->getAbsolutePath($path);
        
        if (!file_exists($absolutePath) || !is_file($absolutePath)) {
            return false;
        }
        
        return is_writable($absolutePath);
    }
    
    /**
     * Verifica se um arquivo é legível
     * 
     * @param string $path Caminho do arquivo
     * @return bool Resultado da verificação
     */
    public function isReadable($path) {
        $absolutePath = $this->pathGenerator->getAbsolutePath($path);
        
        if (!file_exists($absolutePath) || !is_file($absolutePath)) {
            return false;
        }
        
        return is_readable($absolutePath);
    }
    
    /**
     * Altera as permissões de um arquivo
     * 
     * @param string $path Caminho do arquivo
     * @param int $permissions Permissões (octal)
     * @return bool Resultado da operação
     */
    public function changePermissions($path, $permissions) {
        $absolutePath = $this->pathGenerator->getAbsolutePath($path);
        
        if (!file_exists($absolutePath) || !is_file($absolutePath)) {
            return false;
        }
        
        return chmod($absolutePath, $permissions);
    }
} 