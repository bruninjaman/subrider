<?php
session_start();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
        if (strpos($newPath, 'http') === 0 || strpos($newPath, '//') === 0) {
            $response['exists'] = true;
        } else {
            $absolutePath = strpos($newPath, '/') === 0 
                ? dirname(__DIR__) . $newPath 
                : dirname($sourceFile) . '/' . $newPath;
            $response['exists'] = file_exists($absolutePath);
        }
        
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
?> 