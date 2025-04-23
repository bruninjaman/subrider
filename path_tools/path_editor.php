<?php
/**
 * Path Editor - Ferramenta para correção de caminhos quebrados
 * 
 * Este arquivo é o ponto de entrada para o editor de caminhos.
 * Ele processa solicitações para atualizar, testar e corrigir caminhos em arquivos.
 */

// Incluir autoloader
require_once __DIR__ . '/autoload.php';

use PathTools\Core\PathEditorCore;
use PathTools\UI\PathEditorUI;

// Criar log
$logPath = __DIR__ . '/logs';
if (!is_dir($logPath)) {
    mkdir($logPath, 0777, true);
}
$debug_log = fopen($logPath . '/debug_autofix.log', 'a');
function path_log($message) {
    global $debug_log;
    if ($debug_log) {
        fwrite($debug_log, date('[Y-m-d H:i:s] ') . $message . "\n");
    }
}

path_log("=== NOVA REQUISIÇÃO ===");
path_log("Método: " . ($_SERVER['REQUEST_METHOD'] ?? 'UNKNOWN'));
path_log("URI: " . ($_SERVER['REQUEST_URI'] ?? 'UNKNOWN'));
path_log("Script: " . ($_SERVER['SCRIPT_NAME'] ?? 'UNKNOWN'));
path_log("Headers: " . json_encode(getallheaders()));
path_log("POST: " . json_encode($_POST));

// Verificar se é uma requisição AJAX
$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
          strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
path_log("É requisição AJAX: " . ($isAjax ? "Sim" : "Não"));

// Definir que sempre vamos retornar JSON para requisições POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    path_log("Definindo header Content-Type: application/json");
}

// Ativar buffer de saída para capturar quaisquer mensagens de erro
ob_start();

// Configurar manipulador de erros para capturar erros e evitar saída direta
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    // Registrar o erro em um formato que podemos retornar como JSON
    $GLOBALS['error_occurred'] = true;
    $GLOBALS['error_message'] = $errstr;
    $GLOBALS['error_line'] = $errline;
    $GLOBALS['error_file'] = $errfile;
    
    path_log("ERROR: $errstr in $errfile:$errline");
    
    // Retornar true para evitar manipulação de erro padrão
    return true;
});

// Definir flag de erro
$GLOBALS['error_occurred'] = false;

// Iniciar sessão
session_start();

// Criar instâncias
$pathEditor = new PathEditorCore();
$ui = new PathEditorUI($pathEditor);

// Função para garantir que toda saída seja JSON válido
function ensure_json_response() {
    global $debug_log;
    
    // Capturar qualquer saída de buffer
    $output = ob_get_clean();
    
    path_log("Output do buffer: " . substr($output, 0, 200) . (strlen($output) > 200 ? '...' : ''));
    
    // Se um erro ocorreu, enviar resposta de erro
    if ($GLOBALS['error_occurred']) {
        $response = [
            'success' => false,
            'message' => 'Erro PHP: ' . $GLOBALS['error_message'],
            'error_details' => [
                'file' => $GLOBALS['error_file'],
                'line' => $GLOBALS['error_line']
            ]
        ];
        
        // Verificar se já enviamos headers
        if (!headers_sent()) {
            header('Content-Type: application/json');
        }
        
        path_log("Enviando resposta de erro: " . json_encode($response));
        echo json_encode($response);
        
        if ($debug_log) fclose($debug_log);
        exit;
    }
    
    // Para requisições POST, garantir que a saída seja sempre JSON válido
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Se tiver alguma saída não JSON, isso é um erro
        if (!empty($output) && substr($output, 0, 1) !== '{' && substr($output, 0, 1) !== '[') {
            $response = [
                'success' => false,
                'message' => 'Resposta inválida do servidor',
                'debug_output' => $output
            ];
            
            // Verificar se já enviamos headers
            if (!headers_sent()) {
                header('Content-Type: application/json');
            }
            
            path_log("Enviando resposta de erro por saída não-JSON: " . json_encode($response));
            echo json_encode($response);
            
            if ($debug_log) fclose($debug_log);
            exit;
        }
        
        // Se tudo estiver ok, apenas exibe a saída JSON
        path_log("Enviando resposta JSON final: " . substr($output, 0, 200) . (strlen($output) > 200 ? '...' : ''));
        echo $output;
    } else {
        // Para requisições GET, exibir saída normal
        path_log("Enviando resposta HTML final");
        echo $output;
    }
    
    if ($debug_log) fclose($debug_log);
}

// Registrar função para ser chamada quando o script terminar
register_shutdown_function('ensure_json_response');

// Capturar erros fatais
register_shutdown_function(function() {
    $error = error_get_last();
    if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        path_log("ERRO FATAL: " . $error['message'] . " em " . $error['file'] . ":" . $error['line']);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Limpamos qualquer saída já enviada
            ob_clean();
            
            // Verificar se já enviamos headers
            if (!headers_sent()) {
                header('Content-Type: application/json');
            }
            
            echo json_encode([
                'success' => false,
                'message' => 'Erro fatal: ' . $error['message'],
                'error_details' => [
                    'file' => $error['file'],
                    'line' => $error['line']
                ]
            ]);
        }
    }
});

