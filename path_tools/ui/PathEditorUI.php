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