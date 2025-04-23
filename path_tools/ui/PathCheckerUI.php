<?php
namespace PathTools\UI;

use PathTools\Core\PathCheckerCore;

/**
 * Interface de usuário para o verificador de caminhos
 */
class PathCheckerUI {
    private $pathChecker;
    private $templatesDir;
    
    /**
     * Construtor da classe
     * 
     * @param PathCheckerCore|null $pathChecker Instância do verificador de caminhos
     */
    public function __construct($pathChecker = null) {
        $this->pathChecker = $pathChecker ?: new PathCheckerCore();
        $this->templatesDir = dirname(__DIR__) . '/ui/templates';
    }
    
    /**
     * Exibe seletor de arquivos
     */
    public function displaySelector() {
        $structure = $this->pathChecker->getDirectoryStructure();
        include $this->templatesDir . '/selector.php';
    }
    
    /**
     * Renderiza a estrutura de diretórios
     * 
     * @param array $items Itens para renderizar
     * @param int $level Nível de profundidade
     */
    public function renderDirectoryTree($items, $level = 0) {
        foreach ($items as $item) {
            $indent = str_repeat('    ', $level);
            $itemId = 'item_' . md5($item['path']);
            
            if ($item['type'] === 'directory') {
                echo "$indent<li class='directory'>";
                echo "<span class='toggle' onclick='toggleDirectory(\"$itemId\")'>▶</span>";
                echo "<label>";
                echo "<input type='checkbox' name='paths[]' value='" . htmlspecialchars($item['path']) . "' onclick='handleCheck(this)'>";
                echo htmlspecialchars($item['name']);
                echo "</label>";
                
                if (!empty($item['children'])) {
                    echo "\n$indent<ul id='$itemId' class='collapsed'>";
                    $this->renderDirectoryTree($item['children'], $level + 1);
                    echo "$indent</ul>";
                }
                
                echo "</li>\n";
            } else {
                echo "$indent<li class='file'>";
                echo "<span class='toggle'></span>";
                echo "<label>";
                echo "<input type='checkbox' name='paths[]' value='" . htmlspecialchars($item['path']) . "' onclick='handleCheck(this)'>";
                echo htmlspecialchars($item['name']);
                echo "</label>";
                echo "</li>\n";
            }
        }
    }
    
    /**
     * Exibe resultados da verificação
     * 
     * @param array $results Resultados para exibir
     */
    public function displayResults($results) {
        $returnUrl = $_SERVER['PHP_SELF'];
        $errorCount = 0;
        $warningCount = 0;
        
        foreach ($results as $result) {
            if (!$result['exists']) {
                $errorCount++;
            } else if (strpos($result['path'], '<?php') !== false) {
                $warningCount++;
            }
        }
        
        // Definir o caminho correto para o path_editor.php
        $pathEditorUrl = $this->getPathEditorUrl();
        
        include $this->templatesDir . '/results.php';
    }
    
    /**
     * Obtém a URL correta para o path_editor.php
     * 
     * @return string URL para o path_editor.php
     */
    private function getPathEditorUrl() {
        // Tentar determinar o caminho absoluto pelo servidor web
        $scriptPath = $_SERVER['SCRIPT_NAME']; // Ex: /subrider/path_tools/path_checker.php
        
        // Verificar se estamos em path_tools ou em algum subdiretório dele
        if (strpos($scriptPath, '/path_tools/') !== false) {
            // Estamos dentro da pasta path_tools, então usar o mesmo diretório
            return dirname($scriptPath) . '/path_editor.php';
        } else {
            // Estamos fora da pasta path_tools
            $baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
            
            // Verificar caminhos possíveis
            $possiblePaths = [
                '/path_tools/path_editor.php',
                dirname($_SERVER['SCRIPT_NAME']) . '/path_tools/path_editor.php'
            ];
            
            foreach ($possiblePaths as $path) {
                $fullPath = rtrim($baseUrl, '/') . '/' . ltrim($path, '/');
                if (@file_get_contents($fullPath, false, stream_context_create(['http' => ['method' => 'HEAD']])) !== false) {
                    return $path;
                }
            }
            
            // Caminho fallback
            return '/path_tools/path_editor.php';
        }
    }
    
    /**
     * Gera e retorna o HTML para um resultado específico
     * 
     * @param array $result Resultado para renderizar
     * @param int $index Índice do resultado
     * @return string HTML do resultado
     */
    public function renderResultItem($result, $index) {
        $itemClass = $result['exists'] ? 'result-item valid' : 'result-item invalid';
        $statusClass = $result['exists'] ? 'status-valid' : 'status-invalid';
        $statusText = $result['exists'] ? 'Válido' : 'Inválido';
        $sourceFile = htmlspecialchars($result['source_file']);
        $path = htmlspecialchars($result['path']);
        $type = htmlspecialchars($result['type']);
        
        $html = "<div class='$itemClass' id='result-$index'>";
        $html .= "<div class='result-header'>";
        $html .= "<div class='result-type'>$type</div>";
        $html .= "<div class='$statusClass'>$statusText</div>";
        $html .= "</div>";
        
        $html .= "<div class='result-content'>";
        $html .= "<div class='result-path'><strong>Caminho:</strong> $path</div>";
        $html .= "<div class='result-source'><strong>Arquivo:</strong> $sourceFile</div>";
        
        if (!$result['exists']) {
            $html .= "<div class='result-actions'>";
            $html .= "<button onclick='showEditForm($index)' class='edit-button'>Editar</button>";
            $html .= "<button onclick='autoFix($index)' class='autofix-button'>AutoFix</button>";
            $html .= "</div>";
            
            $html .= "<div class='edit-form' id='edit-form-$index' style='display:none;'>";
            $html .= "<form onsubmit='updatePath(event, $index)'>";
            $html .= "<input type='hidden' name='source_file' value='$sourceFile'>";
            $html .= "<input type='hidden' name='old_path' value='$path'>";
            $html .= "<div class='form-group'>";
            $html .= "<label for='new-path-$index'>Novo caminho:</label>";
            $html .= "<input type='text' id='new-path-$index' name='new_path' value='$path' required>";
            $html .= "</div>";
            $html .= "<div class='form-actions'>";
            $html .= "<button type='submit' class='save-button'>Salvar</button>";
            $html .= "<button type='button' onclick='hideEditForm($index)' class='cancel-button'>Cancelar</button>";
            $html .= "</div>";
            $html .= "</form>";
            $html .= "</div>";
        }
        
        $html .= "</div>"; // result-content
        $html .= "</div>"; // result-item
        
        return $html;
    }
    
    /**
     * Exibe uma mensagem de erro
     * 
     * @param string $message Mensagem de erro
     */
    public function displayError($message) {
        include $this->templatesDir . '/error.php';
    }
} 