// Processar requisições
try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Para teste de AutoFix
        if (isset($_POST['test_autofix']) && $_POST['test_autofix'] === 'true') {
            path_log("Processando requisição de teste autofix");
            $path = $_POST['path'] ?? $_POST['old_path'];
            $sourceFile = $_POST['source_file'];
            
            if (empty($path) || empty($sourceFile) || !file_exists($sourceFile)) {
                $errorMessage = empty($path) ? "Caminho vazio" : 
                              (!file_exists($sourceFile) ? "Arquivo fonte não existe: $sourceFile" : "Erro desconhecido");
                
                path_log("Erro em test_autofix: $errorMessage");
                $ui->sendJsonResponse([
                    'success' => false,
                    'message' => $errorMessage,
                    'debug_info' => [
                        'path' => $path,
                        'source_file' => $sourceFile
                    ]
                ]);
            }
            
            // Encontrar caminho alternativo
            $newPath = $pathEditor->findAlternativePath($path, $sourceFile);
            path_log("Caminho alternativo encontrado: $newPath");
            
            $ui->sendJsonResponse([
                'success' => true,
                'new_path' => $newPath,
                'exists' => $pathEditor->checkFileExists($newPath, $sourceFile),
                'method_used' => 'findAlternativePath()'
            ]);
        }
        
        // Para processamento em lote
        if (isset($_POST['autofix']) && $_POST['autofix'] === 'true') {
            path_log("Processando requisição de autofix em lote");
            
            if (!isset($_POST['items'])) {
                path_log("ERRO: Parâmetro 'items' não encontrado");
                $ui->sendJsonResponse([
                    'success' => false,
                    'message' => 'Parâmetro items não encontrado na requisição'
                ]);
            }
            
            path_log("JSON items recebido: " . $_POST['items']);
            $items = json_decode($_POST['items'], true);
            $returnUrl = isset($_POST['return_url']) ? $_POST['return_url'] : '';
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                path_log("ERRO: Falha ao decodificar JSON: " . json_last_error_msg());
                $ui->sendJsonResponse([
                    'success' => false,
                    'message' => 'Erro ao decodificar JSON: ' . json_last_error_msg()
                ]);
            }
            
            path_log("Items decodificados: " . json_encode($items));
            $result = $pathEditor->processBatch($items);
            path_log("Resultado do processBatch: " . json_encode($result));
            $result['return_url'] = $returnUrl;
            
            $ui->sendJsonResponse($result);
        }
        
        // Processamento individual
        if (isset($_POST['source_file']) && isset($_POST['old_path'])) {
            path_log("Processando requisição individual");
            $sourceFile = $_POST['source_file'];
            $oldPath = $_POST['old_path'];
            $newPath = $_POST['new_path'] ?? null;
            $returnUrl = isset($_POST['return_url']) ? $_POST['return_url'] : '';
            
            // Se o novo caminho não foi fornecido, encontrar automaticamente
            if (empty($newPath)) {
                path_log("Novo caminho não fornecido, buscando automaticamente");
                $newPath = $pathEditor->findAlternativePath($oldPath, $sourceFile);
            }
            
            if (empty($newPath)) {
                path_log("ERRO: Não foi possível determinar novo caminho");
                $ui->sendJsonResponse([
                    'success' => false,
                    'message' => 'Não foi possível determinar um novo caminho válido',
                    'return_url' => $returnUrl
                ]);
            }
            
            path_log("Atualizando caminho $oldPath para $newPath");
            $result = $pathEditor->updatePath($sourceFile, $oldPath, $newPath);
            $result['return_url'] = $returnUrl;
            
            // Se a atualização foi bem-sucedida e temos resultados na sessão, atualizar também
            if ($result['success'] && isset($_SESSION['path_checker_results'])) {
                $results = $_SESSION['path_checker_results'];
                foreach ($results as $key => $resultItem) {
                    if ($resultItem['source_file'] === $sourceFile && $resultItem['path'] === $oldPath) {
                        $results[$key]['path'] = $newPath;
                        $results[$key]['exists'] = $result['exists'] ?? false;
                    }
                }
                $_SESSION['path_checker_results'] = $results;
            }
            
            path_log("Enviando resultado da atualização: " . json_encode($result));
            $ui->sendJsonResponse($result);
        }
        
        // Se nenhuma das condições acima for atendida, retornar erro
        path_log("ERRO: Requisição não reconhecida");
        $ui->sendJsonResponse([
            'success' => false,
            'message' => 'Requisição inválida'
        ]);
    } else {
        // Exibir interface para teste de caminhos
        path_log("Exibindo interface de teste");
        $ui->displayTestInterface();
    }
} catch (Exception $e) {
    // Capturar qualquer exceção
    path_log("EXCEÇÃO: " . $e->getMessage() . " em " . $e->getFile() . ":" . $e->getLine());
    $ui->sendJsonResponse([
        'success' => false,
        'message' => 'Exceção: ' . $e->getMessage(),
        'error_details' => [
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]
    ]);
} 