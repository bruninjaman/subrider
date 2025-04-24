<?php
namespace PathTools\UI;

use PathTools\Core\PathEditorCore;

/**
 * Interface de usuário para o editor de caminhos
 */
class PathEditorUI {
    private $pathEditor;
    private $templatesDir;
    
    /**
     * Construtor da classe
     * 
     * @param PathEditorCore|null $pathEditor Instância do editor de caminhos
     */
    public function __construct($pathEditor = null) {
        $this->pathEditor = $pathEditor ?: new PathEditorCore();
        $this->templatesDir = dirname(__DIR__) . '/ui/templates';
    }
    
    /**
     * Exibe interface para teste de caminho alternativo
     */
    public function displayTestInterface() {
        $jsCode = <<<JS
        // Função para carregar os caminhos de arquivos incluídos
        function loadIncludedFilesPaths(sourceFile) {
            if (!sourceFile) return;
            
            // Mostrar loader
            document.getElementById('included-paths-container').style.display = 'block';
            document.getElementById('included-paths-content').innerHTML = '<p>Carregando arquivos incluídos...</p>';
            
            // Preparar dados para a requisição
            const formData = new FormData();
            formData.append('action', 'find_included_files');
            formData.append('source_file', sourceFile);
            
            // Enviar requisição AJAX
            fetch('path_editor.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.included_files && data.included_files.length > 0) {
                    // Mostrar caminhos dos arquivos incluídos
                    let html = '<h3>Caminhos em Arquivos Incluídos:</h3>';
                    
                    // Agrupar por arquivo incluído
                    data.included_files.forEach(function(fileData, index) {
                        html += '<div class="included-file-section">';
                        html += '<h4>Arquivo Incluído: ' + fileData.file + '</h4>';
                        
                        if (fileData.paths && fileData.paths.length > 0) {
                            html += '<table class="paths-table">';
                            html += '<thead><tr><th>Caminho</th><th>Tipo</th><th>Status</th><th>Ações</th></tr></thead>';
                            html += '<tbody>';
                            
                            fileData.paths.forEach(function(path) {
                                const statusClass = path.exists ? 'path-valid' : 'path-invalid';
                                const statusText = path.exists ? 'Válido' : 'Inválido';
                                
                                html += '<tr class="' + statusClass + '">';
                                html += '<td>' + path.path + '</td>';
                                html += '<td>' + path.type + '</td>';
                                html += '<td class="path-status">' + statusText + '</td>';
                                html += '<td>';
                                
                                if (!path.exists) {
                                    html += '<button class="fix-path-btn" data-source="' + fileData.file + '" data-path="' + path.path + '">Corrigir</button>';
                                }
                                
                                html += '</td>';
                                html += '</tr>';
                            });
                            
                            html += '</tbody></table>';
                        } else {
                            html += '<p>Nenhum caminho encontrado neste arquivo.</p>';
                        }
                        
                        html += '</div>';
                    });
                    
                    document.getElementById('included-paths-content').innerHTML = html;
                    
                    // Adicionar eventos para botões de correção
                    document.querySelectorAll('.fix-path-btn').forEach(function(btn) {
                        btn.addEventListener('click', function() {
                            const sourceFile = this.getAttribute('data-source');
                            const oldPath = this.getAttribute('data-path');
                            
                            // Preencher o formulário com os dados do caminho
                            document.getElementById('source_file').value = sourceFile;
                            document.getElementById('old_path').value = oldPath;
                            document.getElementById('new_path').value = '';
                            
                            // Rolar para o formulário
                            document.getElementById('update-form').scrollIntoView({behavior: 'smooth'});
                        });
                    });
                    
                } else {
                    let message = 'Nenhum arquivo incluído encontrado.';
                    if (!data.success) {
                        message = 'Erro: ' + (data.message || 'Erro ao carregar arquivos incluídos.');
                    }
                    document.getElementById('included-paths-content').innerHTML = '<p>' + message + '</p>';
                }
            })
            .catch(function(error) {
                console.error('Erro:', error);
                document.getElementById('included-paths-content').innerHTML = 
                    '<div class="status status-error">' +
                    '<p>Erro ao processar a solicitação: ' + error + '</p>' +
                    '</div>';
            });
        }
        
        // Adicionar evento para verificar arquivos incluídos quando o arquivo fonte for preenchido
        document.addEventListener('DOMContentLoaded', function() {
            const sourceFileInput = document.getElementById('source_file');
            
            // Evento de change para carregar arquivos incluídos
            sourceFileInput.addEventListener('change', function() {
                loadIncludedFilesPaths(this.value);
            });
            
            // Verificar se já existe um valor ao carregar a página
            if (sourceFileInput.value) {
                loadIncludedFilesPaths(sourceFileInput.value);
            }
        });
        JS;
        
        // CSS adicional para os caminhos de arquivos incluídos
        $cssExtra = <<<CSS
        .included-paths-container {
            margin-top: 30px;
            display: none;
        }
        
        .included-file-section {
            margin-bottom: 20px;
            padding: 10px;
            background-color: #f5f5f5;
            border-left: 4px solid #3498db;
        }
        
        .paths-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        
        .paths-table th, .paths-table td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        
        .paths-table th {
            background-color: #f2f2f2;
            text-align: left;
        }
        
        .path-valid {
            background-color: #d4edda;
        }
        
        .path-invalid {
            background-color: #f8d7da;
        }
        
        .fix-path-btn {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            border-radius: 3px;
        }
        
        .fix-path-btn:hover {
            background-color: #45a049;
        }
        CSS;
        
        // Adicionar HTML para a seção de arquivos incluídos
        $includedPathsHtml = <<<HTML
        <div id="included-paths-container" class="included-paths-container">
            <h2>Caminhos em Arquivos Incluídos</h2>
            <div id="included-paths-content"></div>
        </div>
        HTML;
        
        // Guardar os valores para o template
        $extraJS = $jsCode;
        $extraCSS = $cssExtra;
        $extraHTML = $includedPathsHtml;
        
        include $this->templatesDir . '/test_editor.php';
    }
    
    /**
     * Exibe formulário para correção manual de caminho
     * 
     * @param string $sourceFile Arquivo fonte
     * @param string $oldPath Caminho antigo/atual
     */
    public function displayEditForm($sourceFile, $oldPath) {
        $newPath = $this->pathEditor->findAlternativePath($oldPath, $sourceFile);
        include $this->templatesDir . '/edit_form.php';
    }
    
    /**
     * Exibe resultados da operação de edição
     * 
     * @param array $results Resultados da operação
     */
    public function displayEditResults($results) {
        include $this->templatesDir . '/edit_results.php';
    }
    
    /**
     * Exibe interface para processamento em lote
     * 
     * @param array $items Lista de itens para processar
     */
    public function displayBatchInterface($items) {
        include $this->templatesDir . '/batch_editor.php';
    }
    
    /**
     * Exibe resultados do processamento em lote
     * 
     * @param array $results Resultados do processamento
     */
    public function displayBatchResults($results) {
        include $this->templatesDir . '/batch_results.php';
    }
    
    /**
     * Renderiza um item de resultado do processamento em lote
     * 
     * @param array $result Resultado para renderizar
     * @param int $index Índice do resultado
     * @return string HTML do resultado
     */
    public function renderBatchResultItem($result, $index) {
        $itemClass = $result['success'] ? 'result-item success' : 'result-item failure';
        $statusClass = $result['success'] ? 'status-success' : 'status-failure';
        $statusText = $result['success'] ? 'Sucesso' : 'Falha';
        $message = htmlspecialchars($result['message']);
        $oldPath = htmlspecialchars($result['old_path']);
        $newPath = isset($result['new_path']) ? htmlspecialchars($result['new_path']) : '';
        
        $html = "<div class='$itemClass' id='batch-result-$index'>";
        $html .= "<div class='result-header'>";
        $html .= "<div class='$statusClass'>$statusText</div>";
        $html .= "</div>";
        
        $html .= "<div class='result-content'>";
        $html .= "<div class='result-message'>$message</div>";
        $html .= "<div class='result-path'><strong>Caminho antigo:</strong> $oldPath</div>";
        
        if (!empty($newPath)) {
            $html .= "<div class='result-path'><strong>Caminho novo:</strong> $newPath</div>";
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
    
    /**
     * Retorna uma resposta JSON
     * 
     * @param array $data Dados para resposta
     */
    public function sendJsonResponse($data) {
        // Limpar qualquer buffer existente para evitar contaminação da saída
        while (ob_get_level() > 0) {
            ob_end_clean();
        }
        
        // Garantir que o Content-Type esteja configurado como JSON
        if (!headers_sent()) {
            header('Content-Type: application/json');
        }
        
        // Converter dados para JSON com tratamento de erros
        $json = json_encode($data);
        
        // Verificar se houve erro na codificação
        if (json_last_error() !== JSON_ERROR_NONE) {
            // Converter dados para um formato que pode ser serializado com segurança
            $safeData = $this->makeSafeForJson($data);
            $json = json_encode($safeData);
            
            // Se ainda falhar, retornar um erro básico
            if (json_last_error() !== JSON_ERROR_NONE) {
                $json = json_encode([
                    'success' => false,
                    'message' => 'Erro ao codificar resposta JSON: ' . json_last_error_msg()
                ]);
            }
        }
        
        // Enviar a resposta e encerrar o script
        echo $json;
        exit;
    }
    
    /**
     * Converte dados para formato seguro para JSON
     * 
     * @param mixed $data Dados para converter
     * @return mixed Dados seguros para JSON
     */
    private function makeSafeForJson($data) {
        // Para arrays, processar recursivamente
        if (is_array($data)) {
            $safe = [];
            foreach ($data as $key => $value) {
                $safe[$key] = $this->makeSafeForJson($value);
            }
            return $safe;
        }
        
        // Para objetos, converter para array e processar
        if (is_object($data)) {
            return $this->makeSafeForJson((array)$data);
        }
        
        // Para strings, garantir codificação UTF-8 válida
        if (is_string($data)) {
            // Remover caracteres inválidos de UTF-8
            $data = mb_convert_encoding($data, 'UTF-8', 'UTF-8');
            
            // Se a string for muito longa, truncar
            if (strlen($data) > 1000) {
                $data = substr($data, 0, 1000) . '... (truncado)';
            }
            
            return $data;
        }
        
        // Para recursos, retornar descrição
        if (is_resource($data)) {
            return '[Recurso: ' . get_resource_type($data) . ']';
        }
        
        // Outros tipos (int, float, bool, null) são seguros
        return $data;
    }
} 