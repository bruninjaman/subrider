<?php
// Start output buffering to catch any unintended output
ob_start();

// Set JSON header
header('Content-Type: application/json');

// Initialize response
$response = [
    'success' => false,
    'message' => '',
    'results' => []
];

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método não permitido');
    }

    // Handle bulk updates (JSON payload)
    $input = json_decode(file_get_contents('php://input'), true);
    if (isset($input['updates']) && is_array($input['updates'])) {
        $results = [];
        $successCount = 0;
        $total = count($input['updates']);
        
        foreach ($input['updates'] as $update) {
            $sourceFile = $update['source_file'] ?? '';
            $oldPath = $update['old_path'] ?? '';
            $newPath = $update['new_path'] ?? '';
            
            if (empty($sourceFile) || empty($oldPath) || empty($newPath)) {
                $results[$sourceFile . '|' . $oldPath] = [
                    'success' => false,
                    'exists' => false,
                    'message' => 'Parâmetros inválidos'
                ];
                continue;
            }
            
            $content = @file_get_contents($sourceFile);
            if ($content === false) {
                $results[$sourceFile . '|' . $oldPath] = [
                    'success' => false,
                    'exists' => false,
                    'message' => "Não foi possível ler o arquivo $sourceFile"
                ];
                continue;
            }
            
            // Replace the entire old path, preserving quotes
            $oldPathEscaped = preg_quote($oldPath, '/');
            $pattern = '/([\'"])'.$oldPathEscaped.'([\'"])/';
            $replacement = '$1'.$newPath.'$2';
            $newContent = preg_replace($pattern, $replacement, $content);
            
            if ($newContent === $content) {
                $results[$sourceFile . '|' . $oldPath] = [
                    'success' => false,
                    'exists' => false,
                    'message' => "Nenhuma substituição realizada em $sourceFile"
                ];
                continue;
            }
            
            if (@file_put_contents($sourceFile, $newContent) !== false) {
                $exists = false;
                if (strpos($newPath, 'http') === 0 || strpos($newPath, '//') === 0) {
                    $exists = true;
                } else {
                    $absolutePath = strpos($newPath, '/') === 0 
                        ? __DIR__ . $newPath 
                        : dirname($sourceFile) . '/' . $newPath;
                    $absolutePath = realpath($absolutePath) ?: $absolutePath;
                    $exists = file_exists($absolutePath);
                }
                
                $results[$sourceFile . '|' . $oldPath] = [
                    'success' => true,
                    'exists' => $exists,
                    'message' => "Path atualizado em $sourceFile"
                ];
                $successCount++;
            } else {
                $results[$sourceFile . '|' . $oldPath] = [
                    'success' => false,
                    'exists' => false,
                    'message' => "Erro ao atualizar o arquivo $sourceFile"
                ];
            }
        }
        
        $response['success'] = $successCount > 0;
        $response['message'] = "$successCount de $total paths atualizados com sucesso";
        $response['results'] = $results;
    } else {
        // Handle single update (form submission)
        $sourceFile = $_POST['source_file'] ?? '';
        $oldPath = $_POST['old_path'] ?? '';
        $newPath = $_POST['new_path'] ?? '';
        
        if (empty($sourceFile) || empty($oldPath) || empty($newPath)) {
            throw new Exception('Parâmetros inválidos');
        }
        
        $content = @file_get_contents($sourceFile);
        if ($content === false) {
            throw new Exception('Não foi possível ler o arquivo fonte');
        }
        
        // Replace the entire old path, preserving quotes
        $oldPathEscaped = preg_quote($oldPath, '/');
        $pattern = '/([\'"])'.$oldPathEscaped.'([\'"])/';
        $replacement = '$1'.$newPath.'$2';
        $newContent = preg_replace($pattern, $replacement, $content);
        
        if ($newContent === $content) {
            throw new Exception('Nenhuma substituição realizada');
        }
        
        if (@file_put_contents($sourceFile, $newContent) !== false) {
            $response['success'] = true;
            $response['message'] = 'Path atualizado com sucesso!';
            
            if (strpos($newPath, 'http') === 0 || strpos($newPath, '//') === 0) {
                $response['exists'] = true;
            } else {
                $absolutePath = strpos($newPath, '/') === 0 
                    ? __DIR__ . $newPath 
                    : dirname($sourceFile) . '/' . $newPath;
                $absolutePath = realpath($absolutePath) ?: $absolutePath;
                $response['exists'] = file_exists($absolutePath);
            }
        } else {
            throw new Exception('Erro ao atualizar o arquivo');
        }
    }
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
    error_log("Path Editor Error: " . $e->getMessage());
}

// Clean output buffer and send JSON
ob_end_clean();
echo json_encode($response);
exit;
?>