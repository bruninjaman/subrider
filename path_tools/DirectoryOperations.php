<?php
namespace PathTools;

/**
 * Classe responsável por operações em diretórios
 */
class DirectoryOperations {
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
     * Cria um diretório
     * 
     * @param string $path Caminho do diretório
     * @param int $permissions Permissões do diretório (octal)
     * @param bool $recursive Criar diretórios recursivamente
     * @return bool Resultado da operação
     */
    public function createDirectory($path, $permissions = 0755, $recursive = true) {
        $absolutePath = $this->pathGenerator->getAbsolutePath($path);
        
        if (is_dir($absolutePath)) {
            return true;
        }
        
        $result = mkdir($absolutePath, $permissions, $recursive);
        
        return $result;
    }
    
    /**
     * Lista conteúdo de um diretório
     * 
     * @param string $path Caminho do diretório
     * @param bool $includeHidden Incluir arquivos ocultos
     * @return array|false Lista de arquivos e diretórios ou false em caso de erro
     */
    public function listDirectory($path, $includeHidden = false) {
        $absolutePath = $this->pathGenerator->getAbsolutePath($path);
        
        if (!is_dir($absolutePath) || !is_readable($absolutePath)) {
            return false;
        }
        
        $items = scandir($absolutePath);
        
        if (!$includeHidden) {
            // Remove . e .. e arquivos ocultos
            $items = array_filter($items, function($item) {
                return !in_array($item, ['.', '..']) && $item[0] !== '.';
            });
        } else {
            // Remove apenas . e ..
            $items = array_filter($items, function($item) {
                return !in_array($item, ['.', '..']);
            });
        }
        
        return array_values($items);
    }
    
    /**
     * Lista arquivos de um diretório
     * 
     * @param string $path Caminho do diretório
     * @param string $extension Filtrar por extensão (opcional)
     * @param bool $includeHidden Incluir arquivos ocultos
     * @return array|false Lista de arquivos ou false em caso de erro
     */
    public function listFiles($path, $extension = null, $includeHidden = false) {
        $absolutePath = $this->pathGenerator->getAbsolutePath($path);
        
        if (!is_dir($absolutePath) || !is_readable($absolutePath)) {
            return false;
        }
        
        $items = $this->listDirectory($path, $includeHidden);
        
        if (!$items) {
            return [];
        }
        
        $files = array_filter($items, function($item) use ($absolutePath, $extension) {
            $fullPath = $absolutePath . '/' . $item;
            
            if (!is_file($fullPath)) {
                return false;
            }
            
            if ($extension !== null) {
                return pathinfo($fullPath, PATHINFO_EXTENSION) === ltrim($extension, '.');
            }
            
            return true;
        });
        
        return array_values($files);
    }
    
    /**
     * Lista subdiretórios de um diretório
     * 
     * @param string $path Caminho do diretório
     * @param bool $includeHidden Incluir diretórios ocultos
     * @return array|false Lista de diretórios ou false em caso de erro
     */
    public function listDirectories($path, $includeHidden = false) {
        $absolutePath = $this->pathGenerator->getAbsolutePath($path);
        
        if (!is_dir($absolutePath) || !is_readable($absolutePath)) {
            return false;
        }
        
        $items = $this->listDirectory($path, $includeHidden);
        
        if (!$items) {
            return [];
        }
        
        $directories = array_filter($items, function($item) use ($absolutePath) {
            $fullPath = $absolutePath . '/' . $item;
            return is_dir($fullPath);
        });
        
        return array_values($directories);
    }
    
    /**
     * Copia um diretório para outro local
     * 
     * @param string $source Caminho do diretório de origem
     * @param string $destination Caminho do diretório de destino
     * @param int $permissions Permissões dos novos diretórios (octal)
     * @return bool Resultado da operação
     */
    public function copyDirectory($source, $destination, $permissions = 0755) {
        $absoluteSource = $this->pathGenerator->getAbsolutePath($source);
        $absoluteDestination = $this->pathGenerator->getAbsolutePath($destination);
        
        if (!is_dir($absoluteSource)) {
            return false;
        }
        
        // Cria o diretório de destino se não existir
        if (!is_dir($absoluteDestination)) {
            if (!$this->createDirectory($destination, $permissions)) {
                return false;
            }
        }
        
        // Obtém o conteúdo do diretório de origem
        $items = $this->listDirectory($source, true);
        
        if (!$items) {
            return true; // Diretório vazio
        }
        
        $fileOperations = new FileOperations($this->pathGenerator);
        $success = true;
        
        foreach ($items as $item) {
            $sourceItem = $absoluteSource . '/' . $item;
            $destinationItem = $absoluteDestination . '/' . $item;
            
            if (is_dir($sourceItem)) {
                // Copia recursivamente os subdiretórios
                $success = $success && $this->copyDirectory($sourceItem, $destinationItem, $permissions);
            } else {
                // Copia os arquivos
                $success = $success && $fileOperations->copyFile($sourceItem, $destinationItem);
            }
        }
        
        return $success;
    }
    
