<?php
session_start();

// Definir cabeçalho JSON
header('Content-Type: application/json');

// PERM
require_once("../../scripts/perm.php");
// CONNECTION
require_once("../../connection/connection.php");

if (!isset($_POST['fotoID']) || !isset($_POST['motoID'])) {
    echo json_encode(['success' => false, 'message' => 'Parâmetros inválidos.']);
    exit;
}

$fotoID = intval($_POST['fotoID']);
$motoID = intval($_POST['motoID']);
$descricao = isset($_POST['descricao']) ? trim($_POST['descricao']) : null;

// Limitar tamanho para evitar textos excessivos (ajuste conforme necessário)
if ($descricao !== null && strlen($descricao) > 1000) {
    $descricao = substr($descricao, 0, 1000);
}

// Atualiza a descrição da foto específica
$stmt = mysqli_prepare($conn, "UPDATE moto_fotos SET descricao = ? WHERE id = ?");
if ($stmt) {
    mysqli_stmt_bind_param($stmt, 'si', $descricao, $fotoID);
    $success = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    
    if ($success) {
        echo json_encode(['success' => true, 'message' => 'Descrição atualizada com sucesso.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao atualizar descrição no banco de dados.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Erro ao preparar consulta SQL.']);
}

// Fecha conexão
mysqli_close($conn);
exit;
?>