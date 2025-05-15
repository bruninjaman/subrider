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

// Obter dados enviados
$conteudo = isset($_POST['conteudo']) ? $_POST['conteudo'] : '';
$data_conclusao = isset($_POST['data_conclusao']) ? $_POST['data_conclusao'] : '';
$observacoes_finais = isset($_POST['observacoes_finais']) ? $_POST['observacoes_finais'] : '';
$quilometragem = isset($_POST['quilometragem']) ? $_POST['quilometragem'] : '';

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
    
    // Verificar se já existe um relatório para esta ordem
    $query_check = "SELECT * FROM relatorios WHERE ordem_id = '$ordem_id'";
    $result_check = mysqli_query($conn, $query_check);
    
    if (!$result_check) {
        throw new Exception("Erro ao verificar existência de relatório: " . mysqli_error($conn));
    }

    if (mysqli_num_rows($result_check) > 0) {
        // Atualizar relatório existente
        $query = "UPDATE relatorios SET 
                conteudo = ?, 
                data_conclusao = ?, 
                observacoes_finais = ?,
                quilometragem = ?,
                data_modificacao = NOW() 
                WHERE ordem_id = ?";
        
        $stmt = mysqli_prepare($conn, $query);
        
        if (!$stmt) {
            throw new Exception("Erro na preparação da consulta de atualização: " . mysqli_error($conn));
        }
        
        mysqli_stmt_bind_param($stmt, "sssss", $conteudo, $data_conclusao, $observacoes_finais, $quilometragem, $ordem_id);
        $exec_result = mysqli_stmt_execute($stmt);
        
        if (!$exec_result) {
            throw new Exception("Erro na execução da atualização: " . mysqli_stmt_error($stmt));
        }
        
        echo json_encode([
            "status" => "success", 
            "message" => "Relatório atualizado com sucesso!"
        ]);
    } else {
        // Criar novo relatório
        $query = "INSERT INTO relatorios (ordem_id, conteudo, data_conclusao, observacoes_finais, quilometragem, data_criacao, data_modificacao) 
                VALUES (?, ?, ?, ?, ?, NOW(), NOW())";
        
        $stmt = mysqli_prepare($conn, $query);
        
        if (!$stmt) {
            throw new Exception("Erro na preparação da consulta de inserção: " . mysqli_error($conn));
        }
        
        mysqli_stmt_bind_param($stmt, "sssss", $ordem_id, $conteudo, $data_conclusao, $observacoes_finais, $quilometragem);
        $exec_result = mysqli_stmt_execute($stmt);
        
        if (!$exec_result) {
            throw new Exception("Erro na execução da inserção: " . mysqli_stmt_error($stmt));
        }
        
        echo json_encode([
            "status" => "success", 
            "message" => "Relatório salvo com sucesso!"
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