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
$ordem_id = mysqli_real_escape_string($conn, $_GET['ordem']); // Alterado para permitir varchar
$assinatura = isset($_POST['assinatura']) ? mysqli_real_escape_string($conn, $_POST['assinatura']) : '';
$assinatura_cliente = isset($_POST['assinatura_cliente']) ? mysqli_real_escape_string($conn, $_POST['assinatura_cliente']) : '';
$quilometragem = isset($_POST['quilometragem']) ? mysqli_real_escape_string($conn, $_POST['quilometragem']) : '';
$data_conclusao = isset($_POST['data_conclusao']) ? mysqli_real_escape_string($conn, $_POST['data_conclusao']) : '';
$tecnico_responsavel = isset($_POST['tecnico_responsavel']) ? mysqli_real_escape_string($conn, $_POST['tecnico_responsavel']) : '';
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

// Atualiza a quilometragem na tabela de ordem de serviço se foi fornecida
if (!empty($quilometragem)) {
    $update_km = "UPDATE ordem_servicos SET KM = '$quilometragem' WHERE servID = $serv_id";
    mysqli_query($conn, $update_km);
}

// Verifica se já existe um relatório para esta ordem
$verificacao = mysqli_query($conn, "SELECT id FROM relatorios WHERE ordem_id = $serv_id");

if (mysqli_num_rows($verificacao) > 0) {
    // Atualiza o relatório existente
    $row = mysqli_fetch_assoc($verificacao);
    $relatorio_id = $row['id'];
    $query = "UPDATE relatorios SET 
                conteudo = '$conteudo', 
                assinatura_img = '$assinatura',
                assinatura_cliente_img = '$assinatura_cliente',
                quilometragem = '$quilometragem',
                data_conclusao = '$data_conclusao',
                tecnico_responsavel = '$tecnico_responsavel',
                observacoes_finais = '$observacoes_finais',
                data_modificacao = NOW()
              WHERE id = $relatorio_id";
    $action = "atualizado";
} else {
    // Insere um novo relatório
    $query = "INSERT INTO relatorios 
                (ordem_id, conteudo, assinatura_img, assinatura_cliente_img, quilometragem, data_conclusao, tecnico_responsavel, observacoes_finais) 
              VALUES 
                ($serv_id, '$conteudo', '$assinatura', '$assinatura_cliente', '$quilometragem', '$data_conclusao', '$tecnico_responsavel', '$observacoes_finais')";
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