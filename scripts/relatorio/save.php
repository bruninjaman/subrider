<?php
session_start();

// Inclui os arquivos necessários
require_once("../../connection/connection.php");
require_once("../../scripts/functions.php");

// Verifica se o usuário está logado
if (!isset($_SESSION['userID'])) {
    header("Location: ../../login.php");
    exit();
}

// Verifica se todos os dados necessários foram enviados
if (!isset($_POST['conteudo']) || !isset($_GET['ordem'])) {
    echo json_encode(['status' => 'error', 'message' => 'Dados incompletos']);
    exit();
}

// Limpa os dados recebidos
$conteudo = mysqli_real_escape_string($conn, $_POST['conteudo']);
$ordem_id = mysqli_real_escape_string($conn, $_GET['ordem']);
$data_conclusao = isset($_POST['data_conclusao']) ? mysqli_real_escape_string($conn, $_POST['data_conclusao']) : '';
$observacoes_finais = isset($_POST['observacoes_finais']) ? mysqli_real_escape_string($conn, $_POST['observacoes_finais']) : '';

// Verifica se a ordem de serviço existe
$verificacao_ordem = mysqli_query($conn, "SELECT servID FROM ordem_servicos WHERE Codigo = '$ordem_id'");
if (mysqli_num_rows($verificacao_ordem) == 0) {
    echo json_encode(['status' => 'error', 'message' => 'Ordem de serviço não encontrada']);
    exit();
}

// Obtém o servID da ordem para uso nas consultas
$row_ordem = mysqli_fetch_assoc($verificacao_ordem);
$serv_id = $row_ordem['servID'];

// Verifica se já existe um relatório para esta ordem
$verificacao = mysqli_query($conn, "SELECT id FROM relatorios WHERE ordem_id = $serv_id");

if (mysqli_num_rows($verificacao) > 0) {
    // Atualiza o relatório existente
    $row = mysqli_fetch_assoc($verificacao);
    $relatorio_id = $row['id'];
    $query = "UPDATE relatorios SET 
                conteudo = '$conteudo', 
                data_conclusao = '$data_conclusao',
                observacoes_finais = '$observacoes_finais',
                data_modificacao = NOW()
              WHERE id = $relatorio_id";
    $action = "atualizado";
} else {
    // Insere um novo relatório
    $query = "INSERT INTO relatorios 
                (ordem_id, conteudo, data_conclusao, observacoes_finais) 
              VALUES 
                ($serv_id, '$conteudo', '$data_conclusao', '$observacoes_finais')";
    $action = "criado";
}

// Executa a query
if (mysqli_query($conn, $query)) {
    echo json_encode(['status' => 'success', 'message' => "Relatório $action com sucesso"]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Erro ao salvar relatório: ' . mysqli_error($conn)]);
}

// Fecha a conexão
mysqli_close($conn);
?> 