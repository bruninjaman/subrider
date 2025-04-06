<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../repositories/ProprietarioRepository.php';

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

if (!$id) {
    echo json_encode(['success' => false, 'message' => 'ID inválido']);
    exit;
}

$proprietarioRepo = new ProprietarioRepository();

// Verifica se o proprietário existe
$proprietario = $proprietarioRepo->buscarPorId($id);
if (!$proprietario) {
    echo json_encode(['success' => false, 'message' => 'Proprietário não encontrado']);
    exit;
}

// Tenta excluir o proprietário
$resultado = $proprietarioRepo->excluir($id);

if ($resultado) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode([
        'success' => false, 
        'message' => 'Erro ao excluir proprietário. Verifique se não há registros vinculados.'
    ]);
} 