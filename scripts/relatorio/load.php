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

// Verifica se o código da ordem foi enviado
if (!isset($_GET['ordem'])) {
    echo json_encode(['status' => 'error', 'message' => 'Código da ordem não fornecido']);
    exit();
}

// Limpa os dados recebidos
$ordem_codigo = mysqli_real_escape_string($conn, $_GET['ordem']);

// Verifica se a ordem existe
$verificacao_ordem = mysqli_query($conn, "SELECT servID FROM ordem_servicos WHERE Codigo = '$ordem_codigo'");
if (mysqli_num_rows($verificacao_ordem) == 0) {
    echo json_encode(['status' => 'error', 'message' => 'Ordem de serviço não encontrada']);
    exit();
}

// Obtém o servID da ordem
$row_ordem = mysqli_fetch_assoc($verificacao_ordem);
$serv_id = $row_ordem['servID'];

// Busca o relatório no banco de dados
$query = "SELECT conteudo, assinatura_img, assinatura_cliente_img, quilometragem, data_conclusao, 
          tecnico_responsavel, observacoes_finais, data_criacao, data_modificacao 
          FROM relatorios 
          WHERE ordem_id = $serv_id";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) > 0) {
    $relatorio = mysqli_fetch_assoc($result);
    echo json_encode([
        'status' => 'success',
        'conteudo' => $relatorio['conteudo'],
        'assinatura' => $relatorio['assinatura_img'],
        'assinatura_cliente' => $relatorio['assinatura_cliente_img'],
        'quilometragem' => $relatorio['quilometragem'],
        'data_conclusao' => $relatorio['data_conclusao'],
        'tecnico_responsavel' => $relatorio['tecnico_responsavel'],
        'observacoes_finais' => $relatorio['observacoes_finais'],
        'data_criacao' => $relatorio['data_criacao'],
        'data_modificacao' => $relatorio['data_modificacao']
    ]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Relatório não encontrado']);
}

// Fecha a conexão
mysqli_close($conn);
?> 