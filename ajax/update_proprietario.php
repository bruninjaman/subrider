<?php
require_once("../config.php");

// Verificar CSRF token
if (!isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
    security_log("Tentativa de atualização sem CSRF token válido", "WARNING");
    die('Erro de validação');
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitizar e validar inputs
    $newProprietario = sanitize_input($_POST["newProprietario"]);
    $ordem = sanitize_input($_POST["ordem"]);
    
    // Validar dados
    if (empty($newProprietario) || empty($ordem)) {
        security_log("Tentativa de atualização com dados vazios", "WARNING");
        die("Dados inválidos");
    }
    
    // Preparar e executar a query
    $stmt = mysqli_prepare($conn, "UPDATE ordem_servicos SET proprietario_ordem = ? WHERE Codigo = ?");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ss", $newProprietario, $ordem);
        
        if (mysqli_stmt_execute($stmt)) {
            security_log("Proprietário atualizado com sucesso - Ordem: $ordem", "INFO");
            echo "Dados atualizados com sucesso";
        } else {
            security_log("Erro ao atualizar proprietário - Ordem: $ordem", "ERROR");
            echo "Erro ao atualizar dados";
        }
        
        mysqli_stmt_close($stmt);
    } else {
        security_log("Erro na preparação da query de atualização", "ERROR");
        echo "Erro ao processar a solicitação";
    }
} else {
    security_log("Tentativa de acesso inválido ao update_proprietario.php", "WARNING");
    die("Método não permitido");
}

mysqli_close($conn);
?>