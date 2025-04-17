<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sourceFile = $_POST['source_file'];
    $oldPath = $_POST['old_path'];
    $newPath = $_POST['new_path'];

    // Validação básica
    if (empty($sourceFile) || empty($oldPath) || empty($newPath)) {
        die('Parâmetros inválidos');
    }

    // Lê o conteúdo do arquivo
    $content = file_get_contents($sourceFile);
    if ($content === false) {
        die('Não foi possível ler o arquivo fonte');
    }

    // Escapa caracteres especiais para uso em regex
    $oldPath = preg_quote($oldPath, '/');
    
    // Substitui o path antigo pelo novo, mantendo as aspas originais
    $pattern = '/([\'"])'.$oldPath.'([\'"])/';
    $replacement = '$1'.$newPath.'$2';
    $newContent = preg_replace($pattern, $replacement, $content);

    // Salva o arquivo
    if (file_put_contents($sourceFile, $newContent) !== false) {
        $message = "Path atualizado com sucesso!";
        $status = "success";
    } else {
        $message = "Erro ao atualizar o arquivo";
        $status = "error";
    }

    // Redireciona de volta para o path_checker.php com mensagem
    header("Location: path_checker.php?message=" . urlencode($message) . "&status=" . $status);
    exit;
}

// Se não for POST, redireciona para o path_checker
header("Location: path_checker.php");
exit;
?> 