    /**
     * Remove um diretório
     * 
     * @param string $path Caminho do diretório
     * @param bool $recursive Remover conteúdo recursivamente
     * @return bool Resultado da operação
     */
    public function deleteDirectory($path, $recursive = true) {
        $absolutePath = $this->pathGenerator->getAbsolutePath($path);
        
        if (!is_dir($absolutePath)) {
            return false;
        }
        
        if (!$recursive) {
            // Verifica se o diretório está vazio
            $items = $this->listDirectory($path, true);
            
            if (count($items) > 0) {
                return false;
            }
            
            return rmdir($absolutePath);
        }
        
        // Obtém o conteúdo do diretório
        $items = $this->listDirectory($path, true);
        
        if ($items === false) {
            return false;
        }
        
        $fileOperations = new FileOperations($this->pathGenerator);
        $success = true;
        
        foreach ($items as $item) {
            $itemPath = $absolutePath . '/' . $item;
            
            if (is_dir($itemPath)) {
                // Remove recursivamente os subdiretórios
                $success = $success && $this->deleteDirectory($itemPath, true);
            } else {
                // Remove os arquivos
                $success = $success && $fileOperations->deleteFile($itemPath);
            }
        }
        
        // Remove o diretório principal
        return $success && rmdir($absolutePath);
    }
    
    /**
     * Move um diretório para outro local
     * 
     * @param string $source Caminho do diretório de origem
     * @param string $destination Caminho do diretório de destino
     * @return bool Resultado da operação
     */
    public function moveDirectory($source, $destination) {
        $absoluteSource = $this->pathGenerator->getAbsolutePath($source);
        $absoluteDestination = $this->pathGenerator->getAbsolutePath($destination);
        
        if (!is_dir($absoluteSource)) {
            return false;
        }
        
        // Tenta mover usando rename (mais eficiente)
        if (@rename($absoluteSource, $absoluteDestination)) {
            return true;
        }
        
        // Se o rename falhar, copia e depois remove o original
        if ($this->copyDirectory($source, $destination)) {
            return $this->deleteDirectory($source, true);
        }
        
        return false;
    }
    
    /**
     * Verifica se um diretório existe
     * 
     * @param string $path Caminho do diretório
     * @return bool Resultado da verificação
     */
    public function directoryExists($path) {
        $absolutePath = $this->pathGenerator->getAbsolutePath($path);
        return is_dir($absolutePath);
    }
    
    /**
     * Verifica se um diretório está vazio
     * 
     * @param string $path Caminho do diretório
     * @return bool Resultado da verificação (true = vazio)
     */
    public function isDirectoryEmpty($path) {
        $absolutePath = $this->pathGenerator->getAbsolutePath($path);
        
        if (!is_dir($absolutePath)) {
            return false;
        }
        
        $items = scandir($absolutePath);
        return count($items) <= 2; // . e ..
    }
    
    /**
     * Obtém o tamanho total de um diretório
     * 
     * @param string $path Caminho do diretório
     * @return int|false Tamanho em bytes ou false em caso de erro
     */
    public function getDirectorySize($path) {
        $absolutePath = $this->pathGenerator->getAbsolutePath($path);
        
        if (!is_dir($absolutePath)) {
            return false;
        }
        
        $size = 0;
        $items = $this->listDirectory($path, true);
        
        if ($items === false) {
            return false;
        }
        
        foreach ($items as $item) {
            $itemPath = $absolutePath . '/' . $item;
            
            if (is_dir($itemPath)) {
                // Adiciona o tamanho dos subdiretórios recursivamente
                $dirSize = $this->getDirectorySize($itemPath);
                
                if ($dirSize === false) {
                    return false;
                }
                
                $size += $dirSize;
            } else {
                // Adiciona o tamanho dos arquivos
                $fileSize = filesize($itemPath);
                
                if ($fileSize === false) {
                    return false;
                }
                
                $size += $fileSize;
            }
        }
        
        return $size;
    }
    
    /**
     * Altera as permissões de um diretório
     * 
     * @param string $path Caminho do diretório
     * @param int $permissions Permissões (octal)
     * @param bool $recursive Aplicar recursivamente
     * @return bool Resultado da operação
     */
    public function changePermissions($path, $permissions, $recursive = false) {
        $absolutePath = $this->pathGenerator->getAbsolutePath($path);
        
        if (!is_dir($absolutePath)) {
            return false;
        }
        
        // Altera as permissões do diretório principal
        $success = chmod($absolutePath, $permissions);
        
        if (!$recursive) {
            return $success;
        }
        
        // Altera as permissões recursivamente
        $items = $this->listDirectory($path, true);
        
        if ($items === false) {
            return false;
        }
        
        $fileOperations = new FileOperations($this->pathGenerator);
        
        foreach ($items as $item) {
            $itemPath = $absolutePath . '/' . $item;
            
            if (is_dir($itemPath)) {
                // Aplica as permissões aos subdiretórios recursivamente
                $success = $success && $this->changePermissions($itemPath, $permissions, true);
            } else {
                // Aplica as permissões aos arquivos
                $success = $success && $fileOperations->changePermissions($itemPath, $permissions);
            }
        }
        
        return $success;
    }
} 