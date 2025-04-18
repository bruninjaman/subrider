<?php
// Ativar buffer de saída para capturar quaisquer mensagens de erro
ob_start();

// Configurar manipulador de erros para capturar erros e evitar saída direta
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    // Registrar o erro em um formato que podemos retornar como JSON
    $GLOBALS['error_occurred'] = true;
    $GLOBALS['error_message'] = $errstr;
    $GLOBALS['error_line'] = $errline;
    $GLOBALS['error_file'] = $errfile;
    
    // Retornar true para evitar manipulação de erro padrão
    return true;
});

// Definir flag de erro
$GLOBALS['error_occurred'] = false;

session_start();
header('Content-Type: application/json');

// Função auxiliar para verificar se um arquivo existe
function check_file_exists($path, $sourceFile) {
    if (strpos($path, 'http') === 0 || strpos($path, '//') === 0) {
        return true;
    } else {
        $absolutePath = strpos($path, '/') === 0 
            ? dirname(__DIR__) . $path 
            : dirname($sourceFile) . '/' . $path;
        return file_exists($absolutePath);
    }
}

// Função recursiva para procurar o arquivo
function find_file_recursive($dir, $filename, $maxDepth = 3, $currentDepth = 0) {
    if ($currentDepth > $maxDepth) return null;
    
    // Verificamos se o arquivo existe diretamente nesta pasta
    $potentialPath = $dir . '/' . $filename;
    if (file_exists($potentialPath)) {
        return $potentialPath;
    }
    
    // Procuramos em todas as subpastas
    $subdirs = glob($dir . '/*', GLOB_ONLYDIR);
    foreach ($subdirs as $subdir) {
        $result = find_file_recursive($subdir, $filename, $maxDepth, $currentDepth + 1);
        if ($result) return $result;
    }
    
    return null;
}

// Função para encontrar caminho alternativo válido
function find_alternative_path($oldPath, $sourceFile) {
    // Removemos qualquer parâmetro de query string que possa existir
    $pathParts = explode('?', $oldPath);
    $cleanPath = $pathParts[0];
    
    // Extraímos apenas o nome do arquivo
    $filename = basename($cleanPath);
    
    // Procuramos pelo arquivo na pasta do arquivo fonte e subpastas
    $sourceDirRoot = dirname($sourceFile);
    
    $foundPath = find_file_recursive($sourceDirRoot, $filename);
    
    if ($foundPath) {
        // Convertemos para caminho relativo ao arquivo fonte
        $relPath = substr($foundPath, strlen($sourceDirRoot) + 1);
        return $relPath;
    }
    
    return null;
}

// Função para garantir que toda saída seja JSON válido
function ensure_json_response() {
    // Capturar qualquer saída de buffer
    $output = ob_get_clean();
    
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
        echo json_encode($response);
        exit;
    }
    
    // Se tiver alguma saída não JSON, isso é um erro
    if (!empty($output) && substr($output, 0, 1) !== '{' && substr($output, 0, 1) !== '[') {
        $response = [
            'success' => false,
            'message' => 'Resposta inválida do servidor',
            'debug_output' => $output
        ];
        echo json_encode($response);
        exit;
    }
    
    // Se tudo estiver ok, apenas exibe a saída
    echo $output;
}

// Registrar função para ser chamada quando o script terminar
register_shutdown_function('ensure_json_response');

