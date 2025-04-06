<?php
session_start();
require_once(__DIR__ . "/config.php");
require_once(__DIR__ . "/../classes/StatusOrdem.php");

if (!isset($_POST['ordem_id']) || !isset($_POST['novo_status'])) {
    echo json_encode(['success' => false, 'message' => 'Parâmetros inválidos']);
    exit;
}

$ordem_id = $_POST['ordem_id'];
$novo_status = $_POST['novo_status'];
$observacao = isset($_POST['observacao']) ? $_POST['observacao'] : '';

$status_manager = new StatusOrdem($conn, $ordem_id);
$resultado = $status_manager->atualizarStatus($novo_status, $observacao);

echo json_encode([
    'success' => $resultado,
    'message' => $resultado ? 'Status atualizado com sucesso' : 'Erro ao atualizar status'
]);

mysqli_close($conn);