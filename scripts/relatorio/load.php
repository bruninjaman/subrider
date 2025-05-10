<?php
session_start();

// Inclui os arquivos necessários
require_once("../../connection/connection.php");
require_once("../../scripts/functions.php");

// Definir o cabeçalho para JSON
header('Content-Type: application/json');

// Depuração - Verificar se a string '/' está sendo corretamente processada nos parâmetros
$debug_info = [];
$debug_info['ordem_original'] = isset($_GET['ordem']) ? $_GET['ordem'] : 'não definido';

// Verifica se o usuário está logado
if (!isset($_SESSION['userID'])) {
    echo json_encode(['status' => 'error', 'message' => 'Usuário não autenticado', 'debug' => $debug_info]);
    exit();
}

// Verifica se o código da ordem foi enviado
if (!isset($_GET['ordem'])) {
    echo json_encode(['status' => 'error', 'message' => 'Código da ordem não fornecido', 'debug' => $debug_info]);
    exit();
}

// Limpa os dados recebidos e trata a barra (/) no código da ordem
$ordem_codigo = mysqli_real_escape_string($conn, $_GET['ordem']);
$debug_info['ordem_escapada'] = $ordem_codigo;

// Verifica se a ordem existe - melhorando a consulta para tratar possíveis problemas com a barra
$verificacao_query = "SELECT servID FROM ordem_servicos WHERE Codigo = '$ordem_codigo'";
$debug_info['query'] = $verificacao_query;
$verificacao_ordem = mysqli_query($conn, $verificacao_query);
$debug_info['query_erro'] = mysqli_error($conn);
$debug_info['num_rows'] = mysqli_num_rows($verificacao_ordem);

if (!$verificacao_ordem || mysqli_num_rows($verificacao_ordem) == 0) {
    // Tentar uma abordagem alternativa para a consulta
    $ordem_codigo_alt = str_replace('/', '\/', $ordem_codigo); // Escapar a barra de forma alternativa
    $verificacao_query_alt = "SELECT servID FROM ordem_servicos WHERE Codigo = '$ordem_codigo_alt'";
    $debug_info['query_alt'] = $verificacao_query_alt;
    $verificacao_ordem_alt = mysqli_query($conn, $verificacao_query_alt);
    $debug_info['query_alt_erro'] = mysqli_error($conn);
    $debug_info['num_rows_alt'] = mysqli_num_rows($verificacao_ordem_alt);
    
    if ($verificacao_ordem_alt && mysqli_num_rows($verificacao_ordem_alt) > 0) {
        $verificacao_ordem = $verificacao_ordem_alt;
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Ordem de serviço não encontrada', 'debug' => $debug_info]);
        exit();
    }
}

// Obtém o servID da ordem
$row_ordem = mysqli_fetch_assoc($verificacao_ordem);
$serv_id = $row_ordem['servID'];
$debug_info['serv_id'] = $serv_id;

// Busca o relatório no banco de dados
$query = "SELECT conteudo, data_conclusao, observacoes_finais, data_criacao, data_modificacao FROM relatorios WHERE ordem_id = $serv_id";
$debug_info['query_relatorio'] = $query;
$result = mysqli_query($conn, $query);
$debug_info['query_relatorio_erro'] = mysqli_error($conn);
$debug_info['num_rows_relatorio'] = mysqli_num_rows($result);

if ($result && mysqli_num_rows($result) > 0) {
    $relatorio = mysqli_fetch_assoc($result);
    echo json_encode([
        'status' => 'success',
        'conteudo' => $relatorio['conteudo'],
        'data_conclusao' => $relatorio['data_conclusao'],
        'observacoes_finais' => $relatorio['observacoes_finais'],
        'data_criacao' => $relatorio['data_criacao'],
        'data_modificacao' => $relatorio['data_modificacao'],
        'debug' => $debug_info
    ]);
} else {
    // Se não encontrou o relatório mas a ordem existe, retorna status 'novo'
    echo json_encode([
        'status' => 'novo', 
        'message' => 'Novo relatório sendo criado para esta ordem',
        'debug' => $debug_info
    ]);
}

// Fecha a conexão
mysqli_close($conn);
?> 