// Processar a requisição
try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Para processamento em lote
        if (isset($_POST['autofix']) && $_POST['autofix'] === 'true') {
            $items = json_decode($_POST['items'], true);
            $returnUrl = isset($_POST['return_url']) ? $_POST['return_url'] : '';
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                $response = [
                    'success' => false,
                    'message' => 'Erro ao decodificar JSON: ' . json_last_error_msg()
                ];
                echo json_encode($response);
                exit;
            }
            
            $results = [];
            $totalFixed = 0;
            
            foreach ($items as $item) {
                $sourceFile = $item['source_file'];
                $oldPath = $item['path'];
                
                // Tenta encontrar um caminho alternativo
                $newPath = find_alternative_path($oldPath, $sourceFile);
                
                if ($newPath && !check_file_exists($oldPath, $sourceFile)) {
                    // Lê o conteúdo do arquivo
                    $content = file_get_contents($sourceFile);
                    if ($content === false) {
                        $results[] = [
                            'success' => false,
                            'message' => "Não foi possível ler o arquivo: $sourceFile",
                            'old_path' => $oldPath,
                            'new_path' => null
                        ];
                        continue;
                    }
                    
                    // Escapa caracteres especiais para uso em regex
                    $escapedOldPath = preg_quote($oldPath, '/');
                    
                    // Substitui o path antigo pelo novo, mantendo as aspas originais
                    $pattern = '/([\'"])'.$escapedOldPath.'([\'"])/';
                    $replacement = '$1'.$newPath.'$2';
                    $newContent = preg_replace($pattern, $replacement, $content);
                    
                    // Salva o arquivo
                    if (file_put_contents($sourceFile, $newContent) !== false) {
                        $totalFixed++;
                        $results[] = [
                            'success' => true,
                            'message' => "Path atualizado com sucesso!",
                            'old_path' => $oldPath,
                            'new_path' => $newPath,
                            'exists' => true
                        ];
                        
                        // Atualiza sessão se necessário
                        if (isset($_SESSION['path_checker_results'])) {
                            $sessionResults = $_SESSION['path_checker_results'];
                            foreach ($sessionResults as $key => $result) {
                                if ($result['source_file'] === $sourceFile && $result['path'] === $oldPath) {
                                    $sessionResults[$key]['path'] = $newPath;
                                    $sessionResults[$key]['exists'] = true;
                                }
                            }
                            $_SESSION['path_checker_results'] = $sessionResults;
                        }
                    } else {
                        $results[] = [
                            'success' => false,
                            'message' => "Erro ao atualizar o arquivo: $sourceFile",
                            'old_path' => $oldPath,
                            'new_path' => $newPath
                        ];
                    }
                } else {
                    $results[] = [
                        'success' => false,
                        'message' => "Não foi possível encontrar um caminho alternativo para: $oldPath",
                        'old_path' => $oldPath,
                        'new_path' => null
                    ];
                }
            }
            
            $response = [
                'success' => $totalFixed > 0,
                'message' => $totalFixed > 0 ? "Foram consertados $totalFixed de " . count($items) . " caminhos." : "Não foi possível consertar nenhum caminho.",
                'results' => $results,
                'return_url' => $returnUrl
            ];
            
            echo json_encode($response);
            exit;
        }
        
        // Processamento individual
        $sourceFile = $_POST['source_file'];
        $oldPath = $_POST['old_path'];
        $newPath = $_POST['new_path'];
        $returnUrl = isset($_POST['return_url']) ? $_POST['return_url'] : '';

        $response = [
            'success' => false,
            'message' => '',
            'exists' => false,
            'return_url' => $returnUrl
        ];

        // Validação básica
        if (empty($sourceFile) || empty($oldPath) || empty($newPath)) {
            $response['message'] = 'Parâmetros inválidos';
            echo json_encode($response);
            exit;
        }

        // Lê o conteúdo do arquivo
        $content = file_get_contents($sourceFile);
        if ($content === false) {
            $response['message'] = 'Não foi possível ler o arquivo fonte';
            echo json_encode($response);
            exit;
        }

        // Escapa caracteres especiais para uso em regex
        $oldPath = preg_quote($oldPath, '/');
        
        // Substitui o path antigo pelo novo, mantendo as aspas originais
        $pattern = '/([\'"])'.$oldPath.'([\'"])/';
        $replacement = '$1'.$newPath.'$2';
        $newContent = preg_replace($pattern, $replacement, $content);

        // Salva o arquivo
        if (file_put_contents($sourceFile, $newContent) !== false) {
            $response['success'] = true;
            $response['message'] = "Path atualizado com sucesso!";
            
            // Verifica se o novo path existe
            $response['exists'] = check_file_exists($newPath, $sourceFile);
            
            // Se houver resultados na sessão, vamos atualizar também
            if (isset($_SESSION['path_checker_results'])) {
                $results = $_SESSION['path_checker_results'];
                foreach ($results as $key => $result) {
                    if ($result['source_file'] === $sourceFile && $result['path'] === $_POST['old_path']) {
                        $results[$key]['path'] = $newPath;
                        $results[$key]['exists'] = $response['exists'];
                    }
                }
                $_SESSION['path_checker_results'] = $results;
            }
        } else {
            $response['message'] = "Erro ao atualizar o arquivo";
        }

        echo json_encode($response);
        exit;
    }

    // Se não for POST, retorna erro
    $response = [
        'success' => false,
        'message' => 'Método não permitido'
    ];
    echo json_encode($response);
    exit;
} catch (Exception $e) {
    // Capturar qualquer exceção
    $response = [
        'success' => false,
        'message' => 'Exceção: ' . $e->getMessage(),
        'error_details' => [
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]
    ];
    echo json_encode($response);
    exit;
}
?> 