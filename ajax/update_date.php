<?php
require_once("../config.php");

// Verificar CSRF token
if (!isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
    security_log("Tentativa de atualização de data sem CSRF token válido", "WARNING");
    die('Erro de validação');
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitizar inputs
    $newData = sanitize_input($_POST["newData"]);
    $ordem = sanitize_input($_POST["ordem"]);
    
    // Validar dados
    if (empty($newData) || empty($ordem)) {
        security_log("Tentativa de atualização com dados vazios", "WARNING");
        die("Dados inválidos");
    }
    
    // Validar e converter data
    $dateArray = explode('/', $newData);
    if (count($dateArray) !== 3) {
        security_log("Formato de data inválido: $newData", "WARNING");
        die("Formato de data inválido");
    }
    
    $day = filter_var($dateArray[0], FILTER_VALIDATE_INT);
    $month = filter_var($dateArray[1], FILTER_VALIDATE_INT);
    $year = filter_var($dateArray[2], FILTER_VALIDATE_INT);
    
    // Validar componentes da data
    if (!checkdate($month, $day, $year)) {
        security_log("Data inválida: $newData", "WARNING");
        die("Data inválida");
    }
    
    // Formatar data para o banco
    $formattedDate = sprintf("%04d-%02d-%02d", $year, $month, $day);
    
    // Preparar e executar a query
    $stmt = mysqli_prepare($conn, "UPDATE ordem_servicos SET Data = ? WHERE Codigo = ?");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ss", $formattedDate, $ordem);
        
        if (mysqli_stmt_execute($stmt)) {
            security_log("Data atualizada com sucesso - Ordem: $ordem", "INFO");
            echo "Dados atualizados com sucesso";
        } else {
            security_log("Erro ao atualizar data - Ordem: $ordem", "ERROR");
            echo "Erro ao atualizar dados";
        }
        
        mysqli_stmt_close($stmt);
    } else {
        security_log("Erro na preparação da query de atualização de data", "ERROR");
        echo "Erro ao processar a solicitação";
    }
} else {
    security_log("Tentativa de acesso inválido ao update_date.php", "WARNING");
    die("Método não permitido");
}

mysqli_close($conn);
?>