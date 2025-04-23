<?php
namespace PathTools\Core;

/**
 * Classe principal para verificação de caminhos em arquivos
 */
class PathCheckerCore {
    private $patterns = [
        'css' => '/href=[\'"]([^\'"]+\.css)[\'"]/',
        'js' => '/src=[\'"]([^\'"]+\.js)[\'"]/',
        'ajax' => '/url:\s*[\'"]([^\'"]+)[\'"]/',
        'images' => '/src=[\'"]([^\'"]+\.(jpg|jpeg|png|gif|svg))[\'"]/',
        'php' => '/include[_once]*\([\'"]([^\'"]+\.php)[\'"]\)/',
        'require' => '/require[_once]*\([\'"]([^\'"]+\.php)[\'"]\)/',
    ];

    public $results = [];
    private $scannedFiles = [];
    public $selectedPaths = [];
    public $totalFiles = 0;
    public $processedFiles = 0;
    private $debugLog = [];
    private $projectRoot;

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
     * Obtém a estrutura de diretórios para seleção
     * 
     * @param string $dir Diretório para obter a estrutura
     * @param string $relativePath Caminho relativo para montar a estrutura
     * @return array Estrutura de diretórios em formato array
     */
    public function getDirectoryStructure($dir = null, $relativePath = '') {
        $dir = $dir ?: $this->projectRoot;
        $structure = [];
        $items = glob($dir . '/*');
        
        foreach ($items as $item) {
            $name = basename($item);
            $relPath = $relativePath ? "$relativePath/$name" : $name;
            
            // Ignorar diretórios específicos
            if (in_array($name, ['vendor', 'node_modules', '.git', '.vs', '.cursor'])) {
                continue;
            }
            
            if (is_dir($item)) {
                $structure[] = [
                    'type' => 'directory',
                    'name' => $name,
                    'path' => $relPath,
                    'children' => $this->getDirectoryStructure($item, $relPath)
                ];
            } else if (preg_match('/\.(php|html|js|css)$/', $name)) {
                $structure[] = [
                    'type' => 'file',
                    'name' => $name,
                    'path' => $relPath
                ];
            }
        }
        
        return $structure;
    }

    /**
     * Conta o número de arquivos para escanear
     * 
     * @param array $paths Caminhos selecionados para varredura
     * @return int Número total de arquivos
     */
    public function countScanFiles($paths) {
        $this->totalFiles = 0;
        
        foreach ($paths as $path) {
            $fullPath = $this->projectRoot . '/' . $path;
            
            if (is_file($fullPath) && preg_match('/\.(php|html|js)$/', $fullPath)) {
                $this->totalFiles++;
            } else if (is_dir($fullPath)) {
                // Em vez de contar todos os arquivos no diretório,
                // contamos apenas os arquivos explicitamente selecionados dentro dele
                foreach ($paths as $potentialFile) {
                    if (strpos($potentialFile, $path . '/') === 0) {
                        $fullFilePath = $this->projectRoot . '/' . $potentialFile;
                        if (is_file($fullFilePath) && preg_match('/\.(php|html|js)$/', $fullFilePath)) {
                            $this->totalFiles++;
                        }
                    }
                }
            }
        }
        
        return $this->totalFiles;
    }

    /**
     * Escaneia os caminhos selecionados
     * 
     * @param array $paths Caminhos para escanear
     * @return array Resultados do escaneamento
     */
    public function scanSelectedPaths($paths) {
        $this->results = [];
        $this->scannedFiles = [];
        $this->selectedPaths = $paths;
        $this->processedFiles = 0;
        $this->debugLog = []; // Reset do log de depuração
        
        $this->logDebug("Iniciando verificação com " . count($paths) . " caminhos selecionados");
        
        // Lista de arquivos explicitamente selecionados
        $explicitlySelectedFiles = [];
        
        // Primeiro, separamos apenas os arquivos selecionados explicitamente
        foreach ($paths as $path) {
            $fullPath = $this->projectRoot . '/' . $path;
            $this->logDebug("Verificando caminho selecionado: $path");
            
            if (is_file($fullPath) && preg_match('/\.(php|html|js)$/', $fullPath)) {
                $explicitlySelectedFiles[] = $fullPath;
                $this->logDebug("Arquivo explicitamente selecionado: $fullPath");
            } elseif (is_dir($fullPath)) {
                $this->logDebug("Diretório selecionado: $fullPath");
                
                // Se for um diretório, apenas procuramos se algum arquivo dentro dele foi selecionado explicitamente
                foreach ($paths as $potentialFile) {
                    if (strpos($potentialFile, $path . '/') === 0) {
                        $fullFilePath = $this->projectRoot . '/' . $potentialFile;
                        if (is_file($fullFilePath) && preg_match('/\.(php|html|js)$/', $fullFilePath)) {
                            $explicitlySelectedFiles[] = $fullFilePath;
                            $this->logDebug("Arquivo selecionado (dentro do diretório): $fullFilePath");
                        }
                    }
                }
            }
        }
        
        $this->logDebug("Total de " . count($explicitlySelectedFiles) . " arquivos selecionados para escaneamento");
        
        // Agora, escaneamos apenas os arquivos explicitamente selecionados
        foreach ($explicitlySelectedFiles as $file) {
            if (!in_array($file, $this->scannedFiles)) {
                $this->scannedFiles[] = $file;
                $this->scanFile($file);
                $this->processedFiles++;
                $this->logDebug("Arquivo escaneado: $file");
            }
        }
        
        $this->logDebug("Verificação concluída. " . count($this->results) . " caminhos encontrados.");
        return $this->results;
    }

    /**
     * Escaneia um arquivo em busca de caminhos
     * 
     * @param string $file Caminho do arquivo para escanear
     */
    public function scanFile($file) {
        $content = file_get_contents($file);
        foreach ($this->patterns as $type => $pattern) {
            preg_match_all($pattern, $content, $matches);
            if (!empty($matches[1])) {
                foreach ($matches[1] as $path) {
                    $this->checkPath($path, $file, $type);
                }
            }
        }
    }

    /**
     * Verifica se um caminho existe
     * 
     * @param string $path Caminho para verificar
     * @param string $sourceFile Arquivo fonte onde o caminho foi encontrado
     * @param string $type Tipo de caminho (css, js, etc)
     */
    private function checkPath($path, $sourceFile, $type) {
        $originalPath = $path;
        
        // Convertemos o caminho para absoluto
        if (strpos($path, 'http') !== 0 && strpos($path, '//') !== 0) {
            if (strpos($path, '/') === 0) {
                $path = $this->projectRoot . $path;
            } else {
                $path = dirname($sourceFile) . '/' . $path;
            }
        }

        $exists = false;
        if (strpos($path, 'http') === 0 || strpos($path, '//') === 0) {
            // Para URLs externas, consideramos como válido por padrão
            $exists = true;
        } else {
            $exists = file_exists($path);
        }

        // Adicionamos o resultado para o caminho encontrado
        $this->results[] = [
            'path' => $originalPath,
            'source_file' => $sourceFile,
            'type' => $type,
            'exists' => $exists
        ];
        
        $this->logDebug("Caminho encontrado: $originalPath (Existe: " . ($exists ? "Sim" : "Não") . ")");
    }
    
    /**
     * Retorna o progresso atual do escaneamento
     * 
     * @return float Porcentagem de progresso
     */
    public function getProgress() {
        return $this->totalFiles > 0 ? ($this->processedFiles / $this->totalFiles) * 100 : 0;
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