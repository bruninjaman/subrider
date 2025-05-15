<?php
// Iniciar sessão para verificar autenticação
session_start();

// Definir cabeçalho para JSON
header('Content-Type: application/json');

// Verificar se o usuário está logado
if (!isset($_SESSION["user"])) {
    // Retornar erro em formato JSON
    echo json_encode([
        "status" => "error",
        "message" => "Usuário não autenticado"
    ]);
    exit;
}

// Verificar se a ordem está especificada
$ordem_id = isset($_GET['ordem']) ? $_GET['ordem'] : '';
if (empty($ordem_id)) {
    echo json_encode([
        "status" => "error",
        "message" => "ID da ordem não especificado"
    ]);
    exit;
}

// Conexão com o banco de dados
require_once '../../connection/connection.php';

// Verificar se a conexão foi bem-sucedida
if (!$conn) {
    echo json_encode([
        "status" => "error",
        "message" => "Erro de conexão com o banco de dados: " . mysqli_connect_error()
    ]);
    exit;
}

try {
    // Limpar o ID da ordem (pode conter caracteres especiais como /)
    $ordem_id = mysqli_real_escape_string($conn, $ordem_id);
    
    // Primeiro, verificar se a ordem existe
    $check_ordem = "SELECT Codigo FROM ordem_servicos WHERE Codigo = '$ordem_id'";
    $result_check = mysqli_query($conn, $check_ordem);
    
    if (!$result_check || mysqli_num_rows($result_check) == 0) {
        echo json_encode([
            "status" => "error",
            "message" => "Ordem de serviço não encontrada"
        ]);
        exit;
    }
    
    // Buscar relatório para a ordem especificada
    $query = "SELECT * FROM relatorios WHERE ordem_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    
    if (!$stmt) {
        throw new Exception("Erro na preparação da consulta: " . mysqli_error($conn));
    }
    
    mysqli_stmt_bind_param($stmt, "s", $ordem_id);
    $exec_result = mysqli_stmt_execute($stmt);
    
    if (!$exec_result) {
        throw new Exception("Erro na execução da consulta: " . mysqli_stmt_error($stmt));
    }
    
    $result = mysqli_stmt_get_result($stmt);
    
    if (!$result) {
        throw new Exception("Erro ao obter resultado da consulta: " . mysqli_error($conn));
    }
    
    if (mysqli_num_rows($result) > 0) {
        // Retornar dados do relatório
        $relatorio = mysqli_fetch_assoc($result);
        echo json_encode([
            "status" => "success",
            "conteudo" => $relatorio['conteudo'],
            "data_conclusao" => $relatorio['data_conclusao'],
            "observacoes_finais" => $relatorio['observacoes_finais'],
            "data_criacao" => $relatorio['data_criacao'],
            "data_modificacao" => $relatorio['data_modificacao']
        ]);
    } else {
        // Relatorio não encontrado
        echo json_encode([
            "status" => "novo",
            "message" => "Relatório não encontrado"
        ]);
    }
    
    // Fechar statement
    mysqli_stmt_close($stmt);
} 
catch (Exception $e) {
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}

// Fechar conexão
mysqli_close($conn);
?> 